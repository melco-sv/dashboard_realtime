<div class="min-h-screen bg-[#0b0c15] p-4 text-white font-['Space_Grotesk']">

    {{-- HEADER --}}
    <div class="mb-4">
        <h1 class="text-xl font-bold text-white">Verifikasi Gabah GKP</h1>
        <p class="text-gray-400 text-xs mt-0.5">Data seluruh cabang — nilai merah ⚠ melebihi ambang batas</p>
    </div>

    @if (session()->has('message'))
    <div class="mb-3 bg-green-500/10 border border-green-500 text-green-400 px-3 py-2 rounded-lg text-sm font-bold">
        {{ session('message') }}
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-3 bg-red-500/10 border border-red-500 text-red-400 px-3 py-2 rounded-lg text-sm font-bold">
        {{ session('error') }}
    </div>
    @endif

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-4 gap-3 mb-4">
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Total Kuantum (Kg)</p>
            <p class="text-lg font-bold text-yellow-400">{{ number_format($totalKg, 0, ',', '.') }}</p>
            <p class="text-[10px] text-gray-600">{{ number_format($totalKg / 1000, 2, ',', '.') }} Ton</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Sudah Approved</p>
            <p class="text-lg font-bold text-green-400">{{ number_format($totalApproved) }}</p>
            <p class="text-[10px] text-gray-600">dokumen</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Belum Approved</p>
            <p class="text-lg font-bold text-yellow-400">{{ number_format($totalPending) }}</p>
            <p class="text-[10px] text-gray-600">dokumen</p>
        </div>
        <div class="bg-gray-900 border border-gray-800 rounded-xl p-3">
            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-1">Ditolak</p>
            <p class="text-lg font-bold text-red-400">{{ number_format($totalRejected) }}</p>
            <p class="text-[10px] text-gray-600">dokumen</p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            {{-- Tanggal --}}
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

            {{-- Cabang --}}
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Cabang</label>
                <select wire:model.live="cabang_filter"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                    <option value="">— Semua —</option>
                    @foreach($cabangs as $c)
                    <option value="{{ $c->code_cabang }}">{{ $c->name_cabang }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Status</label>
                <select wire:model.live="status_filter"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
                    <option value="">Semua</option>
                    <option value="pending">Belum Approved</option>
                    <option value="Approve">Sudah Approved</option>
                    <option value="Reject">Ditolak</option>
                </select>
            </div>

            {{-- Search --}}
            <div class="flex-1 min-w-[140px]">
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Cari</label>
                <input type="text" wire:model.live="search" placeholder="No. HPK / Mitra / Cabang..."
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-blue-500 focus:outline-none">
            </div>

            <span class="text-xs text-gray-500 self-end pb-1.5">{{ $dataList->total() }} record</span>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-800/70 text-[10px] text-gray-400 uppercase tracking-wider">
                    <th class="px-2 py-2.5 text-left font-bold w-6">#</th>
                    <th class="px-2 py-2.5 text-left font-bold">Cabang</th>
                    <th class="px-2 py-2.5 text-left font-bold">No. HPK</th>
                    <th class="px-2 py-2.5 text-left font-bold">Mitra / Pengirim</th>
                    <th class="px-2 py-2.5 text-left font-bold">Tanggal</th>
                    <th class="px-2 py-2.5 text-right font-bold">Kuantum (Kg)</th>
                    <th class="px-2 py-2.5 text-center font-bold text-blue-400 border-l border-gray-700">KA (%)</th>
                    <th class="px-2 py-2.5 text-center font-bold text-purple-400">Hampa (%)</th>
                    <th class="px-2 py-2.5 text-center font-bold text-green-400 border-r border-gray-700">Hijau (%)</th>
                    <th class="px-2 py-2.5 text-center font-bold">Status</th>
                    <th class="px-2 py-2.5 text-center font-bold">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/60">
                @forelse ($dataList as $i => $row)
                @php
                    $kg    = (float) str_replace(',', '.', $row->jumlah_timbangan ?? 0);
                    $ka    = (float) ($row->kadar_air_rata_rata ?? 0);
                    $hampa = (float) ($row->kadar_hampa ?? 0);
                    $hijau = (float) ($row->butir_hijau ?? 0);
                @endphp
                <tr class="hover:bg-gray-800/40 transition-colors">
                    <td class="px-2 py-2 text-gray-500">{{ $dataList->firstItem() + $loop->index }}</td>
                    <td class="px-2 py-2 font-semibold text-white">{{ $row->name_cabang ?? '-' }}</td>
                    <td class="px-2 py-2 font-mono text-yellow-300">{{ $row->nomor_hpkk_gabah ?? '-' }}</td>
                    <td class="px-2 py-2">
                        <div class="font-semibold text-white">{{ $row->mitra ?? '-' }}</div>
                        @if($row->pengirim && $row->pengirim !== $row->mitra)
                        <div class="text-gray-500 text-[10px]">{{ $row->pengirim }}</div>
                        @endif
                    </td>
                    <td class="px-2 py-2 text-gray-300 whitespace-nowrap">
                        {{ $row->tanggal_pelaksanaan ? \Carbon\Carbon::parse($row->tanggal_pelaksanaan)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-2 py-2 text-right font-bold text-yellow-400">
                        {{ $kg > 0 ? number_format($kg, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-2 py-2 text-center font-bold border-l border-gray-700 {{ $ka > 14 ? 'text-red-400' : 'text-blue-300' }}">
                        {{ $ka > 0 ? number_format($ka, 2) : '-' }}@if($ka > 14)<span class="text-[9px]">⚠</span>@endif
                    </td>
                    <td class="px-2 py-2 text-center font-bold {{ $hampa > 3 ? 'text-red-400' : 'text-purple-300' }}">
                        {{ $hampa > 0 ? number_format($hampa, 2) : '-' }}@if($hampa > 3)<span class="text-[9px]">⚠</span>@endif
                    </td>
                    <td class="px-2 py-2 text-center font-bold border-r border-gray-700 {{ $hijau > 5 ? 'text-red-400' : 'text-green-300' }}">
                        {{ $hijau > 0 ? number_format($hijau, 2) : '-' }}@if($hijau > 5)<span class="text-[9px]">⚠</span>@endif
                    </td>
                    <td class="px-2 py-2 text-center">
                        @if($row->status_data === 'Approve')
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-green-500/20 text-green-400 border border-green-500/30 whitespace-nowrap">✓ Approved</span>
                        @elseif($row->status_data === 'Reject')
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-500/20 text-red-400 border border-red-500/30 whitespace-nowrap" title="{{ $row->catatan }}">✗ Ditolak</span>
                        @if($row->catatan)
                        <div class="text-[10px] text-red-300/80 mt-1 max-w-[160px] mx-auto leading-snug" title="{{ $row->catatan }}">{{ \Illuminate\Support\Str::limit($row->catatan, 60) }}</div>
                        @endif
                        @else
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</span>
                        @endif
                    </td>
                    <td class="px-2 py-2 text-center">
                        <a href="{{ route('view.foto.gabah', $row->id_hpkk_gabah) }}" wire:navigate
                            class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-500 text-white px-3 py-1 rounded font-bold transition-colors whitespace-nowrap">
                            <i class="fa-solid fa-eye"></i> Detail
                            <span class="bg-blue-900/50 px-1.5 rounded text-[10px]">📷 {{ $row->fotos_count }}</span>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-4 py-10 text-center text-gray-500">Tidak ada data ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-2 border-t border-gray-800 flex items-center justify-between flex-wrap gap-2">
            <p class="text-[10px] text-gray-600">⚠ KA &gt;14% &nbsp;|&nbsp; Hampa &gt;3% &nbsp;|&nbsp; Hijau &gt;5%</p>
            <div>{{ $dataList->links() }}</div>
        </div>
    </div>
</div>
