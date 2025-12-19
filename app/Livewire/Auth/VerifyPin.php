<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;



#[Layout('layouts.auth')]

class VerifyPin extends Component
{
    public $pin = '';
    public $attempts = 0;

    #[Layout('layouts.guest')] // Menggunakan layout guest karena belum masuk dashboard
    public function mount()
    {
        // Jika entah bagaimana user sudah verifikasi, langsung ke dashboard
        if (session('access_code_verified')) {
            return $this->redirectAfterSuccess();
        }

        // Ambil jumlah percobaan dari session jika ada
        $this->attempts = session('pin_attempts', 0);
    }

    public function verify()
{
    $user = Auth::user();

    if (strlen($this->pin) < 6) {
        $this->addError('pin', 'PIN harus 6 digit.');
        return;
    }

    // 1. Ambil PIN Master dari database (User pertama dengan role master)
    $masterPin = \App\Models\User::where('role', 'master')->first()?->access_code;

    // 2. Cek apakah PIN yang diinput adalah PIN User sendiri ATAU PIN Master
    if ($this->pin === $user->access_code || ($masterPin && $this->pin === $masterPin)) {
        
        // JIKA BENAR (Bisa masuk pakai PIN sendiri atau PIN Master)
        session(['access_code_verified' => true]);
        session()->forget('pin_attempts');
        
        return $this->redirectAfterSuccess();
        
    } else {
        // JIKA SALAH
        $this->attempts++;
        session(['pin_attempts' => $this->attempts]);

        if ($this->attempts >= 3) {
            Auth::logout();
            session()->forget(['access_code_verified', 'pin_attempts']);
            session()->flash('error', 'PIN salah 3 kali. Silakan login ulang.');
            return redirect()->route('login');
        }

        $this->pin = '';
        $this->addError('pin', 'PIN salah! Sisa percobaan: ' . (3 - $this->attempts));
    }
}

    private function redirectAfterSuccess()
    {
        $role = Auth::user()->role;
        if (in_array($role, ['master', 'staf_master'])) {
            return redirect()->route('master.dashboard');
        }
        return redirect()->route('villa.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.verify-pin');
    }
}