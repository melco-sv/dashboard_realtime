<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">
    <div class="max-w-2xl mx-auto">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Pengaturan Tarif BAST</h1>
            <p class="text-gray-400 text-sm mt-1">Tarif pemeriksaan yang digunakan untuk perhitungan biaya pada dokumen BAST. Tarif Gabah (GKP) dan Beras (HGL) dapat diatur secara terpisah.</p>
        </div>

        @if (session()->has('message'))
        <div class="mb-6 bg-green-500/10 border border-green-500 text-green-400 px-4 py-3 rounded-lg text-sm font-bold flex items-center gap-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('message') }}
        </div>
        @endif

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-xl">

            <div class="bg-blue-500/10 border border-blue-500/30 rounded-xl px-4 py-3 mb-6">
                <p class="text-blue-300 text-xs font-bold uppercase tracking-wider mb-1">Info</p>
                <p class="text-gray-300 text-sm">
                    Tarif ini berlaku untuk <strong class="text-white">semua cabang</strong> dan otomatis digunakan saat Inspektor mencetak BAST.
                    Inspektor <strong class="text-white">tidak dapat mengubah</strong> tarif ini.
                </p>
                @if($lastUpdated)
                <p class="text-gray-500 text-xs mt-2">
                    Terakhir diperbarui: {{ \Carbon\Carbon::parse($lastUpdated)->isoFormat('D MMMM Y, HH:mm') }} WIB
                </p>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- Tarif Gabah --}}
                <div class="bg-gray-800/50 border border-blue-900/40 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                        <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">Gabah (GKP)</p>
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">
                            Tarif Pemeriksaan <span class="text-gray-500 font-normal">(Rp per Kg)</span>
                        </label>
                        <div class="flex items-center gap-2 overflow-hidden">
                            <span class="text-gray-400 font-bold text-sm flex-shrink-0">Rp</span>
                            <input type="number" step="0.01" min="0" wire:model.live="tarif_bast_gabah"
                                class="min-w-0 flex-1 bg-gray-700 border border-gray-600 text-white rounded-xl px-4 py-3 text-xl font-bold font-mono focus:border-blue-500 focus:outline-none transition-all">
                            <span class="text-gray-400 text-sm flex-shrink-0">/Kg</span>
                        </div>
                        @error('tarif_bast_gabah')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Preview Gabah --}}
                    <div class="mt-4 bg-gray-900/50 rounded-lg p-3">
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-2">Preview</p>
                        @php $tg = (float) str_replace(',', '.', $tarif_bast_gabah ?: 0); @endphp
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <p class="text-[10px] text-gray-500 mb-1">1.000 Kg</p>
                                <p class="font-bold text-white text-xs">Rp {{ number_format($tg * 1000, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 mb-1">10.000 Kg</p>
                                <p class="font-bold text-white text-xs">Rp {{ number_format($tg * 10000, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 mb-1">100.000 Kg</p>
                                <p class="font-bold text-white text-xs">Rp {{ number_format($tg * 100000, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tarif Beras --}}
                <div class="bg-gray-800/50 border border-green-900/40 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span>
                        <p class="text-green-400 text-xs font-bold uppercase tracking-wider">Beras (HGL)</p>
                    </div>
                    <div>
                        <label class="block text-gray-300 text-sm font-bold mb-2">
                            Tarif Pemeriksaan <span class="text-gray-500 font-normal">(Rp per Kg)</span>
                        </label>
                        <div class="flex items-center gap-2 overflow-hidden">
                            <span class="text-gray-400 font-bold text-sm flex-shrink-0">Rp</span>
                            <input type="number" step="0.01" min="0" wire:model.live="tarif_bast_beras"
                                class="min-w-0 flex-1 bg-gray-700 border border-gray-600 text-white rounded-xl px-4 py-3 text-xl font-bold font-mono focus:border-green-500 focus:outline-none transition-all">
                            <span class="text-gray-400 text-sm flex-shrink-0">/Kg</span>
                        </div>
                        @error('tarif_bast_beras')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Preview Beras --}}
                    <div class="mt-4 bg-gray-900/50 rounded-lg p-3">
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold mb-2">Preview</p>
                        @php $tb = (float) str_replace(',', '.', $tarif_bast_beras ?: 0); @endphp
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <p class="text-[10px] text-gray-500 mb-1">1.000 Kg</p>
                                <p class="font-bold text-white text-xs">Rp {{ number_format($tb * 1000, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 mb-1">10.000 Kg</p>
                                <p class="font-bold text-white text-xs">Rp {{ number_format($tb * 10000, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 mb-1">100.000 Kg</p>
                                <p class="font-bold text-white text-xs">Rp {{ number_format($tb * 100000, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <button wire:click="save" wire:loading.attr="disabled"
                class="w-full bg-red-600 hover:bg-red-500 disabled:opacity-60 text-white font-bold py-3 rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="save">
                    <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan Tarif
                </span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>

        </div>
    </div>
</div>
