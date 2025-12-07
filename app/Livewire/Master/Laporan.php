<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Laporan extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.master.laporan');
    }
}
