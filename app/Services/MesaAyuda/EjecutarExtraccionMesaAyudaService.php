<?php

namespace App\Services\MesaAyuda;

use App\Models\MesaAyudaExtraccion;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Symfony\Component\Process\Process;
use Throwable;

class EjecutarExtraccionMesaAyudaService
{
    public function ejecutar(?string $ejecutadoPorRut = null, array $opciones = []): array
    {
        $this->validarModoSeguro();

        $inicio = now();
        $extraccion = $this->crearRegistroExtraccion($ejecutadoPorRut, $inicio, $opciones);

        try {
            $script = $this->resolverScriptExtractor();
            $command = ['node', $script];

            $process = new Process($command, base_path(), $this->resolverEntorno($opciones));
            $process->setTimeout((int) config('mesa_ayuda.timeout_proceso', 600));
            $process->run();

            $stdout = trim($process->getOutput());
            $stderr = trim($process->getErrorOutput());

            if (! $process->isSuccessful()) {
                throw new RuntimeException('Falló la ejecución del extractor Playwright: '.$stderr);
            }

            $rutaJson = $this->resolverUltimoJson();

            if (! $rutaJson) {
                throw new RuntimeException('El extractor terminó, pero no se encontró archivo JSON generado.');
            }

            $resumenImportacion = $this->importarJsonSiExisteServicio($rutaJson);
            $resumenClasificacion = $this->clasificarSiExisteServicio();

            $resultado = [
                'ok' => true,
                'ruta_json' => $rutaJson,
                'stdout' => $stdout,
                'stderr' => $stderr,
                'importacion' => $resumenImportacion,
                'clasificacion' => $resumenClasificacion,
            ];

            $this->actualizarRegistroExtraccion($extraccion, 'completado', $inicio, $rutaJson, $resultado);

            return $resultado;
        } catch (Throwable $e) {
            $this->marcarErrorExtraccion($extraccion, $inicio, $e);
            throw $e;
        }
    }

    protected function validarModoSeguro(): void
    {
        if (! config('mesa_ayuda.solo_lectura', true)) {
            throw new RuntimeException('La extracción debe ejecutarse en modo solo lectura. Revise MESA_AYUDA_SOLO_LECTURA.');
        }

        if (config('mesa_ayuda.permitir_respuesta', false)) {
            throw new RuntimeException('La respuesta automática está habilitada. Para esta fase debe estar desactivada.');
        }
    }

    protected function resolverScriptExtractor(): string
    {
        $script = base_path(config('mesa_ayuda.extractor.script', 'tests-playwright/extraer-json-minimo.cjs'));

        if (! file_exists($script)) {
            throw new RuntimeException("No existe el script extractor: {$script}");
        }

        return $script;
    }

    protected function resolverEntorno(array $opciones = []): array
    {
        $headless = $opciones['headless'] ?? config('mesa_ayuda.headless', true);
        $maxFolios = $opciones['max_folios'] ?? config('mesa_ayuda.max_folios', 0);

        return array_merge($_ENV, $_SERVER, [
            'MESA_AYUDA_URL' => (string) config('mesa_ayuda.url'),
            'MESA_AYUDA_USER' => (string) config('mesa_ayuda.user'),
            'MESA_AYUDA_PASSWORD' => (string) config('mesa_ayuda.password'),
            'MESA_AYUDA_HEADLESS' => $this->boolToString($headless),
            'MESA_AYUDA_MAX_FOLIOS' => (string) $maxFolios,
            'MESA_AYUDA_SOLO_LECTURA' => $this->boolToString(config('mesa_ayuda.solo_lectura', true)),
            'MESA_AYUDA_PERMITIR_RESPUESTA' => $this->boolToString(config('mesa_ayuda.permitir_respuesta', false)),
            'MESA_AYUDA_PERMITIR_DESCARGA_ADJUNTOS' => $this->boolToString(config('mesa_ayuda.permitir_descarga_adjuntos', true)),
        ]);
    }

    protected function boolToString(mixed $value): string
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
    }

    protected function resolverUltimoJson(): ?string
    {
        $directorio = storage_path(config('mesa_ayuda.rutas.pruebas', 'app/mesa-ayuda/pruebas'));
        $archivos = glob($directorio.DIRECTORY_SEPARATOR.'*.json') ?: [];

        if (empty($archivos)) {
            return null;
        }

        usort($archivos, fn ($a, $b) => filemtime($b) <=> filemtime($a));

        return $archivos[0];
    }

    protected function importarJsonSiExisteServicio(string $rutaJson): array
    {
        $serviceClass = \App\Services\MesaAyuda\ImportarJsonMesaAyudaService::class;

        if (! class_exists($serviceClass)) {
            return ['ejecutado' => false, 'motivo' => 'No existe ImportarJsonMesaAyudaService.'];
        }

        try {
            $service = app($serviceClass);

            foreach (['importarDesdeRuta', 'importarArchivo', 'importar'] as $method) {
                if (method_exists($service, $method)) {
                    $resultado = $service->{$method}($rutaJson);

                    return [
                        'ejecutado' => true,
                        'metodo' => $method,
                        'resultado' => $resultado,
                    ];
                }
            }

            if (Artisan::all()['mesa-ayuda:importar-json'] ?? false) {
                Artisan::call('mesa-ayuda:importar-json', ['archivo' => $rutaJson]);

                return [
                    'ejecutado' => true,
                    'metodo' => 'artisan:mesa-ayuda:importar-json',
                    'output' => Artisan::output(),
                ];
            }

            return ['ejecutado' => false, 'motivo' => 'No se encontró método compatible de importación.'];
        } catch (Throwable $e) {
            Log::error('Error importando JSON Mesa de Ayuda', [
                'ruta_json' => $rutaJson,
                'error' => $e->getMessage(),
            ]);

            return ['ejecutado' => false, 'error' => $e->getMessage()];
        }
    }

    protected function clasificarSiExisteServicio(): array
    {
        try {
            if (Artisan::all()['mesa-ayuda:clasificar'] ?? false) {
                Artisan::call('mesa-ayuda:clasificar', ['--pendientes' => true]);

                return [
                    'ejecutado' => true,
                    'metodo' => 'artisan:mesa-ayuda:clasificar --pendientes',
                    'output' => Artisan::output(),
                ];
            }
        } catch (Throwable $e) {
            Log::error('Error clasificando requerimientos Mesa de Ayuda', [
                'error' => $e->getMessage(),
            ]);

            return ['ejecutado' => false, 'error' => $e->getMessage()];
        }

        return ['ejecutado' => false, 'motivo' => 'No existe comando mesa-ayuda:clasificar.'];
    }

    protected function crearRegistroExtraccion(?string $ejecutadoPorRut, $inicio, array $opciones): ?MesaAyudaExtraccion
    {
        if (! class_exists(MesaAyudaExtraccion::class) || ! Schema::hasTable('mesa_ayuda_extracciones')) {
            return null;
        }

        return MesaAyudaExtraccion::create([
            'fecha_inicio' => $inicio,
            'estado' => 'ejecutando',
            'ejecutado_por' => $ejecutadoPorRut ?: null,
            'metadata' => [
                'origen' => 'ui_laravel',
                'opciones' => $opciones,
            ],
        ]);
    }

    protected function actualizarRegistroExtraccion(?MesaAyudaExtraccion $extraccion, string $estado, $inicio, string $rutaJson, array $resultado): void
    {
        if (! $extraccion) {
            return;
        }

        $json = json_decode(file_get_contents($rutaJson), true) ?: [];

        $extraccion->update([
            'fecha_termino' => now(),
            'estado' => $estado,
            'ruta_json' => $rutaJson,
            'total_detectados' => data_get($json, 'total_requerimientos_pendientes', 0),
            'total_importados' => count(data_get($json, 'requerimientos', [])),
            'total_errores' => count(data_get($json, 'errores', [])),
            'metadata' => array_merge($extraccion->metadata ?? [], [
                'duracion_segundos' => now()->diffInSeconds($inicio),
                'resultado' => $resultado,
            ]),
        ]);
    }

    protected function marcarErrorExtraccion(?MesaAyudaExtraccion $extraccion, $inicio, Throwable $e): void
    {
        if (! $extraccion) {
            return;
        }

        $extraccion->update([
            'fecha_termino' => now(),
            'estado' => 'error',
            'mensaje_error' => $e->getMessage(),
            'metadata' => array_merge($extraccion->metadata ?? [], [
                'duracion_segundos' => now()->diffInSeconds($inicio),
                'error_tipo' => get_class($e),
                'stack_trace' => $e->getTraceAsString(),
            ]),
        ]);
    }
}
