<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->query('period', '30');
        $days = (int) $period;

        return view('admin.analytics.index', [
            'platformStats' => $this->analyticsService->getPlatformStats(),
            'movieStats' => $this->analyticsService->getMovieStats(10),
            'vjStats' => $this->analyticsService->getVjStats(10),
            'genreStats' => $this->analyticsService->getGenreStats(10),
            'userAnalytics' => $this->analyticsService->getUserAnalytics(),
            'subscriptionAnalytics' => $this->analyticsService->getSubscriptionAnalytics(),
            'dailyActiveUsers' => $this->analyticsService->getDailyActiveUsers($days),
            'dailyWatchTime' => $this->analyticsService->getDailyWatchTime($days),
            'completionRate' => $this->analyticsService->getCompletionRate(),
            'topContentByWatchTime' => $this->analyticsService->getTopContentByWatchTime(10),
            'period' => $period,
        ]);
    }

    /**
     * Get analytics data as JSON for charts.
     */
    public function data(Request $request)
    {
        $type = $request->query('type');
        $period = $request->query('period', 30);
        $days = (int) $period;

        return response()->json([
            'success' => true,
            'data' => match ($type) {
                'platform' => $this->analyticsService->getPlatformStats(),
                'movies' => $this->analyticsService->getMovieStats(20),
                'vjs' => $this->analyticsService->getVjStats(20),
                'genres' => $this->analyticsService->getGenreStats(20),
                'users' => $this->analyticsService->getUserAnalytics(),
                'subscriptions' => $this->analyticsService->getSubscriptionAnalytics(),
                'daily_active_users' => $this->analyticsService->getDailyActiveUsers($days),
                'daily_watch_time' => $this->analyticsService->getDailyWatchTime($days),
                'completion_rate' => $this->analyticsService->getCompletionRate(),
                'top_content' => $this->analyticsService->getTopContentByWatchTime(20),
                default => [],
            },
        ]);
    }
}
