<?php

namespace App\Services\MesaAyuda;

use App\Models\MesaAyudaAdjunto;
use App\Models\MesaAyudaExtraccion;
use App\Models\MesaAyudaHistorial;
use App\Models\MesaAyudaRequerimiento;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportarRequerimientosMesaAyudaService
{
    public function __construct(
        protected ValidarJsonMesaAyudaService $validador
    ) {}

    public function importarDesdeArchivo(string $rutaJson, ?int $extraccionId = null): MesaAyudaExtraccion
    {
        $path = str_starts_with($rutaJson, DIRECTORY_SEPARATOR) || preg_match('/^[A-Z]:\\\\/i', $rutaJson)
            ? $rutaJson
            : Storage::path($rutaJson);

        $payload = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $this->validador->validar($payload);

        return DB::transaction(function () use ($payload, $rutaJson, $extraccionId) {
            $extraccion = $extraccionId
                ? MesaAyudaExtraccion::findOrFail($extraccionId)
                : MesaAyudaExtraccion::create([
                    'fecha_inicio' => now(),
                    'estado' => 'importando',
                    'ruta_json' => $rutaJson,
                    'metadata' => [
                        'sistema' => Arr::get($payload, 'sistema'),
                        'url_origen' => Arr::get($payload, 'url_origen'),
                    ],
                ]);

            $importados = 0;
            $errores = 0;

            foreach (Arr::get($payload, 'requerimientos', []) as $reqJson) {
                $folio = trim((string) Arr::get($reqJson, 'head.folio'));

                if ($folio === '') {
                    $errores++;
                    continue;
                }

                $head = Arr::get($reqJson, 'head', []);
                $body = Arr::get($reqJson, 'body', []);
                $clasificacion = Arr::get($reqJson, 'clasificacion', []);
                $routing = Arr::get($reqJson, 'routing', []);

                $requerimiento = MesaAyudaRequerimiento::updateOrCreate(
                    ['folio' => $folio],
                    [
                        'extraccion_id' => $extraccion->id,
                        'fecha_hora' => $this->parseFecha(Arr::get($head, 'fecha_hora')),
                        'estado_externo' => Arr::get($head, 'estado'),
                        'componente' => Arr::get($head, 'componente'),
                        'tipo_requerimiento' => Arr::get($head, 'requerimiento'),
                        'tribunal' => Arr::get($head, 'tribunal'),
                        'solicitado_por' => Arr::get($head, 'solicitado_por'),
                        'solicitado_para' => Arr::get($head, 'solicitado_para'),
                        'tiempo_estimado_solucion' => Arr::get($head, 'tiempo_estimado_solucion'),
                        'observacion_principal' => $this->resolverObservacionPrincipal($body),
                        'tipificacion' => Arr::get($body, 'tipificacion.materia') ?: Arr::get($body, 'tipificacion'),
                        'url_detalle' => Arr::get($reqJson, 'url_detalle'),
                        'clasificacion' => Arr::get($clasificacion, 'tipo_requerimiento'),
                        'requiere_cdp' => (bool) Arr::get($clasificacion, 'requiere_cdp', false),
                        'confianza_clasificacion' => Arr::get($clasificacion, 'confianza'),
                        'score_clasificacion' => (int) Arr::get($clasificacion, 'score', 0),
                        'evidencias_clasificacion' => Arr::get($clasificacion, 'evidencias'),
                        'destino_flujo' => Arr::get($routing, 'destino'),
                        'procesar_automaticamente' => (bool) Arr::get($routing, 'procesar_automaticamente', false),
                        'motivo_routing' => Arr::get($routing, 'motivo'),
                        'head_json' => $head,
                        'body_json' => $body,
                        'json_completo' => $reqJson,
                        'fecha_captura' => now(),
                        'origen' => Arr::get($reqJson, 'origen', 'mesa_ayuda_playwright'),
                        'error_captura' => implode("\n", Arr::get($reqJson, 'errores', [])),
                    ]
                );

                $this->sincronizarHistorial($requerimiento, Arr::get($body, 'historial', []));
                $this->sincronizarAdjuntos($requerimiento, Arr::get($body, 'adjuntos', []));
                $importados++;
            }

            $extraccion->update([
                'fecha_termino' => now(),
                'estado' => $errores > 0 ? 'importado_con_observaciones' : 'importado',
                'total_detectados' => (int) Arr::get($payload, 'total_requerimientos_pendientes', count(Arr::get($payload, 'requerimientos', []))),
                'total_importados' => $importados,
                'total_errores' => $errores,
            ]);

            return $extraccion;
        });
    }

    protected function sincronizarHistorial(MesaAyudaRequerimiento $requerimiento, array $historial): void
    {
        $requerimiento->historial()->delete();

        foreach ($historial as $item) {
            MesaAyudaHistorial::create([
                'mesa_ayuda_requerimiento_id' => $requerimiento->id,
                'fecha' => $this->parseFechaSolo(Arr::get($item, 'fecha')),
                'hora' => Arr::get($item, 'hora'),
                'estado_externo' => Arr::get($item, 'estado'),
                'accion' => Arr::get($item, 'accion'),
                'usuario_externo' => Arr::get($item, 'usuario'),
                'observacion' => Arr::get($item, 'observacion'),
                'raw_json' => $item,
            ]);
        }
    }

    protected function sincronizarAdjuntos(MesaAyudaRequerimiento $requerimiento, array $adjuntos): void
    {
        $requerimiento->adjuntos()->delete();

        foreach ($adjuntos as $adjunto) {
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
    }

    protected function resolverObservacionPrincipal(array $body): ?string
    {
        $historial = Arr::get($body, 'historial', []);
        foreach (array_reverse($historial) as $item) {
            if (Arr::get($item, 'estado') === 'Creado' && Arr::get($item, 'observacion')) {
                return Arr::get($item, 'observacion');
            }
        }
        return Arr::get($body, 'observacion') ?: Arr::get($body, 'detalle');
    }

    protected function parseFecha(?string $value): ?Carbon
    {
        if (!$value) return null;
        foreach (['d/m/Y H:i', 'd-m-Y H:i:s', 'd-m-Y H:i', 'Y-m-d H:i:s'] as $format) {
            try { return Carbon::createFromFormat($format, trim($value)); } catch (\Throwable) {}
        }
        try { return Carbon::parse($value); } catch (\Throwable) { return null; }
    }

    protected function parseFechaSolo(?string $value): ?string
    {
        if (!$value) return null;
        foreach (['d-m-Y', 'd/m/Y', 'Y-m-d'] as $format) {
            try { return Carbon::createFromFormat($format, trim($value))->toDateString(); } catch (\Throwable) {}
        }
        try { return Carbon::parse($value)->toDateString(); } catch (\Throwable) { return null; }
    }
}
