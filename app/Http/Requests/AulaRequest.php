<?php
// app/Http/Requests/AulaRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AulaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'codigo' => 'required|string|max:20|unique:aulas,codigo',
            'nombre' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1|max:200',
            'tipo' => ['required', Rule::in(['aula_normal', 'taller', 'auditorio', 'virtual'])],
            'nivel' => ['required', Rule::in(['primaria', 'secundaria', 'academia', 'todos'])],
            'edificio' => 'nullable|string|max:50',
            'piso' => 'nullable|string|max:10',
            'equipamiento' => 'nullable|string',
            'activo' => 'boolean',
            'observaciones' => 'nullable|string|max:255'
        ];

        // Para actualización, hacer campos opcionales y excluir el código actual
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $id = $this->route('id') ?? $this->route('aula');
            $rules['codigo'] = 'sometimes|string|max:20|unique:aulas,codigo,' . $id;
            $rules['capacidad'] = 'sometimes|integer|min:1|max:200';
            $rules['tipo'] = ['sometimes', Rule::in(['aula_normal', 'taller', 'auditorio', 'virtual'])];
            $rules['nivel'] = ['sometimes', Rule::in(['primaria', 'secundaria', 'academia', 'todos'])];
            $rules['activo'] = 'sometimes|boolean';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del aula es obligatorio',
            'codigo.unique' => 'Ya existe un aula con este código',
            'nombre.required' => 'El nombre del aula es obligatorio',
            'capacidad.required' => 'La capacidad del aula es obligatoria',
            'capacidad.min' => 'La capacidad mínima es 1 estudiante',
            'capacidad.max' => 'La capacidad máxima es 200 estudiantes',
            'tipo.required' => 'El tipo de aula es obligatorio',
            'tipo.in' => 'El tipo de aula no es válido',
            'nivel.required' => 'El nivel educativo es obligatorio',
            'nivel.in' => 'El nivel educativo no es válido',
        ];
    }
}
