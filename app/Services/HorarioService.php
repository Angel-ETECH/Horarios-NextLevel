<?php
// app/Services/HorarioService.php

namespace App\Services;

use App\Models\Horario;
use App\Models\Profesor;
use App\Models\Aula;
use App\Models\Grado;
use App\Models\Curso;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HorarioService
{
    protected $disponibilidadService;

    public function __construct(DisponibilidadService $disponibilidadService)
    {
        $this->disponibilidadService = $disponibilidadService;
    }

    /**
     * Validación COMPLETA antes de crear o actualizar un horario
     */
    private function validarHorarioCompleto(array $data, ?int $excludeId = null): void
    {
        $profesorId = $data['profesor_id'];
        $aulaId = $data['aula_id'];
        $gradoId = $data['grado_id'];
        $cursoId = $data['curso_id'];
        $dia = $data['dia_semana'];
        $horaInicio = $data['hora_inicio'];
        $horaFin = $data['hora_fin'];
        $institucion = $data['institucion'];

        // 1. VALIDAR DISPONIBILIDAD DEL PROFESOR
        $this->disponibilidadService->validarDisponibilidadParaClase(
            $profesorId,
            $dia,
            $horaInicio,
            $horaFin,
            $institucion,
            $excludeId
        );

        // 2. VALIDAR QUE EL PROFESOR NO TENGA CONFLICTO CON OTRO HORARIO
        $this->validarConflictoProfesor($profesorId, $dia, $horaInicio, $horaFin, $institucion, $excludeId);

        // 3. VALIDAR QUE EL AULA ESTÉ DISPONIBLE (sin conflictos)
        $this->validarConflictoAula($aulaId, $dia, $horaInicio, $horaFin, $institucion, $excludeId);

        // 4. VALIDAR QUE EL GRADO ESTÉ DISPONIBLE (sin conflictos)
        $this->validarConflictoGrado($gradoId, $dia, $horaInicio, $horaFin, $institucion, $excludeId);

        // 5. VALIDAR QUE EL CURSO EXISTA Y ESTÉ ACTIVO
        $curso = Curso::findOrFail($cursoId);
        if (!$curso->activo) {
            throw ValidationException::withMessages([
                'curso_id' => 'El curso seleccionado no está activo'
            ]);
        }

        // 6. VALIDAR QUE EL GRADO EXISTA Y ESTÉ ACTIVO
        $grado = Grado::findOrFail($gradoId);
        if (!$grado->activo) {
            throw ValidationException::withMessages([
                'grado_id' => 'El grado seleccionado no está activo'
            ]);
        }

        // 7. VALIDAR QUE EL PROFESOR ESTÉ ACTIVO
        $profesor = Profesor::findOrFail($profesorId);
        if ($profesor->estado !== 'activo') {
            throw ValidationException::withMessages([
                'profesor_id' => 'El profesor seleccionado no está activo'
            ]);
        }

        // 8. VALIDAR QUE EL AULA ESTÉ ACTIVA
        $aula = Aula::findOrFail($aulaId);
        if (!$aula->activo) {
            throw ValidationException::withMessages([
                'aula_id' => 'El aula seleccionada no está activa'
            ]);
        }

        // 9. VALIDAR CAPACIDAD DEL AULA VS GRADO
        if ($aula->capacidad < $grado->numero_estudiantes) {
            throw ValidationException::withMessages([
                'aula_id' => "El aula tiene capacidad para {$aula->capacidad} estudiantes, pero el grado tiene {$grado->numero_estudiantes}"
            ]);
        }

        // 10. VALIDAR COMPATIBILIDAD DE NIVELES (curso - grado)
        if ($curso->nivel !== 'todos' && $curso->nivel !== $grado->nivel) {
            throw ValidationException::withMessages([
                'curso_id' => "El curso es de nivel '{$curso->nivel}' pero el grado es de nivel '{$grado->nivel}'"
            ]);
        }

        // 11. VALIDAR COMPATIBILIDAD DE NIVELES (aula - grado)
        if ($aula->nivel !== 'todos' && $aula->nivel !== $grado->nivel) {
            throw ValidationException::withMessages([
                'aula_id' => "El aula es de nivel '{$aula->nivel}' pero el grado es de nivel '{$grado->nivel}'"
            ]);
        }

        // 12. VALIDAR QUE EL TURNO COINCIDA CON EL HORARIO
        $this->validarTurno($horaInicio, $data['turno']);

        // 13. VALIDAR QUE LA INSTITUCIÓN COINCIDA CON LA DEL PROFESOR
        if (!in_array($institucion, ['colegio', 'academia']) ||
            ($profesor->institucion !== 'ambos' && $profesor->institucion !== $institucion)) {
            throw ValidationException::withMessages([
                'institucion' => "El profesor no está asignado a la institución '{$institucion}'"
            ]);
        }
    }

    /**
     * Validar que el profesor no tenga conflicto con otro horario
     */
    private function validarConflictoProfesor(int $profesorId, string $dia, string $horaInicio, string $horaFin, string $institucion, ?int $excludeId = null): void
    {
        $query = Horario::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('estado', 'activo');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $horariosExistentes = $query->get();

        $nuevoInicio = Carbon::parse($horaInicio);
        $nuevoFin = Carbon::parse($horaFin);

        foreach ($horariosExistentes as $horario) {
            $horarioInicio = Carbon::parse($horario->hora_inicio);
            $horarioFin = Carbon::parse($horario->hora_fin);

            // Verificar solapamiento REAL
            if ($nuevoInicio->lt($horarioFin) && $nuevoFin->gt($horarioInicio)) {
                throw ValidationException::withMessages([
                    'conflicto_profesor' => "El profesor ya tiene una clase en ese horario ({$horario->hora_inicio} - {$horario->hora_fin}) con el curso '{$horario->curso->nombre}'"
                ]);
            }
        }
    }

    /**
     * Validar que el aula no tenga conflicto con otro horario
     */
    private function validarConflictoAula(int $aulaId, string $dia, string $horaInicio, string $horaFin, string $institucion, ?int $excludeId = null): void
    {
        $query = Horario::where('aula_id', $aulaId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('estado', 'activo');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $horariosExistentes = $query->get();

        $nuevoInicio = Carbon::parse($horaInicio);
        $nuevoFin = Carbon::parse($horaFin);

        foreach ($horariosExistentes as $horario) {
            $horarioInicio = Carbon::parse($horario->hora_inicio);
            $horarioFin = Carbon::parse($horario->hora_fin);

            // Verificar solapamiento REAL
            if ($nuevoInicio->lt($horarioFin) && $nuevoFin->gt($horarioInicio)) {
                throw ValidationException::withMessages([
                    'conflicto_aula' => "El aula ya está ocupada en ese horario ({$horario->hora_inicio} - {$horario->hora_fin})"
                ]);
            }
        }
    }

    /**
     * Validar que el grado no tenga conflicto con otro horario
     */
    private function validarConflictoGrado(int $gradoId, string $dia, string $horaInicio, string $horaFin, string $institucion, ?int $excludeId = null): void
    {
        $query = Horario::where('grado_id', $gradoId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('estado', 'activo');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $horariosExistentes = $query->get();

        $nuevoInicio = Carbon::parse($horaInicio);
        $nuevoFin = Carbon::parse($horaFin);

        foreach ($horariosExistentes as $horario) {
            $horarioInicio = Carbon::parse($horario->hora_inicio);
            $horarioFin = Carbon::parse($horario->hora_fin);

            // Verificar solapamiento REAL
            if ($nuevoInicio->lt($horarioFin) && $nuevoFin->gt($horarioInicio)) {
                throw ValidationException::withMessages([
                    'conflicto_grado' => "El grado ya tiene una clase en ese horario ({$horario->hora_inicio} - {$horario->hora_fin})"
                ]);
            }
        }
    }

    /**
     * Validar que el turno coincida con el horario
     */
    private function validarTurno(string $hora, string $turno): void
    {
        $horaCarbon = Carbon::parse($hora);
        $horaNumero = (int)$horaCarbon->format('H');

        $turnos = [
            'mañana' => ['min' => 6, 'max' => 12],
            'tarde' => ['min' => 12, 'max' => 18],
            'noche' => ['min' => 18, 'max' => 23]
        ];

        $rango = $turnos[$turno] ?? null;

        if ($rango && ($horaNumero < $rango['min'] || $horaNumero >= $rango['max'])) {
            throw ValidationException::withMessages([
                'turno' => "La hora {$hora} no corresponde al turno '{$turno}'"
            ]);
        }
    }

    /**
     * Crear un nuevo horario
     */
    public function create(array $data): Horario
    {
        return DB::transaction(function () use ($data) {
            // Validación completa
            $this->validarHorarioCompleto($data);

            // Crear horario
            $horario = Horario::create($data);

            // Cargar relaciones
            $horario->load(['profesor', 'curso', 'grado', 'aula']);

            return $horario;
        });
    }

    /**
     * Actualizar un horario existente
     */
    public function update(int $id, array $data): Horario
    {
        return DB::transaction(function () use ($id, $data) {
            $horario = Horario::findOrFail($id);

            // Fusionar datos existentes con los nuevos para validación
            $mergedData = array_merge($horario->toArray(), $data);

            // Validación completa (excluyendo el horario actual)
            $this->validarHorarioCompleto($mergedData, $id);

            // Actualizar
            $horario->update($data);

            // Cargar relaciones
            $horario->load(['profesor', 'curso', 'grado', 'aula']);

            return $horario;
        });
    }

    /**
     * Eliminar (soft delete) un horario
     */
    public function delete(int $id): bool
    {
        $horario = Horario::findOrFail($id);
        return $horario->delete();
    }

    /**
     * Restaurar un horario eliminado
     */
    public function restore(int $id): Horario
    {
        $horario = Horario::withTrashed()->findOrFail($id);
        $horario->restore();
        $horario->load(['profesor', 'curso', 'grado', 'aula']);
        return $horario;
    }

    /**
     * Obtener horarios con filtros
     */
    public function getHorarios(array $filtros = []): Collection
    {
        $query = Horario::with(['profesor', 'curso', 'grado', 'aula'])
            ->where('estado', 'activo');

        // Filtros
        if (!empty($filtros['profesor_id'])) {
            $query->where('profesor_id', $filtros['profesor_id']);
        }

        if (!empty($filtros['grado_id'])) {
            $query->where('grado_id', $filtros['grado_id']);
        }

        if (!empty($filtros['curso_id'])) {
            $query->where('curso_id', $filtros['curso_id']);
        }

        if (!empty($filtros['aula_id'])) {
            $query->where('aula_id', $filtros['aula_id']);
        }

        if (!empty($filtros['dia_semana'])) {
            $query->where('dia_semana', $filtros['dia_semana']);
        }

        if (!empty($filtros['turno'])) {
            $query->where('turno', $filtros['turno']);
        }

        if (!empty($filtros['institucion'])) {
            $query->where('institucion', $filtros['institucion']);
        }

        if (!empty($filtros['periodo_academico'])) {
            $query->where('periodo_academico', $filtros['periodo_academico']);
        }

        return $query->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener horario de un profesor específico
     */
    public function getHorarioProfesor(int $profesorId, ?string $institucion = null): Collection
    {
        $query = Horario::with(['curso', 'grado', 'aula'])
            ->where('profesor_id', $profesorId)
            ->where('estado', 'activo');

        if ($institucion) {
            $query->where('institucion', $institucion);
        }

        return $query->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener horario de un grado específico
     */
    public function getHorarioGrado(int $gradoId, ?string $institucion = null): Collection
    {
        $query = Horario::with(['profesor', 'curso', 'aula'])
            ->where('grado_id', $gradoId)
            ->where('estado', 'activo');

        if ($institucion) {
            $query->where('institucion', $institucion);
        }

        return $query->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener estadísticas de horarios
     */
    public function getEstadisticas(?string $institucion = null): array
    {
        $query = Horario::where('estado', 'activo');

        if ($institucion) {
            $query->where('institucion', $institucion);
        }

        $total = $query->count();

        $porDia = (clone $query)->select('dia_semana', DB::raw('count(*) as total'))
            ->groupBy('dia_semana')
            ->orderBy('dia_semana')
            ->get();

        $porTurno = (clone $query)->select('turno', DB::raw('count(*) as total'))
            ->groupBy('turno')
            ->get();

        $porInstitucion = Horario::where('estado', 'activo')
            ->select('institucion', DB::raw('count(*) as total'))
            ->groupBy('institucion')
            ->get();

        return [
            'total' => $total,
            'por_dia' => $porDia,
            'por_turno' => $porTurno,
            'por_institucion' => $porInstitucion,
            'total_profesores' => (clone $query)->distinct('profesor_id')->count(),
            'total_aulas' => (clone $query)->distinct('aula_id')->count(),
            'total_grados' => (clone $query)->distinct('grado_id')->count()
        ];
    }

    /**
     * Verificar si un horario puede ser movido a otro slot
     * (Para funcionalidad de arrastrar y soltar)
     */
    public function verificarMovimiento(int $horarioId, string $nuevoDia, string $nuevaHoraInicio, string $nuevaHoraFin): array
    {
        $horario = Horario::findOrFail($horarioId);

        try {
            $data = [
                'profesor_id' => $horario->profesor_id,
                'curso_id' => $horario->curso_id,
                'grado_id' => $horario->grado_id,
                'aula_id' => $horario->aula_id,
                'dia_semana' => $nuevoDia,
                'hora_inicio' => $nuevaHoraInicio,
                'hora_fin' => $nuevaHoraFin,
                'turno' => $horario->turno,
                'institucion' => $horario->institucion,
                'tipo' => $horario->tipo,
                'semana' => $horario->semana,
                'periodo_academico' => $horario->periodo_academico
            ];

            $this->validarHorarioCompleto($data, $horarioId);

            return [
                'status' => 'success',
                'message' => 'El horario se puede mover sin conflictos',
                'data' => $data
            ];

        } catch (ValidationException $e) {
            return [
                'status' => 'error',
                'message' => 'El horario no se puede mover',
                'errores' => $e->errors()
            ];
        }
    }
}
