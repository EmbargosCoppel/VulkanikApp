<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class MecanicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mecanicoId = $this->route('mecanico')?->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $mecanicoId,
            'password' => $this->isMethod('POST')
                ? ['required', 'confirmed', Rules\Password::defaults()]
                : ['nullable', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'email.unique' => 'Ya existe un usuario con ese email',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ];
    }
}