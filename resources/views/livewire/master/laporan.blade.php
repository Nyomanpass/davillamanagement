<div class="space-y-6">

    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Laporan</h1>
        
        {{-- Tombol Export Excel --}}
        <button wire:click="exportReport"
            class="inline-flex items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
            <i class="fas fa-file-excel mr-2"></i> Export Excel
        </button>
    </div>

    {{-- Kontainer Utama Putih --}}
    <div class="bg-white p-6 rounded-xl shadow-lg space-y-6">
        
        {{-- Filter Bulan dan Tahun --}}
        <div class="flex flex-wrap items-center gap-4 border-b pb-4">
            <span class="font-medium text-gray-700">Filter:</span>
            
            {{-- Filter Bulan --}}
            <select wire:model.live="selectedMonth" class="py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
                <option value="01">Januari</option>
                <option value="02">Februari</option>
                <option value="12">Desember</option>
                {{-- Opsi bulan lainnya --}}
            </select>
            
            {{-- Filter Tahun --}}
            <select wire:model.live="selectedYear" class="py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
                <option value="2025">2025</option>
                <option value="2024">2024</option>
                {{-- Opsi tahun lainnya --}}
            </select>
        </div>

        {{-- Visualisasi Data (Mengambil gaya dashboard) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
            
            {{-- Status Keuangan (Pie Chart) --}}
            <div class="border border-gray-200 rounded-xl shadow-md p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-teal-500"></i> Status Keuangan (Bulan Ini)
                </h3>
                <div class="h-64 bg-gray-50 flex items-center justify-center rounded-lg text-gray-500">
                    Placeholder: Diagram Lingkaran Status Keuangan
                </div>
            </div>

            {{-- Occupancy (Pie Chart) --}}
            <div class="border border-gray-200 rounded-xl shadow-md p-4 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-bed mr-2 text-blue-500"></i> Tingkat Hunian (Occupancy Rate)
                </h3>
                <div class="h-64 bg-gray-50 flex items-center justify-center rounded-lg text-gray-500">
                    Placeholder: Diagram Lingkaran Tingkat Hunian
                </div>
            </div>
        </div>

        {{-- Tabel Ringkasan Keuangan Bulanan --}}
        <div class="pt-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Ringkasan Keuangan Detail</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pendapatan (Rp)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengeluaran (Rp)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit Bersih (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Contoh Baris Data --}}
                        <tr class="hover:bg-amber-50/50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total Bulan Desember 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">123.000.000</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">30.000.000</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600 font-bold">93.000.000</td>
                        </tr>
                        
                        {{-- Baris Data Detail Villa --}}
                        <tr class="text-gray-600 border-t border-gray-100">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">Villa Jimbaran</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">100.000.000</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">25.000.000</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">75.000.000</td>
                        </tr>
                        <tr class="text-gray-600">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">Villa Kuta</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">23.000.000</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">5.000.000</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">18.000.000</td>
                        </tr>

                        {{-- Baris Total (Jika diperlukan) --}}
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>