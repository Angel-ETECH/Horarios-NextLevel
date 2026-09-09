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
     * (soporte para duración personalizada y por institución)
     */
    public function obtenerBloquesDisponibles(
        int $profesorId,
        string $dia,
        string $institucion = 'colegio',
        int $duracionMinutos = 60
    ): array {
        $disponibilidades = DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('tipo', 'disponible')
            ->orderBy('hora_inicio')
            ->get();

        $bloques = [];
        $breaks = $this->obtenerBreaksPorInstitucion($institucion, $dia, $horaReferencia = null);

        foreach ($disponibilidades as $disponibilidad) {
            $inicio = Carbon::parse($disponibilidad->hora_inicio);
            $fin = Carbon::parse($disponibilidad->hora_fin);

            $horaActual = $inicio->copy();

            while ($horaActual->copy()->addMinutes($duracionMinutos) <= $fin) {
                $horaInicioBloque = $horaActual->copy();
                $horaFinBloque = $horaActual->copy()->addMinutes($duracionMinutos);

                // Verificar si el bloque cae en un receso
                if ($this->bloqueCaeEnReceso($horaInicioBloque, $horaFinBloque, $breaks)) {
                    // Saltar este bloque y continuar
                    $horaActual->addMinutes($duracionMinutos);
                    continue;
                }

                $bloques[] = [
                    'hora_inicio' => $horaInicioBloque->format('H:i:s'),
                    'hora_fin' => $horaFinBloque->format('H:i:s'),
                    'disponibilidad_id' => $disponibilidad->id,
                    'duracion_minutos' => $duracionMinutos,
                    'es_receso' => false,
                    'disponibilidad_origen' => [
                        'inicio' => $disponibilidad->hora_inicio,
                        'fin' => $disponibilidad->hora_fin
                    ]
                ];

                $horaActual->addMinutes($duracionMinutos);
            }
        }

        return $bloques;
    }

    /**
     * Obtener bloques disponibles con intervalos personalizados
     * (soporte para intervalos más flexible)
     */
    public function obtenerBloquesConIntervalos(
        int $profesorId,
        string $dia,
        string $institucion = 'colegio',
        int $intervaloMinutos = 30
    ): array {
        $disponibilidades = DisponibilidadProfesor::where('profesor_id', $profesorId)
            ->where('dia_semana', $dia)
            ->where('institucion', $institucion)
            ->where('tipo', 'disponible')
            ->orderBy('hora_inicio')
            ->get();

        $bloques = [];
        $breaks = $this->obtenerBreaksPorInstitucion($institucion, $dia, $horaReferencia = null);

        foreach ($disponibilidades as $disponibilidad) {
            $inicio = Carbon::parse($disponibilidad->hora_inicio);
            $fin = Carbon::parse($disponibilidad->hora_fin);

            $horaActual = $inicio->copy();

            while ($horaActual->addMinutes($intervaloMinutos) <= $fin) {
                $horaInicioBloque = $horaActual->copy()->subMinutes($intervaloMinutos);
                $horaFinBloque = $horaActual->copy();

                // Verificar si el bloque cae en un receso
                if ($this->bloqueCaeEnReceso($horaInicioBloque, $horaFinBloque, $breaks)) {
                    continue;
                }

                $bloques[] = [
                    'hora_inicio' => $horaInicioBloque->format('H:i:s'),
                    'hora_fin' => $horaFinBloque->format('H:i:s'),
                    'disponibilidad_id' => $disponibilidad->id,
                    'es_receso' => false
                ];
            }
        }

        return $bloques;
    }

    /**
     * Obtener los breaks/recesos según la institución y día
     */
    private function obtenerBreaksPorInstitucion(string $institucion, string $dia, string $horaReferencia = null): array
    {
        $turno = $this->determinarTurno($horaReferencia, $dia);

        // Breaks para turno MAÑANA (7:00 AM - 1:15 PM)
        if ($turno === 'mañana') {
            $breaksPorInstitucion = [
                'primaria' => [
                    ['inicio' => '08:30', 'fin' => '08:50'],  // Recreo 1 (20 min)
                    ['inicio' => '10:20', 'fin' => '10:40'],  // Recreo 2 (20 min)
                ],
                'secundaria' => [
                    ['inicio' => '09:15', 'fin' => '09:35'],  // Recreo 1 (20 min)
                    ['inicio' => '11:10', 'fin' => '11:25'],  // Recreo 2 (15 min)
                ],
                'academia' => [
                    ['inicio' => '08:50', 'fin' => '09:10'],  // Recreo 1 (20 min)
                    ['inicio' => '10:40', 'fin' => '10:50'],  // Recreo 2 (10 min)
                ]
            ];
        }
        // Breaks para turno TARDE (3:30 PM - 8:00 PM)
        else {
            $breaksPorInstitucion = [
                'primaria' => [],  // Sin recesos en la tarde
                'secundaria' => [], // Sin recesos en la tarde
                'academia' => [
                    ['inicio' => '17:00', 'fin' => '17:10'],  // Recreo 1 (10 min)
                    ['inicio' => '18:30', 'fin' => '18:40'],  // Recreo 2 (10 min)
                ]
            ];
        }

        $institucionKey = strtolower($institucion);
        return $breaksPorInstitucion[$institucionKey] ?? [];
    }

    /**
     * Determinar si es turno mañana o tarde basado en el día y hora
     */
    private function determinarTurno(string $dia, ?string $horaReferencia = null): string
    {
        $diaNormalizado = strtolower(trim($dia));

        // Los sábados SIEMPRE son turno mañana
        if ($diaNormalizado === 'sábado' || $diaNormalizado === 'sabado') {
            return 'mañana';
        }

        // Si no hay hora de referencia, asumimos mañana por defecto
        if ($horaReferencia === null) {
            return 'mañana';
        }

        // Para el resto de días, determinar por la hora
        $hora = Carbon::parse($horaReferencia);
        $horaNumero = (int) $hora->format('H');

        // Si es antes de las 2:00 PM (14:00) = turno mañana
        if ($horaNumero < 14) {
            return 'mañana';
        }

        // Si es después de las 2:00 PM = turno tarde
        return 'tarde';
    }

    /**
     * Verificar si un bloque de tiempo cae en un receso
     */
    private function bloqueCaeEnReceso(
        Carbon $inicioBloque,
        Carbon $finBloque,
        array $breaks
    ): bool {
        foreach ($breaks as $break) {
            $inicioBreak = Carbon::parse($break['inicio']);
            $finBreak = Carbon::parse($break['fin']);

            // El bloque cae en receso si se solapa con el break
            if ($inicioBloque < $finBreak && $finBloque > $inicioBreak) {
                return true;
            }
        }

        return false;
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
