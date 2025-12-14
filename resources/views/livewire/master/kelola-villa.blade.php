<div class="space-y-6">

    {{-- Notifikasi --}}
    @foreach (['success', 'error', 'info'] as $msg)
        @if (session()->has($msg))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show"
                 class="p-4 rounded-lg font-medium shadow-sm
                    {{ $msg === 'success' ? 'bg-emerald-100 text-emerald-700' : '' }}
                    {{ $msg === 'error' ? 'bg-red-100 text-red-700' : '' }}
                    {{ $msg === 'info' ? 'bg-blue-100 text-blue-700' : '' }}">
                {{ session($msg) }}
            </div>
        @endif
    @endforeach

    {{-- Header Halaman --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Kelola Villa</h1>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('master.create.villa') }}"
               class="inline-flex items-center gap-2 py-2 px-5 rounded-full bg-amber-700 text-white font-semibold shadow-md hover:bg-amber-800 transition">
               <i class="fas fa-plus"></i> Tambah Villa
            </a>
            <button wire:click="openAddAccountForm"
               class="inline-flex items-center gap-2 py-2 px-5 rounded-full bg-amber-700 text-white font-semibold shadow-md hover:bg-amber-800 transition">
               <i class="fas fa-user-plus"></i> Tambah Akun
            </button>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="bg-white p-6 rounded-2xl shadow-lg space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ([
                ['title' => 'Total Villa', 'value' => $this->totalVilla, 'icon' => 'fas fa-home', 'color' => 'amber'],
                ['title' => 'Total Karyawan', 'value' => $this->totalKaryawan, 'icon' => 'fas fa-users', 'color' => 'amber'],
                ['title' => 'Total Akun Sistem', 'value' => $this->totalAkun, 'icon' => 'fas fa-user-shield', 'color' => 'amber']
            ] as $card)
                <div class="bg-gradient-to-br from-{{ $card['color'] }}-50 to-{{ $card['color'] }}-100 p-6 rounded-2xl flex items-center justify-between h-32 shadow hover:shadow-xl transition">
                    <div>
                        <div class="text-sm font-medium text-gray-600">{{ $card['title'] }}</div>
                        <div class="text-3xl font-bold text-{{ $card['color'] }}-700">{{ $card['value'] }}</div>
                    </div>
                    <i class="{{ $card['icon'] }} text-4xl text-{{ $card['color'] }}-500 opacity-70"></i>
                </div>
            @endforeach
        </div>

        {{-- Search Bar --}}
        <div class="relative mt-4">
            <input type="text" wire:model.live="search" placeholder="Cari Nama atau Alamat Villa..."
                   class="w-full py-3 pl-12 pr-4 border border-gray-300 rounded-full shadow-sm focus:outline-none focus:ring-amber-500 focus:border-amber-500 transition">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <i class="fas fa-search text-gray-400 text-lg"></i>
            </div>
        </div>

        {{-- Daftar Villa --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 pt-4">
            @forelse ($villas as $villa)
                <div class="rounded-2xl border border-gray-100 shadow-md hover:shadow-xl overflow-hidden transition transform hover:-translate-y-1">
                    @if ($villa->image_logo)
                        <img src="{{ Storage::url($villa->image_logo) }}" 
                             alt="Logo {{ $villa->nama_villa }}" 
                             class="h-36 w-full object-cover">
                    @else
                        <div class="bg-teal-400 h-36 w-full flex items-center justify-center">
                            <span class="text-white font-bold text-center px-4">{{ $villa->nama_villa }}</span>
                        </div>
                    @endif

                    <div class="p-4 space-y-2 bg-white">
                        <p class="text-lg font-semibold text-gray-800">{{ $villa->nama_villa }}</p>
                        <p class="text-sm text-gray-500 truncate">{{ $villa->alamat_villa }}</p>
                        <div class="flex gap-2 pt-2">
                            <button wire:click="showVillaDetail({{ $villa->id }})"
                                    class="flex-1 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition">Detail</button>
                            <a href="{{ route('master.edit.villa', $villa->id) }}"
                               class="flex-1 py-2 rounded-lg bg-amber-600 text-white font-medium hover:bg-amber-700 transition text-center">Edit</a>
                            <button wire:click="deleteVilla({{ $villa->id }})"
                                    onclick="confirm('Yakin ingin menghapus {{ $villa->nama_villa }}?') || event.stopImmediatePropagation()"
                                    class="flex-1 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition">Delete</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-6 text-center text-gray-500 border-2 border-dashed rounded-2xl">
                    <i class="fas fa-exclamation-circle mr-2"></i> Tidak ada data villa yang ditemukan.
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($villas->hasPages())
            <div class="pt-6">{{ $villas->links() }}</div>
        @endif
    </div>

    {{-- Modal Detail --}}
    @if($isDetailModalOpen)
        <div x-data="{ open: @entangle('isDetailModalOpen').live }" x-show="open" 
             class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black bg-opacity-50">
            <div x-show="open" x-transition class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-6 space-y-4">
                <h3 class="text-xl font-bold text-gray-900 border-b pb-2">Detail Villa: {{ $selectedVilla?->nama_villa }}</h3>

                @if($selectedVilla)
                    <div class="space-y-3">
                        <p><strong>Alamat:</strong> {{ $selectedVilla->alamat_villa }}</p>
                        <p><strong>Jumlah Kamar:</strong> {{ $selectedVilla->jumlah_kamar }}</p>
                        <p><strong>Fee Manajemen:</strong> {{ $selectedVilla->fee_manajemen }}%</p>
                        <p><strong>Service Karyawan:</strong> {{ $selectedVilla->service_karyawan }}%</p>

                        <h4 class="font-semibold pt-2">Logo:</h4>
                        @if ($selectedVilla->image_logo)
                            <img src="{{ Storage::url($selectedVilla->image_logo) }}" class="h-24 w-24 object-cover rounded-lg border">
                        @else
                            <p class="text-sm text-gray-500">Logo belum tersedia.</p>
                        @endif

                        <h4 class="font-semibold pt-2">Gallery:</h4>
                        @if(!empty($selectedVilla->image_gallery))
                            <div class="grid grid-cols-3 gap-2 max-h-48 overflow-y-auto">
                                @foreach($selectedVilla->image_gallery as $path)
                                    <img src="{{ Storage::url($path) }}" class="h-20 w-full object-cover rounded-lg border">
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Gallery gambar belum tersedia.</p>
                        @endif
                    </div>
                @else
                    <p class="text-red-500">Data tidak dapat dimuat.</p>
                @endif

                <div class="flex justify-end pt-4 border-t">
                    <button wire:click="closeDetailModal" 
                            class="py-2 px-4 text-sm font-medium rounded-lg bg-gray-700 text-white hover:bg-gray-800 transition">Tutup</button>
                </div>
            </div>
        </div>
    @endif

</div>
