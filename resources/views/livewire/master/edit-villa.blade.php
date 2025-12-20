<div>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">
                    Edit <span class="text-amber-600">Villa</span>
                </h1>
                <p class="text-sm text-slate-500 font-medium">Update informasi properti secara berkala.</p>
            </div>
            <a href="{{ route('master.kelola.villa') }}" 
               class="px-5 py-2.5 text-sm font-bold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session()->has('success'))
            <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show"
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center gap-3 animate-in fade-in slide-in-from-top-2 shadow-sm">
                <i class="fas fa-check-circle text-base"></i>
                <span class="text-sm font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <form wire:submit.prevent="updateVilla" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- KOLOM KIRI: FORM DATA --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-700">Informasi & Detail Villa</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Baris 1: Nama & Alamat --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase text-slate-500 tracking-wider ml-1">Nama Villa</label>
                                <input type="text" wire:model="nama_villa" 
                                    class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-3 shadow-sm">
                                @error('nama_villa') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase text-slate-500 tracking-wider ml-1">Alamat Lengkap</label>
                                <input type="text" wire:model="alamat_villa" 
                                    class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-3 shadow-sm">
                                @error('alamat_villa') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Baris 2: Konfigurasi Angka --}}
                        <div class="grid grid-cols-3 gap-6 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                            
                            {{-- Fee Management --}}
                            <div class="space-y-2 text-center">
                                <label class="text-[10px] font-black uppercase text-slate-500 tracking-tighter">Fee Management</label>
                                <div class="relative">
                                    {{-- Tambahkan step="0.1" dan placeholder --}}
                                    <input type="number" 
                                        step="0.1" 
                                        wire:model="fee_manajemen" 
                                        placeholder="0.0"
                                        class="w-full text-center rounded-xl border-slate-200 text-base py-2.5 pr-8 font-bold focus:ring-amber-500 focus:border-amber-500">
                                    <span class="absolute right-3 top-2.5 text-slate-400 text-sm">%</span>
                                </div>
                                @error('fee_manajemen') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Service Staff --}}
                            <div class="space-y-2 text-center">
                                <label class="text-[10px] font-black uppercase text-slate-500 tracking-tighter">Service Staff</label>
                                <div class="relative">
                                    {{-- Tambahkan step="0.1" dan placeholder --}}
                                    <input type="number" 
                                        step="0.1" 
                                        wire:model="service_karyawan" 
                                        placeholder="0.0"
                                        class="w-full text-center rounded-xl border-slate-200 text-base py-2.5 pr-8 font-bold text-emerald-600 focus:ring-emerald-500 focus:border-emerald-500">
                                    <span class="absolute right-3 top-2.5 text-slate-400 text-sm">%</span>
                                </div>
                                @error('service_karyawan') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Total Kamar --}}
                            <div class="space-y-2 text-center">
                                <label class="text-[10px] font-black uppercase text-slate-500 tracking-tighter">Total Kamar</label>
                                <div class="relative">
                                    {{-- Kamar tetap angka bulat, tidak perlu step --}}
                                    <input type="number" 
                                        wire:model="jumlah_kamar" 
                                        class="w-full text-center rounded-xl border-slate-200 text-base py-2.5 pr-8 font-bold text-amber-600 focus:ring-amber-500 focus:border-amber-500">
                                    <i class="fas fa-bed absolute right-3 top-3.5 text-slate-300 text-xs"></i>
                                </div>
                                @error('jumlah_kamar') <p class="text-[10px] text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                            </div>
                            
                        </div>
                    </div>
                </div>

                {{-- GALERI FOTO --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-700">Gallery Properti</h2>
                        <input type="file" wire:model="image_gallery" multiple id="gal" class="hidden">
                        <label for="gal" class="cursor-pointer text-sm font-bold text-amber-600 hover:text-amber-700 transition">
                            <i class="fas fa-plus-circle mr-1"></i> Tambah Foto
                        </label>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-4">
                            {{-- Foto Lama --}}
                            @foreach ($current_gallery_paths as $index => $path)
                                <div class="relative aspect-square group">
                                    <img src="{{ Storage::url($path) }}" class="w-full h-full object-cover rounded-xl border border-slate-100 shadow-sm transition group-hover:brightness-50">
                                    <button type="button" wire:click="removeGalleryImage({{ $index }})" 
                                        onclick="confirm('Hapus foto ini?') || event.stopImmediatePropagation()"
                                        class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="bg-red-600 h-8 w-8 rounded-full flex items-center justify-center shadow-lg">
                                            <i class="fas fa-trash text-white text-xs"></i>
                                        </div>
                                    </button>
                                </div>
                            @endforeach

                            {{-- Preview Foto Baru --}}
                            @if (!empty($image_gallery))
                                @foreach ($image_gallery as $image)
                                    <div class="relative aspect-square">
                                        <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover rounded-xl border-2 border-dashed border-amber-300 opacity-70">
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="bg-amber-500 text-white text-[10px] font-black px-2 py-1 rounded-lg shadow-sm">BARU</span>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        @error('image_gallery.*') <p class="text-xs text-red-500 font-bold mt-4 italic">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: LOGO & ACTION --}}
            <div class="space-y-6">
                {{-- LOGO CARD --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-8 text-center">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-6">Logo Utama</h2>
                    
                    <div class="relative inline-block mx-auto">
                        @if ($image_logo)
                            <img src="{{ $image_logo->temporaryUrl() }}" class="w-40 h-40 object-cover rounded-full border-4 border-amber-400 shadow-xl animate-in zoom-in-95">
                        @elseif ($current_logo_path)
                            <img src="{{ Storage::url($current_logo_path) }}" class="w-40 h-40 object-cover rounded-full border-4 border-slate-100 shadow-lg">
                        @else
                            <div class="w-40 h-40 rounded-full bg-slate-50 border-4 border-dashed border-slate-200 flex items-center justify-center">
                                <i class="fas fa-image text-slate-300 text-4xl"></i>
                            </div>
                        @endif
                        
                        <label class="absolute bottom-2 right-2 h-11 w-11 bg-slate-800 text-white rounded-full flex items-center justify-center border-4 border-white cursor-pointer hover:bg-amber-600 transition shadow-lg">
                            <i class="fas fa-camera text-sm"></i>
                            <input type="file" wire:model="image_logo" class="hidden">
                        </label>
                    </div>
                    @error('image_logo') <p class="text-xs text-red-500 font-bold mt-4">{{ $message }}</p> @enderror
                    <p class="text-xs text-slate-400 mt-6 leading-relaxed italic px-4">Disarankan rasio 1:1 (Square) dengan format PNG/JPG.</p>
                </div>

                {{-- ACTION CARD --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xl p-6 text-center space-y-5">
                    <div class="bg-amber-50 p-4 rounded-xl border border-amber-100">
                        <p class="text-sm text-amber-700 font-medium leading-relaxed">Pastikan seluruh data sudah diperiksa kembali sebelum disimpan ke sistem.</p>
                    </div>
                    
                    <button type="submit" 
                        class="w-full py-4 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-amber-900/10 flex items-center justify-center gap-3 disabled:opacity-50 active:scale-[0.98]"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="updateVilla">SIMPAN PERUBAHAN</span>
                        <span wire:loading wire:target="updateVilla" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> MEMPROSES...
                        </span>
                    </button>
                    
                    <button type="button" onclick="history.back()" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition py-2 block w-full">
                        Batalkan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>