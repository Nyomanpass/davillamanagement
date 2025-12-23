<?php

namespace App\Livewire\Report;

use Livewire\Component;
use App\Models\Pendapatan;
use Carbon\Carbon;
use App\Models\Villa;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class OccupancyReport extends Component
{
    public $filterTahun;
    public $filterBulan;
    public $viewType = 'monthly'; // default bulanan
    public $activeVillaId;
    public $activeVillaName;
    public $listTahun = []; // Tambahkan properti ini

    public function mount()
    {
        $this->filterTahun = date('Y');
        $this->filterBulan = date('m');
       
        $this->activeVillaId = session('villa_id');
        $activeVilla = Villa::find($this->activeVillaId);
        
        if (empty($this->activeVillaId) || !$activeVilla) {
            session()->flash('error', 'Silakan pilih Villa yang ingin dikelola terlebih dahulu.');
            return $this->redirect(route('master.dashboard')); 
        }

        $this->activeVillaName = $activeVilla->nama_villa;

        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $this->listTahun[] = $year;
        }
    }

   public function render()
{
    // Pastikan input adalah integer untuk menghindari error di PHP 8.4
    $tahun = (int) $this->filterTahun;
    $bulan = (int) $this->filterBulan;

    // 1. Logika Perhitungan Berdasarkan Tipe View
    if ($this->viewType === 'monthly') {
        // Gunakan createFromDate agar lebih aman
        $date = Carbon::createFromDate($tahun, $bulan, 1);
        $totalDaysAvailable = $date->daysInMonth; 
        
        $bookings = Pendapatan::where('villa_id', $this->activeVillaId)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out') // Pastikan check_out juga tidak null
            ->whereMonth('check_in', $bulan)
            ->whereYear('check_in', $tahun)
            ->get();
    } else {
        // View Tahunan
        $date = Carbon::createFromDate($tahun, 1, 1);
        $totalDaysAvailable = $date->isLeapYear() ? 366 : 365;
        
        $bookings = Pendapatan::where('villa_id', $this->activeVillaId)
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->whereYear('check_in', $tahun)
            ->get();
    }

    // 2. Hitung Total Malam Terisi
    $totalNightsSold = 0;
    foreach ($bookings as $booking) {
        $in = Carbon::parse($booking->check_in);
        $out = Carbon::parse($booking->check_out);
        
        // diffInDays menghitung selisih malam (24 - 17 = 7 malam)
        $totalNightsSold += $in->diffInDays($out);
    }

    // 3. Persentase Okupansi
    $occupancyRate = $totalDaysAvailable > 0 
        ? ($totalNightsSold / $totalDaysAvailable) * 100 
        : 0;

    return view('livewire.report.occupancy-report', [
        'occupancyRate' => round($occupancyRate, 1),
        'totalNightsSold' => $totalNightsSold,
        'totalDaysAvailable' => $totalDaysAvailable,
        // 'listTahun' tidak perlu di-query lagi karena sudah ada di properti class
    ]);
}
}