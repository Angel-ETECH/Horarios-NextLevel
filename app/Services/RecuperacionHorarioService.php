<?php

namespace App\Services;

use App\Models\Aula;
use App\Models\Horario;
use App\Models\Profesor;
use App\Models\ProfesorCurso;
use App\Models\RecuperacionHorario;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RecuperacionHorarioService
{
    public function __construct(private HorarioCalendarioService $calendario, private HorarioRecesoService $recesos,
        private DisponibilidadService $disponibilidad, private HistorialService $historial) {}

    public function bloquearAgenda(): void
    {
        // Una misma reserva debe verse antes de validar otra escritura concurrente.
        Profesor::orderBy('id')->lockForUpdate()->get(['id']);
    }

    private function comprobarInstalacion(): void
    {
        if (!$this->calendario->instalado()) {
            throw ValidationException::withMessages(['recuperacion' => 'Falta instalar las recuperaciones: ejecuta php artisan migrate.']);
        }
    }

    private function origen(int $id, string $fecha): Horario
    {
        $this->comprobarInstalacion();
        $horario = Horario::with(['profesor', 'grado', 'curso', 'aula'])->findOrFail($id);
        if ($horario->estado !== 'activo' || $horario->profesor?->estado !== 'activo' || !$horario->grado?->activo || !$horario->curso?->activo) {
            throw ValidationException::withMessages(['horario_id' => 'La clase y sus referencias deben estar activas.']);
        }
        if (HorarioCalendarioService::DIAS[CarbonImmutable::parse($fecha)->dayOfWeekIso] !== $horario->dia_semana) {
            throw ValidationException::withMessages(['fecha_original' => 'La fecha original no corresponde al dia habitual de esta clase.']);
        }
        if (!ProfesorCurso::where('profesor_id', $horario->profesor_id)->where('curso_id', $horario->curso_id)
            ->where('grado_id', $horario->grado_id)->where('institucion', $horario->institucion)->where('activo', true)->exists()) {
            throw ValidationException::withMessages(['horario_id' => 'La asignacion de esta clase ya no esta activa.']);
        }
        if (RecuperacionHorario::where('horario_id', $id)->whereDate('fecha_original', $fecha)->where('estado', 'programada')->exists()) {
            throw ValidationException::withMessages(['fecha_original' => 'Esta clase ya tiene una recuperacion. Cancelala antes de reprogramarla.']);
        }
        return $horario;
    }

    private function minutos(string $hora): int
    {
        return (int) substr($hora, 0, 2) * 60 + (int) substr($hora, 3, 2);
    }

    private function hora(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }

    private function validarDestino(Horario $origen, array $datos, ?Collection $agenda = null): string
    {
        $inicio = $this->hora($this->minutos($datos['hora_inicio']));
        $duracion = $this->minutos($origen->hora_fin->format('H:i')) - $this->minutos($origen->hora_inicio->format('H:i'));
        $finMinutos = $this->minutos($inicio) + $duracion;
        $fin = $this->hora($finMinutos);
        if ($duracion <= 0 || $this->minutos($inicio) < 420 || $finMinutos > 1200) {
            throw ValidationException::withMessages(['hora_inicio' => 'La clase completa debe estar entre las 07:00 y las 20:00.']);
        }
        $fecha = CarbonImmutable::parse($datos['fecha_recuperacion']);
        $dia = HorarioCalendarioService::DIAS[$fecha->dayOfWeekIso];
        $aula = Aula::findOrFail($datos['aula_id']);
        if (!$aula->activo || !in_array($aula->institucion, [$origen->institucion, 'ambos'], true)
            || ($origen->grado->numero_estudiantes > $aula->capacidad)) {
            throw ValidationException::withMessages(['aula_id' => 'El aula debe estar activa, admitir la institucion y tener capacidad para el grado.']);
        }
        $turno = $origen->grado->turno === 'manana' ? 'mañana' : ($origen->grado->turno ?: 'mañana');
        $jornada = $this->recesos->configuracionVigente($origen->institucion, $origen->grado->nivel, $turno);
        $fueraJornada = !$jornada || $inicio < $jornada->hora_inicio->format('H:i') || $fin > $jornada->hora_fin->format('H:i');
        if (($fecha->isSunday() || $fueraJornada) && empty($datos['jornada_autorizada'])) {
            throw ValidationException::withMessages(['jornada_autorizada' => 'Autoriza expresamente la recuperacion fuera de jornada o en domingo.']);
        }
        $this->recesos->validarNoChocaConReceso($origen->institucion, $origen->grado->nivel, $turno, $inicio, $fin);
        if (!empty($datos['disponibilidad_excepcional'])) {
            if (empty($datos['disponible_desde']) || empty($datos['disponible_hasta'])
                || $this->minutos($inicio) < $this->minutos($datos['disponible_desde'])
                || $this->minutos($fin) > $this->minutos($datos['disponible_hasta'])) {
                throw ValidationException::withMessages(['disponibilidad' => 'La clase debe caber en el rango excepcional confirmado por el profesor.']);
            }
        } else {
            $this->disponibilidad->validarRangoEnDisponibilidad($origen->profesor_id, $dia, $inicio, $fin, $origen->institucion);
        }
        if ($datos['fecha_original'] === $datos['fecha_recuperacion'] && $inicio === $origen->hora_inicio->format('H:i') && (int) $datos['aula_id'] === $origen->aula_id) {
            throw ValidationException::withMessages(['hora_inicio' => 'El nuevo horario debe ser distinto al original.']);
        }
        $agenda ??= $this->calendario->consultar(['semana_inicio' => $datos['fecha_recuperacion'], 'periodo_academico' => $origen->periodo_academico]);
        $agenda = $agenda->reject(fn ($h) => (string) $h->id === (string) $origen->id && $h->fecha_clase === $datos['fecha_original']);
        $this->validarConflictos($agenda, $datos['fecha_recuperacion'], $inicio, $fin, $origen->profesor_id, $origen->grado_id, $aula->id);
        return $fin;
    }

    private function validarConflictos(Collection $agenda, string $fecha, string $inicio, string $fin, int $profesor, int $grado, int $aula): void
    {
        foreach ($agenda as $h) {
            if ($h->fecha_clase !== $fecha || !$this->recesos->rangosSeCruzan($inicio, $fin, $h->hora_inicio, $h->hora_fin)) continue;
            foreach (['profesor_id' => $profesor, 'grado_id' => $grado, 'aula_id' => $aula] as $campo => $valor) {
                if ((int) $h->$campo === $valor) {
                    throw ValidationException::withMessages([$campo => 'Ya hay otra clase para este '.str_replace('_id', '', $campo).' en la fecha y hora elegidas.']);
                }
            }
        }
    }

    public function opciones(array $datos): array
    {
        $origen = $this->origen($datos['horario_id'], $datos['fecha_original']);
        $agenda = $this->calendario->consultar(['semana_inicio' => $datos['fecha_recuperacion'], 'periodo_academico' => $origen->periodo_academico]);
        $opciones = [];
        for ($minuto = $this->minutos($datos['buscar_desde']); $minuto < $this->minutos($datos['buscar_hasta']); $minuto += 15) {
            try {
                $inicio = $this->hora($minuto);
                $fin = $this->validarDestino($origen, array_merge($datos, ['hora_inicio' => $inicio]), $agenda);
                if ($fin > $datos['buscar_hasta']) continue;
                $opciones[] = ['hora_inicio' => $inicio, 'hora_fin' => $fin, 'aula_id' => (int) $datos['aula_id']];
            } catch (ValidationException $e) {
                $ultimoError = $e->errors();
            }
        }
        return ['opciones' => $opciones, 'motivos' => $opciones ? [] : ($ultimoError ?? ['horario' => ['No hay un bloque completo en ese rango.']])];
    }

    public function crear(array $datos): RecuperacionHorario
    {
        return DB::transaction(function () use ($datos) {
            $this->bloquearAgenda();
            $origen = $this->origen($datos['horario_id'], $datos['fecha_original']);
            $fin = $this->validarDestino($origen, $datos);
            $recuperacion = RecuperacionHorario::firstOrNew(['horario_id' => $origen->id, 'fecha_original' => $datos['fecha_original']]);
            $anterior = $recuperacion->exists ? $this->snapshot($recuperacion) : null;
            $recuperacion->fill(collect($datos)->only(['fecha_recuperacion', 'hora_inicio', 'aula_id', 'disponible_desde',
                'disponible_hasta', 'disponibilidad_excepcional', 'jornada_autorizada', 'motivo'])->all());
            $recuperacion->hora_fin = $fin;
            $recuperacion->original = $origen->toArray();
            $recuperacion->estado = 'programada';
            $recuperacion->creado_por = auth()->id();
            $recuperacion->save();
            $this->historial->registrarEvento('recuperaciones', $anterior ? 'actualizar' : 'crear', $anterior,
                $this->snapshot($recuperacion), $datos['motivo']);
            return $recuperacion;
        });
    }

    public function cancelar(int $id, string $motivo): RecuperacionHorario
    {
        return DB::transaction(function () use ($id, $motivo) {
            $this->bloquearAgenda();
            $r = RecuperacionHorario::with('horario')->lockForUpdate()->findOrFail($id);
            if ($r->estado !== 'programada') {
                throw ValidationException::withMessages(['recuperacion' => 'La recuperacion ya esta cancelada.']);
            }
            if ($r->horario?->estado === 'activo') {
                $agenda = $this->calendario->consultar(['semana_inicio' => $r->fecha_original->toDateString(), 'periodo_academico' => $r->horario->periodo_academico])
                    ->reject(fn ($h) => $h->recuperacion_id === $r->id);
                $h = $r->horario;
                $this->validarConflictos($agenda, $r->fecha_original->toDateString(), $h->hora_inicio->format('H:i'), $h->hora_fin->format('H:i'), $h->profesor_id, $h->grado_id, $h->aula_id);
            }
            $anterior = $this->snapshot($r);
            $r->update(['estado' => 'cancelada']);
            $this->historial->registrarEvento('recuperaciones', 'actualizar', $anterior, $this->snapshot($r), $motivo);
            return $r;
        });
    }

    public function snapshot(RecuperacionHorario $r): array
    {
        return array_merge($r->toArray(), ['solo_auditoria' => true, 'profesor_nombre' => trim(implode(' ', array_filter([
            $r->original['profesor']['nombre'] ?? null, $r->original['profesor']['apellido_paterno'] ?? null, $r->original['profesor']['apellido_materno'] ?? null,
        ]))),
            'curso_nombre' => $r->original['curso']['nombre'] ?? null, 'grado_nombre' => $r->original['grado']['nombre_completo'] ?? null,
            'institucion' => $r->original['institucion'] ?? null]);
    }

    public function protegerPlantilla(int $id): void
    {
        if ($this->calendario->instalado() && RecuperacionHorario::where('horario_id', $id)->where('estado', 'programada')
            ->whereDate('fecha_recuperacion', '>=', today())->exists()) {
            throw ValidationException::withMessages(['recuperacion' => 'Cancela las recuperaciones de esta clase antes de modificar su horario habitual.']);
        }
    }

    public function protegerAula(int $id, array $cambios = [], bool $eliminar = false): void
    {
        if (!$this->calendario->instalado()) return;
        $aula = Aula::findOrFail($id);
        $reservas = RecuperacionHorario::with('horario.grado')->where('aula_id', $id)->where('estado', 'programada')
            ->whereDate('fecha_recuperacion', '>=', today())->get();
        foreach ($reservas as $reserva) {
            $institucion = $cambios['institucion'] ?? $aula->institucion;
            $capacidad = $cambios['capacidad'] ?? $aula->capacidad;
            $activo = $cambios['activo'] ?? $aula->activo;
            if ($eliminar || !$activo || !in_array($institucion, [$reserva->horario?->institucion, 'ambos'], true)
                || $capacidad < ($reserva->horario?->grado?->numero_estudiantes ?? 0)) {
                throw ValidationException::withMessages(['recuperacion' => 'Cancela o reprograma las recuperaciones de esta aula antes de hacer un cambio incompatible.']);
            }
        }
    }

    public function auditar(?string $periodo = null): array
    {
        if (!$this->calendario->instalado()) return ['revisadas' => 0, 'incidencias' => []];
        $registros = RecuperacionHorario::with(['horario.profesor', 'horario.grado', 'horario.curso'])->where('estado', 'programada')
            ->whereDate('fecha_recuperacion', '>=', today())
            ->when($periodo, fn ($q) => $q->whereHas('horario', fn ($q) => $q->where('periodo_academico', $periodo)))->get();
        $incidencias = [];
        foreach ($registros as $r) {
            try {
                $h = $r->horario;
                if (!$h || $h->estado !== 'activo' || $h->profesor?->estado !== 'activo' || !$h->grado?->activo || !$h->curso?->activo) {
                    throw ValidationException::withMessages(['referencias' => 'La clase original o alguna referencia ya no esta activa.']);
                }
                $agenda = $this->calendario->consultar(['semana_inicio' => $r->fecha_recuperacion->toDateString(), 'periodo_academico' => $h->periodo_academico])
                    ->reject(fn ($clase) => $clase->recuperacion_id === $r->id);
                $fin = $this->validarDestino($h, array_merge($r->toArray(), ['hora_inicio' => substr($r->hora_inicio, 0, 5),
                    'fecha_original' => $r->fecha_original->toDateString(), 'fecha_recuperacion' => $r->fecha_recuperacion->toDateString()]), $agenda);
                if ($fin !== substr($r->hora_fin, 0, 5)) {
                    throw ValidationException::withMessages(['duracion' => 'La recuperacion no conserva los minutos de la clase.']);
                }
            } catch (ValidationException $e) {
                $incidencias[] = ['tipo' => 'recuperacion_incompatible', 'registro' => 'recuperacion', 'id' => $r->id,
                    'detalle' => implode(' ', $e->validator->errors()->all())];
            }
        }
        return ['revisadas' => $registros->count(), 'incidencias' => $incidencias];
    }

    public function validarNuevaPlantilla(array $datos, ?int $id = null): void
    {
        if (!$this->calendario->instalado()) return;
        $reservas = RecuperacionHorario::where('estado', 'programada')->whereDate('fecha_recuperacion', '>=', today())
            ->whereHas('horario', fn ($q) => $q->where('estado', 'activo')->where('periodo_academico', $datos['periodo_academico']))->get();
        foreach ($reservas as $r) {
            if (HorarioCalendarioService::DIAS[$r->fecha_recuperacion->dayOfWeekIso] !== $datos['dia_semana']) continue;
            if ($id && RecuperacionHorario::where('horario_id', $id)->where('estado', 'programada')->whereDate('fecha_original', $r->fecha_recuperacion)->exists()) continue;
            $original = $r->original;
            if ($this->recesos->rangosSeCruzan($datos['hora_inicio'], $datos['hora_fin'], $r->hora_inicio, $r->hora_fin)
                && ((int) $original['profesor_id'] === (int) $datos['profesor_id'] || (int) $original['grado_id'] === (int) $datos['grado_id'] || (int) $r->aula_id === (int) $datos['aula_id'])) {
                throw ValidationException::withMessages(['recuperacion' => 'Este horario habitual chocaria con una recuperacion del '.$r->fecha_recuperacion->format('d/m/Y').'.']);
            }
        }
    }
}
