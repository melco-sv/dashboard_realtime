<div class="min-h-screen bg-[#0b0c15] p-4 text-white font-['Space_Grotesk']">

    <div class="mb-4">
        <h1 class="text-xl font-bold text-white">Activity Log</h1>
        <p class="text-gray-400 text-xs mt-0.5">Riwayat seluruh aktivitas user — hanya Super Admin yang dapat melihat ini</p>
    </div>

    {{-- FILTER --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl p-3 mb-4">
        <div class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Dari Tanggal</label>
                <input type="date" wire:model.live="tgl_mulai"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-red-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Sampai</label>
                <input type="date" wire:model.live="tgl_akhir"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-red-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Jenis Aksi</label>
                <select wire:model.live="event_filter"
                    class="bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-red-500 focus:outline-none">
                    <option value="">— Semua —</option>
                    @foreach($events as $event)
                    <option value="{{ $event }}">{{ $event }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[140px]">
                <label class="block text-gray-400 text-[10px] font-bold mb-1 uppercase tracking-wider">Cari User / Detail</label>
                <input type="text" wire:model.live="search" placeholder="Nama user, nomor dokumen..."
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-2 py-1.5 text-xs focus:border-red-500 focus:outline-none">
            </div>
            <span class="text-xs text-gray-500 self-end pb-1.5">{{ $logs->total() }} record</span>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-gray-800/70 text-[10px] text-gray-400 uppercase tracking-wider">
                    <th class="px-3 py-2.5 text-left font-bold">Waktu</th>
                    <th class="px-3 py-2.5 text-left font-bold">User</th>
                    <th class="px-3 py-2.5 text-left font-bold">Cabang</th>
                    <th class="px-3 py-2.5 text-left font-bold">Aksi</th>
                    <th class="px-3 py-2.5 text-left font-bold">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800/60">
                @forelse ($logs as $log)
                @php
                    $props = json_decode($log->properties ?? '{}', true);
                    $badgeColor = match(true) {
                        str_contains($log->description, 'Approve') => 'bg-green-500/20 text-green-400 border-green-500/30',
                        str_contains($log->description, 'Delete')  => 'bg-red-500/20 text-red-400 border-red-500/30',
                        str_contains($log->description, 'Input')   => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
                        str_contains($log->description, 'Tarif')   => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
                        str_contains($log->description, 'User')    => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
                        default                                     => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
                    };
                @endphp
                <tr class="hover:bg-gray-800/30 transition-colors">
                    <td class="px-3 py-2 text-gray-400 whitespace-nowrap">
                        {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}
                    </td>
                    <td class="px-3 py-2">
                        <div class="font-semibold text-white">{{ $log->causer_nama ?? 'System' }}</div>
                        <div class="text-[10px] text-gray-500">{{ $log->causer_level ?? '' }}</div>
                    </td>
                    <td class="px-3 py-2 text-gray-300 text-[10px]">
                        {{ $props['cabang'] ?? ($log->causer_group ?? '-') }}
                    </td>
                    <td class="px-3 py-2">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $badgeColor }}">
                            {{ $log->description }}
                        </span>
                    </td>
                    <td class="px-3 py-2 text-gray-300">
                        @if(!empty($props))
                            @if(isset($props['perubahan']) && !empty($props['perubahan']))
                                {{-- Tampilkan perubahan field-by-field --}}
                                <div class="space-y-0.5">
                                @foreach($props['perubahan'] as $field => $change)
                                <div class="text-[10px]">
                                    <span class="text-gray-500 font-mono">{{ $field }}:</span>
                                    <span class="text-red-400 line-through mx-1">{{ $change['dari'] ?? '—' }}</span>
                                    <span class="text-gray-400">→</span>
                                    <span class="text-green-400 font-semibold ml-1">{{ $change['menjadi'] ?? '—' }}</span>
                                </div>
                                @endforeach
                                </div>
                                {{-- Info tambahan di luar perubahan --}}
                                @foreach($props as $key => $val)
                                    @if($key !== 'perubahan' && $key !== 'cabang')
                                    <span class="text-[10px] text-gray-600">{{ $key }}:</span>
                                    <span class="text-[10px] font-mono text-gray-400">{{ $val }}</span>
                                    &nbsp;
                                    @endif
                                @endforeach
                            @else
                                {{-- Aksi non-update: tampilkan properties biasa --}}
                                @foreach($props as $key => $val)
                                    @if($key !== 'cabang' && !is_array($val))
                                    <span class="text-gray-500 text-[10px]">{{ $key }}:</span>
                                    <span class="font-mono text-[10px]">{{ $val }}</span>
                                    &nbsp;
                                    @endif
                                @endforeach
                            @endif
                        @else
                        <span class="text-gray-600">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada aktivitas tercatat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-2 border-t border-gray-800">
            {{ $logs->links() }}
        </div>
    </div>
</div>
