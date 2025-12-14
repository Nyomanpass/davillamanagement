<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>DavillaManagement</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    
   
    @livewireStyles
</head>

<body class="bg-[#f6f4ef] flex text-gray-800">

    {{-- SIDEBAR --}}
    <aside class="w-64 h-screen fixed left-0 top-0
        bg-gradient-to-b from-[#8c6c3e] to-[#C8A97E]
        text-white shadow-xl">

        <div class="p-6 border-b border-white/20">
            <h1 class="text-xl font-bold tracking-wide">
                Davilla Management
            </h1>
            <p class="text-xs text-white/80 mt-1">
                Villa Control System
            </p>
        </div>

        @php
            $menuBase = "flex items-center gap-3 p-3 rounded-xl transition text-base font-semibold";
            $active = "bg-white/25";
            $hover = "hover:bg-white/15";
            $showLaporanSub = false; // state untuk toggle
        @endphp

        <nav class="mt-6 px-4 space-y-2">

            {{-- DASHBOARD --}}
            <a href="{{ url('/master/dashboard') }}"
            class="{{ $menuBase }} {{ request()->is('master/dashboard') ? $active : $hover }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>
                </svg>
                Dashboard
            </a>

            {{-- PENDAPATAN --}}
            <a href="{{ url('/master/pendapatan') }}"
            class="{{ $menuBase }} {{ request()->is('master/pendapatan*') ? $active : $hover }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M12 8c-3.314 0-6 1.79-6 4s2.686 4 6 4 6-1.79 6-4-2.686-4-6-4z"/>
                    <path d="M6 12v4c0 2.21 2.686 4 6 4s6-1.79 6-4v-4"/>
                </svg>
                Pendapatan
            </a>

            {{-- PENGELUARAN --}}
            <a href="{{ url('/master/pengeluaran') }}"
            class="{{ $menuBase }} {{ request()->is('master/pengeluaran*') ? $active : $hover }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M17 9l-5 5-5-5"/>
                    <path d="M12 4v10"/>
                </svg>
                Pengeluaran
            </a>

    
            {{-- LAPORAN --}}
             <!-- Parent Menu -->
            <div x-data="{ open: false }" class="relative">
                 <!-- Parent Menu -->
                <button @click="open = !open"
                    class="{{ $menuBase }} {{ request()->is('master/laporan*') ? $active : $hover }} flex items-center justify-between w-full">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 17v-6M13 17v-4M17 17v-8"/>
                            <path d="M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                        </svg>
                        Laporan
                    </span>
                    <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- Sub-menu -->
                <div x-show="open" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden z-50 absolute">

                    <a href="{{ url('/master/laporan/') }}" 
                    class="block px-5 py-3 text-sm font-medium text-gray-700 hover:bg-secondary/30 hover:text-primary transition-colors">
                    Laporan Villa
                    </a>

                    <a href="{{ url('/master/laporan/fee-manajemen') }}" 
                    class="block px-5 py-3 text-sm font-medium text-gray-700 hover:bg-secondary/30 hover:text-primary  transition-colors">
                    Fee Manajemen
                    </a>
                </div>
            </div>

                 {{-- KELOLA VILLA --}}
            <a href="{{ url('/master/kelola-villa') }}"
            class="{{ $menuBase }} {{ request()->is('master/kelola-villa*') ? $active : $hover }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M3 10l9-7 9 7v10a2 2 0 0 1-2 2h-4v-6h-6v6H5a2 2 0 0 1-2-2z"/>
                </svg>
                Kelola Villa
            </a>



            {{-- HISTORY USER --}}
            <a href="{{ url('/master/history-user') }}"
            class="{{ $menuBase }} {{ request()->is('master/history-user*') ? $active : $hover }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5z"/>
                    <path d="M3 21v-1a7 7 0 0 1 14 0v1"/>
                </svg>
                History User
            </a>

        </nav>
    </aside>


    {{-- MAIN CONTENT --}}
    <div class="flex-1 ml-64 min-h-screen">

        {{-- HEADER --}}
     <header class="bg-white shadow-sm px-6 py-4 flex justify-between items-center border-b">
    <div>
        <h2 class="text-xl font-semibold text-[#8c6c3e]">
            @yield('title', 'Dashboard')
        </h2>

        {{-- VILLA AKTIF (Panggil Komponen Livewire Baru) --}}
        @livewire('active-villa-header')
        
    </div>

    <a href="/logout" class="text-sm font-semibold text-red-600 hover:underline">
        Logout
    </a>
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
