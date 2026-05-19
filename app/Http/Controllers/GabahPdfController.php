<?php

namespace App\Http\Controllers;

use App\Models\MasHpkkGabah;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Traits\PdfLogoHelper;

class GabahPdfController extends Controller
{
    use PdfLogoHelper;
    public function print($id, $type)
    {
        ini_set('memory_limit', '256M');
        ini_set('max_execution_time', 60);

        $data = MasHpkkGabah::findOrFail($id);

        $viewData = [
            'd'    => $data,
            'logo' => $this->logoBase64('logo-sucofindo.png'),
        ];

        $safeNomor = str_replace('/', '-', $data->nomor_hpkk_gabah);

        $options = [
            'isHtml5ParserEnabled' => false,
            'isRemoteEnabled'      => false,
            'defaultFont'          => 'sans-serif',
            'dpi'                  => 96,
        ];

        if ($type === 'hpk') {
            $pdf = Pdf::setOptions($options)->loadView('pdf.gabah_hpk', $viewData);
            return $pdf->stream('HPK-' . $safeNomor . '.pdf');
        } elseif ($type === 'lhpk') {
            $pdf = Pdf::setOptions($options)->loadView('pdf.gabah_lhpk', $viewData);
            return $pdf->stream('LHPK-' . $safeNomor . '.pdf');
        } elseif ($type === 'witnessing') {
            $pdf = Pdf::setOptions($options)->loadView('pdf.gabah_witnessing', $viewData);
            return $pdf->stream('Witnessing-' . $safeNomor . '.pdf');
        }

        return abort(404);
    }

}
