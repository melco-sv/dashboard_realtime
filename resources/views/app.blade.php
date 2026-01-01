<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Gabah</title>
    
    <link rel="icon" type="image/png" href="{{ asset('assets/logo-sucofindo.png') }}?v=101">
    <link rel="shortcut icon" href="{{ asset('assets/logo-sucofindo.png') }}?v=101">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script> 
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Space Grotesk', sans-serif; }
        [x-cloak] { display: none !important; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: #11131f; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
    </style>
    @livewireStyles
</head>
<body class="bg-[#0b0c15] text-white min-h-screen overflow-x-hidden" x-data="{ sidebarOpen: false }">

    <header class="flex items-center justify-between bg-[#11131f] border-b border-gray-800 px-4 py-3 sticky top-0 z-40 shadow-xl">
        <div class="flex items-center gap-4">
            <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-colors focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
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
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-200">Admin User</p>
                <p class="text-[10px] text-green-500 uppercase font-bold">Online</p>
            </div>
            <div class="w-9 h-9 rounded-full bg-gray-700 border border-gray-600"></div>
        </div>
    </header>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 bg-[#11131f] border-r border-gray-800 transition-transform duration-300 ease-in-out shadow-2xl sidebar-scroll overflow-y-auto" x-cloak>
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
                    <a href="/" class="flex items-center gap-3 px-4 py-3 {{ Request::is('/') ? 'bg-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-xl transition-all group" wire:navigate>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <span class="font-medium">Home</span>
                    </a>
                    
                    <a href="/serapan" class="flex items-center gap-3 px-4 py-3 {{ Request::is('serapan') ? 'bg-orange-600 text-white shadow-lg shadow-orange-500/20' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} rounded-xl transition-all group" wire:navigate>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        <span class="font-medium">Serapan</span>
                    </a>
                </div>
            </div>
        </div>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 bg-black/80 z-40 backdrop-blur-sm cursor-pointer"></div>

    <main class="p-4 md:p-8 min-h-screen transition-all duration-300">
        <div class="max-w-7xl mx-auto">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
</body>
</html>