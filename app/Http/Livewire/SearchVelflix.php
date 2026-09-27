<?php

namespace App\Http\Livewire;

use App\Models\Movie;
use App\Models\Vj;
use Livewire\Component;

class SearchVelflix extends Component
{
    public string $searchVelflix = '';

    /**
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function render()
    {
        $localMovies = collect();
        $localVjs = collect();

        $query = trim($this->searchVelflix);

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

        return view('livewire.search-velflix', [
            'localMovies' => $localMovies,
            'localVjs' => $localVjs,
            'searchQuery' => $query,
        ]);
    }
}
