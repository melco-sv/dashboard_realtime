<div class="min-h-screen bg-[#0b0c15] p-4 text-white font-['Space_Grotesk']">

    <div class="mb-4">
        <h1 class="text-xl font-bold text-white">Status Pembayaran BAST</h1>
        <p class="text-gray-400 text-xs mt-0.5">Tandai dokumen BAST sebagai Dibayar atau Belum Dibayar</p>
    </div>

    @if (session()->has('message'))
    <div class="mb-4 bg-green-500/10 border border-green-500 text-green-400 px-4 py-2 rounded-lg text-sm font-bold">
        {{ session('message') }}
    </div>
    @endif

    {{-- FILTER --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Cabang</label>
                <select wire:model.live="filter_cabang"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:outline-none min-w-[160px]">
                    <option value="">— Semua Cabang —</option>
                    @foreach ($cabangs as $c)
                    <option value="{{ $c->code_cabang }}">{{ $c->name_cabang }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Jenis</label>
                <select wire:model.live="filter_jenis"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                    <option value="">— Semua —</option>
                    <option value="HGL">HGL (Beras)</option>
                    <option value="GKP">GKP (Gabah)</option>
                </select>
            </div>
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Status</label>
                <select wire:model.live="filter_status"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                    <option value="">— Semua —</option>
                    <option value="BELUM DIBAYAR">Belum Dibayar</option>
                    <option value="DIBAYAR">Dibayar</option>
                </select>
            </div>
            <span class="text-xs text-gray-500 self-end pb-1.5">{{ $data->total() }} record</span>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-800/70 text-[10px] text-gray-400 uppercase tracking-wider">
                    <th class="px-3 py-2.5 text-left font-bold">No</th>
                    <th class="px-3 py-2.5 text-left font-bold">Cabang</th>
                    <th class="px-3 py-2.5 text-left font-bold">Jenis</th>
                    <th class="px-3 py-2.5 text-left font-bold">Nomor Surat</th>
                    <th class="px-3 py-2.5 text-left font-bold">Periode</th>
                    <th class="px-3 py-2.5 text-left font-bold">Status</th>
                    <th class="px-3 py-2.5 text-center font-bold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/60">
                @forelse ($data as $row)
                <tr class="hover:bg-gray-800/30 transition-colors">
                    <td class="px-3 py-2 text-gray-500">{{ $data->firstItem() + $loop->index }}</td>
                    <td class="px-3 py-2 font-semibold text-white">
                        {{ $row->cabang->name_cabang ?? $row->code_cabang }}
                    </td>
                    <td class="px-3 py-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border
                            {{ $row->jenis === 'HGL' ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30' }}">
                            {{ $row->jenis }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-gray-300 font-mono text-[10px]">
                        {{ $row->nomor_surat ?? '—' }}
                    </td>
                    <td class="px-3 py-2 text-gray-300 font-mono text-[10px]">
                        {{ \Carbon\Carbon::parse($row->tgl_mulai)->format('d/m/Y') }}
                        <span class="text-gray-600">s.d.</span>
                        {{ \Carbon\Carbon::parse($row->tgl_akhir)->format('d/m/Y') }}
                    </td>
                    <td class="px-3 py-2">
                        @if ($row->status === 'DIBAYAR')
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border bg-green-500/20 text-green-400 border-green-500/30">
                            <i class="fa-solid fa-circle-check mr-1"></i>DIBAYAR
                        </span>
                        @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border bg-red-500/20 text-red-400 border-red-500/30">
                            <i class="fa-solid fa-clock mr-1"></i>BELUM DIBAYAR
                        </span>
                        @endif
                    </td>
                    <td class="px-3 py-2 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button wire:click="toggleStatus({{ $row->id }})"
                                wire:loading.attr="disabled"
                                class="{{ $row->status === 'DIBAYAR' ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500' }} text-white px-2.5 py-1 rounded text-[10px] font-bold transition-all">
                                {{ $row->status === 'DIBAYAR' ? 'Batalkan' : 'Tandai Dibayar' }}
                            </button>
                            <button wire:click="hapus({{ $row->id }})"
                                wire:confirm="Hapus record ini?"
                                class="bg-gray-700 hover:bg-gray-600 text-gray-300 px-2 py-1 rounded text-[10px] transition-all">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                        Belum ada data. Data muncul otomatis saat BAST dicetak.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-2 border-t border-gray-800">
            {{ $data->links() }}
        </div>
    </div>

</div>
