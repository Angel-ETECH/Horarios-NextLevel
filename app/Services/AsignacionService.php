<?php
// app/Services/AsignacionService.php

namespace App\Services;

use App\Models\ProfesorCurso;
use App\Models\Profesor;
use App\Models\Curso;
use App\Models\Grado;
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
            ->withPivot('grado_id', 'horas_asignadas', 'rol', 'activo', 'observaciones', 'institucion')
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
                    'institucion' => $curso->pivot->institucion ?? 'colegio'
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
            ->withPivot('grado_id', 'horas_asignadas', 'rol', 'activo', 'observaciones', 'institucion')
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
            ->withPivot('curso_id', 'horas_asignadas', 'rol', 'activo', 'observaciones', 'institucion')
            ->wherePivot('activo', true)
            ->get();
    }

    /**
     * Obtener asignaciones por institución
     */
    public function getByInstitucion(string $institucion): Collection
    {
        return ProfesorCurso::with(['profesor', 'curso', 'grado'])
            ->where('institucion', $institucion)
            ->where('activo', true)
            ->orderBy('profesor_id')
            ->get();
    }

    /**
     * Obtener profesores disponibles para un curso y grado
     */
    public function getProfesoresDisponibles(int $cursoId, int $gradoId, ?string $institucion = null): Collection
    {
        // Obtener profesores ya asignados a este curso-grado
        $queryProfesoresAsignados = ProfesorCurso::where('curso_id', $cursoId)
            ->where('grado_id', $gradoId)
            ->where('activo', true);

        if ($institucion) {
            $queryProfesoresAsignados->where('institucion', $institucion);
        }

        $profesoresAsignados = $queryProfesoresAsignados->pluck('profesor_id')->toArray();

        // Obtener todos los profesores que no están asignados
        $queryProfesores = Profesor::where('estado', 'activo');

        if ($institucion) {
            $queryProfesores->whereIn('institucion', [$institucion, 'ambos']);
        }

        if (!empty($profesoresAsignados)) {
            $queryProfesores->whereNotIn('id', $profesoresAsignados);
        }

        return $queryProfesores->orderBy('apellido_paterno')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * ========================================
     * VALIDACIONES PRIVADAS
     * ========================================
     */

    /**
     * Validar que no exista una asignación duplicada
     */
    private function validateUnique(array $data, ?int $excludeId = null): void
    {
        $query = ProfesorCurso::where('profesor_id', $data['profesor_id'])
            ->where('curso_id', $data['curso_id'])
            ->where('grado_id', $data['grado_id'])
            ->where('institucion', $data['institucion'] ?? 'colegio');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'asignacion' => 'Ya existe una asignación con el mismo Profesor, Curso, Grado e Institución'
            ]);
        }
    }

    /**
     * Validar la carga horaria del profesor
     */
    private function validateCargaHoraria(int $profesorId, int $nuevasHoras, ?int $excludeId = null): void
    {
        $profesor = Profesor::findOrFail($profesorId);

        // Obtener carga horaria actual (excluyendo la asignación a actualizar si existe)
        $query = ProfesorCurso::where('profesor_id', $profesorId)
            ->where('activo', true);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $cargaActual = $query->sum('horas_asignadas');
        $total = $cargaActual + $nuevasHoras;

        // Si la nueva carga excede la máxima permitida
        if ($total > $profesor->carga_horaria_maxima) {
            throw ValidationException::withMessages([
                'horas_asignadas' => "El profesor excedería su carga horaria máxima ({$profesor->carga_horaria_maxima} horas). Actual: {$cargaActual}h + Nuevas: {$nuevasHoras}h = {$total}h"
            ]);
        }
    }

    /**
     * Validar que el curso y grado existan y estén activos
     */
    private function validateCursoYGrado(int $cursoId, int $gradoId): void
    {
        // Validar curso
        $curso = Curso::find($cursoId);
        if (!$curso) {
            throw ValidationException::withMessages([
                'curso_id' => 'El curso seleccionado no existe'
            ]);
        }

        if (!$curso->activo) {
            throw ValidationException::withMessages([
                'curso_id' => 'El curso seleccionado no está activo'
            ]);
        }

        // Validar grado
        $grado = Grado::find($gradoId);
        if (!$grado) {
            throw ValidationException::withMessages([
                'grado_id' => 'El grado seleccionado no existe'
            ]);
        }

        if (!$grado->activo) {
            throw ValidationException::withMessages([
                'grado_id' => 'El grado seleccionado no está activo'
            ]);
        }

        // Validar compatibilidad de nivel (si el curso tiene nivel específico)
        if ($curso->nivel && $curso->nivel !== 'todos' && $curso->nivel !== $grado->nivel) {
            throw ValidationException::withMessages([
                'curso_id' => "El curso es de nivel '{$curso->nivel}' pero el grado es de nivel '{$grado->nivel}'"
            ]);
        }
    }

    /**
     * Validar compatibilidad profesor-curso
     */
    private function validateProfesorCurso(array $data): void
    {
        $profesor = Profesor::find($data['profesor_id']);
        $curso = Curso::find($data['curso_id']);

        if (!$profesor || !$curso) {
            throw ValidationException::withMessages([
                'profesor_id' => 'El profesor o curso no existe'
            ]);
        }

        // Validar que el profesor esté activo
        if ($profesor->estado !== 'activo') {
            throw ValidationException::withMessages([
                'profesor_id' => 'El profesor no está activo'
            ]);
        }
    }

    /**
     * Actualizar la carga horaria actual de un profesor
     */
    private function actualizarCargaHorariaProfesor(int $profesorId): void
    {
        $totalHoras = ProfesorCurso::where('profesor_id', $profesorId)
            ->where('activo', true)
            ->sum('horas_asignadas');

        Profesor::where('id', $profesorId)->update([
            'carga_horaria_actual' => $totalHoras
        ]);
    }

    /**
     * ========================================
     * CRUD PRINCIPAL
     * ========================================
     */

    /**
     * Crear una nueva asignación
     */
    public function create(array $data): ProfesorCurso
    {
        return DB::transaction(function () use ($data) {
            // 1. Validar que el profesor existe y está activo
            $this->validateProfesorCurso($data);

            // 2. Validar compatibilidad curso-grado
            $this->validateCursoYGrado($data['curso_id'], $data['grado_id']);

            // 3. Validar que no exista duplicado
            $this->validateUnique($data);

            // 4. Validar carga horaria del profesor
            $this->validateCargaHoraria($data['profesor_id'], $data['horas_asignadas']);

            // 5. Crear la asignación
            $asignacion = ProfesorCurso::create($data);

            // 6. Actualizar carga horaria actual del profesor
            $this->actualizarCargaHorariaProfesor($data['profesor_id']);

            // 7. Cargar relaciones
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

            // Obtener valores actuales
            $profesorIdActual = $asignacion->profesor_id;
            $cursoIdActual = $asignacion->curso_id;
            $gradoIdActual = $asignacion->grado_id;
            $horasActuales = $asignacion->horas_asignadas;
            $institucionActual = $asignacion->institucion ?? 'colegio';

            // Obtener valores finales (usar actuales si no se envían)
            $nuevoProfesorId = $data['profesor_id'] ?? $profesorIdActual;
            $nuevoCursoId = $data['curso_id'] ?? $cursoIdActual;
            $nuevoGradoId = $data['grado_id'] ?? $gradoIdActual;
            $nuevasHoras = $data['horas_asignadas'] ?? $horasActuales;
            $nuevaInstitucion = $data['institucion'] ?? $institucionActual;

            // 1. Validar que los IDs existan (si cambiaron)
            if (isset($data['profesor_id']) && $data['profesor_id'] != $profesorIdActual) {
                $this->validateProfesorCurso($data);
            }

            if (isset($data['curso_id']) || isset($data['grado_id'])) {
                $this->validateCursoYGrado(
                    $data['curso_id'] ?? $cursoIdActual,
                    $data['grado_id'] ?? $gradoIdActual
                );
            }

            // 2. VALIDACIÓN CRÍTICA: Verificar duplicados al actualizar
            // IMPORTANTE: Si cambia profesor, curso, grado o institución,
            // debemos verificar que no exista ya esa combinación
            if ($nuevoProfesorId != $profesorIdActual ||
                $nuevoCursoId != $cursoIdActual ||
                $nuevoGradoId != $gradoIdActual ||
                $nuevaInstitucion != $institucionActual) {

                $this->validateUnique([
                    'profesor_id' => $nuevoProfesorId,
                    'curso_id' => $nuevoCursoId,
                    'grado_id' => $nuevoGradoId,
                    'institucion' => $nuevaInstitucion
                ], $id);
            }

            // 3. Validar carga horaria (si cambia profesor o horas)
            if ($nuevoProfesorId != $profesorIdActual || $nuevasHoras != $horasActuales) {
                $this->validateCargaHoraria($nuevoProfesorId, $nuevasHoras, $id);
            }

            // 4. Actualizar la asignación
            $asignacion->update($data);

            // 5. Actualizar carga horaria de ambos profesores (si cambió)
            if ($nuevoProfesorId != $profesorIdActual) {
                $this->actualizarCargaHorariaProfesor($profesorIdActual);
                $this->actualizarCargaHorariaProfesor($nuevoProfesorId);
            } else {
                $this->actualizarCargaHorariaProfesor($nuevoProfesorId);
            }

            // 6. Cargar relaciones
            $asignacion->load(['profesor', 'curso', 'grado']);

            return $asignacion;
        });
    }

    /**
     * Eliminar (desactivar) una asignación
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $asignacion = ProfesorCurso::findOrFail($id);

            // En lugar de eliminar, desactivar
            $asignacion->update(['activo' => false]);

            // Actualizar carga horaria del profesor
            $this->actualizarCargaHorariaProfesor($asignacion->profesor_id);

            return true;
        });
    }

    /**
     * Reactivar una asignación desactivada
     */
    public function activar(int $id): ProfesorCurso
    {
        return DB::transaction(function () use ($id) {
            $asignacion = ProfesorCurso::findOrFail($id);

            // Verificar que no haya duplicado al activar
            $this->validateUnique([
                'profesor_id' => $asignacion->profesor_id,
                'curso_id' => $asignacion->curso_id,
                'grado_id' => $asignacion->grado_id,
                'institucion' => $asignacion->institucion ?? 'colegio'
            ], $id);

            // Verificar carga horaria
            $this->validateCargaHoraria(
                $asignacion->profesor_id,
                $asignacion->horas_asignadas,
                $id
            );

            // Reactivar
            $asignacion->update(['activo' => true]);

            // Actualizar carga horaria
            $this->actualizarCargaHorariaProfesor($asignacion->profesor_id);

            // Cargar relaciones
            $asignacion->load(['profesor', 'curso', 'grado']);

            return $asignacion;
        });
    }

    /**
     * Desactivar una asignación (alias de delete)
     */
    public function desactivar(int $id): ProfesorCurso
    {
        $this->delete($id);
        return ProfesorCurso::findOrFail($id);
    }

    /**
     * Eliminar todas las asignaciones de un profesor
     */
    public function deleteByProfesor(int $profesorId): int
    {
        return DB::transaction(function () use ($profesorId) {
            $count = ProfesorCurso::where('profesor_id', $profesorId)
                ->where('activo', true)
                ->update(['activo' => false]);

            // Actualizar carga horaria del profesor
            $this->actualizarCargaHorariaProfesor($profesorId);

            return $count;
        });
    }

    /**
     * ========================================
     * ESTADÍSTICAS
     * ========================================
     */

    /**
     * Obtener estadísticas de asignaciones
     */
    public function getEstadisticas(): array
    {
        $totalAsignaciones = ProfesorCurso::where('activo', true)->count();
        $totalProfesores = Profesor::where('estado', 'activo')->count();
        $totalCursos = Curso::where('activo', true)->count();
        $totalGrados = Grado::where('activo', true)->count();

        // Asignaciones por institución
        $porInstitucion = ProfesorCurso::where('activo', true)
            ->select('institucion', DB::raw('count(*) as total'))
            ->groupBy('institucion')
            ->get();

        // Asignaciones por nivel
        $porNivel = ProfesorCurso::where('activo', true)
            ->join('grados', 'profesor_curso.grado_id', '=', 'grados.id')
            ->select('grados.nivel', DB::raw('count(*) as total'))
            ->groupBy('grados.nivel')
            ->get();

        // Asignaciones por rol
        $porRol = ProfesorCurso::where('activo', true)
            ->select('rol', DB::raw('count(*) as total'))
            ->groupBy('rol')
            ->get();

        // Total de horas asignadas
        $totalHoras = ProfesorCurso::where('activo', true)->sum('horas_asignadas');

        return [
            'total_asignaciones' => $totalAsignaciones,
            'total_profesores' => $totalProfesores,
            'total_cursos' => $totalCursos,
            'total_grados' => $totalGrados,
            'total_horas_asignadas' => $totalHoras,
            'por_institucion' => $porInstitucion,
            'por_nivel' => $porNivel,
            'por_rol' => $porRol,
            'promedio_asignaciones_por_profesor' => $totalProfesores > 0
                ? round($totalAsignaciones / $totalProfesores, 2)
                : 0,
            'promedio_horas_por_profesor' => $totalProfesores > 0
                ? round($totalHoras / $totalProfesores, 2)
                : 0
        ];
    }
}
