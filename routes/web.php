<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\VelflixController;
use App\Http\Controllers\VjController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Newsletter
Route::view('/', 'home')->name('home');
Route::post('newsletter', NewsletterController::class)->name('newsletter.subscribe');

// Ugandan VJ Discovery
Route::get('/vjs', [VjController::class, 'index'])->name('vjs.index');
Route::get('/vjs/{slug}', [VjController::class, 'show'])->name('vjs.show');

// Authentication (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('login', [SessionsController::class, 'create'])->name('login');
    Route::post('login', [SessionsController::class, 'store']);
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store']);

    // Google Socialite OAuth
    Route::controller(LoginController::class)->group(function () {
        Route::get('login/google', 'redirectToProvider')->name('login.google');
        Route::get('login/google/callback', 'handleProviderCallback')->name('login.google.callback');
    });
});

// Authenticated Viewer Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [SessionsController::class, 'destroy'])->name('logout');
    Route::get('/movies', [VelflixController::class, 'index'])->name('velflix.index');
    Route::get('/movie/{watch}', [VelflixController::class, 'show'])->name('movies.show');
});

// Admin CMS Console
Route::middleware(['auth', 'can:admin'])->group(function () {
    Route::view('admin', 'livewire.admin-controller')->name('admin.dashboard');
});
