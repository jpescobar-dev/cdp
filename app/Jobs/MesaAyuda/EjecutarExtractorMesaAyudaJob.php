<?php

namespace App\Jobs\MesaAyuda;

use App\Models\MesaAyudaExtraccion;
use App\Services\MesaAyuda\ImportarRequerimientosMesaAyudaService;
use App\Services\MesaAyuda\PlaywrightExtractorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Throwable;

class EjecutarExtractorMesaAyudaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 900;

    public function __construct(
        public int $extraccionId,
        public bool $importar = true
    ) {}

    public function handle(
        PlaywrightExtractorService $extractor,
        ImportarRequerimientosMesaAyudaService $importador
    ): void {
        $extraccion = MesaAyudaExtraccion::findOrFail($this->extraccionId);

        $extraccion->update([
            'fecha_inicio' => $extraccion->fecha_inicio ?? Carbon::now(),
            'estado' => 'ejecutando',
        ]);

        try {
            $jsonPath = $extractor->ejecutar($extraccion);

            $extraccion->update([
                'fecha_termino' => Carbon::now(),
                'estado' => 'extraido',
                'ruta_json' => $jsonPath,
            ]);

            if ($this->importar) {
                $resultado = $importador->importar($jsonPath, $extraccion);

                $extraccion->update([
                    'estado' => 'importado',
                    'total_detectados' => $resultado['total_detectados'] ?? 0,
                    'total_importados' => $resultado['total_importados'] ?? 0,
                    'total_errores' => $resultado['total_errores'] ?? 0,
                ]);
            }
        } catch (Throwable $e) {
            $extraccion->update([
                'fecha_termino' => Carbon::now(),
                'estado' => 'error',
                'mensaje_error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
