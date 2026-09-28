<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Subscription;
use App\Models\Vj;
use App\Services\AnalyticsService;

class DashboardController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the Admin CMS metrics and overview.
     */
    public function index()
    {
        $stats = [
            'total_movies' => Movie::where('status', 'published')->count(),
            'total_series' => Series::where('status', 'published')->count(),
            'total_episodes' => Episode::count(),
            'total_vjs' => Vj::where('is_active', true)->count(),
            'total_movie_views' => Movie::sum('views'),
            'total_series_views' => Episode::sum('views'),
            'active_subscriptions' => Subscription::active()->count(),
        ];

        $recentMovies = Movie::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentSeries = Series::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Add analytics summary
        $platformStats = $this->analyticsService->getPlatformStats();

        return view('admin.dashboard', compact('stats', 'recentMovies', 'recentSeries', 'platformStats'));
    }
}
