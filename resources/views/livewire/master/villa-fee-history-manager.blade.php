<div>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">
                    Settings <span class="text-amber-600">Villa</span>
                </h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg uppercase tracking-wider border border-amber-200">Konfigurasi</span>
                    <p class="text-slate-600 font-bold text-base">{{ $villaName }}</p>
                </div>
            </div>
            <a href="{{ route('master.kelola.villa') }}" 
               class="inline-flex items-center px-5 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>
        </div>

        {{-- GRID UTAMA --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- KOLOM KIRI: SETTING KATEGORI --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold text-slate-800">Service Fee Categories</h4>
                        <p class="text-sm text-slate-500 mt-1">Pilih kategori yang akan masuk dalam perhitungan Service Fee.</p>
                    </div>
                    <button wire:click="saveCategories" 
                        class="px-5 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-md active:scale-95">
                        <i class="fas fa-save mr-2"></i> Simpan
                    </button>
                </div>

                <div class="p-6 space-y-8">
                    {{-- NOTIFIKASI --}}
                    @if (session()->has('success_category'))
                        <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show"
                             class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm font-bold animate-in fade-in zoom-in">
                             <i class="fas fa-check-circle mr-2 text-base"></i> {{ session('success_category') }}
                        </div>
                    @endif

                    {{-- GRUP PENDAPATAN --}}
                    <div class="space-y-4">
                        <h5 class="text-xs font-black text-emerald-600 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-arrow-circle-down text-sm"></i> Income Categories
                        </h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($allCategories->where('type', 'income') as $cat)
                                <label class="group flex items-center p-4 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-emerald-500 hover:bg-white transition-all shadow-sm">
                                    <input type="checkbox" wire:model="selectedCategories" value="{{ $cat->id }}" 
                                           class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                    <span class="ml-3 text-sm font-bold text-slate-700">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- GRUP PENGELUARAN --}}
                    <div class="space-y-4 pt-6 border-t border-slate-100">
                        <h5 class="text-xs font-black text-red-600 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-arrow-circle-up text-sm"></i> Expense Categories
                        </h5>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($allCategories->where('type', 'expense') as $cat)
                                <label class="group flex items-center p-4 bg-slate-50 border border-slate-200 rounded-xl cursor-pointer hover:border-red-500 hover:bg-white transition-all shadow-sm">
                                    <input type="checkbox" wire:model="selectedCategories" value="{{ $cat->id }}" 
                                           class="w-5 h-5 text-red-600 rounded border-slate-300 focus:ring-red-500">
                                    <span class="ml-3 text-sm font-bold text-slate-700">{{ $cat->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: INPUT RIWAYAT FEE --}}
            <div class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <h2 class="text-base font-bold text-slate-800">
                            {{ $editingId ? 'Update Riwayat' : 'Input Fee Baru' }}
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500 tracking-wide ml-1">Fee Manajemen (%)</label>
                            <input type="number" wire:model="fee_manajemen" 
                                   class="w-full rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 text-sm py-3 px-4 shadow-sm"
                                   placeholder="0">
                            @error('fee_manajemen') <span class="text-xs text-red-500 font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500 tracking-wide ml-1">Service Karyawan (%)</label>
                            <input type="number" wire:model="service_karyawan" 
                                   class="w-full rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 text-sm py-3 px-4 shadow-sm"
                                   placeholder="0">
                            @error('service_karyawan') <span class="text-xs text-red-500 font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold uppercase text-slate-500 tracking-wide ml-1">Mulai Berlaku</label>
                            <input type="date" wire:model="mulai_berlaku" 
                                   class="w-full rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 text-sm py-3 px-4 shadow-sm">
                            @error('mulai_berlaku') <span class="text-xs text-red-500 font-bold ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 flex flex-col gap-3">
                            <button wire:click="save" class="w-full py-3.5 bg-amber-600 text-white rounded-xl font-bold text-sm hover:bg-amber-700 transition shadow-lg shadow-amber-100 active:scale-[0.98]">
                                {{ $editingId ? 'Update Data Riwayat' : 'Tambah Riwayat Baru' }}
                            </button>
                            @if($editingId)
                                <button wire:click="$set('editingId', null)" class="w-full py-3 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition">
                                    Batalkan Edit
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL RIWAYAT --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-5 bg-slate-50 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Riwayat Perubahan Persentase Fee</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-white text-slate-500 uppercase text-xs font-bold tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Konfigurasi Fee</th>
                            <th class="px-6 py-4 text-center">Service Kar.</th>
                            <th class="px-6 py-4">Mulai Berlaku</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($histories as $h)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm border border-amber-100">
                                        {{ $h->fee_manajemen }}%
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-700">Fee Manajemen</span>
                                        <span class="text-xs text-slate-400">Persentase manajemen villa</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="px-4 py-1.5 bg-emerald-50 text-emerald-700 rounded-full font-bold text-xs border border-emerald-100 uppercase tracking-tight">
                                    {{ $h->service_karyawan }}% Service
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2 text-sm font-medium text-slate-600">
                                    <i class="far fa-calendar-alt text-slate-400"></i>
                                    {{ $h->mulai_berlaku->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-4">
                                    <button wire:click="edit({{ $h->id }})" class="text-sm font-bold text-blue-600 hover:text-blue-800 transition">Edit</button>
                                    <button wire:click="delete({{ $h->id }})" 
                                            onclick="confirm('Hapus riwayat ini?') || event.stopImmediatePropagation()"
                                            class="text-sm font-bold text-red-400 hover:text-red-600 transition">Hapus</button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <i class="fas fa-history text-slate-200 text-5xl mb-4"></i>
                                <p class="text-slate-500 font-medium text-base">Belum ada riwayat fee yang tercatat.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>