<div>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">
                    Tambah <span class="text-amber-600">Villa Baru</span>
                </h1>
                <p class="text-sm text-slate-500 font-medium mt-1">Daatarkan properti baru ke dalam sistem manajemen.</p>
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

        <form wire:submit.prevent="saveVilla" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- KOLOM KIRI: FORM DATA UTAMA --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-700">Detail Informasi Villa</h2>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        {{-- Nama Villa --}}
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500 tracking-wider ml-1">Nama Properti</label>
                            <input type="text" wire:model="nama_villa" 
                                class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-3 px-4 shadow-sm"
                                placeholder="Masukkan nama villa...">
                            @error('nama_villa') <span class="text-xs text-red-500 font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500 tracking-wider ml-1">Alamat Lengkap</label>
                            <textarea wire:model="alamat_villa" rows="3" 
                                class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-3 px-4 shadow-sm"
                                placeholder="Tulis alamat lengkap lokasi villa..."></textarea>
                            @error('alamat_villa') <span class="text-xs text-red-500 font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Konfigurasi & Kamar --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-5 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase text-slate-500 text-center block">Fee Manaj. (%)</label>
                                <div class="relative">
                                    <input type="number" wire:model="fee_manajemen" class="w-full text-center rounded-xl border-slate-200 text-sm py-2.5 pr-8 font-bold">
                                    <span class="absolute right-3 top-2.5 text-slate-400 text-xs">%</span>
                                </div>
                                @error('fee_manajemen') <p class="text-[10px] text-red-500 font-bold text-center">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase text-slate-500 text-center block">Service Kar. (%)</label>
                                <div class="relative">
                                    <input type="number" wire:model="service_karyawan" class="w-full text-center rounded-xl border-slate-200 text-sm py-2.5 pr-8 font-bold text-emerald-600">
                                    <span class="absolute right-3 top-2.5 text-slate-400 text-xs">%</span>
                                </div>
                                @error('service_karyawan') <p class="text-[10px] text-red-500 font-bold text-center">{{ $message }}</p> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black uppercase text-slate-500 text-center block">Total Kamar</label>
                                <div class="relative">
                                    <input type="number" wire:model="jumlah_kamar" class="w-full text-center rounded-xl border-slate-200 text-sm py-2.5 pr-8 font-bold text-amber-600">
                                    <i class="fas fa-bed absolute right-3 top-3 text-slate-300 text-[10px]"></i>
                                </div>
                                @error('jumlah_kamar') <p class="text-[10px] text-red-500 font-bold text-center">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GALLERY UPLOAD --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-slate-700">Gallery Villa</h2>
                        <input type="file" wire:model="image_gallery" multiple id="gal" class="hidden">
                        <label for="gal" class="cursor-pointer text-sm font-bold text-amber-600 hover:text-amber-700 transition flex items-center gap-2">
                            <i class="fas fa-cloud-upload-alt"></i> Upload Foto
                        </label>
                    </div>
                    <div class="p-6">
                        @if ($image_gallery)
                            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-4 mb-4">
                                @foreach ($image_gallery as $img)
                                    <div class="relative aspect-square">
                                        <img src="{{ $img->temporaryUrl() }}" class="w-full h-full object-cover rounded-xl border-2 border-amber-100 shadow-sm">
                                        <div class="absolute -top-1 -right-1 bg-amber-500 text-white p-1 rounded-full shadow-md">
                                            <i class="fas fa-check text-[8px]"></i>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <label for="gal" class="flex flex-col items-center justify-center py-10 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:bg-slate-50 transition">
                                <i class="fas fa-images text-slate-300 text-4xl mb-3"></i>
                                <p class="text-sm text-slate-500 font-medium">Klik untuk pilih beberapa foto villa</p>
                            </label>
                        @endif
                        @error('image_gallery.*') <p class="text-xs text-red-500 font-bold mt-2 text-center">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: LOGO & ACTION --}}
            <div class="space-y-6">
                {{-- LOGO CARD --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-8 text-center">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-6">Logo Properti</h2>
                    
                    <div class="relative inline-block mx-auto">
                        @if ($image_logo)
                            <img src="{{ $image_logo->temporaryUrl() }}" class="w-40 h-40 object-cover rounded-full border-4 border-amber-400 shadow-xl animate-in zoom-in-95">
                        @else
                            <div class="w-40 h-40 rounded-full bg-slate-50 border-4 border-dashed border-slate-200 flex items-center justify-center group hover:bg-slate-100 transition">
                                <i class="fas fa-image text-slate-200 text-4xl group-hover:text-slate-300"></i>
                            </div>
                        @endif
                        
                        <label class="absolute bottom-2 right-2 h-11 w-11 bg-slate-800 text-white rounded-full flex items-center justify-center border-4 border-white cursor-pointer hover:bg-amber-600 transition shadow-lg">
                            <i class="fas fa-camera text-sm"></i>
                            <input type="file" wire:model="image_logo" class="hidden">
                        </label>
                    </div>
                    @error('image_logo') <p class="text-xs text-red-500 font-bold mt-4">{{ $message }}</p> @enderror
                    <p class="text-xs text-slate-400 mt-6 leading-relaxed italic px-4">Upload logo resmi villa (Format Square).</p>
                </div>

                {{-- ACTION CARD --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xl p-6 text-center space-y-5">
                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <p class="text-xs text-blue-700 font-medium leading-relaxed italic text-left">
                            <i class="fas fa-info-circle mr-1 text-blue-400"></i> Data ini akan menjadi dasar perhitungan biaya di sistem.
                        </p>
                    </div>
                    
                    <button type="submit" 
                        class="w-full py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-amber-900/10 flex items-center justify-center gap-3 active:scale-[0.98] disabled:opacity-50"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveVilla">DAFTARKAN VILLA</span>
                        <span wire:loading wire:target="saveVilla" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> MEMPROSES...
                        </span>
                    </button>
                    
                    <button type="button" onclick="window.location='{{ route('master.kelola.villa') }}'" 
                            class="text-sm font-bold text-slate-500 hover:text-red-500 transition py-2 block w-full">
                        Batalkan Pendaftaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>