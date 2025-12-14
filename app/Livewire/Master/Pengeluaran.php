<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Villa;
use App\Models\Pengeluaran as PengeluaranModel; // Import Model Pengeluaran
use Carbon\Carbon;
use Livewire\WithPagination; 

#[Layout('layouts.app')]
class Pengeluaran extends Component
{
    use WithPagination;
    
    // --- Properti Villa Aktif ---
    public $activeVillaId; 
    public $activeVillaName;

    // --- Properti Form Input (CREATE) ---
    #[Rule('required')] public $jenisPengeluaran = 'gaji'; // Set default pengeluaran
    #[Rule('required|numeric|min:1')] public $nominal;
    public $tanggal; // Akan diset di mount
    public $keterangan; // Tambahan untuk pengeluaran (optional)

    // --- Properti Filter ---
    public $filterBulan = ''; 
    public $filterTahun = '';
    public $filterStartDate = '';
    public $filterEndDate = '';
    public $perPage = 10;
    
    // --- Dropdown ---
    public $listJenisPengeluaran = [
        'gaji' => 'Gaji Karyawan',
        'operasional' => 'Biaya Operasional',
        'marketing' => 'Biaya Marketing',
        'listrik' => 'Biaya Listrik/Air',
        'makanan' => 'Belanja Makanan/Bahan',
        'lainnya' => 'Pengeluaran Lain-Lain',
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
        
        // Populate Tahun Filter
        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $this->listTahun[$year] = $year;
        }

        $this->filterBulan = now()->format('m'); // '01'..'12'
        $this->filterTahun = $currentYear;
        $this->hitungRingkasan();
    }
    
    public function savePengeluaran() // Ubah nama method
    {
        // Validasi
        $this->validate([
            'jenisPengeluaran' => 'required',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255', // Keterangan adalah optional
        ]);

        try {
            PengeluaranModel::create([ // Gunakan Model Pengeluaran
                'villa_id' => $this->activeVillaId,
                'jenis_pengeluaran' => $this->jenisPengeluaran,
                'nominal' => $this->nominal,
                'tanggal' => $this->tanggal,
                'keterangan' => $this->keterangan, // Simpan keterangan
            ]);

            session()->flash('success', 'Data pengeluaran berhasil ditambahkan.');
            
            // Reset form setelah simpan
            $this->reset(['jenisPengeluaran', 'nominal', 'keterangan']); 
            
            // Set ulang default field
            $this->tanggal = Carbon::now()->format('Y-m-d');
            $this->jenisPengeluaran = 'gaji';

            // Muat ulang data ringkasan dan tabel
            $this->hitungRingkasan();
            $this->resetPage();

        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['filterBulan', 'filterTahun', 'filterStartDate', 'filterEndDate'])) {
            $this->resetPage();
        }
    }

    private function hitungRingkasan()
    {
        if (!$this->activeVillaId) { return; }

        $bulanIni = now()->format('m');
        $hariIni = now()->toDateString(); 
        $baseQuery = PengeluaranModel::where('villa_id', $this->activeVillaId); // Gunakan Model Pengeluaran

        $this->ringkasanBulanIni = $baseQuery->clone()->whereMonth('tanggal', $bulanIni)->sum('nominal');
        $this->ringkasanHariIni = $baseQuery->clone()->whereDate('tanggal', $hariIni)->sum('nominal');
        $this->ringkasanAllTime = $baseQuery->clone()->sum('nominal');

        // Hitung Total Berdasarkan Filter
        $this->ringkasanTotalFilter = $this->applyFilter($baseQuery)->sum('nominal');
    }
    
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
        $baseQuery = PengeluaranModel::with('villa') // Gunakan Model Pengeluaran
            ->where('villa_id', $this->activeVillaId);

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

    public function exportExcel()
    {
        if ($this->getFilteredData()->isEmpty()) {
            session()->flash('error', 'Tidak ada data yang sesuai dengan filter untuk diekspor ke Excel.');
            return;
        }

        $params = $this->getExportParams();

        // Redirect ke rute export pengeluaran
        $this->redirect(route('export.pengeluaran.excel', $params)); 
    }


    public function exportPdf()
    {
        // Asumsi: getFilteredData() mengambil data berdasarkan filter Livewire saat ini
        if ($this->getFilteredData()->isEmpty()) {
            session()->flash('error', 'Tidak ada data yang sesuai dengan filter untuk diekspor ke PDF.');
            return;
        }

        $params = $this->getExportParams();
         $this->redirect(route('export.pengeluaran.pdf', $params)); 
    }


    public function render()
    {
        // 1. Dapatkan Base Query
        $pengeluaranQuery = PengeluaranModel::with('villa') // Gunakan Model Pengeluaran
            ->where('villa_id', $this->activeVillaId);

        // 2. Terapkan Filter
        $pengeluaranFiltered = $this->applyFilter($pengeluaranQuery)
            ->latest()
            ->paginate($this->perPage, ['*'], 'pengeluaranPage'); // Ubah nama pagination

        // 3. Update Ringkasan
        $this->hitungRingkasan(); 

        return view('livewire.master.pengeluaran', [
            'dataPengeluaran' => $pengeluaranFiltered, // Ubah nama variabel
        ]);
    }
}