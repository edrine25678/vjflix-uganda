<?php

namespace App\Http\Controllers;

use App\Models\Vj;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class VjController extends Controller
{
    public function index(): View|Factory
    {
        $vjs = Vj::active()
            ->withCount('movies')
            ->orderByDesc('views_count')
            ->get();

        return view('vjs.index', [
            'vjs' => $vjs,
        ]);
    }

    public function show(string $slug): View|Factory
    {
        $vj = Vj::where('slug', $slug)->firstOrFail();

        $movies = $vj->movies()
            ->with('genres')
            ->published()
            ->latest('published_at')
            ->paginate(12);

        return view('vjs.show', [
            'vj' => $vj,
            'movies' => $movies,
        ]);
    }
}
