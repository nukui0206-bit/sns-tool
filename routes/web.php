<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SocialAccountController;
use App\Models\Client;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard', [
        'clientsCount' => Client::count(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

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
});

require __DIR__.'/auth.php';
