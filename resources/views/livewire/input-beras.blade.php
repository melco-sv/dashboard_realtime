<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">
    <div class="max-w-7xl mx-auto mb-8 flex justify-between items-center border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-emerald-600">
                Form Input Beras
            </h1>
            <p class="text-gray-400 text-sm mt-1">Create New HPKK Record (HGL)</p>
        </div>
        
        <div class="flex gap-4 items-center">
            @if (session()->has('message'))
                <div class="px-4 py-2 bg-green-500/10 border border-green-500/50 text-green-400 rounded-lg text-sm font-bold">
                    {{ session('message') }}
                </div>
            @endif
            @if (session()->has('error'))
                <div class="px-4 py-2 bg-red-500/10 border border-red-500/50 text-red-400 rounded-lg text-sm font-bold">
                    {{ session('error') }}
                </div>
            @endif

            <a href="{{ route('list.beras') }}" wire:navigate class="px-5 py-2.5 rounded-xl bg-gray-800 border border-gray-700 text-white hover:bg-gray-700 hover:border-gray-600 transition-all flex items-center gap-2 font-bold shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                List Data
            </a>
        </div>
    </div>

    <form wire:submit.prevent="store" class="max-w-7xl mx-auto">
        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl mb-6 relative overflow-hidden group">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-green-500/10 rounded-full blur-2xl group-hover:bg-green-500/20 transition-all"></div>
            <h2 class="text-lg font-bold text-green-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-8 bg-green-500 rounded-full"></span>
                Identitas Dokumen & Sample
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">No. HPKK Beras (Auto)</label>
                    <input type="text" wire:model="nomor_hpkk_beras" readonly class="w-full bg-gray-800/50 border border-gray-700 text-gray-500 font-bold rounded-lg px-4 py-3 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">ID MO / Nomor MO</label>
                    <input type="text" wire:model="id_mo" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Nomor Order</label>
                    <input type="text" wire:model="nomor_order" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                </div>
            </div>
             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-4 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Tempat Pemeriksaan</label>
                    <input type="text" wire:model="tempat_pemeriksaan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                </div>
                 <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Tanggal Pemeriksaan</label>
                    <input type="date" wire:model="tanggal_pemeriksaan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Kode Sample</label>
                    <input type="text" wire:model="kode_sample" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Dasar Pemeriksaan</label>
                    <input type="text" wire:model="dasar_pemeriksaan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                </div>
            </div>
        </div>

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl mb-6 relative overflow-hidden group">
            <h2 class="text-lg font-bold text-emerald-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-8 bg-emerald-500 rounded-full"></span>
                Kualitas Fisik (Visual)
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 relative z-10">
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Kondisi Kemasan</label>
                    <select wire:model="kondisi_kemasan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-emerald-500">
                        <option value="">Pilih...</option>
                        <option value="Baik">Baik</option>
                        <option value="Rusak">Rusak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Hama/Penyakit</label>
                    <select wire:model="hama" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-emerald-500">
                        <option value="">Pilih...</option>
                        <option value="Bebas">Bebas</option>
                        <option value="Ada">Ada</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Dedak/Katul/Sekam</label>
                    <select wire:model="dedak_katul_sekam" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-emerald-500">
                        <option value="">Pilih...</option>
                        <option value="Bebas">Bebas</option>
                        <option value="Ada">Ada</option>
                    </select>
                </div>
                 <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Bau Apek/Asam</label>
                    <select wire:model="bau" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-emerald-500">
                        <option value="">Pilih...</option>
                        <option value="Bebas">Bebas</option>
                        <option value="Ada">Ada</option>
                    </select>
                </div>
                 <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Bahan Kimia</label>
                    <select wire:model="bahan_kimia" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-3 py-2 text-sm focus:border-emerald-500">
                        <option value="">Pilih...</option>
                        <option value="Bebas">Bebas</option>
                        <option value="Ada">Ada</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <h2 class="text-lg font-bold text-cyan-400 mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-cyan-500 rounded-full"></span>
                    Kadar Air (%)
                </h2>
                <div class="bg-[#11131f] p-4 rounded-xl border border-gray-800 space-y-3">
                     <div class="flex items-center justify-between">
                        <label class="text-xs text-gray-400">Ulangan 1</label>
                        <input type="number" step="0.01" wire:model.live="ulangan_1" class="w-24 bg-gray-800 border border-gray-700 text-white text-sm rounded px-2 py-1">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="text-xs text-gray-400">Ulangan 2</label>
                        <input type="number" step="0.01" wire:model.live="ulangan_2" class="w-24 bg-gray-800 border border-gray-700 text-white text-sm rounded px-2 py-1">
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="text-xs text-gray-400">Ulangan 3</label>
                        <input type="number" step="0.01" wire:model.live="ulangan_3" class="w-24 bg-gray-800 border border-gray-700 text-white text-sm rounded px-2 py-1">
                    </div>
                     <div class="flex items-center justify-between pt-2 border-t border-gray-700">
                        <label class="text-xs text-white font-bold">Rata-rata</label>
                        <input type="text" wire:model="rata_rata" readonly class="w-24 bg-gray-900 border border-gray-700 text-cyan-400 font-bold text-sm rounded px-2 py-1 text-right">
                    </div>
                </div>
            </div>

            <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl relative overflow-hidden">
                <h2 class="text-lg font-bold text-cyan-400 mb-4 flex items-center gap-2">
                    <span class="w-2 h-8 bg-cyan-500 rounded-full"></span>
                    Fisik Beras (%)
                </h2>
                <div class="space-y-4">
                     <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Derajat Sosoh</label>
                        <input type="number" step="0.01" wire:model="derajat_sosoh" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:border-cyan-500">
                    </div>
                     <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Butir Patah</label>
                        <input type="number" step="0.01" wire:model="butir_patah" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:border-cyan-500">
                    </div>
                     <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Menir</label>
                        <input type="number" step="0.01" wire:model="menir" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-2 focus:border-cyan-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl mb-6">
            <h2 class="text-lg font-bold text-orange-400 mb-4 flex items-center gap-2">
                <span class="w-2 h-8 bg-orange-500 rounded-full"></span>
                Kuantum & Hasil Samping
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                 <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Kuantum Gabah Sesuai MO (Kg)</label>
                    <input type="number" step="0.01" wire:model="kuantum_gabah_sesuai_mo" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-orange-500">
                </div>
                 <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Kuantum Beras (Kg)</label>
                    <input type="number" step="0.01" wire:model="kuantum_beras" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-orange-500">
                </div>
            </div>

            <div class="bg-[#11131f] p-4 rounded-xl border border-gray-800 grid grid-cols-1 md:grid-cols-4 gap-4">
                 <div>
                    <label class="block text-gray-500 text-[10px] font-bold mb-1">Menir (Kg)</label>
                    <input type="number" step="0.01" wire:model="hasil_samping_menir" class="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold mb-1">Butir Patah (Kg)</label>
                    <input type="number" step="0.01" wire:model="hasil_samping_butir_patah" class="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold mb-1">Dedak/Katul (Kg)</label>
                    <input type="number" step="0.01" wire:model="hasil_samping_dedak_katul" class="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-gray-500 text-[10px] font-bold mb-1">Butir Kuning/Rusak (Kg)</label>
                    <input type="number" step="0.01" wire:model="hasil_samping_butir_kuning_rusak" class="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded px-3 py-2">
                </div>
            </div>
        </div>

        <div class="bg-[#1a1d2d] border border-gray-700/50 rounded-2xl p-6 shadow-xl mb-6">
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
             <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4 relative z-10">
                
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Group / Cabang (Auto)</label>
                     <div class="flex gap-2">
                        <input type="text" wire:model="group" readonly class="w-full bg-[#11131f] border border-gray-700 text-gray-500 font-bold rounded-lg px-4 py-3 cursor-not-allowed">
                        
                        <div class="bg-[#11131f] border border-gray-700 rounded-lg px-4 py-3 text-gray-400 font-bold flex items-center justify-center min-w-[120px]">
                            {{ optional(Auth::user()->cabang)->name_cabang ?? '-' }}
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Status (Auto)</label>
                    <input type="text" wire:model="status" readonly class="w-full bg-[#11131f] border border-gray-700 text-gray-500 font-bold rounded-lg px-4 py-3 cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs font-bold mb-2">Catatan</label>
                    <textarea wire:model="catatan" rows="1" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-gray-500"></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-800">
            <button type="button" wire:click="cancel" class="px-6 py-3 rounded-xl bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white font-bold transition-all">
                Cancel
            </button>
            <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-green-600 to-emerald-500 hover:from-green-500 hover:to-emerald-400 text-white font-bold shadow-lg shadow-green-500/30 transition-all transform hover:scale-105">
                Create Data
            </button>
        </div>
    </form>
</div>