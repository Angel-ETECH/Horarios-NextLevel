<?php
// app/Http/Requests/DisponibilidadRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DisponibilidadRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Temporal, luego agregamos autenticación
    }

    /**
     * Obtén las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        $rules = [
            'profesor_id' => 'required|exists:profesores,id',
            'dia_semana' => [
                'required',
                Rule::in(['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'])
            ],
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'tipo' => [
                'required',
                Rule::in(['disponible', 'no_disponible'])
            ],
            'turno' => [
                'nullable',
                Rule::in(['mañana', 'tarde', 'noche'])
            ],
            'institucion' => [
                'nullable',
                Rule::in(['colegio', 'academia'])
            ],
            'observacion' => 'nullable|string|max:255'
        ];

        // Si es actualización, el profesor_id no es requerido
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['profesor_id'] = 'sometimes|exists:profesores,id';
        }

        return $rules;
    }

    /**
     * Obtén los mensajes de validación que se aplican a la solicitud.
     */
    public function messages(): array
    {
        return [
            'profesor_id.required' => 'El profesor es obligatorio',
            'profesor_id.exists' => 'El profesor seleccionado no existe',
            'dia_semana.required' => 'El día de la semana es obligatorio',
            'dia_semana.in' => 'El día de la semana no es válido',
            'hora_inicio.required' => 'La hora de inicio es obligatoria',
            'hora_inicio.date_format' => 'La hora de inicio debe tener formato HH:MM',
            'hora_fin.required' => 'La hora de fin es obligatoria',
            'hora_fin.date_format' => 'La hora de fin debe tener formato HH:MM',
            'hora_fin.after' => 'La hora de fin debe ser después de la hora de inicio',
            'tipo.required' => 'El tipo de disponibilidad es obligatorio',
            'tipo.in' => 'El tipo de disponibilidad no es válido',
            'turno.in' => 'El turno no es válido',
            'institucion.in' => 'La institución debe ser colegio o academia',
            'observacion.max' => 'La observación no puede exceder los 255 caracteres'
        ];
    }

    /**
     * Preparar los datos para su validación.
     */
    protected function prepareForValidation()
    {
        // Normalizar el día de la semana (pasar a minúsculas y quitar acentos)
        if ($this->has('dia_semana')) {
            $dia = strtolower($this->input('dia_semana'));
            $dia = str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $dia);
            $this->merge(['dia_semana' => $dia]);
        }

        // Trim en observación
        if ($this->has('observacion')) {
            $this->merge(['observacion' => trim($this->input('observacion'))]);
        }

        // Si no viene institución, asignar 'colegio' por defecto
        if (!$this->has('institucion')) {
            $this->merge(['institucion' => 'colegio']);
        }
    }
}
