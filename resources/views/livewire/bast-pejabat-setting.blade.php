<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">
    <div class="max-w-2xl mx-auto">

        <div class="mb-8">
            <h1 class="text-2xl font-bold text-white">Pengaturan Pejabat BAST</h1>
            <p class="text-gray-400 text-sm mt-1">Nama pejabat penanda tangan yang otomatis dipakai saat mencetak BAST GKP &amp; HGL untuk cabang Anda. Cukup isi sekali di sini &mdash; tidak perlu diketik ulang setiap kali cetak.</p>
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
                <p class="text-blue-300 text-xs font-bold uppercase tracking-wider mb-1">Cabang Aktif</p>
                <p class="text-gray-300 text-sm">
                    Pengaturan ini berlaku untuk cabang <strong class="text-white">{{ $namaCabang }}</strong>@if($codeCabang) <span class="text-gray-500">({{ $codeCabang }})</span>@endif.
                    Nama di bawah akan otomatis terisi pada form saat cetak BAST, dan tetap bisa diubah saat itu bila diperlukan.
                </p>
                @if($lastUpdated)
                <p class="text-gray-500 text-xs mt-2">
                    Terakhir diperbarui: {{ \Carbon\Carbon::parse($lastUpdated)->isoFormat('D MMMM Y, HH:mm') }} WIB
                </p>
                @endif
            </div>

            <div class="space-y-6 mb-6">

                {{-- Kepala Unit Pelayanan SUCOFINDO --}}
                <div class="bg-gray-800/50 border border-yellow-900/40 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2.5 h-2.5 bg-yellow-500 rounded-full"></span>
                        <p class="text-yellow-400 text-xs font-bold uppercase tracking-wider">PT SUCOFINDO</p>
                    </div>
                    <label class="block text-gray-300 text-sm font-bold mb-2">Nama Kepala Unit Pelayanan</label>
                    <input type="text" wire:model="nama_kepala_unit" placeholder="Nama lengkap Kepala Unit Pelayanan SUCOFINDO"
                        class="w-full bg-gray-700 border border-gray-600 text-white rounded-xl px-4 py-3 text-sm focus:border-yellow-500 focus:outline-none transition-all">
                    @error('nama_kepala_unit')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pimpinan Cabang Bulog --}}
                <div class="bg-gray-800/50 border border-blue-900/40 rounded-xl p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                        <p class="text-blue-400 text-xs font-bold uppercase tracking-wider">PERUM BULOG</p>
                    </div>
                    <label class="block text-gray-300 text-sm font-bold mb-2">Nama Pimpinan Cabang</label>
                    <input type="text" wire:model="nama_pimpinan_cabang" placeholder="Nama lengkap Pimpinan Cabang Bulog"
                        class="w-full bg-gray-700 border border-gray-600 text-white rounded-xl px-4 py-3 text-sm focus:border-blue-500 focus:outline-none transition-all">
                    @error('nama_pimpinan_cabang')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <button wire:click="save" wire:loading.attr="disabled"
                class="w-full bg-yellow-600 hover:bg-yellow-500 disabled:opacity-60 text-white font-bold py-3 rounded-xl text-sm transition-colors flex items-center justify-center gap-2">
                <span wire:loading.remove wire:target="save">
                    <svg class="w-4 h-4 inline mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Simpan
                </span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>

        </div>
    </div>
</div>
