<?php

namespace App\Console\Commands;

use App\Models\MesaAyudaExtraccion;
use App\Services\MesaAyuda\ImportarRequerimientosMesaAyudaService;
use App\Services\MesaAyuda\PlaywrightExtractorService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Throwable;

class MesaAyudaExtraerCommand extends Command
{
    protected $signature = 'mesa-ayuda:extraer {--importar : Importa automáticamente el JSON generado a la base de datos}';

    protected $description = 'Ejecuta el extractor Playwright de Mesa de Ayuda y opcionalmente importa el JSON generado.';

    public function handle(
        PlaywrightExtractorService $extractor,
        ImportarRequerimientosMesaAyudaService $importador
    ): int {
        $extraccion = MesaAyudaExtraccion::create([
            'fecha_inicio' => Carbon::now(),
            'estado' => 'ejecutando',
            'ejecutado_por' => auth()->user()->rut ?? null,
        ]);

        $this->info("Extracción creada: {$extraccion->id}");

        try {
            $jsonPath = $extractor->ejecutar($extraccion);

            $extraccion->update([
                'fecha_termino' => Carbon::now(),
                'estado' => 'extraido',
                'ruta_json' => $jsonPath,
            ]);

            $this->info("JSON generado: {$jsonPath}");

            if ($this->option('importar')) {
                $resultado = $importador->importar($jsonPath, $extraccion);

                $extraccion->update([
                    'estado' => 'importado',
                    'total_detectados' => $resultado['total_detectados'] ?? 0,
                    'total_importados' => $resultado['total_importados'] ?? 0,
                    'total_errores' => $resultado['total_errores'] ?? 0,
                ]);

                $this->info('Importación terminada.');
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            $extraccion->update([
                'fecha_termino' => Carbon::now(),
                'estado' => 'error',
                'mensaje_error' => $e->getMessage(),
            ]);

            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
