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

class RekapTarifExport implements FromCollection, WithMapping, WithEvents, WithCustomStartCell, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $counter = 0;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.*', 
                'r.name_cabang', 
                'r.parent_company' // FIX: Gunakan parent_company
            )
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->startDate . ' 00:00:00', 
                $this->endDate . ' 23:59:59'
            ])
            ->orderBy('m.tanggal_pelaksanaan', 'asc')
            ->get();
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function map($row): array
    {
        $this->counter++;
        
        // Sanitasi Angka
        $beratRaw = str_replace(',', '.', $row->jumlah_timbangan); 
        $berat = floatval($beratRaw); 
        $tarif = 36.63; 
        $biaya = $berat * $tarif;
        $tanggal = Carbon::parse($row->tanggal_pelaksanaan)->isoFormat('D MMMM');

        // Data dari SQL Join
        $namaCabang = !empty($row->name_cabang) ? strtoupper($row->name_cabang) : '-';
        
        // FIX: Ambil parent_company sebagai Kantor Wilayah
        $namaWilayah = !empty($row->parent_company) ? strtoupper($row->parent_company) : '-';

        return [
            $this->counter,             
            $namaWilayah,               // Kolom B
            $namaCabang,                // Kolom C
            strtoupper($row->mitra),    // Kolom D
            $tanggal,                   
            $row->no_order_pembelian,   
            $row->nomor_hpkk_gabah,     
            $berat,                     
            $tarif,                     
            $biaya,                     
        ];
    }

    // ... (Fungsi registerEvents SAMA PERSIS dengan sebelumnya, tidak berubah)
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $start = Carbon::parse($this->startDate)->isoFormat('D MMMM Y');
                $end = Carbon::parse($this->endDate)->isoFormat('D MMMM Y');

                // Header Judul
                $sheet->mergeCells('A1:J1'); $sheet->setCellValue('A1', 'REKAP PELAKSANAAN PEMERIKSAAN KUALITAS DAN KUANTITAS GABAH KERING PANEN (GKP)');
                $sheet->mergeCells('A2:J2'); $sheet->setCellValue('A2', 'OLEH PT SUCOFINDO');
                $sheet->mergeCells('A3:J3'); $sheet->setCellValue('A3', 'PERIODE ' . $start . ' s.d ' . $end);
                $sheet->getStyle('A1:A3')->applyFromArray(['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => 'center']]);

                // Header Tabel
                $sheet->setCellValue('A5', 'No.');
                $sheet->setCellValue('B5', 'Kantor Wilayah');
                $sheet->setCellValue('C5', 'Kantor Cabang');
                $sheet->setCellValue('D5', 'Pelaksana Pengolahan');
                $sheet->setCellValue('E5', 'Tanggal');
                $sheet->setCellValue('F5', 'No. PO');
                $sheet->setCellValue('G5', 'No. LHPK');
                $sheet->setCellValue('H5', 'Kuantum GKP' . PHP_EOL . '(Kg)');
                $sheet->setCellValue('I5', 'Pemeriksaan Kualitas dan' . PHP_EOL . 'Kuantitas');
                $sheet->setCellValue('I6', 'Tarif (Rp/Kg)');
                $sheet->setCellValue('J6', 'Biaya (Rp)');

                $sheet->mergeCells('A5:A6'); $sheet->mergeCells('B5:B6'); $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('D5:D6'); $sheet->mergeCells('E5:E6'); $sheet->mergeCells('F5:F6');
                $sheet->mergeCells('G5:G6'); $sheet->mergeCells('H5:H6'); $sheet->mergeCells('I5:J5');

                $sheet->getStyle('A5:J6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
                ]);

                // Data & Total
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 7) {
                    $sheet->getStyle('A7:J' . $lastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'alignment' => ['vertical' => 'center']
                    ]);
                    $sheet->getStyle('H7:H' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('I7:I' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('J7:J' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('A7:A' . $lastRow)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('H7:J' . $lastRow)->getAlignment()->setHorizontal('right');

                    // Total
                    $totalRow = $lastRow + 1;
                    $sheet->mergeCells('A' . $totalRow . ':G' . $totalRow);
                    $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                    $sheet->setCellValue('H' . $totalRow, '=SUM(H7:H' . $lastRow . ')');
                    $sheet->setCellValue('J' . $totalRow, '=SUM(J7:J' . $lastRow . ')');
                    
                    $sheet->getStyle('A' . $totalRow . ':J' . $totalRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEEEEEE']],
                    ]);
                    $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal('right');
                    $sheet->getStyle('H' . $totalRow . ':J' . $totalRow)->getAlignment()->setHorizontal('right');
                    $sheet->getStyle('H' . $totalRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('J' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
                }
            },
        ];
    }
}