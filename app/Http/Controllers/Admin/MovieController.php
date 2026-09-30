<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\Vj;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    /**
     * Display a listing of movies.
     */
    public function index(Request $request): View|Factory
    {
        $search = $request->query('search');

        $query = Movie::with(['vj', 'genres']);

        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhereHas('vj', fn ($q) => $q->where('stage_name', 'like', "%{$search}%"));
        }

        $movies = $query->latest()->paginate(15);

        return view('admin.movies.index', [
            'movies' => $movies,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new movie.
     */
    public function create(Request $request): View|Factory
    {
        $vjs = Vj::active()->orderBy('stage_name')->get();
        $genres = Genre::where('is_active', true)->orderBy('name')->get();

        $tmdbPrefill = null;
        if ($tmdbId = $request->query('tmdb_id')) {
            try {
                $tmdbService = app(\App\Services\TmdbService::class);
                if ($raw = $tmdbService->getMovieDetails($tmdbId)) {
                    $tmdbPrefill = $tmdbService->formatMovieForForm($raw);
                }
            } catch (\Throwable $e) {
                // Ignore TMDB error and show regular blank form
            }
        }

        return view('admin.movies.create', [
            'vjs' => $vjs,
            'genres' => $genres,
            'tmdbPrefill' => $tmdbPrefill,
        ]);
    }

    /**
     * Store a newly created movie in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'original_title' => ['nullable', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'vj_id' => ['nullable', 'exists:vjs,id'],
            'release_year' => ['nullable', 'integer', 'min:1950', 'max:2030'],
            'duration' => ['nullable', 'integer'],
            'video_url' => ['nullable', 'url'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-matroska,video/ogg', 'max:1048576'],
            'trailer_url' => ['nullable', 'url'],
            'poster_url' => ['nullable', 'url'],
            'poster_file' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:4096'],
            'backdrop_url' => ['nullable', 'url'],
            'backdrop_file' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:4096'],
            'status' => ['required', 'in:published,draft,archived'],
            'featured' => ['nullable', 'boolean'],
            'trending' => ['nullable', 'boolean'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
        ]);

        $videoPath = null;
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('uploads/videos', 'public');
        }

        $poster = $validated['poster_url'] ?? null;
        if ($request->hasFile('poster_file')) {
            $poster = $request->file('poster_file')->store('uploads/posters', 'public');
        }

        $backdrop = $validated['backdrop_url'] ?? null;
        if ($request->hasFile('backdrop_file')) {
            $backdrop = $request->file('backdrop_file')->store('uploads/backdrops', 'public');
        }

        $slug = Str::slug($validated['title']);
        $uniqueSlug = $slug;
        $counter = 1;
        while (Movie::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-{$counter}";
            $counter++;
        }

        $movie = Movie::create([
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'original_title' => $validated['original_title'] ?? null,
            'synopsis' => $validated['synopsis'] ?? null,
            'description' => $validated['description'] ?? null,
            'vj_id' => $validated['vj_id'] ?? null,
            'release_year' => $validated['release_year'] ?? date('Y'),
            'duration' => $validated['duration'] ?? 110,
            'video_url' => $validated['video_url'] ?? null,
            'video_path' => $videoPath,
            'trailer_url' => $validated['trailer_url'] ?? null,
            'poster' => $poster,
            'backdrop' => $backdrop,
            'status' => $validated['status'],
            'featured' => $request->boolean('featured'),
            'trending' => $request->boolean('trending'),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        if (! empty($validated['genres'])) {
            $movie->genres()->sync($validated['genres']);
        }

        return redirect()->route('admin.movies.index')->with('success', "Movie '{$movie->title}' created successfully.");
    }

    /**
     * Show the form for editing the specified movie.
     */
    public function edit(Movie $movie): View|Factory
    {
        $vjs = Vj::active()->orderBy('stage_name')->get();
        $genres = Genre::where('is_active', true)->orderBy('name')->get();

        return view('admin.movies.edit', [
            'movie' => $movie->load('genres'),
            'vjs' => $vjs,
            'genres' => $genres,
        ]);
    }

    /**
     * Update the specified movie in storage.
     */
    public function update(Request $request, Movie $movie): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'original_title' => ['nullable', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'vj_id' => ['nullable', 'exists:vjs,id'],
            'release_year' => ['nullable', 'integer', 'min:1950', 'max:2030'],
            'duration' => ['nullable', 'integer'],
            'video_url' => ['nullable', 'url'],
            'video_file' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/webm,video/x-matroska,video/ogg', 'max:1048576'],
            'trailer_url' => ['nullable', 'url'],
            'poster_url' => ['nullable', 'url'],
            'poster_file' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:4096'],
            'backdrop_url' => ['nullable', 'url'],
            'backdrop_file' => ['nullable', 'image', 'mimes:jpeg,png,webp,jpg', 'max:4096'],
            'status' => ['required', 'in:published,draft,archived'],
            'featured' => ['nullable', 'boolean'],
            'trending' => ['nullable', 'boolean'],
            'genres' => ['nullable', 'array'],
            'genres.*' => ['exists:genres,id'],
        ]);

        $videoPath = $movie->video_path;
        if ($request->hasFile('video_file')) {
            $videoPath = $request->file('video_file')->store('uploads/videos', 'public');
        }

        $poster = $validated['poster_url'] ?? $movie->poster;
        if ($request->hasFile('poster_file')) {
            $poster = $request->file('poster_file')->store('uploads/posters', 'public');
        }

        $backdrop = $validated['backdrop_url'] ?? $movie->backdrop;
        if ($request->hasFile('backdrop_file')) {
            $backdrop = $request->file('backdrop_file')->store('uploads/backdrops', 'public');
        }

        $movie->update([
            'title' => $validated['title'],
            'original_title' => $validated['original_title'] ?? $movie->original_title,
            'synopsis' => $validated['synopsis'] ?? null,
            'description' => $validated['description'] ?? null,
            'vj_id' => $validated['vj_id'] ?? null,
            'release_year' => $validated['release_year'] ?? $movie->release_year,
            'duration' => $validated['duration'] ?? $movie->duration,
            'video_url' => $validated['video_url'] ?? $movie->video_url,
            'video_path' => $videoPath,
            'trailer_url' => $validated['trailer_url'] ?? $movie->trailer_url,
            'poster' => $poster,
            'backdrop' => $backdrop,
            'status' => $validated['status'],
            'featured' => $request->boolean('featured'),
            'trending' => $request->boolean('trending'),
            'published_at' => $validated['status'] === 'published' && ! $movie->published_at ? now() : $movie->published_at,
        ]);

        if (isset($validated['genres'])) {
            $movie->genres()->sync($validated['genres']);
        } else {
            $movie->genres()->detach();
        }

        return redirect()->route('admin.movies.index')->with('success', "Movie '{$movie->title}' updated successfully.");
    }

    /**
     * Remove the specified movie from storage.
     */
    public function destroy(Movie $movie): RedirectResponse
    {
        $title = $movie->title;
        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', "Movie '{$title}' deleted successfully.");
    }
}
