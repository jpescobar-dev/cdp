<?php

namespace App\Console\Commands;

use App\Services\MesaAyuda\EjecutarExtraccionMesaAyudaService;
use Illuminate\Console\Command;

class ConectarExtraerMesaAyudaCommand extends Command
{
    protected $signature = 'mesa-ayuda:conectar-extraer {--max-folios=0 : 0 procesa todos; N limita cantidad}';

    protected $description = 'Conecta con Mesa de Ayuda usando Playwright, extrae requerimientos, genera JSON, importa y clasifica si los servicios existen.';

    public function handle(EjecutarExtraccionMesaAyudaService $service): int
    {
        $this->info('Conectando con Mesa de Ayuda y ejecutando extractor Playwright...');

        $resultado = $service->ejecutar(null, [
            'max_folios' => (int) $this->option('max-folios'),
        ]);

        $this->info('Extracción finalizada.');
        $this->line('JSON: '.($resultado['ruta_json'] ?? 'sin ruta'));
        $this->line('Importación: '.json_encode($resultado['importacion'] ?? [], JSON_UNESCAPED_UNICODE));
        $this->line('Clasificación: '.json_encode($resultado['clasificacion'] ?? [], JSON_UNESCAPED_UNICODE));

        return self::SUCCESS;
    }
}
