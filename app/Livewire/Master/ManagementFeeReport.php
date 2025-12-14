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

    public function generateReport()
    {
        $this->reports = [];
        $this->totalNetRevenueGlobal = 0;
        $this->totalFeeManagementGlobal = 0;
        $totalSisaKotorGlobal = 0;

        // Tentukan periode
        if ($this->filterMode === 'yearly') {
            $start = Carbon::createFromDate($this->selectedYear, 1, 1)->startOfYear();
            $end = $start->copy()->endOfYear();
        } else {
            if (!$this->selectedMonth) return; // Pastikan ada bulan
            $start = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $end = $start->copy()->endOfMonth();
        }

        // Ambil semua villa
         $queryVillas = Villa::orderBy('nama_villa');
        if ($this->filterVilla) {
            $queryVillas->where('id', $this->filterVilla);
        }
        $villas = $queryVillas->get();

        foreach ($villas as $villa) {
            $totalPendapatan = Pendapatan::where('villa_id', $villa->id)
                ->whereBetween('tanggal', [$start, $end])
                ->sum('nominal');

            $totalPengeluaran = Pengeluaran::where('villa_id', $villa->id)
                ->whereBetween('tanggal', [$start, $end])
                ->sum('nominal');

            $pendapatanBersih = $totalPendapatan - $totalPengeluaran;
            $serviceKaryawanNominal = $pendapatanBersih * ($villa->service_karyawan / 100);
            $sisaKotor = $pendapatanBersih - $serviceKaryawanNominal;
            $feeManajemenNominal = $sisaKotor * ($villa->fee_manajemen / 100);

            $this->reports[] = [
                'id' => $villa->id,
                'name' => $villa->nama_villa,
                'sisa_kotor' => $sisaKotor,
                'fee_percent' => $villa->fee_manajemen,
                'fee_amount' => $feeManajemenNominal,
            ];

            $this->totalNetRevenueGlobal += $sisaKotor;
            $this->totalFeeManagementGlobal += $feeManajemenNominal;
            $totalSisaKotorGlobal += $sisaKotor;
        }

        $this->averageFeePercent = $totalSisaKotorGlobal > 0
            ? number_format(($this->totalFeeManagementGlobal / $totalSisaKotorGlobal) * 100, 1)
            : 0;

        $this->dispatch(
            'report-loaded',
            reports: collect($this->reports)->map(fn ($r) => [
                'name' => $r['name'],
                'fee_amount' => (int) $r['fee_amount'],
            ])->values()
        );

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
