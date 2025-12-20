{{-- Satu-satunya Root Element --}}
<div> 
    <div class="space-y-6">
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                    Kelola <span class="text-amber-600">Akun Pengguna</span>
                </h1>
                <p class="text-sm text-slate-500">
                    Manajemen akses, role, dan hak akses modul untuk seluruh staf.
                </p>
            </div>
            <a href="{{ route('master.manageakun.create-user') }}"
               class="inline-flex items-center justify-center gap-2 py-3 px-6 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition-all active:scale-95">
               <i class="fas fa-user-plus"></i>
               <span>Tambah Akun Baru</span>
            </a>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session()->has('success'))
            <div x-data="{show:true}" x-init="setTimeout(()=>show=false,3000)" x-show="show"
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        {{-- FILTER & SEARCH --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-end gap-4">
                <div class="w-full md:w-1/2">
                    <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Cari Pengguna</label>
                    <div class="relative mt-1">
                        <input wire:model.live.debounce.300ms="search" type="text" 
                               placeholder="Nama, Username, Email, atau Role..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-slate-400 text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="w-full md:w-auto">
                    <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider ml-1">Tampilkan</label>
                    <select wire:model.live="paginate"
                        class="w-full mt-1 px-4 py-2.5 border border-slate-300 rounded-lg focus:ring-2 focus:ring-amber-500 text-sm bg-white font-medium">
                        <option value="10">10 Data</option>
                        <option value="20">20 Data</option>
                        <option value="50">50 Data</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- TABEL PENGGUNA --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Identitas Pengguna</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Role & Akses</th>
                            <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Penempatan Villa</th>
                            <th class="px-6 py-4 text-center text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold mr-3">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-800">{{ $user->name }}</div>
                                            <div class="text-xs text-slate-500">@<span>{{ $user->username }}</span></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-[10px] font-bold rounded-md border uppercase tracking-tighter
                                        @if($user->role == 'owner') bg-emerald-50 text-emerald-700 border-emerald-100
                                        @elseif($user->role == 'staf_master') bg-indigo-50 text-indigo-700 border-indigo-100
                                        @else bg-amber-50 text-amber-700 border-amber-100 @endif">
                                        {{ str_replace('_', ' ', $user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-slate-600">{{ $user->villa->nama_villa ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex justify-center gap-2">
                                        <button wire:click="editUser({{ $user->id }})" class="px-3 py-1 text-xs font-bold text-amber-600 border border-amber-200 rounded-lg hover:bg-amber-600 hover:text-white transition">Edit</button>
                                        <button wire:click="deleteUser({{ $user->id }})" onclick="confirm('Apakah Anda yakin ingin menghapus akun {{ $user->name }}?') || event.stopImmediatePropagation()"
                                             class="px-3 py-1 text-xs font-bold text-red-600 border border-red-200 rounded-lg hover:bg-red-600 hover:text-white transition">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        </div>

        {{-- MODAL UPDATE --}}
       {{-- MODAL UPDATE --}}
@if($updateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>
        
        {{-- Modal Content --}}
        <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 z-10 overflow-hidden animate-in zoom-in-95 duration-200">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Edit Akun Pengguna</h2>
                <button wire:click="closeModal" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form wire:submit.prevent="update" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 h-[65vh] overflow-y-auto px-2 custom-scrollbar">
                    
                    {{-- Nama Lengkap --}}
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-600 ml-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-2.5">
                        @error('name') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Role Akses --}}
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-600 ml-1">Role Akses</label>
                        <select wire:model.live="role" class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-2.5 font-semibold">
                            <option value="">-- Pilih Role --</option>
                            <option value="owner">Owner</option>
                            <option value="staf_master">Staf Master</option>
                            <option value="staf">Staf Villa</option>
                        </select>
                        @error('role') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Username (Readonly biasanya lebih aman untuk edit) --}}
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-600 ml-1">Username</label>
                        <input type="text" wire:model="username" class="w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 text-sm py-2.5 cursor-not-allowed" readonly>
                        @error('username') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Access Code (PIN 6 Digit) --}}
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-600 ml-1">Access Code (PIN 6 Digit)</label>
                        <input type="number" wire:model="access_code" maxlength="6" 
                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" 
                            placeholder="PIN 6 Digit" 
                            class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-2.5 font-mono tracking-widest font-bold">
                        @error('access_code') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Alamat Email --}}
                    <div class="space-y-1">
                        <label class="text-sm font-bold text-slate-600 ml-1">Alamat Email</label>
                        <input type="email" wire:model="email" class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-2.5">
                        @error('email') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>

                    {{-- Penempatan Villa --}}
                    @if(in_array($role, ['owner', 'staf']))
                    <div class="space-y-1 animate-in slide-in-from-left-2">
                        <label class="text-sm font-bold text-slate-600 ml-1">Penempatan Villa</label>
                        <select wire:model="villa_id" class="w-full rounded-xl border-slate-200 focus:ring-amber-500 focus:border-amber-500 text-sm py-2.5 font-semibold">
                            <option value="">-- Pilih Villa --</option>
                            @foreach($listVilla as $v)
                                <option value="{{ $v->id }}">{{ $v->nama_villa }}</option>
                            @endforeach
                        </select>
                        @error('villa_id') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                    </div>
                    @endif

                    {{-- Ubah Password --}}
                    <div class="md:col-span-2 p-4 bg-amber-50 rounded-2xl border border-amber-100 mt-2">
                        <p class="text-xs font-black text-amber-600 uppercase mb-3 tracking-wider">Ubah Password (Opsi)</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <input type="password" wire:model="newPassword" placeholder="Password Baru" class="rounded-xl border-slate-200 focus:ring-amber-500 text-sm py-2.5">
                            <input type="password" wire:model="newPassword_confirmation" placeholder="Konfirmasi Password" class="rounded-xl border-slate-200 focus:ring-amber-500 text-sm py-2.5">
                        </div>
                        <p class="text-[10px] text-amber-500 mt-2 italic">*Kosongkan jika tidak ingin mengganti password.</p>
                        @error('newPassword') <span class="text-xs text-red-500 font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    {{-- Hak Akses (Permissions) --}}
                    @if(in_array($role, ['owner', 'staf', 'staf_master']))
                    <div class="md:col-span-2 mt-4 animate-in fade-in">
                        <label class="text-sm font-bold text-slate-600 block mb-3 ml-1 tracking-tight">Hak Akses Modul</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach(['pendapatan', 'pengeluaran'] as $modul)
                            <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50/50">
                                <p class="text-xs font-black text-slate-700 uppercase mb-3 border-b border-slate-200 pb-2 flex items-center gap-2">
                                    <i class="fas fa-{{ $modul == 'pendapatan' ? 'wallet' : 'file-invoice-dollar' }} text-amber-600"></i>
                                    {{ $modul }}
                                </p>
                                <div class="flex gap-5">
                                    @foreach(['create', 'update', 'delete'] as $act)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="checkbox" wire:model="permissions.{{ $modul }}.{{ $act }}" class="w-4 h-4 rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                        <span class="text-[10px] font-black text-slate-500 uppercase group-hover:text-slate-800 transition">{{ $act }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-slate-100">
                    <button type="button" wire:click="closeModal" class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-8 py-2.5 text-sm font-bold bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition shadow-lg shadow-amber-900/20 active:scale-95 flex items-center gap-2">
                        <span wire:loading.remove wire:target="update">Simpan Perubahan</span>
                        <span wire:loading wire:target="update"><i class="fas fa-circle-notch fa-spin"></i> Memproses...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
    </div>

    {{-- Masukkan Style ke dalam div yang sama agar tidak dianggap sebagai root element baru --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</div> 
{{-- Selesai div utama --}}