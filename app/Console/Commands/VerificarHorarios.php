<?php

namespace App\Console\Commands;

use App\Models\DisponibilidadProfesor;
use App\Models\Horario;
use App\Models\Profesor;
use App\Models\ProfesorCurso;
use App\Services\DisponibilidadService;
use App\Services\HorarioRecesoService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

class VerificarHorarios extends Command
{
    protected $signature = 'horarios:verificar {--periodo= : Limitar los bloques a un periodo academico} {--json : Mostrar el informe en JSON}';
    protected $description = 'Comprueba conexiones, disponibilidad, recesos y conflictos sin modificar datos';

    public function handle(DisponibilidadService $disponibilidad, HorarioRecesoService $recesos): int
    {
        $query = Horario::with(['profesor', 'curso', 'grado', 'aula'])->where('estado', 'activo');
        if ($this->option('periodo')) {
            $query->where('periodo_academico', $this->option('periodo'));
        }
        $horarios = $query->ordenSemanal()->get();
        $asignaciones = ProfesorCurso::with(['profesor', 'curso', 'grado', 'aula'])->where('activo', true)->get();
        $franjas = DisponibilidadProfesor::where('tipo', 'disponible')->get()
            ->groupBy(fn ($d) => "{$d->profesor_id}|{$d->institucion}|{$d->dia_semana}");
        $porAsignacion = $asignaciones->keyBy(fn ($a) => "{$a->profesor_id}|{$a->curso_id}|{$a->grado_id}|{$a->institucion}");
        $incidencias = [];
        $agregar = static function (string $tipo, string $registro, int $id, string $detalle) use (&$incidencias): void {
            $incidencias[] = compact('tipo', 'registro', 'id', 'detalle');
        };

        foreach ($asignaciones as $a) {
            if (!$a->profesor || $a->profesor->estado !== 'activo' || !$a->curso?->activo || !$a->grado?->activo) {
                $agregar('referencia_inactiva', 'asignacion', $a->id, 'Profesor, curso o grado ausente o inactivo.');
            }
            if (!$a->aula?->activo || !in_array($a->aula?->institucion, [$a->institucion, 'ambos'], true)) {
                $agregar('aula_incompatible', 'asignacion', $a->id, 'Selecciona un aula activa de la institucion o compartida.');
            }
        }
        foreach (Profesor::all() as $p) {
            $carga = (int) $asignaciones->where('profesor_id', $p->id)->sum('horas_asignadas');
            if ($p->carga_horaria_actual !== $carga || $carga > $p->carga_horaria_maxima) {
                $agregar('carga_incoherente', 'profesor', $p->id,
                    "Asignadas: {$carga}; contador: {$p->carga_horaria_actual}; limite: {$p->carga_horaria_maxima}.");
            }
        }
        foreach ($horarios as $h) {
            $a = $porAsignacion->get("{$h->profesor_id}|{$h->curso_id}|{$h->grado_id}|{$h->institucion}");
            if (!$a || $a->aula_id !== $h->aula_id) {
                $agregar('asignacion_incompatible', 'horario', $h->id, 'Falta una asignacion activa con el mismo profesor, curso, grado, institucion y aula.');
            }
            $inicio = $h->hora_inicio->format('H:i');
            $fin = $h->hora_fin->format('H:i');
            $rangos = $franjas->get("{$h->profesor_id}|{$h->institucion}|{$h->dia_semana}", collect());
            if (!$disponibilidad->rangoEstaCubierto($rangos, $inicio, $fin)) {
                $agregar('sin_disponibilidad', 'horario', $h->id,
                    "Profesor #{$h->profesor_id}: {$h->dia_semana} {$inicio}-{$fin} ({$h->institucion}).");
            }
            if (!$h->grado) {
                $agregar('grado_ausente', 'horario', $h->id, 'El grado esta eliminado o no existe.');
                continue;
            }
            try {
                $recesos->validarNoChocaConReceso($h->institucion, $h->grado->nivel,
                    $h->grado->turno ?: 'mañana', $inicio, $fin);
            } catch (ValidationException $e) {
                $agregar('cruce_receso', 'horario', $h->id, implode(' ', $e->validator->errors()->all()));
            }
        }

        // Agrupar por dia y periodo evita comparar jornadas que no coinciden.
        foreach ($horarios->groupBy(fn ($h) => "{$h->periodo_academico}|{$h->dia_semana}") as $grupo) {
            $ordenados = $grupo->sortBy(fn ($h) => $h->hora_inicio->format('H:i'))->values();
            foreach ($ordenados as $i => $a) {
                for ($j = $i + 1; $j < $ordenados->count(); $j++) {
                    $b = $ordenados[$j];
                    if ($b->hora_inicio->gte($a->hora_fin)) { break; }
                    if ($a->profesor_id === $b->profesor_id || $a->aula_id === $b->aula_id || $a->grado_id === $b->grado_id) {
                        $agregar('cruce_horario', 'horario', $a->id, "Coincide con el horario #{$b->id} por profesor, aula o grado.");
                    }
                }
            }
        }
        $recuperaciones = app(\App\Services\RecuperacionHorarioService::class)->auditar($this->option('periodo'));
        $incidencias = array_merge($incidencias, $recuperaciones['incidencias']);
        $resultado = ['horarios_revisados' => $horarios->count(), 'asignaciones_revisadas' => $asignaciones->count(),
            'recuperaciones_revisadas' => $recuperaciones['revisadas'],
            'total_incidencias' => count($incidencias), 'incidencias' => $incidencias];
        if ($this->option('json')) {
            $this->line(json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        } else {
            $this->info("Horarios: {$resultado['horarios_revisados']}. Asignaciones: {$resultado['asignaciones_revisadas']}.");
            $this->table(['Tipo', 'Registro', 'ID', 'Detalle'], $incidencias);
            $this->line('Solo lectura: no se modificaron datos.');
        }
        return $incidencias ? self::FAILURE : self::SUCCESS;
    }
}
