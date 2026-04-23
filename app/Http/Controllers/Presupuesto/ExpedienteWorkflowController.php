<?php

namespace App\Http\Controllers\Presupuesto;

use App\Http\Controllers\Controller;
use App\Http\Requests\Presupuesto\CambiarEstadoRequest;
use App\Models\Funcionario;
use App\Models\Presupuesto\ExpedientePresupuestario;
use App\Services\Presupuesto\Workflow\ExpedienteWorkflowService;
use Illuminate\Http\RedirectResponse;
use RuntimeException;

class ExpedienteWorkflowController extends Controller
{
    public function __construct(
        protected ExpedienteWorkflowService $workflowService
    ) {
    }

    public function update(CambiarEstadoRequest $request, ExpedientePresupuestario $expediente): RedirectResponse
    {
        $usuario = Funcionario::query()->findOrFail($request->user()->rut);

        try {
            $this->workflowService->cambiarEstado(
                expediente: $expediente,
                estadoDestinoId: (int) $request->estado_destino_id,
                usuarioEjecutor: $usuario,
                comentario: $request->comentario
            );

            return redirect()
                ->route('presupuesto.expedientes.show', $expediente)
                ->with('success', 'El estado del expediente fue actualizado correctamente.');
        } catch (RuntimeException $e) {
            return redirect()
                ->route('presupuesto.expedientes.show', $expediente)
                ->with('error', $e->getMessage());
        }
    }
}
