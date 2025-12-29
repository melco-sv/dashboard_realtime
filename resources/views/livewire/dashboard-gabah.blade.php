<div wire:poll.10s class="space-y-8"> 
    
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
            </div>

            <div class="lg:col-span-2 bg-gray-800/50 rounded-2xl p-4 border border-gray-700 backdrop-blur-sm" wire:ignore>
                <h4 class="text-xs text-gray-400 uppercase mb-4 ml-2">Tren Pemasukan (Bulanan)</h4>
                <div id="chart-gabah"></div>
            </div>
        </div>
    </div>

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
            </div>

            <div class="lg:col-span-2 bg-gray-800/50 rounded-2xl p-4 border border-gray-700 backdrop-blur-sm" wire:ignore>
                <h4 class="text-xs text-gray-400 uppercase mb-4 ml-2">Tren Produksi (Bulanan)</h4>
                <div id="chart-beras"></div>
            </div>
        </div>
    </div>

    <script>
        // 1. Definisikan Variabel Global agar bisa diakses/direset
        var chartGabah = null;
        var chartBeras = null;

        // 2. Bungkus Logika Render Chart dalam satu fungsi
        function loadDashboardCharts() {
            // Cek apakah elemen chart ada di layar? (Mencegah error di halaman lain)
            const chartGabahEl = document.querySelector("#chart-gabah");
            const chartBerasEl = document.querySelector("#chart-beras");

            if (!chartGabahEl || !chartBerasEl) return;

            // --- A. BERSIHKAN CHART LAMA (PENTING AGAR TIDAK DUPLIKAT/ERROR) ---
            if (chartGabah) chartGabah.destroy();
            if (chartBeras) chartBeras.destroy();

            // --- OPSI CHART ---
            const getChartOptions = (color) => ({
                chart: { type: 'bar', height: 280, toolbar: { show: false }, background: 'transparent', fontFamily: 'Space Grotesk' },
                plotOptions: { bar: { borderRadius: 4, columnWidth: '40%' } },
                dataLabels: { enabled: false },
                grid: { borderColor: '#374151', strokeDashArray: 4 },
                xaxis: { 
                    labels: { style: { colors: '#9ca3af' } },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: { 
                    labels: { 
                        style: { colors: '#9ca3af' },
                        formatter: (val) => {
                            if(val >= 1000000) return (val/1000000).toFixed(1) + ' Jt';
                            if(val >= 1000) return (val/1000).toFixed(0) + ' K';
                            return val;
                        }
                    } 
                },
                tooltip: { theme: 'dark' },
                colors: [color],
                theme: { mode: 'dark' }
            });

            // --- B. RENDER CHART BARU ---
            chartGabah = new ApexCharts(chartGabahEl, getChartOptions('#3b82f6'));
            chartGabah.render();

            chartBeras = new ApexCharts(chartBerasEl, getChartOptions('#10b981'));
            chartBeras.render();
        }

        // 3. JALANKAN FUNGSI SAAT NAVIGASI SELESAI (Back/Forward/Link)
        document.addEventListener('livewire:navigated', () => {
            loadDashboardCharts();
        });

        // 4. LISTENER UNTUK UPDATE DATA REALTIME (POLLING)
        // Kita letakkan di luar loadDashboardCharts agar tidak ter-register double
        document.addEventListener('livewire:init', () => {
            // Jalankan sekali saat pertama kali load F5
            loadDashboardCharts();

            Livewire.on('update-charts', (payload) => {
                let data = Array.isArray(payload) ? payload[0] : payload;
                if(data) {
                    // Cek if chart instance exists & element is still on DOM
                    if(chartGabah && document.querySelector("#chart-gabah")) {
                        if(data.gabah_labels && data.gabah_values) {
                            chartGabah.updateOptions({ xaxis: { categories: data.gabah_labels } });
                            chartGabah.updateSeries([{ name: 'Gabah (Kg)', data: data.gabah_values }]);
                        }
                    }
                    if(chartBeras && document.querySelector("#chart-beras")) {
                        if(data.beras_labels && data.beras_values) {
                            chartBeras.updateOptions({ xaxis: { categories: data.beras_labels } });
                            chartBeras.updateSeries([{ name: 'Beras (Kg)', data: data.beras_values }]);
                        }
                    }
                }
            });
        });
    </script>
</div>