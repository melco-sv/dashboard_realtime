<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk'] flex items-center justify-center">

    <div class="w-full max-w-4xl bg-gray-900 border border-gray-800 rounded-lg p-8 shadow-2xl relative">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">
                Upload Foto Kegiatan
            </h1>
        </div>

        <form wire:submit.prevent="save" class="space-y-6">

            <div>
                <label class="block text-gray-400 text-sm mb-2">Id Hpkk Gabah</label>
                <input type="text" wire:model="id_hpkk_gabah" readonly
                    class="w-full bg-gray-200 border border-gray-300 text-gray-700 rounded px-4 py-2 cursor-not-allowed font-mono text-sm focus:outline-none">
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">Nama</label>
                <input type="text" wire:model="nama" placeholder="Nama Kegiatan"
                    class="w-full bg-white border border-gray-300 text-gray-900 rounded px-4 py-2 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none text-sm transition-colors">
                @error('nama') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">File Photo ( Max 2MB )</label>

                <div class="flex items-center justify-center w-full">
                    <label for="dropzone-file" class="flex flex-col items-center justify-center w-full h-48 border-2 border-gray-600 border-dashed rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-all relative overflow-hidden group">

                        @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" class="absolute inset-0 w-full h-full object-contain p-2 z-10 bg-white">
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity z-20">
                            <span class="text-white text-sm font-bold">Klik untuk ganti foto</span>
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-xs text-gray-500">Klik untuk upload gambar</p>
                        </div>
                        @endif

                        <input id="dropzone-file" type="file" wire:model="photo" class="hidden" accept="image/*" />
                    </label>
                </div>

                <div wire:loading wire:target="photo" class="text-blue-400 text-xs mt-2 font-bold animate-pulse">
                    Sedang memproses gambar...
                </div>
                @error('photo') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-gray-400 text-sm mb-2">Group</label>
                <input type="text" wire:model="group" readonly
                    class="w-full bg-gray-200 border border-gray-300 text-gray-700 rounded px-4 py-2 cursor-not-allowed text-sm focus:outline-none">
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition-colors text-sm">
                    Create
                </button>
                <button type="button" wire:click="cancel" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded shadow transition-colors text-sm">
                    Cancel
                </button>
            </div>

        </form>
    </div>
</div>