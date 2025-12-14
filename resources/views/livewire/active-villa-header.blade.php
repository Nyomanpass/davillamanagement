<div class="mt-1">
    @if(session('villa_id'))
        <p class="text-sm text-gray-500">
            Villa Aktif:
            <span class="font-semibold text-[#C8A97E]">
                {{ $activeVillaName }}
            </span>
        </p>
    @else
        <p class="text-sm text-red-500">
            {{ $activeVillaName }}
        </p>
    @endif
</div>
