<?php

namespace App\Livewire\Master;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Pengeluaran extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.master.pengeluaran');
    }
}
