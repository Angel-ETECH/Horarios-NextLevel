<?php
// app/Http/Requests/AsignacionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AsignacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'profesor_id' => 'required|exists:profesores,id',
            'curso_id' => 'required|exists:cursos,id',
            'grado_id' => 'required|exists:grados,id',
            'horas_asignadas' => 'required|integer|min:1|max:40',
            'rol' => ['required', Rule::in(['titular', 'asistente', 'suplente'])],
            'activo' => 'boolean',
            'observaciones' => 'nullable|string|max:255'
        ];

        // Para actualización, hacer campos opcionales
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules = [
                'profesor_id' => 'sometimes|exists:profesores,id',
                'curso_id' => 'sometimes|exists:cursos,id',
                'grado_id' => 'sometimes|exists:grados,id',
                'horas_asignadas' => 'sometimes|integer|min:1|max:40',
                'rol' => ['sometimes', Rule::in(['titular', 'asistente', 'suplente'])],
                'activo' => 'sometimes|boolean',
                'observaciones' => 'nullable|string|max:255'
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'profesor_id.required' => 'El profesor es obligatorio',
            'profesor_id.exists' => 'El profesor seleccionado no existe',
            'curso_id.required' => 'El curso es obligatorio',
            'curso_id.exists' => 'El curso seleccionado no existe',
            'grado_id.required' => 'El grado es obligatorio',
            'grado_id.exists' => 'El grado seleccionado no existe',
            'horas_asignadas.required' => 'Las horas asignadas son obligatorias',
            'horas_asignadas.min' => 'Las horas asignadas deben ser al menos 1',
            'horas_asignadas.max' => 'Las horas asignadas no pueden exceder 40',
            'rol.required' => 'El rol del profesor es obligatorio',
            'rol.in' => 'El rol debe ser titular, asistente o suplente',
            'activo.boolean' => 'El campo activo debe ser verdadero o falso',
        ];
    }

    /**
     * Preparar los datos para la validación
     */
    protected function prepareForValidation(): void
    {
        // Si no se envía activo, poner true por defecto
        if ($this->has('activo') === false) {
            $this->merge(['activo' => true]);
        }
    }
}
