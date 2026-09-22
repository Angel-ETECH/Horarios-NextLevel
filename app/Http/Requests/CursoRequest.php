<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CursoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cursoId = $this->route('id');

        return [
            'codigo' => ['required', 'string', 'max:20', Rule::unique('cursos')->ignore($cursoId)],
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'horas_semanales' => 'required|integer|min:1|max:30',
            'duracion_minutos' => 'required|integer|in:30,45,60,90,120',
            'nivel' => 'required|in:primaria,secundaria,academia,todos',
            'tipo' => 'required|in:obligatorio,electivo,taller',
            'color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'activo' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'El código del curso ya está registrado',
            'duracion_minutos.in' => 'La duración debe ser 30, 45, 60, 90 o 120 minutos',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #FF0000)'
        ];
    }
}
