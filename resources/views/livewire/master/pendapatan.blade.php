{{-- resources/views/livewire/master/pendapatan.blade.php --}}

@php
    $canCreate = auth()->user()->hasPermissionTo('pendapatan', 'create');
    $canUpdate = auth()->user()->hasPermissionTo('pendapatan', 'update');
    $canDelete = auth()->user()->hasPermissionTo('pendapatan', 'delete');
@endphp

<div class="space-y-6">

    {{-- HEADER --}}

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                Pendapatan:
                <span class="text-amber-600">{{ $this->activeVillaName }}</span>
            </h1>
            <p class="text-sm text-slate-500 hidden md:block">
                Kelola data pendapatan dengan kategori dinamis (Room & Item)
            </p>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session()->has('success'))
        <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show"
            class="p-4 rounded-lg bg-green-50 border border-green-200 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM INPUT --}}
    @if($canCreate || $isEditMode)
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
        
        <h2 class="text-xl font-semibold text-slate-800">
            {{ $isEditMode ? 'Edit Data Pendapatan' : 'Tambah Data Pendapatan' }}
        </h2>
      
        <form wire:submit.prevent="savePendapatan" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Pilih Kategori --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori Pendapatan</label>
                    <select wire:model.live="category_id"
                        class="w-full px-4 py-3 rounded-md border border-slate-300 bg-white focus:ring-amber-500 focus:border-amber-500">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Transaksi</label>
                    <input type="date" wire:model="tanggal"
                        class="w-full px-4 py-3 rounded-md border border-slate-300 focus:ring-amber-500 focus:border-amber-500">
                    @error('tanggal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- FORM DINAMIS BERDASARKAN KATEGORI --}}
            @if($category_id)
            <div class="p-5 rounded-xl border {{ $is_room ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200' }}">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-4">Detail {{ $is_room ? 'Booking Room' : 'Item/Layanan' }}</h3>
                
                @if($is_room)
                    {{-- Form Room --}}
                   <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="text-xs font-medium text-slate-600">Check In</label>
            <input type="date" wire:model.live="check_in" class="w-full px-3 py-2 border rounded-md">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600">Check Out</label>
            <input type="date" wire:model.live="check_out" class="w-full px-3 py-2 border rounded-md">
        </div>
        <div>
            <label class="text-xs font-medium text-slate-600">Total Malam</label>
        
                <input type="number" wire:model.live="nights" class="w-full px-3 py-2 border rounded-md">
            
        </div>
        <div>
           <label class="block text-sm font-bold text-slate-700 mb-1">Jenis Pendapatan</label>
            <select wire:model="jenis_pendapatan"
                class="w-full px-4 py-3 rounded-lg border @error('jenis_pendapatan') border-red-500 @else border-slate-300 @enderror bg-white focus:ring-amber-500 font-medium">
                <option value="">Pilih Jenis Pendapatan</option>
                <option value="operasional">Operasional</option>
                <option value="non_operasional">Non-Operasional</option>
            </select>
            @error('jenis_pendapatan') 
                <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p> 
            @enderror
        </div>
    </div>
                @else
                    {{-- Form Umum --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-xs font-medium text-slate-600">Nama Item</label>
                            <input type="text" wire:model="item_name" class="w-full px-3 py-2 border rounded-md">
                        </div>
                      <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Jenis Pendapatan</label>
                            <select wire:model="jenis_pendapatan"
                                class="w-full px-4 py-3 rounded-lg border @error('jenis_pendapatan') border-red-500 @else border-slate-300 @enderror bg-white focus:ring-amber-500 font-medium">
                                <option value="">Pilih Jenis Pendapatan</option>
                                <option value="operasional">Operasional</option>
                                <option value="non_operasional">Non-Operasional</option>
                            </select>
                            @error('jenis_pendapatan') 
                                <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p> 
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Qty</label>
                            <input type="number" wire:model.live="qty" class="w-full px-3 py-2 border rounded-md">
                        </div>
                        <div>
                            <label class="text-xs font-medium text-slate-600">Harga Satuan</label>
                            <input type="number" wire:model.live="price_per_item" class="w-full px-3 py-2 border rounded-md">
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nominal Total (Read Only) --}}
               {{-- Bagian Nominal Total --}}
                <div>
                    <label class="block text-sm font-bold text-amber-600 mb-1">
                        Total Nominal @if($is_room) (Isi Manual) @else (Otomatis) @endif
                    </label>
                    <div class="relative">
                        <input type="number" 
                            wire:model="nominal" 
                            {{-- Jika BUKAN room, maka READONLY (tidak bisa diketik) --}}
                            {{ !$is_room ? 'readonly' : '' }}
                            class="w-full px-4 py-3 border rounded-md font-bold text-xl 
                            {{ !$is_room ? 'bg-slate-100 text-slate-500' : 'bg-white text-slate-900 border-amber-500 focus:ring-amber-500' }}">
                        
                        <span class="absolute inset-y-0 right-3 flex items-center text-slate-500 font-bold">Rp</span>
                    </div>
                    @error('nominal') <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p> @enderror
                    
                    @if(!$is_room && $category_id)
                        <p class="text-[10px] text-slate-400 mt-1">* Nominal dihitung otomatis: Qty x Harga Satuan</p>
                    @endif
                </div>

                {{-- Metode Pembayaran --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Metode Pembayaran</label>
                    <select wire:model="metodePembayaran"
                        class="w-full px-4 py-3 rounded-md border border-slate-300 bg-white focus:ring-amber-500">
                        @foreach($this->listMetodePembayaran as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan (Opsional)</label>
                <textarea wire:model="keterangan" rows="2" class="w-full px-4 py-2 border rounded-md"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                @if($isEditMode)
                    <button type="button" wire:click="resetForm" class="px-6 py-3 rounded-md text-slate-600 hover:bg-slate-100">Batal</button>
                @endif
                <button type="submit" class="px-10 py-3 rounded-md bg-amber-600 text-white hover:bg-amber-700 font-bold transition shadow-lg">
                    {{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Transaksi' }}
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
    @foreach ([
        ['Bulan Ini', $this->ringkasanBulanIni, 'bg-white text-slate-800', 'fa-calendar-days', 'text-blue-500'],
        ['Hari Ini', $this->ringkasanHariIni, 'bg-white text-slate-800', 'fa-clock', 'text-emerald-500'],
        ['Hasil Filter', $this->ringkasanTotalFilter, 'bg-white text-slate-800', 'fa-filter', 'text-amber-500'],
        ['All Time', $this->ringkasanAllTime, 'bg-amber-600 text-white', 'fa-layer-group', 'text-white'],
    ] as [$label, $value, $class, $icon, $iconColor])
    
    <div class="{{ $class }} p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group transition-all duration-300 hover:shadow-md">
        <div class="flex justify-between items-center relative z-10">
            <div>
                <p class="text-xs uppercase font-black tracking-widest opacity-60">{{ $label }}</p>
                <p class="text-xl font-extrabold mt-1 tracking-tight">
                    Rp {{ number_format($value, 0, ',', '.') }}
                </p>
            </div>
            
            {{-- Ikon Diperbesar --}}
            <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $label == 'All Time' ? 'bg-white/20' : 'bg-slate-50' }} transition-transform group-hover:scale-110">
                <i class="fa-solid {{ $icon }} {{ $iconColor }} text-2xl"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

    {{-- FILTER DATA --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            {{-- Filter Bulan --}}
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400">Bulan</label>
                <select wire:model.live="filterBulan"
                    class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500 text-sm">
                    <option value="">Semua Bulan</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>

            {{-- Filter Tahun --}}
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400">Tahun</label>
                <select wire:model.live="filterTahun"
                    class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500 text-sm">
                    <option value="">Semua Tahun</option>
                    @foreach ($listTahun as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tanggal Mulai --}}
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400">Dari Tanggal</label>
                <input type="date" wire:model.live="filterStartDate"
                    class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500 text-sm">
            </div>

            {{-- Filter Tanggal Selesai --}}
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400">Sampai Tanggal</label>
                <input type="date" wire:model.live="filterEndDate"
                    class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500 text-sm">
            </div>
        </div>

       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Filter Kategori --}}
    <div>
        <label class="text-[10px] font-bold uppercase text-slate-400">Kategori</label>
        <select wire:model.live="filterCategory"
            class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500 text-sm">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Filter Jenis --}}
    <div>
         <label class="text-[10px] font-bold uppercase text-slate-400">Jenis Pendapatan</label>
        <select wire:model.live="filterJenisPendapatan" 
            class="w-full px-3 py-2 text-sm rounded-md bg-white border border-slate-300 focus:ring-1 focus:ring-amber-500 font-medium">
            <option value="">Semua Jenis</option>
            <option value="operasional">Operasional</option>
            <option value="non_operasional">Non-Operasional</option>
        </select>
    </div>
</div>

        <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-100">
            <button wire:click="resetFilter"
                class="text-xs font-bold text-amber-600 hover:text-amber-700 uppercase tracking-widest">
                <i class="fas fa-sync-alt"></i> Reset Filter
            </button>
            <div class="flex gap-2">
                <button wire:click="exportExcel" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-md hover:bg-emerald-700 uppercase tracking-tighter transition"> <i class="fas fa-file-excel mr-1"></i>Export Excel</button>
                <button wire:click="exportPdf" class="px-4 py-2 bg-red-600 text-white text-xs font-bold rounded-md hover:bg-red-700 uppercase tracking-tighter transition"><i class="fas fa-file-pdf mr-1"></i>Export PDF</button>
            </div>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-slate-800">Daftar Pendapatan</h2>
            
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kategori & Detail</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Harga Satuan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase text-right">Nominal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Metode</th>
                        @if($canUpdate || $canDelete)
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($dataPendapatan as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-sm font-medium">
                                {{ $item->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
    <div class="flex flex-col">
        {{-- Baris Atas: Nama Kategori & Badge Jenis --}}
        <div class="flex items-center gap-2 mb-1">
            <span class="font-bold text-slate-700">{{ $item->category->name ?? 'N/A' }}</span>
            
            {{-- Badge Jenis Pendapatan --}}
            
        </div>

        @if($item->jenis_pendapatan === 'operasional')
                <span class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-[9px] font-black text-emerald-600 uppercase border border-emerald-100">
                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                    Operasional
                </span>
            @elseif($item->jenis_pendapatan === 'non_operasional')
                <span class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-[9px] font-black text-rose-600 uppercase border border-rose-100">
                    <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                    Non-Operasional
                </span>
            @else
                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[9px] font-black text-slate-400 uppercase border border-slate-200">
                    N/A
                </span>
            @endif
        {{-- Baris Bawah: Detail Item/Room --}}
        <div class="text-xs text-slate-500 italic">
            @if(str_contains(strtolower($item->category->name ?? ''), 'room'))
                
                ({{ $item->check_in ? $item->check_in->format('d M') : '-' }} - {{ $item->check_out ? $item->check_out->format('d M') : '-' }})
            @else
                
                {{ $item->item_name ?? 'Tanpa Nama' }} ({{ $item->qty ?? 0 }}x)
            @endif
        </div>
    </div>
</td>
                           <td class="px-6 py-4 text-sm font-bold text-right">
                                @if(str_contains(strtolower($item->category->name ?? ''), 'room'))
                                    {{-- Jika kategori adalah Room, tampilkan Harga Per Malam --}}
                                    <span class="text-xs text-slate-400 block font-normal">Harga/Malam:</span>
                                  Rp {{ !empty($item->price_per_night) 
                                        ? number_format($item->price_per_night, 0, ',', '.') 
                                        : '-' 
                                    }}
                                @else
                                    {{-- Jika kategori lainnya, tampilkan Harga Per Item --}}
                                    <span class="text-xs text-slate-400 block font-normal">Harga/Item:</span>
                                    Rp {{ number_format($item->price_per_item, 0, ',', '.') }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-green-700 text-right">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm uppercase text-xs font-semibold text-slate-400">
                                {{ $item->metode_pembayaran }}
                            </td>
                            @if($canUpdate || $canDelete)
                             <td class="px-6 py-4 text-sm text-center">
                                <div class="flex justify-center gap-3">
                                    @if($canUpdate)
                                    <button wire:click="edit({{ $item->id }})" class="text-blue-600 hover:text-blue-800 font-bold">Edit</button>
                                    @endif
                                    @if($canDelete)
                                    <button wire:click="delete({{ $item->id }})" 
                                        onclick="confirm('Hapus data ini?') || event.stopImmediatePropagation()"
                                        class="text-red-600 hover:text-red-800 font-bold">Hapus</button>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400 italic">Belum ada data pendapatan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dataPendapatan->count() > 0)
            <div class="flex flex-col md:flex-row justify-between items-center mt-6 gap-4">
                {{-- Pagination di Kiri --}}
                <div class="flex-1">
                    {{ $dataPendapatan->links() }}
                </div>

                {{-- Ringkasan Total di Kanan --}}
                <div class="relative group">
                    <div class="absolute  transition duration-1000"></div>
                    <div class="relative flex items-center bg-white rounded-2xl p-1">
                        {{-- Label --}}
                        <div class="px-5 py-3 ">
                            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Total Terfilter</p>
                            <p class="text-xs font-bold text-slate-600 flex items-center gap-1">
                                <i class="fas fa-receipt opacity-50"></i>
                                {{ $dataPendapatan->total() }} Transaksi
                            </p>
                        </div>
                        
                        {{-- Nominal --}}
                        <div class="px-6 py-3 bg-slate-50/50 rounded-r-xl">
                            <p class="text-2xl text-black font-semibold text-slate-800 tracking-tight">
                                <span class="text-amber-600 text-sm mr-1">Rp</span>{{ number_format($this->ringkasanTotalFilter, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>