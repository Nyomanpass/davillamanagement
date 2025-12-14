<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Villa;
use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

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

    // Data untuk Grafik (jika data bulanan sudah siap)
    // public $monthlyPendapatan = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    // public $monthlyPengeluaran = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];


  public function mount()
    {
        $this->listVilla = Villa::all();
        $this->villa_id = session('villa_id');

        $this->loadSummary();
        
        // <<< KUNCI PERBAIKAN REFRESH >>>
        // Panggil dispatch untuk mengirim data chart (yang sudah difilter session)
        // segera setelah mount() selesai.
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
        // Ini mengirim event ke komponen ActiveVillaHeader
    $this->dispatch('villaSelected', $this->villa_id);
    }

    public function dispatchChartsData()
    {
        // Mengirim data payload yang diperlukan chart
        $this->dispatch('charts-data-updated', [
            'data' => [
                'pendapatan' => $this->totalPendapatan,
                'pengeluaran' => $this->totalPengeluaran,
                // Tambahkan data line chart jika sudah diimplementasikan
            ]
        ]);
    }

    public function loadSummary()
    {
        // --- Inisialisasi Query Builders ---
        $pendapatanQuery = Pendapatan::query();
        $pengeluaranQuery = Pengeluaran::query();
        $pendapatanHarianQuery = Pendapatan::query();
        $pengeluaranHarianQuery = Pengeluaran::query();
        
        
        if ($this->villa_id) {
            $pendapatanQuery->where('villa_id', $this->villa_id);
            $pengeluaranQuery->where('villa_id', $this->villa_id);
            $pendapatanHarianQuery->where('villa_id', $this->villa_id);
            $pengeluaranHarianQuery->where('villa_id', $this->villa_id);
        }

        // --- Ambil Data ---
        $this->totalVilla = Villa::count();
        $this->totalPendapatan = $pendapatanQuery->sum('nominal');
        $this->totalPengeluaran = $pengeluaranQuery->sum('nominal');
        
        // Hari Ini
        $this->pendapatanHariIni = $pendapatanHarianQuery->whereDate('tanggal', now())->sum('nominal');
        $this->pengeluaranHariIni = $pengeluaranHarianQuery->whereDate('tanggal', now())->sum('nominal');
        
        // Transaksi total
        $this->totalTransaksi = $pendapatanQuery->count() + $pengeluaranQuery->count();

        // Jika Anda mengambil data bulanan, implementasi logikanya di sini
    }

    public function render()
    {
        return view('livewire.master.dashboard');
    }
}