<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasHpkkGabah;
use Barryvdh\DomPDF\Facade\Pdf;

class GabahPdfController extends Controller
{
    public function print($id, $type)
    {
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);

        $data = MasHpkkGabah::findOrFail($id);

        $viewData = ['d' => $data];

        // SANITASI NAMA FILE
        // Mengganti tanda '/' menjadi '-' agar tidak error saat download
        $safeNomor = str_replace('/', '-', $data->nomor_hpkk_gabah);

        // LOGIKA MEMILIH TEMPLATE
        if ($type == 'hpk') {
            $pdf = Pdf::loadView('pdf.gabah_hpk', $viewData);
            return $pdf->stream('HPK-' . $safeNomor . '.pdf');
        } elseif ($type == 'lhpk') {
            $pdf = Pdf::loadView('pdf.gabah_lhpk', $viewData);
            return $pdf->stream('LHPK-' . $safeNomor . '.pdf');
        } elseif ($type == 'witnessing') {
            $pdf = Pdf::loadView('pdf.gabah_witnessing', $viewData);
            return $pdf->stream('Witnessing-' . $safeNomor . '.pdf');
        }

        return abort(404);
    }
}
