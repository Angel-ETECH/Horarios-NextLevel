<?php
// database/seeders/ProfesorSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Profesor;

class ProfesorSeeder extends Seeder
{
    public function run(): void
    {
        $profesores = [
            [
                'codigo' => 'DOC-001',
                'nombre' => 'Carlos',
                'apellido_paterno' => 'García',
                'apellido_materno' => 'Pérez',
                'email' => 'carlos.garcia@academia.edu',
                'telefono' => '987654321',
                'dni' => '12345678',
                'sexo' => 'masculino',
                'fecha_nacimiento' => '1985-06-15',
                'especialidad' => 'Matemáticas',
                'carga_horaria_maxima' => 30,
                'estado' => 'activo',
                'carga_horaria_actual' => 24,
                'observaciones' => 'Profesor con 10 años de experiencia'
            ],
            [
                'codigo' => 'DOC-002',
                'nombre' => 'María',
                'apellido_paterno' => 'Rodríguez',
                'apellido_materno' => 'López',
                'email' => 'maria.rodriguez@academia.edu',
                'telefono' => '987654322',
                'dni' => '87654321',
                'sexo' => 'femenino',
                'fecha_nacimiento' => '1990-03-20',
                'especialidad' => 'Comunicación',
                'carga_horaria_maxima' => 28,
                'estado' => 'activo',
                'carga_horaria_actual' => 20,
                'observaciones' => 'Especialista en lingüística'
            ],
            [
                'codigo' => 'DOC-003',
                'nombre' => 'Juan',
                'apellido_paterno' => 'Martínez',
                'apellido_materno' => 'Sánchez',
                'email' => 'juan.martinez@academia.edu',
                'telefono' => '987654323',
                'dni' => '23456789',
                'sexo' => 'masculino',
                'fecha_nacimiento' => '1988-11-05',
                'especialidad' => 'Ciencias Naturales',
                'carga_horaria_maxima' => 32,
                'estado' => 'activo',
                'carga_horaria_actual' => 28,
                'observaciones' => 'Biólogo con maestría'
            ],
            [
                'codigo' => 'DOC-004',
                'nombre' => 'Ana',
                'apellido_paterno' => 'Torres',
                'apellido_materno' => 'Flores',
                'email' => 'ana.torres@academia.edu',
                'telefono' => '987654324',
                'dni' => '34567890',
                'sexo' => 'femenino',
                'fecha_nacimiento' => '1992-08-30',
                'especialidad' => 'Inglés',
                'carga_horaria_maxima' => 25,
                'estado' => 'activo',
                'carga_horaria_actual' => 20,
                'observaciones' => 'Certificada en Cambridge'
            ],
            [
                'codigo' => 'DOC-005',
                'nombre' => 'Luis',
                'apellido_paterno' => 'Ramírez',
                'apellido_materno' => 'Gómez',
                'email' => 'luis.ramirez@academia.edu',
                'telefono' => '987654325',
                'dni' => '45678901',
                'sexo' => 'masculino',
                'fecha_nacimiento' => '1978-01-12',
                'especialidad' => 'Historia',
                'carga_horaria_maxima' => 26,
                'estado' => 'activo',
                'carga_horaria_actual' => 18,
                'observaciones' => 'Historiador con 15 años'
            ]
        ];

        foreach ($profesores as $profesor) {
            Profesor::create($profesor);
        }

        $this->command->info('✅ ' . count($profesores) . ' profesores creados');
    }
}
