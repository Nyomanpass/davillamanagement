<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // WAJIB: Import BelongsTo

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',      // <-- TAMBAHKAN
        'email',
        'password',
        'role',          // <-- TAMBAHKAN
        'villa_id',      // <-- TAMBAHKAN
        'permissions',   // <-- TAMBAHKAN
        'access_code',
    ];

    public function villa(): BelongsTo
    {
        // Secara default, Laravel akan mencari foreign key 'villa_id' di tabel 'users'
        // dan mencocokkannya dengan primary key 'id' di tabel 'villas'.
        return $this->belongsTo(Villa::class);
    }
    
    /**
     * Memeriksa apakah user memiliki izin tertentu pada modul tertentu.
     *
     * @param string $modul, e.g., 'pendapatan', 'pengeluaran'
     * @param string $action, e.g., 'create', 'update', 'delete'
     * @return bool
     */
    public function hasPermissionTo($modul, $action)
    {
        // 1. STAF MASTER selalu mendapatkan akses penuh.
        if ($this->role === 'master') {
            return true;
        }

        // 2. Cek berdasarkan permissions (Termasuk Owner dan Staf Biasa)
        
        // Gunakan $this->permissions ?? [] untuk memastikan ini adalah array
        $permissions = $this->permissions ?? []; 

        // Cek apakah modul dan aksi ada di array permissions
        if (
            is_array($permissions) && 
            isset($permissions[$modul]) && 
            isset($permissions[$modul][$action])
        ) {
            // Mengembalikan nilai boolean (true/false) dari permission
            return (bool) $permissions[$modul][$action]; 
        }

        // 3. Jika izin tidak ditemukan, kembalikan false.
        return false;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array', // <-- WAJIB: TAMBAHKAN BARIS INI!
            'access_code' => 'encrypted',
        ];
    }
}
