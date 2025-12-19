<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Villa;

class ActiveVillaHeader extends Component
{
    // Properti yang akan diperbarui dari sesi
    public $activeVillaName = '⚠️ Silakan pilih villa terlebih dahulu';
    
    // Kunci: Listener untuk event dari komponen Dashboard
    protected $listeners = ['villaSelected' => 'updateVilla'];
    

    public function mount()
    {
        $this->loadActiveVilla();
    }

    // Dipanggil saat component Dashboard memicu event 'villaSelected'
public function updateVilla($newVillaId)
{
    $this->loadActiveVilla();
}


    private function loadActiveVilla()
    {
        $villaId = session('villa_id');
        
        if ($villaId) {
            $villa = Villa::find($villaId);
            if ($villa) {
                $this->activeVillaName = "{$villa->nama_villa}";
            } else {
                $this->activeVillaName = 'Villa tidak ditemukan';
            }
        } else {
            $this->activeVillaName = '⚠️ Silakan pilih villa terlebih dahulu';
        }
    }

    public function render()
    {
        return view('livewire.active-villa-header');
    }
}