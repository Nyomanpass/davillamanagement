<div class="space-y-6">
    <form wire:submit.prevent="saveEmployee" class="bg-white p-6 rounded-lg shadow-xl max-w-xl mx-auto">
        
        <h2 class="text-2xl font-semibold text-gray-800 mb-6">Tambah Karyawan Baru</h2>

        {{-- Flash Messages --}}
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

        {{-- Input Nama --}}
        <div class="mb-4">
            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Karyawan</label>
            <input type="text" id="nama" wire:model="nama" class="mt-1 block w-full border p-2 rounded-md @error('nama') border-red-500 @enderror">
            @error('nama') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Input Alamat --}}
        <div class="mb-4">
            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
            <textarea id="alamat" wire:model="alamat" rows="2" class="mt-1 block w-full border p-2 rounded-md @error('alamat') border-red-500 @enderror"></textarea>
            @error('alamat') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Input Jabatan --}}
        <div class="mb-6">
            <label for="jabatan" class="block text-sm font-medium text-gray-700">Jabatan</label>
            <input type="text" id="jabatan" wire:model="jabatan" class="mt-1 block w-full border p-2 rounded-md @error('jabatan') border-red-500 @enderror">
            @error('jabatan') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        {{-- Tombol Submit --}}
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                Simpan Karyawan
            </button>
        </div>
    </form>
</div>
