<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Villa;
use App\Models\Pendapatan as PendapatanModel;
use Illuminate\Support\Facades\Response; 
use Illuminate\Database\Eloquent\Builder; 
use Carbon\Carbon;
use Livewire\WithPagination; 

#[Layout('layouts.app')]
class Pendapatan extends Component
{
    use WithPagination;
    
    // --- Properti Villa Aktif ---
    public $activeVillaId; 
    public $activeVillaName;

    // --- Properti Form Input (CREATE) ---
    // ... (Properti jenisPendapatan, nominal, tanggal, metodePembayaran tetap sama) ...
    #[Rule('required')] public $jenisPendapatan = 'sewa'; // Set default yang valid
    #[Rule('required|numeric|min:1')] public $nominal;
    public $tanggal; // Akan diset di mount
    #[Rule('required')] public $metodePembayaran = 'transfer'; // Set default yang valid

    // --- Properti Filter (BARU) ---
    public $filterBulan = ''; // Contoh: '01', '02', 'all'
    public $filterTahun = '';
    public $filterStartDate = '';
    public $filterEndDate = '';
    public $perPage = 10; // Untuk pagination
    
    // --- Dropdown ---
    public $listJenisPendapatan = [
        'sewa' => 'Sewa Villa',
        'makanan' => 'Penjualan Makanan',
        'minuman' => 'Penjualan Minuman',
        'laundry' => 'Layanan Laundry',
        'transportasi' => 'Layanan Transportasi',
        'tour' => 'Layanan Tour / Aktivitas',
        'extra_bed' => 'Sewa Extra Bed',
        'deposit_refund' => 'Deposit yang Tidak Dikembalikan',
        'komisi' => 'Komisi (Affiliate/Agent)',
        'lainnya' => 'Pendapatan Lain-Lain',
    ];
    public $listMetodePembayaran = [
        'transfer' => 'Transfer Bank',
        'cash' => 'Tunai (Cash)',
    ];
    public $listTahun = []; 
    
    // --- Ringkasan ---
    public $ringkasanBulanIni = 0;
    public $ringkasanHariIni = 0;
    public $ringkasanAllTime = 0;
    public $ringkasanTotalFilter = 0; 

    public function mount()
    {
        // ... (Logika Cek Villa Aktif tetap sama) ...
        $this->activeVillaId = session('villa_id');
        $activeVilla = Villa::find($this->activeVillaId);
        
        if (empty($this->activeVillaId) || !$activeVilla) {
            session()->flash('error', 'Silakan pilih Villa yang ingin dikelola terlebih dahulu.');
            return $this->redirect(route('master.dashboard')); 
        }

        $this->activeVillaName = $activeVilla->nama_villa;
        $this->tanggal = now()->format('Y-m-d');
        
        // Populate Tahun Filter (Contoh: 5 tahun ke belakang)
        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $this->listTahun[$year] = $year;
        }

         $this->filterBulan = now()->format('m'); // '01'..'12'
         $this->filterTahun = $currentYear;

        $this->hitungRingkasan();
    }
    
    public function savePendapatan()
    {
        // Pastikan Anda memvalidasi semua field
        $this->validate([
            'jenisPendapatan' => 'required',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'metodePembayaran' => 'required',
        ]);

        try {
            PendapatanModel::create([
                'villa_id' => $this->activeVillaId,
                'jenis_pendapatan' => $this->jenisPendapatan,
                'nominal' => $this->nominal,
                'tanggal' => $this->tanggal,
                'metode_pembayaran' => $this->metodePembayaran,
            ]);

            session()->flash('success', 'Data pendapatan berhasil ditambahkan.');
            
            // Reset form setelah simpan
            $this->reset(['jenisPendapatan', 'nominal']); // Reset field yang diinput user
            
            // Set ulang default field
            $this->tanggal = Carbon::now()->format('Y-m-d');
            $this->metodePembayaran = 'cash';

            // Muat ulang data ringkasan dan tabel
            $this->hitungRingkasan();
            $this->resetPage(); // Reset pagination agar data baru muncul di halaman 1

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // BARU: Reset pagination saat filter diubah
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filterBulan', 'filterTahun', 'filterStartDate', 'filterEndDate'])) {
            $this->resetPage();
        }
    }

    private function hitungRingkasan()
    {
        // ... (Logika ringkasan Bulan Ini, Bulan Lalu, All Time tetap sama) ...
        if (!$this->activeVillaId) { return; }

        $bulanIni = now()->format('m');
        $hariIni = now()->toDateString(); 
        $baseQuery = PendapatanModel::where('villa_id', $this->activeVillaId);

        $this->ringkasanBulanIni = $baseQuery->clone()->whereMonth('tanggal', $bulanIni)->sum('nominal');
        $this->ringkasanHariIni = $baseQuery->clone()->whereDate('tanggal', $hariIni)->sum('nominal');
        $this->ringkasanAllTime = $baseQuery->clone()->sum('nominal');

        // BARU: Hitung Total Berdasarkan Filter
        $this->ringkasanTotalFilter = $this->applyFilter($baseQuery)->sum('nominal');
    }
    
    // BARU: Method untuk menerapkan semua filter ke query
    private function applyFilter($query)
    {
        if ($this->filterBulan) {
            $query->whereMonth('tanggal', $this->filterBulan);
        }

        if ($this->filterTahun) {
            $query->whereYear('tanggal', $this->filterTahun);
        }
        
        if ($this->filterStartDate) {
            $query->whereDate('tanggal', '>=', $this->filterStartDate);
        }
        
        if ($this->filterEndDate) {
            $query->whereDate('tanggal', '<=', $this->filterEndDate);
        }

        return $query;
    }

    private function getFilteredData()
    {
        $baseQuery = PendapatanModel::with('villa')
            ->where('villa_id', $this->activeVillaId);

        // Panggil applyFilter untuk mendapatkan query yang sudah difilter
        return $this->applyFilter($baseQuery)->latest()->get();
    }

    private function getExportParams()
    {
        return [
            'villa_id' => $this->activeVillaId,
            'bulan' => $this->filterBulan,
            'tahun' => $this->filterTahun,
            'start' => $this->filterStartDate,
            'end' => $this->filterEndDate,
        ];
    }

    public function exportPdf()
    {
        // Pengecekan data kosong
        if ($this->getFilteredData()->isEmpty()) {
            session()->flash('error', 'Tidak ada data yang sesuai dengan filter untuk diekspor ke PDF.');
            return;
        }

        $params = $this->getExportParams();

        // HAPUS navigate: true AGAR BROWSER MENDOWNLOAD FILE SECARA TRADISIONAL
        $this->redirect(route('export.pendapatan.pdf', $params)); 
    }

    public function exportExcel()
    {
        // Pengecekan data kosong
        if ($this->getFilteredData()->isEmpty()) {
            session()->flash('error', 'Tidak ada data yang sesuai dengan filter untuk diekspor ke Excel.');
            return;
        }

        $params = $this->getExportParams();

        // HAPUS navigate: true
        $this->redirect(route('export.pendapatan.excel', $params)); 
    }

    public function render()
    {
        // 1. Dapatkan Base Query
        $pendapatanQuery = PendapatanModel::with('villa')
            ->where('villa_id', $this->activeVillaId);

        // 2. Terapkan Filter
        $pendapatanFiltered = $this->applyFilter($pendapatanQuery)
            ->latest()
            ->paginate($this->perPage); // <-- Gunakan pagination

        // 3. Pastikan ringkasanTotalFilter di-update setiap kali render (jika ada perubahan filter)
        // Kita panggil ulang hitungRingkasan() agar ringkasanTotalFilter selalu fresh
        $this->hitungRingkasan(); 

        return view('livewire.master.pendapatan', [
            'dataPendapatan' => $pendapatanFiltered, // Mengganti dataPendapatanTerbaru
        ]);
    }
}

