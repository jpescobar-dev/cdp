<?php

namespace App\Services\MesaAyuda;

use App\Models\CdpBorrador;
use App\Models\MesaAyudaRequerimiento;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GenerarBorradorCdpService
{
    public function generarDesdeRequerimiento(MesaAyudaRequerimiento $requerimiento, array $datosIniciales = []): CdpBorrador
    {
        if (!$requerimiento->requiere_cdp) {
            throw new RuntimeException('El requerimiento no está clasificado como CDP.');
        }

        $existente = CdpBorrador::where('mesa_ayuda_requerimiento_id', $requerimiento->id)->first();
        if ($existente) {
            return $existente;
        }

        return DB::transaction(function () use ($requerimiento, $datosIniciales) {
            $requerimiento->loadMissing(['historial', 'adjuntos', 'expedientePresupuestario']);

            $body = $requerimiento->body_json ?? [];
            $datosSolicitante = Arr::get($body, 'datos_solicitante', []);
            $tipificacion = $requerimiento->tipificacion ?: Arr::get($body, 'tipificacion.materia');
            $observacion = $requerimiento->observacion_principal ?: $requerimiento->historial->pluck('observacion')->filter()->last();

            $defaults = [
                'mesa_ayuda_requerimiento_id'  => $requerimiento->id,
                'expediente_presupuestario_id' => $requerimiento->expediente_presupuestario_id,
                'numero_cdp'                   => null,
                'fecha_emision'                => now()->toDateString(),
                'cf'                           => config('mesa_ayuda.default_cf', '14'),
                'st'                           => null,
                'gasto_operacional'            => true,
                'tipo_gasto'                   => 'GO',
                'nombre_iniciativa'            => $this->limpiarNombreIniciativa($observacion, $tipificacion),
                'codigo_iniciativa'            => null,
                'cuenta_presupuestaria'        => null,
                'denominacion'                 => null,
                'unidad_ejecutora'             => config('mesa_ayuda.default_unidad_ejecutora', 'COYHAIQUE'),
                'numero_ue'                    => config('mesa_ayuda.default_numero_ue', '14'),
                'monto_impto_incluido'         => null,
                'validez'                      => now()->year . '-12-31',
                'caracter_gasto'               => 'TRANSITORIO',
                'medio_solicitud'              => 'Requerimiento',
                'numero_requerimiento'         => $requerimiento->folio,
                'ccosto_requirente'            => null,
                'moneda_compra'                => null,
                'fecha_paridad'                => null,
                'valor_paridad'                => null,
                'total_moneda_compra'          => null,
                'texto_certificacion'          => $this->textoCertificacionBase(),
                'notas'                        => [
                    'El presente certificado es válido hasta el 31/12/' . now()->year . '.',
                    'Si hay montos en distintos subtítulo/ítems/asignación presupuestarios, se deben identificar cada uno de éstos por separado.',
                ],
                'respuesta_mesa_ayuda_borrador' => $this->respuestaMesaAyudaBase($requerimiento->folio),
                'estado'                        => 'borrador',
                'requiere_revision_usuario'     => true,
                'aprobado_por_usuario'          => false,
                'json_generado'                 => [
                    'origen'     => 'generador_conservador_laravel',
                    'folio'      => $requerimiento->folio,
                    'solicitante' => $datosSolicitante,
                    'adjuntos'   => $requerimiento->adjuntos->pluck('nombre_archivo')->values()->all(),
                ],
                'advertencias' => [
                    'Borrador generado desde requerimiento. Confirme la disponibilidad presupuestaria antes de aprobar.',
                ],
            ];

            // datos_faltantes calculado después de tener $defaults completamente construido
            $defaults['datos_faltantes'] = CdpBorrador::calcularDatosFaltantes(array_merge($defaults, $datosIniciales));

            $provistos = collect($datosIniciales)
                ->reject(fn ($v) => $v === null || $v === '')
                ->only([
                    'numero_cdp', 'fecha_emision', 'cf', 'st', 'tipo_gasto',
                    'cuenta_presupuestaria', 'denominacion',
                    'nombre_iniciativa', 'codigo_iniciativa',
                    'unidad_ejecutora', 'numero_ue',
                    'validez', 'caracter_gasto',
                    'monto_impto_incluido', 'moneda_compra',
                    'fecha_paridad', 'valor_paridad', 'total_moneda_compra',
                    'medio_solicitud', 'ccosto_requirente',
                ])
                ->all();

            // Auto-generar número CDP dentro de la transacción para garantizar unicidad
            if (empty($provistos['numero_cdp'])) {
                $provistos['numero_cdp'] = CdpBorrador::siguienteNumeroCdp();
            }

            return CdpBorrador::create(array_merge($defaults, $provistos));
        });
    }

    private function limpiarNombreIniciativa(?string $observacion, ?string $tipificacion): ?string
    {
        $texto = trim((string) ($observacion ?: $tipificacion));
        $texto = preg_replace('/\s+/', ' ', $texto) ?: $texto;

        return mb_substr($texto, 0, 250);
    }

    private function textoCertificacionBase(): string
    {
        return 'De conformidad a la normativa aplicable y de acuerdo al presupuesto aprobado para la institución, se certifica en borrador que la Corporación Administrativa del Poder Judicial cuenta con disponibilidad presupuestaria para el financiamiento de los bienes, servicios y/u obras indicados en la documentación adjunta, sujeto a revisión y confirmación del usuario autorizado.';
    }

    private function respuestaMesaAyudaBase(string $folio): string
    {
        return "Estimado/a, junto con saludar, se prepara Certificado de Disponibilidad Presupuestaria asociado al requerimiento N° {$folio}. El documento se encuentra en revisión interna para validación de datos presupuestarios antes de su emisión.";
    }
}
