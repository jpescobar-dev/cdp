<?php

namespace App\Services\MesaAyuda;

use App\Jobs\MesaAyuda\ClasificarRequerimientoMesaAyudaJob;
use App\Models\MesaAyudaRequerimiento;
use App\Services\Agentes\AgenteEjecucionService;
use Throwable;

class OrquestadorMesaAyudaService
{
    public function __construct(
        private readonly AgenteEjecucionService $ejecuciones,
    ) {}

    public function despacharClasificacion(MesaAyudaRequerimiento $requerimiento, ?int $solicitadoPorUserId = null): void
    {
        ClasificarRequerimientoMesaAyudaJob::dispatch($requerimiento->id, $solicitadoPorUserId);
    }

    public function clasificarRequerimiento(MesaAyudaRequerimiento $requerimiento, ?int $solicitadoPorUserId = null): MesaAyudaRequerimiento
    {
        $ejecucion = $this->ejecuciones->iniciar(
            agenteCodigo: 'agente.clasificador_cdp',
            tipoTarea: 'clasificar_requerimiento_mesa_ayuda',
            input: [
                'folio' => $requerimiento->folio,
                'head' => $requerimiento->head_json,
                'body' => $requerimiento->body_json,
            ],
            mesaAyudaRequerimientoId: $requerimiento->id,
            solicitadoPorUserId: $solicitadoPorUserId
        );

        try {
            /** @var ClasificadorMesaAyudaCdpService $clasificador */
            $clasificador = app(ClasificadorMesaAyudaCdpService::class);
            $resultado = $clasificador->clasificarDesdeModelo($requerimiento);

            $requerimiento->update([
                'clasificacion' => $resultado['tipo_requerimiento'] ?? null,
                'requiere_cdp' => (bool) ($resultado['requiere_cdp'] ?? false),
                'confianza_clasificacion' => $resultado['confianza'] ?? null,
                'score_clasificacion' => (int) ($resultado['score'] ?? 0),
                'evidencias_clasificacion' => $resultado['evidencias'] ?? [],
                'destino_flujo' => $resultado['routing']['destino'] ?? null,
                'procesar_automaticamente' => (bool) ($resultado['routing']['procesar_automaticamente'] ?? false),
                'motivo_routing' => $resultado['routing']['motivo'] ?? null,
            ]);

            $this->ejecuciones->completar(
                ejecucion: $ejecucion,
                output: $resultado,
                resumen: 'Requerimiento clasificado correctamente.'
            );

            return $requerimiento->refresh();
        } catch (Throwable $e) {
            $this->ejecuciones->fallar($ejecucion, $e);
            throw $e;
        }
    }
}
