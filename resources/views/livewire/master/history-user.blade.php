<div class="space-y-6">
    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">
                Riwayat <span class="text-amber-600">Aktivitas</span>
            </h1>
            <p class="text-sm text-slate-500 hidden md:block">
                Pantau seluruh log aktivitas pengguna untuk menjaga keamanan data.
            </p>
        </div>
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest bg-slate-100 px-4 py-2 rounded-lg border border-slate-200">
                Total Log: <span class="text-slate-800">{{ $activities->total() }}</span>
            </span>
        </div>
    </div>

    {{-- FILTER DATA --}}
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Pencarian --}}
            <div class="md:col-span-2">
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Cari Aktivitas</label>
                <div class="relative mt-1">
                    <input wire:model.live.debounce.300ms="search" type="text" 
                           placeholder="Cari nama user atau jenis aktivitas..."
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-slate-300 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-slate-400 text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- Filter Bulan --}}
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Bulan</label>
                <select wire:model.live="selectedMonth" 
                        class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 focus:ring-amber-500 text-sm bg-white">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">{{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>

            {{-- Filter Tahun --}}
            <div>
                <label class="text-[10px] font-bold uppercase text-slate-400 tracking-wider">Tahun</label>
                <select wire:model.live="selectedYear" 
                        class="w-full mt-1 px-3 py-2 rounded-lg border border-slate-300 focus:ring-amber-500 text-sm bg-white">
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- TABEL AKTIVITAS --}}
    <div class="bg-white overflow-hidden rounded-xl border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Aktivitas</th>
                        <th class="px-6 py-4 text-left text-[11px] font-bold text-slate-500 uppercase tracking-wider">Target Data</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    @forelse ($activities as $activity)
                        <tr class="hover:bg-amber-50/30 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-slate-700">
                                    {{ $activity->created_at->timezone('Asia/Makassar')->format('d M Y') }}
                                </div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $activity->created_at->timezone('Asia/Makassar')->format('H:i:s') }} WITA
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold text-xs mr-3">
                                        {{ substr($activity->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">{{ $activity->user->name ?? 'User Dihapus' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-bold rounded-md border 
                                    @if(($activity->user->role ?? '') == 'master') bg-red-50 text-red-700 border-red-100
                                    @elseif(($activity->user->role ?? '') == 'staf_master') bg-indigo-50 text-indigo-700 border-indigo-100
                                    @elseif(($activity->user->role ?? '') == 'owner') bg-emerald-50 text-emerald-700 border-emerald-100
                                    @else bg-slate-50 text-slate-600 border-slate-100 @endif uppercase tracking-tighter">
                                    {{ str_replace('_', ' ', $activity->user->role ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center text-[11px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-100 uppercase tracking-tight shadow-sm" 
                                    title="{{ $activity->activity_type }}">
                                    {{ Str::limit($activity->activity_type, 35, '...') }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $modelName = Str::afterLast($activity->loggable_type, '\\');
                                    $targetId = $activity->loggable_id;
                                @endphp
                                <div class="text-xs text-slate-500">
                                    <span class="font-medium text-slate-700">{{ $modelName }}</span>
                                    <span class="block text-[10px] italic">ID Reference: #{{ $targetId }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-history text-slate-200 text-5xl mb-4"></i>
                                    <p class="text-slate-400 italic">Tidak ada rekaman aktivitas ditemukan pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $activities->links() }}
        </div>
    </div>
</div>