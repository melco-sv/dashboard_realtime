<div class="space-y-8 p-6 bg-[#0b0c15] min-h-screen">
    
    <style>
        /* Mengubah ikon kalender menjadi putih agar terlihat di background gelap */
        .dark-date-input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>

    <div class="text-center pb-2">
        <h2 class="text-3xl font-extrabold text-white tracking-tight">
            Dashboard Kualitas Serapan
        </h2>
        <p class="text-gray-400 text-sm mt-1">Monitoring Kualitas Gabah & Beras</p>
    </div>

    <div class="bg-gray-900 border border-gray-800 p-4 rounded-2xl flex flex-col md:flex-row gap-4 items-center justify-between shadow-lg">
        
        <div class="flex flex-col md:flex-row gap-4 w-full md:w-auto">
            
            <div class="flex items-center gap-3 bg-gray-800 px-4 py-2 rounded-xl border border-gray-700">
                <span class="text-gray-400 text-xs uppercase font-bold tracking-wider">Cabang:</span>
                
                <select wire:model.live="cabang" class="bg-transparent text-white font-bold focus:outline-none text-sm cursor-pointer min-w-[200px]">
                    <option value="" class="bg-gray-900 text-gray-200">Semua Cabang</option>
                    @foreach($listCabang as $kode => $nama)
                        <option value="{{ $kode }}" class="bg-gray-900 text-gray-200">{{ $nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3 bg-gray-800 px-4 py-2 rounded-xl border border-gray-700">
                <span class="text-gray-400 text-xs uppercase font-bold tracking-wider">Periode:</span>
                <input type="month" wire:model.live="periode" class="bg-transparent text-white font-bold focus:outline-none text-sm cursor-pointer dark-date-input">
            </div>
        </div>

        <div wire:loading class="text-blue-400 text-xs font-bold animate-pulse flex items-center gap-2">
            <svg class="animate-spin h-4 w-4 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            Mengupdate Data...
        </div>
    </div>

    <div class="bg-gray-900 border border-blue-900/30 p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        <div class="flex items-center gap-4 mb-8 pb-4 border-b border-gray-800 relative z-10">
            <div class="p-3 bg-blue-500/10 rounded-xl border border-blue-500/20">
                <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white uppercase tracking-wide">
                    Komoditas Gabah
                </h3>
                <p class="text-xs text-blue-400 font-bold uppercase tracking-wider">GKP (Gabah Kering Panen)</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-9 gap-4 relative z-10">
            <x-box-stat label="MIN - Kadar Air" :value="$gabahStats['ka']['min']" color="text-blue-400" />
            <x-box-stat label="MAX - Kadar Air" :value="$gabahStats['ka']['max']" color="text-blue-400" />
            <x-box-stat label="AVG - Kadar Air" :value="$gabahStats['ka']['avg']" color="text-blue-400" isBold="true" />

            <x-box-stat label="MIN - Hampa" :value="$gabahStats['hampa']['min']" color="text-gray-200" />
            <x-box-stat label="MAX - Hampa" :value="$gabahStats['hampa']['max']" color="text-gray-200" />
            <x-box-stat label="AVG - Hampa" :value="$gabahStats['hampa']['avg']" color="text-gray-200" isBold="true" />

            <x-box-stat label="MIN - Btr Hijau" :value="$gabahStats['hijau']['min']" color="text-green-400" />
            <x-box-stat label="MAX - Btr Hijau" :value="$gabahStats['hijau']['max']" color="text-green-400" />
            <x-box-stat label="AVG - Btr Hijau" :value="$gabahStats['hijau']['avg']" color="text-green-400" isBold="true" />
        </div>
    </div>

    <div class="bg-gray-900 border border-green-900/30 p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        
        <div class="flex items-center gap-4 mb-8 pb-4 border-b border-gray-800 relative z-10">
            <div class="p-3 bg-green-500/10 rounded-xl border border-green-500/20">
                <svg class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white uppercase tracking-wide">
                    Komoditas Beras
                </h3>
                <p class="text-xs text-green-400 font-bold uppercase tracking-wider">Hasil Produksi</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 relative z-10">
            
            <x-box-stat label="AVG - Kadar Air" :value="$berasStats['ka']['avg']" color="text-green-400" isBold="true" />
            
            <x-box-stat label="MIN - Sosoh" :value="$berasStats['sosoh']['min']" />
            <x-box-stat label="MAX - Sosoh" :value="$berasStats['sosoh']['max']" />
            <x-box-stat label="AVG - Sosoh" :value="$berasStats['sosoh']['avg']" isBold="true" />

            <x-box-stat label="MIN - Patah" :value="$berasStats['patah']['min']" />
            <x-box-stat label="MAX - Patah" :value="$berasStats['patah']['max']" />
            <x-box-stat label="AVG - Patah" :value="$berasStats['patah']['avg']" isBold="true" />

            <x-box-stat label="MIN - Menir" :value="$berasStats['menir']['min']" />
            <x-box-stat label="MAX - Menir" :value="$berasStats['menir']['max']" />
            <x-box-stat label="AVG - Menir" :value="$berasStats['menir']['avg']" isBold="true" />

            <x-box-stat label="AVG - Rendemen" :value="$berasStats['rendemen']['avg']" color="text-purple-400" isBold="true" />
        </div>
    </div>
</div>