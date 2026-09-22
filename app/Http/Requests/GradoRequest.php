<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon;

class GradoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $gradoId = $this->route('id');
        $currentYear = Carbon::now()->year;

        return [
            'codigo' => ['required', 'string', 'max:20', Rule::unique('grados')->ignore($gradoId)],
            'nivel' => 'required|in:primaria,secundaria,academia',
            'grado' => 'required|string|max:20',
            'seccion' => 'required|string|max:5',
            'capacidad_maxima' => 'required|integer|min:1|max:30',
            'numero_estudiantes' => 'required|integer|min:0|max:30',
            'año_academico' => [
                'required',
                'integer',
                'min:' . ($currentYear - 1), // Permite año anterior
                'max:' . ($currentYear + 5),  // Permite hasta 5 años futuros
            ],
            'turno' => 'required|in:mañana,tarde,noche,completo',
            'activo' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'El código del grado ya está registrado',
            'numero_estudiantes.max' => 'El número de estudiantes no puede exceder la capacidad máxima del grado',
            'año_academico.min' => 'El año académico debe ser 2020 o posterior'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->numero_estudiantes > $this->capacidad_maxima) {
                $validator->errors()->add(
                    'numero_estudiantes',
                    'El número de estudiantes no puede exceder la capacidad máxima del grado'
                );
            }
        });
    }
}
