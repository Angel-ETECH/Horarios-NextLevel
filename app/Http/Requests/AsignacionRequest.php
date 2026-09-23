<?php
// app/Http/Requests/AsignacionRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AsignacionRequest extends FormRequest
{
    /**
     * Autorizar la solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación.
     */
    public function rules(): array
    {
        /*
        |--------------------------------------------------------------------------
        | CREAR
        |--------------------------------------------------------------------------
        */

        $rules = [
            'profesor_id' => [
                'required',
                'integer',
                'exists:profesores,id',
            ],

            'institucion' => [
                'required',
                Rule::in([
                    'colegio',
                    'academia',
                ]),
            ],

            'curso_id' => [
                'required',
                'integer',
                'exists:cursos,id',
            ],

            'grado_id' => [
                'required',
                'integer',
                'exists:grados,id',
            ],

            'horas_asignadas' => [
                'required',
                'integer',
                'min:1',
                'max:40',
            ],

            'rol' => [
                'required',
                Rule::in([
                    'titular',
                    'asistente',
                    'suplente',
                ]),
            ],

            'activo' => [
                'boolean',
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | ACTUALIZAR
        |--------------------------------------------------------------------------
        */

        if (
            $this->isMethod('put') ||
            $this->isMethod('patch')
        ) {
            $rules = [
                'profesor_id' => [
                    'sometimes',
                    'integer',
                    'exists:profesores,id',
                ],

                'institucion' => [
                    'sometimes',
                    Rule::in([
                        'colegio',
                        'academia',
                    ]),
                ],

                'curso_id' => [
                    'sometimes',
                    'integer',
                    'exists:cursos,id',
                ],

                'grado_id' => [
                    'sometimes',
                    'integer',
                    'exists:grados,id',
                ],

                'horas_asignadas' => [
                    'sometimes',
                    'integer',
                    'min:1',
                    'max:40',
                ],

                'rol' => [
                    'sometimes',
                    Rule::in([
                        'titular',
                        'asistente',
                        'suplente',
                    ]),
                ],

                'activo' => [
                    'sometimes',
                    'boolean',
                ],

                'observaciones' => [
                    'nullable',
                    'string',
                    'max:255',
                ],
            ];
        }


        return $rules;
    }

    /**
     * Mensajes personalizados.
     */
    public function messages(): array
    {
        return [
            'profesor_id.required' =>
                'El profesor es obligatorio.',

            'profesor_id.integer' =>
                'El profesor seleccionado no es válido.',

            'profesor_id.exists' =>
                'El profesor seleccionado no existe.',


            'institucion.required' =>
                'La institución es obligatoria.',

            'institucion.in' =>
                'La institución debe ser colegio o academia.',


            'curso_id.required' =>
                'El curso es obligatorio.',

            'curso_id.integer' =>
                'El curso seleccionado no es válido.',

            'curso_id.exists' =>
                'El curso seleccionado no existe.',


            'grado_id.required' =>
                'El grado es obligatorio.',

            'grado_id.integer' =>
                'El grado seleccionado no es válido.',

            'grado_id.exists' =>
                'El grado seleccionado no existe.',


            'horas_asignadas.required' =>
                'Las horas asignadas son obligatorias.',

            'horas_asignadas.integer' =>
                'Las horas asignadas deben ser un número entero.',

            'horas_asignadas.min' =>
                'Las horas asignadas deben ser al menos 1.',

            'horas_asignadas.max' =>
                'Las horas asignadas no pueden exceder 40.',


            'rol.required' =>
                'El rol del profesor es obligatorio.',

            'rol.in' =>
                'El rol debe ser titular, asistente o suplente.',


            'activo.boolean' =>
                'El estado de la asignación no es válido.',


            'observaciones.string' =>
                'Las observaciones deben ser texto.',

            'observaciones.max' =>
                'Las observaciones no pueden superar los 255 caracteres.',
        ];
    }

    /**
     * Preparar datos antes de validar.
     */
    protected function prepareForValidation(): void
    {
        /*
        |--------------------------------------------------------------------------
        | INSTITUCIÓN
        |--------------------------------------------------------------------------
        */

        if ($this->has('institucion')) {
            $this->merge([
                'institucion' =>
                    strtolower(
                        trim(
                            (string) $this->institucion
                        )
                    ),
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIVO
        |--------------------------------------------------------------------------
        |
        | En creación, si no se envía activo,
        | la asignación queda activa por defecto.
        |
        */

        if (
            !$this->isMethod('put') &&
            !$this->isMethod('patch') &&
            $this->has('activo') === false
        ) {
            $this->merge([
                'activo' => true,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | OBSERVACIONES
        |--------------------------------------------------------------------------
        */

        if ($this->has('observaciones')) {
            $observaciones =
                trim(
                    (string) $this->observaciones
                );

            $this->merge([
                'observaciones' =>
                    $observaciones !== ''
                        ? $observaciones
                        : null,
            ]);
        }
    }
}