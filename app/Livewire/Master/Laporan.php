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

        $villa = Villa::findOrFail($this->activeVillaId);
        $this->villaDetail = $villa;

       

        // Mode TAHUNAN
        if ($this->filterMode === 'yearly') {
            $startOfPeriod = Carbon::createFromDate($this->selectedYear, 1, 1)->startOfYear();
            $endOfPeriod = $startOfPeriod->copy()->endOfYear();
            $periodeFormat = 'Y'; // Format hanya tahun
        } 
        // Mode BULANAN (Default/Lama)
        else { 
            // Pastikan bulan terisi di mode bulanan
            if (!$this->selectedMonth) {
                $this->reportData = [];
                return;
            }
            $startOfPeriod = Carbon::createFromDate($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
            $endOfPeriod = $startOfPeriod->copy()->endOfMonth();
            $periodeFormat = 'F Y'; // Format Bulan dan Tahun
        }


        // 2. Query Pendapatan dan Pengeluaran (Menggunakan activeVillaId)
        $totalPendapatan = Pendapatan::where('villa_id', $this->activeVillaId)
            ->whereBetween('tanggal', [$startOfPeriod, $endOfPeriod])
            ->sum('nominal');

        $totalPengeluaran = Pengeluaran::where('villa_id', $this->activeVillaId)
            ->whereBetween('tanggal', [$startOfPeriod, $endOfPeriod])
            ->sum('nominal');

        // 3. Lakukan Perhitungan Bisnis (Logika perhitungan tetap sama)
        // ... (Logika perhitungan tetap sama) ...
        $pendapatanBersih = $totalPendapatan - $totalPengeluaran;

        $serviceKaryawanPercentage = $villa->service_karyawan / 100;
        $serviceKaryawanNominal = $pendapatanBersih * $serviceKaryawanPercentage;

        $pendapatanKotor = $pendapatanBersih - $serviceKaryawanNominal;

        $feeManajemenPercentage = $villa->fee_manajemen / 100;
        $feeManajemenNominal = $pendapatanKotor * $feeManajemenPercentage;

        $pendapatanOwner = $pendapatanKotor - $feeManajemenNominal;
        // --- Akhir Logika Perhitungan ---

        // Simpan Hasil
        $this->reportData = [
            // ... (data hasil perhitungan tetap sama) ...
            'totalPendapatan' => $totalPendapatan,
            'totalPengeluaran' => $totalPengeluaran,
            'pendapatanBersih' => $pendapatanBersih,
            'serviceKaryawanNominal' => $serviceKaryawanNominal,
            'serviceKaryawanPercentage' => $villa->service_karyawan,
            'pendapatanKotor' => $pendapatanKotor,
            'feeManajemenNominal' => $feeManajemenNominal,
            'feeManajemenPercentage' => $villa->fee_manajemen,
            'pendapatanOwner' => $pendapatanOwner,
            // *** Perubahan di sini untuk periode ***
            'periode' => $startOfPeriod->translatedFormat($periodeFormat),
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