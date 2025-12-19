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
                                        <button wire:click="deleteUser({{ $user->id }})" class="px-3 py-1 text-xs font-bold text-red-600 border border-red-200 rounded-lg hover:bg-red-600 hover:text-white transition">Hapus</button>
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
        @if($updateModal)
            {{-- Isi modal tetap sama seperti sebelumnya --}}
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="closeModal"></div>
                <div class="relative bg-white w-full max-w-2xl rounded-2xl shadow-2xl border border-slate-200 z-10 p-6">
                    {{-- Form Anda di sini... --}}
                    <h2 class="text-xl font-bold mb-4">Edit Akun</h2>
                    {{-- (Salin sisa form modal dari pesan sebelumnya ke sini) --}}
                    <button wire:click="closeModal" class="mt-4 text-slate-500">Tutup</button>
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