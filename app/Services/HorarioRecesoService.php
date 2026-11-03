<?php

namespace App\Services;

use App\Models\BloqueHorario;
use App\Models\ConfiguracionHorario;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class HorarioRecesoService
{
    public function recesosVigentes(?string $institucion = null, ?string $nivel = null, ?string $turno = null): Collection
    {
        $recesos = collect();
        foreach (config('horarios.recesos_fijos', []) as $nombreInstitucion => $niveles) {
            if ($institucion && $institucion !== $nombreInstitucion) {
                continue;
            }
            foreach ($niveles as $nombreNivel => $rangos) {
                if ($nivel && $nivel !== $nombreNivel) {
                    continue;
                }
                foreach ($rangos as $rango) {
                    if ($turno !== 'tarde') {
                        $recesos->push(new BloqueHorario($rango + ['tipo' => 'receso']));
                    }
                }
            }
        }
        return $recesos->unique(fn ($b) => $this->hora($b->hora_inicio).'-'.$this->hora($b->hora_fin))
            ->sortBy(fn ($b) => $this->minutos($b->hora_inicio))->values();
    }

    public function recesosParaDisponibilidad(string $institucion, ?string $nivel = null, ?string $turno = null): Collection
    {
        if ($nivel || $institucion === 'academia') {
            return $this->recesosVigentes($institucion, $nivel, $turno);
        }
        // Sin grado solo se excluye el descanso comun a todos los niveles.
        $comunes = null;
        foreach (array_keys(config("horarios.recesos_fijos.{$institucion}", [])) as $nombreNivel) {
            $rangos = $this->recesosVigentes($institucion, $nombreNivel, $turno);
            if ($comunes === null) {
                $comunes = $rangos;
                continue;
            }
            $intersecciones = collect();
            foreach ($comunes as $a) {
                foreach ($rangos as $b) {
                    $inicio = max($this->hora($a->hora_inicio), $this->hora($b->hora_inicio));
                    $fin = min($this->hora($a->hora_fin), $this->hora($b->hora_fin));
                    if ($inicio < $fin) {
                        $intersecciones->push(new BloqueHorario([
                            'hora_inicio' => $inicio, 'hora_fin' => $fin, 'tipo' => 'receso', 'nombre' => 'Receso comun',
                        ]));
                    }
                }
            }
            $comunes = $intersecciones;
        }
        return ($comunes ?? collect())->values();
    }

    public function configuracionVigente(string $institucion, string $nivel, string $turno): ?ConfiguracionHorario
    {
        $config = ConfiguracionHorario::with('bloques')
            ->where('institucion', $institucion)->where('nivel', $nivel)
            ->whereIn('turno', [$turno, 'completo'])->where('activo', true)
            ->orderByRaw('CASE WHEN turno = ? THEN 0 ELSE 1 END', [$turno])
            ->orderByDesc('año_academico')->orderByDesc('id')->first();
        return $config ? $this->normalizarConfiguracion($config) : null;
    }

    public function normalizarConfiguracion(ConfiguracionHorario $config): ConfiguracionHorario
    {
        $normalizada = clone $config;
        $bloques = $this->bloquesFijos($config);
        $normalizada->setRelation('bloques', $bloques);
        $normalizada->setRelation('bloquesClase', $bloques->where('tipo', 'clase')->values());
        $normalizada->setRelation('bloquesReceso', $bloques->where('tipo', 'receso')->values());
        $normalizada->setAttribute('recesos_fijos', true);
        return $normalizada;
    }

    public function bloquesFijos(ConfiguracionHorario $config): Collection
    {
        $cursor = $this->minutos($config->hora_inicio);
        $fin = $this->minutos($config->hora_fin);
        $duracion = max(30, (int) ($config->duracion_bloque_minutos ?: 45));
        $recesos = $this->recesosVigentes($config->institucion, $config->nivel)
            ->filter(fn ($r) => $this->minutos($r->hora_fin) > $cursor && $this->minutos($r->hora_inicio) < $fin);
        $bloques = collect();
        $numero = 1;
        while ($cursor < $fin) {
            $receso = $recesos->first(fn ($r) => $this->minutos($r->hora_inicio) <= $cursor
                && $this->minutos($r->hora_fin) > $cursor);
            $siguiente = $recesos->first(fn ($r) => $this->minutos($r->hora_inicio) > $cursor);
            $hasta = $receso ? min($fin, $this->minutos($receso->hora_fin))
                : min($cursor + $duracion, $fin, $siguiente ? $this->minutos($siguiente->hora_inicio) : $fin);
            $bloques->push(new BloqueHorario([
                'configuracion_horario_id' => $config->id, 'orden' => $bloques->count() + 1,
                'hora_inicio' => sprintf('%02d:%02d', intdiv($cursor, 60), $cursor % 60),
                'hora_fin' => sprintf('%02d:%02d', intdiv($hasta, 60), $hasta % 60),
                'tipo' => $receso ? 'receso' : 'clase', 'nombre' => $receso ? $receso->nombre : 'Clase '.$numero,
                'numero_bloque' => $receso ? null : $numero++,
            ]));
            $cursor = $hasta;
        }
        return $bloques;
    }

    public function validarBloques(array $bloques, string $turno, ?string $inicio = null, ?string $fin = null): void
    {
        $ordenados = collect($bloques)->sortBy(fn ($b) => $this->minutos($b['hora_inicio']))->values();
        if ($ordenados->isEmpty()) {
            throw ValidationException::withMessages(['bloques' => 'La configuracion debe contener bloques.']);
        }
        $cursor = $inicio ? $this->minutos($inicio) : $this->minutos($ordenados->first()['hora_inicio']);
        foreach ($ordenados as $bloque) {
            $hasta = $this->minutos($bloque['hora_fin']);
            if ($this->minutos($bloque['hora_inicio']) !== $cursor || $hasta <= $cursor) {
                throw ValidationException::withMessages(['bloques' => 'Los bloques deben ser continuos y tener duracion positiva.']);
            }
            $cursor = $hasta;
        }
        if ($fin !== null && $cursor !== $this->minutos($fin)) {
            throw ValidationException::withMessages(['bloques' => 'Los bloques deben cubrir toda la jornada configurada.']);
        }
    }

    public function validarConfiguracionLista(string $institucion, string $nivel, string $turno): void
    {
        $config = $this->configuracionVigente($institucion, $nivel, $turno);
        if (!$config) {
            throw ValidationException::withMessages(['configuracion_horario' => 'Falta la jornada de este nivel y turno. Ejecuta la instalacion de configuraciones fijas.']);
        }
        $this->validarBloques($config->bloques->toArray(), $turno, $this->hora($config->hora_inicio), $this->hora($config->hora_fin));
    }

    public function validarNoChocaConReceso(string $institucion, string $nivel, string $turno, string $horaInicio, string $horaFin): void
    {
        foreach ($this->recesosVigentes($institucion, $nivel) as $receso) {
            if ($this->rangosSeCruzan($horaInicio, $horaFin, $receso->hora_inicio, $receso->hora_fin)) {
                throw ValidationException::withMessages([
                    'receso' => 'No se puede programar una clase durante '.$receso->nombre.' ('.$this->hora($receso->hora_inicio).' - '.$this->hora($receso->hora_fin).').',
                ]);
            }
        }
    }

    public function rangoChocaConRecesoInstitucion(string $institucion, string $horaInicio, string $horaFin): ?array
    {
        foreach ($this->recesosParaDisponibilidad($institucion) as $receso) {
            if ($this->rangosSeCruzan($horaInicio, $horaFin, $receso->hora_inicio, $receso->hora_fin)) {
                return ['hora_inicio' => $this->hora($receso->hora_inicio), 'hora_fin' => $this->hora($receso->hora_fin), 'nombre' => $receso->nombre];
            }
        }
        return null;
    }

    public function rangosSeCruzan(string $inicioA, string $finA, string $inicioB, string $finB): bool
    {
        return Carbon::parse($inicioA)->lt(Carbon::parse($finB)) && Carbon::parse($finA)->gt(Carbon::parse($inicioB));
    }

    private function minutos(string $hora): int
    {
        $hora = Carbon::parse($hora);
        return (int) $hora->format('H') * 60 + (int) $hora->format('i');
    }

    private function hora(string $hora): string
    {
        return Carbon::parse($hora)->format('H:i');
    }
}
