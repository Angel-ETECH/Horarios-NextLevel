<?php

namespace App\Services;

use App\Models\BloqueHorario;
use App\Models\ConfiguracionHorario;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class HorarioRecesoService
{
    public const RECESOS_BASE = [
        ['nombre' => 'Receso mañana 1', 'hora_inicio' => '09:00', 'hora_fin' => '09:30'],
        ['nombre' => 'Receso mañana 2', 'hora_inicio' => '11:00', 'hora_fin' => '11:30'],
        ['nombre' => 'Receso tarde 1', 'hora_inicio' => '15:00', 'hora_fin' => '15:30'],
        ['nombre' => 'Receso tarde 2', 'hora_inicio' => '17:00', 'hora_fin' => '17:30'],
    ];

    public function recesosBaseParaTurno(string $turno): array
    {
        if ($turno === 'mañana') {
            return array_slice(self::RECESOS_BASE, 0, 2);
        }

        if ($turno === 'tarde') {
            return array_slice(self::RECESOS_BASE, 2, 2);
        }

        return self::RECESOS_BASE;
    }

    public function configuracionVigente(string $institucion, string $nivel, string $turno): ?ConfiguracionHorario
    {
        return ConfiguracionHorario::with('bloques')
            ->where('institucion', $institucion)
            ->where('nivel', $nivel)
            ->whereIn('turno', [$turno, 'completo'])
            ->where('activo', true)
            ->orderByRaw("CASE WHEN turno = ? THEN 0 ELSE 1 END", [$turno])
            ->orderByDesc('año_academico')
            ->first();
    }

    public function recesosVigentes(?string $institucion = null, ?string $nivel = null, ?string $turno = null): Collection
    {
        $query = ConfiguracionHorario::with('bloquesReceso')
            ->where('activo', true);

        if ($institucion) {
            $query->where('institucion', $institucion);
        }

        if ($nivel) {
            $query->where('nivel', $nivel);
        }

        if ($turno) {
            $query->whereIn('turno', [$turno, 'completo']);
        }

        return $query
            ->orderByDesc('año_academico')
            ->get()
            ->flatMap(fn (ConfiguracionHorario $config) => $config->bloquesReceso)
            ->unique(fn (BloqueHorario $bloque) => $this->hora($bloque->hora_inicio) . '-' . $this->hora($bloque->hora_fin))
            ->sortBy(fn (BloqueHorario $bloque) => $this->minutos($bloque->hora_inicio))
            ->values();
    }

    public function validarBloques(array $bloques, string $turno): void
    {
        $bloquesOrdenados = collect($bloques)
            ->sortBy(fn ($bloque) => $this->minutos($bloque['hora_inicio']))
            ->values();

        for ($index = 1; $index < $bloquesOrdenados->count(); $index++) {
            $anterior = $bloquesOrdenados[$index - 1];
            $actual = $bloquesOrdenados[$index];

            if ($this->minutos($actual['hora_inicio']) !== $this->minutos($anterior['hora_fin'])) {
                throw ValidationException::withMessages([
                    'bloques' => 'Los bloques deben ser continuos: cada bloque debe comenzar cuando termina el anterior.',
                ]);
            }
        }

        $recesos = collect($bloques)
            ->filter(fn ($bloque) => ($bloque['tipo'] ?? null) === 'receso')
            ->values();

        $mañana = $recesos->filter(fn ($bloque) => $this->minutos($bloque['hora_inicio']) < 12 * 60)->count();
        $tarde = $recesos->filter(fn ($bloque) => $this->minutos($bloque['hora_inicio']) >= 12 * 60)->count();

        $esperados = [
            'mañana' => ['mañana' => 2, 'tarde' => 0],
            'tarde' => ['mañana' => 0, 'tarde' => 2],
            'completo' => ['mañana' => 2, 'tarde' => 2],
        ][$turno] ?? ['mañana' => 2, 'tarde' => 2];

        if ($mañana !== $esperados['mañana'] || $tarde !== $esperados['tarde']) {
            $texto = $turno === 'completo'
                ? 'La configuración completa debe tener 2 recesos en la mañana y 2 recesos en la tarde.'
                : "La configuración de turno {$turno} debe tener 2 recesos de ese turno.";

            throw ValidationException::withMessages([
                'bloques' => $texto,
            ]);
        }
    }

    public function validarConfiguracionLista(string $institucion, string $nivel, string $turno): void
    {
        $config = $this->configuracionVigente($institucion, $nivel, $turno);

        if (!$config) {
            throw ValidationException::withMessages([
                'configuracion_horario' => 'Antes de crear asignaciones debes guardar la configuración de horarios y recesos para esta institución, nivel y turno.',
            ]);
        }

        $this->validarBloques($config->bloques->toArray(), $config->turno);
    }

    public function validarNoChocaConReceso(string $institucion, string $nivel, string $turno, string $horaInicio, string $horaFin): void
    {
        $config = $this->configuracionVigente($institucion, $nivel, $turno);

        if (!$config) {
            return;
        }

        foreach ($config->bloques->where('tipo', 'receso') as $receso) {
            if ($this->rangosSeCruzan($horaInicio, $horaFin, $receso->hora_inicio, $receso->hora_fin)) {
                $inicio = $this->hora($receso->hora_inicio);
                $fin = $this->hora($receso->hora_fin);

                throw ValidationException::withMessages([
                    'receso' => "No se puede usar el horario {$horaInicio} - {$horaFin} porque cruza el receso {$inicio} - {$fin}.",
                ]);
            }
        }
    }

    public function rangoChocaConRecesoInstitucion(string $institucion, string $horaInicio, string $horaFin): ?array
    {
        foreach ($this->recesosVigentes($institucion) as $receso) {
            if ($this->rangosSeCruzan($horaInicio, $horaFin, $receso->hora_inicio, $receso->hora_fin)) {
                return [
                    'hora_inicio' => $this->hora($receso->hora_inicio),
                    'hora_fin' => $this->hora($receso->hora_fin),
                    'nombre' => $receso->nombre ?: 'Receso',
                ];
            }
        }

        return null;
    }

    public function rangosSeCruzan(string $inicioA, string $finA, string $inicioB, string $finB): bool
    {
        return Carbon::parse($inicioA)->lt(Carbon::parse($finB))
            && Carbon::parse($finA)->gt(Carbon::parse($inicioB));
    }

    private function minutos(string $hora): int
    {
        $carbon = Carbon::parse($hora);

        return ((int) $carbon->format('H')) * 60 + (int) $carbon->format('i');
    }

    private function hora(string $hora): string
    {
        return Carbon::parse($hora)->format('H:i');
    }
}
