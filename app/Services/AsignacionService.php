<?php
// app/Services/AsignacionService.php

namespace App\Services;

use App\Models\ProfesorCurso;
use App\Models\Profesor;
use App\Models\Curso;
use App\Models\Grado;
use App\Models\Aula;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AsignacionService
{
    /**
     * Obtener todas las asignaciones con sus relaciones
     */
    public function getAll(): Collection
    {
        return ProfesorCurso::with(['profesor', 'curso', 'grado'])
            ->orderBy('profesor_id')
            ->orderBy('curso_id')
            ->get();
    }

    /**
     * Obtener asignaciones de un profesor específico
     */
    public function getByProfesor(int $profesorId): Collection
    {
        $profesor = Profesor::findOrFail($profesorId);

        return $profesor->cursos()
            ->withPivot('grado_id', 'horas_asignadas', 'rol', 'activo', 'observaciones')
            ->with(['grados' => function($query) use ($profesorId) {
                $query->wherePivot('profesor_id', $profesorId);
            }])
            ->wherePivot('activo', true)
            ->get()
            ->map(function($curso) use ($profesorId) {
                // Obtener el grado específico para esta asignación
                $grado = Grado::whereHas('profesores', function($query) use ($profesorId, $curso) {
                    $query->where('profesor_id', $profesorId)
                          ->where('curso_id', $curso->id);
                })->first();

                return [
                    'curso' => $curso,
                    'grado' => $grado,
                    'horas_asignadas' => $curso->pivot->horas_asignadas,
                    'rol' => $curso->pivot->rol,
                    'activo' => $curso->pivot->activo,
                    'observaciones' => $curso->pivot->observaciones,
                ];
            });
    }

    /**
     * Obtener asignaciones de un curso específico
     */
    public function getByCurso(int $cursoId): Collection
    {
        $curso = Curso::findOrFail($cursoId);

        return $curso->profesores()
            ->withPivot('grado_id', 'horas_asignadas', 'rol', 'activo', 'observaciones')
            ->wherePivot('activo', true)
            ->get();
    }

    /**
     * Obtener asignaciones de un grado específico
     */
    public function getByGrado(int $gradoId): Collection
    {
        $grado = Grado::findOrFail($gradoId);

        return $grado->profesores()
            ->withPivot('curso_id', 'horas_asignadas', 'rol', 'activo', 'observaciones')
            ->wherePivot('activo', true)
            ->get();
    }

    /**
     * Obtener profesores disponibles para un curso y grado
     */
    public function getProfesoresDisponibles(int $cursoId, int $gradoId): Collection
    {
        // Obtener profesores que pueden dictar el curso
        $profesoresAsignados = ProfesorCurso::where('curso_id', $cursoId)
            ->where('grado_id', $gradoId)
            ->where('activo', true)
            ->pluck('profesor_id')
            ->toArray();

        // Obtener todos los profesores que no están asignados a este curso-grado
        return Profesor::whereNotIn('id', $profesoresAsignados)
            ->where('estado', 'activo')
            ->orderBy('apellido_paterno')
            ->get();
    }

    /**
     * Crear una nueva asignación
     */
    public function create(array $data): ProfesorCurso
    {
        return DB::transaction(function () use ($data) {
            // Validar que no exista una asignación duplicada
            $this->validateUnique($data);

            // Validar que el profesor pueda dictar este curso (opcional)
            $this->validateProfesorCurso($data);

            // Validar que el profesor no exceda su carga horaria
            $this->validateCargaHoraria($data['profesor_id'], $data['horas_asignadas']);

            // Validar que el curso existe y está activo
            $curso = Curso::findOrFail($data['curso_id']);
            if (!$curso->activo) {
                throw ValidationException::withMessages([
                    'curso_id' => 'El curso no está activo'
                ]);
            }

            // Validar que el grado existe y está activo
            $grado = Grado::findOrFail($data['grado_id']);
            if (!$grado->activo) {
                throw ValidationException::withMessages([
                    'grado_id' => 'El grado no está activo'
                ]);
            }

            // Crear la asignación
            $asignacion = ProfesorCurso::create($data);

            // Cargar relaciones
            $asignacion->load(['profesor', 'curso', 'grado']);

            return $asignacion;
        });
    }

    /**
     * Actualizar una asignación existente
     */
    public function update(int $id, array $data): ProfesorCurso
    {
        return DB::transaction(function () use ($id, $data) {
            $asignacion = ProfesorCurso::findOrFail($id);

            // Si se está cambiando el profesor, validar
            if (isset($data['profesor_id']) && $data['profesor_id'] != $asignacion->profesor_id) {
                $this->validateUnique($data, $id);
                $this->validateCargaHoraria($data['profesor_id'], $data['horas_asignadas'] ?? 0);
            }

            // Si se cambian las horas, validar carga horaria
            if (isset($data['horas_asignadas']) && $data['horas_asignadas'] != $asignacion->horas_asignadas) {
                $diferencia = $data['horas_asignadas'] - $asignacion->horas_asignadas;
                $this->validateCargaHoraria(
                    $asignacion->profesor_id,
                    $diferencia,
                    true // es actualización
                );
            }

            // Actualizar
            $asignacion->update($data);
            $asignacion->load(['profesor', 'curso', 'grado']);

            return $asignacion;
        });
    }

    /**
     * Eliminar una asignación (soft delete)
     */
    public function delete(int $id): bool
    {
        $asignacion = ProfesorCurso::findOrFail($id);
        return $asignacion->delete();
    }

    /**
     * Desactivar una asignación (en lugar de eliminar)
     */
    public function desactivar(int $id): ProfesorCurso
    {
        $asignacion = ProfesorCurso::findOrFail($id);
        $asignacion->update(['activo' => false]);
        return $asignacion;
    }

    /**
     * Activar una asignación desactivada
     */
    public function activar(int $id): ProfesorCurso
    {
        $asignacion = ProfesorCurso::findOrFail($id);

        // Validar que no haya conflicto al activar
        $this->validateUnique($asignacion->toArray(), $id);
        $this->validateCargaHoraria($asignacion->profesor_id, $asignacion->horas_asignadas);

        $asignacion->update(['activo' => true]);
        return $asignacion;
    }

    /**
     * Validar que no exista una asignación duplicada
     */
    private function validateUnique(array $data, ?int $excludeId = null): void
    {
        $query = ProfesorCurso::where('profesor_id', $data['profesor_id'])
            ->where('curso_id', $data['curso_id'])
            ->where('grado_id', $data['grado_id']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'asignacion' => 'Ya existe una asignación para este profesor, curso y grado'
            ]);
        }
    }

    /**
     * Validar que el profesor pueda dictar este curso (si existe validación adicional)
     */
    private function validateProfesorCurso(array $data): void
    {
        // Aquí podrías agregar lógica adicional como:
        // - Verificar especialidad del profesor
        // - Verificar si el profesor tiene experiencia en el curso
        // Por ahora, solo validamos que exista
        $profesor = Profesor::find($data['profesor_id']);
        $curso = Curso::find($data['curso_id']);

        if (!$profesor || !$curso) {
            throw ValidationException::withMessages([
                'profesor_id' => 'El profesor o curso no existe'
            ]);
        }
    }

    /**
     * Validar la carga horaria del profesor
     */
    private function validateCargaHoraria(int $profesorId, int $horasNuevas, bool $esActualizacion = false): void
    {
        $profesor = Profesor::findOrFail($profesorId);

        // Obtener carga horaria actual (todas las asignaciones activas)
        $cargaActual = ProfesorCurso::where('profesor_id', $profesorId)
            ->where('activo', true)
            ->sum('horas_asignadas');

        $total = $cargaActual + $horasNuevas;

        // Si la nueva carga excede la máxima permitida
        if ($total > $profesor->carga_horaria_maxima) {
            throw ValidationException::withMessages([
                'horas_asignadas' => "El profesor excedería su carga horaria máxima ({$profesor->carga_horaria_maxima} horas). Actual: {$cargaActual}h, Nuevas: {$horasNuevas}h"
            ]);
        }
    }

    /**
     * Obtener estadísticas de asignaciones
     */
    public function getEstadisticas(): array
    {
        $totalAsignaciones = ProfesorCurso::where('activo', true)->count();
        $totalProfesores = Profesor::where('estado', 'activo')->count();
        $totalCursos = Curso::where('activo', true)->count();
        $totalGrados = Grado::where('activo', true)->count();

        // Asignaciones por nivel
        $asignacionesPorNivel = ProfesorCurso::where('activo', true)
            ->join('grados', 'profesor_curso.grado_id', '=', 'grados.id')
            ->select('grados.nivel', DB::raw('count(*) as total'))
            ->groupBy('grados.nivel')
            ->get();

        return [
            'total_asignaciones' => $totalAsignaciones,
            'total_profesores' => $totalProfesores,
            'total_cursos' => $totalCursos,
            'total_grados' => $totalGrados,
            'asignaciones_por_nivel' => $asignacionesPorNivel,
            'promedio_asignaciones_por_profesor' => $totalProfesores > 0
                ? round($totalAsignaciones / $totalProfesores, 2)
                : 0
        ];
    }
}
