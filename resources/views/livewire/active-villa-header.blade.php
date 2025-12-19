<div class="mt-3 grid grid-cols-2 gap-3 px-1">
    {{-- Info Role --}}
    <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100 shadow-sm">
        <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center">
            <i class="fa-solid fa-user-shield text-teal-600 text-sm"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] uppercase font-black text-slate-400 leading-none mb-1 tracking-wider">Role</p>
            <p class="text-sm font-bold text-slate-700 capitalize truncate">
                {{ str_replace('_', ' ', auth()->user()->role) }}
            </p>
        </div>
    </div>

    {{-- Info Villa Aktif --}}
    @if(session('villa_id'))
        <div class="flex items-center gap-3 bg-amber-50/50 p-3 rounded-xl border border-amber-100 shadow-sm">
            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                {{-- Ikon Rumah Villa --}}
                <i class="fa-solid fa-house-chimney text-amber-600 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-black text-amber-500 leading-none mb-1 tracking-wider">Villa</p>
                <p class="text-sm font-bold text-slate-800 truncate">
                    {{ $activeVillaName }}
                </p>
            </div>
        </div>
    @else
        <div class="flex items-center gap-3 bg-red-50 p-3 rounded-xl border border-red-100 shadow-sm">
            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
              <i class="fa-solid fa-triangle-exclamation text-red-600 text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] uppercase font-black text-red-400 leading-none mb-1">Status</p>
                <p class="text-sm font-bold text-red-600 truncate">
                    Kosong
                </p>
            </div>
        </div>
    @endif
</div>