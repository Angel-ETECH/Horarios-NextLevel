<?php
// database/seeders/ProfesorCursoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfesorCurso;

class ProfesorCursoSeeder extends Seeder
{
    public function run(): void
    {
        $relaciones = [
            // Carlos García enseña Matemáticas
            ['profesor_id' => 1, 'curso_id' => 1, 'grado_id' => 1, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 1ro Primaria A'],
            ['profesor_id' => 1, 'curso_id' => 1, 'grado_id' => 2, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 1ro Primaria B'],
            ['profesor_id' => 1, 'curso_id' => 1, 'grado_id' => 3, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 2do Primaria A'],
            ['profesor_id' => 1, 'curso_id' => 7, 'grado_id' => 8, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 1ro Secundaria A'],
            ['profesor_id' => 1, 'curso_id' => 7, 'grado_id' => 9, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 1ro Secundaria B'],
            ['profesor_id' => 1, 'curso_id' => 12, 'grado_id' => 14, 'horas_asignadas' => 8, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas Pre Academia A'],

            // María Rodríguez enseña Comunicación
            ['profesor_id' => 2, 'curso_id' => 2, 'grado_id' => 1, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación 1ro Primaria A'],
            ['profesor_id' => 2, 'curso_id' => 2, 'grado_id' => 2, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación 1ro Primaria B'],
            ['profesor_id' => 2, 'curso_id' => 8, 'grado_id' => 8, 'horas_asignadas' => 5, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación 1ro Secundaria A'],
            ['profesor_id' => 2, 'curso_id' => 8, 'grado_id' => 9, 'horas_asignadas' => 5, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación 1ro Secundaria B'],
            ['profesor_id' => 2, 'curso_id' => 13, 'grado_id' => 14, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación Pre Academia A'],

            // Juan Martínez enseña Ciencias Naturales
            ['profesor_id' => 3, 'curso_id' => 3, 'grado_id' => 3, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Ciencia y Tecnología 2do Primaria A'],
            ['profesor_id' => 3, 'curso_id' => 10, 'grado_id' => 10, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Ciencias Naturales 2do Secundaria A'],
            ['profesor_id' => 3, 'curso_id' => 15, 'grado_id' => 14, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Ciencias Integradas Pre Academia A'],

            // Ana Torres enseña Inglés
            ['profesor_id' => 4, 'curso_id' => 5, 'grado_id' => 3, 'horas_asignadas' => 3, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Inglés 2do Primaria A'],
            ['profesor_id' => 4, 'curso_id' => 9, 'grado_id' => 8, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Inglés 1ro Secundaria A'],
            ['profesor_id' => 4, 'curso_id' => 14, 'grado_id' => 14, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Inglés Pre Academia A'],

            // Luis Ramírez enseña Historia
            ['profesor_id' => 5, 'curso_id' => 4, 'grado_id' => 4, 'horas_asignadas' => 3, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Personal Social 3ro Primaria A'],
            ['profesor_id' => 5, 'curso_id' => 11, 'grado_id' => 10, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Historia y Geografía 2do Secundaria A'],
        ];

        foreach ($relaciones as $relacion) {
            ProfesorCurso::create($relacion);
        }

        $this->command->info('✅ ' . count($relaciones) . ' relaciones profesor-curso creadas');
    }
}
