<?php

namespace App\Services\MesaAyuda;

use App\Models\Estado;
use App\Models\MesaAyudaRequerimiento;
use Illuminate\Support\Str;

class ClasificarRequerimientoMesaAyudaService
{
    public function clasificar(MesaAyudaRequerimiento $requerimiento): array
    {
        $texto = $this->normalizar($this->construirTextoAnalisis($requerimiento));

        $score = 0;
        $evidencias = [];

        $reglas = [
            [
                'patron' => '/certificados?\s+de\s+disponibilidad\s+presupuestaria/i',
                'puntos' => 70,
                'fuente' => 'tipificacion',
                'detalle' => 'Coincide con tipificación Certificados de disponibilidad presupuestaria.',
            ],
            [
                'patron' => '/\bcdp\b/i',
                'puntos' => 35,
                'fuente' => 'observacion',
                'detalle' => 'Contiene sigla CDP.',
            ],
            [
                'patron' => '/disponibilidad\s+presupuestaria/i',
                'puntos' => 35,
                'fuente' => 'texto',
                'detalle' => 'Contiene expresión disponibilidad presupuestaria.',
            ],
            [
                'patron' => '/solicito\s+cdp|solicitud\s+de\s+cdp|emitir\s+cdp|emision\s+de\s+cdp/i',
                'puntos' => 35,
                'fuente' => 'observacion',
                'detalle' => 'La observación solicita emisión de CDP.',
            ],
            [
                'patron' => '/certificado\s+de\s+disponibilidad/i',
                'puntos' => 35,
                'fuente' => 'texto',
                'detalle' => 'Contiene certificado de disponibilidad.',
            ],
            [
                'patron' => '/cotizacion|cotizaciones|orden\s+de\s+compra|\boc\b|presupuesto/i',
                'puntos' => 10,
                'fuente' => 'adjuntos_texto',
                'detalle' => 'Existen antecedentes típicos de solicitud presupuestaria: cotización, OC o presupuesto.',
            ],
        ];

        foreach ($reglas as $regla) {
            if (preg_match($regla['patron'], $texto)) {
                $score += $regla['puntos'];
                $evidencias[] = [
                    'fuente' => $regla['fuente'],
                    'detalle' => $regla['detalle'],
                    'puntos' => $regla['puntos'],
                ];
            }
        }

        $clasificacion = 'otro';
        $requiereCdp = false;
        $confianza = 'baja';
        $requiereRevisionUsuario = false;
        $destinoFlujo = 'bandeja_general';
        $procesarAutomaticamente = false;
        $motivoRouting = 'No corresponde a CDP según reglas actuales.';

        if ($score >= 70) {
            $clasificacion = 'certificado_disponibilidad_presupuestaria';
            $requiereCdp = true;
            $confianza = 'alta';
            $destinoFlujo = 'agente_presupuestario_cdp';
            $procesarAutomaticamente = true;
            $motivoRouting = 'Clasificado como CDP con confianza alta.';
        } elseif ($score >= 35) {
            $clasificacion = 'posible_certificado_disponibilidad_presupuestaria';
            $requiereCdp = false;
            $confianza = 'media';
            $requiereRevisionUsuario = true;
            $destinoFlujo = 'revision_usuario';
            $procesarAutomaticamente = false;
            $motivoRouting = 'Posible CDP, requiere revisión de usuario.';
        }

        $resultado = [
            'clasificacion' => $clasificacion,
            'requiere_cdp' => $requiereCdp,
            'confianza_clasificacion' => $confianza,
            'score_clasificacion' => $score,
            'evidencias_clasificacion' => $evidencias,
            'requiere_revision_usuario' => $requiereRevisionUsuario,
            'destino_flujo' => $destinoFlujo,
            'procesar_automaticamente' => $procesarAutomaticamente,
            'motivo_routing' => $motivoRouting,
        ];

        $this->actualizarRequerimiento($requerimiento, $resultado);

        return $resultado;
    }

    public function clasificarPendientes(?int $limite = null): array
    {
        $query = MesaAyudaRequerimiento::query()
            ->whereNull('clasificacion')
            ->orWhere('clasificacion', '')
            ->orderBy('created_at');

        if ($limite) {
            $query->limit($limite);
        }

        $procesados = 0;
        $errores = [];

        foreach ($query->get() as $requerimiento) {
            try {
                $this->clasificar($requerimiento);
                $procesados++;
            } catch (\Throwable $e) {
                $errores[] = [
                    'id' => $requerimiento->id,
                    'folio' => $requerimiento->folio,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return [
            'procesados' => $procesados,
            'errores' => $errores,
        ];
    }

    private function actualizarRequerimiento(MesaAyudaRequerimiento $requerimiento, array $resultado): void
    {
        $estadoNombre = match ($resultado['clasificacion']) {
            'certificado_disponibilidad_presupuestaria' => 'CLASIFICADO',
            'posible_certificado_disponibilidad_presupuestaria' => 'POSIBLE_CDP',
            default => 'NO_CDP',
        };

        $estadoId = Estado::query()
            ->where('tabla_referencia', 'mesa_ayuda_requerimientos')
            ->where('nombre', $estadoNombre)
            ->value('id');

        $requerimiento->fill([
            'clasificacion' => $resultado['clasificacion'],
            'requiere_cdp' => $resultado['requiere_cdp'],
            'confianza_clasificacion' => $resultado['confianza_clasificacion'],
            'score_clasificacion' => $resultado['score_clasificacion'],
            'evidencias_clasificacion' => $resultado['evidencias_clasificacion'],
            'destino_flujo' => $resultado['destino_flujo'],
            'procesar_automaticamente' => $resultado['procesar_automaticamente'],
            'motivo_routing' => $resultado['motivo_routing'],
        ]);

        if ($estadoId) {
            $requerimiento->estado_id = $estadoId;
        }

        $requerimiento->save();
    }

    private function construirTextoAnalisis(MesaAyudaRequerimiento $requerimiento): string
    {
        $requerimiento->loadMissing(['historial', 'adjuntos']);

        $historial = $requerimiento->historial
            ->map(fn ($item) => trim(($item->accion ?? '') . ' ' . ($item->usuario_externo ?? '') . ' ' . ($item->observacion ?? '')))
            ->implode(' ');

        $adjuntos = $requerimiento->adjuntos
            ->map(fn ($item) => trim(($item->nombre_archivo ?? '') . ' ' . ($item->clasificacion_documento ?? '') . ' ' . Str::limit($item->texto_extraido ?? '', 2000, '')))
            ->implode(' ');

        return implode(' ', array_filter([
            $requerimiento->folio,
            $requerimiento->estado_externo,
            $requerimiento->componente,
            $requerimiento->tipo_requerimiento,
            $requerimiento->tribunal,
            $requerimiento->solicitado_por,
            $requerimiento->solicitado_para,
            $requerimiento->observacion_principal,
            $requerimiento->tipificacion,
            json_encode($requerimiento->head_json ?? [], JSON_UNESCAPED_UNICODE),
            json_encode($requerimiento->body_json ?? [], JSON_UNESCAPED_UNICODE),
            $historial,
            $adjuntos,
        ]));
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower($texto, 'UTF-8');
        $texto = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'u', 'n'], $texto);
        $texto = preg_replace('/\s+/', ' ', $texto) ?? $texto;

        return trim($texto);
    }
}
