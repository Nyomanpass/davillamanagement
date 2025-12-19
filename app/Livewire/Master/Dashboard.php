<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Villa;
use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $listVilla = [];
    public $villa_id;

    // Ringkasan KPI
    public $totalVilla = 0;
    public $totalPendapatan = 0;
    public $totalPengeluaran = 0;
    public $pendapatanHariIni = 0;
    public $pengeluaranHariIni = 0;
    public $totalTransaksi = 0;


    public $monthlyPendapatan = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    public $monthlyPengeluaran = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

    public $selectedYear;
    public $listTahun = [];

    public function mount()
    {
        // 1. Inisialisasi Filter Tahun (WAJIB TAMBAHAN)
        $this->selectedYear = now()->year;
        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $this->listTahun[$year] = $year;
        }

        // 2. Logika Villa (Kode Anda Sebelumnya)
        $user = Auth::user();
        $this->listVilla = Villa::all();
        
        $sessionId = session('villa_id');

        if ($user && $user->villa_id) {
            $this->villa_id = $user->villa_id;
            session(['villa_id' => $user->villa_id]);
        } 
        elseif (!empty($sessionId)) {
            $this->villa_id = $sessionId;
        } else {
            $this->villa_id = null;
        }

        // 3. Muat Data & Grafik
        $this->loadSummary();
        
        if ($this->villa_id) {
            $this->dispatchChartsData(); 
        }
    }
        
    public function updatedSelectedYear()
    {
        $this->loadSummary();
        $this->dispatchChartsData();
    }

    public function pilihVilla()
    {
        // 1. Simpan pilihan villa ke session
        session(['villa_id' => $this->villa_id]);
        
        // 2. Muat ulang data ringkasan dan KPI
        $this->loadSummary();
        
        // 3. Panggil dispatch saat filter berubah
        $this->dispatchChartsData();
        $this->js('window.location.reload()');
       
        // Ini mengirim event ke komponen ActiveVillaHeader
    $this->dispatch('villaSelected', $this->villa_id);
    }


    public function dispatchChartsData()
{
    $this->dispatch('charts-data-updated', data: [
        'pendapatan' => (float) $this->totalPendapatan,
        'pengeluaran' => (float) $this->totalPengeluaran,
        'lineData' => [
            // Gunakan array_values untuk memastikan formatnya array [0,1,2..] bukan object {0:x, 1:y}
            'pendapatan' => array_values($this->monthlyPendapatan), 
            'pengeluaran' => array_values($this->monthlyPengeluaran),
        ],
    ]);
}

    


    public function loadSummary()
{
    // Gunakan zona waktu Bali untuk filter "Hari Ini"
    $todayBali = now()->timezone('Asia/Makassar')->format('Y-m-d');

    // Query Dasar difilter berdasarkan Tahun yang dipilih
    $pendapatanQuery = Pendapatan::whereYear('tanggal', $this->selectedYear);
    $pengeluaranQuery = Pengeluaran::whereYear('tanggal', $this->selectedYear);
    
    if ($this->villa_id) {
        $pendapatanQuery->where('villa_id', $this->villa_id);
        $pengeluaranQuery->where('villa_id', $this->villa_id);
    }

    // KPI Summary (Tergantung Tahun)
    $this->totalPendapatan = (float) $pendapatanQuery->sum('nominal');
    $this->totalPengeluaran = (float) $pengeluaranQuery->sum('nominal');
    $this->totalTransaksi = $pendapatanQuery->count() + $pengeluaranQuery->count();

    // KPI Harian (Tetap hari ini, tapi difilter Villa)
    $this->pendapatanHariIni = Pendapatan::when($this->villa_id, fn($q) => $q->where('villa_id', $this->villa_id))
        ->whereDate('tanggal', $todayBali)->sum('nominal');
    $this->pengeluaranHariIni = Pengeluaran::when($this->villa_id, fn($q) => $q->where('villa_id', $this->villa_id))
        ->whereDate('tanggal', $todayBali)->sum('nominal');

    // Data Grafik Bulanan (Tergantung Tahun)
    $rawMonthlyPendapatan = Pendapatan::selectRaw('MONTH(tanggal) as bulan, SUM(nominal) as total')
        ->whereYear('tanggal', $this->selectedYear)
        ->when($this->villa_id, fn($q) => $q->where('villa_id', $this->villa_id))
        ->groupBy('bulan')->pluck('total', 'bulan')->toArray();

    $this->monthlyPendapatan = array_map(fn($m) => $rawMonthlyPendapatan[$m] ?? 0, range(1, 12));
    
    $rawMonthlyPengeluaran = Pengeluaran::selectRaw('MONTH(tanggal) as bulan, SUM(nominal) as total')
        ->whereYear('tanggal', $this->selectedYear)
        ->when($this->villa_id, fn($q) => $q->where('villa_id', $this->villa_id))
        ->groupBy('bulan')->pluck('total', 'bulan')->toArray();
    
    $this->monthlyPengeluaran = array_map(fn($m) => $rawMonthlyPengeluaran[$m] ?? 0, range(1, 12));
}

    public function render()
    {
        return view('livewire.master.dashboard');
    }
}