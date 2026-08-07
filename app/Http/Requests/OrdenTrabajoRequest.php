<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrdenTrabajoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'vehiculo_id' => $this->isMethod('post') ? 'required|exists:vehiculos,id' : 'sometimes|exists:vehiculos,id',
            'mecanico_id' => $this->isMethod('post') ? [
                'required',
                'exists:users,id',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'mecanico')),
            ] : [
                'sometimes',
                'exists:users,id',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'mecanico')),
            ],
            'estado' => 'sometimes|in:diagnóstico,esperando_piezas,reparación,finalizado',
            'diagnostico' => 'nullable|string',
            'trabajos_realizados' => 'nullable|string',
            'mano_obra' => 'sometimes|numeric|min:0',
            'observaciones' => 'nullable|string',
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'vehiculo_id.required' => 'Debe seleccionar un vehículo',
            'mecanico_id.required' => 'Debe seleccionar un mecánico',
            'estado.in' => 'El estado no es válido',
            'mano_obra.min' => 'La mano de obra no puede ser negativa',
        ];
    }
}