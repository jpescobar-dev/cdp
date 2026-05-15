<?php

namespace App\Services\MesaAyuda;

use App\Models\MesaAyudaRequerimiento;
use Illuminate\Support\Str;

class ClasificadorCdpService
{
    public function clasificarModelo(MesaAyudaRequerimiento $requerimiento): array
    {
        $textoAdjuntos = $requerimiento->relationLoaded('adjuntos')
            ? $requerimiento->adjuntos->pluck('nombre_archivo')->implode(' ')
            : $requerimiento->adjuntos()->pluck('nombre_archivo')->implode(' ');

        $textoHistorial = $requerimiento->relationLoaded('historial')
            ? $requerimiento->historial->pluck('observacion')->implode(' ')
            : $requerimiento->historial()->pluck('observacion')->implode(' ');

        $texto = collect([
            $requerimiento->componente,
            $requerimiento->tipo_requerimiento,
            $requerimiento->tipificacion,
            $requerimiento->observacion_principal,
            $textoHistorial,
            $textoAdjuntos,
        ])->filter()->implode(' ');

        return $this->clasificarTexto($texto);
    }

    public function clasificarTexto(string $texto): array
    {
        $normalizado = $this->normalizar($texto);

        $score = 0;
        $evidencias = [];

        $reglas = [
            [
                'patron' => '/certificados? de disponibilidad presupuestaria/',
                'puntos' => 70,
                'fuente' => 'tipificacion',
                'descripcion' => 'Coincide con Certificados de disponibilidad presupuestaria.',
            ],
            [
                'patron' => '/\bcdp\b/',
                'puntos' => 30,
                'fuente' => 'observacion',
                'descripcion' => 'Contiene sigla CDP.',
            ],
            [
                'patron' => '/disponibilidad presupuestaria/',
                'puntos' => 30,
                'fuente' => 'texto',
                'descripcion' => 'Contiene disponibilidad presupuestaria.',
            ],
            [
                'patron' => '/solicit(o|a).*cdp|solicitud.*cdp/',
                'puntos' => 30,
                'fuente' => 'observacion',
                'descripcion' => 'La observación solicita CDP.',
            ],
            [
                'patron' => '/cotizacion|cotizaciones|orden de compra|\boc\b/',
                'puntos' => 10,
                'fuente' => 'adjuntos',
                'descripcion' => 'Contiene señales documentales de compra/cotización.',
            ],
        ];

        foreach ($reglas as $regla) {
            if (preg_match($regla['patron'], $normalizado)) {
                $score += $regla['puntos'];
                $evidencias[] = [
                    'fuente' => $regla['fuente'],
                    'descripcion' => $regla['descripcion'],
                    'puntos' => $regla['puntos'],
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
                    'motivo' => 'Posible CDP, requiere revisión humana.',
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
                'motivo' => 'No corresponde a CDP según reglas actuales.',
            ],
        ];
    }

    private function normalizar(string $texto): string
    {
        $texto = Str::lower($texto);
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto) ?: $texto;
        return preg_replace('/\s+/', ' ', $texto) ?? $texto;
    }
}
