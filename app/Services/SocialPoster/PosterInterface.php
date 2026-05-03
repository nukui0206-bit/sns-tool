<?php

namespace App\Services\SocialPoster;

use App\Models\Post;

interface PosterInterface
{
    /**
     * 指定された投稿を外部 SNS に公開する。
     *
     * 返り値の形：
     *   ['ok' => true,  'external_post_id' => string]   成功時
     *   ['ok' => false, 'error' => string]              失敗時
     *
     * @return array{ok: bool, external_post_id?: string, error?: string}
     */
    public function publish(Post $post): array;
}
