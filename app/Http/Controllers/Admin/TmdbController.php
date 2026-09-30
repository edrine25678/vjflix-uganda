<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Series;
use App\Models\Vj;
use App\Services\TmdbService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TmdbController extends Controller
{
    protected TmdbService $tmdb;

    public function __construct(TmdbService $tmdb)
    {
        $this->tmdb = $tmdb;
    }

    /**
     * Display the TMDB Resource Hub for exploring & importing content.
     */
    public function index(Request $request): View|Factory
    {
        $tab = $request->query('tab', 'popular_movies');
        $query = trim($request->query('query', ''));
        $page = max(1, (int) $request->query('page', 1));

        $results = [];
        $totalPages = 1;
        $configured = $this->tmdb->isConfigured();

        if ($configured) {
            if (! empty($query)) {
                $type = $request->query('type', 'movie');
                $response = $type === 'tv'
                    ? $this->tmdb->searchSeries($query, $page)
                    : $this->tmdb->searchMovies($query, $page);

                $results = $response['results'] ?? [];
                $totalPages = min(50, $response['total_pages'] ?? 1);
            } else {
                switch ($tab) {
                    case 'trending_movies':
                        $response = $this->tmdb->getTrendingMovies();
                        $results = $response['results'] ?? [];
                        break;
                    case 'popular_series':
                        $response = $this->tmdb->getPopularSeries($page);
                        $results = $response['results'] ?? [];
                        $totalPages = min(50, $response['total_pages'] ?? 1);
                        break;
                    case 'trending_series':
                        $response = $this->tmdb->getTrendingSeries();
                        $results = $response['results'] ?? [];
                        break;
                    case 'popular_movies':
                    default:
                        $response = $this->tmdb->getPopularMovies($page);
                        $results = $response['results'] ?? [];
                        $totalPages = min(50, $response['total_pages'] ?? 1);
                        break;
                }
            }
        }

        $vjs = Vj::active()->orderBy('stage_name')->get();

        return view('admin.tmdb.index', [
            'tab' => $tab,
            'query' => $query,
            'page' => $page,
            'totalPages' => $totalPages,
            'results' => $results,
            'configured' => $configured,
            'vjs' => $vjs,
        ]);
    }

    /**
     * JSON search endpoint for quick-fetch & autocomplete in movie/series forms.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->query('query', ''));
        $type = $request->query('type', 'movie');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $response = $type === 'tv'
            ? $this->tmdb->searchSeries($query)
            : $this->tmdb->searchMovies($query);

        $items = array_slice($response['results'] ?? [], 0, 10);
        $formatted = [];

        foreach ($items as $item) {
            $isTv = $type === 'tv';
            $title = $isTv ? ($item['name'] ?? '') : ($item['title'] ?? '');
            $date = $isTv ? ($item['first_air_date'] ?? '') : ($item['release_date'] ?? '');
            $year = $date ? substr($date, 0, 4) : '';
            $poster = ! empty($item['poster_path']) ? 'https://image.tmdb.org/t/p/w200' . $item['poster_path'] : null;

            $formatted[] = [
                'id' => $item['id'],
                'title' => $title,
                'year' => $year,
                'poster' => $poster,
                'overview' => Str::limit($item['overview'] ?? '', 120),
                'rating' => round($item['vote_average'] ?? 0, 1),
            ];
        }

        return response()->json(['results' => $formatted]);
    }

    /**
     * JSON endpoint to get formatted movie or series details for instant form auto-fill.
     */
    public function details(Request $request, string $type, string $id): JsonResponse
    {
        if ($type === 'tv') {
            $data = $this->tmdb->getSeriesDetails($id);
            if (! $data) {
                return response()->json(['error' => 'Series not found on TMDB'], 404);
            }
            return response()->json($this->tmdb->formatSeriesForForm($data));
        }

        $data = $this->tmdb->getMovieDetails($id);
        if (! $data) {
            return response()->json(['error' => 'Movie not found on TMDB'], 404);
        }

        return response()->json($this->tmdb->formatMovieForForm($data));
    }

    /**
     * Direct 1-click import from TMDB into the database.
     */
    public function import(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tmdb_id' => ['required'],
            'type' => ['required', 'in:movie,tv'],
            'vj_id' => ['nullable', 'exists:vjs,id'],
            'status' => ['required', 'in:published,draft,archived'],
        ]);

        if ($validated['type'] === 'tv') {
            $raw = $this->tmdb->getSeriesDetails($validated['tmdb_id']);
            if (! $raw) {
                return back()->with('error', 'Could not fetch series data from TMDB.');
            }

            $formatted = $this->tmdb->formatSeriesForForm($raw);
            $slug = Str::slug($formatted['translated_title_suggestion']);
            $uniqueSlug = $slug;
            $counter = 1;
            while (Series::where('slug', $uniqueSlug)->exists()) {
                $uniqueSlug = "{$slug}-{$counter}";
                $counter++;
            }

            $series = Series::create([
                'title' => $formatted['translated_title_suggestion'],
                'slug' => $uniqueSlug,
                'synopsis' => $formatted['synopsis'],
                'description' => $formatted['description'],
                'poster' => $formatted['poster_url'],
                'backdrop' => $formatted['backdrop_url'],
                'vj_id' => $validated['vj_id'] ?? null,
                'first_air_year' => $formatted['first_air_year'],
                'status' => $validated['status'],
                'average_rating' => $formatted['rating'],
                'published_at' => $validated['status'] === 'published' ? now() : null,
            ]);

            if (! empty($formatted['genre_ids'])) {
                $series->genres()->sync($formatted['genre_ids']);
            }

            return redirect()->route('admin.series.edit', $series->id)
                ->with('success', "Imported \"{$series->title}\" from TMDB! You can now customize episodes.");
        }

        $raw = $this->tmdb->getMovieDetails($validated['tmdb_id']);
        if (! $raw) {
            return back()->with('error', 'Could not fetch movie data from TMDB.');
        }

        $formatted = $this->tmdb->formatMovieForForm($raw);
        $slug = Str::slug($formatted['translated_title_suggestion']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Movie::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-{$counter}";
            $counter++;
        }

        $movie = Movie::create([
            'title' => $formatted['translated_title_suggestion'],
            'slug' => $uniqueSlug,
            'original_title' => $formatted['original_title'],
            'synopsis' => $formatted['synopsis'],
            'description' => $formatted['description'],
            'poster' => $formatted['poster_url'],
            'backdrop' => $formatted['backdrop_url'],
            'trailer_url' => $formatted['trailer_url'],
            'duration' => $formatted['duration'],
            'release_year' => $formatted['release_year'],
            'vj_id' => $validated['vj_id'] ?? null,
            'country_of_origin' => $formatted['country_of_origin'],
            'original_language' => $formatted['original_language'],
            'status' => $validated['status'],
            'average_rating' => $formatted['rating'],
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        if (! empty($formatted['genre_ids'])) {
            $movie->genres()->sync($formatted['genre_ids']);
        }

        return redirect()->route('admin.movies.edit', $movie->id)
            ->with('success', "Imported \"{$movie->title}\" from TMDB! Video URL and translation notes can be added below.");
    }
}
