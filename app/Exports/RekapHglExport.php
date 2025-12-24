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

class RekapHglExport implements FromCollection, WithMapping, WithEvents, WithCustomStartCell, ShouldAutoSize
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
        return DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select('m.*', 'r.name_cabang', 'r.parent_company')
            ->whereBetween('m.tanggal_pemeriksaan', [
                $this->startDate . ' 00:00:00', 
                $this->endDate . ' 23:59:59'
            ])
            ->orderBy('m.tanggal_pemeriksaan', 'asc')
            ->get();
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function map($row): array
    {
        $this->counter++;
        
        // Sanitasi Angka (hapus koma)
        $kuantumGkp = (float) str_replace(',', '.', $row->kuantum_gabah_sesuai_mo ?? 0);
        $kuantumBeras = (float) str_replace(',', '.', $row->kuantum_beras ?? 0);
        
        $tanggal = Carbon::parse($row->tanggal_pemeriksaan)->isoFormat('D MMMM');
        $namaCabang = !empty($row->name_cabang) ? strtoupper($row->name_cabang) : '-';
        $namaWilayah = !empty($row->parent_company) ? strtoupper($row->parent_company) : '-';
        
        // Mapping kolom Pelaksana Pengolahan (Karena tidak ada kolom 'mitra' di tabel beras, kita pakai '-' atau 'tempat_pemeriksaan')
        $pelaksana = '-'; 

        return [
            $this->counter,
            $namaWilayah,
            $namaCabang,
            $pelaksana,
            $tanggal,
            $row->id_mo ?? '-',            // Nomor MO
            $kuantumGkp,                   // Kuantum GKP (Kg)
            $row->nomor_lhpk_beras ?? '-', // No. LHPK
            $kuantumBeras,                 // Jumlah Kuantum Beras
            
            // Hasil Analisa
            $row->derajat_sosoh ?? 0,
            $row->ulangan_1 ?? 0,
            $row->ulangan_2 ?? 0,
            $row->ulangan_3 ?? 0,
            $row->rata_rata ?? 0,     // Kadar Air
            $row->butir_patah ?? 0,
            $row->menir ?? 0,         // Butir Menir
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
                $sheet->mergeCells('A1:P1'); $sheet->setCellValue('A1', 'LAPORAN PEMERIKSAAN HGL');
                $sheet->mergeCells('A2:P2'); $sheet->setCellValue('A2', 'PERIODE ' . $start . ' s.d ' . $end);
                $sheet->getStyle('A1:A2')->applyFromArray(['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => 'center']]);

                // Header Tabel
                $sheet->setCellValue('A5', 'No');
                $sheet->setCellValue('B5', 'Kantor Wilayah');
                $sheet->setCellValue('C5', 'Kantor Cabang');
                $sheet->setCellValue('D5', 'Pelaksana Pengolahan');
                $sheet->setCellValue('E5', 'Tanggal');
                $sheet->setCellValue('F5', 'Nomor MO');
                $sheet->setCellValue('G5', 'Kuantum GKP (Kg)');
                $sheet->setCellValue('H5', 'No. LHPK');
                $sheet->setCellValue('I5', 'Jumlah Kuantum Beras');
                $sheet->setCellValue('J5', 'Hasil Analisa');

                $sheet->setCellValue('J6', 'Derajat Sosoh');
                $sheet->setCellValue('K6', 'Ulangan 1');
                $sheet->setCellValue('L6', 'Ulangan 2');
                $sheet->setCellValue('M6', 'Ulangan 3');
                $sheet->setCellValue('N6', 'Kadar Air');
                $sheet->setCellValue('O6', 'Butir Patah');
                $sheet->setCellValue('P6', 'Butir Menir');

                $sheet->mergeCells('A5:A6'); $sheet->mergeCells('B5:B6'); $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('D5:D6'); $sheet->mergeCells('E5:E6'); $sheet->mergeCells('F5:F6');
                $sheet->mergeCells('G5:G6'); $sheet->mergeCells('H5:H6'); $sheet->mergeCells('I5:I6');
                $sheet->mergeCells('J5:P5'); 

                // Style Header
                $sheet->getStyle('A5:P6')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE65100']],
                ]);

                // Style Data
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 7) {
                    $sheet->getStyle('A7:P' . $lastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
                        'alignment' => ['vertical' => 'center']
                    ]);
                    $sheet->getStyle('G7:G' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('I7:I' . $lastRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('A7:A' . $lastRow)->getAlignment()->setHorizontal('center');
                }
            },
        ];
    }
}