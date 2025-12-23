<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasHpkkBeras;
use Barryvdh\DomPDF\Facade\Pdf;

class BerasPdfController extends Controller
{
    public function print($id, $type)
    {
        $data = MasHpkkBeras::findOrFail($id);
        $viewData = ['d' => $data];

        // Sanitasi Nama File (Ganti / jadi -)
        $safeNomor = str_replace('/', '-', $data->nomor_hpkk_beras); 

        if ($type == 'hpk') {
            $pdf = Pdf::loadView('pdf.beras_hpk', $viewData);
            return $pdf->stream('HPK-Beras-' . $safeNomor . '.pdf');
        } 
        elseif ($type == 'lhpk') {
            $pdf = Pdf::loadView('pdf.beras_lhpk', $viewData);
            return $pdf->stream('LHPK-Beras-' . $safeNomor . '.pdf');
        } 
        elseif ($type == 'witnessing') {
            $pdf = Pdf::loadView('pdf.beras_witnessing', $viewData);
            return $pdf->stream('Witnessing-Beras-' . $safeNomor . '.pdf');
        }

        return abort(404);
    }
}