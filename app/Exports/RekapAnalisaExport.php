<?php

namespace App\Exports;

use App\Models\MasHpkkGabah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RekapAnalisaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return MasHpkkGabah::whereBetween('tanggal_pelaksanaan', [$this->startDate, $this->endDate])->get();
    }

    public function headings(): array
    {
        return [
            'No', 'No LHPK', 'Tanggal', 'Kuantum (Kg)', 
            'Ulangan 1', 'Ulangan 2', 'Ulangan 3', 
            'Kadar Air (%)', 'Kadar Hampa (%)', 'Kadar Butir Hijau (%)'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id_hpkk_gabah,
            $row->nomor_hpkk_gabah,
            $row->tanggal_pelaksanaan,
            $row->jumlah_timbangan,
            $row->ulangan_1,
            $row->ulangan_2,
            $row->ulangan_3,
            $row->kadar_air_rata_rata,
            $row->kadar_hampa,
            $row->butir_hijau,
        ];
    }
}