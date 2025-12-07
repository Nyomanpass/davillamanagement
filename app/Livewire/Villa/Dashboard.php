<?php

namespace App\Livewire\Villa;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.villa.dashboard');
    }
}
