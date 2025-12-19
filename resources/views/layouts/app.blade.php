<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DavillaManagement</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   
    @livewireStyles
</head>

<body class="bg-[#f6f4ef] flex text-gray-800">

    {{-- SIDEBAR --}}
    <aside class="w-64 h-screen fixed left-0 top-0
        bg-gradient-to-b from-[#8c6c3e] to-[#C8A97E]
        text-white shadow-xl">

        <div class="p-6 border-b border-white/20">
           <img src="/images/logodua.png" alt="">
        </div>

        @php
            $menuBase = "flex items-center gap-3 p-3 rounded-xl transition text-base font-semibold";
            $active = "bg-white/25";
            $hover = "hover:bg-white/15";
            $showLaporanSub = false; // state untuk toggle
        @endphp

<nav class="mt-6 px-4 space-y-1.5">
    @php
        $role = auth()->user()->role ?? null;
        
        // 1. Tentukan PREFIX URL berdasarkan role
        $isMasterLevel = in_array($role, ['master', 'staf_master']);
        $urlPrefix = $isMasterLevel ? 'master' : 'villa';
        
        // 2. Tentukan izin untuk menampilkan menu terbatas
        $isMasterAdmin = ($role === 'master');
        $isLimitedUser = in_array($role, ['staf_master', 'owner', 'staf']);

        // Variabel Izin
        $canAccessFinance = $isMasterAdmin || $isLimitedUser || auth()->user()->hasPermissionTo('pendapatan'); 
        $canAccessLaporan = $isMasterAdmin || $isLimitedUser || auth()->user()->hasPermissionTo('laporan');

        // Style Helper (Pastikan variabel ini sudah didefinisikan di component)
        // $menuBase = "flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all";
    @endphp

    {{-- 1. DASHBOARD --}}
    <a href="{{ url($urlPrefix . '/dashboard') }}"
    class="{{ $menuBase }} {{ request()->is($urlPrefix . '/dashboard') ? $active : $hover }}">
        <i class="fa-solid fa-house w-5 text-center text-base"></i>
        <span>Dashboard</span>
    </a>

    {{-- 2. PENDAPATAN --}}
    @if ($canAccessFinance)
        <a href="{{ url($urlPrefix . '/pendapatan') }}"
        class="{{ $menuBase }} {{ request()->is($urlPrefix . '/pendapatan*') ? $active : $hover }}">
            <i class="fa-solid fa-wallet w-5 text-center text-base"></i>
            <span>Pendapatan</span>
        </a>
    @endif

    {{-- 3. PENGELUARAN --}}
    @if ($canAccessFinance)
        <a href="{{ url($urlPrefix . '/pengeluaran') }}"
        class="{{ $menuBase }} {{ request()->is($urlPrefix . '/pengeluaran*') ? $active : $hover }}">
            <i class="fa-solid fa-cart-shopping w-5 text-center text-base"></i>
            <span>Pengeluaran</span>
        </a>
    @endif

    {{-- 4. LAPORAN (Dropdown) --}}
    @if ($canAccessLaporan)
        <div x-data="{ open: {{ request()->is($urlPrefix . '/laporan*') ? 'true' : 'false' }} }" class="relative">
             <button @click="open = !open"
                class="{{ $menuBase }} {{ request()->is($urlPrefix . '/laporan*') ? $active : $hover }} flex items-center justify-between w-full text-left">
                <span class="flex items-center gap-3">
                    <i class="fa-solid fa-chart-pie w-5 text-center text-base"></i>
                    <span>Laporan</span>
                </span>
                <i :class="{ 'rotate-180': open }" class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300"></i>
            </button>

            <div x-show="open" 
    x-cloak
    x-collapse
    class="mt-2 ml-6 space-y-2 border-l border-white/20"> {{-- Garis pinggir tipis transparan --}}
    
    {{-- Laporan Villa --}}
    <a href="{{ url($urlPrefix . '/laporan/') }}" 
       class="flex items-center py-2 px-4 text-sm font-semibold transition-all
       {{ request()->is($urlPrefix . '/laporan') ? 'text-amber-400' : 'text-white' }}">
       
       {{-- Ikon Box Chart untuk Laporan Villa --}}
       <i class="fa-solid fa-chart-column text-[12px] mr-3 {{ request()->is($urlPrefix . '/laporan') ? 'text-amber-400' : 'text-white/60' }}"></i>
       Laporan Villa
    </a>
    <a href="{{ url($urlPrefix . '/laporan/okupansi') }}" 
       class="flex items-center py-2 px-4 text-sm font-semibold transition-all
       {{ request()->is($urlPrefix . '/laporan/okupansi*') ? 'text-amber-400' : 'text-white' }}">
       {{-- Ikon Bed atau Door Open untuk Okupansi --}}
       <i class="fa-solid fa-bed text-[12px] mr-3 {{ request()->is($urlPrefix . '/laporan/okupansi*') ? 'text-amber-400' : 'text-white/60' }}"></i>
       Okupansi Villa
    </a>

    {{-- Fee Manajemen (Hanya untuk Master) --}}
    @if ($isMasterAdmin)
        <a href="{{ url($urlPrefix . '/laporan/fee-manajemen') }}" 
           class="flex items-center py-2 px-4 text-sm font-semibold transition-all
           {{ request()->is($urlPrefix . '/laporan/fee-manajemen*') ? 'text-amber-400' : 'text-white' }}">
           
           {{-- Ikon Coins untuk Fee Manajemen --}}
           <i class="fa-solid fa-coins text-[12px] mr-3 {{ request()->is($urlPrefix . '/laporan/fee-manajemen*') ? 'text-amber-400' : 'text-white/60' }}"></i>
           Fee Manajemen
        </a>
    @endif
</div>
        </div>
    @endif
    
    <div class="my-4 border-t border-slate-100 mx-2"></div>

    {{-- 5. KELOLA VILLA (Hanya untuk Master) --}}
    @if ($isMasterAdmin)
        <a href="{{ url($urlPrefix . '/kelola-villa') }}"
        class="{{ $menuBase }} {{ request()->is($urlPrefix . '/kelola-villa*') ? $active : $hover }}">
            <i class="fa-solid fa-villas w-5 text-center text-base fa-hotel"></i>
            <span>Kelola Villa</span>
        </a>
    @endif

    {{-- 6. LOG AKTIVITAS (Hanya untuk Master) --}}
    @if ($isMasterAdmin)
        <a href="{{ url($urlPrefix . '/history-user') }}"
        class="{{ $menuBase }} {{ request()->is($urlPrefix . '/history-user*') ? $active : $hover }}">
            <i class="fa-solid fa-clock-rotate-left w-5 text-center text-base"></i>
            <span>Log Aktivitas</span>
        </a>
    @endif

    {{-- 7. KELOLA AKUN --}}
    @if ($isMasterAdmin)
        <a href="{{ url($urlPrefix . '/akun/kelola') }}"
        class="{{ $menuBase }} {{ request()->is($urlPrefix . '/akun/kelola*') ? $active : $hover }}">
            <i class="fa-solid fa-user-gear w-5 text-center text-base"></i>
            <span>Kelola Akun</span>
        </a>
    @endif

    {{-- 8. SETTING KATEGORI --}}
    @if ($isMasterAdmin)
        <a href="{{ url($urlPrefix . '/settings/categories') }}"
        class="{{ $menuBase }} {{ request()->is($urlPrefix . '/settings/categories*') ? $active : $hover }}">
            <i class="fa-solid fa-tags w-5 text-center text-base"></i>
            <span>Setting Kategori</span>
        </a>
    @endif

</nav>
    </aside>


    {{-- MAIN CONTENT --}}
    <div class="flex-1 ml-64 min-h-screen">

        {{-- HEADER --}}
     <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center border-b">
    <div>
       

        {{-- VILLA AKTIF (Panggil Komponen Livewire Baru) --}}
        @livewire('active-villa-header')
        
    </div>

    <div class="mt-4 px-1">
    <a href="/logout" 
       class="flex items-center justify-center gap-3 w-full py-3 px-4 bg-red-50 text-red-600 rounded-xl border border-red-100 font-bold text-sm transition-all duration-300 hover:bg-red-600 hover:text-white hover:shadow-lg group">
        
        {{-- Ikon Logout --}}
        <i class="fa-solid fa-right-from-bracket text-sm transition-transform group-hover:-translate-x-1"></i>
        
        <span>Keluar Aplikasi</span>
    </a>
</div>
</header>
        {{-- PAGE CONTENT --}}
        <main class="p-6">
            <div class="bg-white rounded-xl shadow-sm p-6">
                {{ $slot }}
            </div>
        </main>

    </div>
    @livewireScripts

      @stack('scripts')

</body>


</html>
