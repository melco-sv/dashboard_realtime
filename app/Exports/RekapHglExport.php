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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RekapHglExport implements FromCollection, WithMapping, WithEvents, WithCustomStartCell, ShouldAutoSize
{
    protected $startDate;
    protected $endDate;
    protected $groupId; // Filter Cabang
    protected $tempat;  // Filter Tempat Pemeriksaan
    protected $counter = 0;

    // 1. UPDATE CONSTRUCTOR (Terima 4 Parameter)
    public function __construct($startDate, $endDate, $groupId = null, $tempat = null)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->groupId   = $groupId;
        $this->tempat    = $tempat;
    }

    // 2. QUERY DATA (Dengan Filter)
    public function collection()
    {
        $query = DB::table('mas_hpkk_beras as m')
            ->leftJoin('ref_cabang as r', 'm.group', '=', 'r.code_cabang')
            ->select(
                'm.*', 
                'r.name_cabang', 
                'r.parent_company'
            )
            ->whereBetween('m.tanggal_pemeriksaan', [
                $this->startDate . ' 00:00:00', 
                $this->endDate . ' 23:59:59'
            ]);

        // Filter Cabang
        if (!empty($this->groupId)) {
            $query->where('m.group', $this->groupId);
        }

        // Filter Tempat
        if (!empty($this->tempat)) {
            $query->where('m.tempat_pemeriksaan', $this->tempat);
        }

        return $query->orderBy('m.tanggal_pemeriksaan', 'asc')->get();
    }

    public function startCell(): string
    {
        return 'A7';
    }

    // 3. MAPPING DATA
    public function map($row): array
    {
        $this->counter++;
        
        // Sanitasi Angka (String to Float)
        $kuantumGkp = (float) str_replace(',', '.', $row->kuantum_gabah_sesuai_mo ?? 0);
        $kuantumBeras = (float) str_replace(',', '.', $row->kuantum_beras ?? 0);
        
        $tanggal = Carbon::parse($row->tanggal_pemeriksaan)->isoFormat('D MMMM Y');
        $namaCabang = !empty($row->name_cabang) ? strtoupper($row->name_cabang) : '-';
        $namaWilayah = !empty($row->parent_company) ? strtoupper($row->parent_company) : '-';
        
        // Pelaksana Pengolahan diambil dari Tempat Pemeriksaan
        $pelaksana = !empty($row->tempat_pemeriksaan) ? strtoupper($row->tempat_pemeriksaan) : '-';

        return [
            $this->counter,             // A: No
            $namaWilayah,               // B: Wilayah
            $namaCabang,                // C: Cabang
            $pelaksana,                 // D: Pelaksana
            $tanggal,                   // E: Tanggal
            $row->id_mo ?? '-',         // F: No MO
            $kuantumGkp,                // G: Kuantum GKP
            $row->nomor_lhpk_beras ?? '-', // H: No LHPK
            $kuantumBeras,              // I: Kuantum Beras
            
            // J-P: Hasil Analisa
            $row->derajat_sosoh ?? 0,
            $row->ulangan_1 ?? 0,
            $row->ulangan_2 ?? 0,
            $row->ulangan_3 ?? 0,
            $row->rata_rata ?? 0,       // Kadar Air Avg
            $row->butir_patah ?? 0,
            $row->menir ?? 0,
        ];
    }

    // 4. STYLING (THEME ORANGE)
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $start = Carbon::parse($this->startDate)->isoFormat('D MMMM Y');
                $end = Carbon::parse($this->endDate)->isoFormat('D MMMM Y');

                // --- HEADER JUDUL (Row 1-3) ---
                $sheet->mergeCells('A1:P1'); 
                $sheet->setCellValue('A1', 'LAPORAN PEMERIKSAAN HGL (BERAS)');
                
                $sheet->mergeCells('A2:P2'); 
                $sheet->setCellValue('A2', 'PERIODE ' . $start . ' s.d ' . $end);
                
                $sheet->getStyle('A1:A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14], 
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                ]);

                // --- HEADER TABEL (Row 5-6) ---
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

                // Sub-Header (Row 6)
                $sheet->setCellValue('J6', 'Derajat Sosoh');
                $sheet->setCellValue('K6', 'Ulangan 1');
                $sheet->setCellValue('L6', 'Ulangan 2');
                $sheet->setCellValue('M6', 'Ulangan 3');
                $sheet->setCellValue('N6', 'Kadar Air');
                $sheet->setCellValue('O6', 'Butir Patah');
                $sheet->setCellValue('P6', 'Butir Menir');

                // Merge Cells
                $sheet->mergeCells('A5:A6'); 
                $sheet->mergeCells('B5:B6'); 
                $sheet->mergeCells('C5:C6');
                $sheet->mergeCells('D5:D6'); 
                $sheet->mergeCells('E5:E6'); 
                $sheet->mergeCells('F5:F6');
                $sheet->mergeCells('G5:G6'); 
                $sheet->mergeCells('H5:H6'); 
                $sheet->mergeCells('I5:I6');
                $sheet->mergeCells('J5:P5'); // Merge Header 'Hasil Analisa'

                // Style Header (Orange Theme)
                $sheet->getStyle('A5:P6')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], // Teks Putih
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, 
                        'vertical' => Alignment::VERTICAL_CENTER, 
                        'wrapText' => true
                    ],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID, 
                        'startColor' => ['argb' => 'FFE65100'] // Orange Gelap
                    ],
                ]);

                // --- DATA FORMATTING (Row 7++) ---
                $lastRow = $sheet->getHighestRow();
                
                if ($lastRow >= 7) {
                    // Border Semua Data
                    $sheet->getStyle('A7:P' . $lastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
                    ]);

                    // Format Angka (Koma Desimal)
                    // G (Kuantum GKP) dan I (Kuantum Beras)
                    $sheet->getStyle('G7:G' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('I7:I' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    
                    // J-P (Hasil Analisa)
                    $sheet->getStyle('J7:P' . $lastRow)->getNumberFormat()->setFormatCode('#,##0.00');

                    // Alignment
                    $sheet->getStyle('A7:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('G7:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // --- TOTAL ROW ---
                    $totalRow = $lastRow + 1;
                    $sheet->mergeCells('A' . $totalRow . ':F' . $totalRow);
                    $sheet->setCellValue('A' . $totalRow, 'TOTAL');
                    
                    // Rumus Sum Kuantum GKP
                    $sheet->setCellValue('G' . $totalRow, '=SUM(G7:G' . $lastRow . ')');
                    
                    // Rumus Sum Kuantum Beras
                    $sheet->setCellValue('I' . $totalRow, '=SUM(I7:I' . $lastRow . ')');

                    // Style Total Row
                    $sheet->getStyle('A' . $totalRow . ':P' . $totalRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFE0B2']], // Orange Muda
                    ]);

                    $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('G' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
                    $sheet->getStyle('I' . $totalRow)->getNumberFormat()->setFormatCode('#,##0.00');
                }
            },
        ];
    }
}