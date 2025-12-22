<div class="space-y-8 p-6 bg-[#0b0c15] min-h-screen">
    
    <div class="text-center pb-2">
        <h2 class="text-3xl font-extrabold text-white tracking-tight">
            Dashboard Kualitas Serapan
        </h2>
        <p class="text-gray-400 text-sm mt-1">Monitoring Harian Kualitas Gabah & Beras</p>
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