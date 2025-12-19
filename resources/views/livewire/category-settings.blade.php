<div>
    <div class="space-y-6">
        {{-- HEADER --}}
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                Kelola <span class="text-amber-600">Kategori</span>
            </h1>
            <p class="text-sm text-slate-500">
                Atur kategori pendapatan dan pengeluaran secara dinamis.
            </p>
        </div>

        {{-- FORM INPUT CARD --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-sm font-black uppercase tracking-widest text-slate-700">
                    {{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                </h2>
                @if($isEdit)
                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded uppercase">Mode Edit</span>
                @endif
            </div>

            <div class="p-6">
                @if (session()->has('message'))
                    <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show"
                         class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                    <div class="md:col-span-2">
                        <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Nama Kategori</label>
                        <input type="text" wire:model="name"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 text-sm py-2.5"
                            placeholder="Contoh: Room, Laundry, Gaji Staf...">
                        @error('name')
                            <span class="text-[10px] text-red-500 font-bold mt-1 ml-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Jenis</label>
                        <select wire:model="type"
                            class="mt-1 w-full rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 text-sm py-2.5 bg-white">
                            <option value="income">Pendapatan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="flex-1 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-sm font-bold rounded-xl transition-all active:scale-95">
                            {{ $isEdit ? 'Update' : 'Simpan' }}
                        </button>

                        @if($isEdit)
                            <button type="button" wire:click="resetFields"
                                class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-bold rounded-xl transition">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        {{-- DAFTAR KATEGORI TABLE --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-700">Daftar Kategori Terdaftar</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-white border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Nama Kategori</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-400 uppercase tracking-wider">Tipe/Jenis</th>
                            <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($categories as $cat)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="font-bold text-slate-700 group-hover:text-amber-600 transition-colors">{{ $cat->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-left">
                                <span class="px-3 py-1 text-[10px] font-black uppercase tracking-tighter rounded-md border
                                    {{ $cat->type == 'income'
                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                                        : 'bg-red-50 text-red-700 border-red-100' }}">
                                    <i class="fas {{ $cat->type == 'income' ? 'fa-arrow-down' : 'fa-arrow-up' }} mr-1"></i>
                                    {{ $cat->type == 'income' ? 'Pendapatan' : 'Pengeluaran' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <button wire:click="edit({{ $cat->id }})"
                                        class="text-xs font-bold text-amber-600 hover:text-amber-800 transition uppercase tracking-widest">
                                        Edit
                                    </button>
                                    <button
                                        onclick="confirm('Hapus kategori ini?') || event.stopImmediatePropagation()"
                                        wire:click="delete({{ $cat->id }})"
                                        class="text-xs font-bold text-red-400 hover:text-red-600 transition uppercase tracking-widest">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($categories->count() == 0)
                <div class="py-12 text-center">
                    <i class="fas fa-tags text-slate-200 text-4xl mb-3"></i>
                    <p class="text-slate-400 italic text-sm">Belum ada kategori yang dibuat.</p>
                </div>
            @endif
        </div>
    </div>
</div>