<div class="space-y-6">

    <div class="flex items-center justify-between">
        {{-- Header menunjukkan mode EDIT --}}
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit Villa: {{ $this->nama_villa }}</h1>
        <p class="text-sm text-gray-600 hidden md:block">Perbarui detail dan aset Villa.</p>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-lg bg-emerald-100 text-emerald-700 font-medium">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-lg bg-red-100 text-red-700 font-medium">{{ session('error') }}</div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-lg space-y-8">

        <h2 class="text-xl font-semibold text-gray-800">Formulir Perubahan Data</h2>
        
        <form wire:submit.prevent="updateVilla" class="space-y-6"> {{-- Ganti method ke updateVilla --}}
            
            {{-- 1. Nama Villa dan Alamat --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama_villa" class="block text-sm font-medium text-gray-700">Nama Villa</label>
                    <input type="text" id="nama_villa" wire:model="nama_villa" class="mt-1 block w-full rounded-lg">
                    @error('nama_villa') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                 <div>
                    <label for="alamat_villa" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <input type="text" id="alamat_villa" wire:model="alamat_villa" class="mt-1 block w-full rounded-lg">
                    @error('alamat_villa') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- 2. Fee dan Kamar --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="fee_manajemen" class="block text-sm font-medium text-gray-700">Fee Manajemen (%)</label>
                    <input type="number" id="fee_manajemen" wire:model="fee_manajemen" min="0" max="100" class="mt-1 block w-full rounded-lg">
                    @error('fee_manajemen') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="service_karyawan" class="block text-sm font-medium text-gray-700">Service Karyawan (%)</label>
                    <input type="number" id="service_karyawan" wire:model="service_karyawan" min="0" max="100" class="mt-1 block w-full rounded-lg">
                    @error('service_karyawan') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="jumlah_kamar" class="block text-sm font-medium text-gray-700">Jumlah Kamar</label>
                    <input type="number" id="jumlah_kamar" wire:model="jumlah_kamar" min="1" class="mt-1 block w-full rounded-lg">
                    @error('jumlah_kamar') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 border-t pt-6">Manajemen Gambar</h3>

            {{-- 3. Logo Villa --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Logo Villa (PNG/JPG Max 2MB)</label>
                
                {{-- Tampilan Logo Lama --}}
                @if ($current_logo_path)
                    <p class="text-xs text-gray-500 mt-2">Logo Saat Ini:</p>
                    <img src="{{ Storage::url($current_logo_path) }}" class="h-16 w-16 object-cover rounded-lg border my-2">
                @endif
                
                {{-- Upload Input Baru --}}
                <input type="file" wire:model="image_logo" class="mt-1 block w-full text-sm text-gray-500">
                @error('image_logo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                
                {{-- Preview Upload Baru (Jika ada file baru) --}}
                @if ($image_logo)
                    <p class="mt-2 text-xs text-blue-600">Preview Logo Baru:</p>
                    <img src="{{ $image_logo->temporaryUrl() }}" class="h-16 w-16 object-cover rounded-lg border my-2">
                @endif
            </div>

            {{-- 4. Gallery Villa --}}
            <div>
                <label class="block text-sm font-medium text-gray-700">Gallery Villa (Tambah Gambar Baru)</label>
                
                {{-- Tampilan Gallery Lama --}}
                @if (!empty($current_gallery_paths))
                    <p class="text-xs text-gray-500 mt-2">Gallery Saat Ini (Klik untuk hapus):</p>
                    <div class="flex flex-wrap gap-3 mt-2">
                        @foreach ($current_gallery_paths as $index => $path)
                            <div class="relative group">
                                <img src="{{ Storage::url($path) }}" class="h-20 w-20 object-cover rounded-lg border cursor-pointer">
                                {{-- Tombol Hapus --}}
                                <button type="button" wire:click="removeGalleryImage({{ $index }})"
                                    onclick="confirm('Yakin hapus gambar ini?') || event.stopImmediatePropagation()"
                                    class="absolute top-0 right-0 p-1 bg-red-600 text-white rounded-full text-xs opacity-0 group-hover:opacity-100 transition-opacity">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
                
                {{-- Upload Input Baru (Multiple) --}}
                <input type="file" wire:model="image_gallery" multiple class="mt-3 block w-full text-sm text-gray-500">
                @error('image_gallery.*') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                
                {{-- Preview Upload Baru (Jika ada file baru) --}}
                @if (!empty($image_gallery))
                    <p class="mt-2 text-xs text-blue-600">Preview Gambar Baru:</p>
                    <div class="flex flex-wrap gap-3 mt-2">
                        @foreach ($image_gallery as $image)
                            <img src="{{ $image->temporaryUrl() }}" class="h-20 w-20 object-cover rounded-lg border">
                        @endforeach
                    </div>
                @endif
            </div>


            {{-- Tombol Simpan --}}
            <div class="pt-6 text-right border-t">
                <button type="submit"
                    class="inline-flex justify-center py-3 px-8 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed">
                    <span wire:loading.remove>Simpan Perubahan</span>
                    <span wire:loading>Memproses Update...</span>
                </button>
            </div>
        </form>
    </div>
</div>