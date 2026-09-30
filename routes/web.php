<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EpisodeController as AdminEpisodeController;
use App\Http\Controllers\Admin\MovieController as AdminMovieController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\TmdbController as AdminTmdbController;
use App\Http\Controllers\Admin\VjController as AdminVjController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\NewsletterController;
// Unused while the public payment routes are disabled. See the route block below.
// use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\StreamController;
// Unused while the public subscription routes are disabled. See the route block below.
// use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\VjController;
use App\Http\Controllers\VjFlixController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

// Public Landing Page & Newsletter
Route::view('/', 'home')->name('home');
Route::post('newsletter', NewsletterController::class)->name('newsletter.subscribe');

// Ugandan VJ Discovery
Route::get('/vjs', [VjController::class, 'index'])->name('vjs.index');
Route::get('/vjs/{slug}', [VjController::class, 'show'])->name('vjs.show');

// Movie Catalog & Streaming Watch Page (Public Access)
Route::get('/movies', [VjFlixController::class, 'index'])->name('vjflix.index');
Route::get('/movie/{watch}', [VjFlixController::class, 'show'])->name('movies.show');

// TV Series Catalog & Episodes Streaming
Route::get('/series', [SeriesController::class, 'index'])->name('series.index');
Route::get('/series/{slug}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/series/{slug}/season/{season}/episode/{episode}', [SeriesController::class, 'watch'])->name('series.watch');

// Media Streaming Delivery (HTTP 206 Partial Content Byte-Range Serving)
Route::get('/stream/movie/{slug}', [StreamController::class, 'streamMovie'])->name('stream.movie');
Route::get('/stream/series/{seriesSlug}/season/{season}/episode/{episode}', [StreamController::class, 'streamEpisode'])->name('stream.episode');

// Watch Progress & Resume API
Route::middleware('auth')->group(function () {
    Route::post('/api/progress', [StreamController::class, 'updateProgress'])->name('progress.update');
    Route::get('/api/progress/{type}/{id}', [StreamController::class, 'getProgress'])->name('progress.get');
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

    // Subscriptions
    // Disabled: VJFlix Uganda is a free, open-access app, so there is nothing to
    // subscribe to. The controllers, models and tables are kept in place so this
    // can be re-enabled later; uncomment to restore the paywall.
    //
    // Keep the literal paths above /subscriptions/{subscription}, otherwise the
    // parameterised route matches first and route-model binding tries to cast
    // "history" to a bigint id.
    //
    // Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    // Route::get('/subscriptions/history', [SubscriptionController::class, 'history'])->name('subscriptions.history');
    // Route::get('/subscriptions/plan/{plan}', [SubscriptionController::class, 'plan'])->name('subscriptions.plan');
    // Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    // Route::post('/subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    // Payments
    // Disabled alongside subscriptions, see the note above.
    //
    // Route::post('/payments/initiate', [PaymentController::class, 'initiate'])->name('payments.initiate');
    // Route::get('/payments/status', [PaymentController::class, 'status'])->name('payments.status');
    // Route::get('/payments/check/{reference}', [PaymentController::class, 'checkStatus'])->name('payments.check');
    // Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
    // Route::get('/payments/receipt/{payment}', [PaymentController::class, 'receipt'])->name('payments.receipt');

    // Recommendations API
    Route::prefix('api/recommendations')->name('recommendations.')->group(function () {
        Route::get('/personalized', [RecommendationController::class, 'personalized'])->name('personalized');
        Route::get('/watch-history', [RecommendationController::class, 'basedOnWatchHistory'])->name('watch-history');
        Route::get('/similar/{id}', [RecommendationController::class, 'similar'])->name('similar');
        Route::get('/vj/{id}', [RecommendationController::class, 'moreFromVj'])->name('vj');
        Route::get('/trending', [RecommendationController::class, 'trending'])->name('trending');
        Route::get('/new', [RecommendationController::class, 'newReleases'])->name('new');
        Route::get('/genres', [RecommendationController::class, 'basedOnGenres'])->name('genres');
    });
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

    // TMDB Movie & Series Resources & Importer
    Route::get('tmdb', [AdminTmdbController::class, 'index'])->name('tmdb.index');
    Route::get('tmdb/search', [AdminTmdbController::class, 'search'])->name('tmdb.search');
    Route::get('tmdb/details/{type}/{id}', [AdminTmdbController::class, 'details'])->name('tmdb.details');
    Route::post('tmdb/import', [AdminTmdbController::class, 'import'])->name('tmdb.import');

    // Subscription Management
    Route::resource('plans', AdminPlanController::class);
    Route::get('subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('subscriptions/{subscription}', [AdminSubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::put('subscriptions/{subscription}', [AdminSubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::delete('subscriptions/{subscription}', [AdminSubscriptionController::class, 'destroy'])->name('subscriptions.destroy');

    // Payment Management
    Route::get('payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');

    // Analytics
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('analytics/data', [AnalyticsController::class, 'data'])->name('analytics.data');
});

// Payment Callback Routes (Public)
// Disabled: this is an unauthenticated endpoint that a payment provider posts to.
// It stays off so no provider can create payments for a site that is now free.
// Uncomment together with the payment routes above to re-enable.
//
// Route::post('/payments/callback/{provider}', [PaymentController::class, 'callback'])->name('payments.callback');
