<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FailedJobController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAccountController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('clients', ClientController::class);
    Route::resource('posts', PostController::class)->except(['show']);
    Route::resource('social_accounts', SocialAccountController::class);

    // Phase 5: 投稿カレンダー
    Route::view('/calendar', 'calendar.index')->name('calendar.index');
    Route::get('/calendar/events', [PostController::class, 'calendarEvents'])->name('calendar.events');
    Route::patch('/posts/{post}/schedule', [PostController::class, 'updateSchedule'])->name('posts.schedule');

    // Phase 6: AI 文案生成
    Route::post('/posts/ai-generate', [PostController::class, 'aiGenerate'])->name('posts.ai_generate');

    // Phase 10: 失敗ジョブ管理
    Route::get('/failed_jobs', [FailedJobController::class, 'index'])->name('failed_jobs.index');
    Route::delete('/failed_jobs', [FailedJobController::class, 'destroyAll'])->name('failed_jobs.destroy_all');
    Route::get('/failed_jobs/{id}', [FailedJobController::class, 'show'])->name('failed_jobs.show');
    Route::delete('/failed_jobs/{id}', [FailedJobController::class, 'destroy'])->name('failed_jobs.destroy');
    Route::post('/failed_jobs/{id}/retry', [FailedJobController::class, 'retry'])->name('failed_jobs.retry');
});

require __DIR__.'/auth.php';
