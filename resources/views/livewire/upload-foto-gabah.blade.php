<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk'] flex items-center justify-center">

    <div class="w-full max-w-2xl bg-gray-900 border border-gray-800 rounded-2xl p-8 shadow-2xl relative overflow-hidden">

        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        <div class="mb-8 border-b border-gray-800 pb-4">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-cyan-500">
                Upload Foto Gabah
            </h1>
            <p class="text-gray-400 text-sm mt-1">Dokumentasi GKP: <span class="font-mono text-blue-400">{{ $nomor_hpkk }}</span></p>
        </div>

        {{-- INFO CARD RECORD --}}
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 mb-6 grid grid-cols-2 gap-3">
            <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-0.5">Mitra</p>
                <p class="text-sm text-white font-bold">{{ $mitra_nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-0.5">Tanggal Pelaksanaan</p>
                <p class="text-sm text-white">{{ $tanggal ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-0.5">Lokasi</p>
                <p class="text-sm text-white uppercase">{{ $lokasi_record ?? '-' }}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-0.5">No HPK</p>
                <p class="text-xs font-mono text-blue-300 break-all">{{ $nomor_hpkk ?? '-' }}</p>
            </div>
        </div>

        <form wire:submit.prevent="save" class="relative z-10 space-y-6">

            <div>
                <label class="block text-gray-400 text-xs font-bold mb-2 uppercase tracking-wider">Nama Kegiatan</label>
                <input type="text" wire:model="nama" placeholder="Contoh: Pengecekan Karung / Sampling"
                    class="w-full bg-gray-800 border border-gray-700 text-white rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none transition-colors">
                @error('nama') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-400 text-xs font-bold mb-2 uppercase tracking-wider">File Photo (Max 10MB)</label>

                <div class="flex items-center justify-center w-full">
                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-700 border-dashed rounded-xl cursor-pointer bg-gray-800 hover:bg-gray-750 hover:border-blue-500 transition-all relative overflow-hidden group">

                        @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-cover opacity-50 group-hover:opacity-30 transition-opacity">
                        <div class="z-10 flex flex-col items-center justify-center pt-5 pb-6">
                            <p class="text-sm text-blue-400 font-bold">Foto Terpilih!</p>
                            <p class="text-xs text-gray-400 mt-1">Klik untuk ganti</p>
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            <p class="text-sm text-gray-400">Klik untuk upload gambar</p>
                        </div>
                        @endif

                        <input id="dropzone-file" type="file" wire:model="photo" class="hidden" accept="image/*" />
                    </label>
                </div>
                <div wire:loading wire:target="photo" class="text-blue-400 text-xs mt-2 font-bold animate-pulse">Memproses gambar...</div>
                @error('photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex gap-4 pt-4 border-t border-gray-800">
                <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-500 hover:to-cyan-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-blue-500/30 transition-all">
                    Upload Foto
                </button>
                <button type="button" wire:click="cancel" class="px-8 bg-gray-800 text-gray-400 hover:text-white hover:bg-gray-700 font-bold py-3 rounded-xl transition-all">
                    Batal
                </button>
            </div>

        </form>
    </div>
</div>
