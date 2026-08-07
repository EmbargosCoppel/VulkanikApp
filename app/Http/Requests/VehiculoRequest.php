<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vehiculoId = $this->route('vehiculo')?->id;

        return [
            'cliente_id' => 'required|exists:clientes,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'placa' => 'required|string|unique:vehiculos,placa,' . $vehiculoId,
            'color' => 'nullable|string|max:50',
            'vin' => 'nullable|string|max:17',
            'notas' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debe seleccionar un cliente',
            'marca.required' => 'La marca es obligatoria',
            'modelo.required' => 'El modelo es obligatorio',
            'anio.required' => 'El año es obligatorio',
            'anio.min' => 'El año no puede ser menor a 1900',
            'placa.required' => 'La placa es obligatoria',
            'placa.unique' => 'Ya existe un vehículo con esa placa',
        ];
    }
}