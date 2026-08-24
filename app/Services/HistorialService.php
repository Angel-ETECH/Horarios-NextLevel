<?php
// app/Services/HistorialService.php

namespace App\Services;

use App\Models\HistorialCambio;
use App\Models\Horario;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HistorialService
{
    /**
     * Registrar un cambio en el historial
     */
    public function registrarCambio(
        int $horarioId,
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
     * Obtener historial de un horario específico
     */
    public function getHistorialHorario(int $horarioId): \Illuminate\Database\Eloquent\Collection
    {
        return HistorialCambio::deHorario($horarioId)
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener historial de todos los horarios con filtros
     */
    public function getHistorialGeneral(array $filtros = []): \Illuminate\Database\Eloquent\Collection
    {
        $query = HistorialCambio::with(['horario.profesor', 'horario.curso', 'usuario']);

        if (isset($filtros['accion'])) {
            $query->accion($filtros['accion']);
        }

        if (isset($filtros['usuario_id'])) {
            $query->usuario($filtros['usuario_id']);
        }

        if (isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin'])) {
            $query->fecha($filtros['fecha_inicio'], $filtros['fecha_fin']);
        }

        if (isset($filtros['profesor_id'])) {
            $query->whereHas('horario', function($q) use ($filtros) {
                $q->where('profesor_id', $filtros['profesor_id']);
            });
        }

        return $query->orderBy('created_at', 'desc')
            ->limit($filtros['limit'] ?? 100)
            ->get();
    }

    /**
     * Revertir un horario a una versión anterior
     */
    public function revertirCambio(int $historialId, ?string $motivo = null): Horario
    {
        return DB::transaction(function () use ($historialId, $motivo) {
            $registro = HistorialCambio::findOrFail($historialId);
            $horario = Horario::findOrFail($registro->horario_id);

            // Guardar el estado actual antes de revertir
            $estadoActual = $horario->toArray();

            // Si es una eliminación, restaurar
            if ($registro->accion === 'eliminar') {
                $horario->restore();
                $horario->update(['estado' => 'activo']);

                $this->registrarCambio(
                    $horario->id,
                    'restaurar',
                    $estadoActual,
                    $horario->toArray(),
                    $motivo ?? 'Revertido desde historial'
                );

                return $horario;
            }

            // Si es una actualización o creación, restaurar datos anteriores
            if ($registro->accion === 'actualizar' && $registro->datos_anteriores) {
                $horario->update($registro->datos_anteriores);

                $this->registrarCambio(
                    $horario->id,
                    'revertir',
                    $estadoActual,
                    $horario->toArray(),
                    $motivo ?? 'Revertido desde historial'
                );

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
                'usuario' => $registro->usuario?->name ?? 'Sistema',
                'datos' => $registro->datos_nuevos ?? $registro->datos_anteriores,
                'motivo' => $registro->motivo,
                'id_historial' => $registro->id,
            ];
            $versionActual++;
        }

        // Agregar la versión actual del horario
        $horario = Horario::find($horarioId);
        if ($horario) {
            $versiones[] = [
                'version' => $versionActual,
                'fecha' => $horario->updated_at,
                'accion' => 'actual',
                'usuario' => 'Sistema',
                'datos' => $horario->toArray(),
                'motivo' => 'Estado actual del horario',
                'id_historial' => null,
            ];
        }

        return array_reverse($versiones);
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

        $ultimosCambios = HistorialCambio::with(['horario.profesor', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_cambios' => $totalCambios,
            'cambios_por_accion' => $cambiosPorAccion,
            'cambios_por_dia' => $cambiosPorDia,
            'ultimos_cambios' => $ultimosCambios,
        ];
    }
}
