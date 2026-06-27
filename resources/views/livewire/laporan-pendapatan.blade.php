<div class="min-h-screen bg-[#0b0c15] p-4 md:p-6 text-white font-['Space_Grotesk']">

    {{-- HEADER --}}
    <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Laporan Pendapatan per Cabang</h1>
            <p class="text-gray-400 text-sm mt-0.5">PT Sucofindo — pendapatan pemeriksaan GKP &amp; HGL, diurutkan dari tertinggi</p>
        </div>
        <button wire:click="download" wire:loading.attr="disabled"
            class="bg-green-600 hover:bg-green-500 disabled:opacity-60 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-green-500/20 transition-all inline-flex items-center gap-2">
            <i class="fa-solid fa-file-excel"></i>
            <span wire:loading.remove wire:target="download">Download Excel</span>
            <span wire:loading wire:target="download">Menyiapkan...</span>
        </button>
    </div>

    {{-- FILTER PERIODE (opsional) --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Dari Tanggal</label>
                <input type="date" wire:model.live="tgl_mulai"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Sampai</label>
                <input type="date" wire:model.live="tgl_akhir"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
            </div>
            @if ($tgl_mulai || $tgl_akhir)
            <button wire:click="$set('tgl_mulai', ''); $set('tgl_akhir', '')"
                class="text-gray-400 hover:text-white text-xs px-2 py-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                <i class="fa-solid fa-xmark"></i> Reset
            </button>
            @endif
            <span class="text-[11px] text-gray-500 self-end pb-1.5">Kosongkan untuk seluruh periode</span>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Total Pendapatan</p>
            <p class="text-xl font-bold text-green-400">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Cabang Tertinggi</p>
            <p class="text-sm font-bold text-yellow-300 truncate">{{ $top->cabang ?? '-' }}</p>
            <p class="text-[11px] text-gray-500">{{ $top ? 'Rp ' . number_format($top->total_pendapatan, 0, ',', '.') : '—' }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Total Tonase Gabah</p>
            <p class="text-lg font-bold text-yellow-400">{{ number_format($grandGabahKg, 0, ',', '.') }} <span class="text-xs text-gray-500">Kg</span></p>
            <p class="text-[11px] text-gray-600">{{ number_format($grandGabahKg / 1000, 2, ',', '.') }} Ton</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Total Tonase Beras</p>
            <p class="text-lg font-bold text-green-400">{{ number_format($grandBerasKg, 0, ',', '.') }} <span class="text-xs text-gray-500">Kg</span></p>
            <p class="text-[11px] text-gray-600">{{ number_format($grandBerasKg / 1000, 2, ',', '.') }} Ton</p>
        </div>
    </div>

    {{-- TABEL PREVIEW --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-x-auto">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-800/70 text-[10px] text-gray-400 uppercase tracking-wider">
                    <th class="px-3 py-2.5 text-left font-bold">#</th>
                    <th class="px-3 py-2.5 text-left font-bold">Cabang</th>
                    <th class="px-3 py-2.5 text-left font-bold">Wilayah</th>
                    <th class="px-3 py-2.5 text-right font-bold">Tonase Gabah (Kg)</th>
                    <th class="px-3 py-2.5 text-right font-bold">Tonase Beras (Kg)</th>
                    <th class="px-3 py-2.5 text-right font-bold">Pendapatan Gabah</th>
                    <th class="px-3 py-2.5 text-right font-bold">Pendapatan Beras</th>
                    <th class="px-3 py-2.5 text-right font-bold text-green-400">Total Pendapatan</th>
                    <th class="px-3 py-2.5 text-right font-bold">Kontribusi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/60">
                @forelse ($rows as $i => $row)
                <tr class="hover:bg-gray-800/30 transition-colors {{ $i === 0 ? 'bg-yellow-500/5' : '' }}">
                    <td class="px-3 py-2 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-3 py-2 font-semibold text-white">
                        @if ($i === 0)<i class="fa-solid fa-crown text-yellow-400 mr-1"></i>@endif
                        {{ $row->cabang }}
                    </td>
                    <td class="px-3 py-2 text-gray-400">{{ $row->wilayah }}</td>
                    <td class="px-3 py-2 text-right text-yellow-300">{{ number_format($row->gabah_kg, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-right text-green-300">{{ number_format($row->beras_kg, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-right text-gray-300">Rp {{ number_format($row->pendapatan_gabah, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-right text-gray-300">Rp {{ number_format($row->pendapatan_beras, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-right font-bold text-green-400">Rp {{ number_format($row->total_pendapatan, 0, ',', '.') }}</td>
                    <td class="px-3 py-2 text-right text-gray-400">{{ number_format($row->kontribusi, 2, ',', '.') }}%</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-10 text-center text-gray-500">Belum ada data pendapatan.</td>
                </tr>
                @endforelse
            </tbody>
            @if ($rows->isNotEmpty())
            <tfoot>
                <tr class="bg-gray-800/50 font-bold text-white border-t border-gray-700">
                    <td class="px-3 py-2.5" colspan="3">TOTAL</td>
                    <td class="px-3 py-2.5 text-right text-yellow-300">{{ number_format($grandGabahKg, 0, ',', '.') }}</td>
                    <td class="px-3 py-2.5 text-right text-green-300">{{ number_format($grandBerasKg, 0, ',', '.') }}</td>
                    <td class="px-3 py-2.5" colspan="2"></td>
                    <td class="px-3 py-2.5 text-right text-green-400">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    <td class="px-3 py-2.5 text-right">100%</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

</div>
