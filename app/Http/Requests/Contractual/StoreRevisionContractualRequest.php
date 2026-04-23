<?php

namespace App\Http\Requests\Contractual;

use Illuminate\Foundation\Http\FormRequest;

class StoreRevisionContractualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El título es obligatorio.',
            'titulo.string' => 'El título debe ser un texto válido.',
            'titulo.max' => 'El título no puede superar los 255 caracteres.',
            'descripcion.string' => 'La descripción debe ser un texto válido.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'titulo' => is_string($this->titulo) ? trim($this->titulo) : $this->titulo,
            'descripcion' => is_string($this->descripcion) ? trim($this->descripcion) : $this->descripcion,
        ]);
    }
}