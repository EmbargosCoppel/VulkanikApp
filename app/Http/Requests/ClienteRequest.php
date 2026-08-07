<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
            'rfc' => 'nullable|string|max:13',
            'es_empresa' => 'sometimes|boolean',
            'nombre_empresa' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'email.email' => 'El email debe ser válido',
            'rfc.max' => 'El RFC no puede exceder 13 caracteres',
        ];
    }
}