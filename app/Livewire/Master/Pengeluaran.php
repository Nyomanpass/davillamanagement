<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use App\Models\Villa;
use App\Models\Category; // Import Model Category
use App\Models\Pengeluaran as PengeluaranModel;
use Carbon\Carbon;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Pengeluaran extends Component
{
    use WithPagination;
    
    // --- Properti Villa & State ---
    public $activeVillaId; 
    public $activeVillaName;
    public $pengeluaranId = null; 
    public $isEditMode = false;

    // --- Properti Form Input (Sesuai Migrasi Baru) ---
    public $category_id;
    public $nama_pengeluaran;
    public $qty = 1;
    public $satuan = 'Pcs';
    public $harga_satuan = 0;
    public $nominal = 0; // Total (Qty * Harga Satuan)
    public $tanggal;
    public $metode_pembayaran = 'cash';
    public $keterangan;

    // --- Properti Filter ---
    public $filterBulan = ''; 
    public $filterTahun = '';
    public $filterStartDate = '';
    public $filterEndDate = '';
    public $filterCategory = ''; // Filter Kategori Baru
    public $perPage = 10;
    
    public $listTahun = []; 
    public $listMetodePembayaran = [
        'cash' => 'Tunai (Cash)',
        'transfer' => 'Transfer Bank',
        'petty_cash' => 'Petty Cash',
    ];

    public $ringkasanBulanIni = 0;
    public $ringkasanHariIni = 0;
    public $ringkasanAllTime = 0;
    public $ringkasanTotalFilter = 0; 

    public function mount()
    {
        $this->activeVillaId = session('villa_id');
        $activeVilla = Villa::find($this->activeVillaId);
        
        if (empty($this->activeVillaId) || !$activeVilla) {
            session()->flash('error', 'Silakan pilih Villa terlebih dahulu.');
            return $this->redirect(route('master.dashboard')); 
        }

        $this->activeVillaName = $activeVilla->nama_villa;
        $this->tanggal = now()->format('Y-m-d');
        
        $currentYear = now()->year;
        for ($i = 0; $i < 5; $i++) {
            $year = $currentYear - $i;
            $this->listTahun[$year] = $year;
        }

        $this->filterBulan = now()->format('m');
        $this->filterTahun = $currentYear;
        $this->hitungRingkasan();
    }

    // --- Hook Updated untuk Auto Calculate & Filter ---
    public function updated($propertyName)
    {
        // Hitung Otomatis Nominal: Qty * Harga Satuan
        if (in_array($propertyName, ['qty', 'harga_satuan'])) {
            $this->nominal = (float)$this->qty * (float)$this->harga_satuan;
        }

        // Reset Page jika filter berubah
        if (in_array($propertyName, ['filterBulan', 'filterTahun', 'filterStartDate', 'filterEndDate', 'filterCategory'])) {
            $this->resetPage();
            $this->hitungRingkasan();
        }
    }

    public function resetForm()
    {
        $this->reset([
            'category_id', 'nama_pengeluaran', 'qty', 'satuan', 
            'harga_satuan', 'nominal', 'keterangan', 'pengeluaranId', 'isEditMode'
        ]); 
        $this->tanggal = Carbon::now()->format('Y-m-d');
        $this->qty = 1;
        $this->metode_pembayaran = 'cash';
    }

    public function resetFilter()
    {
        $this->reset(['filterBulan', 'filterTahun', 'filterStartDate', 'filterEndDate', 'filterCategory']);
        $this->filterBulan = now()->format('m');
        $this->filterTahun = now()->year;
        $this->hitungRingkasan(); 
        $this->resetPage(); 
    }

    public function savePengeluaran()
    {
        $this->validate([
            'category_id' => 'required',
            'nama_pengeluaran' => 'required|string|max:255',
            'qty' => 'required|numeric|min:0.1',
            'harga_satuan' => 'required|numeric',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'metode_pembayaran' => 'required',
        ]);

        $data = [
            'villa_id' => $this->activeVillaId,
            'category_id' => $this->category_id,
            'nama_pengeluaran' => $this->nama_pengeluaran,
            'qty' => $this->qty,
            'satuan' => $this->satuan,
            'harga_satuan' => $this->harga_satuan,
            'nominal' => $this->nominal,
            'tanggal' => $this->tanggal,
            'metode_pembayaran' => $this->metode_pembayaran,
            'keterangan' => $this->keterangan,
        ];

        try {
            if ($this->isEditMode && $this->pengeluaranId) {
                PengeluaranModel::findOrFail($this->pengeluaranId)->update($data);
                session()->flash('success', 'Data pengeluaran berhasil diperbarui.');
            } else {
                PengeluaranModel::create($data);
                session()->flash('success', 'Data pengeluaran berhasil ditambahkan.');
            }

            $this->resetForm();
            $this->hitungRingkasan();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $p = PengeluaranModel::findOrFail($id);
        
        $this->pengeluaranId = $p->id;
        $this->category_id = $p->category_id;
        $this->nama_pengeluaran = $p->nama_pengeluaran;
        $this->qty = $p->qty;
        $this->satuan = $p->satuan;
        $this->harga_satuan = $p->harga_satuan;
        $this->nominal = $p->nominal;
        $this->tanggal = Carbon::parse($p->tanggal)->format('Y-m-d');
        $this->metode_pembayaran = $p->metode_pembayaran;
        $this->keterangan = $p->keterangan; 
        
        $this->isEditMode = true;
        $this->js('window.scrollTo({top: 0, behavior: "smooth"})');
    }

    public function delete($id)
    {
        try {
            PengeluaranModel::findOrFail($id)->delete();
            session()->flash('success', 'Data pengeluaran berhasil dihapus.');
            $this->hitungRingkasan();
            $this->resetPage();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus data.');
        }
    }

    private function hitungRingkasan()
    {
        if (!$this->activeVillaId) return;

        $baseQuery = PengeluaranModel::where('villa_id', $this->activeVillaId);

        $this->ringkasanBulanIni = $baseQuery->clone()->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year)->sum('nominal');
        $this->ringkasanHariIni = $baseQuery->clone()->whereDate('tanggal', now()->toDateString())->sum('nominal');
        $this->ringkasanAllTime = $baseQuery->clone()->sum('nominal');
        $this->ringkasanTotalFilter = $this->applyFilter($baseQuery->clone())->sum('nominal');
    }
    
    private function applyFilter($query)
    {
        if ($this->filterBulan) $query->whereMonth('tanggal', $this->filterBulan);
        if ($this->filterTahun) $query->whereYear('tanggal', $this->filterTahun);
        if ($this->filterStartDate) $query->whereDate('tanggal', '>=', $this->filterStartDate);
        if ($this->filterEndDate) $query->whereDate('tanggal', '<=', $this->filterEndDate);
        if ($this->filterCategory) $query->where('category_id', $this->filterCategory); // Filter Kategori

        return $query;
    }

    private function getFilteredData()
    {
        $baseQuery = PengeluaranModel::with(['villa', 'category'])
            ->where('villa_id', $this->activeVillaId);

        return $this->applyFilter($baseQuery)->latest()->get();
    }

    private function getExportParams()
    {
        return [
            'villa_id' => $this->activeVillaId,
            'bulan'    => $this->filterBulan,
            'tahun'    => $this->filterTahun,
            'start'    => $this->filterStartDate,
            'end'      => $this->filterEndDate,
            'category' => $this->filterCategory, // Kirim kategori ke export
        ];
    }

    public function exportExcel()
    {
        if ($this->getFilteredData()->isEmpty()) {
            session()->flash('error', 'Tidak ada data untuk diekspor.');
            return;
        }
        $this->redirect(route('export.pengeluaran.excel', $this->getExportParams())); 
    }

    public function exportPdf()
    {
        if ($this->getFilteredData()->isEmpty()) {
            session()->flash('error', 'Tidak ada data untuk diekspor.');
            return;
        }
        $this->redirect(route('export.pengeluaran.pdf', $this->getExportParams())); 
    }

    public function render()
    {
        $query = PengeluaranModel::with(['villa', 'category'])
            ->where('villa_id', $this->activeVillaId);

        return view('livewire.master.pengeluaran', [
            'dataPengeluaran' => $this->applyFilter($query)->latest()->paginate($this->perPage, ['*'], 'pengeluaranPage'),
            'categories' => Category::where('type', 'expense')->get(), // Ambil kategori pengeluaran
        ]);
    }
}