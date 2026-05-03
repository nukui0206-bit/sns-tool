<?php

namespace App\Console\Commands;

use App\Jobs\PublishPostJob;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishDuePosts extends Command
{
    protected $signature = 'posts:publish-due';

    protected $description = '投稿予定日時を過ぎた予約投稿（status=scheduled）を抽出して PublishPostJob を dispatch する。';

    public function handle(): int
    {
        $now = now();

        $posts = Post::query()
            ->where('status', Post::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', $now)
            ->orderBy('scheduled_at')
            ->get();

        $count = $posts->count();

        if ($count === 0) {
            $this->info("[posts:publish-due] {$now} : 対象なし");
            return self::SUCCESS;
        }

        $this->info("[posts:publish-due] {$now} : 対象 {$count} 件を dispatch します");

        foreach ($posts as $post) {
            PublishPostJob::dispatch($post->id);
            $this->line(sprintf(
                '  → Post #%d (client_id=%d, scheduled_at=%s) dispatched',
                $post->id,
                $post->client_id,
                $post->scheduled_at?->format('Y-m-d H:i') ?? '-'
            ));
        }

        return self::SUCCESS;
    }
}
