@php
    $routeTitles = [
        // GANTI DARI 'admin.xxx' KE 'master.xxx'
        'master.dashboard' => 'Dashboard',
        'master.kelola.villa' => 'Kelola Villa',
        'master.pendapatan' => 'Pendapatan',
        'master.pengeluaran' => 'Pengeluaran',
        'master.laporan' => 'Laporan',
        'master.history.user' => 'History User',
    ];

    // Gunakan fungsi Route::currentRouteName() dari Laravel untuk mendapatkan nama rute yang sedang aktif
    // Sertakan pengecekan namespace (use Illuminate\Support\Facades\Route;) jika ini adalah layout utama.
    use Illuminate\Support\Facades\Route; 
    
    $currentRoute = Route::currentRouteName() ?? 'master.dashboard'; 
    $pageTitle = $routeTitles[$currentRoute] ?? ($title ?? 'Dashboard');
@endphp

<!DOCTYPE html>
<html lang="{{ session('locale', 'id') }}">

<head>
    <meta charset="UTF-8">
    <title>Villa Management - {{ $pageTitle }}</title>
    <link rel="icon" type="image/png" href="/images/simpleLogo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Ikon Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite('resources/css/app.css')
    @livewireStyles
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- SCRIPT BARU DITAMBAHKAN DI SINI UNTUK MEMPERBAIKI ERROR $currentRoute --}}
    <script>
        // Mendefinisikan variabel global $currentRoute untuk Alpine/JS
        // Ini mengatasi Uncaught ReferenceError saat menu mobile diproses
        window.$currentRoute = '{{ $currentRoute }}';
    </script>
    {{-- AKHIR SCRIPT BARU --}}

    <style>
        .sidebar-active { 
            background-color: #f7d9a1; 
            color: #7c5c29; 
            font-weight: 600;
        }
        .sidebar-icon-active {
            color: #7c5c29;
        }
        /* Mengatasi masalah Trix jika digunakan */
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="bg-gray-100 flex h-screen overflow-hidden font-jakarta" x-data="{ collapsed: false }">

    <aside x-data="{ activeMenu: '{{ $currentRoute }}' }" :class="collapsed ? 'md:w-20' : 'md:w-64'"
        class="bg-secondary shadow-2xl hidden md:flex flex-col transition-all duration-300 h-full fixed left-0 top-0 z-30 border-r border-gray-200">
        
        {{-- Logo dan Nama Sistem (Tidak Ada Perubahan) --}}
        <div class="flex items-center p-4 h-[65px] border-b border-gray-200">
            {{-- LOGO PLACEHOLDER/IKON (Terlihat saat dilipat) --}}
            <i class="fa-solid fa-hotel text-xl text-white" x-show="collapsed" x-transition.opacity></i>
            <span class="text-lg font-bold text-white whitespace-nowrap ml-2" x-show="!collapsed" x-transition.opacity>Villa Management</span>
            {{-- Tombol Collapse --}}
            <button @click="collapsed = !collapsed" 
                :class="collapsed ? 'mx-auto' : 'ml-auto'" 
                class="text-white p-2 rounded-full hover:bg-gray-100 transition-all duration-300">
                <i class="fa-solid" :class="collapsed ? 'fa-angles-right' : 'fa-bars'"></i>
            </button>
        </div>

        {{-- Navigation Menu --}}
        <div class="flex-1 overflow-y-auto pt-2 pb-4 space-y-6 mt-5">

            <div x-data="{ openVilla: false }">

    <!-- BUTTON UTAMA -->
    <button 
        @click="openVilla = !openVilla"
        class="flex items-center p-2 mx-4 text-md font-medium text-white rounded-lg transition w-full pr-10"
        :class="openVilla ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'"
    >
        <i class="fa-solid fa-house-chimney w-5 h-5 mr-3"
           :class="openVilla ? 'sidebar-icon-active' : 'text-white'"></i>

        <span x-show="!collapsed" x-transition.opacity>Pilih Villa</span>

        <!-- Arrow -->
        <i class="fa-solid fa-chevron-down ml-auto text-white transition-transform"
           :class="openVilla ? 'rotate-180' : ''"></i>
    </button>

    <!-- SUBMENU -->
    <div 
        x-show="openVilla" 
        x-transition 
        class="ml-10 mt-1 space-y-1"
        x-cloak
    >

        <!-- Dummy villa -->
        <a href="#"
           class="flex items-center p-2 text-sm font-medium text-white rounded-lg transition hover:bg-gray-100 hover:text-black">
            <i class="fa-solid fa-circle w-3 h-3 mr-3"></i>
            Villa A
        </a>

        <a href="#"
           class="flex items-center p-2 text-sm font-medium text-white rounded-lg transition hover:bg-gray-100 hover:text-black">
            <i class="fa-solid fa-circle w-3 h-3 mr-3"></i>
            Villa B
        </a>

        <a href="#"
           class="flex items-center p-2 text-sm font-medium text-white rounded-lg transition hover:bg-gray-100 hover:text-black">
            <i class="fa-solid fa-circle w-3 h-3 mr-3"></i>
            Villa C
        </a>

    </div>

</div>


           
        <a href="{{ route('master.dashboard') }}" 
           class="flex items-center p-2 mx-4 text-md font-medium text-white rounded-lg transition"
           :class="activeMenu === 'master.dashboard' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
            <i class="fa-solid fa-money-bill-transfer w-5 h-5 mr-3" :class="activeMenu === 'master.dashboard' ? 'sidebar-icon-active' : 'text-white'"></i>
            <span x-show="!collapsed" x-transition.opacity>Dashboard</span>
        </a>

            {{-- Menu Item Lain --}}
        <a href="{{ route('master.pendapatan') }}" 
           class="flex items-center p-2 mx-4 text-md font-medium text-white rounded-lg transition"
           :class="activeMenu === 'master.pendapatan' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
            <i class="fa-solid fa-sack-dollar w-5 h-5 mr-3" :class="activeMenu === 'master.pendapatan' ? 'sidebar-icon-active' : 'text-white'"></i>
            <span x-show="!collapsed" x-transition.opacity>Pendapatan</span>
        </a>
        
        <a href="{{ route('master.pengeluaran') }}" 
           class="flex items-center p-2 mx-4 text-md font-medium text-white rounded-lg transition"
           :class="activeMenu === 'master.pengeluaran' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
            <i class="fa-solid fa-money-bill-transfer w-5 h-5 mr-3" :class="activeMenu === 'master.pengeluaran' ? 'sidebar-icon-active' : 'text-white'"></i>
            <span x-show="!collapsed" x-transition.opacity>Pengeluaran</span>
        </a>
        
        <a href="{{ route('master.kelola.villa') }}" 
           class="flex items-center p-2 mx-4 text-md font-medium text-white rounded-lg transition"
           :class="activeMenu === 'master.kelola.villa' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
            <i class="fa-solid fa-list-check w-5 h-5 mr-3" :class="activeMenu === 'master.kelola.villa' ? 'sidebar-icon-active' : 'text-white'"></i>
            <span x-show="!collapsed" x-transition.opacity>Kelola Villa</span>
        </a>
        
        <a href="{{ route('master.laporan') }}" 
           class="flex items-center p-2 mx-4 text-md font-medium text-white rounded-lg transition"
           :class="activeMenu === 'master.laporan' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
            <i class="fa-solid fa-chart-pie w-5 h-5 mr-3" :class="activeMenu === 'master.laporan' ? 'sidebar-icon-active' : 'text-white'"></i>
            <span x-show="!collapsed" x-transition.opacity>Laporan</span>
        </a>
        
        <a href="{{ route('master.history.user') }}" 
           class="flex items-center p-2 mx-4 text-md font-medium text-white rounded-lg transition"
           :class="activeMenu === 'master.history.user' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
            <i class="fa-solid fa-clock-rotate-left w-5 h-5 mr-3" :class="activeMenu === 'master.history.user' ? 'sidebar-icon-active' : 'text-white'"></i>
            <span x-show="!collapsed" x-transition.opacity>History User</span>
        </a>
        </div>
        
    </aside>

    <main class="flex-1 overflow-auto transition-all duration-300"
        :class="{ 
            'md:ml-20': collapsed,  /* Margin 80px saat dilipat */
            'md:ml-64': !collapsed, /* Margin 256px saat terbuka */
            'ml-0': true                   /* Margin 0 di mobile (di bawah md) */
        }"
    >
        
        {{-- Header --}}
        <div class="sticky top-0 bg-white shadow-md p-4 h-[65px] border-b border-gray-200 flex justify-between items-center z-20">
            
            {{-- Mobile Sidebar Toggle & Page Title (Gunakan @click="openSideBar = !openSideBar" untuk mobile sidebar) --}}
            <div class="flex items-center">
                <div x-data="{ openSideBar: false }" class="relative md:hidden">
                    {{-- Toggle Button untuk Mobile --}}
                    <button @click="openSideBar = !openSideBar" class="p-2 mr-3 text-gray-500">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>

                    <div x-show="openSideBar" @click="openSideBar = false" x-transition.opacity class="fixed inset-0 bg-gray-900/50 z-40"></div>
                    <aside x-show="openSideBar" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="-translate-x-full"
                        class="fixed top-0 left-0 h-full w-64 z-50 bg-white shadow-lg flex flex-col overflow-y-auto">

                        {{-- Mobile Header --}}
                        <div class="flex justify-between items-center p-4 border-b border-gray-200 bg-secondary">
                            <h1 class="text-white text-xl font-semibold">Villa Management</h1>
                            <button @click="openSideBar = false" class="text-white p-2 rounded-full hover:bg-white/20">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        {{-- Mobile Navigation (Daftar Rata Tanpa Grup Villa Jimbaran) --}}
                        <div class="flex-1 overflow-y-auto pt-2 pb-4 space-y-2">
                            
                            {{-- Dashboard Item --}}
                            <a href="{{ route('master.dashboard') }}" 
                            class="flex items-center p-2 mx-4 text-md font-medium text-gray-700 rounded-lg transition"
                            :class="$currentRoute == 'master.dashboard' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
                                {{-- Menggunakan ikon yang sama seperti menu lain (opsional, bisa diganti fa-grip-vertical jika mau) --}}
                                <i class="fa-solid fa-house w-5 h-5 mr-3" 
                                :class="$currentRoute == 'master.dashboard' ? 'sidebar-icon-active' : 'text-amber-600'"></i>
                                Dashboard
                            </a>
                            
                            {{-- Menu Item Pendapatan --}}
                            <a href="{{ route('master.pendapatan') }}" 
                            class="flex items-center p-2 mx-4 text-md font-medium text-gray-700 rounded-lg transition"
                            :class="$currentRoute == 'master.pendapatan' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
                                <i class="fa-solid fa-sack-dollar w-5 h-5 mr-3" :class="$currentRoute == 'master.pendapatan' ? 'sidebar-icon-active' : 'text-amber-600'"></i>
                                Pendapatan
                            </a>
                            
                            {{-- Menu Item Pengeluaran --}}
                            <a href="{{ route('master.pengeluaran') }}" 
                            class="flex items-center p-2 mx-4 text-md font-medium text-gray-700 rounded-lg transition"
                            :class="$currentRoute == 'master.pengeluaran' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
                                <i class="fa-solid fa-money-bill-transfer w-5 h-5 mr-3" :class="$currentRoute == 'master.pengeluaran' ? 'sidebar-icon-active' : 'text-amber-600'"></i>
                                Pengeluaran
                            </a>
                            
                            {{-- Menu Item Kelola Villa --}}
                            <a href="{{ route('master.kelola.villa') }}" 
                            class="flex items-center p-2 mx-4 text-md font-medium text-gray-700 rounded-lg transition"
                            :class="$currentRoute == 'master.kelola.villa' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
                                <i class="fa-solid fa-list-check w-5 h-5 mr-3" :class="$currentRoute == 'master.kelola.villa' ? 'sidebar-icon-active' : 'text-amber-600'"></i>
                                Kelola Villa
                            </a>
                            
                            {{-- Menu Item Laporan --}}
                            <a href="{{ route('master.laporan') }}" 
                            class="flex items-center p-2 mx-4 text-md font-medium text-gray-700 rounded-lg transition"
                            :class="$currentRoute == 'master.laporan' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
                                <i class="fa-solid fa-chart-pie w-5 h-5 mr-3" :class="$currentRoute == 'master.laporan' ? 'sidebar-icon-active' : 'text-amber-600'"></i>
                                Laporan
                            </a>
                            
                            {{-- Menu Item History User --}}
                            <a href="{{ route('master.history.user') }}" 
                            class="flex items-center p-2 mx-4 text-md font-medium text-gray-700 rounded-lg transition"
                            :class="$currentRoute == 'master.history.user' ? 'sidebar-active text-amber-800' : 'hover:bg-gray-100'">
                                <i class="fa-solid fa-clock-rotate-left w-5 h-5 mr-3" :class="$currentRoute == 'master.history.user' ? 'sidebar-icon-active' : 'text-amber-600'"></i>
                                History User
                            </a>
                        </div>
                    </aside>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ $pageTitle }}
                </h2>
            </div>
            
            {{-- User & Notification (Konten tidak diubah) --}}
            <div class="flex items-center">
                
                <div class="hidden md:block h-6 w-px bg-gray-300 mx-4"></div>
                
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="px-2 py-1 rounded-lg hover:bg-gray-100 transition-all font-semibold flex items-center">
                        <span class="hidden lg:inline text-sm">
                            Halo, **{{ auth()->user()->name ?? 'Master Admin' }}**
                            <i class="fa-solid fa-angle-down ml-2 text-xs"></i>
                        </span>
                        <span class="lg:hidden">
                            <i class="fa-solid fa-user-circle text-gray-600 text-2xl"></i>
                        </span>
                    </button>

                    <div x-show="open" @click.away="open = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50 origin-top-right">

                        <a href="#" 
                            class="flex items-center px-4 py-3 text-md text-white hover:bg-gray-100 transition-all">
                            <i class="fa-solid fa-user-gear mr-3 w-4"></i>
                            Profile Settings
                        </a>
                        <a href="{{ route('logout') }}" 
                            class="flex items-center px-4 py-3 text-md text-gray-700 hover:bg-gray-100 transition-all">
                            <i class="fa-solid fa-right-from-bracket mr-3 w-4 text-gray-500"></i>
                            Sign Out
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="p-6">
            {{ $slot ?? '' }}
        </div>
    </main>

    @livewireScripts

</body>
</html>