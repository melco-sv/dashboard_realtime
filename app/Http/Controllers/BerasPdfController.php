<?php

namespace App\Http\Controllers;

use App\Models\MasHpkkBeras;
use Barryvdh\DomPDF\Facade\Pdf;

class BerasPdfController extends Controller
{
    public function print($id, $type)
    {
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 60);

        $data = MasHpkkBeras::findOrFail($id);

        $viewData = [
            'd'    => $data,
            'logo' => $this->getLogoBase64(),
        ];

        $safeNomor = str_replace('/', '-', $data->nomor_hpkk_beras);

        $options = [
            'isHtml5ParserEnabled' => false,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'sans-serif',
            'dpi'                  => 96,
        ];

        if ($type === 'hpk') {
            $pdf = Pdf::setOptions($options)->loadView('pdf.beras_hpk', $viewData);
            return $pdf->stream('HPK-Beras-' . $safeNomor . '.pdf');
        } elseif ($type === 'lhpk') {
            $pdf = Pdf::setOptions($options)->loadView('pdf.beras_lhpk', $viewData);
            return $pdf->stream('LHPK-Beras-' . $safeNomor . '.pdf');
        } elseif ($type === 'witnessing') {
            $pdf = Pdf::setOptions($options)->loadView('pdf.beras_witnessing', $viewData);
            return $pdf->stream('Witnessing-Beras-' . $safeNomor . '.pdf');
        }

        return abort(404);
    }

    private function getLogoBase64(): string
    {
        $path = public_path('assets/logo-sucofindo.png');

        if (!file_exists($path)) {
            return '';
        }

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }
}
