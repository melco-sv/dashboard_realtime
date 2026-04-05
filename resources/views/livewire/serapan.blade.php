<div class="space-y-8 p-6 bg-[#0b0c15] min-h-screen font-['Space_Grotesk']">

    <style>
        .dark-date-input::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
    </style>

    @php
        $fmtKg = function($val) {
            $val = (float) $val;
            if ($val >= 1000000) return number_format($val / 1000000, 2, ',', '.') . ' Jt Kg';
            if ($val >= 1000)    return number_format($val / 1000, 2, ',', '.') . ' Ton';
            return number_format($val, 2, ',', '.') . ' Kg';
        };
        $badge = function($chg, $invertColor = false) {
            if ($chg === null) return ['cls' => 'text-gray-500 bg-gray-800', 'arrow' => '—', 'val' => 'N/A'];
            $isUp   = $chg >= 0;
            $isGood = $invertColor ? !$isUp : $isUp;
            return [
                'cls'   => $isGood ? 'text-green-400 bg-green-500/10' : 'text-red-400 bg-red-500/10',
                'arrow' => $isUp ? '↑' : '↓',
                'val'   => abs($chg) . '%',
            ];
        };
        $compBar = function($pct) {
            return [
                'bar'    => $pct >= 80 ? 'bg-green-500' : ($pct >= 60 ? 'bg-yellow-500' : 'bg-red-500'),
                'text'   => $pct >= 80 ? 'text-green-400' : ($pct >= 60 ? 'text-yellow-400' : 'text-red-400'),
                'badge'  => $pct >= 80 ? 'bg-green-500/20 text-green-400' : ($pct >= 60 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400'),
                'status' => $pct >= 80 ? 'BAIK' : ($pct >= 60 ? 'CUKUP' : 'PERLU PERHATIAN'),
            ];
        };
    @endphp

    {{-- HEADER --}}
    <div class="text-center pb-2">
        <h2 class="text-3xl font-extrabold text-white tracking-tight">Dashboard Kualitas Serapan</h2>
        <p class="text-gray-400 text-sm mt-1">Monitoring Kualitas Gabah & Beras</p>
    </div>

    {{-- FILTER --}}
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
            <div class="w-2 h-2 bg-blue-400 rounded-full animate-ping"></div>
            Mengupdate Data...
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SECTION 1: RINGKASAN AKTIVITAS (NEW)                         --}}
    {{-- ============================================================ --}}
    @if(!empty($aktivitasStats))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Gabah: Jumlah Pemeriksaan --}}
        @php $b = $badge($momStats['gabah_count_chg'] ?? null); @endphp
        <div class="bg-gray-900 border border-blue-900/40 p-5 rounded-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl pointer-events-none"></div>
            <p class="text-[10px] uppercase font-bold text-gray-500 tracking-widest mb-3">Pemeriksaan Gabah</p>
            <div class="flex items-end gap-2">
                <span class="text-4xl font-extrabold text-blue-400">{{ number_format($aktivitasStats['gabah_count']) }}</span>
                <span class="text-xs text-gray-500 mb-1">sampel</span>
            </div>
            @if(!empty($momStats))
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $b['cls'] }}">{{ $b['arrow'] }} {{ $b['val'] }}</span>
                <span class="text-[10px] text-gray-600">vs {{ $momStats['prev_periode'] }}</span>
            </div>
            @endif
        </div>

        {{-- Gabah: Total KG --}}
        @php $b = $badge($momStats['gabah_kg_chg'] ?? null); @endphp
        <div class="bg-gray-900 border border-blue-900/40 p-5 rounded-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-blue-500/5 rounded-full blur-2xl pointer-events-none"></div>
            <p class="text-[10px] uppercase font-bold text-gray-500 tracking-widest mb-3">Total Kuantum Gabah</p>
            <span class="text-2xl font-extrabold text-blue-300 leading-tight">{{ $fmtKg($aktivitasStats['gabah_kg']) }}</span>
            @if(!empty($momStats))
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $b['cls'] }}">{{ $b['arrow'] }} {{ $b['val'] }}</span>
                <span class="text-[10px] text-gray-600">vs {{ $momStats['prev_periode'] }}</span>
            </div>
            @endif
        </div>

        {{-- Beras: Jumlah Pemeriksaan --}}
        @php $b = $badge($momStats['beras_count_chg'] ?? null); @endphp
        <div class="bg-gray-900 border border-green-900/40 p-5 rounded-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 rounded-full blur-2xl pointer-events-none"></div>
            <p class="text-[10px] uppercase font-bold text-gray-500 tracking-widest mb-3">Pemeriksaan Beras</p>
            <div class="flex items-end gap-2">
                <span class="text-4xl font-extrabold text-green-400">{{ number_format($aktivitasStats['beras_count']) }}</span>
                <span class="text-xs text-gray-500 mb-1">sampel</span>
            </div>
            @if(!empty($momStats))
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $b['cls'] }}">{{ $b['arrow'] }} {{ $b['val'] }}</span>
                <span class="text-[10px] text-gray-600">vs {{ $momStats['prev_periode'] }}</span>
            </div>
            @endif
        </div>

        {{-- Beras: Total KG --}}
        @php $b = $badge($momStats['beras_kg_chg'] ?? null); @endphp
        <div class="bg-gray-900 border border-green-900/40 p-5 rounded-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/5 rounded-full blur-2xl pointer-events-none"></div>
            <p class="text-[10px] uppercase font-bold text-gray-500 tracking-widest mb-3">Total Kuantum Beras</p>
            <span class="text-2xl font-extrabold text-green-300 leading-tight">{{ $fmtKg($aktivitasStats['beras_kg']) }}</span>
            @if(!empty($momStats))
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs px-2 py-0.5 rounded-full font-bold {{ $b['cls'] }}">{{ $b['arrow'] }} {{ $b['val'] }}</span>
                <span class="text-[10px] text-gray-600">vs {{ $momStats['prev_periode'] }}</span>
            </div>
            @endif
        </div>

    </div>
    @endif

    {{-- ============================================================ --}}
    {{-- SECTION 2: GABAH                                             --}}
    {{-- ============================================================ --}}
    <div class="bg-gray-900 border border-blue-900/30 p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        <div class="flex items-center gap-4 mb-8 pb-4 border-b border-gray-800 relative z-10">
            <div class="p-3 bg-blue-500/10 rounded-xl border border-blue-500/20">
                <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white uppercase tracking-wide">Komoditas Gabah</h3>
                <p class="text-xs text-blue-400 font-bold uppercase tracking-wider">GKP (Gabah Kering Panen)</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-9 gap-4 relative z-10">
            <x-box-stat label="MIN - Kadar Air"  :value="$gabahStats['ka']['min']"    color="text-blue-400" />
            <x-box-stat label="MAX - Kadar Air"  :value="$gabahStats['ka']['max']"    color="text-blue-400" />
            <x-box-stat label="AVG - Kadar Air"  :value="$gabahStats['ka']['avg']"    color="text-blue-400" isBold="true" />
            <x-box-stat label="MIN - Hampa"      :value="$gabahStats['hampa']['min']" color="text-gray-200" />
            <x-box-stat label="MAX - Hampa"      :value="$gabahStats['hampa']['max']" color="text-gray-200" />
            <x-box-stat label="AVG - Hampa"      :value="$gabahStats['hampa']['avg']" color="text-gray-200" isBold="true" />
            <x-box-stat label="MIN - Btr Hijau"  :value="$gabahStats['hijau']['min']" color="text-green-400" />
            <x-box-stat label="MAX - Btr Hijau"  :value="$gabahStats['hijau']['max']" color="text-green-400" />
            <x-box-stat label="AVG - Btr Hijau"  :value="$gabahStats['hijau']['avg']" color="text-green-400" isBold="true" />
        </div>

        {{-- COMPLIANCE BAR GABAH (NEW) --}}
        @if(!empty($complianceStats) && $complianceStats['gabah_total'] > 0)
        @php $c = $compBar($complianceStats['gabah_pct']); @endphp
        <div class="mt-8 pt-6 border-t border-gray-800 relative z-10">
            <div class="flex items-start justify-between mb-3 gap-4">
                <div>
                    <p class="text-xs uppercase font-bold text-gray-400 tracking-widest">Tingkat Kelulusan Standar</p>
                    <p class="text-[10px] text-gray-600 mt-1">Standar: KA ≤ 38% &nbsp;·&nbsp; Hampa ≤ 40% &nbsp;·&nbsp; Butir Hijau ≤ 30%</p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-3xl font-extrabold {{ $c['text'] }}">{{ $complianceStats['gabah_pct'] }}%</span>
                    <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full font-bold {{ $c['badge'] }}">{{ $c['status'] }}</span>
                </div>
            </div>
            <div class="w-full bg-gray-800 h-3 rounded-full overflow-hidden">
                <div class="{{ $c['bar'] }} h-full rounded-full transition-all duration-700" style="width: {{ $complianceStats['gabah_pct'] }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                {{ number_format($complianceStats['gabah_lulus']) }} lulus dari {{ number_format($complianceStats['gabah_total']) }} sampel diperiksa
            </p>
        </div>
        @endif
    </div>

    {{-- ============================================================ --}}
    {{-- SECTION 3: BERAS                                             --}}
    {{-- ============================================================ --}}
    <div class="bg-gray-900 border border-green-900/30 p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        <div class="flex items-center gap-4 mb-8 pb-4 border-b border-gray-800 relative z-10">
            <div class="p-3 bg-green-500/10 rounded-xl border border-green-500/20">
                <svg class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-white uppercase tracking-wide">Komoditas Beras</h3>
                <p class="text-xs text-green-400 font-bold uppercase tracking-wider">Hasil Produksi</p>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 relative z-10">
            <x-box-stat label="AVG - Kadar Air"  :value="$berasStats['ka']['avg']"       color="text-green-400" isBold="true" />
            <x-box-stat label="MIN - Sosoh"       :value="$berasStats['sosoh']['min']" />
            <x-box-stat label="MAX - Sosoh"       :value="$berasStats['sosoh']['max']" />
            <x-box-stat label="AVG - Sosoh"       :value="$berasStats['sosoh']['avg']"    isBold="true" />
            <x-box-stat label="MIN - Patah"       :value="$berasStats['patah']['min']" />
            <x-box-stat label="MAX - Patah"       :value="$berasStats['patah']['max']" />
            <x-box-stat label="AVG - Patah"       :value="$berasStats['patah']['avg']"    isBold="true" />
            <x-box-stat label="MIN - Menir"       :value="$berasStats['menir']['min']" />
            <x-box-stat label="MAX - Menir"       :value="$berasStats['menir']['max']" />
            <x-box-stat label="AVG - Menir"       :value="$berasStats['menir']['avg']"    isBold="true" />
            <x-box-stat label="AVG - Rendemen"    :value="$berasStats['rendemen']['avg']" color="text-purple-400" isBold="true" />
        </div>

        {{-- COMPLIANCE BAR BERAS (NEW) --}}
        @if(!empty($complianceStats) && $complianceStats['beras_total'] > 0)
        @php $c = $compBar($complianceStats['beras_pct']); @endphp
        <div class="mt-8 pt-6 border-t border-gray-800 relative z-10">
            <div class="flex items-start justify-between mb-3 gap-4">
                <div>
                    <p class="text-xs uppercase font-bold text-gray-400 tracking-widest">Tingkat Kelulusan Standar</p>
                    <p class="text-[10px] text-gray-600 mt-1">Standar: Rendemen ≥ 50% &nbsp;·&nbsp; Patah ≤ 25% &nbsp;·&nbsp; Menir ≤ 2% &nbsp;·&nbsp; Sosoh ≥ 95%</p>
                </div>
                <div class="text-right shrink-0">
                    <span class="text-3xl font-extrabold {{ $c['text'] }}">{{ $complianceStats['beras_pct'] }}%</span>
                    <span class="ml-2 text-[10px] px-2 py-0.5 rounded-full font-bold {{ $c['badge'] }}">{{ $c['status'] }}</span>
                </div>
            </div>
            <div class="w-full bg-gray-800 h-3 rounded-full overflow-hidden">
                <div class="{{ $c['bar'] }} h-full rounded-full transition-all duration-700" style="width: {{ $complianceStats['beras_pct'] }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                {{ number_format($complianceStats['beras_lulus']) }} lulus dari {{ number_format($complianceStats['beras_total']) }} sampel diperiksa
            </p>
        </div>
        @endif

        {{-- HASIL SAMPING (NEW) --}}
        @if(!empty($hasilSampingStats) && $hasilSampingStats['total'] > 0)
        <div class="mt-8 pt-6 border-t border-gray-800 relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-xs uppercase font-bold text-orange-400 tracking-widest">Hasil Samping</p>
                    <p class="text-[10px] text-gray-500 mt-1">Total: <span class="text-orange-300 font-bold">{{ $fmtKg($hasilSampingStats['total']) }}</span></p>
                </div>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @php
                    $hsItems = [
                        ['label' => 'Menir',        'val' => $hasilSampingStats['menir'],        'color' => 'text-orange-300', 'border' => 'border-orange-900/30'],
                        ['label' => 'Butir Patah',  'val' => $hasilSampingStats['butir_patah'],  'color' => 'text-yellow-300', 'border' => 'border-yellow-900/30'],
                        ['label' => 'Dedak / Katul','val' => $hasilSampingStats['dedak_katul'],  'color' => 'text-amber-300',  'border' => 'border-amber-900/30'],
                        ['label' => 'Kuning / Rusak','val'=> $hasilSampingStats['kuning_rusak'], 'color' => 'text-red-300',    'border' => 'border-red-900/30'],
                    ];
                @endphp
                @foreach($hsItems as $item)
                <div class="bg-gray-800/40 p-4 rounded-xl border {{ $item['border'] }} text-center">
                    <p class="text-[10px] uppercase font-bold text-gray-500 tracking-widest mb-2">{{ $item['label'] }}</p>
                    <p class="text-lg font-bold {{ $item['color'] }}">{{ $fmtKg($item['val']) }}</p>
                    @if($hasilSampingStats['total'] > 0)
                    <p class="text-[10px] text-gray-600 mt-1">{{ number_format(($item['val'] / $hasilSampingStats['total']) * 100, 1) }}% dari total</p>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- ============================================================ --}}
    {{-- SECTION 4: SUPER ADMIN                                       --}}
    {{-- ============================================================ --}}
    @if(Auth::check() && strtolower(trim(Auth::user()->level)) == 'super admin' && !empty($advancedStats))
    <div class="bg-indigo-900/20 border border-indigo-500/30 p-6 md:p-8 rounded-3xl relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center justify-between mb-6 pb-4 border-b border-indigo-500/20 relative z-10">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-indigo-500/20 rounded-xl border border-indigo-500/40 text-indigo-300">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white uppercase tracking-wide">Analisis Lanjutan</h3>
                    <p class="text-xs text-indigo-400 font-bold uppercase tracking-wider">Super Admin Only View</p>
                </div>
            </div>
            <span class="bg-indigo-600 text-white text-[10px] px-2 py-1 rounded uppercase font-bold tracking-widest shadow-lg shadow-indigo-500/50">Statistika & Ranking</span>
        </div>

        {{-- CV Cards (existing) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
            <div class="bg-gray-800/60 p-5 rounded-2xl border border-gray-700 backdrop-blur-sm hover:border-indigo-500/50 transition-colors group">
                <h4 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 group-hover:text-indigo-400 transition-colors">Konsistensi Kadar Air Gabah</h4>
                <div class="flex justify-between items-end">
                    <div><span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Std. Deviasi</span><span class="text-white font-mono text-xl">{{ number_format($advancedStats['gabah_ka']['sd'], 2) }}</span></div>
                    <div class="text-right"><span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">CV %</span><span class="text-2xl font-bold {{ $advancedStats['gabah_ka']['cv'] < 5 ? 'text-green-400' : 'text-yellow-400' }}">{{ number_format($advancedStats['gabah_ka']['cv'], 2) }}%</span></div>
                </div>
                <div class="mt-3 w-full bg-gray-700 h-1.5 rounded-full overflow-hidden"><div class="bg-indigo-500 h-full transition-all duration-1000" style="width: {{ min($advancedStats['gabah_ka']['cv'] * 5, 100) }}%"></div></div>
                <p class="text-[10px] text-gray-500 mt-2 italic">Semakin kecil %, semakin seragam kualitas gabah.</p>
            </div>
            <div class="bg-gray-800/60 p-5 rounded-2xl border border-gray-700 backdrop-blur-sm hover:border-purple-500/50 transition-colors group">
                <h4 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 group-hover:text-purple-400 transition-colors">Konsistensi Rendemen</h4>
                <div class="flex justify-between items-end">
                    <div><span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Std. Deviasi</span><span class="text-white font-mono text-xl">{{ number_format($advancedStats['beras_rendemen']['sd'], 2) }}</span></div>
                    <div class="text-right"><span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">CV %</span><span class="text-2xl font-bold {{ $advancedStats['beras_rendemen']['cv'] < 5 ? 'text-green-400' : 'text-yellow-400' }}">{{ number_format($advancedStats['beras_rendemen']['cv'], 2) }}%</span></div>
                </div>
                <div class="mt-3 w-full bg-gray-700 h-1.5 rounded-full overflow-hidden"><div class="bg-purple-500 h-full transition-all duration-1000" style="width: {{ min($advancedStats['beras_rendemen']['cv'] * 5, 100) }}%"></div></div>
            </div>
            <div class="bg-gray-800/60 p-5 rounded-2xl border border-gray-700 backdrop-blur-sm hover:border-pink-500/50 transition-colors group">
                <h4 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2 group-hover:text-pink-400 transition-colors">Konsistensi Butir Patah</h4>
                <div class="flex justify-between items-end">
                    <div><span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">Std. Deviasi</span><span class="text-white font-mono text-xl">{{ number_format($advancedStats['beras_patah']['sd'], 2) }}</span></div>
                    <div class="text-right"><span class="text-[10px] text-gray-500 uppercase font-bold block mb-1">CV %</span><span class="text-2xl font-bold {{ $advancedStats['beras_patah']['cv'] < 15 ? 'text-green-400' : 'text-red-400' }}">{{ number_format($advancedStats['beras_patah']['cv'], 2) }}%</span></div>
                </div>
                <div class="mt-3 w-full bg-gray-700 h-1.5 rounded-full overflow-hidden"><div class="bg-pink-500 h-full transition-all duration-1000" style="width: {{ min($advancedStats['beras_patah']['cv'] * 2, 100) }}%"></div></div>
            </div>
        </div>

        {{-- RANKING CABANG (NEW) --}}
        @if(!empty($rankingCabang))
        <div class="mt-8 pt-6 border-t border-indigo-500/20 relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-widest">Ranking Performa Cabang</h4>
                    <p class="text-[10px] text-gray-500 mt-1">Urutan terbaik → terburuk berdasarkan Rata-rata Kadar Air Gabah</p>
                </div>
                <span class="text-[10px] text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-full font-bold border border-indigo-500/30">
                    {{ count($rankingCabang) }} Cabang
                </span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-700/50">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-800/80 text-gray-400 text-[10px] uppercase tracking-widest">
                            <th class="px-4 py-3 text-left font-bold">#</th>
                            <th class="px-4 py-3 text-left font-bold">Cabang</th>
                            <th class="px-4 py-3 text-center font-bold">Pem. Gabah</th>
                            <th class="px-4 py-3 text-center font-bold">Pem. Beras</th>
                            <th class="px-4 py-3 text-center font-bold">Avg KA Gabah</th>
                            <th class="px-4 py-3 text-center font-bold">Avg Hampa</th>
                            <th class="px-4 py-3 text-center font-bold">Avg Rendemen</th>
                            <th class="px-4 py-3 text-center font-bold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700/40">
                        @foreach($rankingCabang as $i => $row)
                        @php
                            $rank      = $i + 1;
                            $rankCls   = $rank === 1 ? 'text-yellow-400' : ($rank === 2 ? 'text-gray-300' : ($rank === 3 ? 'text-amber-600' : 'text-gray-600'));
                            $isGood    = $row['avg_ka'] <= 14 && ($row['avg_rendemen'] === null || $row['avg_rendemen'] >= 63);
                        @endphp
                        <tr class="bg-gray-900/50 hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 py-3 font-extrabold {{ $rankCls }}">#{{ $rank }}</td>
                            <td class="px-4 py-3 font-bold text-white">{{ $row['name'] }}</td>
                            <td class="px-4 py-3 text-center font-mono text-blue-300">{{ number_format($row['jumlah_gabah']) }}</td>
                            <td class="px-4 py-3 text-center font-mono text-green-300">{{ number_format($row['jumlah_beras']) }}</td>
                            <td class="px-4 py-3 text-center font-mono font-bold {{ $row['avg_ka'] <= 14 ? 'text-green-400' : 'text-red-400' }}">
                                {{ number_format($row['avg_ka'], 2) }}%
                            </td>
                            <td class="px-4 py-3 text-center font-mono {{ $row['avg_hampa'] <= 3 ? 'text-green-400' : 'text-yellow-400' }}">
                                {{ number_format($row['avg_hampa'], 2) }}%
                            </td>
                            <td class="px-4 py-3 text-center font-mono {{ ($row['avg_rendemen'] ?? 0) >= 63 ? 'text-green-400' : 'text-orange-400' }}">
                                {{ $row['avg_rendemen'] !== null ? number_format($row['avg_rendemen'], 2) . '%' : '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $isGood ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                    {{ $isGood ? 'BAIK' : 'PERHATIAN' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
    @endif

</div>
