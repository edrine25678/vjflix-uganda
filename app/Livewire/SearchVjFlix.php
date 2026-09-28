<?php

namespace App\Livewire;

use App\Models\Movie;
use App\Models\Vj;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SearchVjFlix extends Component
{
    public string $searchVjFlix = '';

    /**
     * @return View|Factory
     */
    public function render()
    {
        $localMovies = collect();
        $localVjs = collect();

        $query = trim($this->searchVjFlix);

        // Fixed logic: comparison now correctly outside strlen
        if (strlen($query) >= 2) {
            $localMovies = Movie::with(['vj', 'genres'])
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('original_title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                })
                ->limit(6)
                ->get();

            $localVjs = Vj::where('stage_name', 'like', "%{$query}%")
                ->orWhere('name', 'like', "%{$query}%")
                ->limit(3)
                ->get();
        }

        return view('livewire.search-vjflix', [
            'localMovies' => $localMovies,
            'localVjs' => $localVjs,
            'searchQuery' => $query,
        ]);
    }
}
