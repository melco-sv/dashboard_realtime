<div x-data x-on:open-pdf.window="window.open($event.detail.url, '_blank')" class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">BAST Gabah GKP</h1>
        <p class="text-gray-400 text-sm mt-1">Berita Acara Serah Terima — Dokumen Rekapitulasi Gabah Kering Panen</p>
    </div>

    @if (session()->has('message'))
    <div class="mb-4 bg-green-500/10 border border-green-500 text-green-400 px-4 py-3 rounded-lg text-sm font-bold">
        {{ session('message') }}
    </div>
    @endif

    {{-- FILTER --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-gray-400 text-xs font-bold mb-1 uppercase tracking-wider">Dari Tanggal</label>
                <input type="date" wire:model.live="tgl_mulai"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-yellow-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-gray-400 text-xs font-bold mb-1 uppercase tracking-wider">Sampai Tanggal</label>
                <input type="date" wire:model.live="tgl_akhir"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-yellow-500 focus:outline-none">
            </div>
            <button wire:click="filter"
                class="bg-yellow-600 hover:bg-yellow-500 text-white px-5 py-2 rounded-lg text-sm font-bold transition-colors">
                Terapkan Filter
            </button>
        </div>
    </div>

    {{-- STATS CARDS --}}
    @php
        $totalTon = $total_kg / 1000;
        $estimasiBiaya = $total_kg * (float) str_replace(',', '.', $tarif);
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Total Dokumen</p>
            <p class="text-2xl font-bold text-white">{{ number_format($total_record) }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Total Kuantum (Kg)</p>
            <p class="text-2xl font-bold text-yellow-400">{{ number_format($total_kg, 3, ',', '.') }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Total Kuantum (Ton)</p>
            <p class="text-2xl font-bold text-yellow-400">{{ number_format($totalTon, 3, ',', '.') }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Estimasi Biaya (Rp)</p>
            <p class="text-lg font-bold text-orange-400">{{ number_format($estimasiBiaya, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- PREVIEW TABLE --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-6">
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-800">
            <h2 class="text-sm font-bold text-gray-300 uppercase tracking-wider">Preview Data BAST</h2>
            <button wire:click="openModal"
                class="bg-yellow-600 hover:bg-yellow-500 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                Cetak BAST PDF
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-800/50">
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">No</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kanwil / Kanca</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Pengirim</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Tanggal</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">No. PO</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">No. HPK</th>
                        <th class="text-right px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kuantum Gabah (Kg)</th>
                        <th class="text-right px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Tarif (Rp/Kg)</th>
                        <th class="text-right px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Biaya (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($dataList as $i => $row)
                    @php
                        $kg = (float) str_replace(',', '.', $row->jumlah_timbangan ?? 0);
                        $tarifVal = (float) str_replace(',', '.', $tarif);
                        $biaya = $kg * $tarifVal;
                    @endphp
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $dataList->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <div class="text-[10px] text-gray-500">{{ $row->parent_company ?? '-' }}</div>
                            <div class="text-xs font-bold text-white">{{ $row->name_cabang ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-300">{{ $row->pengirim ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-300">{{ \Carbon\Carbon::parse($row->tanggal_pelaksanaan)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-xs font-mono text-gray-300">{{ $row->no_order_pembelian ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-mono text-gray-300">{{ $row->nomor_hpkk_gabah ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-yellow-400">{{ number_format($kg, 3, ',', '.') }}</td>
                        <td class="px-4 py-3 text-xs text-right text-gray-300">{{ number_format($tarifVal, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-orange-400">{{ number_format($biaya, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                            Tidak ada data pada rentang tanggal yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if ($dataList->count() > 0)
                <tfoot>
                    <tr class="bg-gray-800/50 border-t border-gray-700">
                        <td colspan="6" class="px-4 py-3 text-xs font-bold text-gray-400 uppercase">Total</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-yellow-400">{{ number_format($total_kg, 3, ',', '.') }}</td>
                        <td></td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-orange-400">{{ number_format($estimasiBiaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-800">
            {{ $dataList->links() }}
        </div>
    </div>

    {{-- MODAL --}}
    @if ($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
        <div class="bg-gray-900 border border-gray-700 rounded-2xl p-6 w-full max-w-lg shadow-2xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-lg font-bold text-white">Lengkapi Data BAST</h3>
                <button wire:click="closeModal" class="text-gray-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-1 uppercase tracking-wider">Nomor Surat</label>
                    <input type="text" wire:model="nomor_surat"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-yellow-500 focus:outline-none font-mono">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-1 uppercase tracking-wider">Nama Kepala Unit Pelayanan (SUCOFINDO)</label>
                    <input type="text" wire:model="nama_kepala_unit" placeholder="Nama lengkap..."
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-yellow-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-1 uppercase tracking-wider">Nama Pimpinan Cabang Bulog</label>
                    <input type="text" wire:model="nama_pimpinan_cabang" placeholder="Nama lengkap..."
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-yellow-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-1 uppercase tracking-wider">
                        Tarif Pemeriksaan (Rp/Kg)
                        @if(!Auth::user()->isSuperAdmin())
                        <span class="text-gray-600 font-normal normal-case ml-1">— hanya Super Admin yang dapat mengubah</span>
                        @endif
                    </label>
                    @if(Auth::user()->isSuperAdmin())
                    <input type="number" step="0.01" wire:model="tarif" placeholder="46.40"
                        class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-yellow-500 focus:outline-none">
                    @else
                    <div class="w-full bg-gray-800/50 border border-gray-700/50 text-gray-400 rounded-lg px-3 py-2 text-sm font-mono cursor-not-allowed">
                        {{ $tarif }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button wire:click="cetakPdf"
                    class="flex-1 bg-yellow-600 hover:bg-yellow-500 text-white font-bold py-2.5 rounded-xl text-sm text-center transition-colors">
                    Cetak PDF
                </button>
                <button wire:click="closeModal"
                    class="px-6 bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
