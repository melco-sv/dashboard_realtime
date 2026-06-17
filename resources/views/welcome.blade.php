<!-- <!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Gabah</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> 
    
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        [x-cloak] { display: none !important; }
        
        /* Scrollbar Halus untuk Sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #11131f; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #f97316; }
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
                <div class="w-9 h-9 bg-gradient-to-br from-orange-600 to-red-600 rounded-lg flex items-center justify-center shadow-lg shadow-orange-500/20">
                    <span class="font-bold text-white text-xs tracking-tighter">SC</span>
                </div>
                <div class="leading-tight hidden md:block">
                    <h1 class="font-bold text-lg tracking-tight text-white">SUCOFINDO</h1>
                    <p class="text-[10px] text-orange-500 font-medium tracking-widest uppercase">Monitoring System</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-200">Admin User</p>
                <p class="text-[10px] text-green-500 uppercase font-bold">Online</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-gray-700 border border-gray-600 overflow-hidden">
                <svg class="w-full h-full text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </div>
        </div>
    </header>

    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
        class="fixed inset-y-0 left-0 z-50 w-72 bg-[#11131f] border-r border-gray-800 transition-transform duration-300 ease-in-out shadow-2xl sidebar-scroll overflow-y-auto"
        x-cloak>
        
        <div class="h-16 flex items-center justify-between px-6 border-b border-gray-800 bg-[#0b0c15]">
            <span class="text-sm font-bold text-gray-400 tracking-widest uppercase">Navigation</span>
            <button @click="sidebarOpen = false" class="text-gray-500 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="p-4 space-y-8">
            
            <div>
                <h3 class="px-4 text-xs font-bold text-orange-600 uppercase tracking-wider mb-2">Main</h3>
                <div class="space-y-1">
                    <a href="#" class="flex items-center gap-3 px-4 py-3 bg-orange-600 text-white rounded-xl shadow-lg shadow-orange-500/20 group">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span class="font-medium">Home</span>
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all group">
                        <svg class="w-5 h-5 group-hover:text-orange-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        <span class="font-medium">Serapan</span>
                    </a>
                </div>
            </div>

            <div>
                <h3 class="px-4 text-xs font-bold text-orange-600 uppercase tracking-wider mb-2">Form</h3>
                <div class="space-y-1">
                    
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 group-hover:text-blue-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <span class="font-medium">HPK & LHPK</span>
                            </div>
                            <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="mt-1 ml-4 border-l border-gray-700 pl-4 space-y-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-white hover:bg-gray-800 rounded-lg">Input Gabah</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-white hover:bg-gray-800 rounded-lg">Input Beras</a>
                        </div>
                    </div>

                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 text-gray-400 hover:text-white hover:bg-gray-800 rounded-xl transition-all group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 group-hover:text-green-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <span class="font-medium">Laporan</span>
                            </div>
                            <svg :class="open ? 'rotate-90' : ''" class="w-4 h-4 transition-transform text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </button>
                        <div x-show="open" x-cloak x-transition class="mt-1 ml-4 border-l border-gray-700 pl-4 space-y-1">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-white hover:bg-gray-800 rounded-lg">Laporan Harian</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-500 hover:text-white hover:bg-gray-800 rounded-lg">Laporan Bulanan</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/80 z-40 backdrop-blur-sm cursor-pointer"></div>

    <main class="p-4 md:p-8 min-h-screen transition-all duration-300">
        <div class="max-w-7xl mx-auto">
            @livewire('dashboard-gabah')
        </div>
    </main>

    @livewireScripts
</body>
</html> -->