<div wire:poll.30s>
    @auth
        @if(Auth::user()->isVerification() && $count > 0)
            <div class="relative" x-data="{ open: false }"
                @mouseenter="open = true" @mouseleave="open = false">

                {{-- Lonceng + jumlah pending --}}
                <button type="button" @click="open = !open"
                    title="{{ $count }} dokumen menunggu approval"
                    class="relative inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-300 hover:text-white hover:bg-gray-800 transition-colors focus:outline-none">
                    <i class="fa-solid fa-bell text-lg"></i>
                    <span class="absolute -top-0.5 -right-0.5 min-w-[16px] h-4 px-1 flex items-center justify-center bg-red-500 text-white text-[10px] font-bold rounded-full leading-none shadow">
                        {{ $count > 99 ? '99+' : $count }}
                    </span>
                </button>

                {{-- Dropdown: 5 perintah terakhir --}}
                <div x-show="open" x-cloak x-transition.opacity.duration.150ms
                    class="absolute right-0 mt-2 w-72 bg-[#11131f] border border-gray-700 rounded-xl shadow-2xl z-50 overflow-hidden">

                    <div class="px-4 py-2.5 border-b border-gray-800 flex items-center justify-between">
                        <span class="text-xs font-bold text-white">Perintah Terakhir</span>
                        <a href="{{ route('verifikasi.gabah') }}" wire:navigate
                            class="text-[10px] font-bold text-orange-500 hover:text-orange-400 whitespace-nowrap">
                            {{ $count }} menunggu →
                        </a>
                    </div>

                    <div class="max-h-72 overflow-y-auto divide-y divide-gray-800/60">
                        @forelse ($recent as $item)
                        <a href="{{ $item['url'] }}" wire:navigate @click="open = false"
                            class="block px-4 py-2.5 flex items-start gap-2.5 hover:bg-gray-800/40 transition-colors">
                            <i class="fa-solid {{ $item['icon'] }} {{ $item['color'] }} mt-0.5 text-sm"></i>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold text-gray-200 truncate">{{ $item['label'] }}</p>
                                <p class="text-[11px] font-mono text-gray-400 truncate">{{ $item['code'] }}</p>
                                <p class="text-[10px] text-gray-600 truncate">
                                    @if(!empty($item['cabang'])){{ $item['cabang'] }}@endif
                                    @if(!empty($item['actor'])) &bull; {{ $item['actor'] }}@endif
                                    @if(!empty($item['time'])) &bull; {{ \Carbon\Carbon::parse($item['time'])->diffForHumans() }}@endif
                                </p>
                            </div>
                            <i class="fa-solid fa-chevron-right text-gray-700 text-[10px] mt-1"></i>
                        </a>
                        @empty
                        <div class="px-4 py-6 text-center text-gray-600 text-xs">Belum ada aktivitas.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    @endauth
</div>
