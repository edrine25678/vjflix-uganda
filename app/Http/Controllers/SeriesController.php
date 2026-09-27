<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Genre;
use App\Models\Season;
use App\Models\Series;
use App\Models\Vj;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SeriesController extends Controller
{
    /**
     * Display the TV Series catalog.
     */
    public function index(Request $request): View|Factory
    {
        $selectedGenre = $request->query('genre');
        $selectedVj = $request->query('vj');

        $query = Series::with(['vj', 'genres', 'seasons.episodes'])->published();

        if ($selectedGenre) {
            $query->whereHas('genres', fn ($q) => $q->where('slug', $selectedGenre));
        }

        if ($selectedVj) {
            $query->whereHas('vj', fn ($q) => $q->where('slug', $selectedVj));
        }

        $allSeries = $query->latest('published_at')->paginate(15);

        // Featured showcase hero
        $heroSeries = Series::with(['vj', 'genres', 'seasons'])
            ->featured()
            ->latest('published_at')
            ->first() ?? Series::with(['vj', 'genres', 'seasons'])->latest('published_at')->first();

        // Trending Series in Uganda
        $trendingSeries = Series::with(['vj', 'genres'])
            ->trending()
            ->orderByDesc('views')
            ->limit(8)
            ->get();

        // Top VJs who translate series
        $seriesVjs = Vj::active()
            ->whereHas('series')
            ->withCount('series')
            ->orderByDesc('series_count')
            ->get();

        $genres = Genre::where('is_active', true)->pluck('name', 'slug');

        return view('series.index', [
            'heroSeries' => $heroSeries,
            'trendingSeries' => $trendingSeries,
            'allSeries' => $allSeries,
            'seriesVjs' => $seriesVjs,
            'genres' => $genres,
            'selectedGenre' => $selectedGenre,
            'selectedVj' => $selectedVj,
        ]);
    }

    /**
     * Display the detail page for a TV Series.
     */
    public function show(string $slug): View|Factory
    {
        $series = Series::with([
            'vj',
            'genres',
            'seasons' => fn ($q) => $q->orderBy('season_number'),
            'seasons.episodes' => fn ($q) => $q->orderBy('episode_number')->with('vj'),
        ])
        ->where('slug', $slug)
        ->orWhere('id', $slug)
        ->first();

        if (! $series) {
            abort(404, 'Series translation not found.');
        }

        // Increment series view counter
        $series->increment('views');

        $moreByVj = $series->vj_id
            ? Series::with(['vj', 'genres'])
                ->where('vj_id', $series->vj_id)
                ->where('id', '!=', $series->id)
                ->published()
                ->limit(6)
                ->get()
            : collect();

        $firstEpisode = null;
        if ($series->seasons->isNotEmpty() && $series->seasons->first()->episodes->isNotEmpty()) {
            $firstEpisode = $series->seasons->first()->episodes->first();
        }

        return view('series.show', [
            'series' => $series,
            'moreByVj' => $moreByVj,
            'firstEpisode' => $firstEpisode,
        ]);
    }

    /**
     * Watch a specific episode of a series.
     */
    public function watch(string $seriesSlug, int $seasonNumber = 1, int $episodeNumber = 1): View|Factory
    {
        $series = Series::with(['vj', 'genres', 'seasons.episodes'])->where('slug', $seriesSlug)->firstOrFail();

        $season = Season::where('series_id', $series->id)
            ->where('season_number', $seasonNumber)
            ->firstOrFail();

        $episode = Episode::with('vj')
            ->where('season_id', $season->id)
            ->where('episode_number', $episodeNumber)
            ->firstOrFail();

        // Increment episode views
        $episode->increment('views');

        // Next and Previous episodes
        $nextEpisode = Episode::where('season_id', $season->id)
            ->where('episode_number', '>', $episodeNumber)
            ->orderBy('episode_number')
            ->first();

        $prevEpisode = Episode::where('season_id', $season->id)
            ->where('episode_number', '<', $episodeNumber)
            ->orderByDesc('episode_number')
            ->first();

        return view('series.watch', [
            'series' => $series,
            'season' => $season,
            'episode' => $episode,
            'nextEpisode' => $nextEpisode,
            'prevEpisode' => $prevEpisode,
        ]);
    }
}
