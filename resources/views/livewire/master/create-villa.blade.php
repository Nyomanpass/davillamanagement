<div class="space-y-6">
    <form wire:submit.prevent="saveVilla" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-xl">
        
        {{-- Header Form --}}
        <div class="flex items-center mb-6">
            <a href="{{ route('master.kelola.villa') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fa-solid fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">Tambah Villa Baru</h2>
                <p class="text-sm text-gray-500">Isi data yang diperlukan untuk membuat villa baru.</p>
            </div>
        </div>

        {{-- Success / Error --}}
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Data Villa --}}
        <div class="p-6 border border-gray-300 rounded-lg mb-6 space-y-4">
            <h3 class="text-xl font-medium text-gray-700">Data Villa Baru</h3>
            
            <div>
                <label for="nama_villa" class="block text-sm font-medium text-gray-700">Nama Villa</label>
                <input type="text" id="nama_villa" wire:model="nama_villa" class="mt-1 block w-full border p-3 rounded-md">
                @error('nama_villa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="alamat_villa" class="block text-sm font-medium text-gray-700">Alamat Villa</label>
                <textarea id="alamat_villa" wire:model="alamat_villa" rows="2" class="mt-1 block w-full border p-3 rounded-md"></textarea>
                @error('alamat_villa') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fee Manajemen (%)</label>
                    <input type="number" wire:model="fee_manajemen" min="0" max="100" class="mt-1 block w-full border p-3 rounded-md">
                    @error('fee_manajemen') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Service Karyawan (%)</label>
                    <input type="number" wire:model="service_karyawan" min="0" max="100" class="mt-1 block w-full border p-3 rounded-md">
                    @error('service_karyawan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Kamar --}}
        <div class="p-6 border border-gray-300 rounded-lg mb-6">
            <label class="block text-sm font-medium text-gray-700">Jumlah Kamar</label>
            <input type="number" wire:model="jumlah_kamar" min="1" class="mt-1 block w-full border p-3 rounded-md">
            @error('jumlah_kamar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Image --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="p-4 border rounded-lg text-center">
                <label>Image Gallery</label>
                <input type="file" wire:model="image_gallery" multiple class="mt-2">
                @error('image_gallery.*') <span class="text-red-500 text-sm block mt-1">File tidak valid</span> @enderror
                @if ($image_gallery)
                    <div class="flex flex-wrap gap-2 mt-2">
                        @foreach ($image_gallery as $img)
                            <img src="{{ $img->temporaryUrl() }}" class="h-16 w-16 object-cover rounded" alt="Gallery">
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="p-4 border rounded-lg text-center">
                <label>Image Logo</label>
                <input type="file" wire:model="image_logo" class="mt-2">
                @error('image_logo') <span class="text-red-500 text-sm block mt-1">{{ $message }}</span> @enderror
                @if ($image_logo)
                    <img src="{{ $image_logo->temporaryUrl() }}" class="h-20 w-20 object-contain rounded mt-2 mx-auto">
                @endif
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex justify-end gap-4">
            <button type="button" onclick="window.location='{{ route('master.kelola.villa') }}'" class="px-6 py-2 bg-gray-500 text-white rounded-md">Cancel</button>
            <button type="submit" class="px-6 py-2 bg-amber-700 text-white rounded-md">Tambah Villa</button>
        </div>
    </form>
</div>
