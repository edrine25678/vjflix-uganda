<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Vj;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class VjFlixController extends Controller
{
    public function index(): View|Factory
    {
        // 1. Featured hero banner movie
        $heroMovie = Movie::with(['vj', 'genres'])
            ->featured()
            ->latest('published_at')
            ->first() ?? Movie::with(['vj', 'genres'])->latest('published_at')->first();

        // 2. Trending in Uganda (most viewed/trending)
        $trending = Movie::with(['vj', 'genres'])
            ->trending()
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        // 3. Latest Translations
        $latestTranslations = Movie::with(['vj', 'genres'])
            ->published()
            ->latest('published_at')
            ->limit(10)
            ->get();

        // 4. Featured Ugandan Video Jockeys
        $popularVjs = Vj::active()
            ->withCount('movies')
            ->orderByDesc('views_count')
            ->limit(6)
            ->get();

        // 5. Genre-specific movie rows
        $actionMovies = Movie::with(['vj', 'genres'])
            ->whereHas('genres', fn ($q) => $q->where('slug', 'action'))
            ->published()
            ->limit(10)
            ->get();

        $comedyMovies = Movie::with(['vj', 'genres'])
            ->whereHas('genres', fn ($q) => $q->where('slug', 'comedy'))
            ->published()
            ->limit(10)
            ->get();

        $sciFiMovies = Movie::with(['vj', 'genres'])
            ->whereHas('genres', fn ($q) => $q->where('slug', 'sci-fi-fantasy'))
            ->published()
            ->limit(10)
            ->get();

        $lugandaSpecial = Movie::with(['vj', 'genres'])
            ->whereHas('genres', fn ($q) => $q->where('slug', 'luganda-special'))
            ->published()
            ->limit(10)
            ->get();

        $genres = Genre::where('is_active', true)->pluck('name', 'id');

        $continueWatching = auth()->check() ? auth()->user()->continueWatching(8) : collect();

        return view('main', [
            'heroMovie' => $heroMovie,
            'trending' => $trending,
            'latestTranslations' => $latestTranslations,
            'popularVjs' => $popularVjs,
            'continueWatching' => $continueWatching,
            'actionMovies' => $actionMovies,
            'comedyMovies' => $comedyMovies,
            'sciFiMovies' => $sciFiMovies,
            'lugandaSpecial' => $lugandaSpecial,
            'genres' => $genres,
        ]);
    }

    /**
     * Display a specific movie translation.
     *
     * @param  string|int  $id
     */
    public function show($id): View|Factory
    {
        $movie = Movie::with(['vj', 'genres'])
            ->where('slug', $id)
            ->orWhere('id', $id)
            ->first();

        if (! $movie) {
            abort(404, 'Movie translation not found.');
        }

        $moreFromVj = $movie->vj_id
            ? Movie::with(['vj', 'genres'])
                ->where('vj_id', $movie->vj_id)
                ->where('id', '!=', $movie->id)
                ->published()
                ->limit(8)
                ->get()
            : collect();

        $similarMovies = Movie::with(['vj', 'genres'])
            ->where('id', '!=', $movie->id)
            ->published()
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        // Increment stream view count
        $movie->increment('views');

        return view('movies.show', [
            'movie' => $movie,
            'moreFromVj' => $moreFromVj,
            'similarMovies' => $similarMovies,
        ]);
    }
}
