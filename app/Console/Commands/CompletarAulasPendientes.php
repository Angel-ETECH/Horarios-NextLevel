<?php

namespace App\Console\Commands;

use App\Models\Aula;
use App\Models\Horario;
use App\Models\ProfesorCurso;
use App\Services\HistorialService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CompletarAulasPendientes extends Command
{
    protected $signature = 'horarios:completar-aulas {--aplicar : Guardar la propuesta} {--respaldo= : Archivo SQL previo}';

    protected $description = 'Propone aulas para asignaciones antiguas, sin modificar sus horas ni crear cruces';

    public function handle(HistorialService $historial): int
    {
        if ($this->option('aplicar') && (!is_file((string) $this->option('respaldo'))
            || filesize((string) $this->option('respaldo')) === 0)) {
            $this->error('Para aplicar indica --respaldo con una copia SQL existente y no vacia.');
            return self::FAILURE;
        }

        try {
            $plan = DB::transaction(function () use ($historial) {
                $asignaciones = ProfesorCurso::with(['grado', 'curso', 'profesor', 'aula'])
                    ->where('activo', true)->whereNull('aula_id')->orderBy('id')->lockForUpdate()->get();
                $aulas = Aula::where('activo', true)->orderBy('id')->get();
                $horarios = Horario::withTrashed()->lockForUpdate()->get();
                $plan = [];

                foreach ($asignaciones as $asignacion) {
                    $bloques = $horarios->filter(fn ($h) => !$h->trashed() && $h->estado === 'activo'
                        && $h->profesor_id === $asignacion->profesor_id && $h->curso_id === $asignacion->curso_id
                        && $h->grado_id === $asignacion->grado_id && $h->institucion === $asignacion->institucion);
                    if ($bloques->isEmpty()) {
                        throw new RuntimeException("La asignacion {$asignacion->id} no tiene horarios para proponer un aula. Seleccionala manualmente.");
                    }
                    $usos = $bloques->countBy('aula_id');
                    $candidatas = $aulas->filter(fn ($aula) => isset($usos[$aula->id])
                        || (in_array($aula->tipo, ['aula_normal', 'auditorio'], true)
                            && $aula->capacidad >= ($asignacion->grado?->numero_estudiantes ?? 0)))
                        ->sortBy(fn ($aula) => [-($usos[$aula->id] ?? 0), $aula->tipo !== 'aula_normal', $aula->id]);

                    $aula = $candidatas->first(fn ($aula) => $this->sinCruces($aula->id, $bloques, $horarios));
                    if (!$aula) {
                        throw new RuntimeException("No hay un aula fija libre para la asignacion {$asignacion->id}. No se modifico nada.");
                    }
                    $movidos = $bloques->where('aula_id', '!=', $aula->id)->count();
                    $plan[] = [$asignacion->id, $asignacion->profesor?->nombre_completo,
                        $asignacion->grado?->nombre_completo, $aula->nombre, $movidos,
                        $usos->count() === 1 && isset($usos[$aula->id]) ? 'Aula unica existente' : 'Propuesta automatica sin cruces'];

                    if ($this->option('aplicar')) {
                        $antes = $asignacion->toArray();
                        $asignacion->update(['aula_id' => $aula->id]);
                        $asignacion->load('aula');
                        $historial->registrarEvento('asignaciones', 'actualizar', $antes, $asignacion->toArray(),
                            'Aula completada para entrega: '.$plan[array_key_last($plan)][5]);
                    }
                    foreach ($bloques as $bloque) {
                        if ($bloque->aula_id === $aula->id) {
                            continue;
                        }
                        $antes = $bloque->toArray();
                        $bloque->aula_id = $aula->id;
                        if ($this->option('aplicar')) {
                            $bloque->version++;
                            $bloque->save();
                            $historial->registrarCambio($bloque->id, 'actualizar', $antes, $bloque->toArray(),
                                'Aula adaptada a la asignacion para entrega; horas conservadas');
                        }
                    }
                }
                return $plan;
            });

            $this->table(['Asignacion', 'Profesor', 'Grado', 'Aula propuesta', 'Bloques a mover', 'Criterio'], $plan);
            $this->info(count($plan).' asignaciones '.($this->option('aplicar') ? 'actualizadas con historial.' : 'revisadas. Simulacion: datos sin cambios.'));
            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }

    private function sinCruces(int $aulaId, Collection $bloques, Collection $horarios): bool
    {
        $propios = $bloques->pluck('id')->all();
        foreach ($bloques as $bloque) {
            foreach ($horarios as $otro) {
                if (in_array($otro->id, $propios, true) || $otro->aula_id !== $aulaId
                    || $otro->dia_semana !== $bloque->dia_semana || $otro->periodo_academico !== $bloque->periodo_academico) {
                    continue;
                }
                $inicio = $bloque->hora_inicio->format('H:i');
                $fin = $bloque->hora_fin->format('H:i');
                $otroInicio = $otro->hora_inicio->format('H:i');
                $otroFin = $otro->hora_fin->format('H:i');
                if ($inicio === $otroInicio || (!$otro->trashed() && $otro->estado === 'activo'
                    && $inicio < $otroFin && $fin > $otroInicio)) {
                    return false;
                }
            }
        }
        return true;
    }
}
