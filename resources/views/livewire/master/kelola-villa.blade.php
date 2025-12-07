<div class="space-y-6">

    {{-- Header Halaman & Tombol Aksi --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Kelola Villa</h1>
        
        {{-- Tombol Aksi (Sesuai Screenshot 01.44.49.png) --}}
        <div class="flex space-x-3">
            {{-- Tombol Tambah Villa --}}
            <a href="{{ route('master.create.villa') }}"
                class="inline-flex items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-amber-700 hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all">
                <i class="fas fa-plus mr-2"></i> Tambah Villa
         </a>
            
            {{-- Tombol Tambah Akun (untuk staf/owner) --}}
            <button wire:click="openAddAccountForm"
                class="inline-flex items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-amber-700 hover:bg-amber-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all">
                <i class="fas fa-plus mr-2"></i> Tambah Akun
            </button>
        </div>
    </div>
    
    {{-- Kontainer Utama Putih --}}
    <div class="bg-white p-6 rounded-xl shadow-lg space-y-6">
        
        {{-- Slot 3 Kartu Ringkasan (Placeholder) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Card Placeholder 1 --}}
            <div class="bg-gray-100 p-6 rounded-xl shadow-inner flex items-center justify-between h-32">
                <div class="text-gray-500">Total Villa</div>
                <div class="w-10 h-10 bg-gray-300 rounded-lg"></div>
            </div>
            {{-- Card Placeholder 2 --}}
            <div class="bg-gray-100 p-6 rounded-xl shadow-inner flex items-center justify-between h-32">
                <div class="text-gray-500">Total Karyawan</div>
                <div class="w-10 h-10 bg-gray-300 rounded-lg"></div>
            </div>
            {{-- Card Placeholder 3 --}}
            <div class="bg-gray-100 p-6 rounded-xl shadow-inner flex items-center justify-between h-32">
                <div class="text-gray-500">Total Akun</div>
                <div class="w-10 h-10 bg-gray-300 rounded-lg"></div>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="relative mt-4">
            <input type="text" wire:model.live="search" placeholder="Search..."
                class="w-full py-3 pl-10 pr-4 border-gray-300 focus:outline-none focus:ring-amber-500 focus:border-amber-500 rounded-lg shadow-sm">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
        </div>
        
        {{-- Daftar Villa dalam Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pt-4">
            
            {{-- Villa Card (Contoh 1) --}}
            @foreach (range(1, 3) as $item)
            <div class="rounded-xl shadow-lg overflow-hidden border border-gray-100">
                {{-- Area Gambar/Header (Menggunakan warna teal dari dashboard) --}}
                <div class="bg-teal-400 h-32 w-full flex items-center justify-center relative">
                    <span class="text-white text-xl font-bold">Villa {{ $item }}</span>
                    {{-- Placeholder untuk ikon kecil di sudut --}}
                    <div class="absolute top-3 right-3 w-4 h-4 bg-white/30 rounded-full"></div> 
                </div>
                
                {{-- Area Detail dan Aksi --}}
                <div class="p-4 bg-white space-y-3">
                    <p class="text-lg font-semibold text-gray-800">Nama Villa #{{ $item }}</p>
                    <p class="text-sm text-gray-500 truncate">Jl. Raya Jimbaran No. 123, Bali</p>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-between space-x-2 pt-2">
                        <button wire:click="editVilla({{ $item }})"
                            class="flex-1 py-2 text-sm font-medium text-white rounded-lg bg-amber-700 hover:bg-amber-800 transition-all">
                            Edit
                        </button>
                        <button wire:click="deleteVilla({{ $item }})"
                            class="flex-1 py-2 text-sm font-medium text-white rounded-lg bg-red-600 hover:bg-red-700 transition-all">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
            
            {{-- Anda dapat menambahkan lebih banyak card di sini sesuai data --}}

        </div>
    </div>
</div>