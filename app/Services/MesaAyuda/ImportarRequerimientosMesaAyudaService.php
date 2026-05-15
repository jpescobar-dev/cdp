<?php

namespace App\Services\MesaAyuda;

use App\Models\MesaAyudaAdjunto;
use App\Models\MesaAyudaExtraccion;
use App\Models\MesaAyudaHistorial;
use App\Models\MesaAyudaRequerimiento;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ImportarRequerimientosMesaAyudaService
{
    public function __construct(
        private readonly ClasificadorRequerimientoService $clasificador
    ) {
    }

    public function importarDesdeArchivo(string $rutaJson, ?string $ejecutadoPor = null): MesaAyudaExtraccion
    {
        $contenido = file_get_contents($rutaJson);

        if ($contenido === false) {
            throw new \RuntimeException("No se pudo leer el archivo JSON: {$rutaJson}");
        }

        $payload = json_decode($contenido, true);

        if (!is_array($payload)) {
            throw new \InvalidArgumentException('El archivo no contiene un JSON válido.');
        }

        return $this->importar($payload, $rutaJson, $ejecutadoPor);
    }

    public function importar(array $payload, ?string $rutaJson = null, ?string $ejecutadoPor = null): MesaAyudaExtraccion
    {
        return DB::transaction(function () use ($payload, $rutaJson, $ejecutadoPor) {
            $requerimientos = Arr::get($payload, 'requerimientos', []);

            $extraccion = MesaAyudaExtraccion::create([
                'fecha_inicio' => now(),
                'fecha_termino' => now(),
                'estado' => 'importado',
                'ejecutado_por' => $ejecutadoPor,
                'total_detectados' => count($requerimientos),
                'total_importados' => 0,
                'total_errores' => 0,
                'ruta_json' => $rutaJson,
                'metadata' => [
                    'sistema' => Arr::get($payload, 'sistema'),
                    'url_origen' => Arr::get($payload, 'url_origen'),
                    'fecha_ejecucion' => Arr::get($payload, 'fecha_ejecucion'),
                ],
            ]);

            $totalImportados = 0;
            $totalErrores = 0;

            foreach ($requerimientos as $item) {
                try {
                    $this->guardarRequerimiento($extraccion, $item);
                    $totalImportados++;
                } catch (\Throwable $e) {
                    $totalErrores++;
                }
            }

            $extraccion->update([
                'total_importados' => $totalImportados,
                'total_errores' => $totalErrores,
                'estado' => $totalErrores > 0 ? 'importado_con_observaciones' : 'importado',
            ]);

            return $extraccion->fresh(['requerimientos']);
        });
    }

    private function guardarRequerimiento(MesaAyudaExtraccion $extraccion, array $item): MesaAyudaRequerimiento
    {
        $head = Arr::get($item, 'head', []);
        $body = Arr::get($item, 'body', []);
        $clasificacion = Arr::get($item, 'clasificacion') ?: $this->clasificador->clasificar($item);
        $routing = Arr::get($clasificacion, 'routing', Arr::get($item, 'routing', []));

        $folio = (string) Arr::get($head, 'folio', Arr::get($body, 'folio'));

        if ($folio === '') {
            throw new \InvalidArgumentException('El requerimiento no contiene folio.');
        }

        $observacionPrincipal = collect(Arr::get($body, 'historial', []))
            ->pluck('observacion')
            ->filter()
            ->last();

        $requerimiento = MesaAyudaRequerimiento::updateOrCreate(
            ['folio' => $folio],
            [
                'extraccion_id' => $extraccion->id,
                'fecha_hora' => $this->parseFechaHora(Arr::get($head, 'fecha_hora')),
                'estado_externo' => Arr::get($head, 'estado', Arr::get($body, 'estado_actual')),
                'componente' => Arr::get($head, 'componente'),
                'tipo_requerimiento' => Arr::get($head, 'requerimiento'),
                'tribunal' => Arr::get($head, 'tribunal', Arr::get($body, 'datos_solicitante.tribunal')),
                'solicitado_por' => Arr::get($head, 'solicitado_por'),
                'solicitado_para' => Arr::get($head, 'solicitado_para'),
                'tiempo_estimado_solucion' => Arr::get($head, 'tiempo_estimado_solucion', Arr::get($body, 'datos_adicionales.tiempo_estimado_solucion')),
                'observacion_principal' => $observacionPrincipal,
                'tipificacion' => $this->tipificacionComoTexto(Arr::get($body, 'tipificacion')),
                'url_detalle' => Arr::get($item, 'url_detalle'),
                'clasificacion' => Arr::get($clasificacion, 'tipo_requerimiento'),
                'requiere_cdp' => (bool) Arr::get($clasificacion, 'requiere_cdp', false),
                'confianza_clasificacion' => Arr::get($clasificacion, 'confianza'),
                'score_clasificacion' => (int) Arr::get($clasificacion, 'score', 0),
                'evidencias_clasificacion' => Arr::get($clasificacion, 'evidencias', []),
                'destino_flujo' => Arr::get($routing, 'destino'),
                'procesar_automaticamente' => (bool) Arr::get($routing, 'procesar_automaticamente', false),
                'motivo_routing' => Arr::get($routing, 'motivo'),
                'head_json' => $head,
                'body_json' => $body,
                'json_completo' => $item,
                'fecha_captura' => now(),
                'origen' => 'mesa_ayuda',
                'error_captura' => Arr::get($item, 'errores') ? json_encode(Arr::get($item, 'errores')) : null,
            ]
        );

        $requerimiento->historial()->delete();
        foreach (Arr::get($body, 'historial', []) as $historial) {
            MesaAyudaHistorial::create([
                'mesa_ayuda_requerimiento_id' => $requerimiento->id,
                'fecha' => $this->parseFecha(Arr::get($historial, 'fecha')),
                'hora' => Arr::get($historial, 'hora'),
                'estado_externo' => Arr::get($historial, 'estado'),
                'accion' => Arr::get($historial, 'accion'),
                'usuario_externo' => Arr::get($historial, 'usuario'),
                'observacion' => Arr::get($historial, 'observacion'),
                'raw_json' => $historial,
            ]);
        }

        $requerimiento->adjuntos()->delete();
        foreach (Arr::get($body, 'adjuntos', []) as $adjunto) {
            MesaAyudaAdjunto::create([
                'mesa_ayuda_requerimiento_id' => $requerimiento->id,
                'nombre_archivo' => Arr::get($adjunto, 'nombre_archivo'),
                'ruta_local' => Arr::get($adjunto, 'ruta_local'),
                'url_origen' => Arr::get($adjunto, 'url'),
                'tipo_mime' => Arr::get($adjunto, 'tipo_mime'),
                'tamano_bytes' => Arr::get($adjunto, 'tamano_bytes'),
                'hash_archivo' => Arr::get($adjunto, 'hash_archivo'),
                'descargado' => (bool) Arr::get($adjunto, 'descargado', false),
                'texto_extraido' => Arr::get($adjunto, 'texto_extraido'),
                'clasificacion_documento' => Arr::get($adjunto, 'clasificacion_documento'),
                'metadata' => $adjunto,
            ]);
        }

        return $requerimiento;
    }

    private function parseFechaHora(?string $valor): ?Carbon
    {
        if (!$valor) {
            return null;
        }

        foreach (['d/m/Y H:i', 'd-m-Y H:i', 'd/m/Y H:i:s', 'd-m-Y H:i:s'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($valor));
            } catch (\Throwable) {
            }
        }

        return null;
    }

    private function parseFecha(?string $valor): ?Carbon
    {
        if (!$valor) {
            return null;
        }

        foreach (['d/m/Y', 'd-m-Y', 'Y-m-d'] as $format) {
            try {
                return Carbon::createFromFormat($format, trim($valor));
            } catch (\Throwable) {
            }
        }

        return null;
    }

    private function tipificacionComoTexto(mixed $tipificacion): ?string
    {
        if (is_string($tipificacion)) {
            return $tipificacion;
        }

        if (is_array($tipificacion)) {
            return implode(' -> ', array_filter([
                Arr::get($tipificacion, 'zona'),
                Arr::get($tipificacion, 'area'),
                Arr::get($tipificacion, 'materia'),
            ]));
        }

        return null;
    }
}
