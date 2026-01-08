<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">
    
    <div class="max-w-7xl mx-auto mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">List Dokumen Gabah</h1>
            <p class="text-gray-400 text-sm">Monitoring Data HPK/LHPK</p>
        </div>
        
        <div class="flex-1 px-4">
            @if (session()->has('message'))
                <div class="bg-green-500/10 border border-green-500 text-green-400 px-4 py-2 rounded-lg text-sm font-bold text-center">
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <div class="flex gap-3">
            <input type="text" wire:model.live="search" placeholder="Cari Nomor HPK / Mitra..." 
                class="bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:border-blue-500">
            <a href="{{ route('input.gabah') }}" wire:navigate class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2">
                + Input Baru
            </a>
        </div>
    </div>

    <div class="max-w-7xl mx-auto bg-gray-900 border border-gray-800 rounded-2xl p-1 overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-400 uppercase bg-gray-800 border-b border-gray-700">
                    <tr>
                        <th class="px-6 py-4">No</th>
                        <th class="px-6 py-4 text-center">Action</th>
                        <th class="px-6 py-4">No HPK LHPK</th>
                        <!-- <th class="px-6 py-4">Status</th> -->
                        <th class="px-6 py-4">Bln Sesuai PO</th>
                        <th class="px-6 py-4">Tgl Pelaksanaan</th>
                        <th class="px-6 py-4">Total Penerimaan</th>
                        <th class="px-6 py-4">No PO</th>
                        <th class="px-6 py-4">Kode Sampel</th>
                        <th class="px-6 py-4">Kendaraan</th>
                        <th class="px-6 py-4">Mitra</th>
                    </tr>
                </thead>
                
                <tbody class="divide-y divide-gray-800">
                    @forelse($gabahList as $index => $item)
                    <tr class="hover:bg-gray-800/50 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $gabahList->firstItem() + $index }}</td>
                        
                        <td class="px-4 py-4">
                            <div class="flex flex-col gap-1.5 items-center">
                                <a href="{{ route('print.gabah', ['id' => $item->id_hpkk_gabah, 'type' => 'hpk']) }}" target="_blank" class="w-32 bg-blue-600 hover:bg-blue-700 text-white py-1 px-2 rounded text-[10px] font-bold text-center uppercase tracking-wide">Print HPK</a>
                                <a href="{{ route('print.gabah', ['id' => $item->id_hpkk_gabah, 'type' => 'lhpk']) }}" target="_blank" class="w-32 bg-blue-500 hover:bg-blue-600 text-white py-1 px-2 rounded text-[10px] font-bold text-center uppercase tracking-wide">Print LHPK</a>
                                <a href="{{ route('print.gabah', ['id' => $item->id_hpkk_gabah, 'type' => 'witnessing']) }}" target="_blank" class="w-32 bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-2 rounded text-[10px] font-bold text-center uppercase tracking-wide">Print Witnessing</a>
                                
                                @if($item->status_data == 'Approve')
                                    <button disabled class="w-32 bg-gray-500 text-white py-1 px-2 rounded text-[10px] font-bold text-center uppercase tracking-wide cursor-not-allowed">Approved</button>
                                @else
                                    <button wire:click="approve({{ $item->id_hpkk_gabah }})" wire:confirm="Approve data ini?" class="w-32 bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-2 rounded text-[10px] font-bold text-center uppercase tracking-wide">Approve</button>
                                @endif

                                <a href="{{ route('edit.gabah', $item->id_hpkk_gabah) }}" wire:navigate class="w-32 bg-green-600 hover:bg-green-700 text-white py-1 px-2 rounded text-[10px] font-bold text-center uppercase tracking-wide">Update</a>
                                
                                <a href="{{ route('upload.gabah', $item->id_hpkk_gabah) }}" wire:navigate class="w-32 bg-pink-600 hover:bg-pink-700 text-white py-1 px-2 rounded text-[10px] font-bold text-center uppercase tracking-wide flex justify-center items-center">
                                    Upload Foto
                                </a>
                                
                                <button wire:click="delete({{ $item->id_hpkk_gabah }})" wire:confirm="Yakin ingin menghapus data ini?" class="w-32 bg-red-500 hover:bg-red-600 text-white py-1 px-2 rounded text-[10px] font-bold text-center uppercase tracking-wide">Delete</button>
                            </div>
                        </td>

                        <td class="px-6 py-4 font-mono text-gray-300">{{ $item->nomor_hpkk_gabah }}</td>
                        
                        <!-- <td class="px-6 py-4 font-bold {{ $item->status_data == 'Approve' ? 'text-green-400' : 'text-yellow-400' }}">
                            {{ $item->status_data ?? '-' }}
                        </td> -->

                        <td class="px-6 py-4 text-center">{{ date('m', strtotime($item->tanggal_pelaksanaan)) }}</td>
                        <td class="px-6 py-4">{{ $item->tanggal_pelaksanaan }}</td>
                        <td class="px-6 py-4 font-bold text-green-400">{{ number_format($item->jumlah_timbangan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">{{ $item->no_order_pembelian }}</td>
                        <td class="px-6 py-4 font-mono text-xs">{{ $item->kode_sample }}</td>
                        <td class="px-6 py-4">{{ $item->nomor_registrasi_alat_angkut }}</td>
                        <td class="px-6 py-4 font-bold">{{ $item->mitra }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-8 text-center text-gray-500">Belum ada data gabah.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-gray-800">
            {{ $gabahList->links() }}
        </div>
    </div>
</div>