<?php
// database/seeders/AulaSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aula;

class AulaSeeder extends Seeder
{
    public function run(): void
    {
        $aulas = [
            ['codigo' => 'A-101', 'nombre' => 'Aula 101', 'capacidad' => 30, 'tipo' => 'aula_normal', 'nivel' => 'todos', 'equipamiento' => 'Proyector, Pizarra acrílica', 'activo' => 1],
            ['codigo' => 'A-102', 'nombre' => 'Aula 102', 'capacidad' => 35, 'tipo' => 'aula_normal', 'nivel' => 'todos', 'equipamiento' => 'Proyector, Aire acondicionado', 'activo' => 1],
            ['codigo' => 'A-201', 'nombre' => 'Aula 201', 'capacidad' => 25, 'tipo' => 'aula_normal', 'nivel' => 'secundaria', 'equipamiento' => 'Proyector, Aire acondicionado', 'activo' => 1],
            ['codigo' => 'A-202', 'nombre' => 'Aula 202', 'capacidad' => 30, 'tipo' => 'aula_normal', 'nivel' => 'secundaria', 'equipamiento' => 'Proyector, Pizarra digital', 'activo' => 1],
            ['codigo' => 'LAB-01', 'nombre' => 'Taller de Karate', 'capacidad' => 20, 'tipo' => 'taller', 'nivel' => 'todos', 'equipamiento' => 'Microscopios, Reactivos, Proyector', 'activo' => 1],
            ['codigo' => 'LAB-02', 'nombre' => 'Auditorio de Computación', 'capacidad' => 25, 'tipo' => 'auditorio', 'nivel' => 'todos', 'equipamiento' => '30 PCs, Proyector, Aire acondicionado', 'activo' => 1],
            ['codigo' => 'TALLER-01', 'nombre' => 'Taller de Arte', 'capacidad' => 20, 'tipo' => 'taller', 'nivel' => 'todos', 'equipamiento' => 'Mesas de trabajo, Materiales de arte', 'activo' => 1],
            ['codigo' => 'AUD-01', 'nombre' => 'Auditorio Principal', 'capacidad' => 100, 'tipo' => 'auditorio', 'nivel' => 'todos', 'equipamiento' => 'Proyector, Sonido, Aire acondicionado', 'activo' => 1],
            ['codigo' => 'VIR-01', 'nombre' => 'Aula Virtual 1', 'capacidad' => 50, 'tipo' => 'virtual', 'nivel' => 'todos', 'equipamiento' => 'Plataforma Zoom, Pizarra digital', 'activo' => 1],
            ['codigo' => 'A-301', 'nombre' => 'Aula 301', 'capacidad' => 40, 'tipo' => 'aula_normal', 'nivel' => 'academia', 'equipamiento' => 'Proyector, Aire acondicionado', 'activo' => 1],
        ];

        foreach ($aulas as $aula) {
            Aula::create($aula);
        }

        $this->command->info('✅ ' . count($aulas) . ' aulas creados');
    }
}
