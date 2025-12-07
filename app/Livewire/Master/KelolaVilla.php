<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;

class KelolaVilla extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.master.kelola-villa');
    }
}
