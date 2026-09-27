<?php
// app/Http/Requests/ResetPasswordRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(6)->letters()->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'El token es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.exists' => 'No existe una cuenta con este email',
            'password.required' => 'La contraseña es obligatoria',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres',
        ];
    }
}
