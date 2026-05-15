<?php

namespace App\Services\Presupuesto\Workflow;

use App\Models\Estado;
use App\Models\Presupuesto\ExpedientePresupuestario;
use App\Models\Presupuesto\ExpedienteTarea;
use Carbon\Carbon;

class TareaWorkflowService
{
    public function cerrarPendientes(ExpedientePresupuestario $expediente): void
    {
        ExpedienteTarea::query()
            ->where('expediente_id', $expediente->id)
            ->where('estado', 'pendiente')
            ->update([
                'estado' => 'resuelta',
                'fecha_cierre' => now(),
                'updated_at' => now(),
            ]);
    }

    public function crearPorEstado(ExpedientePresupuestario $expediente, Estado $estadoDestino, string $usuarioRut): ?ExpedienteTarea
    {
        $titulo = null;
        $descripcion = null;
        $asignadoA = $expediente->responsable_rut ?: $usuarioRut;

        switch ($estadoDestino->nombre) {
            case 'En revisión':
                $titulo = 'Revisar expediente presupuestario';
                $descripcion = 'Revisar antecedentes, validar consistencia y preparar el expediente.';
                break;
            case 'Aprobado':
                $titulo = 'Emitir documento presupuestario';
                $descripcion = 'El expediente fue aprobado y queda listo para emisión.';
                break;
            case 'Emitido':
                return null;
            default:
                return null;
        }

        return ExpedienteTarea::create([
            'expediente_id' => $expediente->id,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'asignado_a' => $asignadoA,
            'creado_por' => $usuarioRut,
            'fecha_vencimiento' => Carbon::now()->addDays(2),
            'estado' => 'pendiente',
        ]);
    }
}
