<?php

namespace App\Http\Requests\Contractual;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRevisionContractualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'estado_id' => ['required', 'exists:estados,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.max' => 'El título no puede superar los 255 caracteres.',
            'estado_id.required' => 'Debe seleccionar un estado.',
            'estado_id.exists' => 'El estado seleccionado no es válido.',
        ];
    }
}