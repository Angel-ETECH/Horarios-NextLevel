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
     * Obtener todas las disponibilidades.
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
     * Obtener disponibilidades de un profesor.
     */
    public function getByProfesor(
        int $profesorId
    ): Collection {

        $profesor =
            Profesor::findOrFail(
                $profesorId
            );

        return $profesor
            ->disponibilidades()
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
    }

    /**
     * Obtener disponibilidades por día.
     */
    public function getByDia(
        string $dia
    ): Collection {

        return DisponibilidadProfesor::with(
            'profesor'
        )
            ->where(
                'dia_semana',
                $dia
            )
            ->where(
                'tipo',
                'disponible'
            )
            ->orderBy(
                'hora_inicio'
            )
            ->get();
    }

    /**
     * Obtener disponibilidades por turno.
     */
    public function getByTurno(
        string $turno
    ): Collection {

        return DisponibilidadProfesor::with(
            'profesor'
        )
            ->where(
                'turno',
                $turno
            )
            ->where(
                'tipo',
                'disponible'
            )
            ->orderBy(
                'profesor_id'
            )
            ->orderBy(
                'dia_semana'
            )
            ->get();
    }

    /**
     * Obtener disponibilidades por institución.
     */
    public function getByInstitucion(
        string $institucion
    ): Collection {

        return DisponibilidadProfesor::with(
            'profesor'
        )
            ->where(
                'institucion',
                $institucion
            )
            ->where(
                'tipo',
                'disponible'
            )
            ->orderBy(
                'profesor_id'
            )
            ->orderBy(
                'dia_semana'
            )
            ->orderBy(
                'hora_inicio'
            )
            ->get();
    }

    /**
     * Obtener disponibilidades por profesor e institución.
     */
    public function getByProfesorEInstitucion(
        int $profesorId,
        string $institucion
    ): Collection {

        return DisponibilidadProfesor::where(
            'profesor_id',
            $profesorId
        )
            ->where(
                'institucion',
                $institucion
            )
            ->orderBy(
                'dia_semana'
            )
            ->orderBy(
                'hora_inicio'
            )
            ->get();
    }

    /**
     * Validar que una clase esté dentro de una disponibilidad.
     */
    public function validarRangoEnDisponibilidad(
        int $profesorId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        string $institucion,
        ?int $excludeId = null
    ): bool {

        $disponibilidades =
            DisponibilidadProfesor::where(
                'profesor_id',
                $profesorId
            )
                ->where(
                    'dia_semana',
                    $dia
                )
                ->where(
                    'institucion',
                    $institucion
                )
                ->where(
                    'tipo',
                    'disponible'
                )
                ->get();

        if (
            $disponibilidades->isEmpty()
        ) {

            throw ValidationException::withMessages([
                'disponibilidad' =>
                    "El profesor no tiene disponibilidad registrada para {$dia} en {$institucion}",
            ]);
        }

        $horaInicioCarbon =
            Carbon::parse(
                $horaInicio
            );

        $horaFinCarbon =
            Carbon::parse(
                $horaFin
            );

        $rangoValido =
            false;

        foreach (
            $disponibilidades
            as $disponibilidad
        ) {

            $dispInicio =
                Carbon::parse(
                    $disponibilidad
                        ->hora_inicio
                );

            $dispFin =
                Carbon::parse(
                    $disponibilidad
                        ->hora_fin
                );

            if (
                $horaInicioCarbon >=
                    $dispInicio &&
                $horaFinCarbon <=
                    $dispFin
            ) {

                $rangoValido =
                    true;

                break;
            }
        }

        if (
            !$rangoValido
        ) {

            throw ValidationException::withMessages([
                'disponibilidad' =>
                    "El rango horario {$horaInicio} - {$horaFin} no está completamente dentro de la disponibilidad del profesor para {$dia} en {$institucion}",
            ]);
        }

        return true;
    }

    /**
     * Verificar solapamientos.
     */
    public function verificarSolapamientos(
        int $profesorId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        string $institucion,
        ?int $excludeId = null
    ): bool {

        $query =
            DisponibilidadProfesor::where(
                'profesor_id',
                $profesorId
            )
                ->where(
                    'dia_semana',
                    $dia
                )
                ->where(
                    'institucion',
                    $institucion
                )
                ->where(
                    'tipo',
                    'disponible'
                );

        if (
            $excludeId
        ) {

            $query->where(
                'id',
                '!=',
                $excludeId
            );
        }

        $existentes =
            $query->get();

        $nuevoInicio =
            Carbon::parse(
                $horaInicio
            );

        $nuevoFin =
            Carbon::parse(
                $horaFin
            );

        foreach (
            $existentes
            as $existente
        ) {

            $existenteInicio =
                Carbon::parse(
                    $existente
                        ->hora_inicio
                );

            $existenteFin =
                Carbon::parse(
                    $existente
                        ->hora_fin
                );

            if (
                $nuevoInicio->lt(
                    $existenteFin
                ) &&
                $nuevoFin->gt(
                    $existenteInicio
                )
            ) {

                throw ValidationException::withMessages([
                    'solapamiento' =>
                        "El rango {$horaInicio} - {$horaFin} se solapa con una disponibilidad existente ({$existente->hora_inicio} - {$existente->hora_fin}) para {$dia} en {$institucion}",
                ]);
            }
        }

        return true;
    }

    /**
     * Validar disponibilidad completa para una clase.
     */
    public function validarDisponibilidadParaClase(
        int $profesorId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        string $institucion,
        ?int $excludeId = null
    ): bool {

        $this->validarRangoEnDisponibilidad(
            $profesorId,
            $dia,
            $horaInicio,
            $horaFin,
            $institucion,
            $excludeId
        );

        return true;
    }

    /**
     * Validar rango horario.
     */
    private function validarRangoHorario(
        array $data
    ): void {

        $horaInicio =
            Carbon::parse(
                $data['hora_inicio']
            );

        $horaFin =
            Carbon::parse(
                $data['hora_fin']
            );

        if (
            $horaInicio >=
            $horaFin
        ) {

            throw ValidationException::withMessages([
                'hora_inicio' =>
                    'La hora de inicio debe ser menor que la hora de fin',
            ]);
        }

        $minHora =
            Carbon::parse(
                '07:00'
            );

        $maxHora =
            Carbon::parse(
                '20:00'
            );

        if (
            $horaInicio <
                $minHora ||
            $horaFin >
                $maxHora
        ) {

            throw ValidationException::withMessages([
                'hora_inicio' =>
                    'El rango horario debe estar entre 07:00 y 20:00',
            ]);
        }

        $horas =
            $horaInicio
                ->diffInHours(
                    $horaFin
                );

        if (
            $horas >
            13
        ) {

            throw ValidationException::withMessages([
                'hora_fin' =>
                    'El bloque de disponibilidad no puede exceder las 13 horas continuas',
            ]);
        }

        $minutos =
            $horaInicio
                ->diffInMinutes(
                    $horaFin
                );

        if (
            $minutos <
            30
        ) {

            throw ValidationException::withMessages([
                'hora_fin' =>
                    'El bloque de disponibilidad debe tener al menos 30 minutos de duración',
            ]);
        }
    }

    /**
     * Validar duplicados y solapamientos.
     */
    private function validarUnicidadYSolapamiento(
        array $data,
        ?int $excludeId = null
    ): void {

        $query =
            DisponibilidadProfesor::where(
                'profesor_id',
                $data['profesor_id']
            )
                ->where(
                    'dia_semana',
                    $data['dia_semana']
                )
                ->where(
                    'hora_inicio',
                    $data['hora_inicio']
                )
                ->where(
                    'hora_fin',
                    $data['hora_fin']
                )
                ->where(
                    'institucion',
                    $data['institucion'] ??
                    'colegio'
                );

        if (
            $excludeId
        ) {

            $query->where(
                'id',
                '!=',
                $excludeId
            );
        }

        if (
            $query->exists()
        ) {

            throw ValidationException::withMessages([
                'disponibilidad' =>
                    'Ya existe una disponibilidad con el mismo rango horario para este profesor',
            ]);
        }

        $this->verificarSolapamientos(
            $data['profesor_id'],
            $data['dia_semana'],
            $data['hora_inicio'],
            $data['hora_fin'],
            $data['institucion'] ??
                'colegio',
            $excludeId
        );
    }

    /**
     * Crear disponibilidad.
     *
     * Si existe un registro eliminado mediante SoftDeletes
     * con la misma clave única, se restaura y reutiliza.
     */
    public function create(
        array $data
    ): DisponibilidadProfesor {

        return DB::transaction(
            function () use ($data) {

                /*
                |--------------------------------------------------------------------------
                | VALORES POR DEFECTO
                |--------------------------------------------------------------------------
                */

                $data['institucion'] =
                    $data['institucion'] ??
                    'colegio';


                /*
                |--------------------------------------------------------------------------
                | VALIDAR PROFESOR
                |--------------------------------------------------------------------------
                */

                Profesor::findOrFail(
                    $data['profesor_id']
                );


                /*
                |--------------------------------------------------------------------------
                | VALIDAR RANGO
                |--------------------------------------------------------------------------
                */

                $this->validarRangoHorario(
                    $data
                );


                /*
                |--------------------------------------------------------------------------
                | BUSCAR REGISTRO ELIMINADO
                |--------------------------------------------------------------------------
                |
                | La clave única de la BD utiliza:
                |
                | profesor_id
                | dia_semana
                | hora_inicio
                | institucion
                |
                | Por eso debemos revisar también los registros
                | eliminados mediante SoftDeletes.
                |
                */

                $registroEliminado =
                    DisponibilidadProfesor::withTrashed()
                        ->where(
                            'profesor_id',
                            $data['profesor_id']
                        )
                        ->where(
                            'dia_semana',
                            $data['dia_semana']
                        )
                        ->where(
                            'hora_inicio',
                            $data['hora_inicio']
                        )
                        ->where(
                            'institucion',
                            $data['institucion']
                        )
                        ->whereNotNull(
                            'deleted_at'
                        )
                        ->first();


                /*
                |--------------------------------------------------------------------------
                | SI EXISTÍA ELIMINADO: RESTAURAR
                |--------------------------------------------------------------------------
                */

                if (
                    $registroEliminado
                ) {

                    /*
                    | Comprobar que el nuevo rango no choque
                    | con otras disponibilidades activas.
                    */

                    $this->verificarSolapamientos(
                        $data['profesor_id'],
                        $data['dia_semana'],
                        $data['hora_inicio'],
                        $data['hora_fin'],
                        $data['institucion'],
                        $registroEliminado->id
                    );


                    /*
                    | Restauramos primero.
                    */

                    $registroEliminado
                        ->restore();


                    /*
                    | Actualizamos todos los datos.
                    */

                    $registroEliminado
                        ->update(
                            $data
                        );


                    /*
                    | Refrescar modelo.
                    */

                    $registroEliminado
                        ->refresh();


                    /*
                    | Cargar profesor.
                    */

                    $registroEliminado
                        ->load(
                            'profesor'
                        );


                    return
                        $registroEliminado;
                }


                /*
                |--------------------------------------------------------------------------
                | SI NO EXISTÍA ELIMINADO
                |--------------------------------------------------------------------------
                */

                $this
                    ->validarUnicidadYSolapamiento(
                        $data
                    );


                $disponibilidad =
                    DisponibilidadProfesor::create(
                        $data
                    );


                $disponibilidad
                    ->load(
                        'profesor'
                    );


                return
                    $disponibilidad;
            }
        );
    }

    /**
     * Actualizar disponibilidad.
     */
    public function update(
        int $id,
        array $data
    ): DisponibilidadProfesor {

        return DB::transaction(
            function () use (
                $id,
                $data
            ) {

                $disponibilidad =
                    DisponibilidadProfesor::findOrFail(
                        $id
                    );


                if (
                    isset(
                        $data['profesor_id']
                    ) &&
                    $data['profesor_id'] !=
                        $disponibilidad
                            ->profesor_id
                ) {

                    Profesor::findOrFail(
                        $data['profesor_id']
                    );
                }


                $mergedData =
                    array_merge(
                        $disponibilidad
                            ->toArray(),
                        $data
                    );


                if (
                    isset(
                        $data['hora_inicio']
                    ) ||
                    isset(
                        $data['hora_fin']
                    )
                ) {

                    $this->validarRangoHorario(
                        $mergedData
                    );
                }


                $this
                    ->validarUnicidadYSolapamiento(
                        $mergedData,
                        $id
                    );


                $disponibilidad
                    ->update(
                        $data
                    );


                $disponibilidad
                    ->load(
                        'profesor'
                    );


                return
                    $disponibilidad;
            }
        );
    }

    /**
     * Eliminar disponibilidad.
     *
     * Se mantiene SoftDelete.
     */
    public function delete(
        int $id
    ): bool {

        $disponibilidad =
            DisponibilidadProfesor::findOrFail(
                $id
            );

        return
            $disponibilidad
                ->delete();
    }

    /**
     * Eliminar disponibilidades de profesor.
     */
    public function deleteByProfesor(
        int $profesorId
    ): int {

        $profesor =
            Profesor::findOrFail(
                $profesorId
            );

        return $profesor
            ->disponibilidades()
            ->delete();
    }

    /**
     * Eliminar disponibilidades por institución.
     */
    public function deleteByInstitucion(
        int $profesorId,
        string $institucion
    ): int {

        return DisponibilidadProfesor::where(
            'profesor_id',
            $profesorId
        )
            ->where(
                'institucion',
                $institucion
            )
            ->delete();
    }

    // =========================================================
    // CONSULTAS
    // =========================================================

    /**
     * Verificar disponibilidad puntual.
     */
    public function verificarDisponibilidadPuntual(
        int $profesorId,
        string $dia,
        string $hora,
        string $institucion = 'colegio'
    ): bool {

        return DisponibilidadProfesor::where(
            'profesor_id',
            $profesorId
        )
            ->where(
                'dia_semana',
                $dia
            )
            ->where(
                'institucion',
                $institucion
            )
            ->where(
                'hora_inicio',
                '<=',
                $hora
            )
            ->where(
                'hora_fin',
                '>=',
                $hora
            )
            ->where(
                'tipo',
                'disponible'
            )
            ->exists();
    }

    /**
     * Obtener bloques disponibles.
     */
    public function obtenerBloquesDisponibles(
        int $profesorId,
        string $dia,
        string $institucion = 'colegio',
        ?int $duracionMinutos = null
    ): array {

        $disponibilidades =
            DisponibilidadProfesor::where(
                'profesor_id',
                $profesorId
            )
                ->where(
                    'dia_semana',
                    $dia
                )
                ->where(
                    'institucion',
                    $institucion
                )
                ->where(
                    'tipo',
                    'disponible'
                )
                ->orderBy(
                    'hora_inicio'
                )
                ->get();

        if (
            $disponibilidades->isEmpty()
        ) {
            return [];
        }

        $bloques =
            [];

        foreach (
            $disponibilidades
            as $disponibilidad
        ) {

            $bloques[] = [
                'hora_inicio' =>
                    $disponibilidad
                        ->hora_inicio,

                'hora_fin' =>
                    $disponibilidad
                        ->hora_fin,

                'disponibilidad_id' =>
                    $disponibilidad
                        ->id,
            ];
        }

        return
            $bloques;
    }

    /**
     * Disponibilidades agrupadas por día.
     */
    public function getAgrupadoPorDia(
        int $profesorId,
        string $institucion = 'colegio'
    ): array {

        $disponibilidades =
            DisponibilidadProfesor::where(
                'profesor_id',
                $profesorId
            )
                ->where(
                    'institucion',
                    $institucion
                )
                ->where(
                    'tipo',
                    'disponible'
                )
                ->orderBy(
                    'dia_semana'
                )
                ->orderBy(
                    'hora_inicio'
                )
                ->get();

        $agrupado =
            [];

        foreach (
            $disponibilidades
            as $disp
        ) {

            $dia =
                $disp->dia_semana;


            if (
                !isset(
                    $agrupado[
                        $dia
                    ]
                )
            ) {

                $agrupado[
                    $dia
                ] =
                    [];
            }


            $agrupado[
                $dia
            ][] = [

                'id' =>
                    $disp->id,

                'hora_inicio' =>
                    $disp->hora_inicio,

                'hora_fin' =>
                    $disp->hora_fin,

                'turno' =>
                    $disp->turno,

                'tipo' =>
                    $disp->tipo,

                'observacion' =>
                    $disp->observacion,
            ];
        }

        return
            $agrupado;
    }

    /**
     * Verificar conflictos con horarios existentes.
     */
    public function verificarConflictosConHorarios(
        int $profesorId,
        string $dia,
        string $horaInicio,
        string $horaFin,
        string $institucion,
        ?int $excludeHorarioId = null
    ): bool {

        $query =
            \App\Models\Horario::where(
                'profesor_id',
                $profesorId
            )
                ->where(
                    'dia_semana',
                    $dia
                )
                ->where(
                    'institucion',
                    $institucion
                )
                ->where(
                    'estado',
                    'activo'
                );


        if (
            $excludeHorarioId
        ) {

            $query->where(
                'id',
                '!=',
                $excludeHorarioId
            );
        }


        $horariosExistentes =
            $query->get();


        $nuevoInicio =
            Carbon::parse(
                $horaInicio
            );

        $nuevoFin =
            Carbon::parse(
                $horaFin
            );


        foreach (
            $horariosExistentes
            as $horario
        ) {

            $horarioInicio =
                Carbon::parse(
                    $horario
                        ->hora_inicio
                );

            $horarioFin =
                Carbon::parse(
                    $horario
                        ->hora_fin
                );


            if (
                $nuevoInicio->lt(
                    $horarioFin
                ) &&
                $nuevoFin->gt(
                    $horarioInicio
                )
            ) {

                throw ValidationException::withMessages([
                    'conflicto' =>
                        "El horario {$horaInicio} - {$horaFin} se solapa con una clase existente ({$horario->hora_inicio} - {$horario->hora_fin})",
                ]);
            }
        }


        return true;
    }

    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    /**
     * Obtener estadísticas.
     */
    public function getEstadisticas(): array
    {
        $total =
            DisponibilidadProfesor::count();


        $disponibles =
            DisponibilidadProfesor::where(
                'tipo',
                'disponible'
            )
                ->count();


        $noDisponibles =
            DisponibilidadProfesor::where(
                'tipo',
                'no_disponible'
            )
                ->count();


        $porInstitucion =
            DisponibilidadProfesor::select(
                'institucion',
                DB::raw(
                    'count(*) as total'
                )
            )
                ->groupBy(
                    'institucion'
                )
                ->get();


        $porDia =
            DisponibilidadProfesor::select(
                'dia_semana',
                DB::raw(
                    'count(*) as total'
                )
            )
                ->groupBy(
                    'dia_semana'
                )
                ->orderBy(
                    'dia_semana'
                )
                ->get();


        return [
            'total' =>
                $total,

            'disponibles' =>
                $disponibles,

            'no_disponibles' =>
                $noDisponibles,

            'por_institucion' =>
                $porInstitucion,

            'por_dia' =>
                $porDia,

            'profesores_con_disponibilidad' =>
                DisponibilidadProfesor::distinct(
                    'profesor_id'
                )
                    ->count(),
        ];
    }
}
