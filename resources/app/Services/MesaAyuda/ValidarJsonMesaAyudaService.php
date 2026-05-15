<?php

namespace App\Services\MesaAyuda;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidarJsonMesaAyudaService
{
    /**
     * Valida la estructura mínima del JSON generado por Playwright.
     * No valida reglas presupuestarias; solo estructura, consistencia básica y campos mínimos.
     *
     * @throws ValidationException
     */
    public function validar(array $payload): array
    {
        $validator = Validator::make($payload, [
            'sistema' => ['required', 'string'],
            'url_origen' => ['nullable', 'string'],
            'fecha_ejecucion' => ['required', 'date'],
            'total_requerimientos_pendientes' => ['required', 'integer', 'min:0'],
            'requerimientos' => ['required', 'array'],
            'requerimientos.*.head' => ['required', 'array'],
            'requerimientos.*.head.folio' => ['required', 'string'],
            'requerimientos.*.head.estado' => ['nullable', 'string'],
            'requerimientos.*.head.componente' => ['nullable', 'string'],
            'requerimientos.*.head.requerimiento' => ['nullable', 'string'],
            'requerimientos.*.body' => ['nullable', 'array'],
            'requerimientos.*.body.folio' => ['nullable', 'string'],
            'requerimientos.*.body.historial' => ['nullable', 'array'],
            'requerimientos.*.body.adjuntos' => ['nullable', 'array'],
            'requerimientos.*.clasificacion' => ['nullable', 'array'],
            'requerimientos.*.capturado_correctamente' => ['required', 'boolean'],
            'requerimientos.*.errores' => ['nullable', 'array'],
        ]);

        $validator->after(function ($validator) use ($payload) {
            $folios = [];

            foreach (Arr::get($payload, 'requerimientos', []) as $index => $req) {
                $headFolio = Arr::get($req, 'head.folio');
                $bodyFolio = Arr::get($req, 'body.folio');

                if ($headFolio && isset($folios[$headFolio])) {
                    $validator->errors()->add("requerimientos.$index.head.folio", "Folio duplicado en JSON: {$headFolio}");
                }

                if ($headFolio) {
                    $folios[$headFolio] = true;
                }

                if ($bodyFolio && $headFolio && $bodyFolio !== $headFolio) {
                    $validator->errors()->add("requerimientos.$index.body.folio", "El folio del body no coincide con el folio del head.");
                }

                $capturado = (bool) Arr::get($req, 'capturado_correctamente', false);
                $errores = Arr::get($req, 'errores', []);

                if (!$capturado && empty($errores)) {
                    $validator->errors()->add("requerimientos.$index.errores", 'Si capturado_correctamente es false debe existir al menos un error registrado.');
                }
            }
        });

        return $validator->validate();
    }
}
