<?php

namespace App\Http\Requests\Contractual;

use Illuminate\Foundation\Http\FormRequest;

class StoreHallazgoRevisionContractualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'estado_id' => ['nullable', 'exists:estados,id'],
            'titulo' => ['required', 'string', 'max:255'],
            'tipo_hallazgo' => ['nullable', 'string', 'max:50'],
            'tipo_riesgo' => ['nullable', 'string', 'max:100'],
            'nivel_criticidad' => ['nullable', 'string', 'max:50'],
            'hecho_acreditado' => ['nullable', 'string'],
            'observacion' => ['nullable', 'string'],
            'fundamento_documental' => ['nullable', 'string'],
            'consecuencia_posible' => ['nullable', 'string'],
            'recomendacion' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título del hallazgo es obligatorio.',
            'titulo.max' => 'El título no puede superar los 255 caracteres.',
            'estado_id.exists' => 'El estado seleccionado no es válido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $campos = [
            'titulo',
            'tipo_hallazgo',
            'tipo_riesgo',
            'nivel_criticidad',
            'hecho_acreditado',
            'observacion',
            'fundamento_documental',
            'consecuencia_posible',
            'recomendacion',
        ];

        $data = [];

        foreach ($campos as $campo) {
            $data[$campo] = is_string($this->{$campo})
                ? trim($this->{$campo})
                : $this->{$campo};
        }

        $this->merge($data);
    }
}
