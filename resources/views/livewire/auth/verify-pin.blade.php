<div class="min-h-screen flex items-center justify-center bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">

    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-[2.5rem] shadow-2xl border border-slate-100 relative">
        
        {{-- Logo Section --}}
        <div class="flex flex-col items-center">
            <div class="mb-6">
                <img src="{{ asset('images/logosatu.png') }}" alt="Logo" class="h-20 w-auto object-contain">
            </div>
        
            <h2 class="text-center text-2xl font-black text-slate-800 tracking-tight">
                Verifikasi Keamanan
            </h2>
            <p class="mt-2 text-center text-sm text-slate-500 font-medium px-4">
                Masukkan 6 digit PIN akses Anda untuk membuka dashboard villa.
            </p>
        </div>

        <form class="mt-8 space-y-6" wire:submit.prevent="verify">
            <div class="relative group">
                {{-- Input PIN dengan Styling Modern --}}
                <input wire:model="pin" 
                       type="password" 
                       maxlength="6" 
                       required 
                       autofocus
                       class="appearance-none block w-full px-3 py-5 border-2 border-slate-100 placeholder-slate-300 text-slate-800 rounded-2xl focus:outline-none focus:ring-0 focus:border-amber-500 transition-all text-4xl tracking-[0.6em] text-center font-black shadow-inner bg-slate-50/50" 
                       placeholder="••••••">
                
                {{-- Border Glow Effect saat Fokus --}}
                <div class="absolute inset-0 rounded-2xl pointer-events-none border border-amber-500/0 group-focus-within:border-amber-500/20 transition-all"></div>
            </div>

            @error('pin')
                <div class="flex items-center justify-center gap-2 text-red-600 text-xs font-bold bg-red-50 p-3 rounded-xl border border-red-100 animate-shake">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $message }}
                </div>
            @enderror

            <div class="pt-2">
                <button type="submit" 
                        class="group relative w-full flex items-center justify-center py-4 px-4 border border-transparent text-sm font-black rounded-2xl text-white bg-amber-600 hover:bg-slate-900 shadow-xl transition-all duration-300 active:scale-95"> 
                    Buka Akses Dashboard
                </button>
            </div>
        </form>

        <div class="text-center mt-6">
            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                    class="inline-flex items-center text-xs text-slate-400 hover:text-red-500 font-bold transition-all uppercase tracking-widest">
                <i class="fa-solid fa-arrow-left-long mr-2"></i> Batal & Keluar
            </button>
            <form id="logout-form" action="{{ route('logout') }}" method="GET" class="hidden"></form>
        </div>
    </div>
    <style>
    /* Animasi getar jika PIN salah */
    .animate-shake {
        animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
    }
    @keyframes shake {
        10%, 90% { transform: translate3d(-1px, 0, 0); }
        20%, 80% { transform: translate3d(2px, 0, 0); }
        30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
        40%, 60% { transform: translate3d(4px, 0, 0); }
    }
</style>
</div>

