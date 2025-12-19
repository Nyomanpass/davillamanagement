<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;

class CreateUser extends Component
{
    #[Layout('layouts.app')]
    public $name;
    public $username;
    public $email;
    public $role = ''; 
    public $villa_id;
    public $password;
    public $password_confirmation;
    public $listVilla = [];
    public $permissions = [];
    public $access_code;

    public function mount()
    {
        $this->listVilla = Villa::all();
    }

    public function updatedRole($value)
    {
        // Logika Hak Akses
        if (in_array($value, ['owner', 'staf', 'staf_master'])) {
            $this->permissions = [
                'pendapatan' => ['create' => false, 'update' => false, 'delete' => false],
                'pengeluaran' => ['create' => false, 'update' => false, 'delete' => false],
            ];
        } else {
            $this->permissions = [];
        }

        // Logika Pilih Villa (Mengosongkan jika bukan owner/staf)
        if (!in_array($value, ['owner', 'staf'])) {
            $this->villa_id = null;
        }
    }

    public function save()
    {
        // ... (Validasi rules tetap sama) ...
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:owner,staf,staf_master',
            'password' => 'required|string|min:6|confirmed',
            'access_code' => 'required|numeric|digits:6',
        ];

        if (in_array($this->role, ['owner','staf'])) {
            $rules['villa_id'] = 'required|exists:villas,id';
        }

        $this->validate($rules);

        // --- PERUBAHAN UTAMA DI SINI ---
        
        // Tentukan data permissions yang akan disimpan (HANYA ARRAY PHP)
        $permissionsData = null;
        if (in_array($this->role, ['owner', 'staf', 'staf_master'])) {
            // Langsung gunakan array PHP ($this->permissions).
            // Model User akan mengurus JSON encoding.
            $permissionsData = $this->permissions; 
        }

        User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'villa_id' => in_array($this->role, ['owner','staf']) ? $this->villa_id : null,
            'password' => Hash::make($this->password),
            'access_code' => $this->access_code, // Simpan PIN
            
            // Menggunakan array PHP $permissionsData
            'permissions' => $permissionsData, 
        ]);

        session()->flash('success', 'User berhasil dibuat.');
        $this->reset(['name','username','email','role','villa_id','password','password_confirmation','permissions', 'access_code']);
    }
    
    public function render()
    {
        return view('livewire.master.create-user');
    }
}