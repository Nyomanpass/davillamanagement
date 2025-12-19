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
    public $isEditMode = false; // Flag untuk tahu apakah sedang mode Edit
    public $pendapatanId = null; // ID data yang sedang diedit

    
    // --- Properti Form Input (CREATE) ---
    // ... (Properti jenisPendapatan, nominal, tanggal, metodePembayaran tetap sama) ...
    public $category_id;
    public $is_room = false;

    // Field Room
    public $check_in, $check_out, $nights = 1, $price_per_night = 0;

    // Field Item Umum
    public $item_name, $qty = 1, $price_per_item = 0;

    public $keterangan;
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

    public $filterCategory = ''; 
    public $ringkasanTotalPerKategori = 0;

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

    public function updatedCategoryId($value)
    {
        $category = \App\Models\Category::find($value);
        if ($category) {
            $this->is_room = str_contains(strtolower($category->name), 'room');
        } else {
            $this->is_room = false;
        }
        $this->hitungNominal();
    }



    public function resetForm()
    {
        // 1. Reset field utama dan flag mode edit
        $this->reset([
            'category_id', 
            'nominal', 
            'pendapatanId', 
            'isEditMode', 
            'keterangan',
            'is_room'
        ]);

        // 2. Reset field khusus Room
        $this->reset([
            'check_in', 
            'check_out', 
            'nights', 
            'price_per_night'
        ]);

        // 3. Reset field khusus Item/Layanan
        $this->reset([
            'item_name', 
            'qty', 
            'price_per_item'
        ]);

        // 4. Set nilai default untuk field yang tidak boleh kosong
        $this->tanggal = now()->format('Y-m-d');
        $this->metodePembayaran = 'transfer';
        $this->nights = 1;
        $this->qty = 1;
        $this->nominal = 0;
    }
        

    public function savePendapatan()
    {
        $this->validate([
            'category_id' => 'required',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'metodePembayaran' => 'required',
        ]);

        $data = [
            'villa_id'          => $this->activeVillaId,
            'category_id'       => $this->category_id,
            'nominal'           => $this->nominal,
            'tanggal'           => $this->tanggal,
            'metode_pembayaran' => $this->metodePembayaran,
            'keterangan'        => $this->keterangan,
            // Data Room
            'check_in'          => $this->is_room ? $this->check_in : null,
            'check_out'         => $this->is_room ? $this->check_out : null,
            'nights'            => $this->is_room ? $this->nights : null,
            'price_per_night'   => $this->is_room ? $this->price_per_night : null,
            // Data Item
            'item_name'         => !$this->is_room ? $this->item_name : null,
            'qty'               => !$this->is_room ? $this->qty : null,
            'price_per_item'    => !$this->is_room ? $this->price_per_item : null,
        ];

        try {
            if ($this->isEditMode && $this->pendapatanId) {
                PendapatanModel::findOrFail($this->pendapatanId)->update($data);
                session()->flash('success', 'Data diperbarui.');
            } else {
                PendapatanModel::create($data);
                session()->flash('success', 'Data ditambahkan.');
            }
            $this->resetForm();
            $this->hitungRingkasan();
            $this->resetPage(); // Reset pagination
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function edit($id)
{
    // 1. Ambil data dari database
    $pendapatan = PendapatanModel::findOrFail($id);
    
    // 2. Set Properti Utama
    $this->pendapatanId = $pendapatan->id;
    $this->category_id = $pendapatan->category_id; // Menggantikan jenisPendapatan
    $this->nominal = $pendapatan->nominal;
    $this->tanggal = Carbon::parse($pendapatan->tanggal)->format('Y-m-d');
    $this->metodePembayaran = $pendapatan->metode_pembayaran;
    $this->keterangan = $pendapatan->keterangan;

    // 3. Trigger Deteksi is_room
    // Kita panggil fungsi ini agar UI form berubah (jadi mode Room atau Item)
    $this->updatedCategoryId($this->category_id);

    // 4. Set Properti Detail (Field baru)
    if ($this->is_room) {
        // Jika data adalah Room
        $this->check_in = $pendapatan->check_in ? Carbon::parse($pendapatan->check_in)->format('Y-m-d') : null;
        $this->check_out = $pendapatan->check_out ? Carbon::parse($pendapatan->check_out)->format('Y-m-d') : null;
        $this->nights = $pendapatan->nights;
        $this->price_per_night = $pendapatan->price_per_night;
    } else {
        // Jika data adalah Item Umum (Laundry, dll)
        $this->item_name = $pendapatan->item_name;
        $this->qty = $pendapatan->qty;
        $this->price_per_item = $pendapatan->price_per_item;
    }
    
    $this->isEditMode = true;

    // 5. Scroll ke atas
    $this->js('window.scrollTo({top: 0, behavior: "smooth"})');
}

      public function resetFilter()
    {
        $this->reset(['filterBulan', 'filterTahun', 'filterStartDate', 'filterEndDate', 'filterCategory']);

        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $this->listTahun[$year] = $year;
        }

        $this->filterBulan = now()->format('m'); // '01'..'12'
        $this->filterTahun = $currentYear;

        $this->hitungRingkasan(); 
        $this->resetPage(); 
    }



    public function delete($id)
    {
       
        try {
            PendapatanModel::findOrFail($id)->delete();
            session()->flash('success', 'Data pendapatan berhasil dihapus.');
            $this->hitungRingkasan();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    private function hitungNominal()
    {
        if ($this->is_room) {
            $this->nominal = (int)$this->nights * (float)$this->price_per_night;
        } else {
            $this->nominal = (int)$this->qty * (float)$this->price_per_item;
        }
    }

    

    // BARU: Reset pagination saat filter diubah
    public function updated($propertyName)
    {
        // Reset pagination jika filter berubah (kode lama Anda)
        if (in_array($propertyName, ['filterBulan', 'filterTahun', 'filterStartDate', 'filterEndDate', 'filterCategory'])) {
            $this->resetPage();
            $this->hitungRingkasan(); // <--- Sangat Penting!
        }
        
        // Hitung nominal otomatis
        $this->hitungNominal();
    }



private function hitungRingkasan()
{
    if (!$this->activeVillaId) { return; }

    $bulanIni = now()->format('m');
    $tahunIni = now()->year;
    $hariIni = now()->toDateString(); 
    
    // Base query agar kita tidak menulis ulang where villa_id
    $baseQuery = PendapatanModel::where('villa_id', $this->activeVillaId);

    // 1. Ringkasan Statis (Tetap, tidak terpengaruh filter)
    $this->ringkasanBulanIni = $baseQuery->clone()
        ->whereMonth('tanggal', $bulanIni)
        ->whereYear('tanggal', $tahunIni) // Tambahkan tahun agar tidak menjumlahkan bulan sama di tahun lalu
        ->sum('nominal');

    $this->ringkasanHariIni = $baseQuery->clone()
        ->whereDate('tanggal', $hariIni)
        ->sum('nominal');

    $this->ringkasanAllTime = $baseQuery->clone()
        ->sum('nominal');

    // 2. Ringkasan Dinamis (Mengikuti Filter: Bulan, Tahun, Tanggal, DAN Kategori)
    // Pastikan selalu gunakan clone() sebelum applyFilter agar baseQuery tetap bersih
    $this->ringkasanTotalFilter = $this->applyFilter($baseQuery->clone())->sum('nominal');
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

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
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
            'villa_id'   => $this->activeVillaId,
            'bulan'      => $this->filterBulan,
            'tahun'      => $this->filterTahun,
            'start'      => $this->filterStartDate,
            'end'        => $this->filterEndDate,
            'category'   => $this->filterCategory, // Tambahkan ini
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
        $pendapatanQuery = PendapatanModel::with(['villa', 'category'])
            ->where('villa_id', $this->activeVillaId);

        return view('livewire.master.pendapatan', [
            'dataPendapatan' => $this->applyFilter($pendapatanQuery)->latest()->paginate($this->perPage),
            'categories' => \App\Models\Category::where('type', 'income')->get(), // Tambahkan ini
        ]);
    }

}

