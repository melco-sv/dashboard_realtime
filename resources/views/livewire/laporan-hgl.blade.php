<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">

    <div class="max-w-7xl mx-auto mb-8 flex flex-col md:flex-row justify-between items-start md:items-center border-b border-gray-800 pb-4 gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-emerald-600">
                Laporan HGL (Beras)
            </h1>
            <p class="text-gray-400 text-sm mt-1">
                Periode {{ \Carbon\Carbon::parse($tgl_mulai)->format('d F Y') }}
                S/D {{ \Carbon\Carbon::parse($tgl_akhir)->format('d F Y') }}
            </p>
        </div>

        <div class="flex gap-2">
            <button wire:click="downloadExcel" class="px-4 py-2 bg-green-900/30 border border-green-600/50 text-green-400 rounded-lg text-xs font-bold hover:bg-green-600 hover:text-white transition flex items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet"></i> Download Excel
            </button>
            <!-- <button wire:click="downloadPdf" class="px-4 py-2 bg-red-900/30 border border-red-600/50 text-red-400 rounded-lg text-xs font-bold hover:bg-red-600 hover:text-white transition flex items-center gap-2">
                <i class="bi bi-file-earmark-pdf"></i> Download PDF
            </button> -->
        </div>
    </div>

    <div class="max-w-7xl mx-auto mb-6">
        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-5 shadow-xl">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">

                <div>
                    <label class="text-gray-400 text-xs font-bold mb-1 block">Dari Tanggal</label>
                    <input type="date" wire:model="tgl_mulai" class="w-full bg-gray-800 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:border-green-500 focus:outline-none">
                </div>

                <div>
                    <label class="text-gray-400 text-xs font-bold mb-1 block">Sampai Tanggal</label>
                    <input type="date" wire:model="tgl_akhir" class="w-full bg-gray-800 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:border-green-500 focus:outline-none">
                </div>

                @if(Auth::user()->level !== 'Inspektor')
                <div>
                    <label class="text-gray-400 text-xs font-bold mb-1 block">Filter Cabang</label>
                    <select wire:model="filter_cabang" class="w-full bg-gray-800 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:border-green-500 focus:outline-none">
                        <option value="">Semua Cabang</option>
                        @foreach($list_cabang as $cabang)
                        <option value="{{ $cabang->code_cabang }}">{{ $cabang->name_cabang }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="text-gray-400 text-xs font-bold mb-1 block">Pelaksana Pengolahan</label>
                    <select wire:model="filter_tempat" class="w-full bg-gray-800 border border-gray-600 text-white text-sm rounded-lg px-3 py-2 focus:border-emerald-500 focus:outline-none">
                        <option value="">Semua Pelaksana</option>
                        @foreach($list_tempat as $tempat)
                        <option value="{{ $tempat }}">{{ $tempat }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button wire:click="filter" class="w-full py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-500 hover:to-emerald-500 text-white font-bold rounded-lg shadow-lg shadow-emerald-500/30 transition-all transform hover:scale-105 flex items-center justify-center gap-2">
                        <i class="bi bi-search"></i> Terapkan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-lg flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-green-500/10 rounded-full blur-2xl group-hover:bg-green-500/20 transition-all"></div>
            <div>
                <p class="text-gray-400 text-sm font-bold">Total Dokumen</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ number_format($total_record) }} <span class="text-sm text-gray-500 font-normal">Record</span></h3>
            </div>
            <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center text-green-400">
                <i class="bi bi-file-text text-2xl"></i>
            </div>
        </div>

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-lg flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div>
                <p class="text-gray-400 text-sm font-bold">Total Kuantum Beras</p>
                <h3 class="text-3xl font-bold text-white mt-1">{{ number_format($total_penerimaan, 2, ',', '.') }} <span class="text-sm text-gray-500 font-normal">Kg</span></h3>
            </div>
            <div class="w-12 h-12 bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-400">
                <i class="bi bi-box-seam text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto bg-[#1a1d2d] border border-gray-700/50 rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-400 uppercase bg-gray-800/80 border-b border-gray-700">
                    <tr>
                        <th class="px-6 py-4 font-bold border-r border-gray-700/50">Tgl</th>
                        <th class="px-6 py-4 font-bold border-r border-gray-700/50">No HPKK</th>
                        <th class="px-6 py-4 font-bold border-r border-gray-700/50">Pelaksana / MO</th>
                        <th class="px-6 py-4 font-bold text-center border-r border-gray-700/50">Kuantum (Kg)</th>

                        <th class="px-2 py-2 text-center border-r border-gray-700/50 text-gray-500">Sosoh</th>
                        <th class="px-2 py-2 text-center border-r border-gray-700/50 text-gray-500">U1</th>
                        <th class="px-2 py-2 text-center border-r border-gray-700/50 text-gray-500">U2</th>
                        <th class="px-2 py-2 text-center border-r border-gray-700/50 text-gray-500">U3</th>

                        <th class="px-4 py-4 text-center font-bold text-yellow-500 border-r border-gray-700/50">KA (%)</th>
                        <th class="px-4 py-4 text-center border-r border-gray-700/50">Patah</th>
                        <th class="px-4 py-4 text-center border-r border-gray-700/50">Menir</th>
                        <th class="px-6 py-4 font-bold">Cabang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700/50">
                    @forelse($data_laporan as $item)
                    <tr class="hover:bg-gray-800/50 transition-colors text-gray-300">
                        <td class="px-6 py-4 font-mono text-xs border-r border-gray-700/30 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($item->tanggal_pemeriksaan)->format('d-m-Y') }}
                        </td>

                        <td class="px-6 py-4 font-mono text-xs font-bold text-green-400 border-r border-gray-700/30">
                            {{ $item->nomor_hpkk_beras }}
                        </td>

                        <td class="px-6 py-4 border-r border-gray-700/30">
                            <div class="font-bold text-white text-xs">{{ $item->tempat_pemeriksaan }}</div>
                            <div class="text-[10px] text-gray-500 mt-1">MO: {{ $item->id_mo }}</div>
                        </td>

                        <td class="px-6 py-4 text-right font-mono font-bold text-white border-r border-gray-700/30">
                            {{ number_format((float)str_replace(',', '.', $item->kuantum_beras), 0, ',', '.') }}
                        </td>

                        <td class="px-2 py-4 text-center text-gray-400 text-xs border-r border-gray-700/30">{{ $item->derajat_sosoh }}</td>
                        <td class="px-2 py-4 text-center text-gray-500 text-xs border-r border-gray-700/30">{{ $item->ulangan_1 }}</td>
                        <td class="px-2 py-4 text-center text-gray-500 text-xs border-r border-gray-700/30">{{ $item->ulangan_2 }}</td>
                        <td class="px-2 py-4 text-center text-gray-500 text-xs border-r border-gray-700/30">{{ $item->ulangan_3 }}</td>

                        <td class="px-4 py-4 text-center text-yellow-400 font-bold border-r border-gray-700/30">
                            {{ $item->rata_rata }}
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-700/30">
                            {{ $item->butir_patah }}
                        </td>
                        <td class="px-4 py-4 text-center border-r border-gray-700/30">
                            {{ $item->menir }}
                        </td>

                        <td class="px-6 py-4 text-xs text-gray-500 uppercase">
                            {{ $item->name_cabang }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="px-6 py-16 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="bi bi-inbox text-4xl opacity-50"></i>
                                <span class="font-medium">Tidak ada data HGL ditemukan pada periode / filter ini.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-700 bg-[#1a1d2d] flex justify-between items-center text-xs text-gray-500">
            <div>
                Menampilkan data {{ $data_laporan->firstItem() }} - {{ $data_laporan->lastItem() }} dari {{ $total_record }}
            </div>
            <div>
                {{ $data_laporan->links() }}
            </div>
        </div>
    </div>

</div>