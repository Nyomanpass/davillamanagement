<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Villa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_villa',
        'alamat_villa',
        'fee_manajemen',
        'service_karyawan',
        'jumlah_kamar',
        'image_logo',
        'image_gallery', 
    ];

    protected $casts = [
        'image_gallery' => 'array',
    ];
    
  
    public function pendapatan()
    {
        return $this->hasMany(Pendapatan::class);
    }

   
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class);
    }

    public function feeHistories()
    {
        // Mengambil riwayat, yang terbaru ditaruh paling atas
        return $this->hasMany(VillaFeeHistory::class)->orderBy('mulai_berlaku', 'desc');
    }

    public function specialCategories()
    {
        return $this->belongsToMany(Category::class, 'villa_special_categories');
    }
    
}