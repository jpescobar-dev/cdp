<?php

namespace App\Http\Requests\Presupuesto;

use Illuminate\Foundation\Http\FormRequest;

class CambiarEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'estado_destino_id' => ['required', 'integer', 'exists:estados,id'],
            'comentario' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'estado_destino_id.required' => 'Debes indicar el estado de destino.',
            'estado_destino_id.exists' => 'El estado de destino no es válido.',
        ];
    }
}
