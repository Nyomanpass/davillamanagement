<div class="space-y-6">

    {{-- Header Halaman dan Notifikasi --}}
    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight border-b border-gray-200 pb-4">
        <i class="fas fa-chart-line text-teal-600 mr-2"></i> Dashboard Master
    </h1>
    @if (session()->has('success'))
        <div class="p-4 rounded-xl font-medium border-l-4 shadow-lg flex items-center bg-emerald-50 text-emerald-800 border-emerald-500">
            <i class="fas fa-check-circle mr-3"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Pilih Villa (Filter - AUTOLOAD) --}}
    <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
        <div class="flex flex-col md:flex-row md:items-end gap-3">
            
            <div class="flex-grow">
                <label for="villa_select" class="block mb-1 font-semibold text-sm text-gray-700">Pilih Villa</label>
                <select 
                    wire:model.live="villa_id"     {{-- Livewire 3: Update properti segera --}}
                    wire:change="pilihVilla"       {{-- Panggil fungsi untuk me-reload data dan dispatch chart event --}}
                    id="villa_select"
                    class="w-full border-gray-300 rounded-lg p-2.5 shadow-sm 
                           focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                    <option value="">-- Semua Villa --</option>
                    @foreach ($listVilla as $villa)
                        <option value="{{ $villa->id }}">{{ $villa->nama_villa }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tombol diubah menjadi informasi karena update sudah otomatis --}}
            <div class="md:self-end">
                <button type="button" disabled
                    class="w-full md:w-auto px-5 py-2.5 bg-gray-400 text-white font-semibold rounded-lg shadow-md cursor-not-allowed flex items-center justify-center gap-2">
                    <i class="fas fa-sync-alt"></i> Auto Update Aktif
                </button>
            </div>
            
        </div>
    </div>


    {{-- Tampilkan Data Utama HANYA JIKA villa_id SUDAH TERPILIH --}}
    @if ($villa_id) 

        {{-- Ringkasan KPI Utama --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Villa --}}
            <div class="bg-teal-500 text-white p-5 rounded-xl shadow-lg h-32 flex flex-col justify-between transition duration-300 hover:shadow-xl">
                <p class="text-sm font-medium opacity-90">Total Villa Dikelola</p>
                <p class="text-3xl font-extrabold">{{ $totalVilla }}</p>
            </div>
            {{-- Total Transaksi --}}
            <div class="bg-amber-600 text-white p-5 rounded-xl shadow-lg h-32 flex flex-col justify-between transition duration-300 hover:shadow-xl">
                <p class="text-sm font-medium opacity-90">Total Transaksi (Periode)</p>
                <p class="text-3xl font-extrabold">{{ $totalTransaksi }}</p>
            </div>
            {{-- Pendapatan Hari Ini --}}
            <div class="bg-blue-600 text-white p-5 rounded-xl shadow-lg h-32 flex flex-col justify-between transition duration-300 hover:shadow-xl">
                <p class="text-sm font-medium opacity-90">Pendapatan Hari Ini</p>
                <p class="text-3xl font-extrabold">Rp {{ number_format($pendapatanHariIni,0,',','.') }}</p>
            </div>
            {{-- Pengeluaran Hari Ini --}}
            <div class="bg-pink-600 text-white p-5 rounded-xl shadow-lg h-32 flex flex-col justify-between transition duration-300 hover:shadow-xl">
                <p class="text-sm font-medium opacity-90">Pengeluaran Hari Ini</p>
                <p class="text-3xl font-extrabold">Rp {{ number_format($pengeluaranHariIni,0,',','.') }}</p>
            </div>
        </div>

        {{-- KPI Tambahan --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            {{-- Total Pendapatan --}}
            <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Pendapatan (Gross)</p>
                <p class="text-xl font-bold text-teal-700 mt-1">Rp {{ number_format($totalPendapatan,0,',','.') }}</p>
            </div>
            {{-- Total Pengeluaran --}}
            <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Pengeluaran</p>
                <p class="text-xl font-bold text-pink-700 mt-1">Rp {{ number_format($totalPengeluaran,0,',','.') }}</p>
            </div>
            {{-- Transaksi Hari Ini --}}
            <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase">Transaksi Hari Ini</p>
                <p class="text-xl font-bold text-blue-700 mt-1">{{ $totalTransaksi }}</p>
            </div>
            {{-- Villa Dipilih --}}
            <div class="bg-white p-5 rounded-xl shadow-md border border-gray-100 text-center">
                <p class="text-xs font-semibold text-gray-500 uppercase">Properti Aktif</p>
                <p class="text-base font-bold text-amber-700 mt-1 truncate">
                    {{ $villa_id ? $listVilla->find($villa_id)->nama_villa : 'SEMUA VILLA' }}
                </p>
            </div>
        </div>

        {{-- Grafik - Menangkap event dari PHP --}}
        <div 
            x-data="{}" 
            @charts-data-updated.window="initializeCharts($event.detail.data)" 
            class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-4"
        >
            
            {{-- Line Chart --}}
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-2xl" wire:ignore>
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3 mb-4">Tren Pendapatan & Pengeluaran Bulanan</h3>
                <div class="relative h-80 w-full"> 
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
            
            {{-- Pie Chart --}}
            <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-2xl flex flex-col items-center justify-center" wire:ignore>
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3 mb-4 w-full">Distribusi Pendapatan vs Pengeluaran</h3>
                <div class="relative h-80 w-full"> 
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

    @else
        {{-- Pesan yang muncul jika $villa_id kosong --}}
        <div class="bg-white p-8 rounded-xl shadow-lg border border-gray-100 text-center mt-6">
            <i class="fas fa-exclamation-circle text-amber-500 text-4xl mb-4"></i>
            <p class="font-semibold text-xl">Villa Belum Terpilih</p>
            <p class="text-base text-gray-600 mt-2">Silakan pilih **Villa** dari menu drop-down di atas untuk melihat ringkasan data dan grafik.</p>
        </div>
    @endif

</div>

{{-- SCRIPT CHART.JS (LIVEWIRE 3 OPTIMIZED) --}}
<script>
    let pieChartInstance = null;
    let lineChartInstance = null;
    
    // Fungsi menerima data baru dari Livewire event
    function initializeCharts(newData) {
        
        // Ambil data dari event (jika ada) atau dari PHP inline (untuk inisialisasi awal)
        let pieData = {
            // Menggunakan PHP interpolation sebagai fallback/inisialisasi awal
            // Catatan: Jika tidak ada data, ini akan bernilai 0
            pendapatan: newData?.pendapatan ?? {{ $totalPendapatan ?? 0 }},
            pengeluaran: newData?.pengeluaran ?? {{ $totalPengeluaran ?? 0 }},
            
            // Dummy Data untuk Line Chart (Ganti dengan data bulanan PHP jika sudah tersedia)
            lineData: newData?.lineData ?? {
                pendapatan: [10,20,15,25,30,40,35,30,45,50,60,55], 
                pengeluaran: [5,15,10,20,25,20,25,20,30,35,40,30]
            }
        };

        // Hancurkan instansi lama
        if (pieChartInstance) { pieChartInstance.destroy(); pieChartInstance = null; }
        if (lineChartInstance) { lineChartInstance.destroy(); lineChartInstance = null; }

        // --- 1. Pie Chart ---
        const pieCtx = document.getElementById('pieChart');
        if (pieCtx) {
            pieChartInstance = new Chart(pieCtx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Pengeluaran', 'Pendapatan'],
                    datasets: [{
                        // Gunakan data dinamis
                        data: [pieData.pengeluaran, pieData.pendapatan], 
                        backgroundColor: ['#f87171', '#34d399']
                    }]
                },
                options: { 
                    responsive:true, 
                    maintainAspectRatio: false,
                    plugins:{legend:{position:'bottom'}} 
                }
            });
        }

        // --- 2. Line Chart ---
        const lineCtx = document.getElementById('lineChart');
        if (lineCtx) {
            lineChartInstance = new Chart(lineCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                    datasets: [{
                        label: 'Pendapatan',
                        data: pieData.lineData.pendapatan, 
                        borderColor:'#06b6d4', backgroundColor:'rgba(6,182,212,0.1)', fill:true, tension:0.4
                    },{
                        label:'Pengeluaran',
                        data: pieData.lineData.pengeluaran,
                        borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,0.1)', fill:true, tension:0.4
                    }]
                },
                options:{
                    responsive:true, 
                    maintainAspectRatio:false,
                    plugins:{legend:{position:'top'}},
                    scales:{y:{beginAtZero:true}}
                }
            });
        }
    }

    // PENTING: 1. Inisialisasi Awal (Saat Livewire Selesai Memuat)
    document.addEventListener('livewire:load', () => {
        // Panggil inisialisasi awal (menggunakan data inline PHP)
        setTimeout(initializeCharts, 50); 
    });
</script>