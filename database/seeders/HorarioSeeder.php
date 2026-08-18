<?php
// database/seeders/HorarioSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Horario;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $horarios = [
            // Carlos García - Matemáticas 1ro Primaria A
            ['profesor_id' => 1, 'curso_id' => 1, 'aula_id' => 11, 'grado_id' => 1, 'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => 'Clase de Matemáticas 1A'],
            ['profesor_id' => 1, 'curso_id' => 1, 'aula_id' => 11, 'grado_id' => 1, 'dia_semana' => 'lunes', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 1, 'curso_id' => 1, 'aula_id' => 11, 'grado_id' => 1, 'dia_semana' => 'miércoles', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 1, 'curso_id' => 1, 'aula_id' => 11, 'grado_id' => 1, 'dia_semana' => 'miércoles', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 1, 'curso_id' => 1, 'aula_id' => 11, 'grado_id' => 1, 'dia_semana' => 'viernes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 1, 'curso_id' => 1, 'aula_id' => 11, 'grado_id' => 1, 'dia_semana' => 'viernes', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],

            // María Rodríguez - Comunicación 1ro Primaria A
            ['profesor_id' => 2, 'curso_id' => 2, 'aula_id' => 12, 'grado_id' => 1, 'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => 'Clase de Comunicación 1A'],
            ['profesor_id' => 2, 'curso_id' => 2, 'aula_id' => 12, 'grado_id' => 1, 'dia_semana' => 'martes', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 2, 'curso_id' => 2, 'aula_id' => 12, 'grado_id' => 1, 'dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 2, 'curso_id' => 2, 'aula_id' => 12, 'grado_id' => 1, 'dia_semana' => 'jueves', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 2, 'curso_id' => 2, 'aula_id' => 12, 'grado_id' => 1, 'dia_semana' => 'viernes', 'hora_inicio' => '09:00', 'hora_fin' => '10:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 2, 'curso_id' => 2, 'aula_id' => 12, 'grado_id' => 1, 'dia_semana' => 'viernes', 'hora_inicio' => '10:00', 'hora_fin' => '11:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],

            // Juan Martínez - Ciencias Naturales
            ['profesor_id' => 3, 'curso_id' => 10, 'aula_id' => 15, 'grado_id' => 10, 'dia_semana' => 'lunes', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => 'Clase de Ciencias Naturales'],
            ['profesor_id' => 3, 'curso_id' => 10, 'aula_id' => 15, 'grado_id' => 10, 'dia_semana' => 'miércoles', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 3, 'curso_id' => 10, 'aula_id' => 15, 'grado_id' => 10, 'dia_semana' => 'viernes', 'hora_inicio' => '14:00', 'hora_fin' => '16:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],

            // Ana Torres - Inglés
            ['profesor_id' => 4, 'curso_id' => 9, 'aula_id' => 13, 'grado_id' => 8, 'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => 'Clase de Inglés 1S'],
            ['profesor_id' => 4, 'curso_id' => 9, 'aula_id' => 13, 'grado_id' => 8, 'dia_semana' => 'lunes', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 4, 'curso_id' => 9, 'aula_id' => 13, 'grado_id' => 8, 'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 4, 'curso_id' => 9, 'aula_id' => 13, 'grado_id' => 8, 'dia_semana' => 'martes', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],

            // Luis Ramírez - Historia
            ['profesor_id' => 5, 'curso_id' => 11, 'aula_id' => 14, 'grado_id' => 10, 'dia_semana' => 'martes', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => 'Clase de Historia 2S'],
            ['profesor_id' => 5, 'curso_id' => 11, 'aula_id' => 14, 'grado_id' => 10, 'dia_semana' => 'jueves', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 5, 'curso_id' => 11, 'aula_id' => 14, 'grado_id' => 10, 'dia_semana' => 'viernes', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
        ];

        foreach ($horarios as $horario) {
            Horario::create($horario);
        }

        $this->command->info('✅ ' . count($horarios) . ' horarios creados');
    }
}
