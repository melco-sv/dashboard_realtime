<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">
    
    <div class="max-w-7xl mx-auto mb-8 flex justify-between items-center border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-yellow-400 to-orange-600">
                Edit Data Gabah
            </h1>
            <p class="text-gray-400 text-sm mt-1">Update Existing HPKK Record</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-orange-600/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        <form wire:submit.prevent="update" class="relative z-10">
            
            <div class="mb-8">
                <h3 class="text-blue-400 font-bold uppercase tracking-widest text-xs mb-4 border-l-2 border-blue-500 pl-3">
                    Informasi Dokumen & Pengiriman
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nomor HPK/LHPK</label>
                        <input type="text" wire:model="nomor_hpkk_gabah" readonly 
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-300 rounded-lg px-4 py-3 cursor-not-allowed font-mono">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nomor Purchase Order</label>
                        <input type="text" wire:model="no_order_pembelian" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nomor Order</label>
                        <input type="text" wire:model="nomor_order" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nama Mitra</label>
                        <input type="text" wire:model="mitra" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Pengirim</label>
                        <input type="text" wire:model="pengirim" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Tanggal Pelaksanaan</label>
                        <input type="date" wire:model="tanggal_pelaksanaan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 dark-date-input">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                 <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Jenis Kendaraan</label>
                        <input type="text" wire:model="jenis_alat_angkut" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Plat Kendaraan</label>
                        <input type="text" wire:model="nomor_registrasi_alat_angkut" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none uppercase">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Hama Penyakit</label>
                        <select wire:model="hama_penyakit" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none appearance-none cursor-pointer">
                            <option value="">-- Pilih --</option>
                            <option value="Bebas">Bebas</option>
                            <option value="Tidak Bebas">Tidak Bebas</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Metode Timbang</label>
                        <select wire:model="metode_timbang" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none appearance-none cursor-pointer">
                            <option value="">-- Pilih --</option>
                            <option value="Weightbridge">Weightbridge</option>
                            <option value="Non Weightbridge - Sawah">Non Weightbridge - Sawah</option>
                            <option value="Non Weightbridge - MPP">Non Weightbridge - MPP</option>
                        </select>
                    </div>
                    <div class="md:col-span-2 relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Jumlah Timbangan</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-green-500 font-bold text-sm">KG</span></div>
                            <input type="number" step="0.01" wire:model="jumlah_timbangan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-12 pr-4 py-3 focus:border-green-500 focus:outline-none text-lg font-mono">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Kode Sample</label>
                        <input type="text" wire:model="kode_sample" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-gray-800/30 p-4 rounded-xl border border-gray-700/50">
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Ulangan 1</label><input type="number" step="0.01" wire:model.live="ulangan_1" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Ulangan 2</label><input type="number" step="0.01" wire:model.live="ulangan_2" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Ulangan 3</label><input type="number" step="0.01" wire:model.live="ulangan_3" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="relative"><label class="block text-blue-400 text-xs font-bold mb-2">KA Rata-rata (Auto)</label><input type="text" wire:model="kadar_air_rata_rata" readonly class="w-full bg-blue-900/20 border border-blue-500/50 text-blue-400 font-bold rounded-lg px-4 py-3"></div>
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Kadar Hampa</label><input type="number" step="0.01" wire:model="kadar_hampa" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Butir Hijau</label><input type="number" step="0.01" wire:model="butir_hijau" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                </div>
            </div>

            <div class="mb-8 border-t border-gray-800 pt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Tanggal DOC</label><input type="date" wire:model="tanggal_doc" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 dark-date-input"></div>
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Lokasi</label><input type="text" wire:model="lokasi" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Mengetahui</label><input type="text" wire:model="mengetahui" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Petugas</label><input type="text" wire:model="petugas" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Group</label><input type="text" wire:model="group" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></div>
                    <div><label class="block text-gray-400 text-xs font-bold mb-2">Catatan</label><textarea wire:model="catatan" rows="1" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3"></textarea></div>
                </div>
            </div>


            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-800">
                <button type="button" wire:click="cancel" class="px-6 py-3 rounded-xl bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white font-bold transition-all">
                    Cancel
                </button>
                <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-green-600 to-green-500 hover:from-green-500 hover:to-green-400 text-white font-bold shadow-lg shadow-green-500/30 transition-all transform hover:scale-105">
                    Update Data
                </button>
            </div>

        </form>
    </div>
    <style>.dark-date-input::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }</style>
</div>