<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class velflixCard extends Component
{
    /** @var mixed */
    public $movie;

    /** @var mixed */
    public $velflix;

    /**
     * @param  mixed  $movie
     * @return void
     */
    public function __construct($movie = null)
    {
        $this->movie = $movie;
        $this->velflix = $movie;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|\Closure|string
     */
    public function render()
    {
        return view('components.velflix-card');
    }
}
