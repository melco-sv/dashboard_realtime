<div class="min-h-screen bg-[#0b0c15] p-6 text-white font-['Space_Grotesk']">

    {{-- HEADER --}}
    <div class="max-w-full mx-auto mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-white">List Dokumen Gabah (GKP)</h1>
            <p class="text-gray-400 text-sm">Monitoring Data Hasil Pemeriksaan Kualitas & Kuantitas</p>
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
                placeholder="Cari No HPK / Mitra / Sampel..."
                class="bg-gray-800 border border-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:border-blue-500 w-72">
            <a href="{{ route('input.gabah') }}" wire:navigate
                class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 shadow-lg shadow-blue-500/30 whitespace-nowrap">
                + Input Baru
            </a>
        </div>
    </div>

    {{-- CARD LIST --}}
    <div class="max-w-full mx-auto space-y-3">

        {{-- HEADER ROW --}}
        <div class="hidden lg:grid bg-gray-800 rounded-xl px-4 py-2 text-[10px] uppercase tracking-widest text-gray-400 font-bold"
            style="grid-template-columns: 200px 1fr;">
            <div class="flex items-center">Action</div>
            <div class="grid grid-cols-4 gap-x-4 gap-y-1">
                <span>No HPK / LHPK</span>
                <span>Tanggal</span>
                <span>Lokasi</span>
                <span>Kode Sampel</span>
                <span>Mitra</span>
                <span>Pengirim</span>
                <span>No PO</span>
                <span>Nomor Order</span>
                <span>Jenis Alat Angkut</span>
                <span>Nopol</span>
                <span>Hama / Penyakit</span>
                <span>Jml Timbangan</span>
                <span>KA Rata-rata</span>
                <span>Kadar Hampa</span>
                <span>Butir Hijau</span>
                <span>Petugas</span>
            </div>
        </div>

        @forelse($gabahList as $index => $item)
        <div class="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden hover:border-blue-900/50 transition-colors">
            <div class="flex flex-col lg:flex-row">

                {{-- KOLOM KIRI: NOMOR + TOMBOL --}}
                <div class="flex flex-row lg:flex-col items-start gap-2 p-4 bg-gray-800/30 border-b lg:border-b-0 lg:border-r border-gray-800"
                    style="min-width: 200px; max-width: 200px;">

                    <div class="text-[10px] text-gray-500 font-bold mb-1">
                        #{{ $gabahList->firstItem() + $index }}
                        @if($item->status_data == 'Approve')
                            <span class="ml-1 text-green-400">✓ Approved</span>
                        @endif
                    </div>

                    <div class="flex flex-wrap lg:flex-col gap-1.5 w-full">
                        <a href="{{ route('print.gabah', ['id' => $item->id_po, 'type' => 'hpk']) }}" target="_blank"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Print HPK
                        </a>
                        <a href="{{ route('print.gabah', ['id' => $item->id_po, 'type' => 'lhpk']) }}" target="_blank"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Print LHPK
                        </a>
                        <a href="{{ route('print.gabah', ['id' => $item->id_po, 'type' => 'witnessing']) }}" target="_blank"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Print Witnessing
                        </a>
                        <a href="{{ route('edit.gabah', $item->id_po) }}" wire:navigate
                            class="w-full bg-green-600 hover:bg-green-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Update
                        </a>
                        <a href="{{ route('upload.gabah', $item->id_po) }}" wire:navigate
                            class="w-full bg-pink-600 hover:bg-pink-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Upload Foto
                        </a>
                        @if($item->status_data == 'Approve')
                        <button disabled
                            class="w-full bg-gray-700 text-gray-500 py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase cursor-not-allowed">
                            Approved
                        </button>
                        @else
                        <button wire:click="approve({{ $item->id_po }})" wire:confirm="Approve data ini?"
                            class="w-full bg-yellow-500 hover:bg-yellow-400 text-black py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Approve
                        </button>
                        @endif
                        <button wire:click="delete({{ $item->id_po }})" wire:confirm="Hapus data gabah ini?"
                            class="w-full bg-red-600 hover:bg-red-700 text-white py-1.5 px-2 rounded-lg text-xs font-bold text-center uppercase tracking-wide">
                            Delete
                        </button>
                    </div>
                </div>

                {{-- KOLOM KANAN: SEMUA DATA --}}
                <div class="flex-1 p-4 grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-3">

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">No HPK / LHPK</p>
                        <p class="text-xs font-mono text-gray-200 break-all">{{ $item->nomor_hpkk_gabah ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Tanggal Pelaksanaan</p>
                        <p class="text-xs text-white">{{ $item->tanggal_pelaksanaan ? date('d M Y', strtotime($item->tanggal_pelaksanaan)) : '-' }}</p>
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
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Mitra</p>
                        <p class="text-xs font-bold text-white">{{ $item->mitra ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Pengirim</p>
                        <p class="text-xs text-gray-200">{{ $item->pengirim ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">No PO</p>
                        <p class="text-xs font-mono text-gray-300">{{ $item->no_order_pembelian ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Nomor Order</p>
                        <p class="text-xs font-mono text-gray-300">{{ $item->nomor_order ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Jenis Alat Angkut</p>
                        <p class="text-xs text-gray-200">{{ $item->jenis_alat_angkut ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Nopol</p>
                        <p class="text-xs font-mono text-gray-200">{{ $item->nomor_registrasi_alat_angkut ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Hama / Penyakit</p>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold
                            {{ $item->hama_penyakit == 'Bebas' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $item->hama_penyakit ?? '-' }}
                        </span>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Jml Timbangan</p>
                        <p class="text-sm font-extrabold text-blue-400">
                            {{ $item->jumlah_timbangan ? number_format($item->jumlah_timbangan, 2, ',', '.') . ' Kg' : '-' }}
                        </p>
                    </div>

                    {{-- 3 parameter kualitas: color-coded --}}
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">KA Rata-rata</p>
                        <p class="text-sm font-extrabold {{ ($item->kadar_air_rata_rata <= 38) ? 'text-green-400' : 'text-red-400' }}">
                            {{ $item->kadar_air_rata_rata ? number_format($item->kadar_air_rata_rata, 2, ',', '.') . '%' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Kadar Hampa</p>
                        <p class="text-sm font-extrabold {{ ($item->kadar_hampa <= 40) ? 'text-green-400' : 'text-red-400' }}">
                            {{ $item->kadar_hampa ? number_format($item->kadar_hampa, 2, ',', '.') . '%' : '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-[10px] text-gray-500 uppercase font-bold tracking-wider mb-0.5">Butir Hijau</p>
                        <p class="text-sm font-extrabold {{ ($item->butir_hijau <= 30) ? 'text-green-400' : 'text-red-400' }}">
                            {{ $item->butir_hijau ? number_format($item->butir_hijau, 2, ',', '.') . '%' : '-' }}
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
            Belum ada data gabah.
        </div>
        @endforelse

        {{-- PAGINATION --}}
        <div class="pt-2">
            {{ $gabahList->links() }}
        </div>

    </div>
</div>
