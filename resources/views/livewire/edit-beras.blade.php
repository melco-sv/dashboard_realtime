<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">
    
    <div class="max-w-7xl mx-auto mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">Edit Data HPKK Beras</h1>
            <p class="text-gray-400 text-sm">Formulir perubahan data hasil pemeriksaan kualitas</p>
        </div>
        <a href="{{ route('list.beras') }}" wire:navigate class="text-gray-400 hover:text-white transition">
            &larr; Kembali
        </a>
    </div>

    @if (session()->has('error'))
        <div class="max-w-7xl mx-auto mb-4 bg-red-500/10 border border-red-500 text-red-400 px-4 py-2 rounded-lg text-sm font-bold">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit="update" class="max-w-7xl mx-auto">
        
        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl mb-6">
            <h3 class="text-green-500 font-bold text-lg mb-4 border-b border-gray-800 pb-2">A. Identitas Dokumen</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="text-gray-400 text-sm">Nomor HPKK</label>
                    <input type="text" wire:model="nomor_hpkk_beras" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-green-500 outline-none">
                    @error('nomor_hpkk_beras') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-gray-400 text-sm">ID MO</label>
                    <input type="text" wire:model="id_mo" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-green-500 outline-none">
                </div>

                <div>
                    <label class="text-gray-400 text-sm">Nomor Order</label>
                    <input type="text" wire:model="nomor_order" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-green-500 outline-none">
                </div>
                <div>
                    <label class="text-gray-400 text-sm">Kode Sample</label>
                    <input type="text" wire:model="kode_sample" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-green-500 outline-none">
                </div>
                <div>
                    <label class="text-gray-400 text-sm">Tanggal Pemeriksaan</label>
                    <input type="date" wire:model="tanggal_pemeriksaan" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-green-500 outline-none">
                </div>

                <div>
                    <label class="text-gray-400 text-sm">Tempat Pemeriksaan</label>
                    <input type="text" wire:model="tempat_pemeriksaan" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-green-500 outline-none">
                </div>
                <div>
                    <label class="text-gray-400 text-sm">Lokasi (Cabang)</label>
                    <input type="text" wire:model="lokasi" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-green-500 outline-none">
                </div>
                 <div>
                    <label class="text-gray-400 text-sm">Periode</label>
                    <input type="text" wire:model="periode" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-green-500 outline-none">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl">
                <h3 class="text-yellow-500 font-bold text-lg mb-4 border-b border-gray-800 pb-2">B. Pemeriksaan Fisik</h3>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="text-gray-400 text-sm">Bau</label>
                        <select wire:model="bau" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-yellow-500 outline-none">
                            <option value="">Pilih...</option>
                            <option value="Bebas">Bebas</option>
                            <option value="Apek">Apek</option>
                            <option value="Asam">Asam</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Hama</label>
                        <select wire:model="hama" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-yellow-500 outline-none">
                            <option value="">Pilih...</option>
                            <option value="Bebas">Bebas</option>
                            <option value="Ada">Ada</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Kondisi Kemasan</label>
                        <input type="text" wire:model="kondisi_kemasan" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-yellow-500 outline-none">
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Dedak/Katul/Sekam (Visual)</label>
                        <input type="text" wire:model="dedak_katul_sekam" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-yellow-500 outline-none">
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Dasar Pemeriksaan</label>
                        <input type="text" wire:model="dasar_pemeriksaan" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-yellow-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl">
                <h3 class="text-blue-500 font-bold text-lg mb-4 border-b border-gray-800 pb-2">C. Hasil Analisa Lab</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 grid grid-cols-4 gap-2 bg-gray-800/50 p-2 rounded-lg">
                        <div class="col-span-4 text-xs text-blue-300 mb-1">Kadar Air (%)</div>
                        <input type="number" step="0.01" wire:model="ulangan_1" placeholder="U1" class="bg-gray-800 border border-gray-600 rounded px-2 py-1 text-sm focus:border-blue-500">
                        <input type="number" step="0.01" wire:model="ulangan_2" placeholder="U2" class="bg-gray-800 border border-gray-600 rounded px-2 py-1 text-sm focus:border-blue-500">
                        <input type="number" step="0.01" wire:model="ulangan_3" placeholder="U3" class="bg-gray-800 border border-gray-600 rounded px-2 py-1 text-sm focus:border-blue-500">
                        <input type="number" step="0.01" wire:model="rata_rata" placeholder="Rata" class="bg-gray-900 border border-blue-500/50 rounded px-2 py-1 text-sm font-bold text-blue-400">
                    </div>

                    <div>
                        <label class="text-gray-400 text-sm">Derajat Sosoh (%)</label>
                        <input type="number" step="0.01" wire:model="derajat_sosoh" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Butir Patah (%)</label>
                        <input type="number" step="0.01" wire:model="butir_patah" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="text-gray-400 text-sm">Menir (%)</label>
                        <input type="number" step="0.01" wire:model="menir" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-blue-500 outline-none">
                    </div>
                     <div>
                        <label class="text-gray-400 text-sm">Bahan Kimia</label>
                        <input type="text" wire:model="bahan_kimia" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-blue-500 outline-none">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl mb-6">
            <h3 class="text-purple-500 font-bold text-lg mb-4 border-b border-gray-800 pb-2">D. Kuantum & Hasil Samping</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="text-gray-400 text-sm">Kuantum Gabah (MO)</label>
                    <input type="number" step="0.01" wire:model="kuantum_gabah_sesuai_mo" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-purple-500 outline-none">
                </div>
                <div>
                    <label class="text-gray-400 text-sm">Kuantum Beras</label>
                    <input type="number" step="0.01" wire:model="kuantum_beras" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-purple-500 outline-none">
                </div>
                <div>
                    <label class="text-gray-400 text-sm">Rendemen (%)</label>
                    <input type="number" step="0.01" wire:model="rendemen_pengolahan" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-purple-500 outline-none">
                </div>
                 <div>
                    <label class="text-gray-400 text-sm">Hasil S. Menir</label>
                    <input type="number" step="0.01" wire:model="hasil_samping_menir" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-purple-500 outline-none">
                </div>
                
                 <div>
                    <label class="text-gray-400 text-sm">Hasil S. Patah</label>
                    <input type="number" step="0.01" wire:model="hasil_samping_butir_patah" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-purple-500 outline-none">
                </div>
                 <div>
                    <label class="text-gray-400 text-sm">Hasil S. Dedak</label>
                    <input type="number" step="0.01" wire:model="hasil_samping_dedak_katul" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-purple-500 outline-none">
                </div>
                 <div>
                    <label class="text-gray-400 text-sm">Hasil S. Kuning/Rusak</label>
                    <input type="number" step="0.01" wire:model="hasil_samping_butir_kuning_rusak" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-purple-500 outline-none">
                </div>
            </div>
        </div>

        <div class="bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl mb-6">
            <h3 class="text-pink-500 font-bold text-lg mb-4 border-b border-gray-800 pb-2">E. Catatan & Petugas</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="text-gray-400 text-sm">Catatan</label>
                    <textarea wire:model="catatan" rows="2" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-pink-500 outline-none"></textarea>
                </div>
                <div>
                    <label class="text-gray-400 text-sm">Petugas Pemeriksa</label>
                    <input type="text" wire:model="petugas" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-pink-500 outline-none">
                </div>
                <div>
                    <label class="text-gray-400 text-sm">Mengetahui (Atasan)</label>
                    <input type="text" wire:model="mengetahui" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-pink-500 outline-none">
                </div>
                 <div>
                    <label class="text-gray-400 text-sm">Group</label>
                    <input type="text" wire:model="group" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-pink-500 outline-none">
                </div>
                <div>
                    <label class="text-gray-400 text-sm">Status Dokumen</label>
                    <select wire:model="status" class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 mt-1 focus:border-pink-500 outline-none">
                        <option value="Draft">Draft</option>
                        <option value="Approve">Approve</option>
                        <option value="Reject">Reject</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pb-10">
            <button type="submit" 
                class="bg-green-600 hover:bg-green-500 text-white px-8 py-3 rounded-lg font-bold shadow-lg shadow-green-500/20 transition-all transform hover:scale-105">
                Simpan Perubahan
            </button>
            
            <a href="{{ route('list.beras') }}" wire:navigate 
                class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-8 py-3 rounded-lg font-bold transition-all border border-gray-700">
                Batal
            </a>

            <div wire:loading wire:target="update" class="text-green-500 text-sm animate-pulse ml-4">
                Processing update...
            </div>
        </div>

    </form>
</div>