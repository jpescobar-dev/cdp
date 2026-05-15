<?php

namespace App\Http\Requests\Cdp;

use Illuminate\Foundation\Http\FormRequest;

class StoreCdpSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_requirente' => ['required', 'string', 'max:255'],
            'rut_requirente'    => ['required', 'string', 'max:12'],
            'unidad_requirente' => ['required', 'string', 'max:255'],
            'ccosto'            => ['nullable', 'string', 'max:10'],
            'requerimiento'     => ['nullable', 'string', 'max:255'],
            'glosa'             => ['required', 'string', 'max:2000'],
            'proveedor'         => ['required', 'string', 'max:255'],
            'monto_estimado'    => ['required', 'numeric', 'min:1'],
            'moneda'            => ['required', 'in:CLP,UF'],
            'tipo_gasto1'       => ['nullable', 'in:GO,INI'],
            'tipo_gasto2'       => ['nullable', 'in:TRANSITORIO,PERMANENTE'],
            'proyecto_id'       => ['nullable', 'integer', 'exists:proyectos,id'],
            'documentos'        => ['nullable', 'array', 'max:5'],
            'documentos.*'      => ['file', 'max:10240', 'mimes:pdf,jpg,jpeg,png,docx,xlsx'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre_requirente' => 'nombre del requirente',
            'rut_requirente'    => 'RUT del requirente',
            'unidad_requirente' => 'unidad',
            'ccosto'            => 'centro de costo',
            'requerimiento'     => 'número de requerimiento',
            'glosa'             => 'descripción del gasto',
            'proveedor'         => 'proveedor',
            'monto_estimado'    => 'monto estimado',
            'moneda'            => 'moneda',
            'tipo_gasto1'       => 'tipo de gasto',
            'tipo_gasto2'       => 'clasificación del gasto',
            'proyecto_id'       => 'proyecto',
            'documentos'        => 'documentos',
            'documentos.*'      => 'archivo adjunto',
        ];
    }

    public function messages(): array
    {
        return [
            'documentos.*.mimes' => 'Solo se aceptan archivos PDF, JPG, PNG, DOCX o XLSX.',
            'documentos.*.max'   => 'Cada archivo no puede superar 10 MB.',
            'documentos.max'     => 'Se pueden adjuntar como máximo 5 documentos.',
        ];
    }
}
