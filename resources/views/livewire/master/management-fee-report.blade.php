<div class="space-y-6">

    {{-- NOTIFIKASI --}}
    @if (session()->has('success'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 shadow-sm">
            <i class="fas fa-check-circle text-lg"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @elseif (session()->has('error'))
        <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-sm">
            <i class="fas fa-times-circle text-lg"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 flex items-center gap-3">
                Laporan Fee Manajemen
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Ringkasan dan detail fee manajemen villa
            </p>
        </div>

        <button wire:click="exportReport"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg
                   bg-emerald-600 text-white font-semibold text-sm
                   hover:bg-emerald-700 transition shadow-sm">
            <i class="fas fa-file-excel"></i>
            Export Excel
        </button>
    </div>

    {{-- CARD UTAMA --}}
    <div class="p-6 rounded-2xl border border-slate-200 shadow-sm space-y-8">

        {{-- FILTER --}}
      <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

        {{-- TIPE LAPORAN --}}
        <div>
            <label class="text-sm font-semibold text-slate-600 mb-1 block">
                Tipe Laporan
            </label>
            <select wire:model.live="filterMode"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                       focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
            </select>
        </div>

        {{-- BULAN --}}
        @if($filterMode === 'monthly')
        <div>
            <label class="text-sm font-semibold text-slate-600 mb-1 block">
                Bulan
            </label>
            <select wire:model.live="selectedMonth"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                       focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                @foreach($months as $key => $month)
                    <option value="{{ $key }}">{{ $month }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- TAHUN --}}
        <div>
            <label class="text-sm font-semibold text-slate-600 mb-1 block">
                Tahun
            </label>
            <select wire:model.live="selectedYear"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                       focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>

        {{-- VILLA --}}
        <div>
            <label class="text-sm font-semibold text-slate-600 mb-1 block">
                Villa
            </label>
            <select wire:model.live="filterVilla"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm
                       focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                <option value="">Semua Villa</option>
                @foreach($listVillas as $v)
                    <option value="{{ $v->id }}">{{ $v->nama_villa }}</option>
                @endforeach
            </select>
        </div>

    </div>
</div>

        {{-- SUMMARY --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
               <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                <div class="flex items-center gap-3">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                    <span class="text-sm font-semibold text-blue-700 uppercase">
                        Total Sisa Kotor
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-blue-900 mt-2">
                    Rp {{ number_format($totalNetRevenueGlobal, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                <div class="flex items-center gap-3">
                    <i class="fas fa-coins text-amber-600 text-xl"></i>
                    <span class="text-sm font-semibold text-amber-700 uppercase">
                        Fee Manajemen
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-amber-900 mt-2">
                    Rp {{ number_format($totalFeeManagementGlobal, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-5">
                <div class="flex items-center gap-3">
                    <i class="fas fa-percent text-emerald-600 text-xl"></i>
                    <span class="text-sm font-semibold text-emerald-700 uppercase">
                        Rata-rata Fee
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-emerald-900 mt-2">
                    {{ $averageFeePercent }}%
                </div>
            </div>
        </div>

        {{-- JUDUL TABEL --}}
       
        {{-- TABEL --}}
        @if (count($reports) > 0)
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="min-w-full">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-600 uppercase">Villa</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-600 uppercase">Pendapatan</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-slate-600 uppercase">Fee %</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-amber-700 uppercase">Fee (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach ($reports as $report)
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $report['name'] }}</td>
                        <td class="px-6 py-4 text-right">Rp {{ number_format($report['sisa_kotor'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center font-semibold text-amber-600">{{ $report['fee_percent'] }}%</td>
                        <td class="px-6 py-4 text-right font-extrabold text-amber-800">
                            Rp {{ number_format($report['fee_amount'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-slate-50 border-t-2 border-amber-600">
                    <td class="px-6 py-4 text-left font-extrabold text-slate-800 uppercase">TOTAL</td>
                    <td class="px-6 py-4 text-right font-extrabold text-slate-900">
                        Rp {{ number_format($totalNetRevenueGlobal, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-center font-extrabold text-amber-600">-</td>
                    <td class="px-6 py-4 text-right font-extrabold text-amber-800">
                        Rp {{ number_format($totalFeeManagementGlobal, 0, ',', '.') }}
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        {{-- GRAFIK FEE MANAJEMEN --}}
<div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
    <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
        <i class="fas fa-chart-pie text-amber-500"></i>
        Distribusi Fee Manajemen per Villa
    </h3>

    <div class="relative h-[350px]">
        <canvas id="feePieChart"></canvas>
    </div>
</div>

        @else
            <div class="p-8 text-center text-slate-500 border-2 border-dashed rounded-xl">
                <i class="fas fa-database text-4xl mb-3"></i>
                <p class="font-semibold">Tidak ada data</p>
            </div>
        @endif
    </div>
</div>


<script>
document.addEventListener('livewire:initialized', () => {
    let chart;

    Livewire.on('report-loaded', ({ reports }) => {

        if (!reports || reports.length === 0) return;

        const labels = reports.map(r => r.name);
        const values = reports.map(r => r.fee_amount);

        const ctx = document.getElementById('feePieChart');
        if (!ctx) return;

        if (chart) {
            chart.destroy();
        }

        // Warna dasar
        const baseColors = labels.map((_, i) => {
            const hue = (i * 360 / labels.length);
            return `hsl(${hue}, 65%, 60%)`;
        });

        // Warna hover (lebih gelap)
        const hoverColors = labels.map((_, i) => {
            const hue = (i * 360 / labels.length);
            return `hsl(${hue}, 65%, 50%)`; // 50% lebih gelap dari 60%
        });

        chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: baseColors,
                    hoverBackgroundColor: hoverColors,
                    borderColor: '#fff',
                    borderWidth: 2
                    // hoverOffset dihapus supaya tidak tonjol
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) =>
                                ctx.label + ': Rp ' +
                                ctx.raw.toLocaleString('id-ID')
                        }
                    }
                }
            }
        });
    });
});
</script>

