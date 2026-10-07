<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $anio = (int) date('Y');

        $configuraciones = [
            [
                'institucion' => 'colegio',
                'nivel' => 'primaria',
                'turno' => 'tarde',
                'nombre' => "Primaria Tarde {$anio}",
                'hora_inicio' => '13:00',
                'hora_fin' => '18:55',
                'duracion_bloque_minutos' => 45,
                'bloques' => [
                    [1, '13:00', '13:45', 'clase', null, 1],
                    [2, '13:45', '14:30', 'clase', null, 2],
                    [3, '14:30', '14:50', 'receso', 'Recreo 1', null],
                    [4, '14:50', '15:35', 'clase', null, 3],
                    [5, '15:35', '16:20', 'clase', null, 4],
                    [6, '16:20', '16:35', 'receso', 'Recreo 2', null],
                    [7, '16:35', '17:20', 'clase', null, 5],
                    [8, '17:20', '18:10', 'clase', null, 6],
                    [9, '18:10', '18:55', 'clase', null, 7],
                ],
            ],
            [
                'institucion' => 'colegio',
                'nivel' => 'secundaria',
                'turno' => 'tarde',
                'nombre' => "Secundaria Tarde {$anio}",
                'hora_inicio' => '13:00',
                'hora_fin' => '19:00',
                'duracion_bloque_minutos' => 45,
                'bloques' => [
                    [1, '13:00', '13:45', 'clase', null, 1],
                    [2, '13:45', '14:30', 'clase', null, 2],
                    [3, '14:30', '15:15', 'clase', null, 3],
                    [4, '15:15', '15:35', 'receso', 'Receso 1', null],
                    [5, '15:35', '16:25', 'clase', null, 4],
                    [6, '16:25', '17:10', 'clase', null, 5],
                    [7, '17:10', '17:25', 'receso', 'Receso 2', null],
                    [8, '17:25', '18:15', 'clase', null, 6],
                    [9, '18:15', '19:00', 'clase', null, 7],
                ],
            ],
        ];

        DB::transaction(function () use ($configuraciones, $anio) {
            foreach ($configuraciones as $configuracion) {
                $claveConfiguracion = [
                    'institucion' => $configuracion['institucion'],
                    'nivel' => $configuracion['nivel'],
                    'turno' => $configuracion['turno'],
                    'año_academico' => $anio,
                ];

                $valoresConfiguracion = [
                    'nombre' => $configuracion['nombre'],
                    'hora_inicio' => $configuracion['hora_inicio'],
                    'hora_fin' => $configuracion['hora_fin'],
                    'duracion_bloque_minutos' => $configuracion['duracion_bloque_minutos'],
                    'activo' => true,
                    'updated_at' => now(),
                ];

                if (!DB::table('configuracion_horarios')->where($claveConfiguracion)->exists()) {
                    $valoresConfiguracion['created_at'] = now();
                }

                DB::table('configuracion_horarios')->updateOrInsert(
                    $claveConfiguracion,
                    $valoresConfiguracion
                );

                $configuracionId = DB::table('configuracion_horarios')
                    ->where('institucion', $configuracion['institucion'])
                    ->where('nivel', $configuracion['nivel'])
                    ->where('turno', $configuracion['turno'])
                    ->where('año_academico', $anio)
                    ->value('id');

                foreach ($configuracion['bloques'] as $bloque) {
                    $claveBloque = [
                        'configuracion_horario_id' => $configuracionId,
                        'orden' => $bloque[0],
                    ];

                    $valoresBloque = [
                        'hora_inicio' => $bloque[1],
                        'hora_fin' => $bloque[2],
                        'tipo' => $bloque[3],
                        'nombre' => $bloque[4],
                        'numero_bloque' => $bloque[5],
                        'updated_at' => now(),
                    ];

                    if (!DB::table('bloques_horarios')->where($claveBloque)->exists()) {
                        $valoresBloque['created_at'] = now();
                    }

                    DB::table('bloques_horarios')->updateOrInsert(
                        $claveBloque,
                        $valoresBloque
                    );
                }
            }
        });
    }

    public function down(): void
    {
        $anio = (int) date('Y');

        $ids = DB::table('configuracion_horarios')
            ->where('institucion', 'colegio')
            ->whereIn('nivel', ['primaria', 'secundaria'])
            ->where('turno', 'tarde')
            ->where('año_academico', $anio)
            ->pluck('id');

        DB::table('bloques_horarios')
            ->whereIn('configuracion_horario_id', $ids)
            ->delete();

        DB::table('configuracion_horarios')
            ->whereIn('id', $ids)
            ->delete();
    }
};
