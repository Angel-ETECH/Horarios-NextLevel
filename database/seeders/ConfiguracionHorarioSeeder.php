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
        $primaria = ConfiguracionHorario::updateOrCreate(
            [
                'institucion' => 'colegio',
                'nivel' => 'primaria',
                'turno' => 'mañana',
                'año_academico' => $añoActual,
            ],
            [
                'nombre' => 'Primaria Mañana ' . $añoActual,
                'hora_inicio' => '07:00',
                'hora_fin' => '12:55',
                'duracion_bloque_minutos' => 45,
                'activo' => true,
            ]
        );

        $this->crearBloques($primaria);

        // ==========================================
        // COLEGIO - SECUNDARIA (Turno Mañana)
        // ==========================================
        $secundaria = ConfiguracionHorario::updateOrCreate(
            [
                'institucion' => 'colegio',
                'nivel' => 'secundaria',
                'turno' => 'mañana',
                'año_academico' => $añoActual,
            ],
            [
                'nombre' => 'Secundaria Mañana ' . $añoActual,
                'hora_inicio' => '07:00',
                'hora_fin' => '13:10',
                'duracion_bloque_minutos' => 45,
                'activo' => true,
            ]
        );

        $this->crearBloques($secundaria);

        // ==========================================
        // COLEGIO - PRIMARIA (Turno Tarde)
        // ==========================================
        $primariaTarde = ConfiguracionHorario::updateOrCreate(
            [
                'institucion' => 'colegio',
                'nivel' => 'primaria',
                'turno' => 'tarde',
                'año_academico' => $añoActual,
            ],
            [
                'nombre' => 'Primaria Tarde ' . $añoActual,
                'hora_inicio' => '13:00',
                'hora_fin' => '18:55',
                'duracion_bloque_minutos' => 45,
                'activo' => true,
            ]
        );

        $this->crearBloques($primariaTarde);

        // ==========================================
        // COLEGIO - SECUNDARIA (Turno Tarde)
        // ==========================================
        $secundariaTarde = ConfiguracionHorario::updateOrCreate(
            [
                'institucion' => 'colegio',
                'nivel' => 'secundaria',
                'turno' => 'tarde',
                'año_academico' => $añoActual,
            ],
            [
                'nombre' => 'Secundaria Tarde ' . $añoActual,
                'hora_inicio' => '13:00',
                'hora_fin' => '19:00',
                'duracion_bloque_minutos' => 45,
                'activo' => true,
            ]
        );

        $this->crearBloques($secundariaTarde);

        // ==========================================
        // ACADEMIA - Turno Mañana
        // ==========================================
        $academiaMañana = ConfiguracionHorario::updateOrCreate(
            [
                'institucion' => 'academia',
                'nivel' => 'academia',
                'turno' => 'mañana',
                'año_academico' => $añoActual,
            ],
            [
                'nombre' => 'Academia Mañana ' . $añoActual,
                'hora_inicio' => '07:00',
                'hora_fin' => '13:15',
                'duracion_bloque_minutos' => 45,
                'activo' => true,
            ]
        );

        $this->crearBloques($academiaMañana);

        // ==========================================
        // ACADEMIA - Turno Tarde
        // ==========================================
        $academiaTarde = ConfiguracionHorario::updateOrCreate(
            [
                'institucion' => 'academia',
                'nivel' => 'academia',
                'turno' => 'tarde',
                'año_academico' => $añoActual,
            ],
            [
                'nombre' => 'Academia Tarde ' . $añoActual,
                'hora_inicio' => '15:30',
                'hora_fin' => '20:00',
                'duracion_bloque_minutos' => 90,
                'activo' => true,
            ]
        );

        $this->crearBloques($academiaTarde);

        // ==========================================
        // ACADEMIA - Turno Completo (A1)
        // ==========================================
        $academiaCompleto = ConfiguracionHorario::updateOrCreate(
            [
                'institucion' => 'academia',
                'nivel' => 'academia',
                'turno' => 'completo',
                'año_academico' => $añoActual,
            ],
            [
                'nombre' => 'Academia Completo ' . $añoActual,
                'hora_inicio' => '07:00',
                'hora_fin' => '18:30',
                'duracion_bloque_minutos' => 45,
                'activo' => true,
            ]
        );

        $this->crearBloques($academiaCompleto);
    }

    private function crearBloques(ConfiguracionHorario $config): void
    {
        // Eliminar bloques previos de esta configuración para evitar duplicados
        BloqueHorario::where('configuracion_horario_id', $config->id)->delete();

        $bloques = $this->generarBloquesNormalizados($config);

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

    private function generarBloquesNormalizados(ConfiguracionHorario $config): array
    {
        return app(\App\Services\HorarioRecesoService::class)->bloquesFijos($config)
            ->map(fn ($bloque) => [
                'orden' => $bloque->orden,
                'inicio' => $bloque->hora_inicio->format('H:i'),
                'fin' => $bloque->hora_fin->format('H:i'),
                'tipo' => $bloque->tipo,
                'nombre' => $bloque->nombre,
                'numero' => $bloque->numero_bloque,
            ])->all();
    }
}
