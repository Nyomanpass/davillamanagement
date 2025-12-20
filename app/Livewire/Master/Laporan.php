<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Villa;
use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

#[Layout('layouts.app')]
class Laporan extends Component
{
    // --- Properti Villa Aktif (Diambil dari Session/Mount) ---
    public $filterMode = 'monthly';
    public $activeVillaId; 
    public $activeVillaName;

    // --- Properti Filter ---
    public $selectedMonth;
    public $selectedYear;
    public $listTahun = []; // Diganti menjadi listTahun (years)
    

    // --- Properti Hasil Perhitungan ---
    public $reportData = [];
    public $villaDetail = null;

    public function mount()
    {
        // 1. Ambil Villa Aktif dari Session
        $this->activeVillaId = session('villa_id');
        $activeVilla = Villa::find($this->activeVillaId);
        
        if (empty($this->activeVillaId) || !$activeVilla) {
            session()->flash('error', 'Silakan pilih Villa yang ingin dikelola terlebih dahulu.');
            // Menggunakan redirect default jika tidak ada route dashboard yang didefinisikan
            return $this->redirect('/master/dashboard', navigate: true); 
        }

        $this->activeVillaName = $activeVilla->nama_villa;
        
        // 2. Atur filter default (Bulan/Tahun)
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;

        // 3. Populate Tahun Filter (5 tahun ke belakang)
        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $this->listTahun[$year] = $year;
        }

        // 4. Hitung laporan pertama kali
        $this->generateReport();
    }

        
        public function updatedFilterMode($value)
        {
            if ($value === 'yearly') {
                // Mode tahunan → bulan tidak dipakai
                $this->selectedMonth = null;
            }

            if ($value === 'monthly') {
                // BALIK KE BULAN SEKARANG
                $this->selectedMonth = now()->month;
            }

            $this->generateReport();
        }

        public function updatedSelectedMonth()
        {
            if ($this->filterMode === 'monthly') {
                $this->generateReport();
            }
        }

        public function updatedSelectedYear()
        {
            $this->generateReport();
        }

  

    public function generateReport()
{
    if (!$this->activeVillaId || !$this->selectedYear) {
        $this->reportData = [];
        return;
    }

    // Load villa beserta kategori khusus yang sudah dicentang di setting
    $villa = Villa::with('specialCategories')->findOrFail($this->activeVillaId);
    $this->villaDetail = $villa;

    // Ambil daftar ID kategori khusus untuk villa ini
    $specialCategoryIds = $villa->specialCategories->pluck('id')->toArray();

    // 1. Tentukan Periode Utama
    if ($this->filterMode === 'yearly') {
        $monthsToCalculate = range(1, 12);
        $periodeFormat = 'Y';
        $startOfPeriod = Carbon::createFromDate($this->selectedYear, 1, 1)->startOfYear();
    } else {
        if (!$this->selectedMonth) { $this->reportData = []; return; }
        $monthsToCalculate = [(int)$this->selectedMonth];
        $periodeFormat = 'F Y';
        $startOfPeriod = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
    }

    $totals = [
        'pendapatanKhusus' => 0, 'pengeluaranKhusus' => 0,
        'pendapatanUmum' => 0, 'pengeluaranUmum' => 0,
        'serviceKaryawanNominal' => 0, 'feeManajemenNominal' => 0,
        'pendapatanKotor' => 0, 'pendapatanOwner' => 0
    ];

    foreach ($monthsToCalculate as $month) {
        $currentStart = Carbon::createFromDate($this->selectedYear, $month, 1)->startOfMonth();
        $currentEnd = $currentStart->copy()->endOfMonth();

        // Ambil History Fee
        $history = \App\Models\VillaFeeHistory::where('villa_id', $this->activeVillaId)
            ->where('mulai_berlaku', '<=', $currentStart->format('Y-m-d'))
            ->orderBy('mulai_berlaku', 'desc')
            ->first();

        $feeService = $history ? $history->service_karyawan : $villa->service_karyawan;
        $feeManaj = $history ? $history->fee_manajemen : $villa->fee_manajemen;

        // --- QUERY PENDAPATAN & PENGELUARAN DINAMIS ---
        
        // Data Khusus (Berdasarkan Kategori yang dicentang di Setting)
        $pKhusus = Pendapatan::where('villa_id', $this->activeVillaId)
            ->whereBetween('tanggal', [$currentStart, $currentEnd])
            ->whereIn('category_id', $specialCategoryIds) // Menggunakan ID hasil centang
            ->sum('nominal');

        $exKhusus = Pengeluaran::where('villa_id', $this->activeVillaId)
            ->whereBetween('tanggal', [$currentStart, $currentEnd])
            ->whereIn('category_id', $specialCategoryIds)
            ->sum('nominal');

        // Data Umum (Semua yang TIDAK dicentang di Setting)
        $pUmum = Pendapatan::where('villa_id', $this->activeVillaId)
            ->whereBetween('tanggal', [$currentStart, $currentEnd])
            ->whereNotIn('category_id', $specialCategoryIds)
            ->sum('nominal');

        $exUmum = Pengeluaran::where('villa_id', $this->activeVillaId)
            ->whereBetween('tanggal', [$currentStart, $currentEnd])
            ->whereNotIn('category_id', $specialCategoryIds)
            ->sum('nominal');

        // Logika Hitung (Sama seperti sebelumnya)
        $mKhusus = $pKhusus - $exKhusus;
        $sNominal = $mKhusus > 0 ? $mKhusus * ($feeService / 100) : 0;
        $pKotor = ($mKhusus - $sNominal) + ($pUmum - $exUmum);
        $fNominal = $pKotor * ($feeManaj / 100);

        // Akumulasi Totals
        $totals['pendapatanKhusus'] += $pKhusus;
        $totals['pengeluaranKhusus'] += $exKhusus;
        $totals['pendapatanUmum'] += $pUmum;
        $totals['pengeluaranUmum'] += $exUmum;
        $totals['serviceKaryawanNominal'] += $sNominal;
        $totals['feeManajemenNominal'] += $fNominal;
        $totals['pendapatanKotor'] += $pKotor;
        $totals['pendapatanOwner'] += ($pKotor - $fNominal);
    }

    $this->reportData = [
        'totalPendapatan'           => $totals['pendapatanKhusus'] + $totals['pendapatanUmum'],
        'totalPengeluaran'          => $totals['pengeluaranKhusus'] + $totals['pengeluaranUmum'],
        'pendapatanKhusus'          => $totals['pendapatanKhusus'],
        'pengeluaranKhusus'         => $totals['pengeluaranKhusus'],
        'marginKhusus'              => $totals['pendapatanKhusus'] - $totals['pengeluaranKhusus'],
        'serviceKaryawanNominal'    => $totals['serviceKaryawanNominal'],
        'serviceKaryawanPercentage' => $this->filterMode === 'monthly' ? $feeService : 'Mixed',
        'pendapatanUmum'            => $totals['pendapatanUmum'],
        'pengeluaranUmum'           => $totals['pengeluaranUmum'],
        'pendapatanKotor'           => $totals['pendapatanKotor'],
        'feeManajemenNominal'       => $totals['feeManajemenNominal'],
        'feeManajemenPercentage'    => $this->filterMode === 'monthly' ? $feeManaj : 'Mixed',
        'pendapatanOwner'           => $totals['pendapatanOwner'],
        'periode'                   => $startOfPeriod->translatedFormat($periodeFormat),
    ];

    $this->dispatch('report-data-updated', data: $this->reportData);
}

    public function exportExcel()
    {
        return redirect()->route('laporan.excel', [
            'villa_id' => $this->activeVillaId,
            'bulan' => $this->selectedMonth,
            'tahun' => $this->selectedYear,
        ]);
    }

    public function exportPdf()
    {
        return redirect()->route('laporan.pdf', [
            'villa_id' => $this->activeVillaId,
            'bulan' => $this->filterMode === 'monthly' ? $this->selectedMonth : null,
            'tahun' => $this->selectedYear,
        ]);
    }

    

    public function render()
    {
        // Daftar Bulan (tetap sama)
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::createFromDate(null, $i, 1)->format('F');
        }

        // listTahun sudah di-populate di mount()
        $years = $this->listTahun;

        return view('livewire.master.laporan', [
            'months' => $months,
            'years' => $years,
        ]);
    }
}