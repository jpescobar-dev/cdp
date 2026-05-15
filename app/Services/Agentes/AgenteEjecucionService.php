<?php

namespace App\Services\Agentes;

use App\Models\AgenteEjecucion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AgenteEjecucionService
{
    public function iniciar(
        string $agenteCodigo,
        string $tipoTarea,
        array $input = [],
        ?int $mesaAyudaRequerimientoId = null,
        ?int $expedientePresupuestarioId = null,
        ?int $cdpBorradorId = null,
        ?int $solicitadoPorUserId = null,
        ?array $metadata = null
    ): AgenteEjecucion {
        $agenteUser = $this->resolverUsuarioAgente($agenteCodigo);

        return AgenteEjecucion::create([
            'uuid' => (string) Str::uuid(),
            'agente_codigo' => $agenteCodigo,
            'agente_nombre' => $this->nombreLegible($agenteCodigo),
            'tipo_tarea' => $tipoTarea,
            'estado' => 'ejecutando',
            'solicitado_por_user_id' => $solicitadoPorUserId ?? Auth::id(),
            'agente_user_id' => $agenteUser?->id,
            'mesa_ayuda_requerimiento_id' => $mesaAyudaRequerimientoId,
            'expediente_presupuestario_id' => $expedientePresupuestarioId,
            'cdp_borrador_id' => $cdpBorradorId,
            'input_json' => $input,
            'fecha_inicio' => now(),
            'metadata' => $metadata,
        ]);
    }

    public function completar(AgenteEjecucion $ejecucion, array $output = [], ?string $resumen = null): AgenteEjecucion
    {
        $fechaTermino = now();

        $ejecucion->update([
            'estado' => 'completado',
            'output_json' => $output,
            'resumen' => $resumen,
            'fecha_termino' => $fechaTermino,
            'duracion_ms' => $this->duracionMs($ejecucion->fecha_inicio, $fechaTermino),
        ]);

        return $ejecucion->refresh();
    }

    public function fallar(AgenteEjecucion $ejecucion, Throwable $exception, ?array $output = null): AgenteEjecucion
    {
        $fechaTermino = now();

        $ejecucion->update([
            'estado' => 'error',
            'output_json' => $output,
            'error_mensaje' => $exception->getMessage(),
            'error_tipo' => class_basename($exception),
            'stack_trace' => $exception->getTraceAsString(),
            'fecha_termino' => $fechaTermino,
            'duracion_ms' => $this->duracionMs($ejecucion->fecha_inicio, $fechaTermino),
        ]);

        Log::error('Error en ejecución de agente', [
            'agente_codigo' => $ejecucion->agente_codigo,
            'tipo_tarea' => $ejecucion->tipo_tarea,
            'uuid' => $ejecucion->uuid,
            'error' => $exception->getMessage(),
        ]);

        return $ejecucion->refresh();
    }

    public function omitir(AgenteEjecucion $ejecucion, string $motivo, array $output = []): AgenteEjecucion
    {
        $fechaTermino = now();

        $ejecucion->update([
            'estado' => 'omitido',
            'output_json' => $output,
            'resumen' => $motivo,
            'fecha_termino' => $fechaTermino,
            'duracion_ms' => $this->duracionMs($ejecucion->fecha_inicio, $fechaTermino),
        ]);

        return $ejecucion->refresh();
    }

    private function resolverUsuarioAgente(string $agenteCodigo): ?User
    {
        if (! class_exists(User::class)) {
            return null;
        }

        return User::query()
            ->where('codigo_agente', $agenteCodigo)
            ->orWhere('email', $agenteCodigo . '@local.agent')
            ->first();
    }

    private function nombreLegible(string $agenteCodigo): string
    {
        return str($agenteCodigo)
            ->replace('agente.', '')
            ->replace('_', ' ')
            ->replace('.', ' ')
            ->title()
            ->toString();
    }

    private function duracionMs($inicio, $termino): ?int
    {
        if (! $inicio || ! $termino) {
            return null;
        }

        return abs($termino->diffInMilliseconds($inicio));
    }
}
