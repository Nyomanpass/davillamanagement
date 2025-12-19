<?php

namespace App\Livewire\Auth; 

use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    #[Layout('layouts.auth')]

    public $username;
    public $password;

    public function render()
    {
        return view('livewire.auth.login');
    }

    public function login()
    {
        $this->validate([
            'username' => 'required',
            'password' => 'required|min:6',
        ]);

        // Login menggunakan username & password
        if (!Auth::attempt([
            'username' => $this->username,
            'password' => $this->password,
        ])) {
            session()->flash('error', 'Username atau password salah.');
            return;
        }

        session()->regenerate();
        $user = Auth::user(); // 🔥 aman, tidak error

        // ================================
        // 🔥 Logika Multi Role
        // ================================

        // MASTER ADMIN
        if (in_array($user->role, ['master', 'staf_master'])) { // <--- PERUBAHAN DI SINI

            if ($user->villa_id !== null) {
                Auth::logout();
                session()->flash('error', 'Akun master tidak boleh memiliki villa_id.');
                return;
            }

            return redirect()->route('master.dashboard');
        }

        // OWNER / STAF
        if (in_array($user->role, ['owner', 'staf'])) {

            if ($user->villa_id === null) {
                Auth::logout();
                session()->flash('error', 'Akun owner/staf harus memiliki villa.');
                return;
            }

            return redirect()->route('villa.dashboard');
        }

        // Jika role tidak dikenali
        Auth::logout();
        session()->flash('error', 'Role tidak dikenali.');
    }

    public function logout()
    {
        session()->forget('access_code_verified');
        Auth::logout();
        session()->forget('villa_id');
        return redirect()->route('login');
    }
}
