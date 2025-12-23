<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Villa;
use App\Models\User;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class KelolaVilla extends Component
{
    use WithPagination;

    // --- Properti Filter & Pencarian ---
    public $search = '';
    
    // --- Properti Ringkasan ---
    // Menggunakan Compute Property agar data selalu terupdate saat render
    
    // --- Properti Modal Detail ---
    public $isDetailModalOpen = false;
    public ?Villa $selectedVilla = null;

    // --- Life Cycle Hook & Setup ---
    // Mount tidak lagi diperlukan karena kita menggunakan Computed Properties

    // --- Computed Properties untuk Ringkasan Data (Dipanggil saat dibutuhkan di Blade) ---
    // (Properti ini menggantikan hitungRingkasan() dan properti $totalVilla, dll.)
    
    public function getTotalVillaProperty()
    {
        return Villa::count();
    }

    public function getTotalAkunProperty()
    {
        return User::count();
    }

    public function getTotalKaryawanProperty()
    {
        // Asumsi: Karyawan adalah User dengan role 'staf' atau 'karyawan'
       return User::whereIn('role', ['staf', 'staf_master'])->count();
    }


    // --- Logika Modal Detail (Dipanggil oleh tombol 'Detail') ---
    public function showVillaDetail($villaId)
    {
        $villa = Villa::find($villaId);
        
        if (!$villa) {
            session()->flash('error', 'Villa tidak ditemukan.');
            return;
        }

        $this->selectedVilla = $villa;
        $this->isDetailModalOpen = true;
    }
    
    public function closeDetailModal()
    {
        $this->isDetailModalOpen = false;
        $this->selectedVilla = null;
    }
    
    // --- Actions dari Tombol ---

    public function openAddAccountForm()
    {
        session()->flash('info', 'Fungsi Tambah Akun (Modal) akan dibuka di sini.');
    }

    // Method untuk tombol Edit (Jika Anda tetap ingin menggunakan Livewire action)
    public function editVilla($villaId)
    {
        // Tombol Edit di Blade sudah menggunakan <a> tag dengan route('master.edit.villa', ...)
        // Jadi, method ini tidak lagi diperlukan kecuali untuk tombol "Detail" yang kini memanggil showVillaDetail.
        // Jika Anda menggunakan method ini, sebaiknya ia melakukan redirect:
        return $this->redirect(route('master.edit.villa', $villaId), navigate: true); 
    }
    
    public function deleteVilla($villaId)
    {
        try {
            $villa = Villa::findOrFail($villaId);
            
            // Logika hapus gambar jika diperlukan di sini
            // Storage::disk('public')->delete($villa->image_logo); 
            // foreach (json_decode($villa->image_gallery, true) as $path) { ... }
            
            $villa->delete();
            
            // Tidak perlu memanggil hitungRingkasan() karena kita pakai Computed Properties
            session()->flash('success', "Villa ID {$villaId} berhasil dihapus.");
            $this->resetPage(); 
        } catch (\Exception $e) {
            session()->flash('error', "Gagal menghapus villa: " . $e->getMessage());
        }
    }
    
    // Reset pagination saat search berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // --- Method Render ---
    public function render()
    {
        $query = Villa::query();

        // Terapkan Pencarian
        if ($this->search) {
            $query->where('nama_villa', 'like', '%' . $this->search . '%')
                  ->orWhere('alamat_villa', 'like', '%' . $this->search . '%');
        }

        $villas = $query->paginate(12);

        return view('livewire.master.kelola-villa', [
            'villas' => $villas,
        ]);
    }
}