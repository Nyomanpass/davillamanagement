<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Villa;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException; 

#[Layout('layouts.app')]
class EditVilla extends Component
{
    use WithFileUploads;

    // --- Properti Villa yang Sedang Diedit ---
    public Villa $villa;

    // --- Properti Form ---
    public $nama_villa;
    public $alamat_villa;
    public $fee_manajemen;
    public $service_karyawan;
    public $jumlah_kamar;

    // --- Properti Upload & Data Lama ---
    public $image_logo; // File upload baru
    public $current_logo_path; // Path logo lama (string)
    
    public $image_gallery = []; // File upload baru (array)
    public $current_gallery_paths = []; // Path gallery lama (array)


    // --- Validasi ---
    protected function rules()
    {
        return [
            // UNIQUE harus dikecualikan untuk ID villa saat ini
            'nama_villa' => 'required|string|max:255|unique:villas,nama_villa,' . $this->villa->id,
            'alamat_villa' => 'required|string|max:500',
            'fee_manajemen' => 'required|numeric|min:0|max:100',
            'service_karyawan' => 'required|numeric|min:0|max:100',
            'jumlah_kamar' => 'required|numeric|min:1',
            'image_logo' => 'nullable|image|max:2048|mimes:png,jpg,jpeg',
            'image_gallery.*' => 'nullable|image|max:2048|mimes:png,jpg,jpeg',
        ];
    }
    
    // --- Mount: Mengisi Data Form ---
  public function mount(Villa $villa)
    {
        $this->villa = $villa;
        
        // Isi properti form dengan data villa
        $this->nama_villa = $villa->nama_villa;
        $this->alamat_villa = $villa->alamat_villa;
        $this->fee_manajemen = $villa->fee_manajemen;
        $this->service_karyawan = $villa->service_karyawan;
        $this->jumlah_kamar = $villa->jumlah_kamar;
        
        // Simpan path gambar lama
        $this->current_logo_path = $villa->image_logo;
        
        // 🛑 KODE PERBAIKAN TOTAL UNTUK GALLERY DI SINI 🛑
        if (is_string($villa->image_gallery) && !empty($villa->image_gallery)) {
            // Jika berupa STRING dan TIDAK KOSONG (berarti JSON mentah dari DB)
            // Lakukan decoding. Gunakan operator ?? untuk memastikan array kosong jika decode gagal.
            $this->current_gallery_paths = json_decode($villa->image_gallery, true) ?? [];
            
        } elseif (is_array($villa->image_gallery)) {
            // Jika sudah ARRAY (berarti Model Casting berhasil)
            $this->current_gallery_paths = $villa->image_gallery;
            
        } else {
            // Jika NULL atau kosong
            $this->current_gallery_paths = [];
        }
    }

    // --- Method Update ---
    // app/Livewire/Master/EditVilla.php

public function updateVilla()
{
    Log::info('DEBUG: updateVilla dipanggil - MULAI VALIDASI');

    try {
        $this->validate();
        Log::info('DEBUG: VALIDASI BERHASIL!');
        
        DB::beginTransaction();
        
        $updateData = [
            'nama_villa' => $this->nama_villa,
            'alamat_villa' => $this->alamat_villa,
            'fee_manajemen' => $this->fee_manajemen,
            'service_karyawan' => $this->service_karyawan,
            'jumlah_kamar' => $this->jumlah_kamar,
        ];

        // 1. Logika Update Logo (TIDAK ADA PERUBAHAN)
        if ($this->image_logo) {
            if ($this->current_logo_path) {
                Storage::disk('public')->delete($this->current_logo_path);
            }
            $logoPath = $this->image_logo->store('villa_logos', 'public');
            $updateData['image_logo'] = $logoPath;
        } else {
            $updateData['image_logo'] = $this->current_logo_path;
        }
        
        // 2. Logika Update Gallery (Hanya menambahkan gambar baru)
        // $galleryPaths adalah ARRAY karena diisi dari $current_gallery_paths
        $galleryPaths = $this->current_gallery_paths; 
        
        if (!empty($this->image_gallery)) {
            foreach ($this->image_gallery as $image) {
                $galleryPaths[] = $image->store('villa_galleries/' . $this->villa->id, 'public');
            }
        }
        
        // 🛑 PERBAIKAN PENTING #1: Hapus json_encode() 🛑
        // Karena Model Casting 'image_gallery' => 'array' sudah diaktifkan di Villa.php,
        // Laravel akan otomatis mengubah ARRAY menjadi STRING JSON saat menyimpan.
        $updateData['image_gallery'] = $galleryPaths;


        // 3. Simpan Perubahan ke Database
        $this->villa->update($updateData);

        DB::commit();

        session()->flash('success', 'Data Villa berhasil diperbarui!');
        
        // Reset properti file upload setelah berhasil
        $this->reset(['image_logo', 'image_gallery']); 
        
        // 🛑 PERBAIKAN PENTING #2: Hapus json_decode() di sini juga 🛑
        // Ambil path lama yang baru dari Model, yang sudah di-cast Laravel
        $this->current_logo_path = $this->villa->image_logo;
        // Gunakan operator ?? untuk memastikan tipe array
        $this->current_gallery_paths = $this->villa->image_gallery ?? []; 

        // Redirect kembali ke halaman kelola
        return redirect()->route('master.kelola.villa');

    } catch (ValidationException $e) {
        Log::error('VALIDATION FAILED: ' . json_encode($e->errors()));
        throw $e; 

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('FATAL UPDATE ERROR: ' . $e->getMessage());
        session()->flash('error', 'Gagal memperbarui data! Cek laravel.log untuk detail.');
    }
}

    // --- Method untuk menghapus gambar spesifik dari gallery ---
    public function removeGalleryImage($index)
    {
        if (isset($this->current_gallery_paths[$index])) {
            $pathToDelete = $this->current_gallery_paths[$index];
            
            // Hapus dari Storage
            Storage::disk('public')->delete($pathToDelete);
            
            // Hapus dari array properti Livewire
            unset($this->current_gallery_paths[$index]);
            $this->current_gallery_paths = array_values($this->current_gallery_paths); // Re-index array
            
            // Update database
            $this->villa->image_gallery = json_encode($this->current_gallery_paths);
            $this->villa->save();
            
            session()->flash('success', 'Gambar gallery berhasil dihapus.');
        }
    }
    
    // --- Render View ---
    public function render()
    {
        return view('livewire.master.edit-villa');
    }
}