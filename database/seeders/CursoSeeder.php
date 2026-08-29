<?php
// database/seeders/CursoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curso;

class CursoSeeder extends Seeder
{
    public function run(): void
    {
        $cursos = [
            ['codigo' => 'MAT-PRI', 'nombre' => 'Matemáticas Primaria', 'descripcion' => 'Matemáticas básicas para primaria', 'horas_semanales' => 6, 'duracion_minutos' => 60, 'nivel' => 'primaria', 'tipo' => 'obligatorio', 'color' => '#3490dc', 'activo' => 1],
            ['codigo' => 'COM-PRI', 'nombre' => 'Comunicación Primaria', 'descripcion' => 'Lengua y literatura para primaria', 'horas_semanales' => 6, 'duracion_minutos' => 60, 'nivel' => 'primaria', 'tipo' => 'obligatorio', 'color' => '#e74c3c', 'activo' => 1],
            ['codigo' => 'CT-PRI', 'nombre' => 'Ciencia y Tecnología Primaria', 'descripcion' => 'Ciencias naturales y tecnología', 'horas_semanales' => 4, 'duracion_minutos' => 60, 'nivel' => 'primaria', 'tipo' => 'obligatorio', 'color' => '#2ecc71', 'activo' => 1],
            ['codigo' => 'PER-PRI', 'nombre' => 'Personal Social Primaria', 'descripcion' => 'Historia, geografía y formación cívica', 'horas_semanales' => 3, 'duracion_minutos' => 60, 'nivel' => 'primaria', 'tipo' => 'obligatorio', 'color' => '#f39c12', 'activo' => 1],
            ['codigo' => 'ING-PRI', 'nombre' => 'Inglés Primaria', 'descripcion' => 'Inglés nivel básico', 'horas_semanales' => 3, 'duracion_minutos' => 60, 'nivel' => 'primaria', 'tipo' => 'obligatorio', 'color' => '#9b59b6', 'activo' => 1],
            ['codigo' => 'ART-PRI', 'nombre' => 'Arte y Cultura Primaria', 'descripcion' => 'Arte, música y cultura', 'horas_semanales' => 2, 'duracion_minutos' => 60, 'nivel' => 'primaria', 'tipo' => 'electivo', 'color' => '#1abc9c', 'activo' => 1],
            ['codigo' => 'MAT-SEC', 'nombre' => 'Matemáticas Secundaria', 'descripcion' => 'Matemáticas avanzadas para secundaria', 'horas_semanales' => 6, 'duracion_minutos' => 60, 'nivel' => 'secundaria', 'tipo' => 'obligatorio', 'color' => '#3490dc', 'activo' => 1],
            ['codigo' => 'COM-SEC', 'nombre' => 'Comunicación Secundaria', 'descripcion' => 'Lengua y literatura avanzada', 'horas_semanales' => 5, 'duracion_minutos' => 60, 'nivel' => 'secundaria', 'tipo' => 'obligatorio', 'color' => '#e74c3c', 'activo' => 1],
            ['codigo' => 'ING-SEC', 'nombre' => 'Inglés Secundaria', 'descripcion' => 'Inglés nivel intermedio-avanzado', 'horas_semanales' => 4, 'duracion_minutos' => 60, 'nivel' => 'secundaria', 'tipo' => 'obligatorio', 'color' => '#f39c12', 'activo' => 1],
            ['codigo' => 'CIE-SEC', 'nombre' => 'Ciencias Naturales Secundaria', 'descripcion' => 'Biología, química y física', 'horas_semanales' => 4, 'duracion_minutos' => 60, 'nivel' => 'secundaria', 'tipo' => 'obligatorio', 'color' => '#2ecc71', 'activo' => 1],
            ['codigo' => 'SOC-SEC', 'nombre' => 'Historia y Geografía Secundaria', 'descripcion' => 'Historia, geografía y ciencias sociales', 'horas_semanales' => 4, 'duracion_minutos' => 60, 'nivel' => 'secundaria', 'tipo' => 'obligatorio', 'color' => '#9b59b6', 'activo' => 1],
            ['codigo' => 'MAT-ACA', 'nombre' => 'Matemáticas Academia', 'descripcion' => 'Matemáticas pre-universitaria', 'horas_semanales' => 8, 'duracion_minutos' => 90, 'nivel' => 'academia', 'tipo' => 'obligatorio', 'color' => '#3490dc', 'activo' => 1],
            ['codigo' => 'COM-ACA', 'nombre' => 'Comunicación Academia', 'descripcion' => 'Lengua y literatura pre-universitaria', 'horas_semanales' => 6, 'duracion_minutos' => 90, 'nivel' => 'academia', 'tipo' => 'obligatorio', 'color' => '#e74c3c', 'activo' => 1],
            ['codigo' => 'ING-ACA', 'nombre' => 'Inglés Academia', 'descripcion' => 'Inglés nivel avanzado', 'horas_semanales' => 4, 'duracion_minutos' => 90, 'nivel' => 'academia', 'tipo' => 'obligatorio', 'color' => '#f39c12', 'activo' => 1],
            ['codigo' => 'CIE-ACA', 'nombre' => 'Ciencias Integradas Academia', 'descripcion' => 'Ciencias integradas pre-universitaria', 'horas_semanales' => 6, 'duracion_minutos' => 90, 'nivel' => 'academia', 'tipo' => 'obligatorio', 'color' => '#2ecc71', 'activo' => 1],
        ];

        foreach ($cursos as $curso) {
            Curso::create($curso);
        }

        $this->command->info('✅ ' . count($cursos) . ' cursos creados');
    }
}
