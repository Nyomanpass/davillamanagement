<div class="space-y-6">

    {{-- Header Halaman --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Pendapatan</h1>
        <p class="text-sm text-gray-600 hidden md:block">Kelola dan input data pemasukan villa.</p>
    </div>

    {{-- Kontainer Utama Putih --}}
    <div class="bg-white p-6 rounded-xl shadow-lg space-y-8">

        {{-- BAGIAN 1: TAMBAH DATA PENDAPATAN (FORM) --}}
        <h2 class="text-xl font-semibold text-gray-800">Tambah Data Pendapatan</h2>
        
        <form wire:submit.prevent="savePendapatan" class="space-y-6">
            
            {{-- Baris 1: Jenis Pendapatan --}}
            <div>
                <label for="jenis_pendapatan" class="block text-sm font-medium text-gray-700">Jenis Pendapatan</label>
                <select id="jenis_pendapatan" wire:model="jenisPendapatan" 
                    class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-lg shadow-sm">
                    <option value="" disabled>Pilih Jenis Pendapatan</option>
                    {{-- Ganti dengan data loop dari database jika ada --}}
                    <option value="sewa_harian">Sewa Harian</option>
                    <option value="sewa_mingguan">Sewa Mingguan</option>
                    <option value="deposit">Deposit</option>
                    <option value="lainnya">Lainnya</option>
                </select>
                @error('jenisPendapatan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Baris 2: Nominal dan Tanggal --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nominal" class="block text-sm font-medium text-gray-700">Nominal Pendapatan</label>
                    <div class="relative mt-1 rounded-lg shadow-sm">
                        {{-- Field Nominal --}}
                        <input type="number" id="nominal" wire:model="nominal" placeholder="0"
                            class="block w-full py-3 pr-10 border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-lg" 
                            min="0">
                        {{-- Icon Placeholder --}}
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                    </div>
                    @error('nominal') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                    <div class="relative mt-1 rounded-lg shadow-sm">
                        {{-- Field Tanggal --}}
                        <input type="text" id="tanggal" wire:model="tanggal" value="hh/bb/tt"
                            class="block w-full py-3 pr-10 border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-lg" 
                            placeholder="hh/bb/tt">
                        {{-- Icon Kalender --}}
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                    </div>
                    @error('tanggal') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Baris 3: Metode Pembayaran --}}
            <div>
                <label for="metode_pembayaran" class="block text-sm font-medium text-gray-700">Metode Pembayaran</label>
                <select id="metode_pembayaran" wire:model="metodePembayaran"
                    class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 sm:text-sm rounded-lg shadow-sm">
                    <option value="" disabled>Pilih Metode Pembayaran</option>
                    {{-- Ganti dengan data loop dari database jika ada --}}
                    <option value="cash">Cash</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="e_wallet">E-Wallet</option>
                </select>
                @error('metodePembayaran') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Tombol Simpan --}}
            <div class="pt-4 text-right">
                <button type="submit"
                    class="inline-flex justify-center py-3 px-8 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-amber-700 hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all">
                    Simpan
                </button>
            </div>
        </form>
    </div>

    {{-- BAGIAN 2: RINGKASAN DATA (Di bawah form, mengikuti gaya dashboard) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">

        {{-- Kartu Hijau (Bulan Ini) --}}
        <div class="bg-teal-400 text-white p-4 rounded-xl shadow-lg h-28 flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90">Bulan Ini</p>
                <p class="text-2xl font-bold">Rp {{ number_format(80000000, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-arrow-up text-2xl opacity-50"></i>
        </div>

        {{-- Kartu Coklat/Amber (Bulan Lalu) --}}
        <div class="bg-amber-600 text-white p-4 rounded-xl shadow-lg h-28 flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90">Bulan Lalu</p>
                <p class="text-2xl font-bold">Rp {{ number_format(75000000, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-arrow-down text-2xl opacity-50"></i>
        </div>

        {{-- Kartu Biru (All Time) --}}
        <div class="bg-blue-500 text-white p-4 rounded-xl shadow-lg h-28 flex items-center justify-between">
            <div>
                <p class="text-sm opacity-90">All Time</p>
                <p class="text-2xl font-bold">Rp {{ number_format(450000000, 0, ',', '.') }}</p>
            </div>
            <i class="fas fa-chart-line text-2xl opacity-50"></i>
        </div>
    </div>
    
    {{-- BAGIAN 3: TABEL PENDAPATAN (Placeholder) --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Daftar Transaksi Pendapatan</h2>
        <div class="h-64 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-center text-gray-500">
            Placeholder: Tabel daftar transaksi pendapatan
        </div>
    </div>
</div>