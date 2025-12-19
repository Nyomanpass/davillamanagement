<?php
namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\VillaFeeHistory;
use App\Models\Villa;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class VillaFeeHistoryManager extends Component
{
    public $villaId; // Akan diisi otomatis dari {villaId} di Route
    public $villaName;
    public $fee_manajemen, $service_karyawan, $mulai_berlaku;
    public $editingId = null;

    public $selectedCategories = []; // Penampung ID kategori yang dicentang

    // Fungsi mount akan menangkap villaId dari URL
  public function mount($villaId)
    {
        // Gunakan 'with' agar lebih cepat mengambil data kategori khusus
        $villa = Villa::with('specialCategories')->findOrFail($villaId);
        
        $this->villaId = $villa->id;
        $this->villaName = $villa->nama_villa;

        // Ambil semua ID kategori yang sudah tersimpan untuk villa ini
        // Kita ubah ke String agar sinkron dengan value di HTML Checkbox
        $this->selectedCategories = $villa->specialCategories->pluck('id')
            ->map(fn($id) => (string)$id)
            ->toArray();
    }


    protected $rules = [
        'fee_manajemen' => 'required|numeric|min:0|max:100',
        'service_karyawan' => 'required|numeric|min:0|max:100',
        'mulai_berlaku' => 'required|date',
    ];

    public function saveCategories()
    {
        // Validasi sederhana
        if(empty($this->selectedCategories)) {
            session()->flash('error_category', 'Pilih minimal satu kategori.');
            return;
        }

        $villa = Villa::find($this->villaId);
        
        // sync() akan otomatis menghapus yang tidak dicentang 
        // dan menambah yang baru dicentang di tabel villa_special_categories
        $villa->specialCategories()->sync($this->selectedCategories);
        
        session()->flash('success_category', 'Kategori Service Fee berhasil diperbarui untuk ' . $this->villaName);
    }

    public function save()
    {
        $this->validate();

        VillaFeeHistory::updateOrCreate(
            ['id' => $this->editingId],
            [
                'villa_id' => $this->villaId, // Selalu gunakan villaId dari URL
                'fee_manajemen' => $this->fee_manajemen,
                'service_karyawan' => $this->service_karyawan,
                'mulai_berlaku' => $this->mulai_berlaku,
            ]
        );

        $this->reset(['fee_manajemen', 'service_karyawan', 'mulai_berlaku', 'editingId']);
        session()->flash('success', 'Riwayat fee berhasil disimpan.');
    }

    public function edit($id)
    {
        $history = VillaFeeHistory::findOrFail($id);
        $this->editingId = $id;
        $this->fee_manajemen = $history->fee_manajemen;
        $this->service_karyawan = $history->service_karyawan;
        $this->mulai_berlaku = $history->mulai_berlaku->format('Y-m-d');
    }

    public function delete($id)
    {
        VillaFeeHistory::destroy($id);
        session()->flash('success', 'Data berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.master.villa-fee-history-manager', [
            'histories' => VillaFeeHistory::where('villa_id', $this->villaId)
                            ->orderBy('mulai_berlaku', 'desc')
                            ->get(),
            'allCategories' => \App\Models\Category::orderBy('name', 'asc')->get()
        ]);
    }
}