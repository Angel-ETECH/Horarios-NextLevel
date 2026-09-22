<?php
// app/Http/Requests/HorarioRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class HorarioRequest extends FormRequest
{
    /**
     * Determinar si el usuario está autorizado a realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true; // Cambiar cuando tengas autenticación
    }

    /**
     * Obtén las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        $horarioId = $this->route('id');

        $rules = [
            'profesor_id' => 'required|exists:profesores,id',
            'curso_id' => 'required|exists:cursos,id',
            'grado_id' => 'required|exists:grados,id',
            'aula_id' => 'required|exists:aulas,id',
            'dia_semana' => [
                'required',
                Rule::in(['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'])
            ],
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'turno' => [
                'required',
                Rule::in(['mañana', 'tarde', 'noche'])
            ],
            'institucion' => [
                'required',
                Rule::in(['colegio', 'academia'])
            ],
            'tipo' => [
                'required',
                Rule::in(['regular', 'recuperacion', 'cambio'])
            ],
            'semana' => 'required|integer|min:1|max:52',
            'periodo_academico' => 'nullable|string|max:20',
            'observacion' => 'nullable|string|max:255',
            'version' => 'nullable|integer|min:1',
            'estado' => [
                'nullable',
                Rule::in(['activo', 'cancelado', 'completado', 'pendiente'])
            ]
        ];

        // Si es actualización, algunos campos son opcionales
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['profesor_id'] = 'sometimes|exists:profesores,id';
            $rules['curso_id'] = 'sometimes|exists:cursos,id';
            $rules['grado_id'] = 'sometimes|exists:grados,id';
            $rules['aula_id'] = 'sometimes|exists:aulas,id';
            $rules['dia_semana'] = 'sometimes|in:lunes,martes,miércoles,jueves,viernes,sábado';
            $rules['hora_inicio'] = 'sometimes|date_format:H:i';
            $rules['hora_fin'] = 'sometimes|date_format:H:i|after:hora_inicio';
            $rules['turno'] = 'sometimes|in:mañana,tarde,noche';
            $rules['institucion'] = 'sometimes|in:colegio,academia';
            $rules['tipo'] = 'sometimes|in:regular,recuperacion,cambio';
            $rules['semana'] = 'sometimes|integer|min:1|max:52';
        }

        return $rules;
    }

    /**
     * Obtén los mensajes de validación que se aplican a la solicitud.
     */
    public function messages(): array
    {
        return [
            // Profesor
            'profesor_id.required' => 'El profesor es obligatorio',
            'profesor_id.exists' => 'El profesor seleccionado no existe',

            // Curso
            'curso_id.required' => 'El curso es obligatorio',
            'curso_id.exists' => 'El curso seleccionado no existe',

            // Grado
            'grado_id.required' => 'El grado es obligatorio',
            'grado_id.exists' => 'El grado seleccionado no existe',

            // Aula
            'aula_id.required' => 'El aula es obligatoria',
            'aula_id.exists' => 'El aula seleccionada no existe',

            // Día
            'dia_semana.required' => 'El día de la semana es obligatorio',
            'dia_semana.in' => 'El día de la semana no es válido',

            // Horas
            'hora_inicio.required' => 'La hora de inicio es obligatoria',
            'hora_inicio.date_format' => 'La hora de inicio debe tener formato HH:MM',
            'hora_fin.required' => 'La hora de fin es obligatoria',
            'hora_fin.date_format' => 'La hora de fin debe tener formato HH:MM',
            'hora_fin.after' => 'La hora de fin debe ser después de la hora de inicio',

            // Turno
            'turno.required' => 'El turno es obligatorio',
            'turno.in' => 'El turno no es válido',

            // Institución
            'institucion.required' => 'La institución es obligatoria',
            'institucion.in' => 'La institución debe ser colegio o academia',

            // Tipo
            'tipo.required' => 'El tipo de horario es obligatorio',
            'tipo.in' => 'El tipo de horario no es válido',

            // Semana
            'semana.required' => 'La semana es obligatoria',
            'semana.integer' => 'La semana debe ser un número entero',
            'semana.min' => 'La semana debe ser mayor o igual a 1',
            'semana.max' => 'La semana no puede ser mayor a 52',

            // Observación
            'observacion.max' => 'La observación no puede exceder los 255 caracteres',

            // Estado
            'estado.in' => 'El estado del horario no es válido',

            // Versión
            'version.integer' => 'La versión debe ser un número entero',
            'version.min' => 'La versión debe ser mayor o igual a 1',
        ];
    }

    /**
     * Configurar la instancia del validador.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validarFormatoHorario($validator);
            $this->validarTurno($validator);
            $this->validarPeriodoAcademico($validator);
        });
    }

    /**
     * Validar formato y reglas básicas del horario
     */
    private function validarFormatoHorario($validator): void
    {
        $horaInicio = $this->input('hora_inicio');
        $horaFin = $this->input('hora_fin');

        if (!$horaInicio || !$horaFin) {
            return;
        }

        try {
            $inicio = Carbon::parse($horaInicio);
            $fin = Carbon::parse($horaFin);
        } catch (\Exception $e) {
            return;
        }

        // 1. Validar duración máxima (4 horas para una clase)
        if ($inicio->diffInHours($fin) > 4) {
            $validator->errors()->add(
                'hora_fin',
                'La duración de la clase no puede exceder las 4 horas continuas'
            );
        }

        // 2. Validar duración mínima (30 minutos)
        if ($inicio->diffInMinutes($fin) < 30) {
            $validator->errors()->add(
                'hora_fin',
                'La duración mínima de la clase es de 30 minutos'
            );
        }

        // 3. Validar que esté dentro del horario permitido (6:00 - 23:00)
        $minHora = Carbon::parse('06:00');
        $maxHora = Carbon::parse('23:00');

        if ($inicio < $minHora || $fin > $maxHora) {
            $validator->errors()->add(
                'hora_inicio',
                'El horario debe estar entre las 06:00 y las 23:00'
            );
        }

        // 4. Validar que la hora sea en punto o media hora
        $minutosInicio = (int) $inicio->format('i');
        $minutosFin = (int) $fin->format('i');

        if (!in_array($minutosInicio, [0, 30])) {
            $validator->errors()->add(
                'hora_inicio',
                'La hora de inicio debe ser en punto (:00) o media hora (:30)'
            );
        }

        if (!in_array($minutosFin, [0, 30])) {
            $validator->errors()->add(
                'hora_fin',
                'La hora de fin debe ser en punto (:00) o media hora (:30)'
            );
        }
    }

    /**
     * Validar que el turno coincida con el horario
     */
    private function validarTurno($validator): void
    {
        $horaInicio = $this->input('hora_inicio');
        $turno = $this->input('turno');
        $dia = $this->input('dia_semana');

        if (!$horaInicio || !$turno) {
            return;
        }

        try {
            $hora = Carbon::parse($horaInicio);
            $horaNumero = (int) $hora->format('H');
            $minutos = (int) $hora->format('i');
            $horaDecimal = $horaNumero + ($minutos / 60);
        } catch (\Exception $e) {
            return;
        }

        // 1. Los sábados SIEMPRE son turno mañana
        $diaNormalizado = strtolower(trim($dia));
        if ($diaNormalizado === 'sábado' || $diaNormalizado === 'sabado') {
            if ($turno !== 'mañana') {
                $validator->errors()->add(
                    'turno',
                    'Los sábados solo tienen turno mañana'
                );
            }
            return;
        }

        // 2. Validar rangos de turnos
        $turnos = [
            'mañana' => ['min' => 6, 'max' => 14],   // 6:00 - 13:59
            'tarde'  => ['min' => 14, 'max' => 23]   // 14:00 - 22:59
        ];

        $rango = $turnos[$turno] ?? null;

        if ($rango && ($horaDecimal < $rango['min'] || $horaDecimal >= $rango['max'])) {
            $horaMin = sprintf('%02d:00', $rango['min']);
            $horaMax = sprintf('%02d:00', $rango['max']);
            $validator->errors()->add(
                'turno',
                "La hora {$horaInicio} no corresponde al turno '{$turno}'. " .
                "Horario permitido: {$horaMin} - {$horaMax}"
            );
        }
    }

    /**
     * Validar formato del periodo académico
     */
    private function validarPeriodoAcademico($validator): void
    {
        $periodo = $this->input('periodo_academico');

        if (!$periodo) {
            return;
        }

        // Formato esperado: YYYY-YYYY (ej: 2024-2025)
        if (!preg_match('/^\d{4}-\d{4}$/', $periodo)) {
            $validator->errors()->add(
                'periodo_academico',
                'El periodo académico debe tener el formato YYYY-YYYY (ej: 2024-2025)'
            );
            return;
        }

        // Validar que el año inicial sea menor al final
        [$anioInicio, $anioFin] = explode('-', $periodo);
        if ((int) $anioInicio >= (int) $anioFin) {
            $validator->errors()->add(
                'periodo_academico',
                'El año de inicio debe ser menor que el año de fin'
            );
        }

        // Validar que la diferencia sea de 1 año (periodo académico típico)
        if ((int) $anioFin - (int) $anioInicio !== 1) {
            $validator->errors()->add(
                'periodo_academico',
                'El periodo académico debe abarcar un año (ej: 2024-2025)'
            );
        }
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Normalizar el día de la semana
        if ($this->has('dia_semana')) {
            $dia = strtolower($this->input('dia_semana'));
            $dia = str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $dia);
            $this->merge(['dia_semana' => $dia]);
        }

        // Trim en observación
        if ($this->has('observacion')) {
            $this->merge(['observacion' => trim($this->input('observacion'))]);
        }

        // Asignar periodo académico por defecto si no viene
        if (!$this->has('periodo_academico') || empty($this->input('periodo_academico'))) {
            $anioActual = date('Y');
            $this->merge([
                'periodo_academico' => $anioActual . '-' . ($anioActual + 1)
            ]);
        }

        // Asignar versión por defecto
        if (!$this->has('version') || empty($this->input('version'))) {
            $this->merge(['version' => 1]);
        }

        // Asignar estado por defecto
        if (!$this->has('estado') || empty($this->input('estado'))) {
            $this->merge(['estado' => 'activo']);
        }
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'profesor_id' => 'profesor',
            'curso_id' => 'curso',
            'grado_id' => 'grado',
            'aula_id' => 'aula',
            'dia_semana' => 'día de la semana',
            'hora_inicio' => 'hora de inicio',
            'hora_fin' => 'hora de fin',
            'turno' => 'turno',
            'institucion' => 'institución',
            'tipo' => 'tipo de horario',
            'semana' => 'semana',
            'periodo_academico' => 'periodo académico',
            'observacion' => 'observación',
            'estado' => 'estado',
            'version' => 'versión'
        ];
    }

    /**
     * Get the error bag for the request.
     */
    public function errorBag(): string
    {
        return 'horario';
    }
}
