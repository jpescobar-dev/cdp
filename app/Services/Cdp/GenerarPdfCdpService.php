<?php

namespace App\Services\Cdp;

use App\Models\CdpSolicitud;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GenerarPdfCdpService
{
    public function generar(CdpSolicitud $solicitud): string
    {
        $pdf = Pdf::loadView('cdp.solicitudes.pdf', compact('solicitud'))
            ->setPaper('letter', 'portrait');

        $ruta = "cdp/solicitudes/{$solicitud->id}/{$solicitud->nombreCdp()}.pdf";

        Storage::put($ruta, $pdf->output());

        return $ruta;
    }
}
