<!DOCTYPE html>
<html lang="id" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Gabah</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/logo-sucofindo.png') }}?v=2025">
    <link rel="shortcut icon" href="{{ asset('assets/logo-sucofindo.png') }}?v=2025">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }

        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: #11131f;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #374151;
            border-radius: 4px;
        }
    </style>
    @livewireStyles
</head>

<body class="bg-[#0b0c15] text-white min-h-screen overflow-x-hidden" x-data="{ sidebarOpen: false }">

    <header class="flex items-center justify-between bg-[#11131f] border-b border-gray-800 px-4 py-3 sticky top-0 z-40 shadow-xl">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>
            <div class="flex items-center gap-3">

                <img src="{{ asset('assets/logo-sucofindo.png') }}"
                    alt="Logo Sucofindo"
                    class="h-10 w-auto object-contain">

                <div class="leading-tight hidden md:block">
                    <h1 class="font-bold text-lg tracking-tight text-white">SUCOFINDO</h1>
                    <p class="text-[10px] text-orange-500 font-medium tracking-widest uppercase">Monitoring System</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <livewire:approval-notif />
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-200">
                    {{ optional(Auth::user()->cabang)->name_cabang ?? Auth::user()->name }}
                </p>
                <p class="text-[10px] text-green-500 uppercase font-bold">Online</p>
            </div>
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'User') }}&background=374151&color=fff"
                class="w-9 h-9 rounded-full border border-gray-600 object-cover"
                alt="User Avatar">
        </div>
    </header>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-[#11131f] border-r border-gray-800 transition-transform duration-300 ease-in-out shadow-2xl sidebar-scroll overflow-y-auto" x-cloak>
        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800 bg-[#0b0c15]">
            <span class="text-sm font-bold text-gray-400 tracking-widest uppercase">Navigation</span>
            <button @click="sidebarOpen = false" class="text-gray-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-4 space-y-6">

            @auth
            <div class="bg-[#1a1d2d] rounded-xl p-4 border border-gray-700/50 shadow-inner relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mt-2 -mr-2 w-16 h-16 bg-orange-500/10 rounded-full blur-xl group-hover:bg-orange-500/20 transition-all"></div>

                <div class="flex items-start gap-3 relative z-10">
                    <div class="flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=ea580c&color=fff"
                            alt="User"
                            class="w-10 h-10 rounded-full border-2 border-[#11131f] shadow-lg object-cover">
                    </div>

                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</h4>
                        <div class="mt-1 space-y-0.5">
                            <p class="text-[10px] text-gray-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-id-card text-orange-500 text-[10px]"></i>
                                {{ Auth::user()->level ?? '-' }}
                            </p>
                            <p class="text-[10px] text-gray-400 flex items-center gap-1.5">
                                <i class="fa-solid fa-layer-group text-blue-500 text-[10px]"></i>
                                Group: {{ Auth::user()->group ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-gray-700/50 flex gap-2">
                    <a href="{{ route('settings') }}" wire:navigate class="flex-1 bg-gray-800 hover:bg-gray-700 text-gray-300 text-[10px] font-bold py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-gear"></i> Settings
                    </a>
                    <a href="{{ route('logout') }}" class="flex-1 bg-red-500/10 hover:bg-red-500/20 text-red-400 hover:text-red-300 text-[10px] font-bold py-1.5 rounded-lg transition-colors flex items-center justify-center gap-1.5">
                        <i class="fa-solid fa-power-off"></i> Log Out
                    </a>
                </div>
            </div>
            @endauth

            <div>
                <h3 class="px-4 text-xs font-bold text-orange-600 uppercase tracking-wider mb-2">Main</h3>
                <div class="space-y-1">

                    <a href="/" class="flex items-center gap-3 px-4 py-3 {{ Request::is('/') || Request::is('dashboard') ? 'bg-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-xl transition-all group" wire:navigate>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="font-medium">Home</span>
                    </a>

                    <a href="/serapan" class="flex items-center gap-3 px-4 py-3 {{ Request::is('serapan') ? 'bg-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-xl transition-all group" wire:navigate>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="font-medium">Serapan</span>
                    </a>

                </div>
            </div>

            @if(Auth::check() && Auth::user()->isSuperAdmin())
            <div>
                <h3 class="px-4 text-xs font-bold text-red-500 uppercase tracking-wider mb-2">Admin Panel</h3>
                <div class="space-y-1">
                    <a href="{{ route('manage.users') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('manage.users') ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <i class="fa-solid fa-users-gear text-lg"></i>
                        <span class="font-medium">Manage Users</span>
                    </a>
                    <a href="{{ route('tarif.setting') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('tarif.setting') ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <i class="fa-solid fa-tags text-lg"></i>
                        <span class="font-medium">Tarif BAST</span>
                    </a>
                    <a href="{{ route('activity.log') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('activity.log') ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <i class="fa-solid fa-clock-rotate-left text-lg"></i>
                        <span class="font-medium">Activity Log</span>
                    </a>
                    <a href="{{ route('laporan.pendapatan') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('laporan.pendapatan') ? 'bg-red-600 text-white shadow-lg shadow-red-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <i class="fa-solid fa-file-excel text-lg"></i>
                        <span class="font-medium">Rekap Nasional</span>
                    </a>
                </div>
            </div>
            @endif

            @if(Auth::check() && Auth::user()->isVerification())
            <div>
                <h3 class="px-4 text-xs font-bold text-blue-400 uppercase tracking-wider mb-2">Verifikasi</h3>
                <div class="space-y-1">
                    <a href="{{ route('verifikasi.gabah') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('verifikasi.gabah') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <i class="fa-solid fa-wheat-awn text-lg"></i>
                        <span class="font-medium">Verifikasi GKP</span>
                    </a>
                    <a href="{{ route('verifikasi.beras') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('verifikasi.beras') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <i class="fa-solid fa-bowl-rice text-lg"></i>
                        <span class="font-medium">Verifikasi HGL</span>
                    </a>
                    <a href="{{ route('status.bayar.bast') }}" wire:navigate
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all group {{ request()->routeIs('status.bayar.bast') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                        <i class="fa-solid fa-money-check-dollar text-lg"></i>
                        <span class="font-medium">Status Bayar BAST</span>
                    </a>
                </div>
            </div>
            @endif

            @if(Auth::check() && Auth::user()->level == 'Inspektor')
            <div>
                <h3 class="px-4 text-xs font-bold text-orange-600 uppercase tracking-wider mb-2">Form</h3>
                <div class="space-y-1">

                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="font-medium">HPK & LHPK</span>
                            </div>
                            <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <div x-show="open" x-cloak class="mt-1 ml-4 border-l border-gray-700 pl-4 space-y-1">
                            <a href="{{ route('input.gabah') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('input.gabah') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                                <div class="p-2 rounded-lg {{ request()->routeIs('input.gabah') ? 'bg-white/20' : 'bg-gray-800 group-hover:bg-gray-700' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg></div>
                                <span class="font-bold tracking-wide">Input Gabah</span>
                            </a>
                            <a href="{{ route('input.beras') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group {{ request()->routeIs('input.beras') ? 'bg-green-600 text-white shadow-lg shadow-green-500/30' : 'text-gray-400 hover:bg-gray-800 hover:text-white' }}">
                                <div class="p-2 rounded-lg {{ request()->routeIs('input.beras') ? 'bg-white/20' : 'bg-gray-800 group-hover:bg-gray-700' }}"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg></div>
                                <span class="font-bold tracking-wide">Input Beras</span>
                            </a>
                        </div>
                    </div>

                    {{-- BAST (sebelum Laporan) --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-300 text-gray-400 hover:bg-gray-800 hover:text-white group">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-gray-800 group-hover:bg-gray-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg></div>
                                <span class="font-bold tracking-wide">BAST</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" :class="{'rotate-180': open}" class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('bast.gabah') }}" wire:navigate class="block px-4 py-2 rounded-lg text-sm font-bold text-gray-400 hover:text-white hover:bg-gray-800 {{ request()->routeIs('bast.gabah') ? 'text-white bg-gray-800' : '' }}">- BAST GKP</a>
                            <a href="{{ route('bast.beras') }}" wire:navigate class="block px-4 py-2 rounded-lg text-sm font-bold text-gray-400 hover:text-white hover:bg-gray-800 {{ request()->routeIs('bast.beras') ? 'text-white bg-gray-800' : '' }}">- BAST HGL</a>
                        </div>
                    </div>

                    {{-- Laporan --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all duration-300 text-gray-400 hover:bg-gray-800 hover:text-white group">
                            <div class="flex items-center gap-3">
                                <div class="p-2 rounded-lg bg-gray-800 group-hover:bg-gray-700"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg></div>
                                <span class="font-bold tracking-wide">Laporan</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" :class="{'rotate-180': open}" class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" x-transition class="pl-4 mt-1 space-y-1">
                            <a href="{{ route('laporan.gkp') }}" wire:navigate class="block px-4 py-2 rounded-lg text-sm font-bold text-gray-400 hover:text-white hover:bg-gray-800 {{ request()->routeIs('laporan.gkp') ? 'text-white bg-gray-800' : '' }}">- Laporan GKP</a>
                            <a href="{{ route('laporan.hgl') }}" wire:navigate class="block px-4 py-2 rounded-lg text-sm font-bold text-gray-400 hover:text-white hover:bg-gray-800 {{ request()->routeIs('laporan.hgl') ? 'text-white bg-gray-800' : '' }}">- Laporan HGL</a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/80 z-40 backdrop-blur-sm cursor-pointer"></div>

    <main class="p-4 md:p-6 xl:p-8 min-h-screen transition-all duration-300">
        {{ $slot }}
    </main>

    @livewireScripts
</body>

</html>