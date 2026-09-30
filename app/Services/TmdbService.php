<?php

namespace App\Services;

use App\Models\Genre;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TmdbService
{
    protected string $baseUrl = 'https://api.themoviedb.org/3';
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.tmdb.token');
    }

    /**
     * Check if TMDB API credentials are configured.
     */
    public function isConfigured(): bool
    {
        return ! empty($this->token);
    }

    /**
     * Send an HTTP request to the TMDB API.
     */
    protected function request(string $endpoint, array $params = []): array
    {
        if (! $this->isConfigured()) {
            return [];
        }

        try {
            $client = Http::timeout(10)->baseUrl($this->baseUrl);

            // Handle either Bearer JWT token or v3 32-char hex API key
            if (strlen($this->token) === 32 && ctype_xdigit($this->token)) {
                $params['api_key'] = $this->token;
                $response = $client->get($endpoint, $params);
            } else {
                $response = $client->withToken($this->token)->get($endpoint, $params);
            }

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::warning('TMDB API error', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Throwable $e) {
            Log::error('TMDB request exception: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
            ]);
            return [];
        }
    }

    /**
     * Search movies by query title.
     */
    public function searchMovies(string $query, int $page = 1): array
    {
        return $this->request('/search/movie', [
            'query' => $query,
            'page' => $page,
            'include_adult' => false,
        ]);
    }

    /**
     * Search TV series by query title.
     */
    public function searchSeries(string $query, int $page = 1): array
    {
        return $this->request('/search/tv', [
            'query' => $query,
            'page' => $page,
            'include_adult' => false,
        ]);
    }

    /**
     * Get details for a specific movie by TMDB ID.
     */
    public function getMovieDetails(int|string $tmdbId): ?array
    {
        $data = $this->request("/movie/{$tmdbId}", [
            'append_to_response' => 'videos,credits',
        ]);

        return ! empty($data['id']) ? $data : null;
    }

    /**
     * Get details for a specific TV series by TMDB ID.
     */
    public function getSeriesDetails(int|string $tmdbId): ?array
    {
        $data = $this->request("/tv/{$tmdbId}", [
            'append_to_response' => 'videos,credits',
        ]);

        return ! empty($data['id']) ? $data : null;
    }

    /**
     * Get popular movies on TMDB.
     */
    public function getPopularMovies(int $page = 1): array
    {
        return $this->request('/movie/popular', [
            'page' => $page,
        ]);
    }

    /**
     * Get trending movies of the week on TMDB.
     */
    public function getTrendingMovies(string $timeWindow = 'week'): array
    {
        return $this->request("/trending/movie/{$timeWindow}");
    }

    /**
     * Get popular TV series on TMDB.
     */
    public function getPopularSeries(int $page = 1): array
    {
        return $this->request('/tv/popular', [
            'page' => $page,
        ]);
    }

    /**
     * Get trending TV series of the week on TMDB.
     */
    public function getTrendingSeries(string $timeWindow = 'week'): array
    {
        return $this->request("/trending/tv/{$timeWindow}");
    }

    /**
     * Format a raw TMDB movie payload into fields ready for the VJFlix Movie create/edit form.
     */
    public function formatMovieForForm(array $tmdb): array
    {
        $releaseDate = $tmdb['release_date'] ?? null;
        $releaseYear = $releaseDate ? (int) substr($releaseDate, 0, 4) : date('Y');

        $posterPath = $tmdb['poster_path'] ?? null;
        $backdropPath = $tmdb['backdrop_path'] ?? null;

        $trailerUrl = null;
        if (! empty($tmdb['videos']['results'])) {
            foreach ($tmdb['videos']['results'] as $video) {
                if (($video['site'] ?? '') === 'YouTube' && ($video['type'] ?? '') === 'Trailer') {
                    $trailerUrl = 'https://www.youtube.com/watch?v=' . $video['key'];
                    break;
                }
            }
        }

        // Match TMDB genres to local genres table
        $genreIds = [];
        $localGenres = Genre::where('is_active', true)->get();

        $tmdbGenreNames = [];
        if (! empty($tmdb['genres'])) {
            $tmdbGenreNames = array_column($tmdb['genres'], 'name');
        } elseif (! empty($tmdb['genre_ids'])) {
            $genreMap = $this->getTmdbGenreMap();
            foreach ($tmdb['genre_ids'] as $gid) {
                if (isset($genreMap[$gid])) {
                    $tmdbGenreNames[] = $genreMap[$gid];
                }
            }
        }

        foreach ($tmdbGenreNames as $tmdbName) {
            foreach ($localGenres as $localGenre) {
                if ($this->genresMatch($tmdbName, $localGenre->name)) {
                    $genreIds[] = $localGenre->id;
                }
            }
        }

        $overview = $tmdb['overview'] ?? '';
        $tagline = $tmdb['tagline'] ?? '';

        return [
            'tmdb_id' => $tmdb['id'] ?? null,
            'title' => $tmdb['title'] ?? '',
            'translated_title_suggestion' => ($tmdb['title'] ?? '') . ' (Luganda)',
            'original_title' => $tmdb['original_title'] ?? ($tmdb['title'] ?? ''),
            'release_year' => $releaseYear,
            'duration' => $tmdb['runtime'] ?? 120,
            'synopsis' => ! empty($tagline) ? $tagline : (strlen($overview) > 160 ? substr($overview, 0, 157) . '...' : $overview),
            'description' => $overview,
            'poster_url' => $posterPath ? 'https://image.tmdb.org/t/p/w500' . $posterPath : '',
            'backdrop_url' => $backdropPath ? 'https://image.tmdb.org/t/p/original' . $backdropPath : '',
            'trailer_url' => $trailerUrl,
            'rating' => round($tmdb['vote_average'] ?? 0, 2),
            'genre_ids' => array_values(array_unique($genreIds)),
            'country_of_origin' => ! empty($tmdb['production_countries'][0]['name']) ? $tmdb['production_countries'][0]['name'] : 'United States',
            'original_language' => $tmdb['original_language'] ?? 'en',
        ];
    }

    /**
     * Format a raw TMDB TV payload into fields ready for the Series form.
     */
    public function formatSeriesForForm(array $tmdb): array
    {
        $airDate = $tmdb['first_air_date'] ?? null;
        $firstAirYear = $airDate ? (int) substr($airDate, 0, 4) : date('Y');

        $posterPath = $tmdb['poster_path'] ?? null;
        $backdropPath = $tmdb['backdrop_path'] ?? null;

        $genreIds = [];
        $localGenres = Genre::where('is_active', true)->get();

        $tmdbGenreNames = [];
        if (! empty($tmdb['genres'])) {
            $tmdbGenreNames = array_column($tmdb['genres'], 'name');
        } elseif (! empty($tmdb['genre_ids'])) {
            $genreMap = $this->getTmdbGenreMap();
            foreach ($tmdb['genre_ids'] as $gid) {
                if (isset($genreMap[$gid])) {
                    $tmdbGenreNames[] = $genreMap[$gid];
                }
            }
        }

        foreach ($tmdbGenreNames as $tmdbName) {
            foreach ($localGenres as $localGenre) {
                if ($this->genresMatch($tmdbName, $localGenre->name)) {
                    $genreIds[] = $localGenre->id;
                }
            }
        }

        $overview = $tmdb['overview'] ?? '';
        $name = $tmdb['name'] ?? '';

        return [
            'tmdb_id' => $tmdb['id'] ?? null,
            'title' => $name,
            'translated_title_suggestion' => "{$name} (Luganda)",
            'first_air_year' => $firstAirYear,
            'synopsis' => strlen($overview) > 160 ? substr($overview, 0, 157) . '...' : $overview,
            'description' => $overview,
            'poster_url' => $posterPath ? 'https://image.tmdb.org/t/p/w500' . $posterPath : '',
            'backdrop_url' => $backdropPath ? 'https://image.tmdb.org/t/p/original' . $backdropPath : '',
            'genre_ids' => array_values(array_unique($genreIds)),
            'number_of_seasons' => $tmdb['number_of_seasons'] ?? 1,
            'number_of_episodes' => $tmdb['number_of_episodes'] ?? 1,
            'rating' => round($tmdb['vote_average'] ?? 0, 2),
        ];
    }

    /**
     * Map TMDB genre names to local database genre names.
     */
    protected function genresMatch(string $tmdbGenre, string $localGenre): bool
    {
        $tmdbGenre = strtolower(trim($tmdbGenre));
        $localGenre = strtolower(trim($localGenre));

        if ($tmdbGenre === $localGenre) {
            return true;
        }

        if (str_contains($localGenre, $tmdbGenre) || str_contains($tmdbGenre, $localGenre)) {
            return true;
        }

        if ($tmdbGenre === 'science fiction' && str_contains($localGenre, 'sci-fi')) {
            return true;
        }

        if ($tmdbGenre === 'action & adventure' && str_contains($localGenre, 'action')) {
            return true;
        }

        return false;
    }

    /**
     * Standard TMDB genre ID map.
     */
    protected function getTmdbGenreMap(): array
    {
        return [
            28 => 'Action',
            12 => 'Adventure',
            16 => 'Animation',
            35 => 'Comedy',
            80 => 'Crime',
            99 => 'Documentary',
            18 => 'Drama',
            10751 => 'Family',
            14 => 'Fantasy',
            36 => 'History',
            27 => 'Horror',
            10402 => 'Music',
            9648 => 'Mystery',
            10749 => 'Romance',
            878 => 'Science Fiction',
            10770 => 'TV Movie',
            53 => 'Thriller',
            10752 => 'War',
            37 => 'Western',
            10759 => 'Action & Adventure',
            10762 => 'Kids',
            10763 => 'News',
            10764 => 'Reality',
            10765 => 'Sci-Fi & Fantasy',
            10766 => 'Soap',
            10767 => 'Talk',
            10768 => 'War & Politics',
        ];
    }
}
