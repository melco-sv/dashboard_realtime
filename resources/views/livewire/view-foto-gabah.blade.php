<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">

    {{-- HEADER --}}
    <div class="max-w-5xl mx-auto mb-6">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-white">Galeri Foto Gabah</h1>
                <p class="text-gray-400 text-sm mt-1">
                    <span class="font-mono text-blue-400">{{ $gabah->nomor_hpkk_gabah }}</span>
                    &bull; {{ $gabah->mitra ?? '-' }}
                    &bull; {{ $gabah->tanggal_pelaksanaan ? $gabah->tanggal_pelaksanaan->format('d M Y') : '-' }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('upload.gabah', $gabah->id_po) }}" wire:navigate
                    class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-bold">
                    + Upload Foto Baru
                </a>
                <a href="{{ route('list.gabah') }}" wire:navigate
                    class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-bold">
                    Kembali ke List
                </a>
            </div>
        </div>

        {{-- Info Record --}}
        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 bg-gray-900 border border-gray-800 rounded-xl p-4">
            <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-0.5">Lokasi</p>
                <p class="text-sm text-white uppercase">{{ $gabah->lokasi ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-0.5">Pengirim</p>
                <p class="text-sm text-white">{{ $gabah->pengirim ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-0.5">No PO</p>
                <p class="text-sm font-mono text-gray-300">{{ $gabah->no_order_pembelian ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-0.5">Jumlah Foto</p>
                <p class="text-sm font-bold text-blue-400">{{ $fotos->count() }} foto</p>
            </div>
        </div>

        @if (session()->has('message'))
        <div class="mt-3 bg-green-500/10 border border-green-500 text-green-400 px-4 py-2 rounded-lg text-sm font-bold">
            {{ session('message') }}
        </div>
        @endif
    </div>

    {{-- GALLERY GRID --}}
    <div class="max-w-5xl mx-auto">
        @if ($fotos->isEmpty())
        <div class="bg-gray-900 border border-gray-800 rounded-2xl px-6 py-16 text-center">
            <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500 text-lg font-bold">Belum ada foto</p>
            <p class="text-gray-600 text-sm mt-1">Klik "Upload Foto Baru" untuk menambahkan dokumentasi.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($fotos as $foto)
            <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden hover:border-blue-800/50 transition-colors">
                <div class="relative aspect-video bg-gray-800">
                    <img src="{{ Storage::url($foto->file) }}"
                         alt="{{ $foto->nama }}"
                         class="w-full h-full object-cover"
                         onerror="this.src=''; this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-gray-600 text-xs\'>Foto tidak ditemukan</div>'">
                </div>
                <div class="p-3 flex items-center justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">Kegiatan</p>
                        <p class="text-sm text-white font-bold truncate">{{ $foto->nama }}</p>
                    </div>
                    <button wire:click="deleteFoto({{ $foto->id_upload }})"
                            wire:confirm="Hapus foto '{{ $foto->nama }}'?"
                            class="flex-shrink-0 bg-red-600/20 hover:bg-red-600 text-red-400 hover:text-white px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">
                        Hapus
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
