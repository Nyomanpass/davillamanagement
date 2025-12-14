{{-- resources/views/livewire/master/pendapatan.blade.php --}}
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                Pendapatan:
                <span class="text-amber-600">{{ $this->activeVillaName }}</span>
            </h1>
            <p class="text-sm text-slate-500 hidden md:block">
                Kelola dan input data pendapatan villa aktif
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

    @if (session()->has('error'))
        <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show"
            class="p-4 rounded-lg bg-slate-100 border border-slate-200 text-slate-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- FORM INPUT --}}
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-8">
        <h2 class="text-xl font-semibold text-slate-800">
            Tambah Data Pendapatan
        </h2>

        <form wire:submit.prevent="savePendapatan" class="space-y-6">

            {{-- Jenis Pendapatan --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Jenis Pendapatan
                </label>
                <select wire:model="jenisPendapatan"
                    class="w-full px-4 py-3 rounded-md
                           border border-slate-300 bg-white
                           focus:outline-none focus:ring-1 focus:ring-amber-500
                           focus:border-amber-500">
                    @foreach($this->listJenisPendapatan as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @error('jenisPendapatan')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nominal & Tanggal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Nominal
                    </label>
                    <div class="relative">
                        <input type="number" wire:model.live="nominal" min="1" placeholder="0"
                            class="w-full px-4 py-3 pr-12 rounded-md
                                   border border-slate-300 bg-white
                                   focus:outline-none focus:ring-1 focus:ring-amber-500
                                   focus:border-amber-500">
                        <span class="absolute inset-y-0 right-3 flex items-center text-slate-500 text-sm">
                            Rp
                        </span>
                    </div>
                    @error('nominal')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">
                        Tanggal
                    </label>
                    <input type="date" wire:model="tanggal"
                        class="w-full px-4 py-3 rounded-md
                               border border-slate-300 bg-white
                               focus:outline-none focus:ring-1 focus:ring-amber-500
                               focus:border-amber-500">
                    @error('tanggal')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Metode Pembayaran --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">
                    Metode Pembayaran
                </label>
                <select wire:model="metodePembayaran"
                    class="w-full px-4 py-3 rounded-md
                           border border-slate-300 bg-white
                           focus:outline-none focus:ring-1 focus:ring-amber-500
                           focus:border-amber-500">
                    @foreach($this->listMetodePembayaran as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
                @error('metodePembayaran')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Button --}}
            <div class="text-right pt-2">
                <button type="submit"
                    class="px-8 py-3 rounded-md
                           bg-primary text-white
                           hover:bg-slate-900
                           focus:ring-2 focus:ring-amber-500"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove>Simpan Pendapatan</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>

        </form>
    </div>

    {{-- RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        @foreach ([
            ['Pendapatan Bulan Ini', $this->ringkasanBulanIni, 'fa-arrow-up'],
            ['Pendapatan Hari Ini', $this->ringkasanHariIni, 'fa-calendar-day'],
            ['Total Hasil Filter', $this->ringkasanTotalFilter, 'fa-filter'],
        ] as [$label, $value, $icon])
        <div class="bg-white border border-slate-200 p-4 rounded-xl shadow-sm flex justify-between">
            <div>
                <p class="text-sm text-slate-500">{{ $label }}</p>
                <p class="text-xl font-bold text-slate-800">
                    Rp {{ number_format($value,0,',','.') }}
                </p>
            </div>
            <i class="fas {{ $icon }} text-2xl text-slate-300"></i>
        </div>
        @endforeach

        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 shadow-sm h-28 flex flex-col justify-between">
            <div>
                <p class="text-sm opacity-80">All Time</p>
                <p class="text-xl font-bold">
                    Rp {{ number_format($this->ringkasanAllTime,0,',','.') }}
                </p>
            </div>
            <i class="fas fa-chart-line text-2xl opacity-40"></i>
        </div>
    </div>

    {{-- FILTER + EXPORT --}}
<div class="bg-white p-4 rounded-xl shadow-sm mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

        {{-- Bulan --}}
        <div>
            <label class="text-sm text-slate-600">Bulan</label>
            <select wire:model.live="filterBulan"
                class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
                <option value="">Semua</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                @endfor
            </select>
        </div>

        {{-- Tahun --}}
        <div>
            <label class="text-sm text-slate-600">Tahun</label>
            <select wire:model.live="filterTahun"
                class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
                <option value="">Semua</option>
                @foreach ($listTahun as $tahun)
                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                @endforeach
            </select>
        </div>

        {{-- Dari Tanggal --}}
        <div>
            <label class="text-sm text-slate-600">Dari</label>
            <input type="date" wire:model.live="filterStartDate"
                class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
        </div>

        {{-- Sampai Tanggal --}}
        <div>
            <label class="text-sm text-slate-600">Sampai</label>
            <input type="date" wire:model.live="filterEndDate"
                class="w-full px-3 py-2 rounded-md border border-slate-300 focus:ring-1 focus:ring-amber-500">
        </div>
    </div>

    <div class="flex justify-between items-center mt-3">
        {{-- Reset Filter --}}
        <button wire:click="$reset(['filterBulan','filterTahun','filterStartDate','filterEndDate'])"
            class="text-sm text-amber-600 hover:underline">
            Reset Filter
        </button>

        {{-- Export Buttons --}}
        <div class="space-x-2">
            <button wire:click="exportExcel"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                Export Excel
            </button>
            <button wire:click="exportPdf"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                Export PDF
            </button>
        </div>
    </div>
</div>


    {{-- TABEL --}}
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-semibold text-slate-800 mb-4">
            Daftar Pendapatan
        </h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs text-slate-500">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs text-slate-500">Villa</th>
                        <th class="px-6 py-3 text-left text-xs text-slate-500">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs text-slate-500">Nominal</th>
                        <th class="px-6 py-3 text-left text-xs text-slate-500">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($dataPendapatan as $item)
                        <tr>
                            <td class="px-6 py-4 text-sm">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $item->villa->nama_villa ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $this->listJenisPendapatan[$item->jenis_pendapatan] ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-green-700">
                                Rp {{ number_format($item->nominal,0,',','.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $this->listMetodePembayaran[$item->metode_pembayaran] ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-slate-500">
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $dataPendapatan->links() }}
        </div>
    </div>

</div>
