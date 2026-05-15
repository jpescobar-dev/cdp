<?php

namespace App\Http\Controllers\Presupuesto;

use App\Http\Controllers\Controller;
use App\Http\Requests\Presupuesto\CambiarEstadoRequest;
use App\Models\Funcionario;
use App\Models\Presupuesto\ExpedientePresupuestario;
use App\Services\Presupuesto\Workflow\ExpedienteWorkflowService;
use Exception;

class ExpedienteWorkflowController extends Controller
{
    public function cambiarEstado(
        CambiarEstadoRequest $request,
        ExpedientePresupuestario $expediente,
        ExpedienteWorkflowService $workflowService
    ) {
        try {
            $funcionario = Funcionario::where('email', auth()->user()->email)->firstOrFail();

            $workflowService->cambiarEstado(
                expediente: $expediente,
                estadoDestinoId: (int) $request->estado_destino_id,
                usuarioEjecutor: $funcionario,
                comentario: $request->comentario
            );

            return back()->with('success', 'Estado actualizado correctamente.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
