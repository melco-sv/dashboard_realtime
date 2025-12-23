<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { border: 1px solid black; padding: 10px; }
        .logo { width: 100px; }
        .title { font-size: 16px; font-weight: bold; text-align: center; }
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 3px; vertical-align: top; }
        .content-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .content-table th, .content-table td { border: 1px solid black; padding: 5px; }
        .signature-table { width: 100%; margin-top: 50px; text-align: center; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 80%; text-align: center;">
                <span class="title">LAPORAN HASIL PEMERIKSAAN KUALITAS (LHPK) GABAH</span>
            </td>
            <td style="width: 20%; text-align: center;">
                <img src="{{ public_path('assets/logo-sucofindo.png') }}" class="logo" alt="LOGO">
                <br><b>SUCOFINDO</b>
            </td>
        </tr>
    </table>

    <div style="text-align: center; font-weight: bold; margin-bottom: 20px;">
        No. LHPK {{ $d->nomor_hpkk_gabah }}
    </div>

    <table class="info-table">
        <tr><td width="200">Pengirim</td><td>: {{ $d->pengirim }}</td></tr>
        <tr><td>Tempat Pelaksanaan</td><td>: {{ $d->lokasi }}</td></tr>
        <tr><td>Tanggal Pelaksanaan</td><td>: {{ \Carbon\Carbon::parse($d->tanggal_pelaksanaan)->isoFormat('D MMMM Y') }}</td></tr>
        <tr><td>Jenis Alat Angkut</td><td>: {{ $d->jenis_alat_angkut }}</td></tr>
        <tr><td>Nomor Registrasi Alat Angkut</td><td>: {{ $d->nomor_registrasi_alat_angkut }}</td></tr>
        <tr><td>Kode Sampel</td><td>: {{ $d->kode_sample }}</td></tr>
        <tr><td>Nomor Purchase Order</td><td>: {{ $d->no_order_pembelian }}</td></tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th width="50%">PEMERIKSAAN</th>
                <th width="50%">HASIL PEMERIKSAAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><b>1. Kualitatif Gabah</b><br>&nbsp;&nbsp;&nbsp;A. Hama / Penyakit</td>
                <td>{{ $d->hama_penyakit }}</td>
            </tr>
            <tr>
                <td style="vertical-align: top; height: 150px;">
                    <b>2. Kuantitatif Gabah</b><br>
                    &nbsp;&nbsp;&nbsp;A. Metode Penimbangan<br><br>
                    &nbsp;&nbsp;&nbsp;B. Kadar Air<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. Ulangan 1<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. Ulangan 2<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. Ulangan 3<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rata - rata<br>
                    &nbsp;&nbsp;&nbsp;C. Kadar Hampa<br>
                    &nbsp;&nbsp;&nbsp;D. Butir Hijau
                </td>
                <td style="vertical-align: top;">
                    <br>
                    @if($d->weighbridge) ✦ Weighbridge @else ✦ Non Weighbridge @endif 
                    &nbsp;&nbsp;&nbsp; <b>{{ number_format($d->jumlah_timbangan, 0, ',', '.') }} Kg</b>
                    <br><br>
                    {{ $d->ulangan_1 }} %<br>
                    {{ $d->ulangan_2 }} %<br>
                    {{ $d->ulangan_3 }} %<br>
                    {{ $d->kadar_air_rata_rata }} %<br>
                    {{ $d->kadar_hampa }} %<br>
                    {{ $d->butir_hijau }} %
                </td>
            </tr>
        </tbody>
    </table>

    <p>Catatan : {{ $d->catatan }}</p>

    <table class="signature-table">
        <tr>
            <td width="50%">
                Mengetahui<br><br><br><br><br>
                (.......................................)<br>
                {{ $d->mengetahui }}
            </td>
            <td width="50%">
                {{ strtoupper($d->lokasi) }}, {{ \Carbon\Carbon::parse($d->tanggal_doc)->isoFormat('D MMMM Y') }}<br>
                Petugas<br><br><br><br><br>
                <b>{{ $d->petugas }}</b>
            </td>
        </tr>
    </table>
</body>
</html>