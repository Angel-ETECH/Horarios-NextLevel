<?php
// app/Services/DisponibilidadService.php

namespace App\Services;

use App\Models\DisponibilidadProfesor;
use App\Models\Profesor;
use Carbon\Carbon;
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
     * Obtener disponibilidades por institución
     */
    public function getByInstitucion(string $institucion): Collection
    {
        return DisponibilidadProfesor::with('profesor')
            ->where('institucion', $institucion)
            ->where('tipo', 'disponible')
            ->orderBy('profesor_id')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener disponibilidades por profesor e institución
     */
    public function getByProfesorEInstitucion(int $profesorId, string $institucion): Collection
    {
        return DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('institucion', $institucion)
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Validar que un rango horario esté dentro de la disponibilidad del profesor
     */
    public function validarRangoEnDisponibilidad(
        int $profesorId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        string $institucion,
        ?int $excludeId = null
    ): bool {
        // Obtener la disponibilidad del profesor para ese día e institución
        $disponibilidades = DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('tipo', 'disponible')
            ->get();

        if ($disponibilidades->isEmpty()) {
            throw ValidationException::withMessages([
                'disponibilidad' => "El profesor no tiene disponibilidad registrada para {$dia} en {$institucion}"
            ]);
        }

        $horaInicioCarbon = Carbon::parse($horaInicio);
        $horaFinCarbon = Carbon::parse($horaFin);

        $rangoValido = false;

        foreach ($disponibilidades as $disponibilidad) {
            $dispInicio = Carbon::parse($disponibilidad->hora_inicio);
            $dispFin = Carbon::parse($disponibilidad->hora_fin);

            // Verificar que el rango COMPLETO esté dentro de la disponibilidad
            if ($horaInicioCarbon >= $dispInicio && $horaFinCarbon <= $dispFin) {
                $rangoValido = true;
                break;
            }
        }

        if (!$rangoValido) {
            throw ValidationException::withMessages([
                'disponibilidad' => "El rango horario {$horaInicio} - {$horaFin} no está completamente dentro de la disponibilidad del profesor para {$dia} en {$institucion}"
            ]);
        }

        return true;
    }

    /**
     * Verificar que no haya solapamientos en la disponibilidad del profesor
     */
    public function verificarSolapamientos(
        int $profesorId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        string $institucion,
        ?int $excludeId = null
    ): bool {
        $query = DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('tipo', 'disponible');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $existentes = $query->get();

        $nuevoInicio = Carbon::parse($horaInicio);
        $nuevoFin = Carbon::parse($horaFin);

        foreach ($existentes as $existente) {
            $existenteInicio = Carbon::parse($existente->hora_inicio);
            $existenteFin = Carbon::parse($existente->hora_fin);

            // Verificar solapamiento REAL:
            // nuevo_inicio < existente_fin Y nuevo_fin > existente_inicio
            if ($nuevoInicio->lt($existenteFin) && $nuevoFin->gt($existenteInicio)) {
                throw ValidationException::withMessages([
                    'solapamiento' => "El rango {$horaInicio} - {$horaFin} se solapa con una disponibilidad existente ({$existente->hora_inicio} - {$existente->hora_fin}) para {$dia} en {$institucion}"
                ]);
            }
        }

        return true;
    }

    /**
     * Validar disponibilidad completa para una clase específica
     */
    public function validarDisponibilidadParaClase(
        int $profesorId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        string $institucion,
        ?int $excludeId = null
    ): bool {
        // 1. Validar que el rango COMPLETO esté dentro de la disponibilidad
        $this->validarRangoEnDisponibilidad($profesorId, $dia, $horaInicio, $horaFin, $institucion, $excludeId);

        // 2. Validar que no haya SOLAPAMIENTOS
        $this->verificarSolapamientos($profesorId, $dia, $horaInicio, $horaFin, $institucion, $excludeId);

        return true;
    }

    /**
     * Validar que el rango horario sea válido
     */
    private function validarRangoHorario(array $data): void
    {
        $horaInicio = Carbon::parse($data['hora_inicio']);
        $horaFin = Carbon::parse($data['hora_fin']);

        // 1. Verificar que hora_inicio < hora_fin
        if ($horaInicio >= $horaFin) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'La hora de inicio debe ser menor que la hora de fin'
            ]);
        }

        // 2. Verificar rango dentro del horario permitido (6:00 - 23:00)
        $minHora = Carbon::parse('06:00');
        $maxHora = Carbon::parse('23:00');

        if ($horaInicio < $minHora || $horaFin > $maxHora) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'El rango horario debe estar entre 06:00 y 23:00'
            ]);
        }

        // 3. Verificar que no exceda las 12 horas continuas
        $horas = $horaInicio->diffInHours($horaFin);
        if ($horas > 12) {
            throw ValidationException::withMessages([
                'hora_fin' => 'El bloque de disponibilidad no puede exceder las 12 horas continuas'
            ]);
        }

        // 4. Verificar que sea un bloque mínimo de 30 minutos
        $minutos = $horaInicio->diffInMinutes($horaFin);
        if ($minutos < 30) {
            throw ValidationException::withMessages([
                'hora_fin' => 'El bloque de disponibilidad debe tener al menos 30 minutos de duración'
            ]);
        }
    }

    /**
     * Validar que no exista una disponibilidad duplicada o solapada
     */
    private function validarUnicidadYSolapamiento(array $data, ?int $excludeId = null): void
    {
        // 1. Validar duplicados exactos (mismo inicio y fin)
        $query = DisponibilidadProfesor::where('profesor_id', $data['profesor_id'])
            ->where('dia_semana', $data['dia_semana'])
            ->where('hora_inicio', $data['hora_inicio'])
            ->where('hora_fin', $data['hora_fin'])
            ->where('institucion', $data['institucion'] ?? 'colegio');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'disponibilidad' => 'Ya existe una disponibilidad con el mismo rango horario para este profesor'
            ]);
        }

        // 2. Validar solapamientos (rangos que se cruzan)
        $this->verificarSolapamientos(
            $data['profesor_id'],
            $data['dia_semana'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['institucion'] ?? 'colegio',
            $excludeId
        );
    }

    /**
     * Crear una nueva disponibilidad
     */
    public function create(array $data): DisponibilidadProfesor
    {
        return DB::transaction(function () use ($data) {
            // Validar que el profesor existe
            $profesor = Profesor::findOrFail($data['profesor_id']);

            // Validar rango horario
            $this->validarRangoHorario($data);

            // Validar unicidad y solapamientos
            $this->validarUnicidadYSolapamiento($data);

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

            // Validar que el profesor existe (si se está cambiando)
            if (isset($data['profesor_id']) && $data['profesor_id'] != $disponibilidad->profesor_id) {
                $profesor = Profesor::findOrFail($data['profesor_id']);
            }

            // Validar rango horario (si se está cambiando)
            if (isset($data['hora_inicio']) || isset($data['hora_fin'])) {
                $mergedData = array_merge($disponibilidad->toArray(), $data);
                $this->validarRangoHorario($mergedData);
            }

            // Validar unicidad y solapamientos (excluyendo el registro actual)
            $mergedData = array_merge($disponibilidad->toArray(), $data);
            $this->validarUnicidadYSolapamiento($mergedData, $id);

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
     * Eliminar disponibilidades por institución
     */
    public function deleteByInstitucion(int $profesorId, string $institucion): int
    {
        return DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('institucion', $institucion)
            ->delete();
    }

    /**
     * ========================================
     * MÉTODOS DE CONSULTA Y BLOQUES
     * ========================================
     */

    /**
     * Verificar si un profesor tiene disponibilidad en un día y hora específicos
     */
    public function verificarDisponibilidadPuntual(
        int $profesorId,
        string $dia,
        string $hora,
        string $institucion = 'colegio'
    ): bool {
        return DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('hora_inicio', '<=', $hora)
            ->where('hora_fin', '>=', $hora)
            ->where('tipo', 'disponible')
            ->exists();
    }

    /**
     * Obtener bloques disponibles para un profesor en un día específico
     */
    public function obtenerBloquesDisponibles(
        int $profesorId,
        string $dia,
        string $institucion = 'colegio',
        ?int $duracionMinutos = null
    ): array {
        // Obtener disponibilidad del profesor
        $disponibilidades = DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('tipo', 'disponible')
            ->orderBy('hora_inicio')
            ->get();

        if ($disponibilidades->isEmpty()) {
            return [];
        }

        // Obtener la configuración de bloques de la BD
        // (usa el primer grado del profesor como referencia para nivel/turno)
        $bloques = [];

        foreach ($disponibilidades as $disponibilidad) {
            $bloques[] = [
                'hora_inicio' => $disponibilidad->hora_inicio,
                'hora_fin' => $disponibilidad->hora_fin,
                'disponibilidad_id' => $disponibilidad->id,
            ];
        }

        return $bloques;
    }

    /**
     * Obtener disponibilidades agrupadas por día para un profesor
     * (Útil para el frontend)
     */
    public function getAgrupadoPorDia(int $profesorId, string $institucion = 'colegio'): array
    {
        $disponibilidades = DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('institucion', $institucion)
            ->where('tipo', 'disponible')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        $agrupado = [];

        foreach ($disponibilidades as $disp) {
            $dia = $disp->dia_semana;
            if (!isset($agrupado[$dia])) {
                $agrupado[$dia] = [];
            }
            $agrupado[$dia][] = [
                'id' => $disp->id,
                'hora_inicio' => $disp->hora_inicio,
                'hora_fin' => $disp->hora_fin,
                'turno' => $disp->turno,
                'tipo' => $disp->tipo,
                'observacion' => $disp->observacion
            ];
        }

        return $agrupado;
    }

    /**
     * Verificar si hay conflictos con horarios existentes
     */
    public function verificarConflictosConHorarios(
        int $profesorId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        string $institucion,
        ?int $excludeHorarioId = null
    ): bool {
        $query = \App\Models\Horario::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('estado', 'activo');

        if ($excludeHorarioId) {
            $query->where('id', '!=', $excludeHorarioId);
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
                    'conflicto' => "El horario {$horaInicio} - {$horaFin} se solapa con una clase existente ({$horario->hora_inicio} - {$horario->hora_fin})"
                ]);
            }
        }

        return true;
    }

    /**
     * ========================================
     * MÉTODOS DE ESTADÍSTICAS
     * ========================================
     */

    /**
     * Obtener estadísticas de disponibilidad
     * (Para dashboards)
     */
    public function getEstadisticas(): array
    {
        $total = DisponibilidadProfesor::count();
        $disponibles = DisponibilidadProfesor::where('tipo', 'disponible')->count();
        $noDisponibles = DisponibilidadProfesor::where('tipo', 'no_disponible')->count();

        $porInstitucion = DisponibilidadProfesor::select('institucion', DB::raw('count(*) as total'))
            ->groupBy('institucion')
            ->get();

        $porDia = DisponibilidadProfesor::select('dia_semana', DB::raw('count(*) as total'))
            ->groupBy('dia_semana')
            ->orderBy('dia_semana')
            ->get();

        return [
            'total' => $total,
            'disponibles' => $disponibles,
            'no_disponibles' => $noDisponibles,
            'por_institucion' => $porInstitucion,
            'por_dia' => $porDia,
            'profesores_con_disponibilidad' => DisponibilidadProfesor::distinct('profesor_id')->count()
        ];
    }
}
