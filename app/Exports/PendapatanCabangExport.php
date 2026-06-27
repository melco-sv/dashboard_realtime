<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PendapatanCabangExport implements FromCollection, WithMapping, WithEvents, WithCustomStartCell, ShouldAutoSize
{
    protected Collection $rows;
    protected float $tarifGabah;
    protected float $tarifBeras;
    protected string $periodLabel;

    protected int $counter = 0;
    protected string $lastCol  = 'L';
    protected int $headerRow   = 6;
    protected int $startRow    = 7;

    public function __construct(Collection $rows, float $tarifGabah, float $tarifBeras, string $periodLabel = 'Seluruh Periode')
    {
        $this->rows        = $rows;
        $this->tarifGabah  = $tarifGabah;
        $this->tarifBeras  = $tarifBeras;
        $this->periodLabel = $periodLabel;
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function startCell(): string
    {
        return 'A' . $this->startRow; // A7
    }

    public function map($row): array
    {
        $this->counter++;

        return [
            $this->counter,
            $row->wilayah ?: '-',
            $row->cabang ?: '-',
            (float) $row->gabah_kg,
            (float) $row->beras_kg,
            (float) $row->total_kg,
            (float) $row->pendapatan_gabah,
            (float) $row->pendapatan_beras,
            (float) $row->total_pendapatan,
            (float) $row->kontribusi,
            (int) $row->dok_gkp,
            (int) $row->dok_hgl,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $last  = $this->lastCol;   // L
                $hr    = $this->headerRow; // 6
                $sr    = $this->startRow;  // 7

                // ===== KOP / HEADER PT SUCOFINDO =====
                $sheet->mergeCells("A1:{$last}1");
                $sheet->setCellValue('A1', 'PT SUCOFINDO');
                $sheet->mergeCells("A2:{$last}2");
                $sheet->setCellValue('A2', 'LAPORAN PENDAPATAN PEMERIKSAAN GKP & HGL PER CABANG');
                $sheet->mergeCells("A3:{$last}3");
                $sheet->setCellValue('A3', $this->periodLabel);
                $sheet->mergeCells("A4:{$last}4");
                $sheet->setCellValue('A4', sprintf(
                    'Tarif GKP: Rp %s/Kg   ·   Tarif HGL: Rp %s/Kg',
                    number_format($this->tarifGabah, 2, ',', '.'),
                    number_format($this->tarifBeras, 2, ',', '.')
                ));

                $sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => 'center']]);
                $sheet->getStyle('A2')->applyFromArray(['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => 'center']]);
                $sheet->getStyle("A3:A4")->applyFromArray(['font' => ['italic' => true, 'size' => 10], 'alignment' => ['horizontal' => 'center']]);

                // ===== HEADER TABEL (row 6) =====
                $headers = [
                    'No.', 'Kantor Wilayah', 'Cabang',
                    'Tonase Gabah (Kg)', 'Tonase Beras (Kg)', 'Total Tonase (Kg)',
                    'Pendapatan Gabah (Rp)', 'Pendapatan Beras (Rp)', 'Total Pendapatan (Rp)',
                    'Kontribusi (%)', 'Jml Dok GKP', 'Jml Dok HGL',
                ];
                $col = 'A';
                foreach ($headers as $h) {
                    $sheet->setCellValue($col . $hr, $h);
                    $col++;
                }

                $sheet->getStyle("A{$hr}:{$last}{$hr}")->applyFromArray([
                    'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1F4E78']],
                ]);
                $sheet->getRowDimension($hr)->setRowHeight(30);

                $lastRow = $sheet->getHighestRow();

                if ($lastRow >= $sr) {
                    // Border + alignment data
                    $sheet->getStyle("A{$sr}:{$last}{$lastRow}")->applyFromArray([
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'alignment' => ['vertical' => 'center'],
                    ]);
                    $sheet->getStyle("A{$sr}:A{$lastRow}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("D{$sr}:I{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("J{$sr}:J{$lastRow}")->getNumberFormat()->setFormatCode('0.00"%"');
                    $sheet->getStyle("K{$sr}:L{$lastRow}")->getAlignment()->setHorizontal('center');

                    // Sorot cabang pendapatan tertinggi (baris pertama)
                    $sheet->getStyle("A{$sr}:{$last}{$sr}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF2CC']],
                    ]);

                    // ===== BARIS TOTAL =====
                    $totalRow = $lastRow + 1;
                    $sheet->mergeCells("A{$totalRow}:C{$totalRow}");
                    $sheet->setCellValue("A{$totalRow}", 'TOTAL');
                    foreach (['D', 'E', 'F', 'G', 'H', 'I', 'K', 'L'] as $c) {
                        $sheet->setCellValue("{$c}{$totalRow}", "=SUM({$c}{$sr}:{$c}{$lastRow})");
                    }
                    $sheet->setCellValue("J{$totalRow}", 100);

                    $sheet->getStyle("A{$totalRow}:{$last}{$totalRow}")->applyFromArray([
                        'font'    => ['bold' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD9E1F2']],
                    ]);
                    $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal('center');
                    $sheet->getStyle("D{$totalRow}:I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle("J{$totalRow}")->getNumberFormat()->setFormatCode('0.00"%"');
                    $sheet->getStyle("K{$totalRow}:L{$totalRow}")->getAlignment()->setHorizontal('center');

                    // ===== RINGKASAN =====
                    $top = $this->rows->first();
                    $sumRow = $totalRow + 2;

                    $sheet->setCellValue("A{$sumRow}", 'Total Pendapatan');
                    $sheet->setCellValue("C{$sumRow}", "=I{$totalRow}");
                    $sheet->getStyle("C{$sumRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

                    $sheet->setCellValue("A" . ($sumRow + 1), 'Cabang Pendapatan Tertinggi');
                    $sheet->setCellValue("C" . ($sumRow + 1), $top ? ($top->cabang . ' — Rp ' . number_format($top->total_pendapatan, 0, ',', '.')) : '-');

                    $sheet->setCellValue("A" . ($sumRow + 2), 'Jumlah Cabang');
                    $sheet->setCellValue("C" . ($sumRow + 2), $this->rows->count());

                    $sheet->getStyle("A{$sumRow}:A" . ($sumRow + 2))->getFont()->setBold(true);
                }
            },
        ];
    }
}
