<div class="min-h-screen flex items-center justify-center bg-amber-50">

    {{-- Container Utama (Efek Dua Kolom) --}}
    <div class="flex w-full max-w-5xl shadow-2xl rounded-2xl overflow-hidden">

        {{-- Kolom Kiri: Logo (Warna Coklat Tua) --}}
        <div class="hidden md:flex flex-col items-center justify-center w-1/2 p-12 bg-primary/80 text-white">
            <div class="p-4 rounded-full">
                {{-- Ganti SVG di bawah dengan logo 'DA' atau 'MA' yang Anda miliki --}}
                <img src="/images/logodua.png" alt="Logo Villa Management">
            </div>
        </div>

        {{-- Kolom Kanan: Form Login (Warna Coklat Muda/Aksen) --}}
        <div class="w-full md:w-1/2 p-10 lg:p-16 bg-secondary flex flex-col justify-center">

            <h2 class="text-3xl font-bold text-center mb-10 text-white uppercase tracking-widest">
                Login
            </h2>

            <form wire:submit.prevent="login" class="space-y-6">

                {{-- Username --}}
                <div>
                    <input type="text"
                           wire:model="username"
                           placeholder="username"
                           {{-- FORM INPUT PUTIH: bg-white, border-white/30, teks input dark --}}
                           class="w-full border-b border-white/30 focus:border-white bg-white text-gray-800 rounded-lg p-3 outline-none placeholder-gray-500 transition duration-150">
                    @error('username')
                        {{-- Error text tetap merah untuk visibilitas --}}
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <input type="password"
                           wire:model="password"
                           placeholder="password"
                           {{-- FORM INPUT PUTIH: bg-white, border-white/30, teks input dark --}}
                           class="w-full border-b border-white/30 focus:border-white bg-white text-gray-800 rounded-lg p-3 outline-none placeholder-gray-500 transition duration-150">
                    @error('password')
                        <p class="text-red-300 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Error Message (Diubah agar kontras di latar belakang gelap) --}}
                @if (session('error'))
                    <div class="bg-red-400 border border-red-500 text-white px-4 py-2 rounded-lg text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Button --}}
                <button
                    class="w-full bg-amber-800 hover:bg-amber-900 text-white py-3 rounded-lg font-semibold shadow-md hover:shadow-lg transition duration-300 ease-in-out">
                    LOGIN
                </button>
            </form>

            {{-- Footer (Diubah menjadi teks putih) --}}
            <p class="text-center text-white text-xs mt-8">
                © {{ date('Y') }} Villa Management System
            </p>

        </div>
    </div>
</div>