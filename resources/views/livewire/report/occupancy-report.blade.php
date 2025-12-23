<div class="space-y-6">
    {{-- HEADER & TOGGLE --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Laporan Okupansi</h1>
            <p class="text-sm text-slate-500">Persentase hunian villa</p> <span class="text-amber-600">{{ $this->activeVillaName }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Toggle Bulanan/Tahunan --}}
            <div class="inline-flex bg-slate-100 p-1 rounded-xl">
                <button wire:click="$set('viewType', 'monthly')" 
                    class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $viewType == 'monthly' ? 'bg-white shadow text-amber-600' : 'text-slate-500' }}">
                    BULANAN
                </button>
                <button wire:click="$set('viewType', 'yearly')" 
                    class="px-4 py-2 rounded-lg text-xs font-bold transition {{ $viewType == 'yearly' ? 'bg-white shadow text-amber-600' : 'text-slate-500' }}">
                    TAHUNAN
                </button>
            </div>

            {{-- Filter --}}
            <div class="flex gap-2">
                @if($viewType == 'monthly')
                <select wire:model.live="filterBulan" class="text-sm border-slate-200 rounded-lg focus:ring-amber-500">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ sprintf('%02d', $i) }}">{{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
                @endif
                <select wire:model.live="filterTahun" class="text-sm border-slate-200 rounded-lg focus:ring-amber-500">
                    @foreach($listTahun as $thn)
                        <option value="{{ $thn }}">{{ $thn }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- KARTU STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Ringkasan Persentase --}}
        <div class="bg-gradient-to-br from-primary to-secondary p-8 rounded-3xl shadow-xl  relative overflow-hidden text-white">
            <div class="relative z-10">
                <p class="text-sm font-bold uppercase tracking-widest opacity-80">Okupansi {{ $viewType == 'monthly' ? 'Bulan Ini' : 'Tahun Ini' }}</p>
                <h2 class="text-6xl font-black mt-2">{{ $occupancyRate }}%</h2>
                <div class="mt-6 flex items-center gap-2">
                    <div class="flex-1 bg-white/20 h-3 rounded-full overflow-hidden">
                        <div class="bg-white h-full transition-all duration-1000" style="width: {{ $occupancyRate }}%"></div>
                    </div>
                </div>
            </div>
            {{-- Icon Background --}}
            <i class="fa-solid fa-hotel absolute -bottom-4 -right-4 text-9xl opacity-10 rotate-12"></i>
        </div>

        {{-- Detail Angka --}}
        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-center">
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-slate-50 pb-4">
                    <span class="text-slate-500 font-medium">Malam Terisi</span>
                    <span class="text-2xl font-bold text-slate-800">{{ $totalNightsSold }} <small class="text-xs text-slate-400">Malam</small></span>
                </div>
                <div class="flex justify-between items-center border-b border-slate-50 pb-4">
                    <span class="text-slate-500 font-medium">Kapasitas Maksimal</span>
                    <span class="text-2xl font-bold text-slate-800">{{ $totalDaysAvailable }} <small class="text-xs text-slate-400">Hari</small></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500 font-medium">Status</span>
                    @if($occupancyRate >= 70)
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-black uppercase">High Demand</span>
                    @elseif($occupancyRate >= 40)
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-black uppercase">Stable</span>
                    @else
                        <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-black uppercase">Low Occupancy</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- INFO BOX --}}
    <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-start gap-3 text-blue-800 shadow-sm">
        <i class="fa-solid fa-circle-info mt-1"></i>
        <div class="text-xs leading-relaxed italic">
            <strong>Catatan:</strong> Perhitungan ini diambil dari data pendapatan dengan kategori yang memiliki <b>Check-in</b> dan <b>Check-out</b>. Sistem secara otomatis menyesuaikan jumlah hari dalam bulan (misal: Februari 28/29 hari) atau tahun (365/366 hari).
        </div>
    </div>
</div>