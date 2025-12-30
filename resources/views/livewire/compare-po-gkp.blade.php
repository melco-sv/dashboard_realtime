<div class="min-h-screen bg-[#0b0c15] p-6 font-['Space_Grotesk'] text-white">

    <div class="max-w-7xl mx-auto mb-8 bg-[#1a1d2d] border border-gray-700 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-600/10 rounded-full blur-3xl -mr-10 -mt-10"></div>
        <h1 class="text-2xl md:text-3xl font-bold text-center text-white tracking-wide relative z-10">Laporan SCI Berdasarkan PO</h1>
        <p class="text-center text-gray-400 text-sm mt-1">Komparasi Data GKP</p>
    </div>

    <div class="max-w-4xl mx-auto mb-8">
        <form wire:submit.prevent="cari" class="flex flex-col md:flex-row gap-4 items-center justify-center bg-[#1a1d2d] p-4 rounded-xl border border-gray-700/50 shadow-lg">
            <div class="w-full md:w-3/4 relative">
                <input type="text" wire:model="searchPo" placeholder="Masukkan Nomor PO (Contoh: PO/1391/03/2025...)" class="w-full bg-[#0b0c15] border border-gray-600 text-white rounded-lg px-4 py-3 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-500">
            </div>
            <button type="submit" class="w-full md:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-lg shadow-lg flex items-center justify-center gap-2"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        </form>
        @if (session()->has('error'))
            <div class="mt-4 p-4 bg-red-500/10 border border-red-500/50 text-red-400 rounded-xl text-center font-bold animate-pulse">{{ session('error') }}</div>
        @endif
    </div>

    @if($resultData)
    <div class="max-w-7xl mx-auto space-y-8 animate-fade-in-up">
        
        <div class="bg-[#1a1d2d] border border-gray-700 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-800 text-gray-300 font-bold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="px-4 py-4 text-center border-r border-gray-700">No</th>
                            <th class="px-4 py-4 border-r border-gray-700">Kanwil</th>
                            <th class="px-4 py-4 border-r border-gray-700">PO SCI</th>
                            <th class="px-4 py-4 text-right border-r border-gray-700">Kuantum (SCI)</th>
                            <th class="px-4 py-4 text-center border-r border-gray-700">Kadar Air 1</th>
                            <th class="px-4 py-4 text-center border-r border-gray-700">Kadar Air 2</th>
                            <th class="px-4 py-4 text-center border-r border-gray-700">Kadar Air 3</th>
                            <th class="px-4 py-4 text-center border-r border-gray-700">Kadar Hampa</th>
                            <th class="px-4 py-4 text-center">Butir Hijau</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700">
                        @foreach($resultData as $index => $row)
                        <tr class="hover:bg-gray-800/50 transition-colors">
                            <td class="px-4 py-3 text-center border-r border-gray-700/50">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 border-r border-gray-700/50 font-medium text-blue-300">{{ $row->parent_company ?? '-' }}</td>
                            <td class="px-4 py-3 border-r border-gray-700/50 text-gray-300">{{ $row->no_order_pembelian }}</td>
                            
                            <td class="px-4 py-3 text-right border-r border-gray-700/50 font-mono text-white">
                                {{ $row->jumlah_timbangan }}
                            </td>
                            <td class="px-4 py-3 text-center border-r border-gray-700/50">{{ $row->ulangan_1 }}</td>
                            <td class="px-4 py-3 text-center border-r border-gray-700/50">{{ $row->ulangan_2 }}</td>
                            <td class="px-4 py-3 text-center border-r border-gray-700/50">{{ $row->ulangan_3 }}</td>
                            <td class="px-4 py-3 text-center border-r border-gray-700/50">{{ $row->kadar_hampa }}</td>
                            <td class="px-4 py-3 text-center">{{ $row->butir_hijau }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="max-w-4xl mx-auto">
            <div class="bg-[#1a1d2d] border border-blue-900 rounded-2xl overflow-hidden shadow-2xl">
                <div class="bg-blue-900/40 px-6 py-3 border-b border-blue-800">
                    <h3 class="text-center font-bold text-blue-200 uppercase tracking-widest text-sm">Ringkasan Data (Rata-rata)</h3>
                </div>
                <div class="divide-y divide-gray-700 text-sm">
    
    <div class="flex justify-between items-center px-6 py-4 hover:bg-white/5">
        <span class="font-bold text-gray-300">Total Kuantum</span>
        <span class="font-bold text-xl text-white font-mono">{{ $this->formatSummary($totalKuantum) }}</span>
    </div>

    <div class="flex justify-between items-center px-6 py-3 hover:bg-white/5">
        <span class="text-gray-400">Rata-rata Kadar Air 1</span>
        <span class="font-medium text-gray-200">{{ $this->formatSummary($avgKa1) }}</span>
    </div>

    <div class="flex justify-between items-center px-6 py-3 hover:bg-white/5">
        <span class="text-gray-400">Rata-rata Kadar Air 2</span>
        <span class="font-medium text-gray-200">{{ $this->formatSummary($avgKa2) }}</span>
    </div>

    <div class="flex justify-between items-center px-6 py-3 hover:bg-white/5">
        <span class="text-gray-400">Rata-rata Kadar Air 3</span>
        <span class="font-medium text-gray-200">{{ $this->formatSummary($avgKa3) }}</span>
    </div>

    <div class="flex justify-between items-center px-6 py-3 hover:bg-white/5">
        <span class="text-gray-400">Rata-rata Kadar Hampa</span>
        <span class="font-medium text-gray-200">{{ $this->formatSummary($avgHampa) }}</span>
    </div>

    <div class="flex justify-between items-center px-6 py-3 hover:bg-white/5">
        <span class="text-gray-400">Rata-rata Butir Hijau</span>
        <span class="font-medium text-gray-200">{{ $this->formatSummary($avgButirHijau) }}</span>
    </div>

</div>
            </div>
        </div>

    </div>
    @endif
</div>