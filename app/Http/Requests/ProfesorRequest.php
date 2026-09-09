<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfesorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambiar cuando tengas autenticación
    }

    public function rules(): array
    {
        $profesorId = $this->route('id');

        return [
            'codigo' => ['required', 'string', 'max:20', Rule::unique('profesores')->ignore($profesorId)],
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:50',
            'apellido_materno' => 'nullable|string|max:50',
            'email' => ['required', 'email', 'max:100', Rule::unique('profesores')->ignore($profesorId)],
            'telefono' => 'nullable|string|max:20',
            'dni' => ['required', 'string', 'size:8', Rule::unique('profesores')->ignore($profesorId)],
            'sexo' => 'required|in:masculino,femenino',
            'fecha_nacimiento' => 'nullable|date',
            'especialidad' => 'nullable|string|max:100',
            'carga_horaria_maxima' => 'required|integer|min:1|max:40',
            'estado' => 'required|in:activo,inactivo,licencia',
            'institucion' => 'required|in:colegio,academia,ambos',
            'observaciones' => 'nullable|string'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.unique' => 'El código ya está registrado',
            'dni.unique' => 'El DNI ya está registrado',
            'email.unique' => 'El email ya está registrado',
            'dni.size' => 'El DNI debe tener 8 dígitos'
        ];
    }
}
