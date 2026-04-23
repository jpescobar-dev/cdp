<?php

namespace App\Services\Presupuesto\Workflow;

use App\Events\Presupuesto\ExpedienteEstadoCambiado;
use App\Models\Estado;
use App\Models\Funcionario;
use App\Models\Presupuesto\ExpedienteHistorial;
use App\Models\Presupuesto\ExpedientePresupuestario;
use App\Models\Presupuesto\TransicionEstado;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ExpedienteWorkflowService
{
    public function __construct(
        protected TareaWorkflowService $tareaWorkflowService
    ) {
    }

    public function cambiarEstado(
        ExpedientePresupuestario $expediente,
        int $estadoDestinoId,
        Funcionario $usuarioEjecutor,
        ?string $comentario = null
    ): ExpedientePresupuestario {
        return DB::transaction(function () use ($expediente, $estadoDestinoId, $usuarioEjecutor, $comentario) {
            $expediente->refresh();
            $expediente->load('estado');

            $estadoOrigen = $expediente->estado;
            $estadoDestino = Estado::findOrFail($estadoDestinoId);

            $transicion = $this->obtenerTransicionValida(
                $estadoOrigen->id,
                $estadoDestino->id
            );

            $this->validarComentario($transicion, $comentario);
            $this->validarReglasNegocio($expediente, $estadoDestino);

            $expediente->estado_id = $estadoDestino->id;

            if ($estadoDestino->nombre === 'En revisión' && empty($expediente->fecha_ingreso)) {
                $expediente->fecha_ingreso = now();
            }

            if ($estadoDestino->nombre === 'Aprobado') {
                $expediente->fecha_aprobacion = now();
            }

            if ($estadoDestino->nombre === 'Emitido') {
                $expediente->fecha_emision = now();
            }

            $expediente->save();

            ExpedienteHistorial::create([
                'expediente_id' => $expediente->id,
                'estado_id' => $estadoDestino->id,
                'usuario_rut' => $usuarioEjecutor->rut,
                'comentario' => $comentario,
                'fecha_cambio' => now(),
            ]);

            $this->tareaWorkflowService->cerrarPendientes($expediente);
            $tarea = $this->tareaWorkflowService->crearPorEstado(
                $expediente,
                $estadoDestino,
                $usuarioEjecutor->rut
            );

            event(new ExpedienteEstadoCambiado(
                expediente: $expediente->fresh(['estado', 'responsable', 'solicitante']),
                estadoOrigen: $estadoOrigen,
                estadoDestino: $estadoDestino,
                usuarioEjecutor: $usuarioEjecutor,
                comentario: $comentario,
                tarea: $tarea
            ));

            return $expediente->fresh(['estado', 'responsable', 'solicitante']);
        });
    }

    protected function obtenerTransicionValida(int $estadoOrigenId, int $estadoDestinoId): TransicionEstado
    {
        $transicion = TransicionEstado::query()
            ->where('estado_origen_id', $estadoOrigenId)
            ->where('estado_destino_id', $estadoDestinoId)
            ->first();

        if (!$transicion) {
            throw new RuntimeException('La transición de estado no está permitida.');
        }

        return $transicion;
    }

    protected function validarComentario(TransicionEstado $transicion, ?string $comentario): void
    {
        if ($transicion->requiere_comentario && blank($comentario)) {
            throw new RuntimeException('Esta transición requiere comentario obligatorio.');
        }
    }

    protected function validarReglasNegocio(ExpedientePresupuestario $expediente, Estado $estadoDestino): void
    {
        if ($estadoDestino->nombre === 'En revisión' && blank($expediente->responsable_rut)) {
            throw new RuntimeException('Debes asignar un responsable antes de pasar a revisión.');
        }

        if ($estadoDestino->nombre === 'Aprobado') {
            if (blank($expediente->glosa) || blank($expediente->cuenta_presupuestaria) || $expediente->monto <= 0) {
                throw new RuntimeException('No puedes aprobar un expediente con datos presupuestarios incompletos.');
            }
        }

        if ($estadoDestino->nombre === 'Emitido') {
            if ($expediente->estado?->nombre !== 'Aprobado') {
                throw new RuntimeException('Solo se puede emitir un expediente previamente aprobado.');
            }
        }
    }
}
