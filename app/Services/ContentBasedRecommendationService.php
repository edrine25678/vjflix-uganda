<?php

namespace App\Services;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Series;
use App\Models\User;
use App\Models\Vj;
use App\Models\WatchProgress;
use Illuminate\Support\Facades\DB;

class ContentBasedRecommendationService implements RecommendationServiceInterface
{
    /**
     * Get personalized recommendations for a user.
     */
    public function getPersonalizedRecommendations(User $user, int $limit = 10): array
    {
        // Combine multiple recommendation strategies
        $watchHistory = $this->getBasedOnWatchHistory($user, $limit / 2);
        $genreBased = $this->getBasedOnGenres($user, $limit / 2);

        $recommendations = collect()
            ->concat($watchHistory)
            ->concat($genreBased)
            ->unique('id')
            ->take($limit)
            ->values()
            ->all();

        return $recommendations;
    }

    /**
     * Get recommendations based on watch history.
     */
    public function getBasedOnWatchHistory(User $user, int $limit = 10): array
    {
        // Get recently watched movies and their genres/VJs
        $recentlyWatched = WatchProgress::where('user_id', $user->id)
            ->where('watchable_type', Movie::class)
            ->with('watchable.genres', 'watchable.vj')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        if ($recentlyWatched->isEmpty()) {
            return $this->getTrending($limit);
        }

        // Extract genres and VJs from watch history
        $genreIds = $recentlyWatched
            ->pluck('watchable.genres')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        $vjIds = $recentlyWatched
            ->pluck('watchable.vj_id')
            ->filter()
            ->unique()
            ->toArray();

        // Find similar movies
        $query = Movie::where('status', 'published')
            ->whereNotIn('id', $recentlyWatched->pluck('watchable_id'));

        if (! empty($genreIds)) {
            $query->whereHas('genres', function ($q) use ($genreIds) {
                $q->whereIn('genres.id', $genreIds);
            });
        }

        if (! empty($vjIds)) {
            $query->whereIn('vj_id', $vjIds);
        }

        return $query->with(['genres', 'vj'])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($movie) {
                return [
                    'id' => $movie->id,
                    'type' => 'movie',
                    'title' => $movie->title,
                    'slug' => $movie->slug,
                    'poster' => $movie->poster,
                    'year' => $movie->release_year,
                    'rating' => $movie->average_rating,
                    'vj' => $movie->vj ? $movie->vj->stage_name : null,
                    'genres' => $movie->genres->pluck('name')->toArray(),
                ];
            })
            ->toArray();
    }

    /**
     * Get recommendations based on a specific movie.
     */
    public function getSimilarMovies(int $movieId, int $limit = 10): array
    {
        $movie = Movie::with('genres', 'vj')->find($movieId);

        if (! $movie) {
            return [];
        }

        $genreIds = $movie->genres->pluck('id')->toArray();
        $vjId = $movie->vj_id;

        $similar = Movie::where('status', 'published')
            ->where('id', '!=', $movieId)
            ->where(function ($query) use ($genreIds, $vjId) {
                $query->whereHas('genres', function ($q) use ($genreIds) {
                    $q->whereIn('genres.id', $genreIds);
                });

                if ($vjId) {
                    $query->orWhere('vj_id', $vjId);
                }
            })
            ->with(['genres', 'vj'])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        return $similar->map(function ($m) {
            return [
                'id' => $m->id,
                'type' => 'movie',
                'title' => $m->title,
                'slug' => $m->slug,
                'poster' => $m->poster,
                'year' => $m->release_year,
                'rating' => $m->average_rating,
                'vj' => $m->vj ? $m->vj->stage_name : null,
                'genres' => $m->genres->pluck('name')->toArray(),
            ];
        })->toArray();
    }

    /**
     * Get recommendations based on a specific VJ.
     */
    public function getMoreFromVj(int $vjId, int $limit = 10): array
    {
        $vj = Vj::find($vjId);

        if (! $vj) {
            return [];
        }

        $movies = Movie::where('status', 'published')
            ->where('vj_id', $vjId)
            ->with(['genres', 'vj'])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        $series = Series::where('status', 'published')
            ->where('vj_id', $vjId)
            ->with(['genres', 'vj'])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        $recommendations = collect()
            ->concat($movies->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => 'movie',
                    'title' => $m->title,
                    'slug' => $m->slug,
                    'poster' => $m->poster,
                    'year' => $m->release_year,
                    'rating' => $m->average_rating,
                    'vj' => $m->vj ? $m->vj->stage_name : null,
                    'genres' => $m->genres->pluck('name')->toArray(),
                ];
            }))
            ->concat($series->map(function ($s) {
                return [
                    'id' => $s->id,
                    'type' => 'series',
                    'title' => $s->title,
                    'slug' => $s->slug,
                    'poster' => $s->poster,
                    'year' => $s->first_air_year,
                    'rating' => $s->average_rating,
                    'vj' => $s->vj ? $s->vj->stage_name : null,
                    'genres' => $s->genres->pluck('name')->toArray(),
                ];
            }))
            ->take($limit)
            ->values()
            ->all();

        return $recommendations;
    }

    /**
     * Get trending recommendations.
     */
    public function getTrending(int $limit = 10): array
    {
        $movies = Movie::where('status', 'published')
            ->where('trending', true)
            ->with(['genres', 'vj'])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        return $movies->map(function ($m) {
            return [
                'id' => $m->id,
                'type' => 'movie',
                'title' => $m->title,
                'slug' => $m->slug,
                'poster' => $m->poster,
                'year' => $m->release_year,
                'rating' => $m->average_rating,
                'vj' => $m->vj ? $m->vj->stage_name : null,
                'genres' => $m->genres->pluck('name')->toArray(),
            ];
        })->toArray();
    }

    /**
     * Get new releases.
     */
    public function getNewReleases(int $limit = 10): array
    {
        $movies = Movie::where('status', 'published')
            ->with(['genres', 'vj'])
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return $movies->map(function ($m) {
            return [
                'id' => $m->id,
                'type' => 'movie',
                'title' => $m->title,
                'slug' => $m->slug,
                'poster' => $m->poster,
                'year' => $m->release_year,
                'rating' => $m->average_rating,
                'vj' => $m->vj ? $m->vj->stage_name : null,
                'genres' => $m->genres->pluck('name')->toArray(),
            ];
        })->toArray();
    }

    /**
     * Get recommendations based on genre preferences.
     */
    public function getBasedOnGenres(User $user, int $limit = 10): array
    {
        // Get user's most watched genres
        $genreStats = DB::table('watch_progress')
            ->join('movies', 'watch_progress.watchable_id', '=', 'movies.id')
            ->join('movie_genre', 'movies.id', '=', 'movie_genre.movie_id')
            ->join('genres', 'movie_genre.genre_id', '=', 'genres.id')
            ->where('watch_progress.user_id', $user->id)
            ->where('watch_progress.watchable_type', Movie::class)
            ->select('genres.id', 'genres.name', DB::raw('COUNT(*) as watch_count'))
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('watch_count')
            ->limit(3)
            ->get();

        if ($genreStats->isEmpty()) {
            return $this->getTrending($limit);
        }

        $genreIds = $genreStats->pluck('id')->toArray();

        $movies = Movie::where('status', 'published')
            ->whereHas('genres', function ($q) use ($genreIds) {
                $q->whereIn('genres.id', $genreIds);
            })
            ->with(['genres', 'vj'])
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        return $movies->map(function ($m) {
            return [
                'id' => $m->id,
                'type' => 'movie',
                'title' => $m->title,
                'slug' => $m->slug,
                'poster' => $m->poster,
                'year' => $m->release_year,
                'rating' => $m->average_rating,
                'vj' => $m->vj ? $m->vj->stage_name : null,
                'genres' => $m->genres->pluck('name')->toArray(),
            ];
        })->toArray();
    }
}
