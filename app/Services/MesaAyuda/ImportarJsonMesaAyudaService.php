<?php

namespace App\Services\MesaAyuda;

use App\Models\Estado;
use App\Models\MesaAyudaAdjunto;
use App\Models\MesaAyudaExtraccion;
use App\Models\MesaAyudaHistorial;
use App\Models\MesaAyudaRequerimiento;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ImportarJsonMesaAyudaService
{
    public function importar(string $jsonPath, ?string $ejecutadoPor = null): array
    {
        $path = $this->resolverRuta($jsonPath);

        if (! File::exists($path)) {
            throw new RuntimeException("No existe el archivo JSON: {$path}");
        }

        $contenido = File::get($path);
        $data = json_decode($contenido, true);

        if (! is_array($data)) {
            throw new RuntimeException('El archivo no contiene un JSON válido.');
        }

        $requerimientos = Arr::get($data, 'requerimientos', []);

        if (! is_array($requerimientos)) {
            throw new RuntimeException('El JSON no contiene el arreglo requerimientos.');
        }

        return DB::transaction(function () use ($data, $requerimientos, $path, $ejecutadoPor) {
            $extraccion = MesaAyudaExtraccion::create([
                'fecha_inicio' => $this->parseFechaIso(Arr::get($data, 'fecha_ejecucion')) ?? now(),
                'fecha_termino' => now(),
                'estado' => 'importado',
                'ejecutado_por' => $ejecutadoPor,
                'total_detectados' => count($requerimientos),
                'total_importados' => 0,
                'total_errores' => 0,
                'ruta_json' => $this->rutaRelativaStorage($path),
                'metadata' => [
                    'sistema' => Arr::get($data, 'sistema'),
                    'url_origen' => Arr::get($data, 'url_origen'),
                    'archivo_origen' => $path,
                ],
            ]);

            $estadoCapturadoId = $this->estadoId('CAPTURADO');
            $importados = 0;
            $errores = 0;
            $detalles = [];

            foreach ($requerimientos as $item) {
                try {
                    $requerimiento = $this->guardarRequerimiento(
                        extraccion: $extraccion,
                        item: $item,
                        estadoCapturadoId: $estadoCapturadoId,
                        dataCompleta: $data
                    );

                    $this->guardarHistorial($requerimiento, Arr::get($item, 'body.historial', []));
                    $this->guardarAdjuntos($requerimiento, Arr::get($item, 'body.adjuntos', []));

                    $importados++;
                    $detalles[] = [
                        'folio' => $requerimiento->folio,
                        'estado' => 'importado',
                        'id' => $requerimiento->id,
                    ];
                } catch (\Throwable $e) {
                    $errores++;
                    $detalles[] = [
                        'folio' => Arr::get($item, 'head.folio') ?? Arr::get($item, 'body.folio'),
                        'estado' => 'error',
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $extraccion->update([
                'total_importados' => $importados,
                'total_errores' => $errores,
                'estado' => $errores > 0 ? 'importado_con_errores' : 'importado',
                'mensaje_error' => $errores > 0 ? 'Algunos requerimientos no fueron importados. Revisar detalles.' : null,
            ]);

            return [
                'extraccion_id' => $extraccion->id,
                'total_detectados' => count($requerimientos),
                'total_importados' => $importados,
                'total_errores' => $errores,
                'detalles' => $detalles,
            ];
        });
    }

    private function guardarRequerimiento(
        MesaAyudaExtraccion $extraccion,
        array $item,
        ?int $estadoCapturadoId,
        array $dataCompleta
    ): MesaAyudaRequerimiento {
        $head = Arr::get($item, 'head', []);
        $body = Arr::get($item, 'body', []);
        $clasificacion = Arr::get($item, 'clasificacion', []);
        $routing = Arr::get($item, 'routing', []);

        $folio = Arr::get($head, 'folio') ?? Arr::get($body, 'folio');

        if (! $folio) {
            throw new RuntimeException('Requerimiento sin folio.');
        }

        $observacionPrincipal = $this->obtenerObservacionPrincipal(Arr::get($body, 'historial', []));
        $tipificacion = Arr::get($body, 'tipificacion.texto_original')
            ?? collect(Arr::get($body, 'tipificacion', []))->filter()->implode(' -> ');

        $requerimiento = MesaAyudaRequerimiento::withTrashed()->firstOrNew([
            'folio' => (string) $folio,
        ]);

        if ($requerimiento->trashed()) {
            $requerimiento->restore();
        }

        $requerimiento->fill([
            'extraccion_id' => $extraccion->id,
            'fecha_hora' => $this->parseFechaHora(Arr::get($head, 'fecha_hora')),
            'estado_externo' => Arr::get($head, 'estado') ?? Arr::get($body, 'estado_actual'),
            'componente' => Arr::get($head, 'componente'),
            'tipo_requerimiento' => Arr::get($head, 'requerimiento') ?? Arr::get($body, 'tipificacion.materia'),
            'tribunal' => Arr::get($head, 'tribunal') ?? Arr::get($body, 'datos_solicitante.tribunal'),
            'solicitado_por' => Arr::get($head, 'solicitado_por') ?? Arr::get($body, 'datos_solicitante.nombre'),
            'solicitado_para' => Arr::get($head, 'solicitado_para'),
            'tiempo_estimado_solucion' => Arr::get($head, 'tiempo_estimado_solucion') ?? Arr::get($body, 'datos_adicionales.tiempo_estimado_solucion'),
            'observacion_principal' => $observacionPrincipal,
            'tipificacion' => $tipificacion,
            'url_detalle' => Arr::get($item, 'url_detalle') ?? Arr::get($item, 'href'),
            'clasificacion' => Arr::get($clasificacion, 'tipo_requerimiento') ?? Arr::get($clasificacion, 'clasificacion'),
            'requiere_cdp' => (bool) Arr::get($clasificacion, 'requiere_cdp', false),
            'confianza_clasificacion' => Arr::get($clasificacion, 'confianza'),
            'score_clasificacion' => (int) Arr::get($clasificacion, 'score', 0),
            'evidencias_clasificacion' => Arr::get($clasificacion, 'evidencias', []),
            'destino_flujo' => Arr::get($routing, 'destino'),
            'procesar_automaticamente' => (bool) Arr::get($routing, 'procesar_automaticamente', false),
            'motivo_routing' => Arr::get($routing, 'motivo'),
            'estado_id' => $estadoCapturadoId,
            'head_json' => $head,
            'body_json' => $body,
            'json_completo' => $item,
            'fecha_captura' => now(),
            'origen' => Arr::get($item, 'origen', 'mesa_ayuda_playwright'),
            'error_captura' => $this->erroresComoTexto(Arr::get($item, 'errores', [])),
        ]);

        $requerimiento->save();

        return $requerimiento;
    }

    private function guardarHistorial(MesaAyudaRequerimiento $requerimiento, array $historial): void
    {
        MesaAyudaHistorial::where('mesa_ayuda_requerimiento_id', $requerimiento->id)->delete();

        foreach ($historial as $movimiento) {
            MesaAyudaHistorial::create([
                'mesa_ayuda_requerimiento_id' => $requerimiento->id,
                'fecha' => $this->parseFecha(Arr::get($movimiento, 'fecha')),
                'hora' => $this->parseHora(Arr::get($movimiento, 'hora')),
                'estado_externo' => Arr::get($movimiento, 'estado'),
                'accion' => Arr::get($movimiento, 'accion'),
                'usuario_externo' => Arr::get($movimiento, 'usuario'),
                'observacion' => Arr::get($movimiento, 'observacion') ?? Arr::get($movimiento, 'texto_original'),
                'raw_json' => $movimiento,
            ]);
        }
    }

    private function guardarAdjuntos(MesaAyudaRequerimiento $requerimiento, array $adjuntos): void
    {
        foreach ($adjuntos as $adjunto) {
            $nombre = Arr::get($adjunto, 'nombre_archivo');

            if (! $nombre) {
                continue;
            }

            MesaAyudaAdjunto::updateOrCreate(
                [
                    'mesa_ayuda_requerimiento_id' => $requerimiento->id,
                    'nombre_archivo' => $nombre,
                ],
                [
                    'ruta_local' => Arr::get($adjunto, 'ruta_local') ?? '',
                    'url_origen' => Arr::get($adjunto, 'url_origen') ?? Arr::get($adjunto, 'href'),
                    'tipo_mime' => Arr::get($adjunto, 'tipo_mime'),
                    'tamano_bytes' => Arr::get($adjunto, 'tamano_bytes'),
                    'hash_archivo' => Arr::get($adjunto, 'hash_archivo'),
                    'descargado' => (bool) Arr::get($adjunto, 'descargado', false),
                    'texto_extraido' => Arr::get($adjunto, 'texto_extraido'),
                    'clasificacion_documento' => Arr::get($adjunto, 'clasificacion_documento'),
                    'metadata' => $adjunto,
                ]
            );
        }
    }

    private function obtenerObservacionPrincipal(array $historial): ?string
    {
        $creado = collect($historial)->first(function ($movimiento) {
            return str_contains(mb_strtolower((string) Arr::get($movimiento, 'estado')), 'creado')
                || str_contains(mb_strtolower((string) Arr::get($movimiento, 'accion')), 'creado');
        });

        return Arr::get($creado, 'observacion')
            ?? Arr::get(collect($historial)->last(), 'observacion');
    }

    private function estadoId(string $nombre): ?int
    {
        return Estado::query()
            ->where('tabla_referencia', 'mesa_ayuda_requerimientos')
            ->where('nombre', $nombre)
            ->value('id');
    }

    private function resolverRuta(string $jsonPath): string
    {
        if (File::exists($jsonPath)) {
            return $jsonPath;
        }

        $basePath = base_path($jsonPath);
        if (File::exists($basePath)) {
            return $basePath;
        }

        $storagePath = storage_path($jsonPath);
        if (File::exists($storagePath)) {
            return $storagePath;
        }

        return $jsonPath;
    }

    private function rutaRelativaStorage(string $path): string
    {
        $storageApp = storage_path('app') . DIRECTORY_SEPARATOR;

        if (str_starts_with($path, $storageApp)) {
            return str_replace('\\', '/', substr($path, strlen($storageApp)));
        }

        return $path;
    }

    private function parseFechaHora(?string $valor): ?Carbon
    {
        if (! $valor) {
            return null;
        }

        foreach (['d/m/Y H:i:s', 'd/m/Y H:i', 'd-m-Y H:i:s', 'd-m-Y H:i'] as $formato) {
            try {
                return Carbon::createFromFormat($formato, trim($valor));
            } catch (\Throwable) {
                // probar siguiente formato
            }
        }

        try {
            return Carbon::parse($valor);
        } catch (\Throwable) {
            return null;
        }
    }

    private function parseFecha(?string $valor): ?string
    {
        if (! $valor) {
            return null;
        }

        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $formato) {
            try {
                return Carbon::createFromFormat($formato, trim($valor))->format('Y-m-d');
            } catch (\Throwable) {
                // probar siguiente formato
            }
        }

        return null;
    }

    private function parseHora(?string $valor): ?string
    {
        if (! $valor) {
            return null;
        }

        foreach (['H:i:s', 'H:i'] as $formato) {
            try {
                return Carbon::createFromFormat($formato, trim($valor))->format('H:i:s');
            } catch (\Throwable) {
                // probar siguiente formato
            }
        }

        return null;
    }

    private function parseFechaIso(?string $valor): ?Carbon
    {
        if (! $valor) {
            return null;
        }

        try {
            return Carbon::parse($valor);
        } catch (\Throwable) {
            return null;
        }
    }

    private function erroresComoTexto(array $errores): ?string
    {
        if (empty($errores)) {
            return null;
        }

        return json_encode($errores, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
