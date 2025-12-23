<div class="min-h-screen bg-gray-100 font-['Space_Grotesk'] text-sm text-gray-800">
    
    <div class="bg-white p-6 shadow-sm mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Pemeriksaan GKP</h1>
        <p class="text-gray-600 mt-1">
            Periode {{ \Carbon\Carbon::parse($tgl_mulai)->format('d F Y') }} 
            S/D {{ \Carbon\Carbon::parse($tgl_akhir)->format('d F Y') }}
        </p>
    
        <div class="mt-6 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Tanggal Pelaksanaan :</span>
                <input type="date" wire:model="tgl_mulai" class="border border-gray-300 rounded px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:border-blue-500 transition-colors">
                <span class="text-gray-500 font-medium">s/d</span>
                <input type="date" wire:model="tgl_akhir" class="border border-gray-300 rounded px-3 py-2 bg-gray-50 text-gray-700 focus:outline-none focus:border-blue-500 transition-colors">
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <button wire:click="filter" class="bg-[#007bff] hover:bg-blue-600 text-white font-bold py-2 px-6 rounded shadow-sm transition-transform active:scale-95">
                Tampilkan
            </button>
            
            <div class="bg-[#d39e00] text-white font-bold py-2 px-6 rounded shadow-sm flex items-center cursor-default">
                Total Record : {{ $total_record }}
            </div>

            <div class="bg-[#d39e00] text-white font-bold py-2 px-6 rounded shadow-sm flex items-center cursor-default">
                Total Penerimaan : {{ number_format($total_penerimaan, 0, ',', '.') }}
            </div>

            <button wire:click="downloadExcelTarif" class="bg-[#dc3545] hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow-sm transition-transform active:scale-95">
                Download Rekap Tarif
            </button>

            <button wire:click="downloadExcelAnalisa" class="bg-[#dc3545] hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow-sm transition-transform active:scale-95">
                Download Rekap Analisa
            </button>

            <button wire:click="downloadPdf" class="bg-[#dc3545] hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow-sm transition-transform active:scale-95">
                Download PDF
            </button>
        </div>
    </div>

    <div class="px-6 pb-6">
        <div class="bg-white border border-gray-200 shadow-sm overflow-hidden rounded-lg flex flex-col">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-[#e65100] text-white text-xs uppercase tracking-wider">
                            <th class="px-4 py-3 border-r border-orange-400 font-bold text-center align-middle" rowspan="2">No</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold align-middle" rowspan="2">Kantor Wilayah</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold align-middle" rowspan="2">Kantor Cabang</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold align-middle" rowspan="2">Pelaksana Pengolahan</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold align-middle" rowspan="2">Tanggal</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold align-middle" rowspan="2">No. PO</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold align-middle" rowspan="2">No. LHPK</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold text-center align-middle" rowspan="2">Kuantum GKP (Kg)</th>
                            <th class="px-4 py-2 text-center font-bold border-b border-orange-400" colspan="6">Hasil Analisa</th>
                        </tr>
                        <tr class="bg-[#e65100] text-white text-xs uppercase tracking-wider">
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Ulangan 1</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Ulangan 2</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Ulangan 3</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Kadar Air</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Kadar Hampa</th>
                            <th class="px-2 py-2 text-center font-bold">Kadar Butir Hijau</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-xs text-gray-700">
                        @forelse($data_laporan as $index => $item)
                        <tr class="hover:bg-orange-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border-r border-gray-200 text-center font-medium">
                                {{ $data_laporan->firstItem() + $index }}
                            </td>
                            
                            <td class="px-4 py-3 border-r border-gray-200">{{ $item->parent_company ?? '-' }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 font-medium text-gray-900">{{ $item->name_cabang ?? '-' }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 uppercase">{{ $item->mitra }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal_pelaksanaan)->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">{{ $item->no_order_pembelian }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 font-mono text-[10px]">{{ $item->nomor_hpkk_gabah }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 font-bold text-gray-900 text-right">
                                @php
                                    $beratBersih = (float) str_replace(',', '.', $item->jumlah_timbangan);
                                @endphp
                                {{ number_format($beratBersih, 0, ',', '.') }}
                            </td>
                            
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->ulangan_1 }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->ulangan_2 }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->ulangan_3 }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center font-bold text-blue-600">{{ $item->kadar_air_rata_rata }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->kadar_hampa }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->butir_hijau }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="14" class="px-6 py-12 text-center text-gray-500 bg-white">
                                <div class="flex flex-col items-center justify-center">
                                    <p class="text-sm font-medium">Tidak ada data transaksi pada periode ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $data_laporan->links() }}
            </div>
            
            <div class="bg-gray-100 px-6 py-4 border-t border-gray-200 flex justify-end gap-6 text-xs font-bold text-gray-600 uppercase">
                <span>Total Record: {{ $total_record }}</span>
                <span>Total Kuantum: {{ number_format($total_penerimaan, 0, ',', '.') }} Kg</span>
            </div>
        </div>
    </div>
</div>