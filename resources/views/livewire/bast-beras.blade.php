<div x-data="{ confirmCetak: false }" x-on:open-pdf.window="let a=document.createElement('a');a.href=$event.detail.url;a.target='_blank';a.rel='noopener';document.body.appendChild(a);a.click();document.body.removeChild(a);" class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">BAST Beras HGL</h1>
        <p class="text-gray-400 text-sm mt-1">Berita Acara Serah Terima — Dokumen Rekapitulasi Beras Hasil Giling</p>
    </div>

    @if (session()->has('message'))
    <div class="mb-4 bg-green-500/10 border border-green-500 text-green-400 px-4 py-3 rounded-lg text-sm font-bold">
        {{ session('message') }}
    </div>
    @endif

    @if($pdfToken)
    <div wire:poll.2s="checkPdfReady" class="mb-4 bg-blue-500/10 border border-blue-500 text-blue-300 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-3">
        <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
        Sedang menyiapkan PDF, harap tunggu sebentar...
    </div>
    @endif

    @error('pdf')
    <div class="mb-4 bg-red-500/10 border border-red-500 text-red-400 px-4 py-3 rounded-lg text-sm font-bold">
        {{ $message }}
    </div>
    @enderror

    {{-- FILTER --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-gray-400 text-xs font-bold mb-1 uppercase tracking-wider">Dari Tanggal</label>
                <input type="date" wire:model.live="tgl_mulai"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-green-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-gray-400 text-xs font-bold mb-1 uppercase tracking-wider">Sampai Tanggal</label>
                <input type="date" wire:model.live="tgl_akhir"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-green-500 focus:outline-none">
            </div>
            <button wire:click="filter"
                class="bg-green-600 hover:bg-green-500 text-white px-5 py-2 rounded-lg text-sm font-bold transition-colors">
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
            <p class="text-2xl font-bold text-green-400">{{ number_format($total_kg, 3, ',', '.') }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Total Kuantum (Ton)</p>
            <p class="text-2xl font-bold text-green-400">{{ number_format($totalTon, 3, ',', '.') }}</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-4">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Estimasi Biaya (Rp)</p>
            <p class="text-lg font-bold text-yellow-400">{{ number_format($estimasiBiaya, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- PREVIEW TABLE --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden mb-6">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-4 sm:px-5 py-3 border-b border-gray-800">
            <h2 class="text-sm font-bold text-gray-300 uppercase tracking-wider">Preview Data BAST</h2>
            <button type="button" @click="confirmCetak = true" wire:loading.attr="disabled" wire:target="cetakPdf"
                class="w-full sm:w-auto bg-green-600 hover:bg-green-500 disabled:opacity-60 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <span wire:loading.remove wire:target="cetakPdf">Cetak BAST PDF</span>
                <span wire:loading wire:target="cetakPdf">Menyiapkan...</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-800/50">
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">No</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kanwil / Kanca</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Pelaksana Pengolahan</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Tanggal</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">No. MO</th>
                        <th class="text-left px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">No. LHPK</th>
                        <th class="text-right px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Kuantum Beras (Kg)</th>
                        <th class="text-right px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Tarif (Rp/Kg)</th>
                        <th class="text-right px-4 py-3 text-[10px] text-gray-400 uppercase tracking-wider font-bold">Biaya (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800">
                    @forelse ($dataList as $i => $row)
                    @php
                        $kg = (float) str_replace(',', '.', $row->kuantum_beras ?? 0);
                        $tarifVal = (float) str_replace(',', '.', $tarif);
                        $biaya = $kg * $tarifVal;
                    @endphp
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $dataList->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3">
                            <div class="text-[10px] text-gray-500">{{ $row->parent_company ?? '-' }}</div>
                            <div class="text-xs font-bold text-white">{{ $row->name_cabang ?? '-' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-300">{{ $row->tempat_pemeriksaan ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-300">{{ \Carbon\Carbon::parse($row->tanggal_pemeriksaan)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-xs font-mono text-gray-300">{{ $row->id_mo ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs font-mono text-gray-300">{{ $row->nomor_hpkk_beras ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-green-400">{{ number_format($kg, 3, ',', '.') }}</td>
                        <td class="px-4 py-3 text-xs text-right text-gray-300">{{ number_format($tarifVal, 2, ',', '.') }}</td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-yellow-400">{{ number_format($biaya, 0, ',', '.') }}</td>
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
                        <td class="px-4 py-3 text-xs text-right font-bold text-green-400">{{ number_format($total_kg, 3, ',', '.') }}</td>
                        <td></td>
                        <td class="px-4 py-3 text-xs text-right font-bold text-yellow-400">{{ number_format($estimasiBiaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <div class="px-4 py-3 border-t border-gray-800">
            {{ $dataList->links() }}
        </div>
    </div>

    {{-- Modal "Lengkapi Data BAST" tidak dipakai lagi — nomor surat otomatis, tarif & nama pejabat diambil dari Pengaturan (BAST → Pengaturan Pejabat). Cetak langsung dari tombol di atas. --}}

    {{-- MODAL KONFIRMASI CETAK — mencegah nomor surat BAST terbit karena salah periode --}}
    <div x-show="confirmCetak" x-cloak @click="confirmCetak = false"
        x-on:keydown.escape.window="confirmCetak = false"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">

        <div @click.stop
            class="bg-[#1a1d2d] w-full max-w-lg max-h-[90vh] rounded-2xl border border-gray-700 shadow-2xl flex flex-col overflow-hidden">

            {{-- Header --}}
            <div class="px-5 py-4 border-b border-gray-700 bg-[#11131f] flex items-start gap-3 flex-shrink-0">
                <div class="w-9 h-9 rounded-full bg-yellow-500/10 border border-yellow-500/40 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-400"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-white">Konfirmasi Cetak BAST HGL</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Nomor surat akan diterbitkan begitu Anda melanjutkan</p>
                </div>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-4 overflow-y-auto flex-1 min-h-0">

                {{-- Ringkasan data yang akan dicetak --}}
                <div class="bg-[#0b0c15] border border-gray-700/60 rounded-xl divide-y divide-gray-800">
                    <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Periode</span>
                        <span class="text-sm font-bold text-white text-right">
                            {{ \Carbon\Carbon::parse($tgl_mulai)->format('d/m/Y') }}
                            <span class="text-gray-500 font-normal">s.d.</span>
                            {{ \Carbon\Carbon::parse($tgl_akhir)->format('d/m/Y') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Nomor Surat</span>
                        <span class="text-sm font-bold font-mono text-green-400 text-right break-all">{{ $nomor_surat ?: '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Total Dokumen</span>
                        <span class="text-sm font-bold text-white text-right">{{ number_format($total_record) }}</span>
                    </div>
                    <div class="flex items-center justify-between gap-3 px-4 py-2.5">
                        <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Total Kuantum</span>
                        <span class="text-sm font-bold text-white text-right">{{ number_format($total_kg, 3, ',', '.') }} Kg</span>
                    </div>
                </div>

                {{-- Peringatan utama --}}
                <div class="bg-yellow-500/10 border border-yellow-500/40 rounded-xl p-4">
                    <p class="text-sm font-bold text-yellow-300 mb-2">
                        Pastikan periode dan data di atas sudah benar sebelum melanjutkan.
                    </p>
                    <ul class="text-xs text-yellow-100/80 space-y-1.5 list-disc pl-4 leading-relaxed">
                        <li>Nomor surat BAST akan <span class="font-bold">tercatat permanen</span> dan tidak dapat dibatalkan.</li>
                        <li>Jika periode yang dicetak keliru, nomor surat itu tetap terpakai — akibatnya penomoran BAST menjadi tidak berurutan dan Admin Pusat menerima dokumen yang sebenarnya tidak perlu diapprove.</li>
                        <li>Mencetak ulang <span class="font-bold">periode yang sama</span> tetap memakai nomor surat yang sama, jadi aman.</li>
                    </ul>
                </div>

                @if ($total_record == 0)
                <div class="bg-red-500/10 border border-red-500/50 rounded-xl p-3.5">
                    <p class="text-xs text-red-300 font-bold leading-relaxed">
                        Tidak ada data pada periode ini. Melanjutkan akan menerbitkan BAST kosong dan tetap memakai satu nomor surat.
                    </p>
                </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="px-5 py-4 border-t border-gray-700 bg-[#11131f] flex flex-col-reverse sm:flex-row sm:justify-end gap-2 flex-shrink-0">
                <button type="button" @click="confirmCetak = false"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-lg text-sm font-bold text-gray-300 bg-gray-800 hover:bg-gray-700 border border-gray-700 transition-colors">
                    Batal, Periksa Lagi
                </button>
                <button type="button" wire:click="cetakPdf" @click="confirmCetak = false"
                    class="w-full sm:w-auto px-4 py-2.5 rounded-lg text-sm font-bold text-white bg-green-600 hover:bg-green-500 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-arrow-down"></i>
                    Ya, Cetak Sekarang
                </button>
            </div>
        </div>
    </div>
</div>
