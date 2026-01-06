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
    protected $groupId;
    protected $tempat; // Filter Baru
    protected $mitra;  // Filter Baru
    protected $counter = 0;

    // UPDATE CONSTRUCTOR: Menerima 5 Parameter
    public function __construct($startDate, $endDate, $groupId = null, $tempat = null, $mitra = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->groupId   = $groupId;
        $this->tempat    = $tempat;
        $this->mitra     = $mitra;
    }

    public function collection()
    {
        $query = DB::table('mas_hpkk_gabah as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.*', 
                'r.name_cabang', 
                'r.parent_company'
            )
            ->whereBetween('m.tanggal_pelaksanaan', [
                $this->startDate . ' 00:00:00', 
                $this->endDate . ' 23:59:59'
            ]);

        // 1. Filter Cabang
        if (!empty($this->groupId)) {
            $query->where('m.group', $this->groupId);
        }

        // 2. Filter Tempat (Baru)
        if (!empty($this->tempat)) {
            $query->where('m.lokasi', $this->tempat);
        }

        // 3. Filter Mitra (Baru)
        if (!empty($this->mitra)) {
            $query->where('m.mitra', $this->mitra);
        }

        return $query->orderBy('m.tanggal_pelaksanaan', 'asc')->get();
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function map($row): array
    {
        $this->counter++;
        
        $tanggal = Carbon::parse($row->tanggal_pelaksanaan)->isoFormat('D MMMM Y');
        $namaCabang = !empty($row->name_cabang) ? strtoupper($row->name_cabang) : '-';
        $namaWilayah = !empty($row->parent_company) ? strtoupper($row->parent_company) : '-';
        
        $kuantum = floatval(str_replace(',', '.', $row->jumlah_timbangan));

        return [
            $this->counter,
            $namaWilayah,
            $namaCabang,
            strtoupper($row->mitra),
            $tanggal,
            $row->no_order_pembelian,
            $row->nomor_hpkk_gabah,
            $kuantum,
            $row->ulangan_1,
            $row->ulangan_2,
            $row->ulangan_3,
            $row->kadar_air_rata_rata,
            $row->kadar_hampa,
            $row->butir_hijau,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $start = Carbon::parse($this->startDate)->isoFormat('D MMMM Y');
                $end = Carbon::parse($this->endDate)->isoFormat('D MMMM Y');

                // Header Judul
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

                // Header Tabel
                $sheet->setCellValue('A5', 'No');
                $sheet->setCellValue('B5', 'Kantor Wilayah');
                $sheet->setCellValue('C5', 'Kantor Cabang');
                $sheet->setCellValue('D5', 'Pelaksana Pengolahan');
                $sheet->setCellValue('E5', 'Tanggal');
                $sheet->setCellValue('F5', 'No. PO');
                $sheet->setCellValue('G5', 'No. LHPK');
                $sheet->setCellValue('H5', 'Kuantum GKP' . PHP_EOL . '(Kg)');
                $sheet->setCellValue('I5', 'Hasil Analisa');

                $sheet->mergeCells('A5:A6');
                $sheet->mergeCells('B5:B6');
                $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('D5:D6');
                $sheet->mergeCells('E5:E6');
                $sheet->mergeCells('F5:F6');
                $sheet->mergeCells('G5:G6');
                $sheet->mergeCells('H5:H6');
                $sheet->mergeCells('I5:N5');

                $sheet->setCellValue('I6', 'Kadar Air 1 (%)');
                $sheet->setCellValue('J6', 'Kadar Air 2 (%)');
                $sheet->setCellValue('K6', 'Kadar Air 3 (%)');
                $sheet->setCellValue('L6', 'Kadar Air Rata Rata (%)');
                $sheet->setCellValue('M6', 'Kadar Hampa (%)');
                $sheet->setCellValue('N6', 'Kadar Butir Hijau (%)');

                $sheet->getStyle('A5:N6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9D9D9']],
                ]);

                // Data Formatting
                $lastRow = $sheet->getHighestRow();
                
                if ($lastRow >= 7) {
                    $sheet->getStyle('A7:N' . $lastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'alignment' => ['vertical' => 'center']
                    ]);

                    $sheet->getStyle('H7:H' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('I7:N' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('A7:A' . $lastRow)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle('H7:N' . $lastRow)->getAlignment()->setHorizontal('right');

                    // Total Row
                    $totalRow = $lastRow + 1;
                    $sheet->mergeCells('A' . $totalRow . ':G' . $totalRow);
                    $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                    $sheet->setCellValue('H' . $totalRow, '=SUM(H7:H' . $lastRow . ')');

                    $sheet->getStyle('A' . $totalRow . ':N' . $totalRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEEEEEE']],
                    ]);

                    $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal('right');
                    $sheet->getStyle('H' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
                }
            },
        ];
    }
}