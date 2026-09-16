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
     * Obtener aulas por nivel
     */
    public function getByNivel(string $nivel): Collection
    {
        return Aula::where('activo', true)
            ->where(function ($query) use ($nivel) {
                $query->where('nivel', $nivel)
                      ->orWhere('nivel', 'todos');
            })
            ->orderBy('codigo')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Obtener aulas por nivel y tipo
     */
    public function getByNivelYTipo(string $nivel, string $tipo): Collection
    {
        return Aula::where('activo', true)
            ->where('tipo', $tipo)
            ->where(function ($query) use ($nivel) {
                $query->where('nivel', $nivel)
                      ->orWhere('nivel', 'todos');
            })
            ->orderBy('nombre')
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
     * Obtener aulas por edificio y piso
     */
    public function getByUbicacion(?string $edificio = null, ?string $piso = null): Collection
    {
        $query = Aula::where('activo', true);

        if ($edificio) {
            $query->where('edificio', $edificio);
        }

        if ($piso) {
            $query->where('piso', $piso);
        }

        return $query->orderBy('edificio')
            ->orderBy('piso')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * ========================================
     * DISPONIBILIDAD DE AULAS
     * ========================================
     */

    /**
     * Obtener aulas disponibles en un horario específico
     */
    public function getAulasDisponibles(
        string $dia,
        string $horaInicio,
        string $horaFin,
        ?string $nivel = null,
        ?string $institucion = null,
        ?int $excludeHorarioId = null
    ): Collection {
        $query = Aula::where('activo', true);

        // Filtrar por nivel
        if ($nivel) {
            $query->where(function ($q) use ($nivel) {
                $q->where('nivel', $nivel)
                  ->orWhere('nivel', 'todos');
            });
        }

        // Subconsulta: aulas ocupadas en ese horario
        $ocupadas = \App\Models\Horario::where('dia_semana', $dia)
            ->where('estado', 'activo');

        if ($institucion) {
            $ocupadas->where('institucion', $institucion);
        }

        if ($excludeHorarioId) {
            $ocupadas->where('id', '!=', $excludeHorarioId);
        }

        // SOLAPAMIENTO REAL: inicio_nuevo < fin_existente AND fin_nuevo > inicio_existente
        $ocupadas->where('hora_inicio', '<', $horaFin)
                 ->where('hora_fin', '>', $horaInicio);

        $aulasOcupadasIds = $ocupadas->pluck('aula_id')->unique()->toArray();

        if (!empty($aulasOcupadasIds)) {
            $query->whereNotIn('id', $aulasOcupadasIds);
        }

        return $query->orderBy('nombre')->get();
    }

    /**
     * Alias para compatibilidad (getDisponibles)
     */
    public function getDisponibles(string $dia, string $horaInicio, string $horaFin): Collection
    {
        return $this->getAulasDisponibles($dia, $horaInicio, $horaFin);
    }

    /**
     * Verificar disponibilidad de un aula en un horario específico
     */
    public function verificarDisponibilidadAula(
        int $aulaId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        ?string $institucion = null,
        ?int $excludeId = null
    ): bool {
        $query = \App\Models\Horario::where('aula_id', $aulaId)
            ->where('dia_semana', $dia)
            ->where('estado', 'activo');

        if ($institucion) {
            $query->where('institucion', $institucion);
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        // Solapamiento REAL
        $query->where('hora_inicio', '<', $horaFin)
              ->where('hora_fin', '>', $horaInicio);

        return !$query->exists();
    }

    /**
     * ========================================
     * CRUD
     * ========================================
     */

    /**
     * Crear un aula (con validaciones)
     */
    public function create(array $data): Aula
    {
        return DB::transaction(function () use ($data) {
            // Validar código único
            if (Aula::where('codigo', $data['codigo'])->exists()) {
                throw ValidationException::withMessages([
                    'codigo' => 'Ya existe un aula con este código'
                ]);
            }

            // Normalizar institución por defecto
            if (!isset($data['institucion'])) {
                $data['institucion'] = 'colegio';
            }

            return Aula::create($data);
        });
    }

    /**
     * Actualizar un aula (con validaciones)
     */
    public function update(int $id, array $data): Aula
    {
        return DB::transaction(function () use ($id, $data) {
            $aula = Aula::findOrFail($id);

            // Validar código único (si cambia)
            if (isset($data['codigo']) && $data['codigo'] !== $aula->codigo) {
                if (Aula::where('codigo', $data['codigo'])
                    ->where('id', '!=', $id)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'codigo' => 'Ya existe un aula con este código'
                    ]);
                }
            }

            // Si se reduce la capacidad, verificar que no afecte horarios activos
            if (isset($data['capacidad']) && $data['capacidad'] < $aula->capacidad) {
                $this->validarCapacidadVsHorarios($id, $data['capacidad']);
            }

            $aula->update($data);
            return $aula->fresh();
        });
    }

    /**
     * Validar que la nueva capacidad sea suficiente para los grados asignados
     */
    private function validarCapacidadVsHorarios(int $aulaId, int $nuevaCapacidad): void
    {
        $gradosConHorarios = \App\Models\Horario::where('aula_id', $aulaId)
            ->where('estado', 'activo')
            ->with('grado')
            ->get()
            ->pluck('grado')
            ->unique('id');

        foreach ($gradosConHorarios as $grado) {
            if ($grado && $grado->numero_estudiantes > $nuevaCapacidad) {
                throw ValidationException::withMessages([
                    'capacidad' => "No se puede reducir la capacidad a {$nuevaCapacidad} porque el grado '{$grado->nombre_completo}' tiene {$grado->numero_estudiantes} estudiantes"
                ]);
            }
        }
    }

    /**
     * Eliminar un aula (soft delete)
     */
    public function delete(int $id): bool
    {
        $aula = Aula::findOrFail($id);

        // Verificar si tiene horarios activos
        if ($aula->horarios()->where('estado', 'activo')->exists()) {
            throw ValidationException::withMessages([
                'aula' => 'No se puede eliminar el aula porque tiene horarios activos asignados'
            ]);
        }

        return $aula->delete();
    }

    /**
     * ========================================
     * ESTADÍSTICAS
     * ========================================
     */

    /**
     * Obtener estadísticas de aulas
     */
    public function getEstadisticas(): array
    {
        $total = Aula::count();
        $activas = Aula::where('activo', true)->count();
        $capacidadTotal = Aula::where('activo', true)->sum('capacidad');

        $porTipo = Aula::where('activo', true)
            ->select('tipo', DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->get();

        $porNivel = Aula::where('activo', true)
            ->select('nivel', DB::raw('count(*) as total'))
            ->groupBy('nivel')
            ->get();

        $porInstitucion = Aula::where('activo', true)
            ->select('institucion', DB::raw('count(*) as total'))
            ->groupBy('institucion')
            ->get();

        return [
            'total' => $total,
            'activas' => $activas,
            'inactivas' => $total - $activas,
            'capacidad_total' => $capacidadTotal,
            'promedio_capacidad' => $activas > 0 ? round($capacidadTotal / $activas, 2) : 0,
            'por_tipo' => $porTipo,
            'por_nivel' => $porNivel,
            'por_institucion' => $porInstitucion,
        ];
    }
}
