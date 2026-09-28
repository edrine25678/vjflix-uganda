<?php

namespace App\Livewire;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AdminController extends Component
{
    /**
     * @return View|Factory
     */
    public function render()
    {
        return view('livewire.admin-controller');
    }
}
