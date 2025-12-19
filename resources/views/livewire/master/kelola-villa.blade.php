<div>
    <div class="space-y-6">
        {{-- NOTIFIKASI --}}
        @foreach (['success', 'error', 'info'] as $msg)
            @if (session()->has($msg))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                     class="p-4 rounded-xl font-medium shadow-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-2
                        {{ $msg === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : '' }}
                        {{ $msg === 'error' ? 'bg-red-50 text-red-700 border border-red-100' : '' }}
                        {{ $msg === 'info' ? 'bg-blue-50 text-blue-700 border border-blue-100' : '' }}">
                    <i class="fas {{ $msg === 'success' ? 'fa-check-circle' : ($msg === 'error' ? 'fa-times-circle' : 'fa-info-circle') }}"></i>
                    <span>{{ session($msg) }}</span>
                </div>
            @endif
        @endforeach

        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                    Kelola <span class="text-amber-600">Villa</span>
                </h1>
                <p class="text-sm text-slate-500">Daftar properti villa dan konfigurasi biaya manajemen.</p>
            </div>
            <a href="{{ route('master.create.villa') }}"
               class="inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition-all active:scale-95">
               <i class="fas fa-plus"></i>
               <span>Tambah Villa Baru</span>
            </a>
        </div>

        {{-- RINGKASAN STATISTIK --}}
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    @foreach ([
        ['label' => 'Total Villa', 'value' => $this->totalVilla, 'icon' => 'fa-home'],
        ['label' => 'Total Karyawan', 'value' => $this->totalKaryawan, 'icon' => 'fa-users'],
        ['label' => 'Akun Sistem', 'value' => $this->totalAkun, 'icon' => 'fa-user-shield']
    ] as $stat)
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between transition hover:shadow-md group">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">
                    {{ $stat['label'] }}
                </p>
                <p class="text-3xl font-bold text-slate-800">{{ $stat['value'] }}</p>
            </div>
            
            {{-- Kontainer Ikon --}}
            <div class="h-12 w-12 rounded-xl bg-amber-50 flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                {{-- Pastikan class fas/far tertulis di sini --}}
                <i class="fas {{ $stat['icon'] }} text-amber-600 text-xl"></i>
            </div>
        </div>
    @endforeach
</div>

        {{-- SEARCH BAR --}}
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="relative">
                <input type="text" wire:model.live="search" placeholder="Cari nama villa atau alamat..."
                       class="w-full py-3 pl-12 pr-4 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all text-sm">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <i class="fas fa-search text-slate-400"></i>
                </div>
            </div>
        </div>

        {{-- DAFTAR VILLA (GRID) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($villas as $villa)
                <div class="group bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-xl hover:border-amber-200 transition-all duration-300 overflow-hidden">
                    {{-- Thumbnail --}}
                    <div class="relative h-44 w-full overflow-hidden bg-slate-100">
                        @if ($villa->image_logo)
                            <img src="{{ Storage::url($villa->image_logo) }}" 
                                 alt="{{ $villa->nama_villa }}" 
                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110">
                        @else
                            <div class="h-full w-full flex flex-col items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200">
                                <i class="fas fa-image text-slate-300 text-3xl mb-2"></i>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No Logo</span>
                            </div>
                        @endif
                        <div class="absolute top-3 right-3">
                            <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-[10px] font-bold text-slate-700 shadow-sm border border-slate-100">
                                {{ $villa->jumlah_kamar }} Kamar
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-amber-600 transition-colors">{{ $villa->nama_villa }}</h3>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                            <i class="fas fa-map-marker-alt text-amber-500"></i>
                            <span class="truncate">{{ $villa->alamat_villa }}</span>
                        </p>

                        <div class="mt-4 pt-4 border-t border-slate-50 grid grid-cols-2 gap-2">
                            {{-- Baris 1: Detail & Edit --}}
                            <button wire:click="showVillaDetail({{ $villa->id }})"
                                    class="flex items-center justify-center gap-2 py-2 rounded-lg bg-slate-50 text-slate-700 font-bold text-xs hover:bg-slate-100 transition">
                                <i class="fas fa-eye text-blue-500"></i> Detail
                            </button>
                            <a href="{{ route('master.edit.villa', $villa->id) }}"
                               class="flex items-center justify-center gap-2 py-2 rounded-lg bg-slate-50 text-slate-700 font-bold text-xs hover:bg-amber-50 hover:text-amber-700 transition">
                                <i class="fas fa-edit text-amber-500"></i> Edit
                            </a>
                            {{-- Baris 2: Settings & Delete --}}
                            <a href="{{ route('master.villa.settings', $villa->id) }}"
                               class="flex items-center justify-center gap-2 py-2 rounded-lg bg-slate-800 text-white font-bold text-xs hover:bg-slate-700 transition">
                                <i class="fas fa-cog text-amber-400"></i> Settings
                            </a>
                            <button wire:click="deleteVilla({{ $villa->id }})"
                                    onclick="confirm('Hapus villa {{ $villa->nama_villa }}?') || event.stopImmediatePropagation()"
                                    class="flex items-center justify-center gap-2 py-2 rounded-lg bg-red-50 text-red-600 font-bold text-xs hover:bg-red-600 hover:text-white transition border border-red-100">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center">
                    <i class="fas fa-city text-slate-200 text-6xl mb-4"></i>
                    <p class="text-slate-400 font-medium italic">Tidak ada data villa yang ditemukan.</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINATION --}}
        @if($villas->hasPages())
            <div class="mt-8">
                {{ $villas->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL DETAIL --}}
    @if($isDetailModalOpen)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" wire:click="closeDetailModal"></div>
            
            <div class="relative bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-slate-200 animate-in fade-in zoom-in duration-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 uppercase tracking-tight">Detail Villa</h3>
                    <button wire:click="closeDetailModal" class="p-2 rounded-full hover:bg-slate-200 text-slate-400 transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="p-6 space-y-6 max-h-[80vh] overflow-y-auto">
                    {{-- Info Utama --}}
                    <div class="flex gap-4">
                        <div class="h-20 w-20 rounded-xl border bg-slate-50 flex-shrink-0">
                            @if ($selectedVilla?->image_logo)
                                <img src="{{ Storage::url($selectedVilla->image_logo) }}" class="h-full w-full object-cover rounded-xl">
                            @else
                                <div class="h-full w-full flex items-center justify-center text-slate-300"><i class="fas fa-image"></i></div>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-slate-800">{{ $selectedVilla?->nama_villa }}</h2>
                            <p class="text-sm text-slate-500 italic">{{ $selectedVilla?->alamat_villa }}</p>
                        </div>
                    </div>

                    {{-- Grid Info --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Fee Manajemen</p>
                            <p class="text-lg font-bold text-amber-600">{{ $selectedVilla?->fee_manajemen }}%</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Service Karyawan</p>
                            <p class="text-lg font-bold text-emerald-600">{{ $selectedVilla?->service_karyawan }}%</p>
                        </div>
                    </div>

                    {{-- Gallery --}}
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase mb-3 flex items-center gap-2">
                            <i class="fas fa-images text-amber-500"></i> Gallery Gambar
                        </h4>
                        @if(!empty($selectedVilla?->image_gallery))
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($selectedVilla->image_gallery as $path)
                                    <div class="h-24 rounded-lg overflow-hidden border border-slate-100 shadow-sm">
                                        <img src="{{ Storage::url($path) }}" class="h-full w-full object-cover hover:scale-110 transition duration-300">
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-8 bg-slate-50 rounded-xl border-2 border-dashed border-slate-100 text-center">
                                <p class="text-xs text-slate-400 italic font-medium">Belum ada foto gallery.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button wire:click="closeDetailModal" 
                            class="px-6 py-2 text-sm font-bold bg-slate-800 text-white rounded-xl hover:bg-slate-700 transition shadow-md">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
    <style>
    /* Styling khusus untuk scrollbar modal detail agar rapi */
    .overflow-y-auto::-webkit-scrollbar { width: 4px; }
    .overflow-y-auto::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
</div>

