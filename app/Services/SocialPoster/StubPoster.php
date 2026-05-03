<?php

namespace App\Services\SocialPoster;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * 開発・動作確認用のスタブ実装。実 API を叩かず常に成功を返す。
 * 本番では SOCIAL_POSTER_DRIVER で他実装（InstagramPoster / TikTokPoster）に切替予定。
 */
class StubPoster implements PosterInterface
{
    public function publish(Post $post): array
    {
        $externalId = 'stub_' . Str::random(12);

        Log::info('[StubPoster] publish', [
            'post_id' => $post->id,
            'client_id' => $post->client_id,
            'social_account_id' => $post->social_account_id,
            'platform' => $post->socialAccount?->platform,
            'account_name' => $post->socialAccount?->account_name,
            'content_excerpt' => Str::limit($post->content, 80),
            'external_post_id' => $externalId,
        ]);

        return [
            'ok' => true,
            'external_post_id' => $externalId,
        ];
    }
}
