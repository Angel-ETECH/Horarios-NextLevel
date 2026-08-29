<?php
// database/seeders/GradoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grado;

class GradoSeeder extends Seeder
{
    public function run(): void
    {
        $grados = [
            ['codigo' => '1P-A', 'nivel' => 'primaria', 'grado' => '1ro', 'seccion' => 'A', 'nombre_completo' => '1ro Primaria A', 'capacidad_maxima' => 30, 'numero_estudiantes' => 28, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '1P-B', 'nivel' => 'primaria', 'grado' => '1ro', 'seccion' => 'B', 'nombre_completo' => '1ro Primaria B', 'capacidad_maxima' => 30, 'numero_estudiantes' => 25, 'año_academico' => 2024, 'turno' => 'tarde', 'activo' => 1],
            ['codigo' => '2P-A', 'nivel' => 'primaria', 'grado' => '2do', 'seccion' => 'A', 'nombre_completo' => '2do Primaria A', 'capacidad_maxima' => 30, 'numero_estudiantes' => 30, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '3P-A', 'nivel' => 'primaria', 'grado' => '3ro', 'seccion' => 'A', 'nombre_completo' => '3ro Primaria A', 'capacidad_maxima' => 30, 'numero_estudiantes' => 27, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '4P-A', 'nivel' => 'primaria', 'grado' => '4to', 'seccion' => 'A', 'nombre_completo' => '4to Primaria A', 'capacidad_maxima' => 30, 'numero_estudiantes' => 29, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '5P-A', 'nivel' => 'primaria', 'grado' => '5to', 'seccion' => 'A', 'nombre_completo' => '5to Primaria A', 'capacidad_maxima' => 30, 'numero_estudiantes' => 26, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '6P-A', 'nivel' => 'primaria', 'grado' => '6to', 'seccion' => 'A', 'nombre_completo' => '6to Primaria A', 'capacidad_maxima' => 30, 'numero_estudiantes' => 24, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '1S-A', 'nivel' => 'secundaria', 'grado' => '1ro', 'seccion' => 'A', 'nombre_completo' => '1ro Secundaria A', 'capacidad_maxima' => 35, 'numero_estudiantes' => 32, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '1S-B', 'nivel' => 'secundaria', 'grado' => '1ro', 'seccion' => 'B', 'nombre_completo' => '1ro Secundaria B', 'capacidad_maxima' => 35, 'numero_estudiantes' => 30, 'año_academico' => 2024, 'turno' => 'tarde', 'activo' => 1],
            ['codigo' => '2S-A', 'nivel' => 'secundaria', 'grado' => '2do', 'seccion' => 'A', 'nombre_completo' => '2do Secundaria A', 'capacidad_maxima' => 35, 'numero_estudiantes' => 33, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '3S-A', 'nivel' => 'secundaria', 'grado' => '3ro', 'seccion' => 'A', 'nombre_completo' => '3ro Secundaria A', 'capacidad_maxima' => 35, 'numero_estudiantes' => 31, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '4S-A', 'nivel' => 'secundaria', 'grado' => '4to', 'seccion' => 'A', 'nombre_completo' => '4to Secundaria A', 'capacidad_maxima' => 35, 'numero_estudiantes' => 28, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => '5S-A', 'nivel' => 'secundaria', 'grado' => '5to', 'seccion' => 'A', 'nombre_completo' => '5to Secundaria A', 'capacidad_maxima' => 35, 'numero_estudiantes' => 27, 'año_academico' => 2024, 'turno' => 'mañana', 'activo' => 1],
            ['codigo' => 'ACA-A', 'nivel' => 'academia', 'grado' => 'Pre', 'seccion' => 'A', 'nombre_completo' => 'Pre Academia A', 'capacidad_maxima' => 45, 'numero_estudiantes' => 40, 'año_academico' => 2024, 'turno' => 'noche', 'activo' => 1],
            ['codigo' => 'ACA-B', 'nivel' => 'academia', 'grado' => '1er', 'seccion' => 'A', 'nombre_completo' => '1er Academia A', 'capacidad_maxima' => 45, 'numero_estudiantes' => 38, 'año_academico' => 2024, 'turno' => 'noche', 'activo' => 1],
            ['codigo' => 'ACA-C', 'nivel' => 'academia', 'grado' => '2do', 'seccion' => 'A', 'nombre_completo' => '2do Academia A', 'capacidad_maxima' => 45, 'numero_estudiantes' => 35, 'año_academico' => 2024, 'turno' => 'noche', 'activo' => 1],
        ];

        foreach ($grados as $grado) {
            Grado::create($grado);
        }

        $this->command->info('✅ ' . count($grados) . ' grados creados');
    }
}
