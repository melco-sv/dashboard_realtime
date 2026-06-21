@php
    $isVerif    = auth()->check() && auth()->user()->isVerification();
    $isInspektor = auth()->check() && auth()->user()->isInspektor();
    $backRoute  = $isVerif ? route('verifikasi.gabah') : route('list.gabah');

    $ka    = (float) ($gabah->kadar_air_rata_rata ?? 0);
    $hampa = (float) ($gabah->kadar_hampa ?? 0);
    $hijau = (float) ($gabah->butir_hijau ?? 0);
    $kg    = (float) str_replace(',', '.', $gabah->jumlah_timbangan ?? 0);
    $status = $gabah->status_data;

    $num = fn ($v, $d = 2) => ($v !== null && $v !== '' && (float) $v != 0) ? number_format((float) $v, $d, ',', '.') : '-';
@endphp

<div class="min-h-screen bg-[#0b0c15] p-4 md:p-6 text-white font-['Space_Grotesk'] {{ $isVerif ? 'pb-28' : '' }}">

    {{-- HEADER --}}
    <div class="mb-5">
        <div class="flex flex-wrap justify-between items-start gap-3">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-2xl font-bold text-white">Detail & Verifikasi GKP</h1>
                    @if($status === 'Approve')
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-green-500/20 text-green-400 border border-green-500/30">✓ Approved</span>
                    @elseif($status === 'Reject')
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-red-500/20 text-red-400 border border-red-500/30">✗ Ditolak</span>
                    @else
                    <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-yellow-500/20 text-yellow-400 border border-yellow-500/30">Pending</span>
                    @endif
                </div>
                <p class="text-gray-400 text-sm mt-1">
                    <span class="font-mono text-yellow-300">{{ $gabah->nomor_hpkk_gabah }}</span>
                    &bull; {{ $gabah->mitra ?? '-' }}
                    &bull; {{ optional($gabah->cabang)->name_cabang ?? $gabah->code_cabang }}
                </p>
            </div>
            <div class="flex gap-2">
                @if($isInspektor)
                <a href="{{ route('upload.gabah', $gabah->id_hpkk_gabah) }}" wire:navigate
                    class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-bold">
                    + Upload Foto
                </a>
                @endif
                <a href="{{ $backRoute }}" wire:navigate
                    class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-bold">
                    ← Kembali
                </a>
            </div>
        </div>

        @if (session()->has('message'))
        <div class="mt-3 bg-green-500/10 border border-green-500 text-green-400 px-4 py-2 rounded-lg text-sm font-bold">
            {{ session('message') }}
        </div>
        @endif
        @if (session()->has('error'))
        <div class="mt-3 bg-red-500/10 border border-red-500 text-red-400 px-4 py-2 rounded-lg text-sm font-bold">
            {{ session('error') }}
        </div>
        @endif

        @if($status === 'Reject' && $gabah->catatan)
        <div class="mt-3 bg-red-500/10 border border-red-500/40 text-red-300 px-4 py-2.5 rounded-lg text-sm">
            <span class="font-bold text-red-400">Catatan penolakan:</span> {{ $gabah->catatan }}
        </div>
        @endif
    </div>

    {{-- SPLIT: FOTO (kiri) + DATA (kanan) — full width --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">

        {{-- KIRI: FOTO BESAR --}}
        <div class="xl:col-span-5">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-bold text-gray-300 uppercase tracking-wider">Foto Dokumentasi</h2>
                <span class="text-xs text-gray-500">{{ $fotos->count() }} foto</span>
            </div>

            @if ($fotos->isEmpty())
            <div class="bg-gray-900 border border-gray-800 rounded-2xl px-6 py-20 text-center">
                <svg class="w-14 h-14 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-500 text-lg font-bold">Belum ada foto</p>
            </div>
            @else
            <div class="space-y-4">
                @foreach ($fotos as $foto)
                <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden">
                    <a href="{{ Storage::url($foto->file) }}" target="_blank" class="block bg-black">
                        <img src="{{ Storage::url($foto->file) }}"
                             alt="{{ $foto->nama }}"
                             class="w-full max-h-[80vh] object-contain mx-auto"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'flex items-center justify-center py-20 text-gray-600 text-sm\'>Foto tidak ditemukan</div>'">
                    </a>
                    <div class="p-3 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Kegiatan</p>
                            <p class="text-sm text-white font-bold truncate">{{ $foto->nama }}</p>
                        </div>
                        @if($isInspektor)
                        <button wire:click="deleteFoto({{ $foto->id_upload }})"
                                wire:confirm="Hapus foto '{{ $foto->nama }}'?"
                                class="flex-shrink-0 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                            Hapus
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- KANAN: DATA INPUT (detail) — cards bersampingan (masonry 2 kolom) --}}
        <div class="xl:col-span-7">
            <div class="columns-1 md:columns-2 gap-4">

                {{-- KADAR AIR — Ulangan 1-3 + Rata-rata --}}
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4 break-inside-avoid">
                    <h2 class="text-sm font-bold text-gray-300 uppercase tracking-wider mb-3">Kadar Air (%)</h2>
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div class="bg-gray-800/60 rounded-lg p-2">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Ulangan 1</p>
                            <p class="text-base font-bold text-gray-100">{{ $num($gabah->ulangan_1) }}</p>
                        </div>
                        <div class="bg-gray-800/60 rounded-lg p-2">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Ulangan 2</p>
                            <p class="text-base font-bold text-gray-100">{{ $num($gabah->ulangan_2) }}</p>
                        </div>
                        <div class="bg-gray-800/60 rounded-lg p-2">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Ulangan 3</p>
                            <p class="text-base font-bold text-gray-100">{{ $num($gabah->ulangan_3) }}</p>
                        </div>
                        <div class="rounded-lg p-2 border {{ $ka > 14 ? 'bg-red-500/10 border-red-500/40' : 'bg-blue-500/10 border-blue-500/30' }}">
                            <p class="text-[10px] text-gray-400 uppercase font-bold">Rata²</p>
                            <p class="text-base font-bold {{ $ka > 14 ? 'text-red-400' : 'text-blue-300' }}">{{ $ka > 0 ? number_format($ka, 2) : '-' }}@if($ka > 14)<span class="text-[9px]"> ⚠</span>@endif</p>
                        </div>
                    </div>
                </div>

                {{-- MUTU LAIN --}}
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4 break-inside-avoid">
                    <h2 class="text-sm font-bold text-gray-300 uppercase tracking-wider mb-3">Mutu Lain</h2>
                    <div class="grid grid-cols-2 gap-2 text-center">
                        <div class="bg-gray-800/60 rounded-lg p-2">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Kadar Hampa (%)</p>
                            <p class="text-base font-bold {{ $hampa > 3 ? 'text-red-400' : 'text-purple-300' }}">{{ $hampa > 0 ? number_format($hampa, 2) : '-' }}@if($hampa > 3) ⚠@endif</p>
                        </div>
                        <div class="bg-gray-800/60 rounded-lg p-2">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Butir Hijau (%)</p>
                            <p class="text-base font-bold {{ $hijau > 5 ? 'text-red-400' : 'text-green-300' }}">{{ $hijau > 0 ? number_format($hijau, 2) : '-' }}@if($hijau > 5) ⚠@endif</p>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-600 mt-2">⚠ KA &gt;14% · Hampa &gt;3% · Hijau &gt;5%</p>
                </div>

                {{-- KUANTUM & TIMBANGAN --}}
                <div class="bg-gray-900 border border-gray-800 rounded-xl p-4 mb-4 break-inside-avoid">
                    <h2 class="text-sm font-bold text-gray-300 uppercase tracking-wider mb-3">Kuantum & Timbangan</h2>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-gray-800/60 rounded-lg p-2">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Timbangan (Kg)</p>
                            <p class="text-sm font-bold text-yellow-300">{{ $kg > 0 ? number_format($kg, 0, ',', '.') : '-' }}</p>
                        </div>
                        <div class="bg-gray-800/60 rounded-lg p-2">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Rencana Kuantum</p>
                            <p class="text-sm font-bold text-gray-100">{{ $num($gabah->rencana_kuantum, 0) }}</p>
                        </div>
                        <div class="bg-gray-800/60 rounded-lg p-2">
                            <p class="text-[10px] text-gray-500 uppercase font-bold">Hasil Kuantum</p>
                            <p class="text-sm font-bold text-gray-100">{{ $num($gabah->hasil_kuantum, 0) }}</p>
                        </div>
                    </div>
                </div>

                {{-- PEMERIKSAAN --}}
                <div class="bg-gray-900 border border-gray-800 rounded-xl divide-y divide-gray-800/60 mb-4 break-inside-avoid">
                    <div class="px-4 py-2.5"><h2 class="text-sm font-bold text-gray-300 uppercase tracking-wider">Pemeriksaan</h2></div>
                    @foreach ([
                        'Kode Sample'        => $gabah->kode_sample,
                        'Jenis Pemeriksaan'  => $gabah->jenis_pemeriksaan,
                        'Dasar Pemeriksaan'  => $gabah->dasar_pemeriksaan,
                        'Hama / Penyakit'    => $gabah->hama_penyakit,
                        'Jenis Alat Angkut'  => $gabah->jenis_alat_angkut,
                        'No. Reg. Alat'      => $gabah->nomor_registrasi_alat_angkut,
                    ] as $label => $value)
                    <div class="px-4 py-2">
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">{{ $label }}</p>
                        <p class="text-sm text-white break-words">{{ $value ?: '-' }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- IDENTITAS DOKUMEN --}}
                <div class="bg-gray-900 border border-gray-800 rounded-xl divide-y divide-gray-800/60 mb-4 break-inside-avoid">
                    <div class="px-4 py-2.5"><h2 class="text-sm font-bold text-gray-300 uppercase tracking-wider">Identitas Dokumen</h2></div>
                    @foreach ([
                        'No. HPK'             => $gabah->nomor_hpkk_gabah,
                        'No. Order'           => $gabah->no_order_pembelian ?: $gabah->nomor_order,
                        'Mitra'               => $gabah->mitra,
                        'Pengirim'            => $gabah->pengirim,
                        'Cabang'              => optional($gabah->cabang)->name_cabang ?? $gabah->code_cabang,
                        'Tempat Pelaksanaan'  => $gabah->tempat_pelaksanaan,
                        'Lokasi'              => $gabah->lokasi,
                        'Tanggal Pelaksanaan' => $gabah->tanggal_pelaksanaan ? $gabah->tanggal_pelaksanaan->format('d M Y') : null,
                        'Tanggal Dokumen'     => $gabah->tanggal_doc ? $gabah->tanggal_doc->format('d M Y') : null,
                        'Petugas'             => $gabah->petugas,
                        'Mengetahui'          => $gabah->mengetahui,
                        'Kesimpulan'          => $gabah->kesimpulan,
                    ] as $label => $value)
                    <div class="px-4 py-2">
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">{{ $label }}</p>
                        <p class="text-sm text-white break-words">{{ $value ?: '-' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ACTION BAR (admin pusat) --}}
    @if($isVerif)
    <div x-data="{ showReject: false, note: @js($gabah->catatan) }">
        <div class="fixed bottom-0 left-0 right-0 z-40 bg-[#11131f] border-t border-gray-800 px-4 py-3 shadow-2xl">
            <div class="flex items-center justify-between gap-3">
                <div class="text-sm min-w-0">
                    <span class="text-gray-500">No. HPK</span>
                    <span class="font-mono text-yellow-300">{{ $gabah->nomor_hpkk_gabah }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="showReject = true"
                        class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors whitespace-nowrap">
                        ✗ Tolak
                    </button>
                    <button wire:click="approve" wire:confirm="Approve No. HPK {{ $gabah->nomor_hpkk_gabah }}?"
                        class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-bold transition-colors whitespace-nowrap">
                        ✓ Approve
                    </button>
                </div>
            </div>
        </div>

        {{-- Modal Tolak --}}
        <div x-show="showReject" x-cloak x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
            <div class="bg-[#11131f] border border-gray-700 rounded-xl p-4 w-full max-w-md" @click.outside="showReject = false">
                <h3 class="text-sm font-bold text-white mb-2">Tolak Dokumen — Catatan Perbaikan</h3>
                <p class="text-xs text-gray-500 mb-2">Jelaskan apa yang harus diperbaiki oleh cabang.</p>
                <textarea x-model="note" rows="4" placeholder="Tulis apa yang harus diperbaiki..."
                    class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-red-500 focus:outline-none resize-none"></textarea>
                <div class="flex items-center justify-end gap-2 mt-3">
                    <button type="button" x-on:click="showReject = false"
                        class="bg-gray-700 hover:bg-gray-600 text-gray-200 px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                        Batal
                    </button>
                    <button type="button"
                        x-on:click="if (!note || !note.trim()) { alert('Catatan penolakan wajib diisi.'); return; } $wire.reject(note);"
                        class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                        Kirim Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
