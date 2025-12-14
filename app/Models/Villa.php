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
}