<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use App\Models\Vj;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin CMS metrics and overview.
     */
    public function index(): View|Factory
    {
        $stats = [
            'total_movies' => Movie::count(),
            'total_series' => Series::count(),
            'total_episodes' => Episode::count(),
            'total_vjs' => Vj::count(),
            'total_users' => User::count(),
            'total_movie_views' => Movie::sum('views'),
            'total_series_views' => Series::sum('views'),
        ];

        $recentMovies = Movie::with('vj')->latest()->limit(5)->get();
        $recentSeries = Series::with('vj')->latest()->limit(5)->get();
        $topVjs = Vj::withCount(['movies', 'series'])->orderByDesc('views_count')->limit(5)->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentMovies' => $recentMovies,
            'recentSeries' => $recentSeries,
            'topVjs' => $topVjs,
        ]);
    }
}
