<?php
// app/Services/DisponibilidadService.php

namespace App\Services;

use App\Models\DisponibilidadProfesor;
use App\Models\Profesor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DisponibilidadService
{
    /**
     * Obtener todas las disponibilidades con sus relaciones
     */
    public function getAll(): Collection
    {
        return DisponibilidadProfesor::with('profesor')
            ->orderBy('profesor_id')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener disponibilidades de un profesor específico
     */
    public function getByProfesor(int $profesorId): Collection
    {
        $profesor = Profesor::findOrFail($profesorId);

        return $profesor->disponibilidades()
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener disponibilidades por día
     */
    public function getByDia(string $dia): Collection
    {
        return DisponibilidadProfesor::with('profesor')
            ->where('dia_semana', $dia)
            ->where('tipo', 'disponible')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener disponibilidades por turno
     */
    public function getByTurno(string $turno): Collection
    {
        return DisponibilidadProfesor::with('profesor')
            ->where('turno', $turno)
            ->where('tipo', 'disponible')
            ->orderBy('profesor_id')
            ->orderBy('dia_semana')
            ->get();
    }

    /**
     * Crear una nueva disponibilidad
     */
    public function create(array $data): DisponibilidadProfesor
    {
        return DB::transaction(function () use ($data) {
            // Validar que no exista una disponibilidad similar
            $this->validateUnique($data);

            // Validar que el profesor existe
            $profesor = Profesor::findOrFail($data['profesor_id']);

            // Validar rango horario
            $this->validateHorario($data);

            // Crear la disponibilidad
            $disponibilidad = DisponibilidadProfesor::create($data);

            // Cargar la relación profesor
            $disponibilidad->load('profesor');

            return $disponibilidad;
        });
    }

    /**
     * Actualizar una disponibilidad existente
     */
    public function update(int $id, array $data): DisponibilidadProfesor
    {
        return DB::transaction(function () use ($id, $data) {
            $disponibilidad = DisponibilidadProfesor::findOrFail($id);

            // Si se está cambiando el profesor, validar
            if (isset($data['profesor_id']) && $data['profesor_id'] != $disponibilidad->profesor_id) {
                $this->validateUnique($data, $id);
                $profesor = Profesor::findOrFail($data['profesor_id']);
            }

            // Validar rango horario
            if (isset($data['hora_inicio']) || isset($data['hora_fin'])) {
                $this->validateHorario(array_merge($disponibilidad->toArray(), $data));
            }

            // Actualizar
            $disponibilidad->update($data);
            $disponibilidad->load('profesor');

            return $disponibilidad;
        });
    }

    /**
     * Eliminar una disponibilidad (soft delete)
     */
    public function delete(int $id): bool
    {
        $disponibilidad = DisponibilidadProfesor::findOrFail($id);
        return $disponibilidad->delete();
    }

    /**
     * Eliminar todas las disponibilidades de un profesor
     */
    public function deleteByProfesor(int $profesorId): int
    {
        $profesor = Profesor::findOrFail($profesorId);
        return $profesor->disponibilidades()->delete();
    }

    /**
     * Validar que no exista una disponibilidad duplicada
     */
    private function validateUnique(array $data, ?int $excludeId = null): void
    {
        $query = DisponibilidadProfesor::where('profesor_id', $data['profesor_id'])
            ->where('dia_semana', $data['dia_semana'])
            ->where('hora_inicio', $data['hora_inicio'])
            ->where('hora_fin', $data['hora_fin']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'disponibilidad' => 'Ya existe una disponibilidad con los mismos datos para este profesor'
            ]);
        }
    }

    /**
     * Validar que el rango horario sea válido
     */
    private function validateHorario(array $data): void
    {
        // Verificar que hora_inicio < hora_fin
        if ($data['hora_inicio'] >= $data['hora_fin']) {
            throw ValidationException::withMessages([
                'hora_fin' => 'La hora de fin debe ser después de la hora de inicio'
            ]);
        }

        // Verificar que el rango no sea demasiado largo (máximo 12 horas)
        $inicio = \Carbon\Carbon::parse($data['hora_inicio']);
        $fin = \Carbon\Carbon::parse($data['hora_fin']);
        $horas = $inicio->diffInHours($fin);

        if ($horas > 12) {
            throw ValidationException::withMessages([
                'hora_fin' => 'El bloque de disponibilidad no puede exceder las 12 horas'
            ]);
        }
    }

    /**
     * Verificar si un profesor tiene disponibilidad en un día y hora específicos
     */
    public function verificarDisponibilidad(int $profesorId, string $dia, string $hora): bool
    {
        return DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('hora_inicio', '<=', $hora)
            ->where('hora_fin', '>=', $hora)
            ->where('tipo', 'disponible')
            ->exists();
    }

    /**
     * Obtener horarios disponibles de un profesor en un día
     */
    public function getBloquesDisponibles(int $profesorId, string $dia): array
    {
        $disponibilidades = DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('tipo', 'disponible')
            ->orderBy('hora_inicio')
            ->get();

        $bloques = [];
        foreach ($disponibilidades as $disp) {
            $bloques[] = [
                'inicio' => $disp->hora_inicio,
                'fin' => $disp->hora_fin,
                'duracion' => $disp->hora_inicio->diffInHours($disp->hora_fin)
            ];
        }

        return $bloques;
    }
}
