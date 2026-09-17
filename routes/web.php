<?php

use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TopUpRequestController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/topup', [TopUpRequestController::class, 'create'])->name('wallet.topup.create');
    Route::post('/wallet/topup', [TopUpRequestController::class, 'store'])->name('wallet.topup.store');
    Route::get('/wallet/topup/{topUpRequest}', [WalletController::class, 'showTopup'])->name('wallet.topup.show');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::delete('/notifications', [NotificationController::class, 'clear'])->name('notifications.clear');
});

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/games/{slug}', [\App\Http\Controllers\GameController::class, 'show'])->name('games.show');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

require __DIR__ . '/auth.php';
