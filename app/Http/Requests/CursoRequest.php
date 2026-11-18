<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CursoRequest extends FormRequest
{
    use \App\Http\Requests\Concerns\CodigoGeneradoPorServidor;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cursoId = $this->route('id');
        $duraciones = [30, 35, 40, 45, 50, 55, 60];
        $curso = $cursoId ? \App\Models\Curso::find($cursoId) : null;
        if ($curso && in_array($curso->duracion_minutos, [90, 120], true)) {
            $duraciones[] = $curso->duracion_minutos;
        }

        return [
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'horas_semanales' => 'required|integer|min:1|max:30',
            'duracion_minutos' => ['required', 'integer', Rule::in($duraciones)],
            'nivel' => 'required|in:primaria,secundaria,academia,todos',
            'niveles' => ['sometimes', 'nullable', 'array', 'min:1', 'max:3'],
            'niveles.*' => ['required', 'distinct', Rule::in(['primaria', 'secundaria', 'academia'])],
            'tipo' => 'required|in:obligatorio,electivo,taller',
            'color' => 'nullable|string|max:7|regex:/^#[a-fA-F0-9]{6}$/',
            'activo' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'duracion_minutos.in' => 'La duración debe ser 30, 45, 60, 90 o 120 minutos',
            'color.regex' => 'El color debe ser un código hexadecimal válido (ej: #FF0000)'
        ];
    }
}
