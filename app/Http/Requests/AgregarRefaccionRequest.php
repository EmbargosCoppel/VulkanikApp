<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgregarRefaccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refaccion_id' => 'required|exists:refacciones,id',
            'cantidad' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'refaccion_id.required' => 'Debe seleccionar una refacción',
            'refaccion_id.exists' => 'La refacción seleccionada no existe',
            'cantidad.required' => 'Debe indicar la cantidad',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
        ];
    }
}
