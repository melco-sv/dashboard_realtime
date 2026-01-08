<!DOCTYPE html>
<html>
<head>
    <title>HPK Beras</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { border: 1px solid black; padding: 5px; }
        .logo { width: 80px; }
        .title { font-size: 14px; font-weight: bold; text-align: center; }
        .info-table { width: 100%; margin-bottom: 10px; }
        .info-table td { padding: 2px; vertical-align: top; }
        .content-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .content-table th, .content-table td { border: 1px solid black; padding: 5px; vertical-align: top; }
        .signature-table { width: 100%; margin-top: 40px; text-align: center; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 80%; text-align: center;">
                <span class="title">HASIL PEMERIKSAAN KUALITAS (HPK) BERAS</span>
            </td>
            <td style="width: 20%; text-align: center;">
                <img src="{{ public_path('assets/logo-sucofindo.png') }}" class="logo"><br><b>SUCOFINDO</b>
            </td>
        </tr>
    </table>

    <div style="text-align: center; font-weight: bold; margin-bottom: 15px;">
        No. HPK {{ $d->nomor_hpkk_beras }}
    </div>

    <table class="info-table">
        <tr><td width="180">Nomor Order</td><td>: {{ $d->nomor_order }}</td></tr>
        <tr><td>Tempat Pelaksanaan</td><td>: {{ $d->tempat_pemeriksaan }}</td></tr>
        <tr><td>Tanggal Pemeriksaan</td><td>: {{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->isoFormat('D MMMM Y') }}</td></tr>
        <tr><td>Kode Sampel</td><td>: {{ $d->kode_sample }}</td></tr>
        <tr><td>Nomor Manufacturing Order</td><td>: {{ $d->id_mo }}</td></tr>
    </table>

    <table class="content-table">
        <thead>
            <tr><th width="60%">PEMERIKSAAN</th><th width="40%">HASIL PEMERIKSAAN</th></tr>
        </thead>
        <tbody>
            <tr>
                <td><b>1. Kualitatif Beras</b><br>
                    &nbsp;&nbsp;&nbsp;A. Kondisi Kemasan<br>
                    &nbsp;&nbsp;&nbsp;B. Hama<br>
                    &nbsp;&nbsp;&nbsp;C. Dedak / Katul / Sekam<br>
                    &nbsp;&nbsp;&nbsp;D. Bau Apek / Busuk / Asing<br>
                    &nbsp;&nbsp;&nbsp;E. Bahan Kimia
                </td>
                <td><br>
                    {{ $d->kondisi_kemasan }}<br>
                    {{ $d->hama }}<br>
                    {{ $d->dedak_katul_sekam }}<br>
                    {{ $d->bau }}<br>
                    {{ $d->bahan_kimia }}
                </td>
            </tr>
            <tr>
                <td><b>2. Kuantitatif Beras</b><br>
                    &nbsp;&nbsp;&nbsp;A. Kadar Air<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. Ulangan 1<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. Ulangan 2<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. Ulangan 3<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rata - rata<br>
                    &nbsp;&nbsp;&nbsp;B. Derajat Sosoh<br>
                    &nbsp;&nbsp;&nbsp;C. Butir Patah<br>
                    &nbsp;&nbsp;&nbsp;D. Menir<br>
                    &nbsp;&nbsp;&nbsp;E. Kuantum Gabah sesuai MO<br>
                    &nbsp;&nbsp;&nbsp;F. Kuantum Beras<br>
                    &nbsp;&nbsp;&nbsp;G. Rendemen Pengolahan<br>
                    &nbsp;&nbsp;&nbsp;H. Hasil Samping Menir<br>
                    &nbsp;&nbsp;&nbsp;I. Hasil Samping Butir Patah<br>
                    &nbsp;&nbsp;&nbsp;J. Hasil Samping Dedak / Katul<br>
                    &nbsp;&nbsp;&nbsp;K. Hasil Samping Butir Kuning / Rusak
                </td>
                <td><br><br>
                    {{ $d->ulangan_1 }} %<br>
                    {{ $d->ulangan_2 }} %<br>
                    {{ $d->ulangan_3 }} %<br>
                    {{ $d->rata_rata }} %<br>
                    {{ $d->derajat_sosoh }} %<br>
                    {{ $d->butir_patah }} %<br>
                    {{ $d->menir }} %<br>
                    {{ number_format($d->kuantum_gabah_sesuai_mo, 0, ',', '.') }} Kg<br>
                    {{ number_format($d->kuantum_beras, 0, ',', '.') }} Kg<br>
                    {{ $d->rendemen_pengolahan }} %<br>
                    {{ number_format($d->hasil_samping_menir, 0, ',', '.') }} Kg<br>
                    {{ number_format($d->hasil_samping_butir_patah, 0, ',', '.') }} Kg<br>
                    {{ number_format($d->hasil_samping_dedak_katul, 0, ',', '.') }} Kg<br>
                    {{ number_format($d->hasil_samping_butir_kuning_rusak, 0, ',', '.') }} Kg
                </td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 11px;">Catatan : {{ $d->catatan }}</p>

    <table class="signature-table">
        <tr>
            <td width="50%"></td>
            <td width="50%">
                {{ strtoupper($d->lokasi) }}, {{ \Carbon\Carbon::parse($d->tanggal_doc)->isoFormat('D MMMM Y') }}<br>
                Petugas<br><br><br><br><br>
                <b>{{ $d->petugas }}</b><br>
                <!-- <br>
                Mengetahui<br>
                <b>{{ $d->mengetahui }}</b> -->
            </td>
        </tr>
    </table>
</body>
</html>