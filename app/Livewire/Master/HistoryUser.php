<?php

namespace App\Livewire\Master;
use Livewire\Attributes\Layout;

use Livewire\Component;
// use Livewire\WithPagination; // Uncomment jika menggunakan fitur pagination

class HistoryUser extends Component
{
    #[Layout('layouts.app')]
    // use WithPagination; // Uncomment jika menggunakan fitur pagination
    
    public $selectedMonth = '12';
    public $selectedYear = '2025';
    public $perPage = 10;
    public $totalData = 0; // Harus dihitung dari query database
    
    // Properti ini harus diisi dengan hasil query ke database
    // Diberikan placeholder data array untuk demo
    public $activities = []; 

    public function mount()
    {
        // Atur default data untuk demo
        $this->activities = [
            // Data yang sama dengan yang ada di blade placeholder
            ['waktu' => '07-12-2025 09:30', 'nama_user' => 'Admin Villa Jimbaran', 'aktivitas' => 'Login'],
            ['waktu' => '07-12-2025 10:00', 'nama_user' => 'Admin Villa Uluwatu', 'aktivitas' => 'Logout'],
            ['waktu' => '07-12-2025 11:15', 'nama_user' => 'Super Master', 'aktivitas' => 'Tambah Pendapatan'],
        ];
        $this->totalData = count($this->activities);
    }
    
    // Method yang akan dipanggil setiap kali filter berubah
    public function updated()
    {
        // Logika untuk mengambil data riwayat aktivitas berdasarkan filter 
        // $this->activities = ActivityLog::filterByMonth($this->selectedMonth)->filterByYear($this->selectedYear)->paginate($this->perPage);
        // $this->totalData = $this->activities->total();
    }

    public function render()
    {
        return view('livewire.master.history-user');
    }
}