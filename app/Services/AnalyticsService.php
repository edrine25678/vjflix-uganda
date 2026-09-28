<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Vj;
use App\Models\WatchProgress;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get overall platform statistics.
     */
    public function getPlatformStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_movies' => Movie::where('status', 'published')->count(),
            'total_series' => Series::where('status', 'published')->count(),
            'total_vjs' => Vj::where('is_active', true)->count(),
            'total_views' => $this->getTotalViews(),
            'total_watch_time' => $this->getTotalWatchTime(),
            'active_subscriptions' => Subscription::active()->count(),
            'total_revenue' => $this->getTotalRevenue(),
        ];
    }

    /**
     * Get total views across all content.
     */
    public function getTotalViews(): int
    {
        return Movie::sum('views') + Episode::sum('views');
    }

    /**
     * Get total watch time in minutes.
     */
    public function getTotalWatchTime(): int
    {
        return WatchProgress::sum('watch_time_minutes') ?? 0;
    }

    /**
     * Get total revenue from payments.
     */
    public function getTotalRevenue(): int
    {
        return DB::table('payments')
            ->where('status', 'completed')
            ->sum('amount') ?? 0;
    }

    /**
     * Get movie statistics.
     */
    public function getMovieStats(int $limit = 10): array
    {
        return Movie::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'views', 'average_rating', 'release_year'])
            ->map(function ($movie) {
                return [
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'slug' => $movie->slug,
                    'views' => $movie->views,
                    'rating' => $movie->average_rating,
                    'year' => $movie->release_year,
                ];
            })
            ->toArray();
    }

    /**
     * Get VJ statistics.
     */
    public function getVjStats(int $limit = 10): array
    {
        return Vj::withCount(['movies', 'series'])
            ->orderBy('movies_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'stage_name', 'slug'])
            ->map(function ($vj) {
                return [
                    'id' => $vj->id,
                    'name' => $vj->name,
                    'stage_name' => $vj->stage_name,
                    'slug' => $vj->slug,
                    'movies_count' => $vj->movies_count,
                    'series_count' => $vj->series_count,
                    'total_content' => $vj->movies_count + $vj->series_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get genre statistics.
     */
    public function getGenreStats(int $limit = 10): array
    {
        return DB::table('movie_genre')
            ->join('genres', 'movie_genre.genre_id', '=', 'genres.id')
            ->join('movies', 'movie_genre.movie_id', '=', 'movies.id')
            ->select('genres.id', 'genres.name', 'genres.slug', DB::raw('COUNT(*) as count'))
            ->where('movies.status', 'published')
            ->groupBy('genres.id', 'genres.name', 'genres.slug')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get user analytics.
     */
    public function getUserAnalytics(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $newUsersThisMonth = User::where('created_at', '>=', now()->startOfMonth())->count();
        $usersWithSubscriptions = Subscription::active()->count();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'users_with_subscriptions' => $usersWithSubscriptions,
            'subscription_rate' => $totalUsers > 0 ? round(($usersWithSubscriptions / $totalUsers) * 100, 2) : 0,
        ];
    }

    /**
     * Get subscription analytics.
     */
    public function getSubscriptionAnalytics(): array
    {
        $activeSubscriptions = Subscription::active()->count();
        $totalSubscriptions = Subscription::count();
        $revenueThisMonth = DB::table('payments')
            ->where('status', 'completed')
            ->where('paid_at', '>=', now()->startOfMonth())
            ->sum('amount') ?? 0;

        $subscriptionsByPlan = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->select('plans.name', 'plans.slug', DB::raw('COUNT(*) as count'))
            ->where('subscriptions.status', 'active')
            ->groupBy('plans.id', 'plans.name', 'plans.slug')
            ->get()
            ->toArray();

        return [
            'active_subscriptions' => $activeSubscriptions,
            'total_subscriptions' => $totalSubscriptions,
            'revenue_this_month' => $revenueThisMonth,
            'subscriptions_by_plan' => $subscriptionsByPlan,
        ];
    }

    /**
     * Get daily active users for the last 30 days.
     */
    public function getDailyActiveUsers(int $days = 30): array
    {
        return DB::table('watch_progress')
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('COUNT(DISTINCT user_id) as count'))
            ->where('updated_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get watch time statistics by day for the last 30 days.
     */
    public function getDailyWatchTime(int $days = 30): array
    {
        return DB::table('watch_progress')
            ->select(DB::raw('DATE(updated_at) as date'), DB::raw('SUM(watch_time_minutes) as total_minutes'))
            ->where('updated_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Get completion rate statistics.
     */
    public function getCompletionRate(): array
    {
        $totalProgress = WatchProgress::count();
        $completed = WatchProgress::where('completed', true)->count();

        return [
            'total_watched' => $totalProgress,
            'completed' => $completed,
            'completion_rate' => $totalProgress > 0 ? round(($completed / $totalProgress) * 100, 2) : 0,
        ];
    }

    /**
     * Get top content by watch time.
     */
    public function getTopContentByWatchTime(int $limit = 10): array
    {
        $movies = DB::table('watch_progress')
            ->join('movies', 'watch_progress.watchable_id', '=', 'movies.id')
            ->where('watch_progress.watchable_type', Movie::class)
            ->select('movies.id', 'movies.title', 'movies.slug', DB::raw('SUM(watch_progress.watch_time_minutes) as total_minutes'))
            ->groupBy('movies.id', 'movies.title', 'movies.slug')
            ->orderByDesc('total_minutes')
            ->limit($limit)
            ->get()
            ->toArray();

        $episodes = DB::table('watch_progress')
            ->join('episodes', 'watch_progress.watchable_id', '=', 'episodes.id')
            ->where('watch_progress.watchable_type', Episode::class)
            ->join('series', 'episodes.series_id', '=', 'series.id')
            ->select('series.id', 'series.title', 'series.slug', DB::raw('SUM(watch_progress.watch_time_minutes) as total_minutes'))
            ->groupBy('series.id', 'series.title', 'series.slug')
            ->orderByDesc('total_minutes')
            ->limit($limit)
            ->get()
            ->toArray();

        return [
            'movies' => $movies,
            'series' => $episodes,
        ];
    }
}
