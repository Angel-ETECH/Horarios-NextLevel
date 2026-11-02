<?php

namespace App\Services;

use App\Models\Horario;
use App\Models\RecuperacionHorario;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class HorarioCalendarioService
{
    public const DIAS = [1 => 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'];

    public function instalado(): bool
    {
        return Schema::hasTable('recuperaciones_horario');
    }

    public function inicioSemana(string $fecha): CarbonImmutable
    {
        Validator::make(['semana_inicio' => $fecha], ['semana_inicio' => 'required|date_format:Y-m-d'])->validate();
        return CarbonImmutable::parse($fecha, config('app.timezone'))->startOfWeek();
    }

    public function consultar(array $filtros = []): Collection
    {
        $regulares = Horario::with(['profesor', 'curso', 'grado', 'aula'])->where('estado', 'activo')->get();
        $clases = empty($filtros['semana_inicio']) ? $regulares
            : $this->proyectar($regulares, $this->inicioSemana($filtros['semana_inicio']));

        foreach (['profesor_id', 'grado_id', 'aula_id', 'curso_id', 'institucion', 'periodo_academico', 'tipo', 'turno'] as $campo) {
            if (!empty($filtros[$campo]) && $filtros[$campo] !== 'todos') {
                $clases = $clases->where($campo, $filtros[$campo]);
            }
        }
        if (!empty($filtros['dia_semana'])) {
            $dia = strtr(mb_strtolower($filtros['dia_semana']), ['miercoles' => 'miércoles', 'sabado' => 'sábado']);
            $clases = $clases->where('dia_semana', $dia);
        }
        if (!empty($filtros['search'])) {
            $search = mb_strtolower($filtros['search']);
            $clases = $clases->filter(fn ($h) => str_contains(mb_strtolower(implode(' ', [
                $h->profesor?->nombre_completo, $h->curso?->nombre, $h->grado?->nombre_completo, $h->aula?->nombre,
            ])), $search));
        }
        return $clases->sortBy(fn ($h) => array_search($h->dia_semana, self::DIAS).'-'.$h->hora_inicio->format('H:i'))->values();
    }

    public function proyectar(Collection $regulares, CarbonImmutable $lunes): Collection
    {
        $recuperaciones = $this->instalado()
            ? RecuperacionHorario::with('aula')->where('estado', 'programada')->whereHas('horario', fn ($q) => $q->where('estado', 'activo'))
                ->where(fn ($q) => $q->whereBetween('fecha_original', [$lunes->toDateString(), $lunes->addDays(6)->toDateString()])
                    ->orWhereBetween('fecha_recuperacion', [$lunes->toDateString(), $lunes->addDays(6)->toDateString()]))->get()
            : collect();
        $resultado = collect();
        foreach ($regulares as $horario) {
            $numero = array_search($horario->dia_semana, self::DIAS);
            if (!$numero) continue;
            $fecha = $lunes->addDays($numero - 1)->toDateString();
            if ($recuperaciones->contains(fn ($r) => $r->horario_id === $horario->id && $r->fecha_original->toDateString() === $fecha)) continue;
            $clase = clone $horario;
            $clase->setAttribute('fecha_clase', $fecha);
            $resultado->push($clase);
        }
        foreach ($recuperaciones as $recuperacion) {
            if (!$recuperacion->fecha_recuperacion->betweenIncluded($lunes, $lunes->addDays(6))) continue;
            $base = $regulares->firstWhere('id', $recuperacion->horario_id);
            if (!$base) continue;
            $clase = clone $base;
            $clase->id = -$recuperacion->id;
            $clase->setAttribute('horario_original_id', $base->id);
            $clase->setAttribute('recuperacion_id', $recuperacion->id);
            $clase->setAttribute('fecha_clase', $recuperacion->fecha_recuperacion->toDateString());
            $clase->setAttribute('fecha_original', $recuperacion->fecha_original->toDateString());
            $clase->hora_inicio = $recuperacion->hora_inicio;
            $clase->hora_fin = $recuperacion->hora_fin;
            $clase->dia_semana = self::DIAS[$recuperacion->fecha_recuperacion->dayOfWeekIso];
            $clase->aula_id = $recuperacion->aula_id;
            $clase->setRelation('aula', $recuperacion->aula);
            $clase->tipo = 'recuperacion';
            $clase->turno = (int) substr($recuperacion->hora_inicio, 0, 2) < 12 ? 'mañana' : 'tarde';
            $resultado->push($clase);
        }
        return $resultado;
    }
}
