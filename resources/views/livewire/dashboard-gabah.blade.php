<div wire:poll.30s class="space-y-8">

    {{-- ═══════════════════════════════════════════════════════
         HERO KPI CARDS — Verification + SuperAdmin only
    ════════════════════════════════════════════════════════ --}}
    @if ($showFinancial)
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Pendapatan Gabungan --}}
        <div class="bg-gray-900 border border-purple-900/50 rounded-2xl p-5 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-purple-600/10 rounded-full blur-2xl"></div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 bg-purple-500/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-coins text-purple-400 text-xs"></i>
                </div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Pendapatan</p>
            </div>
            <p class="text-white font-bold text-lg leading-tight font-mono">{{ $totalPendapatanGabungDisplay }}</p>
            <p class="text-purple-400 text-xs mt-1">Gabah + Beras</p>
        </div>

        {{-- Total Diterima Gabungan --}}
        <div class="bg-gray-900 border border-emerald-900/50 rounded-2xl p-5 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-emerald-600/10 rounded-full blur-2xl"></div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 bg-emerald-500/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-emerald-400 text-xs"></i>
                </div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Total Diterima</p>
            </div>
            <p class="text-white font-bold text-lg leading-tight font-mono">{{ $totalDiterimaGabungDisplay }}</p>
            <p class="text-emerald-400 text-xs mt-1">BAST DIBAYAR</p>
        </div>

        {{-- BAST Belum Dibayar --}}
        <div class="bg-gray-900 border border-orange-900/50 rounded-2xl p-5 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-orange-600/10 rounded-full blur-2xl"></div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 bg-orange-500/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-clock text-orange-400 text-xs"></i>
                </div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">BAST Pending</p>
            </div>
            <p class="text-white font-bold text-4xl leading-tight font-mono">{{ $bastBelumDibayarCount }}</p>
            <p class="text-orange-400 text-xs mt-1">Belum dibayar</p>
        </div>

        {{-- Cabang Aktif Bulan Ini --}}
        <div class="bg-gray-900 border border-cyan-900/50 rounded-2xl p-5 relative overflow-hidden">
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-cyan-600/10 rounded-full blur-2xl"></div>
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 bg-cyan-500/20 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-building text-cyan-400 text-xs"></i>
                </div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-wider">Cabang Aktif</p>
            </div>
            <p class="text-white font-bold text-4xl leading-tight font-mono">{{ $cabangAktifBulanIni }}</p>
            <p class="text-cyan-400 text-xs mt-1">Bulan {{ date('M Y') }}</p>
        </div>

    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
         SECTION GABAH (existing)
    ════════════════════════════════════════════════════════ --}}
    <div class="bg-gray-900 border border-blue-900/50 rounded-3xl p-8 relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        <h3 class="text-blue-400 font-bold uppercase tracking-wider mb-6 flex items-center gap-2 text-xl">
            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
            Komoditas Gabah (GKP)
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700 backdrop-blur-sm">
                <p class="text-gray-400 text-xs uppercase font-bold tracking-widest">Total Berat Masuk</p>
                <div class="mt-4">
                    <h2 class="text-4xl font-bold text-white tracking-tight">
                        {{ $totalGabahKgDisplay }}
                    </h2>
                    <span class="text-lg text-blue-400 font-bold">Kg</span>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-700 flex justify-between items-center text-sm">
                    <span class="text-gray-500">Jumlah Sampel</span>
                    <span class="text-white font-mono bg-gray-700 px-2 py-1 rounded">{{ $totalGabahAnalisa }}</span>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-700/50 space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Pendapatan</span>
                        <span class="text-blue-400 font-mono text-xs font-bold">{{ $pendapatanGabahDisplay }}</span>
                    </div>
                    @if ($showFinancial)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Diterima</span>
                        <span class="text-green-400 font-mono text-xs font-bold">{{ $totalDidipatGabahDisplay }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 bg-gray-800/50 rounded-2xl p-4 border border-gray-700 backdrop-blur-sm" wire:ignore>
                <h4 class="text-xs text-gray-400 uppercase mb-4 ml-2">Tren Pemasukan (Bulanan)</h4>
                <div id="chart-gabah"></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         SECTION BERAS (existing)
    ════════════════════════════════════════════════════════ --}}
    <div class="bg-gray-900 border border-green-900/50 rounded-3xl p-8 relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        <h3 class="text-green-400 font-bold uppercase tracking-wider mb-6 flex items-center gap-2 text-xl">
            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
            Komoditas Beras
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-gray-800/50 rounded-2xl p-6 border border-gray-700 backdrop-blur-sm">
                <p class="text-gray-400 text-xs uppercase font-bold tracking-widest">Total Kuantum Beras</p>
                <div class="mt-4">
                    <h2 class="text-4xl font-bold text-white tracking-tight">
                        {{ $totalBerasKgDisplay }}
                    </h2>
                    <span class="text-lg text-green-400 font-bold">Kg</span>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-700 flex justify-between items-center text-sm">
                    <span class="text-gray-500">Jumlah Sampel</span>
                    <span class="text-white font-mono bg-gray-700 px-2 py-1 rounded">{{ $totalBerasAnalisa }}</span>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-700/50 space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Pendapatan</span>
                        <span class="text-green-400 font-mono text-xs font-bold">{{ $pendapatanBerasDisplay }}</span>
                    </div>
                    @if ($showFinancial)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Total Diterima</span>
                        <span class="text-emerald-400 font-mono text-xs font-bold">{{ $totalDidipatBerasDisplay }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="lg:col-span-2 bg-gray-800/50 rounded-2xl p-4 border border-gray-700 backdrop-blur-sm" wire:ignore>
                <h4 class="text-xs text-gray-400 uppercase mb-4 ml-2">Tren Produksi (Bulanan)</h4>
                <div id="chart-beras"></div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         QUALITY SNAPSHOT — Verification + SuperAdmin only
    ════════════════════════════════════════════════════════ --}}
    @if ($showFinancial)
    @php
        $qaGood = ['text' => 'text-emerald-400', 'bg' => 'bg-emerald-500/10 border-emerald-500/30', 'dot' => 'bg-emerald-500'];
        $qaWarn = ['text' => 'text-yellow-400',  'bg' => 'bg-yellow-500/10 border-yellow-500/30',   'dot' => 'bg-yellow-500'];
        $qaBad  = ['text' => 'text-red-400',     'bg' => 'bg-red-500/10 border-red-500/30',         'dot' => 'bg-red-500'];

        $cKA    = $avgKadarAir    <= 25 ? $qaGood : ($avgKadarAir    <= 38 ? $qaWarn : $qaBad);
        $cHampa = $avgKadarHampa  <= 20 ? $qaGood : ($avgKadarHampa  <= 40 ? $qaWarn : $qaBad);
        $cHijau = $avgButirHijau  <= 15 ? $qaGood : ($avgButirHijau  <= 30 ? $qaWarn : $qaBad);
        $cSosoh = $avgDerajatSosoh >= 100 ? $qaGood : ($avgDerajatSosoh >= 95 ? $qaWarn : $qaBad);
        $cPatah = $avgButirPatah  <= 15 ? $qaGood : ($avgButirPatah  <= 25 ? $qaWarn : $qaBad);
        $cMenir = $avgMenir       <= 1  ? $qaGood : ($avgMenir       <= 2  ? $qaWarn : $qaBad);
    @endphp

    <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6 shadow-xl">
        <h3 class="text-gray-300 font-bold uppercase tracking-wider mb-5 flex items-center gap-2">
            <i class="fa-solid fa-flask-vial text-gray-500 text-sm"></i>
            Rata-rata Kualitas Keseluruhan
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Gabah --}}
            <div>
                <p class="text-blue-400 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 bg-blue-500 rounded-full"></span> Gabah (GKP)
                </p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach([
                        ['label'=>'Kadar Air','val'=>$avgKadarAir,'c'=>$cKA,'unit'=>'%'],
                        ['label'=>'Kadar Hampa','val'=>$avgKadarHampa,'c'=>$cHampa,'unit'=>'%'],
                        ['label'=>'Butir Hijau','val'=>$avgButirHijau,'c'=>$cHijau,'unit'=>'%'],
                    ] as $m)
                    <div class="border rounded-xl p-3 text-center {{ $m['c']['bg'] }}">
                        <p class="text-gray-500 text-[10px] uppercase tracking-wider mb-1">{{ $m['label'] }}</p>
                        <p class="font-bold text-xl {{ $m['c']['text'] }} font-mono">{{ $m['val'] }}<span class="text-xs">{{ $m['unit'] }}</span></p>
                        <span class="inline-block w-1.5 h-1.5 rounded-full {{ $m['c']['dot'] }} mt-1"></span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Beras --}}
            <div>
                <p class="text-green-400 text-xs font-bold uppercase tracking-widest mb-3 flex items-center gap-2">
                    <span class="w-2 h-2 bg-green-500 rounded-full"></span> Beras (HGL)
                </p>
                <div class="grid grid-cols-3 gap-3">
                    @foreach([
                        ['label'=>'Derajat Sosoh','val'=>$avgDerajatSosoh,'c'=>$cSosoh,'unit'=>'%'],
                        ['label'=>'Butir Patah','val'=>$avgButirPatah,'c'=>$cPatah,'unit'=>'%'],
                        ['label'=>'Menir','val'=>$avgMenir,'c'=>$cMenir,'unit'=>'%'],
                    ] as $m)
                    <div class="border rounded-xl p-3 text-center {{ $m['c']['bg'] }}">
                        <p class="text-gray-500 text-[10px] uppercase tracking-wider mb-1">{{ $m['label'] }}</p>
                        <p class="font-bold text-xl {{ $m['c']['text'] }} font-mono">{{ $m['val'] }}<span class="text-xs">{{ $m['unit'] }}</span></p>
                        <span class="inline-block w-1.5 h-1.5 rounded-full {{ $m['c']['dot'] }} mt-1"></span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         TOP CABANG + AKTIVITAS TERBARU
    ════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top 5 Cabang Bulan Ini --}}
        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6 shadow-xl">
            <h3 class="text-gray-300 font-bold uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fa-solid fa-trophy text-yellow-500 text-sm"></i>
                Top Cabang — {{ date('M Y') }}
            </h3>
            @if(count($topCabang) > 0)
            <div class="space-y-3">
                @foreach($topCabang as $i => $row)
                @php
                    $medals    = ['text-yellow-400','text-gray-300','text-orange-400'];
                    $barColors = ['bg-yellow-500','bg-gray-400','bg-orange-400'];
                    $medal    = $medals[$i]    ?? 'text-gray-500';
                    $barColor = $barColors[$i] ?? 'bg-gray-600';
                @endphp
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm w-5 {{ $medal }}">{{ $i + 1 }}</span>
                            <span class="text-white text-sm font-medium truncate max-w-[140px]">{{ $row['name'] }}</span>
                        </div>
                        <span class="text-gray-400 text-xs font-mono">Rp {{ number_format($row['pendapatan'], 0, ',', '.') }}</span>
                    </div>
                    <div class="h-1.5 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $barColor }} transition-all" style="width: {{ $row['pct'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-600 text-sm text-center py-6">Belum ada data bulan ini</p>
            @endif
        </div>

        {{-- Aktivitas Terbaru --}}
        <div class="bg-gray-900 border border-gray-800 rounded-3xl p-6 shadow-xl">
            <h3 class="text-gray-300 font-bold uppercase tracking-wider mb-5 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-blue-400 text-sm"></i>
                Aktivitas Terbaru
            </h3>
            @if(count($recentActivity) > 0)
            <div class="space-y-3">
                @foreach($recentActivity as $log)
                <div class="flex items-start gap-3 py-2 border-b border-gray-800 last:border-0">
                    <div class="w-7 h-7 bg-gray-800 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-user text-gray-500 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium">{{ $log['desc'] }}</p>
                        <p class="text-gray-500 text-xs">{{ $log['nama'] }}</p>
                    </div>
                    <span class="text-gray-600 text-xs whitespace-nowrap flex-shrink-0">
                        {{ \Carbon\Carbon::parse($log['at'])->diffForHumans() }}
                    </span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-600 text-sm text-center py-6">Belum ada aktivitas</p>
            @endif
        </div>

    </div>
    @endif

    <script>
        var chartGabah = null;
        var chartBeras = null;

        const getChartOptions = (color) => ({
            chart: { type: 'bar', height: 280, toolbar: { show: false }, background: 'transparent', fontFamily: 'Space Grotesk' },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
            dataLabels: { enabled: false },
            grid: { borderColor: '#374151', strokeDashArray: 4 },
            xaxis: { labels: { style: { colors: '#9ca3af' } }, axisBorder: { show: false }, axisTicks: { show: false } },
            yaxis: { labels: { style: { colors: '#9ca3af' }, formatter: (val) => {
                if (val >= 1000000) return (val / 1000000).toFixed(1) + ' Jt';
                if (val >= 1000) return (val / 1000).toFixed(0) + ' K';
                return val;
            }}},
            tooltip: { theme: 'dark' },
            colors: [color],
            theme: { mode: 'dark' }
        });

        function loadDashboardCharts(data) {
            const chartGabahEl = document.querySelector("#chart-gabah");
            const chartBerasEl = document.querySelector("#chart-beras");
            if (!chartGabahEl || !chartBerasEl) return;

            if (chartGabah) chartGabah.destroy();
            if (chartBeras) chartBeras.destroy();

            let optsGabah = getChartOptions('#3b82f6');
            optsGabah.series = [{ name: 'Gabah (Kg)', data: data.gabah_values }];
            optsGabah.xaxis.categories = data.gabah_labels;
            chartGabah = new ApexCharts(chartGabahEl, optsGabah);
            chartGabah.render();

            let optsBeras = getChartOptions('#10b981');
            optsBeras.series = [{ name: 'Beras (Kg)', data: data.beras_values }];
            optsBeras.xaxis.categories = data.beras_labels;
            chartBeras = new ApexCharts(chartBerasEl, optsBeras);
            chartBeras.render();
        }

        const initialChartData = {
            gabah_labels: @json($gabahLabels),
            gabah_values: @json($gabahValues),
            beras_labels: @json($berasLabels),
            beras_values: @json($berasValues),
        };

        document.addEventListener('livewire:navigated', () => loadDashboardCharts(initialChartData));

        document.addEventListener('livewire:init', () => {
            loadDashboardCharts(initialChartData);

            Livewire.on('update-charts', (payload) => {
                let data = Array.isArray(payload) ? payload[0] : payload;
                if (!data) return;
                if (chartGabah && document.querySelector("#chart-gabah")) {
                    chartGabah.updateOptions({ xaxis: { categories: data.gabah_labels } }, false, false);
                    chartGabah.updateSeries([{ name: 'Gabah (Kg)', data: data.gabah_values }]);
                }
                if (chartBeras && document.querySelector("#chart-beras")) {
                    chartBeras.updateOptions({ xaxis: { categories: data.beras_labels } }, false, false);
                    chartBeras.updateSeries([{ name: 'Beras (Kg)', data: data.beras_values }]);
                }
            });
        });
    </script>
</div>
