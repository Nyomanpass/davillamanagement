<div class="space-y-6">

    {{-- Header Halaman --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">History User</h1>
        <p class="text-sm text-gray-600 hidden md:block">Catatan riwayat Login, Logout, dan aktivitas penting pengguna.</p>
    </div>

    {{-- Kontainer Utama Putih --}}
    <div class="bg-white p-6 rounded-xl shadow-lg space-y-6">
        
        {{-- Filter dan Pagination Control --}}
        <div class="flex flex-wrap items-center justify-between gap-4 border-b pb-4">
            
            {{-- Filter Bulan dan Tahun --}}
            <div class="flex items-center gap-4">
                <span class="font-medium text-gray-700">Bulan:</span>
                <select wire:model.live="selectedMonth" class="py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="12">Desember</option>
                    <option value="11">November</option>
                    {{-- Opsi bulan lainnya --}}
                </select>
                
                <span class="font-medium text-gray-700">Tahun:</span>
                <select wire:model.live="selectedYear" class="py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="2025">2025</option>
                    <option value="2024">2024</option>
                    {{-- Opsi tahun lainnya --}}
                </select>
            </div>
            
            {{-- Kontrol Pagination dan Total Data --}}
            <div class="flex items-center gap-4 text-sm text-gray-700">
                <span class="font-medium">Show:</span>
                <select wire:model.live="perPage" class="py-2 px-3 border border-gray-300 rounded-lg shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>total: {{ $totalData ?? 0 }} data</span>
            </div>
        </div>

        {{-- Tabel History User --}}
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Riwayat Aktivitas Pengguna</h2>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    
                    {{-- Loop data riwayat di sini --}}
                    @foreach ($activities as $activity)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{-- Format: 03-12-2025 13:00 --}}
                            {{ $activity['waktu'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">
                            {{ $activity['nama_user'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium 
                                @if ($activity['aktivitas'] == 'Login') bg-teal-100 text-teal-800
                                @elseif ($activity['aktivitas'] == 'Logout') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $activity['aktivitas'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach

                    {{-- Data Placeholder (untuk demo tanpa data nyata) --}}
                    @if (empty($activities))
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">07-12-2025 09:30</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Admin Villa Jimbaran</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">Login</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">07-12-2025 10:00</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Admin Villa Uluwatu</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Logout</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">07-12-2025 11:15</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800">Super Master</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tambah Pendapatan</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        {{-- Slot Pagination (Jika menggunakan Livewire Pagination) --}}
        {{-- <div class="pt-4">
            {{ $activities->links() }}
        </div> --}}
    </div>
</div>