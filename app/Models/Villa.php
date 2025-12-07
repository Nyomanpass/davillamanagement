<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Villa extends Model
{
    use HasFactory;

    // Pastikan semua kolom yang diisi di CreateVilla.php ada di sini
    protected $fillable = [
        'nama_villa',
        'alamat_villa',
        'fee_manajemen',
        'service_karyawan',
        'jumlah_kamar',
        'image_logo',
        'image_gallery', // Harus ada karena Anda mengupdate ini
    ];

    // Karena image_gallery disimpan sebagai array JSON, tambahkan casting
    protected $casts = [
        'image_gallery' => 'array',
    ];
}
