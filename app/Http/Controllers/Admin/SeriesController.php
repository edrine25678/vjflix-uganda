<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use App\Models\Season;
use App\Models\Series;
use App\Models\Vj;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeriesController extends Controller
{
    /**
     * Display a listing of series.
     */
    public function index(Request $request): View|Factory
    {
        $search = $request->query('search');

        $query = Series::with(['vj', 'genres', 'seasons.episodes']);

        if ($search) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhereHas('vj', fn ($q) => $q->where('stage_name', 'like', "%{$search}%"));
        }

        $series = $query->latest()->paginate(15);

        return view('admin.series.index', [
            'series' => $series,
            'search' => $search,
        ]);
    }

    /**
     * Show the form for creating a new series.
     */
    public function create(Request $request): View|Factory
    {
        $vjs = Vj::active()->orderBy('stage_name')->get();
        $genres = Genre::where('is_active', true)->orderBy('name')->get();

        $tmdbPrefill = null;
        if ($tmdbId = $request->query('tmdb_id')) {
            try {
                $tmdbService = app(\App\Services\TmdbService::class);
                if ($raw = $tmdbService->getSeriesDetails($tmdbId)) {
                    $tmdbPrefill = $tmdbService->formatSeriesForForm($raw);
                }
            } catch (\Throwable $e) {
                // Ignore TMDB error
            }
        }

        return view('admin.series.create', [
            'vjs' => $vjs,
            'genres' => $genres,
            'tmdbPrefill' => $tmdbPrefill,
        ]);
    }

    /**
     * Store a newly created series in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'vj_id' => ['nullable', 'exists:vjs,id'],
            'first_air_year' => ['nullable', 'integer', 'min:1950', 'max:2030'],
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
        while (Series::where('slug', $uniqueSlug)->exists()) {
            $uniqueSlug = "{$slug}-{$counter}";
            $counter++;
        }

        $series = Series::create([
            'title' => $validated['title'],
            'slug' => $uniqueSlug,
            'synopsis' => $validated['synopsis'] ?? null,
            'description' => $validated['description'] ?? null,
            'vj_id' => $validated['vj_id'] ?? null,
            'first_air_year' => $validated['first_air_year'] ?? date('Y'),
            'poster' => $poster,
            'backdrop' => $backdrop,
            'status' => $validated['status'],
            'featured' => $request->boolean('featured'),
            'trending' => $request->boolean('trending'),
            'published_at' => $validated['status'] === 'published' ? now() : null,
        ]);

        if (! empty($validated['genres'])) {
            $series->genres()->sync($validated['genres']);
        }

        // Automatically create Season 1 for this new series
        Season::create([
            'series_id' => $series->id,
            'season_number' => 1,
            'title' => 'Season 1',
            'overview' => 'Official Season 1 translated by '.($series->vj ? $series->vj->stage_name : 'Ugandan VJ'),
            'release_year' => $series->first_air_year,
        ]);

        return redirect()->route('admin.series.index')->with('success', "Series '{$series->title}' and Season 1 created successfully.");
    }

    /**
     * Show the form for editing the specified series.
     */
    public function edit(Series $series): View|Factory
    {
        $vjs = Vj::active()->orderBy('stage_name')->get();
        $genres = Genre::where('is_active', true)->orderBy('name')->get();

        return view('admin.series.edit', [
            'series' => $series->load(['genres', 'seasons.episodes']),
            'vjs' => $vjs,
            'genres' => $genres,
        ]);
    }

    /**
     * Update the specified series in storage.
     */
    public function update(Request $request, Series $series): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'synopsis' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'vj_id' => ['nullable', 'exists:vjs,id'],
            'first_air_year' => ['nullable', 'integer', 'min:1950', 'max:2030'],
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

        $poster = $validated['poster_url'] ?? $series->poster;
        if ($request->hasFile('poster_file')) {
            $poster = $request->file('poster_file')->store('uploads/posters', 'public');
        }

        $backdrop = $validated['backdrop_url'] ?? $series->backdrop;
        if ($request->hasFile('backdrop_file')) {
            $backdrop = $request->file('backdrop_file')->store('uploads/backdrops', 'public');
        }

        $series->update([
            'title' => $validated['title'],
            'synopsis' => $validated['synopsis'] ?? null,
            'description' => $validated['description'] ?? null,
            'vj_id' => $validated['vj_id'] ?? null,
            'first_air_year' => $validated['first_air_year'] ?? $series->first_air_year,
            'poster' => $poster,
            'backdrop' => $backdrop,
            'status' => $validated['status'],
            'featured' => $request->boolean('featured'),
            'trending' => $request->boolean('trending'),
            'published_at' => $validated['status'] === 'published' && ! $series->published_at ? now() : $series->published_at,
        ]);

        if (isset($validated['genres'])) {
            $series->genres()->sync($validated['genres']);
        } else {
            $series->genres()->detach();
        }

        return redirect()->route('admin.series.index')->with('success', "Series '{$series->title}' updated successfully.");
    }

    /**
     * Remove the specified series from storage.
     */
    public function destroy(Series $series): RedirectResponse
    {
        $title = $series->title;
        $series->delete();

        return redirect()->route('admin.series.index')->with('success', "Series '{$title}' deleted successfully.");
    }
}
