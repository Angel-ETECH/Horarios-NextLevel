<?php
// database/seeders/DisponibilidadSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DisponibilidadProfesor;

class DisponibilidadSeeder extends Seeder
{
    public function run(): void
    {
        $disponibilidades = [
            // Profesor 1: Carlos García
            ['profesor_id' => 1, 'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => 'Disponible todo el día lunes'],
            ['profesor_id' => 1, 'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 1, 'dia_semana' => 'miércoles', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 1, 'dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 1, 'dia_semana' => 'viernes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],

            // Profesor 2: María Rodríguez
            ['profesor_id' => 2, 'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 2, 'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 2, 'dia_semana' => 'miércoles', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 2, 'dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 2, 'dia_semana' => 'viernes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],

            // Profesor 3: Juan Martínez
            ['profesor_id' => 3, 'dia_semana' => 'lunes', 'hora_inicio' => '13:00', 'hora_fin' => '19:00', 'tipo' => 'disponible', 'turno' => 'tarde', 'observacion' => null],
            ['profesor_id' => 3, 'dia_semana' => 'martes', 'hora_inicio' => '13:00', 'hora_fin' => '19:00', 'tipo' => 'disponible', 'turno' => 'tarde', 'observacion' => null],
            ['profesor_id' => 3, 'dia_semana' => 'miércoles', 'hora_inicio' => '13:00', 'hora_fin' => '19:00', 'tipo' => 'disponible', 'turno' => 'tarde', 'observacion' => null],
            ['profesor_id' => 3, 'dia_semana' => 'jueves', 'hora_inicio' => '13:00', 'hora_fin' => '19:00', 'tipo' => 'disponible', 'turno' => 'tarde', 'observacion' => null],
            ['profesor_id' => 3, 'dia_semana' => 'viernes', 'hora_inicio' => '13:00', 'hora_fin' => '19:00', 'tipo' => 'disponible', 'turno' => 'tarde', 'observacion' => null],

            // Profesor 4: Ana Torres
            ['profesor_id' => 4, 'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 4, 'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 4, 'dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],
            ['profesor_id' => 4, 'dia_semana' => 'viernes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'tipo' => 'disponible', 'turno' => 'mañana', 'observacion' => null],

            // Profesor 5: Luis Ramírez
            ['profesor_id' => 5, 'dia_semana' => 'lunes', 'hora_inicio' => '13:00', 'hora_fin' => '19:00', 'tipo' => 'disponible', 'turno' => 'tarde', 'observacion' => null],
            ['profesor_id' => 5, 'dia_semana' => 'miércoles', 'hora_inicio' => '13:00', 'hora_fin' => '19:00', 'tipo' => 'disponible', 'turno' => 'tarde', 'observacion' => null],
            ['profesor_id' => 5, 'dia_semana' => 'viernes', 'hora_inicio' => '13:00', 'hora_fin' => '19:00', 'tipo' => 'disponible', 'turno' => 'tarde', 'observacion' => null],
        ];

        foreach ($disponibilidades as $disponibilidad) {
            DisponibilidadProfesor::create($disponibilidad);
        }

        $this->command->info('✅ ' . count($disponibilidades) . ' disponibilidades creadas');
    }
}
