<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\SocialPoster\PosterInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishPostJob implements ShouldQueue
{
    use Queueable;

    /** リトライ上限 */
    public int $tries = 3;

    /**
     * リトライ間の待ち時間（秒）。1回目失敗→60秒後、2回目→300秒後、3回目→600秒後。
     *
     * @var array<int, int>
     */
    public array $backoff = [60, 300, 600];

    public function __construct(public int $postId)
    {
    }

    public function handle(PosterInterface $poster): void
    {
        $post = Post::with(['client', 'socialAccount'])->find($this->postId);

        if (! $post) {
            Log::warning('[PublishPostJob] post not found', ['post_id' => $this->postId]);
            return;
        }

        // 既に処理済みの場合はスキップ（重複 dispatch 対策）
        if ($post->status !== Post::STATUS_SCHEDULED) {
            Log::info('[PublishPostJob] skip (status changed)', [
                'post_id' => $post->id,
                'status' => $post->status,
            ]);
            return;
        }

        try {
            $result = $poster->publish($post);

            if (($result['ok'] ?? false) === true) {
                $post->update(['status' => Post::STATUS_POSTED]);
                Log::info('[PublishPostJob] success', [
                    'post_id' => $post->id,
                    'external_post_id' => $result['external_post_id'] ?? null,
                ]);
                return;
            }

            // ok=false のとき：API 側で失敗扱い。リトライさせるため例外化。
            throw new \RuntimeException($result['error'] ?? 'publish returned ok=false');
        } catch (Throwable $e) {
            Log::warning('[PublishPostJob] attempt failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // 最終試行で失敗した場合のみ status=failed に確定
            if ($this->attempts() >= $this->tries) {
                $post->update(['status' => Post::STATUS_FAILED]);
                Log::error('[PublishPostJob] exhausted retries → marked as failed', [
                    'post_id' => $post->id,
                ]);
            }

            throw $e; // リトライさせる
        }
    }
}
