<div class="min-h-screen bg-gray-100 font-['Space_Grotesk'] text-sm text-gray-800">
    
    <div class="bg-white p-6 shadow-sm mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Pemeriksaan HGL</h1>
        <p class="text-gray-600 mt-1">
            Periode {{ \Carbon\Carbon::parse($tgl_mulai)->format('d F Y') }} 
            S/D {{ \Carbon\Carbon::parse($tgl_akhir)->format('d F Y') }}
        </p>
    
        <div class="mt-6 flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Tanggal Pemeriksaan :</span>
                <input type="date" wire:model="tgl_mulai" class="border border-gray-300 rounded px-3 py-2 bg-gray-50 text-gray-700">
                <span class="text-gray-500 font-medium">s/d</span>
                <input type="date" wire:model="tgl_akhir" class="border border-gray-300 rounded px-3 py-2 bg-gray-50 text-gray-700">
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-2">
            <button wire:click="filter" class="bg-[#007bff] hover:bg-blue-600 text-white font-bold py-2 px-6 rounded shadow-sm active:scale-95">
                Tampilkan
            </button>
            <div class="bg-[#d39e00] text-white font-bold py-2 px-6 rounded shadow-sm flex items-center cursor-default">
                Total Record : {{ $total_record }}
            </div>
            <div class="bg-[#d39e00] text-white font-bold py-2 px-6 rounded shadow-sm flex items-center cursor-default">
                Total Penerimaan (Beras) : {{ number_format($total_penerimaan, 0, ',', '.') }}
            </div>
            <button wire:click="downloadExcel" class="bg-[#dc3545] hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow-sm active:scale-95">
                Download Excel
            </button>
            <button wire:click="downloadPdf" class="bg-[#dc3545] hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow-sm active:scale-95">
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
                            <th class="px-4 py-3 border-r border-orange-400 font-bold align-middle" rowspan="2">Nomor MO</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold text-center align-middle" rowspan="2">Kuantum GKP (Kg)</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold align-middle" rowspan="2">No. LHPK</th>
                            <th class="px-4 py-3 border-r border-orange-400 font-bold text-center align-middle" rowspan="2">Jumlah Kuantum Beras</th>
                            <th class="px-4 py-2 text-center font-bold border-b border-orange-400" colspan="7">Hasil Analisa</th>
                        </tr>
                        <tr class="bg-[#e65100] text-white text-xs uppercase tracking-wider">
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Derajat Sosoh</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Ulangan 1</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Ulangan 2</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Ulangan 3</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Kadar Air</th>
                            <th class="px-2 py-2 border-r border-orange-400 text-center font-bold">Butir Patah</th>
                            <th class="px-2 py-2 text-center font-bold">Butir Menir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-xs text-gray-700">
                        @forelse($data_laporan as $index => $item)
                        <tr class="hover:bg-orange-50 transition-colors {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $data_laporan->firstItem() + $index }}</td>
                            
                            <td class="px-4 py-3 border-r border-gray-200">{{ $item->parent_company ?? '-' }}</td>
                            <td class="px-4 py-3 border-r border-gray-200">{{ $item->name_cabang ?? '-' }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 uppercase">-</td> <td class="px-4 py-3 border-r border-gray-200 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($item->tanggal_pemeriksaan)->format('Y-m-d') }}
                            </td>
                            
                            <td class="px-4 py-3 border-r border-gray-200">{{ $item->id_mo ?? '-' }}</td>
                            
                            <td class="px-4 py-3 border-r border-gray-200 font-bold text-right text-gray-600">
                                {{ number_format((float) str_replace(',', '.', $item->kuantum_gabah_sesuai_mo ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 border-r border-gray-200 font-mono text-[10px]">{{ $item->nomor_lhpk_beras ?? '-' }}</td>
                            
                            <td class="px-4 py-3 border-r border-gray-200 font-bold text-right text-gray-900">
                                {{ number_format((float) str_replace(',', '.', $item->kuantum_beras ?? 0), 0, ',', '.') }}
                            </td>

                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->derajat_sosoh ?? 0 }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->ulangan_1 ?? 0 }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->ulangan_2 ?? 0 }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->ulangan_3 ?? 0 }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center font-bold text-blue-600">{{ $item->rata_rata ?? 0 }}</td>
                            <td class="px-4 py-3 border-r border-gray-200 text-center">{{ $item->butir_patah ?? 0 }}</td>
                            <td class="px-4 py-3 text-center">{{ $item->menir ?? 0 }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="17" class="px-6 py-12 text-center text-gray-500 bg-white">
                                Tidak ada data HGL pada periode ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $data_laporan->links() }}
            </div>
        </div>
    </div>
</div>