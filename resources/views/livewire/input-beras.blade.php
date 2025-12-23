<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">
    
    <div class="max-w-7xl mx-auto mb-8 flex justify-between items-center border-b border-gray-800 pb-4">
        <div>
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-400 to-emerald-600">
                Form Input Beras
            </h1>
            <p class="text-gray-400 text-sm mt-1">Create New HPKK Record (HGL)</p>
        </div>
        
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
    </div>

    <div class="max-w-7xl mx-auto bg-gray-900 border border-gray-800 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-green-600/5 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

        <form wire:submit.prevent="save" class="relative z-10">
            
            <div class="mb-8">
                <h3 class="text-green-400 font-bold uppercase tracking-widest text-xs mb-4 border-l-2 border-green-500 pl-3">
                    Informasi Dokumen
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nomor HPK/LHPK (Auto)</label>
                        <input type="text" wire:model="nomor_hpkk_beras" readonly 
                            class="w-full bg-gray-800/50 border border-gray-700 text-gray-300 rounded-lg px-4 py-3 cursor-not-allowed opacity-75 font-mono">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nomor MO</label>
                        <input type="text" wire:model="id_mo" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nomor Order</label>
                        <input type="text" wire:model="nomor_order" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Tempat Pemeriksaan</label>
                        <input type="text" wire:model="tempat_pemeriksaan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-green-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Tanggal Pemeriksaan</label>
                        <input type="date" wire:model="tanggal_pemeriksaan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 dark-date-input focus:border-green-500 focus:outline-none">
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

            <div class="mb-8">
                <h3 class="text-blue-400 font-bold uppercase tracking-widest text-xs mb-4 border-l-2 border-blue-500 pl-3">
                    Pemeriksaan Organoleptik
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @php 
                        $optsBebas = ['Bebas', 'Tidak Bebas']; 
                    @endphp

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Kondisi Kemasan</label>
                        <select wire:model="kondisi_kemasan" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                            <option value="">-- Pilih --</option>
                            <option value="Baik">Baik</option>
                            <option value="Tidak Baik">Tidak Baik</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Hama Penyakit</label>
                        <select wire:model="hama" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                            <option value="">-- Pilih --</option>
                            @foreach($optsBebas as $o) <option value="{{$o}}">{{$o}}</option> @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Dedak/Katul/Sekam</label>
                        <select wire:model="dedak_katul_sekam" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                            <option value="">-- Pilih --</option>
                            @foreach($optsBebas as $o) <option value="{{$o}}">{{$o}}</option> @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Bau Apek/Busuk</label>
                        <select wire:model="bau" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                            <option value="">-- Pilih --</option>
                            @foreach($optsBebas as $o) <option value="{{$o}}">{{$o}}</option> @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Bahan Kimia</label>
                        <select wire:model="bahan_kimia" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:outline-none">
                            <option value="">-- Pilih --</option>
                            @foreach($optsBebas as $o) <option value="{{$o}}">{{$o}}</option> @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-purple-400 font-bold uppercase tracking-widest text-xs mb-4 border-l-2 border-purple-500 pl-3">
                    Analisa Laboratorium
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-800/30 p-4 rounded-xl border border-gray-700/50 mb-6">
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Ulangan 1</label>
                        <input type="number" step="0.01" wire:model.live="ulangan_1" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Ulangan 2</label>
                        <input type="number" step="0.01" wire:model.live="ulangan_2" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Ulangan 3</label>
                        <input type="number" step="0.01" wire:model.live="ulangan_3" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 focus:border-purple-500">
                    </div>
                    <div class="relative">
                        <label class="block text-blue-400 text-xs font-bold mb-2">KA Rata-rata (Auto)</label>
                        <input type="text" wire:model="rata_rata" readonly class="w-full bg-blue-900/20 border border-blue-500/50 text-blue-400 font-bold rounded-lg px-4 py-3">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Derajat Sosoh</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-green-500 font-bold">%</span>
                            </div>
                            <input type="number" step="0.01" wire:model="derajat_sosoh" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-purple-500">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Butir Patah</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-green-500 font-bold">%</span>
                            </div>
                            <input type="number" step="0.01" wire:model="butir_patah" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-purple-500">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Menir</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-green-500 font-bold">%</span>
                            </div>
                            <input type="number" step="0.01" wire:model="menir" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-purple-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-yellow-400 font-bold uppercase tracking-widest text-xs mb-4 border-l-2 border-yellow-500 pl-3">
                    Kuantum & Rendemen
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Kuantum Gabah (MO)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-yellow-500 font-bold text-xs">KG</span>
                            </div>
                            <input type="number" step="0.01" wire:model.live="kuantum_gabah_sesuai_mo" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-yellow-500">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Kuantum Beras</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-yellow-500 font-bold text-xs">KG</span>
                            </div>
                            <input type="number" step="0.01" wire:model.live="kuantum_beras" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-yellow-500">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-blue-400 text-xs font-bold mb-2">Rendemen Pengolahan (%) (Auto)</label>
                        <input type="text" wire:model="rendemen_pengolahan" readonly class="w-full bg-blue-900/20 border border-blue-500/50 text-blue-400 font-bold rounded-lg px-4 py-3">
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-gray-400 font-bold uppercase tracking-widest text-xs mb-4 border-l-2 border-gray-500 pl-3">
                    Hasil Samping
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Menir</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold text-xs">KG</span>
                            </div>
                            <input type="number" step="0.01" wire:model="hasil_samping_menir" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-gray-500">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Butir Patah</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold text-xs">KG</span>
                            </div>
                            <input type="number" step="0.01" wire:model="hasil_samping_butir_patah" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-gray-500">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Dedak Katul</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold text-xs">KG</span>
                            </div>
                            <input type="number" step="0.01" wire:model="hasil_samping_dedak_katul" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-gray-500">
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-gray-400 text-xs font-bold mb-2">Butir Kuning Rusak</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-bold text-xs">KG</span>
                            </div>
                            <input type="number" step="0.01" wire:model="hasil_samping_butir_kuning_rusak" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg pl-10 pr-4 py-3 focus:border-gray-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-8 border-t border-gray-800 pt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Tanggal DOC</label>
                        <input type="date" wire:model="tanggal_doc" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3 dark-date-input">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Lokasi / Kota</label>
                        <input type="text" wire:model="lokasi" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nama Mengetahui</label>
                        <input type="text" wire:model="mengetahui" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Nama Petugas</label>
                        <input type="text" wire:model="petugas" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Group</label>
                        <input type="text" wire:model="group" class="w-full bg-gray-800 border border-gray-700 text-white rounded-lg px-4 py-3">
                    </div>
                    <div>
                        <label class="block text-gray-400 text-xs font-bold mb-2">Status (Auto)</label>
                        <input type="text" wire:model="status" readonly class="w-full bg-gray-800/50 border border-gray-700 text-gray-500 font-bold rounded-lg px-4 py-3 cursor-not-allowed">
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
                    Create Data Beras
                </button>
            </div>
        </form>
    </div>

    <style>
        .dark-date-input::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
</div>