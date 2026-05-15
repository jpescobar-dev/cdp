<?php

namespace App\Http\Controllers;

use App\Models\CdpBorrador;
use App\Models\CdpSolicitud;
use App\Models\Estado;
use App\Models\MesaAyudaRequerimiento;
use App\Models\Presupuesto\ExpedientePresupuestario;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Solicitudes CDP (formulario del requirente)
        $solicitudesTotal   = CdpSolicitud::count();
        $solicitudesPdfGenerado = CdpSolicitud::where('estado', 'pdf_generado')->count();

        // Requerimientos Mesa de Ayuda clasificados como CDP
        $requerimientosCdp          = MesaAyudaRequerimiento::where('requiere_cdp', true)->count();
        $requerimientosSinExpediente = MesaAyudaRequerimiento::where('requiere_cdp', true)
            ->whereNull('expediente_presupuestario_id')
            ->count();

        // Borradores CDP
        $borradores = CdpBorrador::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $borradorPendiente = ($borradores['borrador'] ?? 0)
            + ($borradores['borrador_editado'] ?? 0)
            + ($borradores['observado_usuario'] ?? 0)
            + ($borradores['rechazado'] ?? 0);
        $borradorAprobado  = $borradores['aprobado'] ?? 0;

        // Expedientes presupuestarios por estado
        $estadosExp = Estado::where('tabla_referencia', 'expedientes_presupuestarios')
            ->orderBy('orden')
            ->get();

        $expedientesPorEstado = ExpedientePresupuestario::selectRaw('estado_id, count(*) as total')
            ->groupBy('estado_id')
            ->pluck('total', 'estado_id');

        return view('dashboard', compact(
            'solicitudesTotal',
            'solicitudesPdfGenerado',
            'requerimientosCdp',
            'requerimientosSinExpediente',
            'borradorPendiente',
            'borradorAprobado',
            'estadosExp',
            'expedientesPorEstado',
        ));
    }
}
