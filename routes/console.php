<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ===== Phase 4: 予約投稿の自動公開 =====
// Xserver で動かす際は cron 1本：
//   * * * * * cd /home/laide/sns-tool && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
// QUEUE_CONNECTION=database のままで、queue:work --stop-when-empty を毎分 drain させる方式
// （常駐 worker 不要）。

Schedule::command('posts:publish-due')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->onOneServer();

Schedule::command('queue:work --stop-when-empty --queue=default --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping(5)
    ->onOneServer();
