<div class="">
    <div class="space-y-8">
        
        {{-- HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">
                    Account <span class="text-amber-600">Settings</span>
                </h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg uppercase tracking-wider border border-amber-200">Administrator</span>
                    <p class="text-slate-600 font-bold text-base">Kelola Profil & Keamanan Master</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mr-2">Status:</span>
                <span class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full border border-emerald-100">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Verified Account
                </span>
            </div>
        </div>

        {{-- GRID UTAMA --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- KOLOM KIRI: FORM IDENTITAS (MENGGUNAKAN COL-SPAN 2) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                        <div>
                            <h4 class="text-base font-bold text-slate-800">Informasi Pribadi</h4>
                            <p class="text-sm text-slate-500 mt-1">Perbarui identitas utama Anda untuk keperluan sistem.</p>
                        </div>
                        <button wire:click="updateProfile" 
                            class="px-6 py-2.5 bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-amber-700 transition-all shadow-md shadow-amber-100 active:scale-95 flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Identitas
                        </button>
                    </div>

                    <div class="p-8">
                        {{-- NOTIFIKASI SUCCESS --}}
                        @if (session()->has('success'))
                            <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show"
                                 class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm font-bold flex items-center gap-3">
                                 <i class="fas fa-check-circle text-lg"></i> {{ session('success') }}
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            {{-- Nama Lengkap --}}
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Nama Lengkap</label>
                                <input type="text" wire:model="name" 
                                    class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-sm py-3 px-4 shadow-sm bg-slate-50/50 transition-all">
                                @error('name') <span class="text-[10px] text-red-500 font-bold ml-1 uppercase tracking-tight">{{ $message }}</span> @enderror
                            </div>

                            {{-- Username --}}
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Username</label>
                                <input type="text" wire:model="username" 
                                    class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-sm py-3 px-4 shadow-sm bg-slate-50/50 transition-all">
                                @error('username') <span class="text-[10px] text-red-500 font-bold ml-1 uppercase tracking-tight">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Alamat Email</label>
                                <input type="email" wire:model="email" 
                                    class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-sm py-3 px-4 shadow-sm bg-slate-50/50 transition-all">
                                @error('email') <span class="text-[10px] text-red-500 font-bold ml-1 uppercase tracking-tight">{{ $message }}</span> @enderror
                            </div>

                            {{-- Kode Akses --}}
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-500 uppercase tracking-widest ml-1">Access Code (PIN)</label>
                                <input type="text" wire:model="access_code" 
                                    class="w-full rounded-xl border-slate-200 focus:border-amber-500 focus:ring-amber-500 text-sm py-3 px-4 shadow-sm bg-slate-50/50 transition-all"
                                    placeholder="Contoh: 1234">
                                @error('access_code') <span class="text-[10px] text-red-500 font-bold ml-1 uppercase tracking-tight">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: UPDATE PASSWORD --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-6">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-base font-bold text-slate-800">Keamanan Password</h2>
                        <p class="text-xs text-slate-500 mt-1">Gunakan kombinasi yang aman.</p>
                    </div>
                    
                    <div class="p-6 space-y-5">
                        @if (session()->has('success_password'))
                            <div class="p-4 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-bold border border-emerald-100">
                                {{ session('success_password') }}
                            </div>
                        @endif

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500 tracking-wide ml-1">Password Baru</label>
                            <input type="password" wire:model="password" 
                                class="w-full rounded-xl border-slate-300 focus:border-slate-800 focus:ring-slate-800 text-sm py-3 px-4 shadow-sm transition-all">
                            @error('password') <span class="text-[10px] text-red-500 font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500 tracking-wide ml-1">Konfirmasi Password</label>
                            <input type="password" wire:model="password_confirmation" 
                                class="w-full rounded-xl border-slate-300 focus:border-slate-800 focus:ring-slate-800 text-sm py-3 px-4 shadow-sm transition-all">
                        </div>

                        <div class="pt-4">
                            <button wire:click="updatePassword" 
                                class="w-full py-3.5 bg-slate-800 text-white rounded-xl font-bold text-sm hover:bg-black transition shadow-lg active:scale-[0.98] flex items-center justify-center gap-2">
                                <i class="fas fa-lock"></i> Perbarui Password
                            </button>
                        </div>
                    </div>
                </div>
              
            </div>
        </div>
    </div>
</div>