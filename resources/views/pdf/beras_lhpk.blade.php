<!DOCTYPE html>
<html>
<head>
    <title>LHPK Beras</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        /* Header Table dengan border box di sekeliling logo dan judul */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid black;
        }
        .header-table td {
            padding: 5px;
            vertical-align: middle;
        }
        .logo {
            width: 80px;
            display: block;
            margin: 0 auto;
        }
        .title {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        
        /* Judul Nomor Dokumen */
        .doc-number {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 12px;
        }

        /* Tabel Info (Meta Data) */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .info-table td {
            padding: 2px;
            vertical-align: top;
        }

        /* Tabel Utama (Content) */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
            font-size: 11px;
        }
        .content-table th {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            background-color: #ffffff;
        }
        .content-table td {
            border: 1px solid black;
            padding: 5px;
            vertical-align: top;
        }

        /* Helper untuk text alignment di dalam cell */
        .val-col {
            padding-left: 5px;
        }
        
        /* Tanda Tangan */
        .signature-table {
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }
        
        /* Utility untuk baris baru agar sejajar */
        .row-item {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 75%; text-align: center; border-right: 1px solid black;">
                <span class="title">LAPORAN HASIL PEMERIKSAAN KUALITAS (LHPK) BERAS</span>
            </td>
            <td style="width: 25%; text-align: center;">
                @if($logo)<img src="{{ $logo }}" class="logo" alt="SUCOFINDO">@endif
                <br>
                <b style="font-size: 10px;">SUCOFINDO</b>
            </td>
        </tr>
    </table>

    <div class="doc-number">
        No. LHPK {{ $d->nomor_hpkk_beras }}
    </div>

    <table class="info-table">
        <tr>
            <td width="200">Nomor Order</td>
            <td>: {{ $d->nomor_order }}</td>
        </tr>
        <tr>
            <td>Tempat Pelaksanaan</td>
            <td>: {{ $d->tempat_pemeriksaan }}</td>
        </tr>
        <tr>
            <td>Tanggal Pemeriksaan</td>
            <td>: {{ \Carbon\Carbon::parse($d->tanggal_pemeriksaan)->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td>Kode Sampel</td>
            <td>: {{ $d->kode_sample }}</td>
        </tr>
        <tr>
            <td>Nomor Manufacturing Order</td>
            <td>: {{ $d->id_mo }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th width="60%">PEMERIKSAAN</th>
                <th width="40%">HASIL PEMERIKSAAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <b>1. Kualitatif Beras</b><br>
                    <div style="padding-left: 15px;">
                        A. Kondisi Kemasan<br>
                        B. Hama<br>
                        C. Dedak / Katul / Sekam<br>
                        D. Bau Apek / Busuk / Asing<br>
                        E. Bahan Kimia
                    </div>
                </td>
                <td class="val-col">
                    <br> {{ $d->kondisi_kemasan }}<br>
                    {{ $d->hama }}<br>
                    {{ $d->dedak_katul_sekam }}<br>
                    {{ $d->bau }}<br>
                    {{ $d->bahan_kimia }}
                </td>
            </tr>

            <tr>
                <td>
                    <b>2. Kuantitatif Beras</b><br>
                    <div style="padding-left: 15px;">
                        A. Kadar Air<br>
                        <div style="padding-left: 15px;">
                            1. Ulangan 1<br>
                            2. Ulangan 2<br>
                            3. Ulangan 3<br>
                            Rata - rata
                        </div>
                        B. Derajat Sosoh<br>
                        C. Butir Patah<br>
                        D. Menir<br>
                        E. Kuantum Gabah sesuai MO<br>
                        F. Kuantum Beras<br>
                        G. Rendemen Pengolahan<br>
                        H. Hasil Samping Menir<br>
                        I. Hasil Samping Butir Patah<br>
                        J. Hasil Samping Dedak / Katul<br>
                        K. Hasil Samping Butir Kuning / Rusak
                    </div>
                </td>
                <td class="val-col">
                    <br> <br> {{ $d->ulangan_1 }} %<br>
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

    <div style="margin-top: 10px; font-size: 11px;">
        Catatan : {{ $d->catatan }}
    </div>

    <table class="signature-table">
        <tr>
            <td width="50%">
                <br>
                Mengetahui
                <br><br><br><br><br>
                <b>( {{ $d->mengetahui }} )</b>
            </td>
            <td width="50%">
                {{ strtoupper($d->lokasi) }}, {{ \Carbon\Carbon::parse($d->tanggal_doc)->isoFormat('D MMMM Y') }}<br>
                Petugas
                <br><br><br><br><br>
                <b>{{ $d->petugas }}</b>
            </td>
        </tr>
    </table>

</body>
</html>