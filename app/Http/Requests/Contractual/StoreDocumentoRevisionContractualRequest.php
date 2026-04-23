<?php

namespace App\Http\Requests\Contractual;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoRevisionContractualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'archivo' => ['required', 'file', 'max:20480'],
            'tipo_documento' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'archivo.required' => 'Debe seleccionar un archivo.',
            'archivo.file' => 'El archivo cargado no es válido.',
            'archivo.max' => 'El archivo no puede superar los 20 MB.',
            'tipo_documento.string' => 'El tipo de documento debe ser un texto válido.',
            'tipo_documento.max' => 'El tipo de documento no puede superar los 100 caracteres.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tipo_documento' => is_string($this->tipo_documento)
                ? trim($this->tipo_documento)
                : $this->tipo_documento,
        ]);
    }
}