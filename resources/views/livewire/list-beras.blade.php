<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">

    {{-- HEADER --}}
    <div class="max-w-full mx-auto mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">List Dokumen Beras (HGL)</h1>
            <p class="text-gray-400 text-sm">Monitoring Data Hasil Giling</p>
        </div>

        <div class="flex-1 px-4">
            @if (session()->has('message'))
            <div class="bg-green-500/10 border border-green-500 text-green-400 px-4 py-2 rounded-lg text-sm font-bold text-center">
                {{ session('message') }}
            </div>
            @endif
            @if (session()->has('error'))
            <div class="bg-red-500/10 border border-red-500 text-red-400 px-4 py-2 rounded-lg text-sm font-bold text-center">
                {{ session('error') }}
            </div>
            @endif
        </div>

        <div class="flex gap-3">
            <input type="text" wire:model.live="search"
                placeholder="Cari No HPK / MO / Sampel..."
                class="bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:border-green-500 w-72">
            <a href="{{ route('input.beras') }}" wire:navigate
                class="bg-green-600 hover:bg-green-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 shadow-lg shadow-green-500/30 whitespace-nowrap">
                + Input Baru
            </a>
        </div>
    </div>

    {{-- CARD LIST --}}
    <div class="max-w-full mx-auto space-y-3">

        @forelse($berasList as $index => $item)
        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden hover:border-green-900/50 transition-colors">
            <div class="flex flex-col lg:flex-row">

                {{-- KOLOM KIRI: NOMOR + TOMBOL --}}
                <div class="flex flex-row lg:flex-col items-start gap-2 p-4 bg-gray-800/30 border-b lg:border-b-0 lg:border-r border-gray-800"
                    style="min-width: 200px; max-width: 200px;">

                    <div class="text-[10px] text-gray-500 font-bold mb-1">
                        #{{ $berasList->firstItem() + $index }}
                        @if(isset($item->status) && $item->status == 'Approve')
                            <span class="ml-1 text-green-400">✓ Approved</span>
                        @endif
                    </div>

                    <div class="flex flex-wrap lg:flex-col gap-1.5 w-full">
                        <a href="{{ route('print.beras', ['id' => $item->id_hpkk_beras, 'type' => 'hpk']) }}" target="_blank"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Print HPK
                        </a>
                        <a href="{{ route('print.beras', ['id' => $item->id_hpkk_beras, 'type' => 'lhpk']) }}" target="_blank"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Print LHPK
                        </a>
                        <a href="{{ route('print.beras', ['id' => $item->id_hpkk_beras, 'type' => 'witnessing']) }}" target="_blank"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Print Witnessing
                        </a>
                        <a href="{{ route('edit.beras', $item->id_hpkk_beras) }}" wire:navigate
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Update
                        </a>
                        <a href="{{ route('upload.beras', $item->id_hpkk_beras) }}" wire:navigate
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Upload Foto
                        </a>
                        <button wire:click="delete({{ $item->id_hpkk_beras }})" wire:confirm="Hapus data beras ini?"
                            class="w-full bg-red-600 hover:bg-red-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Delete
                        </button>
                    </div>
                </div>

                {{-- KOLOM KANAN: SEMUA DATA --}}
                <div class="flex-1 p-4 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3">

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">No HPK / LHPK</p>
                        <p class="text-xs font-mono text-gray-200 break-all">{{ $item->nomor_hpkk_beras ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Tanggal Pemeriksaan</p>
                        <p class="text-xs text-white">{{ $item->tanggal_pemeriksaan ? date('d M Y', strtotime($item->tanggal_pemeriksaan)) : '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Lokasi</p>
                        <p class="text-xs text-white uppercase">{{ $item->lokasi ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Kode Sampel</p>
                        <p class="text-xs font-mono text-gray-200">{{ $item->kode_sample ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Tempat Pemeriksaan</p>
                        <p class="text-xs text-white uppercase">{{ $item->tempat_pemeriksaan ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">ID MO</p>
                        <p class="text-xs font-mono text-gray-300">{{ $item->id_mo ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Nomor Order</p>
                        <p class="text-xs font-mono text-gray-300">{{ $item->nomor_order ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Dasar Pemeriksaan</p>
                        <p class="text-xs text-gray-200">{{ $item->dasar_pemeriksaan ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Kondisi Kemasan</p>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold
                            {{ $item->kondisi_kemasan == 'Baik' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $item->kondisi_kemasan ?? '-' }}
                        </span>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Hama</p>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold
                            {{ $item->hama == 'Bebas' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $item->hama ?? '-' }}
                        </span>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Bau</p>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold
                            {{ $item->bau == 'Bebas' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $item->bau ?? '-' }}
                        </span>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Bahan Kimia</p>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold
                            {{ $item->bahan_kimia == 'Bebas' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $item->bahan_kimia ?? '-' }}
                        </span>
                    </div>

                    {{-- Parameter Kualitas --}}
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">KA Rata-rata</p>
                        <p class="text-sm font-extrabold {{ ($item->rata_rata <= 14) ? 'text-green-400' : 'text-red-400' }}">
                            {{ $item->rata_rata ? number_format($item->rata_rata, 2, ',', '.') . '%' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Derajat Sosoh</p>
                        <p class="text-sm font-extrabold {{ ($item->derajat_sosoh >= 95) ? 'text-green-400' : 'text-red-400' }}">
                            {{ $item->derajat_sosoh ? number_format($item->derajat_sosoh, 0, ',', '.') . '%' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Butir Patah</p>
                        <p class="text-sm font-extrabold {{ ($item->butir_patah <= 25) ? 'text-green-400' : 'text-red-400' }}">
                            {{ $item->butir_patah ? number_format($item->butir_patah, 2, ',', '.') . '%' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Menir</p>
                        <p class="text-sm font-extrabold {{ ($item->menir <= 2) ? 'text-green-400' : 'text-red-400' }}">
                            {{ $item->menir ? number_format($item->menir, 2, ',', '.') . '%' : '-' }}
                        </p>
                    </div>

                    {{-- Kuantum & Rendemen --}}
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Kuantum Gabah (MO)</p>
                        <p class="text-sm font-extrabold text-gray-300">
                            {{ $item->kuantum_gabah_sesuai_mo ? number_format($item->kuantum_gabah_sesuai_mo, 2, ',', '.') . ' Kg' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Kuantum Beras</p>
                        <p class="text-sm font-extrabold text-green-400">
                            {{ $item->kuantum_beras ? number_format($item->kuantum_beras, 2, ',', '.') . ' Kg' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Rendemen</p>
                        <p class="text-sm font-extrabold {{ ($item->rendemen_pengolahan >= 50) ? 'text-green-400' : 'text-red-400' }}">
                            {{ $item->rendemen_pengolahan ? number_format($item->rendemen_pengolahan, 2, ',', '.') . '%' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Petugas</p>
                        <p class="text-xs text-gray-200">{{ $item->petugas ?? '-' }}</p>
                    </div>

                </div>
            </div>
        </div>
        @empty
        <div class="bg-gray-900 border border-gray-800 rounded-2xl px-6 py-12 text-center text-gray-500">
            Belum ada data beras.
        </div>
        @endforelse

        {{-- PAGINATION --}}
        <div class="pt-2">
            {{ $berasList->links() }}
        </div>

    </div>
</div>
