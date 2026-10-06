<?php
// app/Services/AsignacionService.php

namespace App\Services;

use App\Models\ProfesorCurso;
use App\Models\Profesor;
use App\Models\Curso;
use App\Models\Grado;
use App\Models\Aula;
use App\Models\Horario;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AsignacionService
{
    public function __construct(
        private HorarioRecesoService $recesoService,
        private HistorialService $historialService
    ) {
    }

    /**
     * Obtener todas las asignaciones con sus relaciones
     */
    public function getAll(): Collection
    {
        return ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
            ->orderBy('profesor_id')
            ->orderBy('curso_id')
            ->get();
    }

    /**
     * Obtener asignaciones de un profesor específico
     */
    public function getByProfesor(int $profesorId): Collection
    {
        Profesor::findOrFail($profesorId);

        return ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
            ->where('profesor_id', $profesorId)
            ->where('activo', true)
            ->orderBy('institucion')
            ->orderBy('grado_id')
            ->orderBy('curso_id')
            ->get();
    }

    /**
     * Obtener asignaciones de un curso específico
     */
    public function getByCurso(int $cursoId): Collection
    {
        Curso::findOrFail($cursoId);

        return ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
            ->where('curso_id', $cursoId)
            ->where('activo', true)
            ->orderBy('institucion')
            ->orderBy('grado_id')
            ->orderBy('profesor_id')
            ->get();
    }

    /**
     * Obtener asignaciones de un grado específico
     */
    public function getByGrado(int $gradoId): Collection
    {
        Grado::findOrFail($gradoId);

        return ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
            ->where('grado_id', $gradoId)
            ->where('activo', true)
            ->orderBy('institucion')
            ->orderBy('curso_id')
            ->orderBy('profesor_id')
            ->get();
    }

    /**
     * Obtener asignaciones por institución
     */
    public function getByInstitucion(string $institucion): Collection
    {
        return ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
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
    private function validateCursoYGrado(int $cursoId, int $gradoId, ?string $institucion = null): void
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

        if ($institucion) {
            $this->validateInstitucionAcademica($institucion, $curso, $grado);
        }
    }

    private function validateInstitucionAcademica(string $institucion, Curso $curso, Grado $grado): void
    {
        $cursoNivel = strtolower((string) ($curso->nivel ?? ''));
        $gradoNivel = strtolower((string) ($grado->nivel ?? ''));

        if ($institucion === 'academia') {
            if ($gradoNivel !== 'academia') {
                throw ValidationException::withMessages([
                    'grado_id' => 'Para Academia solo puedes seleccionar grados de Academia.'
                ]);
            }

            if (!in_array($cursoNivel, ['academia', 'todos'], true)) {
                throw ValidationException::withMessages([
                    'curso_id' => 'Para Academia solo puedes seleccionar cursos de Academia.'
                ]);
            }

            return;
        }

        if ($institucion === 'colegio') {
            if (!in_array($gradoNivel, ['primaria', 'secundaria'], true)) {
                throw ValidationException::withMessages([
                    'grado_id' => 'Para Colegio solo puedes seleccionar grados de Primaria o Secundaria.'
                ]);
            }

            if (!in_array($cursoNivel, ['primaria', 'secundaria', 'todos'], true)) {
                throw ValidationException::withMessages([
                    'curso_id' => 'Para Colegio solo puedes seleccionar cursos de Primaria o Secundaria.'
                ]);
            }
        }
    }

    private function validateAula(int $aulaId, int $gradoId): void
    {
        $aula = Aula::find($aulaId);

        if (!$aula) {
            throw ValidationException::withMessages([
                'aula_id' => 'El aula seleccionada no existe'
            ]);
        }

        if (!$aula->activo) {
            throw ValidationException::withMessages([
                'aula_id' => 'El aula seleccionada no está activa'
            ]);
        }

        Grado::findOrFail($gradoId);
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

    private function validateConfiguracionRecesos(int $gradoId, string $institucion): void
    {
        $grado = Grado::findOrFail($gradoId);
        $turno = $grado->turno ?: 'completo';

        $this->recesoService->validarConfiguracionLista(
            $institucion,
            $grado->nivel,
            $turno
        );
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
            $this->validateCursoYGrado(
                $data['curso_id'],
                $data['grado_id'],
                $data['institucion'] ?? 'colegio'
            );

            // 3. Validar que el aula exista y sea compatible con el grado
            $this->validateAula($data['aula_id'], $data['grado_id']);

            // 4. Validar que no exista duplicado
            $this->validateUnique($data);

            // 5. Validar carga horaria del profesor
            $this->validateCargaHoraria($data['profesor_id'], $data['horas_asignadas']);

            // 6. Validar configuración de recesos antes de programar
            $this->validateConfiguracionRecesos(
                $data['grado_id'],
                $data['institucion'] ?? 'colegio'
            );

            // 7. Crear la asignación
            $asignacion = ProfesorCurso::create($data);

            // 8. Actualizar carga horaria actual del profesor
            $this->actualizarCargaHorariaProfesor($data['profesor_id']);

            // 9. Cargar relaciones
            $asignacion->load(['profesor', 'curso', 'grado', 'aula']);

            $this->registrarAuditoriaAsignacion(
                'crear',
                null,
                $asignacion,
                'Asignación creada'
            );

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
            $datosAnteriores = $this->snapshotAsignacion($asignacion);

            // Obtener valores actuales
            $profesorIdActual = $asignacion->profesor_id;
            $cursoIdActual = $asignacion->curso_id;
            $gradoIdActual = $asignacion->grado_id;
            $aulaIdActual = $asignacion->aula_id;
            $horasActuales = $asignacion->horas_asignadas;
            $institucionActual = $asignacion->institucion ?? 'colegio';

            // Obtener valores finales (usar actuales si no se envían)
            $nuevoProfesorId = $data['profesor_id'] ?? $profesorIdActual;
            $nuevoCursoId = $data['curso_id'] ?? $cursoIdActual;
            $nuevoGradoId = $data['grado_id'] ?? $gradoIdActual;
            $nuevoAulaId = $data['aula_id'] ?? $aulaIdActual;
            $nuevasHoras = $data['horas_asignadas'] ?? $horasActuales;
            $nuevaInstitucion = $data['institucion'] ?? $institucionActual;

            // 1. Validar que los IDs existan (si cambiaron)
            if (isset($data['profesor_id']) && $data['profesor_id'] != $profesorIdActual) {
                $this->validateProfesorCurso($data);
            }

            if (isset($data['curso_id']) || isset($data['grado_id'])) {
                $this->validateCursoYGrado(
                    $data['curso_id'] ?? $cursoIdActual,
                    $data['grado_id'] ?? $gradoIdActual,
                    $nuevaInstitucion
                );
            } elseif (isset($data['institucion'])) {
                $this->validateCursoYGrado(
                    $nuevoCursoId,
                    $nuevoGradoId,
                    $nuevaInstitucion
                );
            }

            if (!$nuevoAulaId) {
                throw ValidationException::withMessages([
                    'aula_id' => 'Selecciona un aula para esta asignación.'
                ]);
            }

            $this->validateAula((int) $nuevoAulaId, (int) $nuevoGradoId);

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

            // 4. Validar configuración de recesos antes de actualizar
            $this->validateConfiguracionRecesos(
                $nuevoGradoId,
                $nuevaInstitucion
            );

            // 5. Actualizar la asignación
            $asignacion->update($data);

            $cambioIdentidad =
                $nuevoProfesorId != $profesorIdActual ||
                $nuevoCursoId != $cursoIdActual ||
                $nuevoGradoId != $gradoIdActual ||
                $nuevaInstitucion != $institucionActual;

            $cambioAula =
                (int) $nuevoAulaId !== (int) $aulaIdActual;

            $quedaActiva = array_key_exists('activo', $data)
                ? (bool) $data['activo']
                : (bool) $asignacion->activo;

            if (!$quedaActiva) {
                $this->eliminarHorariosDeAsignacion(
                    $nuevoProfesorId,
                    $nuevoCursoId,
                    $nuevoGradoId,
                    $nuevaInstitucion
                );
            } elseif ($cambioIdentidad || $cambioAula) {
                $this->eliminarHorariosDeAsignacion(
                    $profesorIdActual,
                    $cursoIdActual,
                    $gradoIdActual,
                    $institucionActual
                );
            } elseif ($nuevasHoras < $horasActuales) {
                $this->ajustarHorariosAhorasAsignadas(
                    $nuevoProfesorId,
                    $nuevoCursoId,
                    $nuevoGradoId,
                    $nuevaInstitucion,
                    $nuevasHoras
                );
            }

            // 6. Actualizar carga horaria de ambos profesores (si cambió)
            if ($nuevoProfesorId != $profesorIdActual) {
                $this->actualizarCargaHorariaProfesor($profesorIdActual);
                $this->actualizarCargaHorariaProfesor($nuevoProfesorId);
            } else {
                $this->actualizarCargaHorariaProfesor($nuevoProfesorId);
            }

            // 7. Cargar relaciones
            $asignacion->load(['profesor', 'curso', 'grado', 'aula']);

            $accionAuditoria = $quedaActiva ? 'actualizar' : 'eliminar';
            $motivoAuditoria = $quedaActiva
                ? 'Asignación actualizada'
                : 'Asignación desactivada';

            $this->registrarAuditoriaAsignacion(
                $accionAuditoria,
                $datosAnteriores,
                $asignacion,
                $motivoAuditoria
            );

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
            $datosAnteriores = $this->snapshotAsignacion($asignacion);

            // En lugar de eliminar, desactivar
            $asignacion->update(['activo' => false]);

            $this->eliminarHorariosDeAsignacion(
                $asignacion->profesor_id,
                $asignacion->curso_id,
                $asignacion->grado_id,
                $asignacion->institucion ?? 'colegio'
            );

            // Actualizar carga horaria del profesor
            $this->actualizarCargaHorariaProfesor($asignacion->profesor_id);

            $asignacion->refresh()->load(['profesor', 'curso', 'grado', 'aula']);

            $this->registrarAuditoriaAsignacion(
                'eliminar',
                $datosAnteriores,
                $asignacion,
                'Asignación desactivada'
            );

            return true;
        });
    }

    /**
     * Eliminar definitivamente una asignación y sus horarios vinculados.
     */
    public function deletePermanent(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $asignacion = ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
                ->findOrFail($id);

            $datosAnteriores = $this->snapshotAsignacion($asignacion);
            $profesorId = $asignacion->profesor_id;

            $this->eliminarHorariosDeAsignacion(
                $asignacion->profesor_id,
                $asignacion->curso_id,
                $asignacion->grado_id,
                $asignacion->institucion ?? 'colegio'
            );

            $asignacion->delete();

            $this->actualizarCargaHorariaProfesor($profesorId);

            $this->historialService->registrarEvento(
                'asignaciones',
                'eliminar',
                $datosAnteriores,
                null,
                'Asignación eliminada definitivamente'
            );

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
            $datosAnteriores = $this->snapshotAsignacion($asignacion);

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

            if (!$asignacion->aula_id) {
                throw ValidationException::withMessages([
                    'aula_id' => 'Selecciona un aula antes de reactivar esta asignación.'
                ]);
            }

            $this->validateAula(
                $asignacion->aula_id,
                $asignacion->grado_id
            );

            $this->validateConfiguracionRecesos(
                $asignacion->grado_id,
                $asignacion->institucion ?? 'colegio'
            );

            // Reactivar
            $asignacion->update(['activo' => true]);

            // Actualizar carga horaria
            $this->actualizarCargaHorariaProfesor($asignacion->profesor_id);

            // Cargar relaciones
            $asignacion->load(['profesor', 'curso', 'grado', 'aula']);

            $this->registrarAuditoriaAsignacion(
                'restaurar',
                $datosAnteriores,
                $asignacion,
                'Asignación reactivada'
            );

            return $asignacion;
        });
    }

    /**
     * Desactivar una asignación (alias de delete)
     */
    public function desactivar(int $id): ProfesorCurso
    {
        $this->delete($id);
        return ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
            ->findOrFail($id);
    }

    /**
     * Eliminar todas las asignaciones de un profesor
     */
    public function deleteByProfesor(int $profesorId): int
    {
        return DB::transaction(function () use ($profesorId) {
            $asignaciones = ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])
                ->where('profesor_id', $profesorId)
                ->where('activo', true)
                ->get();

            $count = $asignaciones->count();

            $asignaciones->each(function (ProfesorCurso $asignacion) {
                $datosAnteriores = $this->snapshotAsignacion($asignacion);
                $asignacion->update(['activo' => false]);
                $asignacion->refresh()->load(['profesor', 'curso', 'grado', 'aula']);

                $this->registrarAuditoriaAsignacion(
                    'eliminar',
                    $datosAnteriores,
                    $asignacion,
                    'Asignación desactivada por limpieza de profesor'
                );
            });

            $horarios = Horario::with(['profesor', 'curso', 'grado', 'aula'])
                ->where('profesor_id', $profesorId)
                ->where('estado', 'activo')
                ->get();

            $this->eliminarHorariosConAuditoria(
                $horarios,
                'Limpieza de horarios por profesor'
            );

            // Actualizar carga horaria del profesor
            $this->actualizarCargaHorariaProfesor($profesorId);

            return $count;
        });
    }

    /**
     * Quitar del horario las clases que pertenecen a una asignación.
     */
    private function eliminarHorariosDeAsignacion(
        int $profesorId,
        int $cursoId,
        int $gradoId,
        string $institucion
    ): int {
        $horarios = Horario::with(['profesor', 'curso', 'grado', 'aula'])
            ->where('profesor_id', $profesorId)
            ->where('curso_id', $cursoId)
            ->where('grado_id', $gradoId)
            ->where('institucion', $institucion)
            ->where('estado', 'activo')
            ->get();

        return $this->eliminarHorariosConAuditoria(
            $horarios,
            'Horario eliminado por cambio de asignación'
        );
    }

    /**
     * Si se reducen horas, elimina las clases sobrantes para que el horario
     * refleje exactamente la asignación actual.
     */
    private function ajustarHorariosAhorasAsignadas(
        int $profesorId,
        int $cursoId,
        int $gradoId,
        string $institucion,
        int $horasAsignadas
    ): void {
        $horarios = Horario::where('profesor_id', $profesorId)
            ->where('curso_id', $cursoId)
            ->where('grado_id', $gradoId)
            ->where('institucion', $institucion)
            ->where('estado', 'activo')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        if ($horarios->count() <= $horasAsignadas) {
            return;
        }

        $horarios
            ->slice($horasAsignadas)
            ->each(function (Horario $horario) {
                $this->eliminarHorariosConAuditoria(
                    collect([$horario]),
                    'Horario eliminado por reducción de horas asignadas'
                );
            });
    }

    private function registrarAuditoriaAsignacion(
        string $accion,
        ?array $datosAnteriores,
        ProfesorCurso $asignacion,
        string $motivo
    ): void {
        $this->historialService->registrarEvento(
            'asignaciones',
            $accion,
            $datosAnteriores,
            $this->snapshotAsignacion($asignacion),
            $motivo
        );
    }

    private function snapshotAsignacion(ProfesorCurso $asignacion): array
    {
        $asignacion->loadMissing(['profesor', 'curso', 'grado', 'aula']);

        return array_merge($asignacion->toArray(), [
            'modulo' => 'asignaciones',
            'tipo_registro' => 'asignacion',
            'profesor_nombre' => $asignacion->profesor?->nombre_completo,
            'curso_nombre' => $asignacion->curso?->nombre,
            'grado_nombre' => $asignacion->grado?->nombre_completo,
            'aula_nombre' => $asignacion->aula?->nombre,
            'institucion' => $asignacion->institucion ?? 'colegio',
        ]);
    }

    private function snapshotHorario(Horario $horario): array
    {
        $horario->loadMissing(['profesor', 'curso', 'grado', 'aula']);

        return array_merge($horario->toArray(), [
            'modulo' => 'horarios',
            'tipo_registro' => 'horario',
            'profesor_nombre' => $horario->profesor?->nombre_completo,
            'curso_nombre' => $horario->curso?->nombre,
            'grado_nombre' => $horario->grado?->nombre_completo,
            'aula_nombre' => $horario->aula?->nombre,
        ]);
    }

    private function eliminarHorariosConAuditoria(
        \Illuminate\Support\Collection $horarios,
        string $motivo
    ): int {
        $eliminados = 0;

        foreach ($horarios as $horario) {
            $this->historialService->registrarCambio(
                $horario->id,
                'eliminar',
                $this->snapshotHorario($horario),
                null,
                $motivo
            );

            $horario->forceDelete();
            $eliminados++;
        }

        return $eliminados;
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
