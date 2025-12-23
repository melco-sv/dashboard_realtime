<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header-box { border: 1px solid black; padding: 10px; margin-bottom: 5px; }
        .header-table { width: 100%; }
        .logo { width: 80px; }
        .title { font-size: 14px; font-weight: bold; text-align: center; }
        .meta-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .meta-table td { border: 1px solid black; padding: 5px; }
        .content-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .content-table th, .content-table td { border: 1px solid black; padding: 5px; }
        .signature-table { width: 100%; margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <div class="header-box">
        <table class="header-table">
            <tr>
                <td style="width: 80%; text-align: center;">
                    <span class="title">FORMULIR PENGAWASAN PEMERIKSAAN KUALITAS<br>GABAH KERING PANEN (GKP)</span>
                </td>
                <td style="width: 20%; text-align: center; border-left: 1px solid black;">
                    <img src="{{ public_path('assets/logo-sucofindo.png') }}" class="logo" alt="LOGO">
                    <br><b>SUCOFINDO</b>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr><td width="30%">Nama Pengirim</td><td>: {{ $d->pengirim }}</td></tr>
        <tr><td>Nomor Referensi</td><td>: {{ $d->nomor_hpkk_gabah }}</td></tr>
        <tr><td>Estimasi Berat (Kg)</td><td>: {{ number_format($d->jumlah_timbangan, 0, ',', '.') }} Kg</td></tr>
        <tr><td>Tempat Pemeriksaan</td><td>: {{ $d->lokasi }}</td></tr>
        <tr><td>Standar Pemeriksaan</td><td>: PO/KSP-AGRI/26</td></tr>
    </table>

    <div style="text-align: center; font-weight: bold; margin: 15px 0;">PROSES PENGAWASAN PEMERIKSAAN KUALITAS</div>

    <table class="content-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="55%">Uraian Proses</th>
                <th width="10%">Checklist (v/x)</th>
                <th width="30%">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td align="center">1</td>
                <td>Contoh barang dari pengirim merupakan Gabah Kering Panen (GKP)?</td>
                <td align="center">v</td>
                <td></td>
            </tr>
            <tr>
                <td align="center">2</td>
                <td>Terdapat kode pada contoh yang diperiksa? Sebutkan nomor kodenya!</td>
                <td align="center">v</td>
                <td>{{ $d->kode_sample }}</td>
            </tr>
            <tr>
                <td align="center">3</td>
                <td>Pemeriksaan hama (secara visual). Sebutkan hasilnya!</td>
                <td align="center">v</td>
                <td>{{ $d->hama_penyakit }}</td>
            </tr>
            <tr>
                <td align="center">4</td>
                <td>Pemeriksaan GKP untuk parameter Kadar Air</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td>- Pemeriksaan I</td>
                <td align="center">v</td>
                <td>{{ $d->ulangan_1 }}%</td>
            </tr>
            <tr>
                <td></td>
                <td>- Pemeriksaan II</td>
                <td align="center">v</td>
                <td>{{ $d->ulangan_2 }}%</td>
            </tr>
            <tr>
                <td></td>
                <td>- Pemeriksaan III</td>
                <td align="center">v</td>
                <td>{{ $d->ulangan_3 }}%</td>
            </tr>
            <tr>
                <td align="center">5</td>
                <td>Pemeriksaan GKP untuk parameter Butir Hampa</td>
                <td align="center">v</td>
                <td>{{ $d->kadar_hampa }}%</td>
            </tr>
            <tr>
                <td align="center">6</td>
                <td>Pemeriksaan GKP untuk parameter Butir Hijau</td>
                <td align="center">v</td>
                <td>{{ $d->butir_hijau }}%</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 10px;">Kesimpulan : Pelaksanaan pemeriksaan kualitas beras *) sesuai/tidak sesuai dengan standard pemeriksaan.<br>Note : *) coret yang tidak perlu</p>

    <table class="signature-table">
        <tr>
            <td width="50%">
                <br>Petugas Pemeriksa<br><br><br><br><br>
                (.......................................)
            </td>
            <td width="50%">
                {{ strtoupper($d->lokasi) }}, {{ \Carbon\Carbon::parse($d->tanggal_doc)->isoFormat('D MMMM Y') }}<br>
                Pengawas<br><br><br><br><br>
                <b>{{ $d->petugas }}</b>
            </td>
        </tr>
    </table>
</body>
</html>