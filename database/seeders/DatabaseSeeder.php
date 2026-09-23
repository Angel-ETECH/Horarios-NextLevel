<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ProfesorSeeder::class,
            GradoSeeder::class,
            CursoSeeder::class,
            AulaSeeder::class,
            AlumnoSeeder::class,
            DisponibilidadSeeder::class,
            ProfesorCursoSeeder::class,
            ConfiguracionHorarioSeeder::class,
            HorarioSeeder::class
        ]);
    }
}
