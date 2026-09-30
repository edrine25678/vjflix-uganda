<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\Movie;
use App\Models\Series;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroSlideController extends Controller
{
    /**
     * Display hero banner posters management.
     */
    public function index(): View|Factory
    {
        $slides = HeroSlide::with(['movie.vj', 'series.vj'])
            ->orderBy('sort_order')
            ->get();

        $availableMovies = Movie::with('vj')
            ->published()
            ->latest('published_at')
            ->limit(30)
            ->get();

        $availableSeries = Series::with('vj')
            ->published()
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.hero.index', [
            'slides' => $slides,
            'availableMovies' => $availableMovies,
            'availableSeries' => $availableSeries,
            'maxLimit' => 6,
            'remainingSlots' => max(0, 6 - $slides->count()),
        ]);
    }

    /**
     * Store a new hero poster (maximum 6 posters).
     */
    public function store(Request $request): RedirectResponse
    {
        if (HeroSlide::count() >= 6) {
            return back()->withErrors(['hero' => 'You can add up to 6 hero posters. Please remove or edit an existing poster first.']);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'vj_name' => ['nullable', 'string', 'max:255'],
            'release_year' => ['nullable', 'string', 'max:10'],
            'rating' => ['nullable', 'string', 'max:10'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'synopsis' => ['nullable', 'string'],
            'backdrop_url' => ['nullable', 'url', 'max:500'],
            'backdrop_file' => ['nullable', 'image', 'max:5120'], // up to 5MB
            'watch_url' => ['nullable', 'string', 'max:500'],
            'download_url' => ['nullable', 'string', 'max:500'],
            'movie_id' => ['nullable', 'integer', 'exists:movies,id'],
            'series_id' => ['nullable', 'integer', 'exists:series,id'],
            'sort_order' => ['nullable', 'integer', 'min:1', 'max:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $backdropPath = null;
        if ($request->hasFile('backdrop_file')) {
            $backdropPath = $request->file('backdrop_file')->store('hero-backdrops', 'public');
        }

        $nextOrder = (int) ($validated['sort_order'] ?? (HeroSlide::max('sort_order') + 1));

        HeroSlide::create([
            'title' => $validated['title'],
            'vj_name' => $validated['vj_name'] ?? null,
            'release_year' => $validated['release_year'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'badge_text' => $validated['badge_text'] ?? 'HD Luganda',
            'synopsis' => $validated['synopsis'] ?? null,
            'backdrop_url' => $validated['backdrop_url'] ?? null,
            'backdrop_path' => $backdropPath,
            'watch_url' => $validated['watch_url'] ?? null,
            'download_url' => $validated['download_url'] ?? null,
            'movie_id' => $validated['movie_id'] ?? null,
            'series_id' => $validated['series_id'] ?? null,
            'sort_order' => $nextOrder,
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : true,
        ]);

        return redirect()->route('admin.hero.index')->with('success', 'Hero poster added successfully (Maximum 6 allowed).');
    }

    /**
     * Quick-add a Movie from the catalog into the Hero Banner.
     */
    public function quickAddMovie(Movie $movie): RedirectResponse
    {
        if (HeroSlide::count() >= 6) {
            return back()->withErrors(['hero' => 'Cannot add more than 6 hero posters. Please remove one first.']);
        }

        $nextOrder = (int) (HeroSlide::max('sort_order') + 1);

        HeroSlide::create([
            'title' => $movie->title,
            'vj_name' => $movie->vj ? $movie->vj->stage_name : 'Luganda',
            'release_year' => (string) $movie->release_year,
            'rating' => number_format($movie->average_rating, 1),
            'badge_text' => 'HD Luganda',
            'synopsis' => $movie->synopsis ?: $movie->description,
            'backdrop_url' => $movie->backdropUrl(),
            'movie_id' => $movie->id,
            'watch_url' => route('movies.show', $movie->slug),
            'download_url' => route('movies.download', $movie->slug),
            'sort_order' => $nextOrder,
            'is_active' => true,
        ]);

        return redirect()->route('admin.hero.index')->with('success', "Added '{$movie->title}' to the Hero Banner.");
    }

    /**
     * Update an existing hero poster slide.
     */
    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'vj_name' => ['nullable', 'string', 'max:255'],
            'release_year' => ['nullable', 'string', 'max:10'],
            'rating' => ['nullable', 'string', 'max:10'],
            'badge_text' => ['nullable', 'string', 'max:50'],
            'synopsis' => ['nullable', 'string'],
            'backdrop_url' => ['nullable', 'url', 'max:500'],
            'backdrop_file' => ['nullable', 'image', 'max:5120'],
            'watch_url' => ['nullable', 'string', 'max:500'],
            'download_url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:1', 'max:6'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('backdrop_file')) {
            if ($heroSlide->backdrop_path && Storage::disk('public')->exists($heroSlide->backdrop_path)) {
                Storage::disk('public')->delete($heroSlide->backdrop_path);
            }
            $heroSlide->backdrop_path = $request->file('backdrop_file')->store('hero-backdrops', 'public');
        }

        $heroSlide->update([
            'title' => $validated['title'],
            'vj_name' => $validated['vj_name'] ?? null,
            'release_year' => $validated['release_year'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'badge_text' => $validated['badge_text'] ?? 'HD Luganda',
            'synopsis' => $validated['synopsis'] ?? null,
            'backdrop_url' => $validated['backdrop_url'] ?? null,
            'watch_url' => $validated['watch_url'] ?? null,
            'download_url' => $validated['download_url'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_active' => $request->has('is_active') ? (bool) $request->input('is_active') : false,
        ]);

        return redirect()->route('admin.hero.index')->with('success', "Hero poster '{$heroSlide->title}' updated.");
    }

    /**
     * Delete a hero slide.
     */
    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        if ($heroSlide->backdrop_path && Storage::disk('public')->exists($heroSlide->backdrop_path)) {
            Storage::disk('public')->delete($heroSlide->backdrop_path);
        }

        $title = $heroSlide->title;
        $heroSlide->delete();

        return redirect()->route('admin.hero.index')->with('success', "Poster '{$title}' removed from Hero carousel.");
    }
}
