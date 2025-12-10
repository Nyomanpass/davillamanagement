<?php

namespace App\Livewire\Master;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Villa;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException; 

class CreateVilla extends Component
{
    use WithFileUploads;
    #[Layout('layouts.app')]

    // --- Properti Form ---
    public $nama_villa;
    public $alamat_villa;
    public $fee_manajemen;
    public $service_karyawan;
    public $jumlah_kamar = 1;

    // Upload
    public $image_gallery = [];
    public $image_logo;

    // --- Validasi ---
    protected $rules = [
        'nama_villa' => 'required|string|max:255|unique:villas,nama_villa',
        'alamat_villa' => 'required|string|max:500',
        'fee_manajemen' => 'required|numeric|min:0|max:100',
        'service_karyawan' => 'required|numeric|min:0|max:100',
        'jumlah_kamar' => 'required|numeric|min:1',
        'image_logo' => 'nullable|image|max:2048|mimes:png,jpg,jpeg',
        'image_gallery.*' => 'nullable|image|max:2048|mimes:png,jpg,jpeg',
    ];

    public function saveVilla()
{
    Log::info('DEBUG: saveVilla dipanggil - MULAI VALIDASI');

    try {
        // Lakukan validasi
        $this->validate();
        
        Log::info('DEBUG: VALIDASI BERHASIL!');

        // --- KODE UTAMA PENYIMPANAN DATA DIMULAI DI SINI ---
        
        DB::beginTransaction();

        // Upload Logo
        $logoPath = null;
        if ($this->image_logo) {
            $logoPath = $this->image_logo->store('villa_logos', 'public');
            Log::info('DEBUG: Logo uploaded: ' . $logoPath);
        }

        // Simpan data villa
        $villa = Villa::create([
            'nama_villa' => $this->nama_villa,
            'alamat_villa' => $this->alamat_villa,
            'fee_manajemen' => $this->fee_manajemen,
            'service_karyawan' => $this->service_karyawan,
            'jumlah_kamar' => $this->jumlah_kamar,
            'image_logo' => $logoPath,
            'image_gallery' => null,
        ]);

        // Upload gallery
        $galleryPaths = [];
        if (!empty($this->image_gallery)) {
            foreach ($this->image_gallery as $image) {
                $galleryPaths[] = $image->store('villa_galleries/' . $villa->id, 'public');
            }
            $villa->image_gallery = $galleryPaths;
            $villa->save();
            Log::info('DEBUG: Gallery uploaded: ' . count($galleryPaths) . ' images');
        }

        DB::commit();

        session()->flash('success', 'Villa baru berhasil ditambahkan!');
        $this->reset();
        return redirect()->route('master.kelola.villa');

    } catch (ValidationException $e) {
        // JIKA VALIDASI GAGAL, CATAT DETAIL ERROR KE LOG
        Log::error('VALIDATION FAILED: ' . json_encode($e->errors()));
        session()->flash('error', 'Validasi gagal! Cek log (larave.log) untuk detail field mana yang error.');
        // Re-throw Exception agar Livewire menampilkan error messages
        throw $e; 

    } catch (\Exception $e) {
        // JIKA ADA ERROR DATABASE/SERVER LAIN
        DB::rollBack();
        Log::error('FATAL SAVE ERROR: ' . $e->getMessage());
        session()->flash('error', 'Gagal menyimpan data! Cek laravel.log untuk detail.');
    }
}



    public function render()
    {
        return view('livewire.master.create-villa');
    }
}
