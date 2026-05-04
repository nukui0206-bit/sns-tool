<?php

namespace App\Providers;

use App\Services\AiCopywriter\AiCopywriterInterface;
use App\Services\AiCopywriter\StubAiCopywriter;
use App\Services\SocialPoster\PosterInterface;
use App\Services\SocialPoster\StubPoster;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // PosterInterface のバインディング。env で実装を切替、未設定や不明なドライバは Stub に fallback。
        $this->app->bind(PosterInterface::class, function () {
            $driver = config('services.social_poster.driver', 'stub');

            return match ($driver) {
                'stub' => new StubPoster(),
                default => new StubPoster(), // Phase 8/9 で 'instagram' / 'tiktok' 実装を追加予定
            };
        });

        // AiCopywriterInterface のバインディング。Phase 7 で 'openai' / 'claude' に差し替え予定。
        $this->app->bind(AiCopywriterInterface::class, function () {
            $driver = config('services.ai_copywriter.driver', 'stub');

            return match ($driver) {
                'stub' => new StubAiCopywriter(),
                default => new StubAiCopywriter(),
            };
        });
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
