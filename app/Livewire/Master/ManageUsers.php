<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Support\Facades\Hash;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class ManageUsers extends Component
{
    use WithPagination;

    #[Layout('layouts.app')]
    // Properti untuk Modal Update
    public $updateModal = false;
    public $userId;
    public $name;
    public $username;
    public $email;
    public $role = '';
    public $villa_id;
    public $permissions = [];
    public $access_code; // Properti PIN Baru
    public $newPassword; // Untuk update password (opsional)
    public $newPassword_confirmation;

    // Data pendukung
    public $listVilla = [];
    public $search = ''; // Properti untuk pencarian
    public $paginate = 10; // Properti untuk paginasi

    // Mount (diinisialisasi sekali)
    public function mount()
    {
        $this->listVilla = Villa::all();
    }

    // Reset halaman paginasi saat properti search berubah
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Logika Hak Akses saat role di modal update berubah
    public function updatedRole($value)
    {
        // Owner sekarang juga bisa punya permissions sesuai permintaan sebelumnya
        if (in_array($value, ['owner', 'staf', 'staf_master'])) {
            // Jika pindah role, inisialisasi permissions jika null/kosong
            if (empty($this->permissions)) {
                 $this->permissions = [
                    'pendapatan' => ['create' => false, 'update' => false, 'delete' => false],
                    'pengeluaran' => ['create' => false, 'update' => false, 'delete' => false],
                ];
            }
        } else {
            $this->permissions = [];
        }

        // Logika Pilih Villa (Mengosongkan jika bukan owner/staf)
        if (!in_array($value, ['owner', 'staf'])) {
            $this->villa_id = null;
        }
    }

    // --- READ (Menampilkan Data) ---
    public function getUsers()
    {
        $query = User::with('villa')
                     ->where('role', '!=', 'master')
                     ->latest();

        // Logika Pencarian
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('username', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('role', 'like', '%' . $this->search . '%');
            });
        }

        return $query->paginate($this->paginate);
    }

    // --- UPDATE (Membuka Modal) ---
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        
        // Isi properti dengan data user
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->villa_id = $user->villa_id;
        $this->access_code = $user->access_code; // Ambil Access Code dari database
        
        // Pastikan permissions diisi dengan array
        $this->permissions = is_array($user->permissions) ? $user->permissions : [];
        
        // Atur state modal
        $this->updateModal = true;
    }

    // --- UPDATE (Menyimpan Perubahan) ---
    public function update()
    {
        // Validasi
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $this->userId,
            'email' => 'required|email|unique:users,email,' . $this->userId,
            'role' => 'required|in:owner,staf,staf_master',
            'access_code' => 'required|numeric|digits:6', // Validasi Access Code wajib 6 digit
        ];

        if (in_array($this->role, ['owner','staf'])) {
            $rules['villa_id'] = 'required|exists:villas,id';
        }

        // Validasi password baru jika diisi
        if ($this->newPassword) {
            $rules['newPassword'] = 'required|string|min:6|confirmed';
        }

        $this->validate($rules, [
            'newPassword.required' => 'Password baru harus diisi jika ingin diubah.',
            'newPassword.min' => 'Password minimal 6 karakter.',
            'newPassword.confirmed' => 'Konfirmasi password tidak cocok.',
            'access_code.digits' => 'Access code harus 6 digit angka.',
        ]);

        $user = User::findOrFail($this->userId);

        // Tentukan data permissions (Owner, Staf, Staf Master diizinkan punya permission)
        $permissionsData = null;
        if (in_array($this->role, ['owner', 'staf', 'staf_master'])) {
            $permissionsData = $this->permissions;
        }

        // Data yang akan diupdate
        $updateData = [
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'villa_id' => in_array($this->role, ['owner','staf']) ? $this->villa_id : null,
            'access_code' => $this->access_code, // Update Access Code
            'permissions' => $permissionsData,
        ];

        // Tambahkan password baru jika diisi
        if ($this->newPassword) {
            $updateData['password'] = Hash::make($this->newPassword);
        }

        $user->update($updateData);

        session()->flash('success', 'User ' . $this->name . ' berhasil diupdate.');
        $this->closeModal();
    }

    // --- DELETE ---
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $name = $user->name;
        $user->delete();

        session()->flash('success', 'User ' . $name . ' berhasil dihapus.');
    }

    // --- UTILITAS MODAL ---
    public function closeModal()
    {
        $this->updateModal = false;
        $this->reset([
            'userId', 'name', 'username', 'email', 'role', 'villa_id', 
            'permissions', 'newPassword', 'newPassword_confirmation', 'access_code'
        ]);
    }

    // Render View
    public function render()
    {
        return view('livewire.master.manage-users', [
            'users' => $this->getUsers(),
        ]);
    }
}