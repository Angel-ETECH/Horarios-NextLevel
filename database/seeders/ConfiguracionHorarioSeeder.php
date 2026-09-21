<?php
// database/seeders/ConfiguracionHorarioSeeder.php

namespace Database\Seeders;

use App\Models\ConfiguracionHorario;
use App\Models\BloqueHorario;
use Illuminate\Database\Seeder;

class ConfiguracionHorarioSeeder extends Seeder
{
    public function run(): void
    {
        $añoActual = date('Y');

        // ==========================================
        // COLEGIO - PRIMARIA (Turno Mañana)
        // ==========================================
        $primaria = ConfiguracionHorario::create([
            'institucion' => 'colegio',
            'nivel' => 'primaria',
            'turno' => 'mañana',
            'nombre' => 'Primaria Mañana ' . $añoActual,
            'hora_inicio' => '07:00',
            'hora_fin' => '12:55',
            'duracion_bloque_minutos' => 45,
            'año_academico' => $añoActual,
            'activo' => true,
        ]);

        $this->crearBloques($primaria, [
            ['orden' => 1, 'inicio' => '07:00', 'fin' => '07:45', 'tipo' => 'clase', 'numero' => 1],
            ['orden' => 2, 'inicio' => '07:45', 'fin' => '08:30', 'tipo' => 'clase', 'numero' => 2],
            ['orden' => 3, 'inicio' => '08:30', 'fin' => '08:50', 'tipo' => 'receso', 'nombre' => 'Recreo 1'],
            ['orden' => 4, 'inicio' => '08:50', 'fin' => '09:35', 'tipo' => 'clase', 'numero' => 3],
            ['orden' => 5, 'inicio' => '09:35', 'fin' => '10:20', 'tipo' => 'clase', 'numero' => 4],
            ['orden' => 6, 'inicio' => '10:20', 'fin' => '10:40', 'tipo' => 'receso', 'nombre' => 'Recreo 2'],
            ['orden' => 7, 'inicio' => '10:40', 'fin' => '11:25', 'tipo' => 'clase', 'numero' => 5],
            ['orden' => 8, 'inicio' => '11:25', 'fin' => '12:15', 'tipo' => 'clase', 'numero' => 6],
            ['orden' => 9, 'inicio' => '12:15', 'fin' => '12:55', 'tipo' => 'clase', 'numero' => 7],
        ]);

        // ==========================================
        // COLEGIO - SECUNDARIA (Turno Mañana)
        // ==========================================
        $secundaria = ConfiguracionHorario::create([
            'institucion' => 'colegio',
            'nivel' => 'secundaria',
            'turno' => 'mañana',
            'nombre' => 'Secundaria Mañana ' . $añoActual,
            'hora_inicio' => '07:00',
            'hora_fin' => '13:10',
            'duracion_bloque_minutos' => 45,
            'año_academico' => $añoActual,
            'activo' => true,
        ]);

        $this->crearBloques($secundaria, [
            ['orden' => 1, 'inicio' => '07:00', 'fin' => '07:45', 'tipo' => 'clase', 'numero' => 1],
            ['orden' => 2, 'inicio' => '07:45', 'fin' => '08:30', 'tipo' => 'clase', 'numero' => 2],
            ['orden' => 3, 'inicio' => '08:30', 'fin' => '09:15', 'tipo' => 'clase', 'numero' => 3],
            ['orden' => 4, 'inicio' => '09:15', 'fin' => '09:35', 'tipo' => 'receso', 'nombre' => 'Receso 1'],
            ['orden' => 5, 'inicio' => '09:35', 'fin' => '10:25', 'tipo' => 'clase', 'numero' => 4],
            ['orden' => 6, 'inicio' => '10:25', 'fin' => '11:10', 'tipo' => 'clase', 'numero' => 5],
            ['orden' => 7, 'inicio' => '11:10', 'fin' => '11:25', 'tipo' => 'receso', 'nombre' => 'Receso 2'],
            ['orden' => 8, 'inicio' => '11:25', 'fin' => '12:15', 'tipo' => 'clase', 'numero' => 6],
            ['orden' => 9, 'inicio' => '12:15', 'fin' => '13:10', 'tipo' => 'clase', 'numero' => 7],
        ]);

        // ==========================================
        // ACADEMIA - Turno Mañana
        // ==========================================
        $academiaMañana = ConfiguracionHorario::create([
            'institucion' => 'academia',
            'nivel' => 'academia',
            'turno' => 'mañana',
            'nombre' => 'Academia Mañana ' . $añoActual,
            'hora_inicio' => '07:00',
            'hora_fin' => '13:15',
            'duracion_bloque_minutos' => 45,
            'año_academico' => $añoActual,
            'activo' => true,
        ]);

        $this->crearBloques($academiaMañana, [
            ['orden' => 1, 'inicio' => '07:00', 'fin' => '07:55', 'tipo' => 'clase', 'numero' => 1],
            ['orden' => 2, 'inicio' => '07:55', 'fin' => '08:50', 'tipo' => 'clase', 'numero' => 2],
            ['orden' => 3, 'inicio' => '08:50', 'fin' => '09:10', 'tipo' => 'receso', 'nombre' => 'Receso 1'],
            ['orden' => 4, 'inicio' => '09:10', 'fin' => '09:55', 'tipo' => 'clase', 'numero' => 3],
            ['orden' => 5, 'inicio' => '09:55', 'fin' => '10:40', 'tipo' => 'clase', 'numero' => 4],
            ['orden' => 6, 'inicio' => '10:40', 'fin' => '10:50', 'tipo' => 'receso', 'nombre' => 'Receso 2'],
            ['orden' => 7, 'inicio' => '10:50', 'fin' => '11:35', 'tipo' => 'clase', 'numero' => 5],
            ['orden' => 8, 'inicio' => '11:35', 'fin' => '12:15', 'tipo' => 'clase', 'numero' => 6],
            ['orden' => 9, 'inicio' => '12:15', 'fin' => '13:15', 'tipo' => 'clase', 'numero' => 7],
        ]);

        // ==========================================
        // ACADEMIA - Turno Tarde
        // ==========================================
        $academiaTarde = ConfiguracionHorario::create([
            'institucion' => 'academia',
            'nivel' => 'academia',
            'turno' => 'tarde',
            'nombre' => 'Academia Tarde ' . $añoActual,
            'hora_inicio' => '15:30',
            'hora_fin' => '20:00',
            'duracion_bloque_minutos' => 90,
            'año_academico' => $añoActual,
            'activo' => true,
        ]);

        $this->crearBloques($academiaTarde, [
            ['orden' => 1, 'inicio' => '15:30', 'fin' => '17:00', 'tipo' => 'clase', 'numero' => 1],
            ['orden' => 2, 'inicio' => '17:00', 'fin' => '17:10', 'tipo' => 'receso', 'nombre' => 'Receso 1'],
            ['orden' => 3, 'inicio' => '17:10', 'fin' => '18:30', 'tipo' => 'clase', 'numero' => 2],
            ['orden' => 4, 'inicio' => '18:30', 'fin' => '18:40', 'tipo' => 'receso', 'nombre' => 'Receso 2'],
            ['orden' => 5, 'inicio' => '18:40', 'fin' => '20:00', 'tipo' => 'clase', 'numero' => 3],
        ]);

        // ==========================================
        // ACADEMIA - Turno Completo (A1)
        // ==========================================
        $academiaCompleto = ConfiguracionHorario::create([
            'institucion' => 'academia',
            'nivel' => 'academia',
            'turno' => 'completo',
            'nombre' => 'Academia Completo ' . $añoActual,
            'hora_inicio' => '07:00',
            'hora_fin' => '18:30',
            'duracion_bloque_minutos' => 45,
            'año_academico' => $añoActual,
            'activo' => true,
        ]);

        $this->crearBloques($academiaCompleto, [
            // Mañana
            ['orden' => 1, 'inicio' => '07:00', 'fin' => '07:55', 'tipo' => 'clase', 'numero' => 1],
            ['orden' => 2, 'inicio' => '07:55', 'fin' => '08:50', 'tipo' => 'clase', 'numero' => 2],
            ['orden' => 3, 'inicio' => '08:50', 'fin' => '09:10', 'tipo' => 'receso', 'nombre' => 'Receso 1'],
            ['orden' => 4, 'inicio' => '09:10', 'fin' => '09:55', 'tipo' => 'clase', 'numero' => 3],
            ['orden' => 5, 'inicio' => '09:55', 'fin' => '10:40', 'tipo' => 'clase', 'numero' => 4],
            ['orden' => 6, 'inicio' => '10:40', 'fin' => '10:50', 'tipo' => 'receso', 'nombre' => 'Receso 2'],
            ['orden' => 7, 'inicio' => '10:50', 'fin' => '11:35', 'tipo' => 'clase', 'numero' => 5],
            ['orden' => 8, 'inicio' => '11:35', 'fin' => '12:15', 'tipo' => 'clase', 'numero' => 6],
            ['orden' => 9, 'inicio' => '12:15', 'fin' => '13:15', 'tipo' => 'clase', 'numero' => 7],
            // Almuerzo
            ['orden' => 10, 'inicio' => '13:15', 'fin' => '15:30', 'tipo' => 'receso', 'nombre' => 'Almuerzo'],
            // Tarde
            ['orden' => 11, 'inicio' => '15:30', 'fin' => '17:00', 'tipo' => 'clase', 'numero' => 8],
            ['orden' => 12, 'inicio' => '17:00', 'fin' => '17:10', 'tipo' => 'receso', 'nombre' => 'Receso 3'],
            ['orden' => 13, 'inicio' => '17:10', 'fin' => '18:30', 'tipo' => 'clase', 'numero' => 9],
        ]);
    }

    private function crearBloques(ConfiguracionHorario $config, array $bloques): void
    {
        foreach ($bloques as $bloque) {
            BloqueHorario::create([
                'configuracion_horario_id' => $config->id,
                'orden' => $bloque['orden'],
                'hora_inicio' => $bloque['inicio'],
                'hora_fin' => $bloque['fin'],
                'tipo' => $bloque['tipo'],
                'nombre' => $bloque['nombre'] ?? null,
                'numero_bloque' => $bloque['numero'] ?? null,
            ]);
        }
    }
}
