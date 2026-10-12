<?php
// app/Services/HistorialService.php

namespace App\Services;

use App\Models\Aula;
use App\Models\Curso;
use App\Models\DisponibilidadProfesor;
use App\Models\Grado;
use App\Models\HistorialCambio;
use App\Models\Horario;
use App\Models\Profesor;
use App\Models\ProfesorCurso;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HistorialService
{
    /**
     * Registrar un cambio en el historial
     */
    public function registrarCambio(
        ?int $horarioId,
        string $accion,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
        ?string $motivo = null,
        ?int $usuarioId = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): HistorialCambio {
        return HistorialCambio::create([
            'horario_id' => $horarioId,
            'usuario_id' => $usuarioId ?? auth()->id(),
            'accion' => $accion,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'motivo' => $motivo,
            'ip_usuario' => $ip ?? request()->ip(),
            'user_agent' => $userAgent ?? request()->userAgent(),
        ]);
    }

    /**
     * Registrar eventos generales del sistema en el mismo historial.
     */
    public function registrarEvento(
        string $modulo,
        string $accion,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
        ?string $motivo = null,
        ?int $usuarioId = null
    ): HistorialCambio {
        $datosAnteriores = $this->prepararSnapshotEvento($modulo, $datosAnteriores);
        $datosNuevos = $this->prepararSnapshotEvento($modulo, $datosNuevos);

        return $this->registrarCambio(
            null,
            $accion,
            $datosAnteriores,
            $datosNuevos,
            $motivo ?? "Evento de {$modulo}",
            $usuarioId
        );
    }

    private function prepararSnapshotEvento(string $modulo, ?array $datos): ?array
    {
        if ($datos === null) {
            return null;
        }

        $datos['modulo'] = $datos['modulo'] ?? $modulo;
        $datos['tipo_registro'] = $datos['tipo_registro'] ?? $modulo;

        return $datos;
    }

    /**
     * Obtener historial de un horario específico
     */
    public function getHistorialHorario(int $horarioId): \Illuminate\Database\Eloquent\Collection
    {
        return HistorialCambio::deHorario($horarioId)
            ->with('usuario')
            ->recientes()
            ->get();
    }

    /**
     * Obtener historial de todos los horarios con filtros
     */
    public function getHistorialGeneral(array $filtros = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = HistorialCambio::with([
            'horario.profesor',
            'horario.curso',
            'horario.grado',
            'horario.aula',
            'usuario'
        ]);

        // Filtros existentes
        if (!empty($filtros['accion'])) {
            $query->porAccion($filtros['accion']);
        }

        if (!empty($filtros['usuario_id'])) {
            $query->porUsuario($filtros['usuario_id']);
        }

        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $query->porFecha($filtros['fecha_inicio'], $filtros['fecha_fin']);
        }

        if (!empty($filtros['profesor_id'])) {
            $query->porProfesor($filtros['profesor_id']);
        }

        // NUEVOS FILTROS
        if (!empty($filtros['grado_id'])) {
            $query->porGrado($filtros['grado_id']);
        }

        if (!empty($filtros['curso_id'])) {
            $query->porCurso($filtros['curso_id']);
        }

        if (!empty($filtros['aula_id'])) {
            $query->porAula($filtros['aula_id']);
        }

        if (!empty($filtros['institucion'])) {
            $query->porInstitucion($filtros['institucion']);
        }

        if (!empty($filtros['horario_id'])) {
            $query->porHorario($filtros['horario_id']);
        }

        return $query->recientes()
            ->limit($filtros['limit'] ?? 100)
            ->get();
    }

    /**
     * Revertir un cambio registrado en el historial.
     *
     * Los eventos de horarios restauran la clase; los eventos de otros módulos
     * (profesores, cursos, grados, aulas, disponibilidad y asignaciones)
     * restauran los valores anteriores o reactivan el registro.
     */
    public function revertirCambio(int $historialId, ?string $motivo = null): object
    {
        return DB::transaction(function () use ($historialId, $motivo) {

            $registro = HistorialCambio::findOrFail($historialId);

            $modulo = $registro->datos_nuevos['modulo']
                ?? $registro->datos_anteriores['modulo']
                ?? null;

            if ($modulo === 'horarios' || $registro->horario_id !== null) {
                return $this->revertirHorario($registro, $motivo);
            }

            return $this->revertirEventoModulo($registro, $modulo, $motivo);
        });
    }

    /**
     * Revertir un evento de horario (restaura la clase).
     */
    private function revertirHorario(HistorialCambio $registro, ?string $motivo): Horario
    {
        $horario = $registro->horario_id
            ? Horario::withTrashed()->find($registro->horario_id)
            : null;

        if (!$horario) {
            throw ValidationException::withMessages([
                'historial' => 'El horario de este registro ya no existe, así que no hay nada que revertir.'
            ]);
        }

            // Guardar el estado actual antes de revertir
            $estadoActual = $horario->toArray();

            // Si es una eliminación, restaurar
            if ($registro->accion === HistorialCambio::ACCION_ELIMINAR) {
                if ($horario->trashed()) {
                    $horario->restore();
                }
                $horario->update(['estado' => 'activo']);

                $this->registrarCambio(
                    $horario->id,
                    HistorialCambio::ACCION_RESTAURAR,
                    $estadoActual,
                    $horario->fresh()->toArray(),
                    $motivo ?? 'Revertido desde historial'
                );

                return $horario->fresh();
            }

            // Si es una actualización o creación, restaurar datos anteriores
            if ($registro->accion === HistorialCambio::ACCION_ACTUALIZAR && $registro->datos_anteriores) {

                // Si el horario está eliminado, restaurarlo primero
                if ($horario->trashed()) {
                    $horario->restore();
                }

                // Limpiar campos que no deben actualizarse
                $datosAnteriores = $registro->datos_anteriores;
                unset(
                    $datosAnteriores['id'],
                    $datosAnteriores['created_at'],
                    $datosAnteriores['updated_at'],
                    $datosAnteriores['deleted_at']
                );

                $horario->update($datosAnteriores);

                $this->registrarCambio(
                    $horario->id,
                    HistorialCambio::ACCION_REVERTIR,
                    $estadoActual,
                    $horario->toArray(),
                    $motivo ?? 'Revertido desde historial'
                );

                return $horario->fresh();
            }

            // Si es una creación, eliminar y liberar el bloque horario
            if ($registro->accion === HistorialCambio::ACCION_CREAR) {
                $this->registrarCambio(
                    $horario->id,
                    HistorialCambio::ACCION_ELIMINAR,
                    $estadoActual,
                    null,
                    $motivo ?? 'Revertido desde historial (creación)'
                );

                $horario->forceDelete();

                return $horario;
            }

            throw ValidationException::withMessages([
                'historial' => 'No se puede revertir esta acción'
            ]);
    }

    /**
     * Revertir un evento de los módulos de catálogo.
     */
    private function revertirEventoModulo(HistorialCambio $registro, ?string $modulo, ?string $motivo): object
    {
        $clase = $this->modeloDelModulo($modulo);

        if (!$clase) {
            throw ValidationException::withMessages([
                'historial' => 'Este registro no se puede revertir desde el historial.'
            ]);
        }

        if ($registro->accion === HistorialCambio::ACCION_CREAR) {
            throw ValidationException::withMessages([
                'historial' => 'Una creación de este módulo se deshace desde su propia pantalla, no desde el historial.'
            ]);
        }

        $id = $registro->datos_nuevos['id']
            ?? $registro->datos_anteriores['id']
            ?? null;

        $modelo = $id ? $this->buscarRegistro($clase, (int) $id) : null;

        if (!$modelo) {
            throw ValidationException::withMessages([
                'historial' => 'El registro original ya no existe, así que no hay nada que revertir.'
            ]);
        }

        $estadoActual = $modelo->toArray();

        if ($registro->accion === HistorialCambio::ACCION_ACTUALIZAR && $registro->datos_anteriores) {

            $modelo->update($this->datosRestaurables($registro->datos_anteriores));

        } elseif ($registro->accion === HistorialCambio::ACCION_ELIMINAR) {

            $this->reactivarRegistro($modelo);

        } else {

            throw ValidationException::withMessages([
                'historial' => 'No se puede revertir esta acción.'
            ]);
        }

        $modelo->refresh();

        $this->registrarEvento(
            (string) $modulo,
            HistorialCambio::ACCION_REVERTIR,
            $estadoActual,
            $modelo->toArray(),
            $motivo ?? 'Revertido desde historial'
        );

        return $modelo;
    }

    /**
     * Modelo Eloquent asociado a cada módulo del historial.
     */
    private function modeloDelModulo(?string $modulo): ?string
    {
        return match ($modulo) {
            'profesores' => Profesor::class,
            'cursos' => Curso::class,
            'grados' => Grado::class,
            'aulas' => Aula::class,
            'asignaciones' => ProfesorCurso::class,
            'disponibilidad' => DisponibilidadProfesor::class,
            default => null,
        };
    }

    /**
     * Buscar el registro incluso si está eliminado (soft delete).
     */
    private function buscarRegistro(string $clase, int $id): ?object
    {
        $consulta = in_array(SoftDeletes::class, class_uses_recursive($clase), true)
            ? $clase::withTrashed()
            : $clase::query();

        return $consulta->find($id);
    }

    /**
     * Campos del snapshot que sí se pueden volver a escribir.
     */
    private function datosRestaurables(array $datos): array
    {
        $excluidos = [
            'id',
            'created_at',
            'updated_at',
            'deleted_at',
            'modulo',
            'tipo_registro',
            'profesor_nombre',
            'curso_nombre',
            'grado_nombre',
            'aula_nombre',
            'aula_codigo',
            'carga_horaria_actual',
        ];

        return array_diff_key($datos, array_flip($excluidos));
    }

    /**
     * Reactivar un registro eliminado o desactivado.
     */
    private function reactivarRegistro(object $modelo): void
    {
        if (method_exists($modelo, 'trashed') && $modelo->trashed()) {
            $modelo->restore();
        }

        if (array_key_exists('activo', $modelo->getAttributes())) {
            $modelo->activo = true;
        }

        if ($modelo->isDirty()) {
            $modelo->save();
        }
    }

    /**
     * Obtener el historial de versiones de un horario
     */
    public function getVersionesHorario(int $horarioId): array
    {
        $historial = $this->getHistorialHorario($horarioId);

        $versiones = [];
        $versionActual = 1;

        foreach ($historial as $registro) {
            $versiones[] = [
                'version' => $versionActual,
                'fecha' => $registro->created_at,
                'accion' => $registro->accion,
                'accion_formateada' => $registro->accion_formateada,
                'usuario' => $registro->usuario_nombre,
                'datos' => $registro->datos_nuevos ?? $registro->datos_anteriores,
                'motivo' => $registro->motivo,
                'id_historial' => $registro->id,
            ];
            $versionActual++;
        }

        // Agregar la versión actual del horario
        $horario = Horario::withTrashed()->find($horarioId);
        if ($horario) {
            $versiones[] = [
                'version' => $versionActual,
                'fecha' => $horario->updated_at,
                'accion' => $horario->trashed() ? 'eliminado' : 'actual',
                'accion_formateada' => $horario->trashed() ? 'Eliminado' : 'Estado actual',
                'usuario' => 'Sistema',
                'datos' => $horario->toArray(),
                'motivo' => $horario->trashed() ? 'Horario eliminado' : 'Estado actual del horario',
                'id_historial' => null,
            ];
        }

        return array_reverse($versiones);
    }

    /**
     * Restaurar un horario eliminado
     */
    public function restaurarHorario(int $horarioId, ?string $motivo = null): Horario
    {
        return DB::transaction(function () use ($horarioId, $motivo) {
            $horario = Horario::withTrashed()->findOrFail($horarioId);

            if (!$horario->trashed()) {
                throw ValidationException::withMessages([
                    'horario' => 'El horario no está eliminado'
                ]);
            }

            $estadoAnterior = $horario->toArray();
            $horario->restore();
            $horario->update(['estado' => 'activo']);

            $this->registrarCambio(
                $horario->id,
                HistorialCambio::ACCION_RESTAURAR,
                $estadoAnterior,
                $horario->fresh()->toArray(),
                $motivo ?? 'Restauración manual'
            );

            return $horario->fresh();
        });
    }

    /**
     * Obtener estadísticas de cambios
     */
    public function getEstadisticas(): array
    {
        $totalCambios = HistorialCambio::count();

        $cambiosPorAccion = HistorialCambio::select('accion', DB::raw('count(*) as total'))
            ->groupBy('accion')
            ->get()
            ->pluck('total', 'accion')
            ->toArray();

        $cambiosPorDia = HistorialCambio::select(
            DB::raw('DATE(created_at) as fecha'),
            DB::raw('count(*) as total')
        )
        ->groupBy('fecha')
        ->orderBy('fecha', 'desc')
        ->limit(7)
        ->get();

        $cambiosPorUsuario = HistorialCambio::select('usuario_id', DB::raw('count(*) as total'))
            ->with('usuario:id,name')
            ->groupBy('usuario_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $cambiosPorInstitucion = HistorialCambio::with('horario')
            ->get()
            ->groupBy(fn($c) => $this->obtenerInstitucionRegistro($c))
            ->map(fn($group) => $group->count())
            ->toArray();

        $ultimosCambios = HistorialCambio::with(['horario.profesor', 'usuario'])
            ->recientes()
            ->limit(10)
            ->get();

        return [
            'total_cambios' => $totalCambios,
            'cambios_por_accion' => $cambiosPorAccion,
            'cambios_por_dia' => $cambiosPorDia,
            'cambios_por_usuario' => $cambiosPorUsuario,
            'cambios_por_institucion' => $cambiosPorInstitucion,
            'ultimos_cambios' => $ultimosCambios,
        ];
    }

    private function obtenerInstitucionRegistro(HistorialCambio $registro): string
    {
        return $registro->horario?->institucion
            ?? ($registro->datos_nuevos['institucion'] ?? null)
            ?? ($registro->datos_anteriores['institucion'] ?? null)
            ?? 'desconocido';
    }
}
