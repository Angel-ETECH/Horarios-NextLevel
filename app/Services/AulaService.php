<?php
// app/Services/AulaService.php

namespace App\Services;

use App\Models\Aula;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AulaService
{
    /**
     * Obtener todas las aulas
     */
    public function getAll(): Collection
    {
        return Aula::orderBy('codigo')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Obtener aulas disponibles por nivel
     */
    public function getByNivel(string $nivel): Collection
    {
        return Aula::where('nivel', $nivel)
            ->orWhere('nivel', 'todos')
            ->where('activo', true)
            ->orderBy('codigo')
            ->get();
    }

    /**
     * Obtener aulas por tipo
     */
    public function getByTipo(string $tipo): Collection
    {
        return Aula::where('tipo', $tipo)
            ->where('activo', true)
            ->orderBy('codigo')
            ->get();
    }

    /**
     * Obtener aulas con capacidad suficiente
     */
    public function getConCapacidad(int $capacidadMinima): Collection
    {
        return Aula::where('capacidad', '>=', $capacidadMinima)
            ->where('activo', true)
            ->orderBy('capacidad')
            ->get();
    }

    /**
     * Obtener aulas disponibles en un día y hora específicos
     */
    public function getDisponibles(string $dia, string $horaInicio, string $horaFin): Collection
    {
        // Obtener IDs de aulas ocupadas
        $aulasOcupadas = \App\Models\Horario::where('dia_semana', $dia)
            ->where('estado', 'activo')
            ->where(function($query) use ($horaInicio, $horaFin) {
                $query->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                    ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                    ->orWhere(function($sub) use ($horaInicio, $horaFin) {
                        $sub->where('hora_inicio', '<=', $horaInicio)
                            ->where('hora_fin', '>=', $horaFin);
                    });
            })
            ->pluck('aula_id')
            ->toArray();

        return Aula::whereNotIn('id', $aulasOcupadas)
            ->where('activo', true)
            ->orderBy('codigo')
            ->get();
    }

    /**
     * Crear una nueva aula
     */
    public function create(array $data): Aula
    {
        return DB::transaction(function () use ($data) {
            // Validar que el código no exista
            if (Aula::where('codigo', $data['codigo'])->exists()) {
                throw ValidationException::withMessages([
                    'codigo' => 'Ya existe un aula con este código'
                ]);
            }

            return Aula::create($data);
        });
    }

    /**
     * Actualizar un aula existente
     */
    public function update(int $id, array $data): Aula
    {
        return DB::transaction(function () use ($id, $data) {
            $aula = Aula::findOrFail($id);

            // Si se cambia el código, verificar unicidad
            if (isset($data['codigo']) && $data['codigo'] != $aula->codigo) {
                if (Aula::where('codigo', $data['codigo'])->exists()) {
                    throw ValidationException::withMessages([
                        'codigo' => 'Ya existe un aula con este código'
                    ]);
                }
            }

            $aula->update($data);
            return $aula;
        });
    }

    /**
     * Eliminar un aula (soft delete)
     */
    public function delete(int $id): bool
    {
        $aula = Aula::findOrFail($id);

        // Verificar si el aula tiene horarios asignados
        if ($aula->horarios()->where('estado', 'activo')->exists()) {
            throw ValidationException::withMessages([
                'aula' => 'No se puede eliminar el aula porque tiene horarios activos asignados'
            ]);
        }

        return $aula->delete();
    }

    /**
     * Obtener estadísticas de aulas
     */
    public function getEstadisticas(): array
    {
        $totalAulas = Aula::count();
        $aulasActivas = Aula::where('activo', true)->count();
        $capacidadTotal = Aula::where('activo', true)->sum('capacidad');
        $capacidadPromedio = $aulasActivas > 0 ? round($capacidadTotal / $aulasActivas, 2) : 0;

        $aulasPorTipo = Aula::where('activo', true)
            ->select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->get();

        $aulasPorNivel = Aula::where('activo', true)
            ->select('nivel', DB::raw('count(*) as total'))
            ->groupBy('nivel')
            ->get();

        return [
            'total_aulas' => $totalAulas,
            'aulas_activas' => $aulasActivas,
            'capacidad_total' => $capacidadTotal,
            'capacidad_promedio' => $capacidadPromedio,
            'aulas_por_tipo' => $aulasPorTipo,
            'aulas_por_nivel' => $aulasPorNivel,
        ];
    }
}
