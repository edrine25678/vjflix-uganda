<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Series;
use App\Models\Watchlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    /**
     * Display the authenticated user's "My List" collection.
     */
    public function index(): View
    {
        $user = auth()->user();

        $watchlists = $user->watchlists()
            ->with(['watchable' => function ($morphTo) {
                $morphTo->morphWith([
                    Movie::class => ['vj', 'genres'],
                    Series::class => ['vj', 'genres'],
                ]);
            }])
            ->latest()
            ->get();

        $movies = $watchlists->where('watchable_type', Movie::class)->map->watchable->filter();
        $series = $watchlists->where('watchable_type', Series::class)->map->watchable->filter();

        return view('watchlist.index', [
            'watchlists' => $watchlists,
            'movies' => $movies,
            'series' => $series,
        ]);
    }

    /**
     * Toggle bookmark state for a movie or TV series.
     */
    public function toggle(Request $request)
    {
        $validated = $request->validate([
            'watchable_type' => ['required', 'string', 'in:movie,series'],
            'watchable_id' => ['required', 'integer'],
        ]);

        $modelClass = $validated['watchable_type'] === 'movie'
            ? Movie::class
            : Series::class;

        $item = $modelClass::findOrFail($validated['watchable_id']);
        $user = auth()->user();

        $existing = Watchlist::where('user_id', $user->id)
            ->where('watchable_type', $modelClass)
            ->where('watchable_id', $item->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWatchlist = false;
            $message = "Removed '{$item->title}' from My List.";
        } else {
            Watchlist::create([
                'user_id' => $user->id,
                'watchable_type' => $modelClass,
                'watchable_id' => $item->id,
            ]);
            $inWatchlist = true;
            $message = "Saved '{$item->title}' to My List!";
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'in_watchlist' => $inWatchlist,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
