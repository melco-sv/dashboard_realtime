<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; }

/* cover table height = A4(297) - margin-top(25) - margin-bottom(30) = 242mm */

/* COVER elements */
.logo       { max-width: 130px; max-height: 65px; }

.title-block { text-align: center; font-size: 12px; font-weight: bold;
               line-height: 1.7; margin-bottom: 22px; text-transform: uppercase; width: 100%; }
.doc-date    { font-size: 11px; line-height: 1.8; margin-bottom: 14px; }
.body-text   { font-size: 11px; line-height: 1.6; margin-bottom: 14px; }

.info-table    { border-collapse: collapse; font-size: 11px; margin-bottom: 18px; margin-left: 5px; }
.info-table td { padding: 3px 5px 3px 0; vertical-align: top; }
.info-table .col-letter { width: 20px; }
.info-table .col-label  { width: 195px; }
.info-table .col-colon  { width: 14px; }

.sign-date  { font-size: 11px; margin-bottom: 6px; }
.sign-table    { width: 100%; border-collapse: collapse; margin-top: 4px; font-size: 11px; }
.sign-table td { width: 50%; padding: 4px 8px; vertical-align: top; text-align: center; }
.sign-space    { height: 25mm; display: block; }

/* HAL 2 & 3 */
.rekap-title { text-align: center; font-size: 10px; font-weight: bold;
               line-height: 1.5; margin-bottom: 12px; text-transform: uppercase; }
.data-table    { width: 100%; border-collapse: collapse; font-size: 8px; margin-bottom: 10px; }
.data-table th { border: 1px solid #000; padding: 4px 3px; background: #f0f0f0;
                 font-size: 8px; text-align: center; vertical-align: middle; font-weight: bold; }
.data-table td { border: 1px solid #000; padding: 3px 4px; vertical-align: middle; }
.data-table .num       { text-align: center; }
.data-table .right     { text-align: right; }
.data-table .total-row { font-weight: bold; background: #f5f5f5; }

.keterangan { font-size: 8.5px; font-style: italic; margin-bottom: 12px; }
.sign2-table    { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
.sign2-table td { width: 50%; padding: 4px 8px; vertical-align: top; text-align: center; }
.sign2-space    { height: 20mm; display: block; }
</style>
</head>
<body>

{{-- ===================== HALAMAN 1 — COVER ===================== --}}
<div style="position:relative; height:242mm; overflow:hidden;">

    <table style="width:100%;border-collapse:collapse;margin-bottom:10px;">
        <tr>
            <td style="width:50%;text-align:left;vertical-align:middle;">
                @if($logo_idsurvey)
                <img src="{{ $logo_idsurvey }}" class="logo" alt="IDSurvey">
                @endif
            </td>
            <td style="width:50%;text-align:right;vertical-align:middle;">
                @if($logo_sucofindo)
                <img src="{{ $logo_sucofindo }}" class="logo" alt="SUCOFINDO">
                @endif
            </td>
        </tr>
    </table>

    <div class="title-block">
        BERITA ACARA SERAH TERIMA<br>
        DOKUMEN REKAPITULASI DATA PELAKSANAAN PEMERIKSAAN KUALITAS DAN<br>
        KUANTITAS GABAH KERING PANEN (GKP)
    </div>

    <div class="doc-date">
        Tanggal : {{ $tgl_cetak }}<br>
        No. {{ $nomor_surat }}
    </div>

    <div class="body-text">
        Telah dilaksanakan kegiatan pengawasan pemeriksaan kualitas dan pengawasan kuantitas
        Gabah Kering Panen (GKP) di :
    </div>

    <table class="info-table">
        <tr>
            <td class="col-letter">a.</td>
            <td class="col-label">Kantor Wilayah Bulog</td>
            <td class="col-colon">:</td>
            <td>{{ $kanwil ?: '-' }}</td>
        </tr>
        <tr>
            <td class="col-letter">b.</td>
            <td class="col-label">Kantor Cabang Bulog</td>
            <td class="col-colon">:</td>
            <td>{{ $cabang->name_cabang ?? '-' }}</td>
        </tr>
        <tr>
            <td class="col-letter">c.</td>
            <td class="col-label">Rekapitulasi Tanggal</td>
            <td class="col-colon">:</td>
            <td>{{ \Carbon\Carbon::parse($tgl_mulai)->isoFormat('D MMMM Y') }} &ndash; {{ \Carbon\Carbon::parse($tgl_akhir)->isoFormat('D MMMM Y') }}</td>
        </tr>
        <tr>
            <td class="col-letter">d.</td>
            <td class="col-label">Total Penerimaan GKP (Kg)</td>
            <td class="col-colon">:</td>
            <td>{{ number_format($total_kg, 3, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="col-letter">e.</td>
            <td class="col-label">Total Penerimaan GKP (Ton)</td>
            <td class="col-colon">:</td>
            <td>{{ number_format($total_kg / 1000, 3, ',', '.') }}</td>
        </tr>
    </table>

    <div class="body-text">
        Demikian berita acara ini kami sampaikan. Kami ucapkan terima kasih.
    </div>

    <div class="sign-date">{{ $cabang->name_cabang ?? '' }}, {{ $tgl_cetak }}</div>

    <table class="sign-table">
        <tr>
            <td>
                Disusun Oleh,<br>
                <strong>PT SUCOFINDO</strong>
                <table style="width:100%;border-collapse:collapse;"><tr><td style="height:22mm;padding:0;"></td></tr></table>
                <strong>{{ $nama_kepala_unit ?: '.................................................' }}</strong><br>
                Kepala Unit Pelayanan {{ $cabang->name_cabang ?? '' }}
            </td>
            <td>
                Diketahui Oleh,<br>
                <strong>PERUM BULOG</strong>
                <table style="width:100%;border-collapse:collapse;"><tr><td style="height:22mm;padding:0;"></td></tr></table>
                <strong>{{ $nama_pimpinan ?: '.................................................' }}</strong><br>
                Pimpinan Cabang {{ $cabang->name_cabang ?? '' }}
            </td>
        </tr>
    </table>


</div>

{{-- ===================== HALAMAN 2 — REKAP TARIF ===================== --}}
<pagebreak sheet-size="A4-L" margin-top="15" margin-bottom="15" margin-left="18" margin-right="18" />
<div>
    <div class="rekap-title">
        REKAP PELAKSANAAN PEMERIKSAAN KUALITAS DAN KUANTITAS GABAH KERING PANEN (GKP)<br>
        OLEH PT SUCOFINDO<br>
        PERIODE {{ \Carbon\Carbon::parse($tgl_mulai)->isoFormat('D MMMM Y') }} s.d {{ \Carbon\Carbon::parse($tgl_akhir)->isoFormat('D MMMM Y') }}
    </div>

    @php $totalBiaya = 0; $totalKgRekap = 0; @endphp

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th style="width:15%">Kanwil / Kanca</th>
                <th style="width:16%">Pengirim</th>
                <th style="width:9%">Tanggal Pelaksanaan</th>
                <th style="width:16%">No. PO</th>
                <th style="width:14%">No. HPK</th>
                <th style="width:9%">Kuantum Gabah (Kg)</th>
                <th style="width:9%">Tarif Pemeriksaan (Rp/Kg)</th>
                <th style="width:9%">Biaya Pemeriksaan (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $row)
            @php
                $kg    = (float) str_replace(',', '.', $row->jumlah_timbangan ?? 0);
                $biaya = $kg * $tarif;
                $totalKgRekap += $kg;
                $totalBiaya   += $biaya;
            @endphp
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ trim(preg_replace('/^\d+\s*-\s*KANTOR WILAYAH\s*/i', '', $row->parent_company ?? '')) ?: '-' }}<br><strong>{{ $row->name_cabang ?? '-' }}</strong></td>
                <td>{{ $row->pengirim ?? '-' }}</td>
                <td class="num">{{ \Carbon\Carbon::parse($row->tanggal_pelaksanaan)->format('d-m-Y') }}</td>
                <td>{{ $row->no_order_pembelian ?? '-' }}</td>
                <td>{{ $row->nomor_hpkk_gabah ?? '-' }}</td>
                <td class="right">{{ number_format($kg, 2, ',', '.') }}</td>
                <td class="num">{{ number_format($tarif, 2, ',', '.') }}</td>
                <td class="right">Rp {{ number_format($biaya, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;padding:10px;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" style="text-align:right;padding-right:6px;">TOTAL</td>
                <td class="right">{{ number_format($totalKgRekap, 2, ',', '.') }}</td>
                <td></td>
                <td class="right">Rp {{ number_format($totalBiaya, 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="keterangan">Keterangan: Periode yang dicatat merupakan periode transaksi per bulan</div>

    <table class="sign2-table">
        <tr>
            <td>
                Disusun Oleh,<br>
                <strong>PT SUCOFINDO</strong>
                <table style="width:100%;border-collapse:collapse;"><tr><td style="height:18mm;padding:0;"></td></tr></table>
                <strong>{{ $nama_kepala_unit ?: '.................................................' }}</strong><br>
                Kepala Unit Pelayanan {{ $cabang->name_cabang ?? '' }}
            </td>
            <td>
                {{ $cabang->name_cabang ?? '' }}, {{ $tgl_cetak }}<br>
                Kantor Cabang Perum (Bulog) {{ $cabang->name_cabang ?? '' }}
                <table style="width:100%;border-collapse:collapse;"><tr><td style="height:18mm;padding:0;"></td></tr></table>
                <strong>{{ $nama_pimpinan ?: '.................................................' }}</strong><br>
                Pimpinan Kanca
            </td>
        </tr>
    </table>
</div>

{{-- ===================== HALAMAN 3 — REKAP ANALISIS ===================== --}}
<pagebreak sheet-size="A4-L" margin-top="15" margin-bottom="15" margin-left="18" margin-right="18" />
<div>
    <div class="rekap-title">
        REKAP PELAKSANAAN PEMERIKSAAN KUALITAS DAN KUANTITAS GABAH KERING PANEN (GKP)<br>
        OLEH PT SUCOFINDO<br>
        PERIODE {{ \Carbon\Carbon::parse($tgl_mulai)->isoFormat('D MMMM Y') }} s.d {{ \Carbon\Carbon::parse($tgl_akhir)->isoFormat('D MMMM Y') }}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width:3%">No</th>
                <th style="width:12%">Kantor Wilayah</th>
                <th style="width:10%">Kantor Cabang</th>
                <th style="width:13%">Pengirim</th>
                <th style="width:8%">Lokasi</th>
                <th style="width:8%">Tanggal</th>
                <th style="width:13%">No. PO</th>
                <th style="width:8%">Kuantum (Kg)</th>
                <th style="width:12%">No. HPK</th>
                <th style="width:4%">KA (%)</th>
                <th style="width:4%">Hampa (%)</th>
                <th style="width:4%">Hijau (%)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $i => $row)
            @php
                $kg = (float) str_replace(',', '.', $row->jumlah_timbangan ?? 0);
            @endphp
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ trim(preg_replace('/^\d+\s*-\s*KANTOR WILAYAH\s*/i', '', $row->parent_company ?? '')) ?: '-' }}</td>
                <td>{{ $row->name_cabang ?? '-' }}</td>
                <td>{{ $row->pengirim ?? '-' }}</td>
                <td>{{ $row->lokasi ?? '-' }}</td>
                <td class="num">{{ \Carbon\Carbon::parse($row->tanggal_pelaksanaan)->format('d-m-Y') }}</td>
                <td>{{ $row->no_order_pembelian ?? '-' }}</td>
                <td class="right">{{ number_format($kg, 2, ',', '.') }}</td>
                <td>{{ $row->nomor_hpkk_gabah ?? '-' }}</td>
                <td class="num">{{ $row->kadar_air_rata_rata ?? '-' }}</td>
                <td class="num">{{ $row->kadar_hampa ?? '-' }}</td>
                <td class="num">{{ $row->butir_hijau ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="12" style="text-align:center;padding:10px;">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="keterangan">Keterangan: Periode yang dicatat merupakan periode transaksi per bulan</div>

    <table class="sign2-table">
        <tr>
            <td>
                Disusun Oleh,<br>
                <strong>PT SUCOFINDO</strong>
                <table style="width:100%;border-collapse:collapse;"><tr><td style="height:18mm;padding:0;"></td></tr></table>
                <strong>{{ $nama_kepala_unit ?: '.................................................' }}</strong><br>
                Kepala Unit Pelayanan {{ $cabang->name_cabang ?? '' }}
            </td>
            <td>
                {{ $cabang->name_cabang ?? '' }}, {{ $tgl_cetak }}<br>
                Kantor Cabang Perum (Bulog) {{ $cabang->name_cabang ?? '' }}
                <table style="width:100%;border-collapse:collapse;"><tr><td style="height:18mm;padding:0;"></td></tr></table>
                <strong>{{ $nama_pimpinan ?: '.................................................' }}</strong><br>
                Pimpinan Kanca
            </td>
        </tr>
    </table>
</div>

</body>
</html>
