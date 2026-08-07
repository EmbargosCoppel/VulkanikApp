<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RefaccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $refaccionId = $this->route('refaccion')?->id;

        return [
            'nombre' => 'required|string|max:255',
            'sku' => 'required|string|unique:refacciones,sku,' . $refaccionId,
            'descripcion' => 'nullable|string',
            'costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'proveedor' => 'nullable|string|max:255',
            'activo' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'sku.required' => 'El SKU es obligatorio',
            'sku.unique' => 'Ya existe una refacción con ese SKU',
            'costo.required' => 'El costo es obligatorio',
            'costo.min' => 'El costo no puede ser negativo',
            'precio_venta.required' => 'El precio de venta es obligatorio',
            'precio_venta.min' => 'El precio de venta no puede ser negativo',
            'stock_actual.required' => 'El stock actual es obligatorio',
            'stock_actual.min' => 'El stock no puede ser negativo',
            'stock_minimo.required' => 'El stock mínimo es obligatorio',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo',
        ];
    }
}