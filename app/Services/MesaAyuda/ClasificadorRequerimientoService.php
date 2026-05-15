<?php

namespace App\Services\MesaAyuda;

class ClasificadorRequerimientoService
{
    public function clasificar(array $requerimiento): array
    {
        $texto = $this->normalizarTexto(implode(' ', array_filter([
            data_get($requerimiento, 'head.componente'),
            data_get($requerimiento, 'head.requerimiento'),
            data_get($requerimiento, 'body.tipificacion.zona'),
            data_get($requerimiento, 'body.tipificacion.area'),
            data_get($requerimiento, 'body.tipificacion.materia'),
            collect(data_get($requerimiento, 'body.historial', []))->pluck('observacion')->implode(' '),
            collect(data_get($requerimiento, 'body.adjuntos', []))->pluck('nombre_archivo')->implode(' '),
        ])));

        $score = 0;
        $evidencias = [];

        $reglas = [
            [
                'patron' => '/certificados? de disponibilidad presupuestaria/',
                'puntos' => 60,
                'fuente' => 'tipificacion',
                'detalle' => 'Coincide con Certificados de disponibilidad presupuestaria.',
            ],
            [
                'patron' => '/\bcdp\b/',
                'puntos' => 30,
                'fuente' => 'observacion',
                'detalle' => 'Contiene sigla CDP.',
            ],
            [
                'patron' => '/disponibilidad presupuestaria/',
                'puntos' => 30,
                'fuente' => 'texto',
                'detalle' => 'Contiene disponibilidad presupuestaria.',
            ],
            [
                'patron' => '/solicito\s+cdp|solicitud\s+de\s+cdp|emision\s+de\s+cdp/',
                'puntos' => 25,
                'fuente' => 'observacion',
                'detalle' => 'La observación solicita CDP.',
            ],
            [
                'patron' => '/cotizacion|orden de compra|\boc\b|presupuesto/',
                'puntos' => 10,
                'fuente' => 'adjuntos_texto',
                'detalle' => 'Contiene evidencia secundaria asociada a compra/cotización.',
            ],
        ];

        foreach ($reglas as $regla) {
            if (preg_match($regla['patron'], $texto)) {
                $score += $regla['puntos'];
                $evidencias[] = [
                    'fuente' => $regla['fuente'],
                    'detalle' => $regla['detalle'],
                ];
            }
        }

        if ($score >= 60) {
            return [
                'tipo_requerimiento' => 'certificado_disponibilidad_presupuestaria',
                'requiere_cdp' => true,
                'confianza' => 'alta',
                'score' => $score,
                'evidencias' => $evidencias,
                'requiere_revision_usuario' => false,
                'routing' => [
                    'procesar_automaticamente' => true,
                    'destino' => 'agente_presupuestario_cdp',
                    'motivo' => 'Clasificado como CDP con confianza alta.',
                ],
            ];
        }

        if ($score >= 35) {
            return [
                'tipo_requerimiento' => 'posible_certificado_disponibilidad_presupuestaria',
                'requiere_cdp' => null,
                'confianza' => 'media',
                'score' => $score,
                'evidencias' => $evidencias,
                'requiere_revision_usuario' => true,
                'routing' => [
                    'procesar_automaticamente' => false,
                    'destino' => 'revision_usuario',
                    'motivo' => 'Posible CDP, requiere confirmación del usuario.',
                ],
            ];
        }

        return [
            'tipo_requerimiento' => 'otro',
            'requiere_cdp' => false,
            'confianza' => 'baja',
            'score' => $score,
            'evidencias' => $evidencias,
            'requiere_revision_usuario' => false,
            'routing' => [
                'procesar_automaticamente' => false,
                'destino' => 'bandeja_general',
                'motivo' => 'No corresponde a CDP o no existe evidencia suficiente.',
            ],
        ];
    }

    private function normalizarTexto(string $texto): string
    {
        $texto = mb_strtolower($texto, 'UTF-8');
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto;
        return preg_replace('/\s+/', ' ', $texto) ?? $texto;
    }
}
