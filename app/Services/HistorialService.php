<?php
// app/Services/HistorialService.php

namespace App\Services;

use App\Models\HistorialCambio;
use App\Models\Horario;
use Carbon\Carbon;
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
     * Revertir un horario a una versión anterior
     */
    public function revertirCambio(int $historialId, ?string $motivo = null): Horario
    {
        return DB::transaction(function () use ($historialId, $motivo) {
            $registro = HistorialCambio::lockForUpdate()->findOrFail($historialId);
            $snapshot = $registro->accion === HistorialCambio::ACCION_CREAR
                ? $registro->datos_nuevos
                : $registro->datos_anteriores;
            $horarioId = $this->validarRegistroHorario($registro, $snapshot);
            $horario = Horario::withTrashed()->lockForUpdate()->find($horarioId);
            $estadoActual = $horario?->toArray();

            // Si es una eliminación, restaurar
            if ($registro->accion === HistorialCambio::ACCION_ELIMINAR) {
                if ($horario && !$horario->trashed()) {
                    throw ValidationException::withMessages([
                        'horario' => 'El horario no esta eliminado.',
                    ]);
                }

                $snapshot['estado'] = 'activo';
                $horario = $this->guardarHorarioValidado($horario, $snapshot, $horarioId);

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

                if (!$horario || $horario->trashed()) {
                    $snapshot['estado'] = 'activo';
                }
                $horario = $this->guardarHorarioValidado($horario, $snapshot, $horarioId);

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
                if (!$horario) {
                    throw ValidationException::withMessages([
                        'horario' => 'El horario ya fue eliminado.',
                    ]);
                }

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
        });
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
            $horario = Horario::withTrashed()->lockForUpdate()->find($horarioId);

            if ($horario && !$horario->trashed()) {
                throw ValidationException::withMessages([
                    'horario' => 'El horario no está eliminado'
                ]);
            }

            $estadoAnterior = $horario?->toArray();
            $snapshot = $estadoAnterior;

            if (!$horario) {
                $registro = HistorialCambio::porHorario($horarioId)
                    ->where('accion', HistorialCambio::ACCION_ELIMINAR)
                    ->orderByDesc('id')
                    ->lockForUpdate()
                    ->get()
                    ->first(fn ($registro) => $this->esSnapshotHorario($registro->datos_anteriores));

                if (!$registro) {
                    throw ValidationException::withMessages([
                        'horario' => 'No existe un snapshot de horario eliminado para restaurar.',
                    ]);
                }

                $snapshot = $registro->datos_anteriores;
                if ($this->validarRegistroHorario($registro, $snapshot) !== $horarioId) {
                    throw ValidationException::withMessages([
                        'historial' => 'El snapshot no corresponde al horario solicitado.',
                    ]);
                }
            }

            $snapshot['estado'] = 'activo';
            $horario = $this->guardarHorarioValidado($horario, $snapshot, $horarioId);

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

    private function esSnapshotHorario(?array $snapshot): bool
    {
        if (!$snapshot
            || (isset($snapshot['modulo']) && $snapshot['modulo'] !== 'horarios')
            || (isset($snapshot['tipo_registro'])
                && !in_array($snapshot['tipo_registro'], ['horario', 'horarios'], true))) {
            return false;
        }

        foreach (['profesor_id', 'curso_id', 'grado_id', 'aula_id', 'dia_semana',
            'hora_inicio', 'hora_fin', 'periodo_academico', 'institucion', 'turno'] as $campo) {
            if (!isset($snapshot[$campo]) || $snapshot[$campo] === '') {
                return false;
            }
        }

        return true;
    }

    private function validarRegistroHorario(HistorialCambio $registro, ?array $snapshot): int
    {
        foreach ([$registro->datos_anteriores, $registro->datos_nuevos] as $datos) {
            if ($datos && ((isset($datos['modulo']) && $datos['modulo'] !== 'horarios')
                || (isset($datos['tipo_registro'])
                    && !in_array($datos['tipo_registro'], ['horario', 'horarios'], true)))) {
                throw ValidationException::withMessages([
                    'historial' => 'Solo se pueden revertir o restaurar registros de horarios.',
                ]);
            }
        }

        $horarioId = (int) ($registro->horario_id ?? ($snapshot['id'] ?? 0));
        if (!$this->esSnapshotHorario($snapshot) || $horarioId <= 0
            || (isset($snapshot['id']) && (int) $snapshot['id'] !== $horarioId)) {
            throw ValidationException::withMessages([
                'historial' => 'El registro no contiene un snapshot valido del horario.',
            ]);
        }

        return $horarioId;
    }

    private function guardarHorarioValidado(?Horario $horario, array $snapshot, int $horarioId): Horario
    {
        if (!$this->esSnapshotHorario($snapshot)) {
            throw ValidationException::withMessages([
                'historial' => 'El snapshot del horario esta incompleto.',
            ]);
        }

        $datos = array_intersect_key($snapshot, array_flip((new Horario())->getFillable()));
        $datos['dia_semana'] = match (strtolower(trim($datos['dia_semana']))) {
            'miercoles' => 'miércoles',
            'sabado' => 'sábado',
            default => strtolower(trim($datos['dia_semana'])),
        };

        try {
            foreach (['hora_inicio', 'hora_fin'] as $campo) {
                $datos[$campo] = Carbon::parse($datos[$campo])->format('H:i');
            }
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'historial' => 'El snapshot contiene horas invalidas.',
            ]);
        }

        app(HorarioService::class)->validarParaRestaurar($datos, $horario?->id);

        // Las claves unicas tambien incluyen horarios cancelados o con soft delete.
        $ocupados = Horario::withTrashed()
            ->where('dia_semana', $datos['dia_semana'])
            ->where('hora_inicio', $datos['hora_inicio'])
            ->where('periodo_academico', $datos['periodo_academico'])
            ->where(function ($query) use ($datos) {
                $query->where('profesor_id', $datos['profesor_id'])
                    ->orWhere('aula_id', $datos['aula_id'])
                    ->orWhere('grado_id', $datos['grado_id']);
            });

        if ($horario) {
            $ocupados->where('id', '!=', $horario->id);
        }

        if ($ocupados->exists()) {
            throw ValidationException::withMessages([
                'horario' => 'El bloque esta ocupado por otro horario, incluso cancelado o eliminado.',
            ]);
        }

        if (!$horario) {
            $horario = new Horario();
            $horario->forceFill(['id' => $horarioId]);
        }

        $horario->fill($datos);
        if ($horario->trashed()) {
            $horario->restore();
        } else {
            $horario->save();
        }

        return $horario->fresh();
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
