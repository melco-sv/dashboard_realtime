<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class RekapAnalisaExport implements FromCollection, WithMapping, WithEvents, WithCustomStartCell, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $groupId; // Properti untuk filter security
    protected $counter = 0;

    // Constructor menerima 3 parameter (termasuk groupId untuk filter cabang)
    public function __construct($startDate, $endDate, $groupId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->groupId = $groupId;
    }

    // 1. QUERY DATA (JOIN KE TABEL CABANG)
    public function collection()
    {
        $query = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.*', 
                'r.name_cabang', 
                'r.parent_company' // Wilayah
            )
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->startDate . ' 00:00:00', 
                $this->endDate . ' 23:59:59'
            ]);

        // Filter Security (Jika Inspektor/Admin memilih cabang)
        if (!empty($this->groupId)) {
            $query->where('m.group', $this->groupId);
        }

        return $query->orderBy('m.tanggal_pelaksanaan', 'asc')->get();
    }

    // 2. DATA DIMULAI DARI BARIS KE-7 (Karena Row 1-6 untuk Header)
    public function startCell(): string
    {
        return 'A7';
    }

    // 3. MAPPING DATA KE KOLOM EXCEL
    public function map($row): array
    {
        $this->counter++;
        
        $tanggal = Carbon::parse($row->tanggal_pelaksanaan)->isoFormat('D MMMM Y');
        $namaCabang = !empty($row->name_cabang) ? strtoupper($row->name_cabang) : '-';
        $namaWilayah = !empty($row->parent_company) ? strtoupper($row->parent_company) : '-';
        
        // Bersihkan angka (ganti koma jadi titik jika perlu)
        $kuantum = floatval(str_replace(',', '.', $row->jumlah_timbangan));

        return [
            $this->counter,             // A: No
            $namaWilayah,               // B: Kantor Wilayah
            $namaCabang,                // C: Kantor Cabang
            strtoupper($row->mitra),    // D: Pelaksana Pengolahan
            $tanggal,                   // E: Tanggal
            $row->no_order_pembelian,   // F: No. PO
            $row->nomor_hpkk_gabah,     // G: No. LHPK
            $kuantum,                   // H: Kuantum
            $row->ulangan_1,            // I: KA 1
            $row->ulangan_2,            // J: KA 2
            $row->ulangan_3,            // K: KA 3
            $row->kadar_air_rata_rata,  // L: KA Rata-rata
            $row->kadar_hampa,          // M: Hampa
            $row->butir_hijau,          // N: Butir Hijau
        ];
    }

    // 4. STYLING & HEADER CUSTOM (SESUAI GAMBAR)
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $start = Carbon::parse($this->startDate)->isoFormat('D MMMM Y');
                $end = Carbon::parse($this->endDate)->isoFormat('D MMMM Y');

                // --- BAGIAN A: JUDUL UTAMA (Row 1-3) ---
                $sheet->mergeCells('A1:N1'); 
                $sheet->setCellValue('A1', 'REKAP PELAKSANAAN PEMERIKSAAN KUALITAS DAN KUANTITAS GABAH KERING PANEN (GKP)');
                
                $sheet->mergeCells('A2:N2'); 
                $sheet->setCellValue('A2', 'OLEH PT SUCOFINDO');
                
                $sheet->mergeCells('A3:N3'); 
                $sheet->setCellValue('A3', 'PERIODE ' . $start . ' s.d ' . $end);
                
                $sheet->getStyle('A1:A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12], 
                    'alignment' => ['horizontal' => 'center']
                ]);

                // --- BAGIAN B: HEADER TABEL (Row 5-6) ---
                
                // Set Judul Kolom Row 5
                $sheet->setCellValue('A5', 'No');
                $sheet->setCellValue('B5', 'Kantor Wilayah');
                $sheet->setCellValue('C5', 'Kantor Cabang');
                $sheet->setCellValue('D5', 'Pelaksana Pengolahan');
                $sheet->setCellValue('E5', 'Tanggal');
                $sheet->setCellValue('F5', 'No. PO');
                $sheet->setCellValue('G5', 'No. LHPK');
                $sheet->setCellValue('H5', 'Kuantum GKP' . PHP_EOL . '(Kg)');
                $sheet->setCellValue('I5', 'Hasil Analisa'); // Header Group

                // Merge Sel Vertikal (Row 5 ke 6)
                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('D5:D6');
                $sheet->mergeCells('E5:E6');
                $sheet->mergeCells('F5:F6');
                $sheet->mergeCells('G5:G6');
                $sheet->mergeCells('H5:H6');

                // Merge Sel Horizontal untuk "Hasil Analisa" (I5 sampai N5)
                $sheet->mergeCells('I5:N5');

                // Set Sub-Header di Row 6
                $sheet->setCellValue('I6', 'Kadar Air 1 (%)');
                $sheet->setCellValue('J6', 'Kadar Air 2 (%)');
                $sheet->setCellValue('K6', 'Kadar Air 3 (%)');
                $sheet->setCellValue('L6', 'Kadar Air Rata Rata (%)');
                $sheet->setCellValue('M6', 'Kadar Hampa (%)');
                $sheet->setCellValue('N6', 'Kadar Butir Hijau (%) (%)');

                // Styling Header Tabel (Warna Abu-abu & Border)
                $sheet->getStyle('A5:N6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']], // Abu-abu
                ]);

                // --- BAGIAN C: FORMATTING DATA (Row 7 ke bawah) ---
                $lastRow = $sheet->getHighestRow();
                
                if ($lastRow >= 7) {
                    // Border Semua Data
                    $sheet->getStyle('A7:N' . $lastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'alignment' => ['vertical' => 'center']
                    ]);

                    // Format Angka Desimal (Kolom H sampai N)
                    $sheet->getStyle('H7:H' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00'); // Kuantum
                    $sheet->getStyle('I7:N' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00'); // Hasil Lab

                    // Alignment
                    $sheet->getStyle('A7:A' . $lastRow)->getAlignment()->setHorizontal('center'); // No
                    $sheet->getStyle('H7:N' . $lastRow)->getAlignment()->setHorizontal('right');  // Angka

                    // --- BAGIAN D: TOTAL ROW ---
                    $totalRow = $lastRow + 1;
                    
                    // Merge Label Total (A sampai G)
                    $sheet->mergeCells('A' . $totalRow . ':G' . $totalRow);
                    $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                    
                    // Rumus SUM Kuantum (Kolom H)
                    $sheet->setCellValue('H' . $totalRow, '=SUM(H7:H' . $lastRow . ')');

                    // Styling Baris Total
                    $sheet->getStyle('A' . $totalRow . ':N' . $totalRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEEEEEE']], // Abu-abu muda
                    ]);

                    $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal('right');
                    $sheet->getStyle('H' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
                }
            },
        ];
    }
}