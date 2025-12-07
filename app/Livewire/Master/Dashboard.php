<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
     #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.master.dashboard');
    }
}
