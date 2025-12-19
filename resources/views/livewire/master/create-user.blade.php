<form wire:submit.prevent="save">
    
    {{-- Header Form --}}
    <div class="flex items-center justify-between gap-4 mb-8 border-b border-slate-100 pb-6">
    {{-- Grup Kiri: Ikon dan Judul --}}
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center shadow-sm">
            <i class="fa-solid fa-user-plus text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl font-black text-slate-800 tracking-tight leading-none">Buat Akun Baru</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Lengkapi data untuk mendaftarkan pengguna.</p>
        </div>
    </div>

    {{-- Grup Kanan: Tombol Kembali --}}
    <a href="{{ route('master.kelola.villa') }}" 
       class="flex items-center px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-amber-600 transition shadow-sm group">
        <i class="fas fa-arrow-left mr-2 text-xs transition-transform group-hover:-translate-x-1"></i>
        Kembali
    </a>
</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Section 1: Profil Pengguna --}}
        <div class="space-y-4">
            <p class="text-[10px] font-black uppercase tracking-[0.15em] text-amber-600 mb-2">Informasi Profil</p>
            
            {{-- Nama --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 border-r border-slate-100 pr-2">
                        <i class="fa-solid fa-id-card text-xs"></i>
                    </span>
                    <input wire:model="name" type="text" placeholder="Nama lengkap"
                        class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all text-sm font-medium">
                </div>
                @error('name') <p class="text-xs text-red-500 mt-1 font-bold ml-1">{{ $message }}</p> @enderror
            </div>

            {{-- Username --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Username</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 border-r border-slate-100 pr-2">
                        <i class="fa-solid fa-at text-xs"></i>
                    </span>
                    <input wire:model="username" type="text" placeholder="Username"
                        class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all text-sm font-medium">
                </div>
                @error('username') <p class="text-xs text-red-500 mt-1 font-bold ml-1">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 border-r border-slate-100 pr-2">
                        <i class="fa-solid fa-envelope text-xs"></i>
                    </span>
                    <input wire:model="email" type="email" placeholder="Email"
                        class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all text-sm font-medium">
                </div>
                @error('email') <p class="text-xs text-red-500 mt-1 font-bold ml-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Section 2: Akses & Keamanan --}}
        <div class="space-y-4">
            <p class="text-[10px] font-black uppercase tracking-[0.15em] text-amber-600 mb-2">Akses & Keamanan</p>
            
            {{-- Role --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Role</label>
                <select wire:model.live="role"
                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all text-sm font-bold text-slate-700">
                    <option value="">-- Pilih Role --</option>
                    <option value="owner">Owner</option>
                    <option value="staf">Staf</option>
                    <option value="staf_master">Staf Master</option>
                </select>
                @error('role') <p class="text-xs text-red-500 mt-1 font-bold ml-1">{{ $message }}</p> @enderror
            </div>

            {{-- Pilih Villa --}}
            @if(in_array($role, ['owner', 'staf']))
            <div class="animate-in fade-in duration-300">
                <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Pilih Villa</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 border-r border-slate-100 pr-2">
                        <i class="fa-solid fa-house-chimney text-xs"></i>
                    </span>
                    <select wire:model="villa_id"
                        class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all text-sm font-bold text-slate-800">
                        <option value="">-- Pilih Villa --</option>
                        @foreach($listVilla as $villa)
                            <option value="{{ $villa->id }}">{{ $villa->nama_villa }}</option>
                        @endforeach
                    </select>
                </div>
                @error('villa_id') <p class="text-xs text-red-500 mt-1 font-bold ml-1">{{ $message }}</p> @enderror
            </div>
            @endif

            {{-- Access Code (PIN) - SEKARANG BG WHITE --}}
            <div class="bg-white p-5 rounded-2xl border-2 border-slate-100 shadow-sm relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-2 opacity-10 group-focus-within:opacity-30 transition-opacity">
                    <i class="fa-solid fa-shield-halved text-4xl text-slate-900"></i>
                </div>
                <label class="block text-[10px] font-black text-slate-400 mb-2 ml-1 uppercase tracking-widest">Access Code (6 Digit PIN)</label>
                <input wire:model="access_code" 
                    type="password" 
                    maxlength="6" 
                    placeholder="••••••"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                    class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:outline-none text-slate-800 text-center text-xl tracking-[0.5em] font-black placeholder-slate-300">
                <p class="text-[9px] text-slate-400 mt-2 italic text-center">*PIN verifikasi kedua setelah login.</p>
                @error('access_code') <p class="text-xs text-red-500 mt-1 font-bold text-center">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Section 3: Hak Akses --}}
    @if(in_array($role, ['owner','staf', 'staf_master']))
    <div class="mt-8 border border-slate-200 p-6 rounded-[1.5rem] bg-slate-50/30">
        <div class="flex items-center gap-2 mb-4">
            <i class="fa-solid fa-fingerprint text-amber-600"></i>
            <p class="font-black text-[10px] text-slate-700 uppercase tracking-widest">Hak Akses Modul</p>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach(['pendapatan','pengeluaran'] as $modul)
            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                <p class="text-xs font-black text-slate-500 mb-3 border-b pb-2 capitalize">{{ $modul }}</p>
                <div class="flex flex-wrap gap-4">
                    @foreach(['create','update','delete'] as $act)
                    <label class="inline-flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" wire:model="permissions.{{ $modul }}.{{ $act }}"
                            class="h-5 w-5 text-amber-600 border-slate-300 rounded-lg focus:ring-amber-500 transition-all cursor-pointer">
                        <span class="text-sm font-bold text-slate-600 group-hover:text-amber-600 capitalize transition-colors">{{ $act }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Section 4: Password --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-slate-100">
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Password</label>
            <input wire:model="password" type="password" placeholder="Password"
                class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all text-sm font-medium">
            @error('password') <p class="text-xs text-red-500 mt-1 font-bold ml-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1 ml-1">Konfirmasi Password</label>
            <input wire:model="password_confirmation" type="password" placeholder="Konfirmasi Password"
                class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 focus:outline-none transition-all text-sm font-medium">
        </div>
    </div>

    {{-- Tombol Simpan & Batal --}}
    <div class="mt-10 flex flex-col sm:flex-row justify-end items-center gap-3 border-t border-slate-50 pt-8">
        
     
       

        {{-- Tombol Simpan --}}
        <button type="submit"
            class="group w-full sm:w-auto px-10 py-4 bg-slate-900 text-white rounded-2xl hover:bg-amber-600 shadow-xl shadow-slate-200 transition-all duration-300 active:scale-95 flex items-center justify-center gap-3">
            <span class="font-black text-sm uppercase tracking-widest">Simpan Akun</span>
            <i class="fa-solid fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
        </button>
        
    </div>
</form>