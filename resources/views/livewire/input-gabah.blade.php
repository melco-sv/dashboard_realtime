<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">

    <div class="max-w-7xl mx-auto mb-8 flex justify-between items-center border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-600">
                Form Input Gabah
            </h1>
            <p class="text-gray-400 text-sm mt-1">Create New HPKK Record</p>
        </div>

        <a href="{{ route('list.gabah') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-gray-800 border border-gray-700 text-white hover:bg-gray-700 hover:border-gray-600 transition-all flex items-center gap-2 font-bold shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            List Data
        </a>
    </div>

    <div class="max-w-7xl mx-auto mb-6">
        @if (session()->has('message'))
        <div class="px-4 py-3 bg-green-500/10 border border-green-500/50 text-green-400 rounded-xl text-sm font-bold flex items-center gap-3 animate-pulse">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('message') }}</span>
        </div>
        @endif

        @if (session()->has('error'))
        <div class="px-4 py-3 bg-red-500/10 border border-red-500/50 text-red-400 rounded-xl text-sm font-bold flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif
    </div>

    <form wire:submit.prevent="store" class="max-w-7xl mx-auto">

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl mb-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-all"></div>

            <h2 class="text-lg font-bold text-blue-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-8 bg-blue-500 rounded-full"></span>
                Identitas Dokumen
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">No. HPKK Gabah (Auto)</label>
                    <input type="text" wire:model="nomor_hpkk_gabah" readonly class="w-full bg-gray-800/50 border border-gray-700 text-gray-500 font-bold rounded-lg px-4 py-3 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">No. Purchase Order</label>
                    <input type="text" wire:model="no_order_pembelian" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Nomor Order</label>
                    <input type="text" wire:model="nomor_order" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Mitra <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="mitra" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                    @error('mitra') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Pengirim</label>
                    <input type="text" wire:model="pengirim" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Tanggal Pelaksanaan</label>
                    <input type="date" wire:model="tanggal_pelaksanaan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl mb-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-all"></div>

            <h2 class="text-lg font-bold text-purple-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-8 bg-purple-500 rounded-full"></span>
                Data Transport & Muatan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Jenis Alat Angkut</label>
                    <input type="text" wire:model="jenis_alat_angkut" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">No. Plat / Registrasi</label>
                    <input type="text" wire:model="nomor_registrasi_alat_angkut" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Hama Penyakit</label>
                    <select wire:model="hama_penyakit" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500 focus:outline-none">
                        <option value="">Pilih...</option>
                        <option value="Ada">Bebas</option>
                        <option value="Tidak Ada">Tidak Bebas</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Metode Timbang <span class="text-red-500">*</span></label>
                    <select wire:model="metode_timbang" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500 focus:outline-none">
                        <option value="">Pilih...</option>
                        <option value="Weightbridge">Weightbridge</option>
                        <option value="Non Weightbridge - Sawah">Non Weightbridge - Sawah</option>
                        <option value="Non Weightbridge - MPP">Non Weightbridge - MPP</option>
                    </select>
                    @error('metode_timbang') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Jumlah Timbangan (Kg) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" wire:model.blur="jumlah_timbangan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500 focus:outline-none">
                    @error('jumlah_timbangan') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Kode Sample</label>
                    <input type="text" wire:model="kode_sample" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl mb-6 relative overflow-hidden group">
            <h2 class="text-lg font-bold text-green-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-8 bg-green-500 rounded-full"></span>
                Hasil Analisa Laboratorium
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-[#11131f] p-4 rounded-xl border border-gray-800">
                    <p class="text-green-500 font-bold mb-3 border-b border-gray-700 pb-2">1. Kadar Air (%)</p>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs text-gray-400">Ulangan 1</label>
                            <input type="number" step="0.01" wire:model.blur="ulangan_1" class="w-24 bg-gray-800 border border-gray-700 text-white text-sm rounded px-2 py-1 focus:border-green-500 focus:outline-none">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="text-xs text-gray-400">Ulangan 2</label>
                            <input type="number" step="0.01" wire:model.blur="ulangan_2" class="w-24 bg-gray-800 border border-gray-700 text-white text-sm rounded px-2 py-1 focus:border-green-500 focus:outline-none">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="text-xs text-gray-400">Ulangan 3</label>
                            <input type="number" step="0.01" wire:model.blur="ulangan_3" class="w-24 bg-gray-800 border border-gray-700 text-white text-sm rounded px-2 py-1 focus:border-green-500 focus:outline-none">
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-700">
                            <label class="text-xs text-white font-bold">Rata-rata</label>
                            <input type="text" wire:model="kadar_air_rata_rata" readonly class="w-24 bg-gray-900 border border-gray-700 text-green-400 font-bold text-sm rounded px-2 py-1 text-right">
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">2. Kadar Hampa / Kotoran (%)</label>
                        <input type="number" step="0.01" wire:model.blur="kadar_hampa" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">3. Butir Hijau / Mengapur (%)</label>
                        <input type="number" step="0.01" wire:model.blur="butir_hijau" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl mb-6 relative overflow-hidden group">
            <h2 class="text-lg font-bold text-gray-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-8 bg-gray-500 rounded-full"></span>
                Penandatangan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Tanggal DOC</label>
                    <input type="date" wire:model="tanggal_doc" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Lokasi</label>
                    <input type="text" wire:model="lokasi" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Mengetahui</label>
                    <input type="text" wire:model="mengetahui" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Petugas</label>
                    <input type="text" wire:model="petugas" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Kode Cabang (Auto)</label>
                    <div class="flex gap-2">
                        <input type="text" wire:model="code_cabang" readonly
                            class="w-full bg-[#11131f] border border-gray-700 text-gray-500 font-bold rounded-lg px-4 py-3 cursor-not-allowed focus:outline-none">

                        <div class="bg-[#11131f] border border-gray-700 rounded-lg px-4 py-3 text-gray-400 font-bold flex items-center justify-center min-w-[120px]">
                            {{ optional(Auth::user()->cabang)->name_cabang ?? '-' }}
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Catatan (Opsional)</label>
                    <textarea wire:model="catatan" rows="1" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-gray-500 focus:outline-none"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-800">
            <button type="button" wire:click="cancel" class="px-6 py-3 rounded-xl bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white font-bold transition-all">
                Cancel
            </button>
            <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-400 text-white font-bold shadow-lg shadow-blue-500/30 transition-all transform hover:scale-105">
                Create Data
            </button>
        </div>
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('livewire:init', () => {

            // Listener untuk Error/Peringatan
            Livewire.on('swal:error', (data) => {
                Swal.fire({
                    icon: 'error',
                    title: data[0].title,
                    text: data[0].text,
                    background: '#1a1d2d', // Warna Card sesuai tema Anda
                    color: '#e5e7eb', // Warna Text Putih/Abu
                    confirmButtonColor: '#d33', // Merah untuk error
                    confirmButtonText: 'OK, Perbaiki',
                    customClass: {
                        popup: 'border border-gray-700 shadow-2xl rounded-2xl'
                    }
                });
            });

            // Listener untuk Sukses
            Livewire.on('swal:success', (data) => {
                Swal.fire({
                    icon: 'success',
                    title: data[0].title,
                    text: data[0].text,
                    background: '#1a1d2d',
                    color: '#e5e7eb',
                    confirmButtonColor: '#3b82f6', // Biru (sesuai tema Gabah)
                    confirmButtonText: 'Sip, Lanjut!',
                    customClass: {
                        popup: 'border border-gray-700 shadow-2xl rounded-2xl'
                    }
                });
            });

        });
    </script>

</div>