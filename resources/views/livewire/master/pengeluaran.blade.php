@php
    $canCreate = auth()->user()->hasPermissionTo('pengeluaran', 'create');
    $canUpdate = auth()->user()->hasPermissionTo('pengeluaran', 'update');
    $canDelete = auth()->user()->hasPermissionTo('pengeluaran', 'delete');
@endphp

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                Pengeluaran:
                <span class="text-amber-600">{{ $this->activeVillaName }}</span>
            </h1>
            <p class="text-sm text-slate-500 hidden md:block">
                Kelola detail belanja, biaya operasional, dan maintenance villa
            </p>
        </div>
    </div>

    {{-- NOTIFIKASI --}}
    @if (session()->has('success'))
        <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show"
            class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- FORM INPUT --}}
    @if($canCreate || $isEditMode)
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
        <h2 class="text-xl font-semibold text-slate-800">{{ $isEditMode ? 'Edit Data Pengeluaran' : 'Tambah Data Pengeluaran' }}</h2>

        <form wire:submit.prevent="savePengeluaran" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Kategori (Dinamis dari DB) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
                    <select wire:model="category_id" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Beban Biaya</label>
                    <select wire:model="jenis_beban" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500 text-sm">
                        <option value="">Pilih Jenis Beban</option>
                        <option value="operasional">Operasional (Biaya Rutin)</option>
                        <option value="non_operasional">Non-Operasional (Pajak/Renov)</option>
                    </select>
                    @error('jenis_beban') <p class="text-xs text-red-500 mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                {{-- Nama Pengeluaran --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Item / Keperluan</label>
                    <input type="text" wire:model="nama_pengeluaran" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
                    @error('nama_pengeluaran') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Qty & Satuan --}}
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Qty</label>
                        <input type="number" step="0.1" wire:model.live="qty" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
                        @error('qty') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
                        <input type="text" wire:model="satuan" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500" placeholder="Pcs/Kg/Lot">
                    </div>
                </div>

                {{-- Harga Satuan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Harga Satuan (Rp)</label>
                    <input type="number" wire:model.live="harga_satuan" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
                    @error('harga_satuan') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Total Nominal (Read Only) --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Total Nominal</label>
                    <input type="number" wire:model="nominal" class="w-full px-4 py-2 rounded-md border border-slate-300 bg-slate-50 font-bold text-red-600" readonly>
                    <p class="text-[10px] text-slate-500 mt-1">*Otomatis: Qty x Harga Satuan</p>
                </div>

                {{-- Tanggal --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                    <input type="date" wire:model="tanggal" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
                    @error('tanggal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Metode Pembayaran --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Metode Bayar</label>
                    <select wire:model="metode_pembayaran" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
                        @foreach($listMetodePembayaran as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan Tambahan</label>
                    <input type="text" wire:model="keterangan" class="w-full px-4 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500" placeholder="Opsional">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                @if($isEditMode)
                    <button type="button" wire:click="resetForm" class="px-6 py-2 rounded-md bg-slate-100 text-slate-600 hover:bg-slate-200">Batal</button>
                @endif
                <button type="submit" class="px-8 py-2 rounded-md bg-slate-900 text-white hover:bg-amber-600 transition" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ $isEditMode ? 'Simpan Perubahan' : 'Simpan Pengeluaran' }}</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
    @foreach ([
        ['Bulan Ini', $this->ringkasanBulanIni, 'bg-white text-slate-800', 'fa-calendar-minus', 'text-red-500'],
        ['Hari Ini', $this->ringkasanHariIni, 'bg-white text-slate-800', 'fa-file-invoice', 'text-pink-500'],
        ['Hasil Filter', $this->ringkasanTotalFilter, 'bg-white text-slate-800', 'fa-filter-circle-dollar', 'text-amber-500'],
    ] as [$label, $value, $class, $icon, $iconColor])
    
    <div class="{{ $class }} p-6 rounded-2xl border border-slate-200 shadow-sm relative overflow-hidden group transition-all duration-300 hover:shadow-md">
        <div class="flex justify-between items-center relative z-10">
            <div>
                <p class="text-xs uppercase font-black tracking-widest opacity-60">{{ $label }}</p>
                <p class="text-xl font-extrabold mt-1 tracking-tight text-red-600">
                    Rp {{ number_format($value, 0, ',', '.') }}
                </p>
            </div>
            
            {{-- Ikon Diperbesar dengan Kontainer --}}
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-red-50 transition-transform group-hover:scale-110">
                <i class="fa-solid {{ $icon }} {{ $iconColor }} text-2xl"></i>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Kartu All Time (Gaya Dark) --}}
    <div class="bg-slate-800 p-6 rounded-2xl shadow-lg relative overflow-hidden group transition-all duration-300 hover:shadow-xl">
        <div class="flex justify-between items-center relative z-10">
            <div>
                <p class="text-xs uppercase font-black tracking-widest text-slate-400">Total All Time</p>
                <p class="text-xl font-extrabold mt-1 tracking-tight text-white">
                    Rp {{ number_format($this->ringkasanAllTime, 0, ',', '.') }}
                </p>
            </div>
            
            <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-white/10 transition-transform group-hover:scale-110">
                <i class="fa-solid fa-chart-line text-white text-2xl"></i>
            </div>
        </div>
        {{-- Hiasan Ikon Besar Transparan --}}
        <i class="fa-solid fa-chart-line absolute -right-2 -bottom-2 text-5xl opacity-10 group-hover:scale-110 transition-transform duration-500"></i>
    </div>
</div>

    {{-- FILTER & EXPORT --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase">Bulan</label>
                <select wire:model.live="filterBulan" class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 focus:ring-amber-500">
                    <option value="">Semua</option>
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase">Tahun</label>
                <select wire:model.live="filterTahun" class="w-full px-3 py-2 text-sm rounded-md border border-slate-300 focus:ring-amber-500">
                    @foreach ($listTahun as $tahun)
                        <option value="{{ $tahun }}">{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase">Dari</label>
                <input type="date" wire:model.live="filterStartDate" class="w-full px-3 py-2 text-sm rounded-md border border-slate-300">
            </div>

            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase">Sampai</label>
                <input type="date" wire:model.live="filterEndDate" class="w-full px-3 py-2 text-sm rounded-md border border-slate-300">
            </div>
        </div>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    {{-- Filter Kategori --}}
    <div class="relative group mt-6">
        <label class="flex items-center gap-1.5 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">
        
            Kategori Item
        </label>
        <div class="relative">
            <select wire:model.live="filterCategory" 
                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:bg-white transition-all duration-200 appearance-none text-slate-700 font-medium">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            {{-- Custom Icon Left --}}
            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-amber-500 transition-colors">
                <i class="fa-solid fa-layer-group text-xs"></i>
            </div>
            {{-- Custom Arrow Right --}}
            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                <i class="fa-solid fa-chevron-down text-[10px]"></i>
            </div>
        </div>
    </div>

    {{-- Filter Jenis Beban --}}
    <div class="relative group mt-6">
        <label class="flex items-center gap-1.5 text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">
          
            Tipe Operasional
        </label>
        <div class="relative">
            <select wire:model.live="filterJenisBeban" 
                class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-amber-500/20 focus:bg-white transition-all duration-200 appearance-none text-slate-700 font-medium">
                <option value="">Semua Tipe</option>
                <option value="operasional">Operasional</option>
                <option value="non_operasional">Non-Operasional</option>
            </select>
            {{-- Custom Icon Left --}}
            <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-amber-500 transition-colors">
                <i class="fa-solid fa-sliders text-xs"></i>
            </div>
            {{-- Custom Arrow Right --}}
            <div class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                <i class="fa-solid fa-chevron-down text-[10px]"></i>
            </div>
        </div>
    </div>
</div>

        <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-100">
            <button wire:click="resetFilter" class="text-xs font-bold text-amber-600 hover:text-amber-700 uppercase tracking-widest">
                <i class="fas fa-sync-alt"></i> Reset Filter
            </button>

            <div class="flex space-x-2">
                <button wire:click="exportExcel" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-md hover:bg-emerald-700 uppercase tracking-tighter transition">
                    <i class="fas fa-file-excel mr-1"></i>Export Excel
                </button>
                <button wire:click="exportPdf" class="px-4 py-2 bg-rose-600 text-white text-xs font-bold rounded-md hover:bg-rose-700 uppercase tracking-tighter transition">
                    <i class="fas fa-file-pdf mr-1"></i>Export PDF
                </button>
            </div>
        </div>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-slate-800">Daftar Pengeluaran</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tanggal & Villa</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Item / Kategori</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-500 uppercase tracking-wider">Qty / Harga Satuan</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-500 uppercase tracking-wider">Total Nominal</th>
                       @if($canUpdate || $canDelete)
                            <th class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($dataPengeluaran as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <span class="font-bold text-slate-700">{{ $item->tanggal->format('d/m/Y') }}</span>
                                <div class="text-[10px] text-slate-400 font-medium">{{ $item->villa->nama_villa ?? '-' }}</div>
                            </td>
                           <td class="px-6 py-4">
    {{-- Nama Pengeluaran --}}
                                <div class="text-sm font-bold text-slate-800">{{ $item->nama_pengeluaran }}</div>
                                
                                <div class="flex items-center gap-2 mt-1.5">
                                    {{-- Badge Kategori --}}
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[9px] font-bold text-slate-500 uppercase border border-slate-200">
                                        {{ $item->category->name ?? '-' }}
                                    </span>

                                    {{-- Badge Jenis Beban (Operasional vs Non-Operasional) --}}
                                    @if($item->jenis_beban === 'operasional')
                                        <span class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-emerald-50 text-[9px] font-black text-emerald-600 uppercase border border-emerald-100">
                                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                            Operasional
                                        </span>
                                    @else
                                        <span class="flex items-center gap-1 px-2 py-0.5 rounded-md bg-rose-50 text-[9px] font-black text-rose-600 uppercase border border-rose-100">
                                            <span class="w-1 h-1 rounded-full bg-rose-500"></span>
                                            Non-Operasional
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-slate-600 font-medium">{{ (float)$item->qty }} {{ $item->satuan }}</div>
                                <div class="text-[10px] text-slate-400">@ Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <span class="text-sm font-black text-red-600 uppercase">Rp {{ number_format($item->nominal,0,',','.') }}</span>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">{{ $item->metode_pembayaran }}</div>
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
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-500 italic">
                                Belum ada data pengeluaran yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

       {{-- FOOTER TABEL: PAGINATION & RINGKASAN --}}
@if($dataPengeluaran->count() > 0)
    <div class="flex flex-col md:flex-row justify-between items-center mt-6 gap-4">
        {{-- Pagination di Kiri --}}
        <div class="flex-1">
            {{ $dataPengeluaran->links() }}
        </div>

        {{-- Ringkasan Total Pengeluaran Terfilter di Kanan --}}
        <div class="relative group">
            {{-- Glow effect tipis warna merah --}}
            <div class="absolute transition duration-1000"></div>
            
            <div class="relative flex items-center bg-white rounded-2xl p-1">
                {{-- Label --}}
                <div class="px-5 py-3 ">
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Total Pengeluaran</p>
                    <p class="text-xs font-bold text-slate-600 flex items-center gap-1">
                        <i class="fas fa-file-invoice-dollar opacity-50"></i>
                        {{ $dataPengeluaran->total() }} Item / Biaya
                    </p>
                </div>
                
                {{-- Nominal (Warna Rose untuk indikasi pengeluaran) --}}
                <div class="px-6 py-3 rounded-r-xl border-l border-slate-50">
                    <p class="text-2xl font-black text-slate-800 tracking-tight">
                        <span class="text-rose-600 text-sm mr-1">Rp</span>{{ number_format($this->ringkasanTotalFilter, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif
    </div>
</div>