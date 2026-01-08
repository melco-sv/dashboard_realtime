<div class="space-y-8 p-6 bg-[#0b0c15] min-h-screen font-['Space_Grotesk']">
    
    <style>
        .dark-date-input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>

    {{-- HEADER --}}
    <div class="text-center pb-2">
        <h2 class="text-3xl font-extrabold text-white tracking-tight">
            Dashboard Kualitas Serapan
        </h2>
        <p class="text-gray-400 text-sm mt-1">Monitoring Kualitas Gabah & Beras</p>
    </div>

    {{-- FILTER SECTION --}}
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
            Mengupdate Data...
        </div>
    </div>

    {{-- SECTION 1: GABAH --}}
    <div class="bg-gray-900 border border-blue-900/30 p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <div class="flex items-center gap-4 mb-8 pb-4 border-b border-gray-800 relative z-10">
            <div class="p-3 bg-blue-500/10 rounded-xl border border-blue-500/20">
                <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white uppercase tracking-wide">Komoditas Gabah</h3>
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

    {{-- SECTION 2: BERAS --}}
    <div class="bg-gray-900 border border-green-900/30 p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
        <div class="flex items-center gap-4 mb-8 pb-4 border-b border-gray-800 relative z-10">
            <div class="p-3 bg-green-500/10 rounded-xl border border-green-500/20">
                <svg class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white uppercase tracking-wide">Komoditas Beras</h3>
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

    {{-- === SECTION 3: STATISTIKA ADMIN (KHUSUS SUPER ADMIN) === --}}
    {{-- PERBAIKAN: Menggunakan strtolower agar cocok dengan 'Super Admin' --}}
    @if(Auth::check() && strtolower(trim(Auth::user()->level)) == 'super admin' && !empty($advancedStats))
    
    <div class="bg-indigo-900/20 border border-indigo-500/30 p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl mt-8 animate-fade-in-up">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>

        <div class="flex items-center justify-between mb-6 pb-4 border-b border-indigo-500/20 relative z-10">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-500/20 rounded-xl border border-indigo-500/40 text-indigo-300">
                   <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white uppercase tracking-wide">Analisis Variabilitas</h3>
                    <p class="text-xs text-indigo-400 font-bold uppercase tracking-wider">Super Admin Only View</p>
                </div>
            </div>
            <div class="text-right">
                <span class="bg-indigo-600 text-white text-[10px] px-2 py-1 rounded uppercase font-bold tracking-widest shadow-lg shadow-indigo-500/50">
                    Koefisien Variasi (CV)
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
            
            {{-- 1. Gabah KA --}}
            <div class="bg-gray-800/60 p-5 rounded-2xl border border-gray-700 backdrop-blur-sm hover:border-indigo-500/50 transition-colors group">
                <h4 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 group-hover:text-indigo-400 transition-colors">Konsistensi Kadar Air Gabah</h4>
                <div class="flex justify-between items-end">
                    <div>
                        <span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Std. Deviasi</span>
                        <span class="text-white font-mono text-xl tracking-tight">{{ number_format($advancedStats['gabah_ka']['sd'], 2) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">CV %</span>
                        <span class="text-2xl font-bold {{ $advancedStats['gabah_ka']['cv'] < 5 ? 'text-green-400' : 'text-yellow-400' }}">
                            {{ number_format($advancedStats['gabah_ka']['cv'], 2) }}%
                        </span>
                    </div>
                </div>
                <div class="mt-3 w-full bg-gray-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-indigo-500 h-full transition-all duration-1000" style="width: {{ min($advancedStats['gabah_ka']['cv'] * 5, 100) }}%"></div>
                </div>
                <p class="text-[10px] text-gray-500 mt-2 italic">Semakin kecil %, semakin seragam kualitas gabah.</p>
            </div>

            {{-- 2. Beras Rendemen --}}
            <div class="bg-gray-800/60 p-5 rounded-2xl border border-gray-700 backdrop-blur-sm hover:border-purple-500/50 transition-colors group">
                <h4 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 group-hover:text-purple-400 transition-colors">Konsistensi Rendemen</h4>
                <div class="flex justify-between items-end">
                    <div>
                        <span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Std. Deviasi</span>
                        <span class="text-white font-mono text-xl tracking-tight">{{ number_format($advancedStats['beras_rendemen']['sd'], 2) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">CV %</span>
                        <span class="text-2xl font-bold {{ $advancedStats['beras_rendemen']['cv'] < 5 ? 'text-green-400' : 'text-yellow-400' }}">
                            {{ number_format($advancedStats['beras_rendemen']['cv'], 2) }}%
                        </span>
                    </div>
                </div>
                 <div class="mt-3 w-full bg-gray-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-purple-500 h-full transition-all duration-1000" style="width: {{ min($advancedStats['beras_rendemen']['cv'] * 5, 100) }}%"></div>
                </div>
            </div>

            {{-- 3. Beras Patah --}}
            <div class="bg-gray-800/60 p-5 rounded-2xl border border-gray-700 backdrop-blur-sm hover:border-pink-500/50 transition-colors group">
                <h4 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 group-hover:text-pink-400 transition-colors">Konsistensi Butir Patah</h4>
                <div class="flex justify-between items-end">
                    <div>
                        <span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Std. Deviasi</span>
                        <span class="text-white font-mono text-xl tracking-tight">{{ number_format($advancedStats['beras_patah']['sd'], 2) }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">CV %</span>
                        <span class="text-2xl font-bold {{ $advancedStats['beras_patah']['cv'] < 15 ? 'text-green-400' : 'text-red-400' }}">
                            {{ number_format($advancedStats['beras_patah']['cv'], 2) }}%
                        </span>
                    </div>
                </div>
                 <div class="mt-3 w-full bg-gray-700 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-pink-500 h-full transition-all duration-1000" style="width: {{ min($advancedStats['beras_patah']['cv'] * 2, 100) }}%"></div>
                </div>
            </div>

        </div>
    </div>
    @endif

</div>