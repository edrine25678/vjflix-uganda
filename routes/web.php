<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EpisodeController as AdminEpisodeController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\VjController as AdminVjController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StreamController;
use App\Http\Controllers\VelflixController;
use App\Http\Controllers\VjController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Newsletter
Route::view('/', 'home')->name('home');
Route::post('newsletter', NewsletterController::class)->name('newsletter.subscribe');

// Ugandan VJ Discovery
Route::get('/vjs', [VjController::class, 'index'])->name('vjs.index');
Route::get('/vjs/{slug}', [VjController::class, 'show'])->name('vjs.show');

// TV Series Catalog & Episodes Streaming
Route::get('/series', [SeriesController::class, 'index'])->name('series.index');
Route::get('/series/{slug}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/series/{slug}/season/{season}/episode/{episode}', [SeriesController::class, 'watch'])->name('series.watch');

// Media Streaming Delivery (HTTP 206 Partial Content Byte-Range Serving)
Route::get('/stream/movie/{slug}', [\App\Http\Controllers\StreamController::class, 'streamMovie'])->name('stream.movie');
Route::get('/stream/series/{seriesSlug}/season/{season}/episode/{episode}', [\App\Http\Controllers\StreamController::class, 'streamEpisode'])->name('stream.episode');

// Watch Progress & Resume API
Route::middleware('auth')->group(function () {
    Route::post('/api/progress', [\App\Http\Controllers\StreamController::class, 'updateProgress'])->name('progress.update');
    Route::get('/api/progress/{type}/{id}', [\App\Http\Controllers\StreamController::class, 'getProgress'])->name('progress.get');
});

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

    // Watchlist / My List
    Route::get('/my-list', [WatchlistController::class, 'index'])->name('watchlist.index');
    Route::post('/watchlist/toggle', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');

    // Ratings & Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // User Profile & Preferences
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin CMS Console
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('movies', AdminMovieController::class);
    Route::resource('vjs', AdminVjController::class);
    Route::resource('series', AdminSeriesController::class);
    Route::post('seasons/{season}/episodes', [AdminEpisodeController::class, 'store'])->name('episodes.store');
    Route::put('episodes/{episode}', [AdminEpisodeController::class, 'update'])->name('episodes.update');
    Route::delete('episodes/{episode}', [AdminEpisodeController::class, 'destroy'])->name('episodes.destroy');
});
