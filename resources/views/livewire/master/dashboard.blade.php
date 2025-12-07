{{-- resources/views/livewire/master/dashboard.blade.php --}}

<div class="space-y-6">

    {{-- Baris 1: Kartu Ringkasan Keuangan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Kartu Hijau --}}
        <div class="bg-teal-400 text-white p-4 rounded-xl shadow-lg h-28 flex items-center justify-between">
            <div><p class="text-sm">Bulan Ini</p><p class="text-2xl font-bold">Rp 80 Jt</p></div>
        </div>

        {{-- Kartu Coklat/Amber --}}
        <div class="bg-amber-600 text-white p-4 rounded-xl shadow-lg h-28 flex items-center justify-between">
            <div><p class="text-sm">Bulan Lalu</p><p class="text-2xl font-bold">Rp 75 Jt</p></div>
        </div>

        {{-- Kartu Biru --}}
        <div class="bg-blue-500 text-white p-4 rounded-xl shadow-lg h-28 flex items-center justify-between">
            <div><p class="text-sm">All Time</p><p class="text-2xl font-bold">Rp 450 Jt</p></div>
        </div>

        {{-- Kartu Pink/Magenta --}}
        <div class="bg-pink-500 text-white p-4 rounded-xl shadow-lg h-28 flex items-center justify-between">
            <div><p class="text-sm">All Time</p><p class="text-2xl font-bold">Rp 120 Jt</p></div>
        </div>
    </div>

    {{-- Baris 2: Kartu Placeholder Kosong (Untuk kesamaan visual) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-gray-100 p-4 rounded-xl shadow-md h-28"></div>
        <div class="bg-gray-100 p-4 rounded-xl shadow-md h-28"></div>
        <div class="bg-gray-100 p-4 rounded-xl shadow-md h-28"></div>
        <div class="bg-gray-100 p-4 rounded-xl shadow-md h-28"></div>
    </div>

    {{-- Baris 3: Konten Utama (Status Keuangan) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Kolom Kiri: Placeholder Besar --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-lg h-96">
            {{-- Konten Grafik atau Data Penting Lainnya --}}
        </div>

        {{-- Kolom Kanan: Status Keuangan (Pie Chart) --}}
        <div class="lg:col-span-1 bg-white p-6 rounded-xl shadow-lg h-96">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 8a1 1 0 012 0v5a1 1 0 11-2 0V8z" clip-rule="evenodd"></path></svg>
                Status Keuangan
            </h3>
            
            {{-- Placeholder untuk Pie Chart --}}
            <div class="w-full h-48 bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                            </div>

            {{-- Legend --}}
            <div class="text-xs space-y-1">
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-red-500 mr-2"></span> Pengeluaran</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-green-500 mr-2"></span> Pemasukan</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-yellow-500 mr-2"></span> Service Karyawan</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-blue-500 mr-2"></span> Pendapatan Manajemen</div>
                <div class="flex items-center"><span class="w-2 h-2 rounded-full bg-pink-500 mr-2"></span> Pendapatan Owner</div>
            </div>
        </div>
    </div>
</div>