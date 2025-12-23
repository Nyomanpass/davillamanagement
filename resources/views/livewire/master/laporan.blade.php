<div class="space-y-8">

    {{-- HEADER --}}
    <div>
        <h1 class="text-3xl font-extrabold text-slate-800">
            Dashboard <span class="text-amber-600">Laporan</span>
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            Villa: <span class="font-semibold text-amber-600">{{ $this->activeVillaName }}</span>
        </p>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl shadow-md">

    {{-- HEADER --}}
        <div class="px-6 py-4 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800">
                    Filter Laporan
                </h2>
                <p class="text-sm text-slate-500">
                    Atur periode laporan keuangan
                </p>
            </div>

            {{-- TIPE LAPORAN --}}
            <div class="w-full sm:w-52">
                <select
                    wire:model.live="filterMode"
                    class="w-full rounded-xl border border-slate-300 bg-slate-50 py-2.5 px-3 text-sm font-medium
                        text-slate-700 shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition"
                >
                    <option value="monthly">Laporan Bulanan</option>
                    <option value="yearly">Laporan Tahunan</option>
                </select>
            </div>
        </div>

    {{-- BODY FILTER --}}
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                {{-- BULAN --}}
                @if ($filterMode === 'monthly')
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-2">
                            Pilih Bulan
                        </label>
                        <select
                            wire:model.live="selectedMonth"
                            class="w-full rounded-xl border border-slate-300 bg-white py-3 px-4 text-sm
                                text-slate-700 shadow-sm
                                focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition"
                        >
                            @foreach ($months as $key => $month)
                                <option value="{{ $key }}">{{ $month }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- TAHUN --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-2">
                        Pilih Tahun
                    </label>
                    <select
                        wire:model.live="selectedYear"
                        class="w-full rounded-xl border border-slate-300 bg-white py-3 px-4 text-sm
                            text-slate-700 shadow-sm
                            focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition"
                    >
                        @foreach ($years as $yearKey => $year)
                            <option value="{{ $yearKey }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>
    </div>

    


    {{-- KONTEN --}}
    @if (!empty($reportData))
   <div class="space-y-8">

       <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-700">
                Ringkasan Keuangan — {{ $reportData['periode'] }}
            </h2>

            <div class="flex gap-2">
                {{-- Tombol Export --}}
                <button wire:click="exportExcel" class="px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                    Export Excel
                </button>
                <button wire:click="exportPdf" class="px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 transition">
                    Export PDF
                </button>
            </div>
        </div>

        {{-- CARD RINGKASAN --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $card = 'p-4 rounded-xl bg-white border border-slate-200 shadow-sm h-28 flex flex-col justify-between';
                $label = 'text-sm text-slate-500 font-medium';
                $value = 'text-xl font-bold text-slate-800';
            @endphp

            <div class="{{ $card }}">
                <p class="{{ $label }}">Total Pendapatan</p>
                <p class="{{ $value }}">Rp {{ number_format($reportData['totalPendapatan'], 0, ',', '.') }}</p>
            </div>

            <div class="{{ $card }}">
                <p class="{{ $label }}">Total Pengeluaran</p>
                <p class="{{ $value }}">Rp {{ number_format($reportData['totalPengeluaran'], 0, ',', '.') }}</p>
            </div>

            <div class="{{ $card }}">
                <p class="{{ $label }}">Service Karyawan</p>
                <p class="text-xl font-bold text-rose-600">
                    - Rp {{ number_format($reportData['serviceKaryawanNominal'], 0, ',', '.') }}
                </p>
            </div>

            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 shadow-sm h-28 flex flex-col justify-between">
                <p class="text-sm text-amber-700 font-bold">PROFIT OWNER</p>
                <p class="text-xl font-extrabold text-amber-800">
                    Rp {{ number_format($reportData['pendapatanOwner'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- GRAFIK --}}
            <div x-data="financialChart()" @report-data-updated.window="initChart($event.detail.data)" wire:ignore class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-700 text-center mb-2">Distribusi Keuangan</h3>
                <div class="h-72 flex items-center justify-center">
                    <canvas id="financialPieChart"></canvas>
                </div>
            </div>

            {{-- DETAIL PERHITUNGAN (LOGIKA BARU) --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-lg font-semibold text-slate-700 border-b pb-2">Detail Perhitungan Logis</h3>

                <div class="space-y-4">
                    {{-- SEKSI KHUSUS --}}
                    <div class="p-3 bg-slate-50 rounded-lg space-y-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori Khusus Service Kariawan</p>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Margin Kotor (In - Out)</span>
                            <span class="font-medium">Rp {{ number_format($reportData['marginKhusus'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-rose-600">
                            <span>
                                Service Karyawan (
                                @if(is_numeric($reportData['serviceKaryawanPercentage']))
                                    {{ (float)$reportData['serviceKaryawanPercentage'] + 0 }}%
                                @else
                                    <span class="text-[10px] italic opacity-75">{{ $reportData['serviceKaryawanPercentage'] }}</span>
                                @endif
                                )
                            </span>
                            <span>- Rp {{ number_format($reportData['serviceKaryawanNominal'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- SEKSI UMUM --}}
                    <div class="p-3 bg-slate-50 rounded-lg space-y-2">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori Umum</p>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600">Margin Bersih Umum</span>
                            <span class="font-medium">Rp {{ number_format($reportData['pendapatanUmum'] - $reportData['pengeluaranUmum'], 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- SUMMARY AKHIR --}}
                    <div class="space-y-2 pt-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-semibold">Total Laba (Kotor)</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($reportData['pendapatanKotor'], 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-blue-600">
                           <span>
                                Fee Manajemen (
                                @if(is_numeric($reportData['feeManajemenPercentage']))
                                    {{ (float)$reportData['feeManajemenPercentage'] + 0 }}%
                                @else
                                    <span class="text-[10px] italic opacity-75">{{ $reportData['feeManajemenPercentage'] }}</span>
                                @endif
                                )
                            </span>
                            <span>- Rp {{ number_format($reportData['feeManajemenNominal'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t-2 border-dashed border-slate-200">
                    <p class="text-xs text-slate-500 font-bold uppercase">Profit Bersih Owner</p>
                    <p class="text-3xl font-extrabold text-slate-900">
                        Rp {{ number_format($reportData['pendapatanOwner'], 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @else
        <div class="p-10 text-center text-slate-500 border border-dashed border-slate-300 rounded-xl bg-slate-50">
            <p class="font-semibold text-lg">Tidak ada data</p>
            <p class="mt-1 text-sm">
                Silakan pilih bulan & tahun lain.
            </p>
        </div>
    @endif
</div>



<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('financialChart', () => ({
        chart: null,

        initChart(data) {
            if (!data) return;

            const canvas = document.getElementById('financialPieChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            if (!ctx) return;

            if (this.chart) {
                this.chart.destroy();
            }

            this.chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: [
                        'Pengeluaran',
                        'Service',
                        'Fee',
                        'Owner'
                    ],
                    datasets: [{
                        data: [
                            data.totalPengeluaran,
                            data.serviceKaryawanNominal,
                            data.feeManajemenNominal,
                            data.pendapatanOwner
                        ],
                        backgroundColor: [
                            '#f87171',  // merah cerah
                            '#fb923c',  // orange cerah
                            '#3b82f6',  // biru cerah
                            '#34d399'   // hijau cerah

                        ],
                        hoverBackgroundColor: [
                            '#ef4444',  // merah sedikit gelap
                            '#f97316',  // orange sedikit gelap
                            '#2563eb',  // biru sedikit gelap
                            '#10b981'   // hijau sedikit gelap
                        ],
                        borderColor: '#fff',   // garis putih antar potongan
                        borderWidth: 2
                        // hoverOffset dihapus supaya tidak tonjol
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    const value = ctx.raw;
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const percent = ((value / total) * 100).toFixed(1);
                                    return `${ctx.label}: Rp ${value.toLocaleString('id-ID')} (${percent}%)`;
                                }
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }));
});
</script>
