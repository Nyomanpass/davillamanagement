<?php

namespace App\Livewire\Master;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')]
#[Title('Pengaturan Profil Master')]
class ProfileSettings extends Component
{
    // Properti Lengkap
    public $name;
    public $username;
    public $email;
    public $access_code;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        $user = Auth::user();
        if ($user) {
            $this->name = $user->name;
            $this->username = $user->username;
            $this->email = $user->email;
            $this->access_code = $user->access_code;
        }
    }

    public function updateProfile()
    {
        $userId = Auth::id();
        
        $validated = $this->validate([
            'name'        => 'required|string|max:255',
            'username'    => 'required|string|alpha_dash|unique:users,username,' . $userId,
            'email'       => 'required|email|unique:users,email,' . $userId,
            'access_code' => 'nullable|string|max:10', // Sesuaikan kebutuhan
        ]);

        User::find($userId)->update($validated);

        session()->flash('success', 'Informasi profil berhasil diperbarui.');
    }

    public function updatePassword()
    {
        $this->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::find(Auth::id())->update([
            'password' => Hash::make($this->password)
        ]);

        $this->reset(['password', 'password_confirmation']);
        session()->flash('success_password', 'Password keamanan berhasil diubah.');
    }

    public function render()
    {
        return view('livewire.master.profile-settings');
    }
}