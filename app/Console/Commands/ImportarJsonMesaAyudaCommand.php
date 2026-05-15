<?php

namespace App\Console\Commands;

use App\Services\MesaAyuda\ImportarJsonMesaAyudaService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ImportarJsonMesaAyudaCommand extends Command
{
    protected $signature = 'mesa-ayuda:importar-json
        {path? : Ruta del archivo JSON generado por Playwright}
        {--latest : Importa el último JSON desde storage/app/mesa-ayuda/pruebas}
        {--usuario= : RUT del usuario que ejecuta/importa}';

    protected $description = 'Importa un JSON de Mesa de Ayuda hacia las tablas mesa_ayuda_*.';

    public function handle(ImportarJsonMesaAyudaService $service): int
    {
        try {
            $path = $this->argument('path');

            if ($this->option('latest')) {
                $path = $this->buscarUltimoJson();
            }

            if (! $path) {
                $this->error('Debe indicar una ruta o usar --latest.');
                return self::FAILURE;
            }

            $this->info("Importando JSON: {$path}");

            $resultado = $service->importar(
                jsonPath: $path,
                ejecutadoPor: $this->option('usuario')
            );

            $this->info('Importación finalizada.');
            $this->line('Extracción ID: ' . $resultado['extraccion_id']);
            $this->line('Detectados: ' . $resultado['total_detectados']);
            $this->line('Importados: ' . $resultado['total_importados']);
            $this->line('Errores: ' . $resultado['total_errores']);

            $this->table(
                ['Folio', 'Estado', 'ID/Error'],
                collect($resultado['detalles'])->map(function ($detalle) {
                    return [
                        $detalle['folio'] ?? '-',
                        $detalle['estado'] ?? '-',
                        $detalle['id'] ?? $detalle['error'] ?? '-',
                    ];
                })->all()
            );

            return ($resultado['total_errores'] ?? 0) > 0
                ? self::FAILURE
                : self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }

    private function buscarUltimoJson(): string
    {
        $directorio = storage_path('app/mesa-ayuda/pruebas');

        if (! File::isDirectory($directorio)) {
            throw new RuntimeException("No existe el directorio: {$directorio}");
        }

        $archivos = collect(File::files($directorio))
            ->filter(fn ($file) => $file->getExtension() === 'json')
            ->sortByDesc(fn ($file) => $file->getMTime())
            ->values();

        if ($archivos->isEmpty()) {
            throw new RuntimeException("No hay archivos JSON en: {$directorio}");
        }

        return $archivos->first()->getPathname();
    }
}
