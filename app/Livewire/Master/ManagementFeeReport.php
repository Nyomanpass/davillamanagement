<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\Villa;
use App\Models\Pendapatan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ManagementFeeReport extends Component
{
    // --- Filter ---
    public $filterMode = 'monthly'; // 'monthly' atau 'yearly'
    public $filterVilla = '';
    public $selectedMonth;
    public $selectedYear;
    public $listTahun = [];
    public $listVillas;

    // --- Hasil ---
    public $reports = [];
    public $totalNetRevenueGlobal = 0;
    public $totalFeeManagementGlobal = 0;
    public $averageFeePercent = 0;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;

        $this->listVillas = Villa::orderBy('nama_villa')->get();

        // Populate tahun terakhir 5 tahun
        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $this->listTahun[$year] = $year;
        }

        $this->generateReport();
    }

    public function updatedFilterMode($value)
    {
        if ($value === 'yearly') {
            $this->selectedMonth = null;
        } else {
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

    public function updatedFilterVilla()
    {
        $this->generateReport();
    }

    private function getFeeAtDate($villaId, $date)
    {
        // Mencari riwayat fee yang mulai_berlaku <= tanggal laporan
        $history = \App\Models\VillaFeeHistory::where('villa_id', $villaId)
            ->where('mulai_berlaku', '<=', $date)
            ->orderBy('mulai_berlaku', 'desc') // Ambil yang paling terbaru dari tanggal tersebut
            ->first();

        if ($history) {
            return [
                'service' => $history->service_karyawan,
                'manajemen' => $history->fee_manajemen
            ];
        }

        // Jika tidak ada riwayat di tabel history, gunakan data default dari tabel villas
        $villa = \App\Models\Villa::find($villaId);
        return [
            'service' => $villa->service_karyawan,
            'manajemen' => $villa->fee_manajemen
        ];
    }

    public function generateReport()
{
    $this->reports = [];
    $this->totalNetRevenueGlobal = 0;
    $this->totalFeeManagementGlobal = 0;

    // 1. Tentukan daftar bulan yang akan diproses
    if ($this->filterMode === 'yearly') {
        $monthsToProcess = range(1, 12);
    } else {
        if (!$this->selectedMonth) return;
        $monthsToProcess = [(int)$this->selectedMonth];
    }

    // Load Villas beserta Special Categories (Pivot)
    $queryVillas = Villa::with('specialCategories')->orderBy('nama_villa');
    if ($this->filterVilla) {
        $queryVillas->where('id', $this->filterVilla);
    }
    $villas = $queryVillas->get();

    foreach ($villas as $villa) {
        // Ambil ID kategori khusus yang sudah di-setting untuk villa ini
        $specialCategoryIds = $villa->specialCategories->pluck('id')->toArray();

        // Penampung total akumulasi villa
        $villaTotalLabaKotor = 0;
        $villaTotalFeeAmount = 0;
        $villaTotalServiceAmount = 0;
        $lastFeeUsed = 0;

        foreach ($monthsToProcess as $month) {
            $currentStart = Carbon::createFromDate($this->selectedYear, $month, 1)->startOfMonth();
            $currentEnd = $currentStart->copy()->endOfMonth();

            // 2. Ambil Fee History
            $fees = $this->getFeeAtDate($villa->id, $currentStart->format('Y-m-d'));
            $feeManajPercent = $fees['manajemen'];
            $feeServicePercent = $fees['service'];
            $lastFeeUsed = $feeManajPercent; 

            // 3. Query Data Per Bulan (Dinamis berdasarkan Category ID)
            
            // Pendapatan & Pengeluaran KHUSUS (Kategori yang dicentang)
            $pKhusus = Pendapatan::where('villa_id', $villa->id)
                ->whereBetween('tanggal', [$currentStart, $currentEnd])
                ->whereIn('category_id', $specialCategoryIds)
                ->sum('nominal');

            $exKhusus = Pengeluaran::where('villa_id', $villa->id)
                ->whereBetween('tanggal', [$currentStart, $currentEnd])
                ->whereIn('category_id', $specialCategoryIds)
                ->sum('nominal');

            // Pendapatan & Pengeluaran UMUM (Kategori yang TIDAK dicentang)
            $pUmum = Pendapatan::where('villa_id', $villa->id)
                ->whereBetween('tanggal', [$currentStart, $currentEnd])
                ->whereNotIn('category_id', $specialCategoryIds)
                ->sum('nominal');

            $exUmum = Pengeluaran::where('villa_id', $villa->id)
                ->whereBetween('tanggal', [$currentStart, $currentEnd])
                ->whereNotIn('category_id', $specialCategoryIds)
                ->sum('nominal');

            // 4. Hitung Service & Laba
            $marginKhusus = $pKhusus - $exKhusus;
            $sNominal = $marginKhusus > 0 ? $marginKhusus * ($feeServicePercent / 100) : 0;
            $lKotor = ($marginKhusus - $sNominal) + ($pUmum - $exUmum);
            $fNominal = $lKotor * ($feeManajPercent / 100);

            // 5. Akumulasi
            $villaTotalLabaKotor += $lKotor;
            $villaTotalFeeAmount += $fNominal;
            $villaTotalServiceAmount += $sNominal;
        }

        // 6. Masukkan hasil ke array reports
        $this->reports[] = [
            'id' => $villa->id,
            'name' => $villa->nama_villa,
            'laba_kotor' => $villaTotalLabaKotor,
            'fee_percent' => $this->filterMode === 'monthly' ? $lastFeeUsed : 'Mixed',
            'fee_amount' => $villaTotalFeeAmount,
            'service_amount' => $villaTotalServiceAmount,
        ];

        $this->totalNetRevenueGlobal += $villaTotalLabaKotor;
        $this->totalFeeManagementGlobal += $villaTotalFeeAmount;
    }

    $this->averageFeePercent = $this->totalNetRevenueGlobal > 0
        ? number_format(($this->totalFeeManagementGlobal / $this->totalNetRevenueGlobal) * 100, 1)
        : 0;

    $this->dispatch('report-loaded', reports: collect($this->reports)->map(fn ($r) => [
        'name' => $r['name'],
        'fee_amount' => (int) $r['fee_amount'],
    ])->values());
}

    public function render()
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->format('F');
        }

        return view('livewire.master.management-fee-report', [
            'months' => $months,
            'years' => $this->listTahun,
            'listVillas' => $this->listVillas,
        ]);
    }
}
