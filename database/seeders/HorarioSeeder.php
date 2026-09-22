<?php
// database/seeders/HorarioSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Horario;
use App\Models\ProfesorCurso;
use App\Models\ConfiguracionHorario;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Iniciando generación de horarios...');

        // 1. Verificar que existan asignaciones
        $totalAsignaciones = ProfesorCurso::where('activo', true)->count();

        if ($totalAsignaciones === 0) {
            $this->command->warn('⚠️  No hay asignaciones activas. Saltando generación.');
            return;
        }

        $this->command->info("📚 Se encontraron {$totalAsignaciones} asignaciones activas");

        // 2. Verificar que existan configuraciones de horarios
        $totalConfiguraciones = ConfiguracionHorario::where('activo', true)->count();

        if ($totalConfiguraciones === 0) {
            $this->command->error('❌ No hay configuraciones de horarios activas.');
            $this->command->info('   Ejecuta primero: php artisan db:seed --class=ConfiguracionHorarioSeeder');
            return;
        }

        $this->command->info("⚙️  Se encontraron {$totalConfiguraciones} configuraciones activas");

        // 3. Generar horarios automáticamente
        $this->generarHorariosAutomaticos();
    }

    /**
     * Generar horarios automáticamente usando el motor
     */
    protected function generarHorariosAutomaticos(): void
    {
        try {
            $generator = app(\App\Services\HorarioGeneratorService::class);

            $resultado = $generator->generarHorarios([
                'institucion' => null, // Todas las instituciones
                'periodo_academico' => date('Y') . '-' . (date('Y') + 1),
                'respetar_existentes' => true,
            ]);

            if ($resultado['success']) {
                $this->command->info('✅ Horarios generados automáticamente');
                $this->mostrarEstadisticas($resultado);

                // Mostrar asignaciones incompletas si las hay
                if (!empty($resultado['asignaciones_incompletas'])) {
                    $this->mostrarAsignacionesIncompletas($resultado['asignaciones_incompletas']);
                }
            } else {
                $this->command->error('❌ Error al generar horarios automáticos');
                $this->command->error($resultado['error'] ?? 'Error desconocido');
            }

        } catch (\Exception $e) {
            $this->command->error('❌ Error en el generador automático: ' . $e->getMessage());
            $this->command->error('   Trace: ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    /**
     * Mostrar estadísticas del generador
     */
    protected function mostrarEstadisticas(array $resultado): void
    {
        $stats = $resultado['estadisticas'] ?? [];

        $this->command->info('📊 Estadísticas de generación:');
        $this->command->info('   - Horarios creados: ' . ($stats['horarios_creados'] ?? 0));
        $this->command->info('   - Total asignaciones: ' . ($stats['total_asignaciones'] ?? 0));
        $this->command->info('   - Horarios existentes: ' . ($stats['horarios_existentes'] ?? 0));
        $this->command->info('   - Conflictos: ' . ($stats['total_conflictos'] ?? 0));
        $this->command->info('   - Asignaciones incompletas: ' . ($stats['asignaciones_incompletas'] ?? 0));

        $tasaExito = $stats['tasa_exito'] ?? 0;
        if ($tasaExito > 80) {
            $this->command->info("   ✅ Tasa de éxito: {$tasaExito}%");
        } else {
            $this->command->warn("   ⚠️  Tasa de éxito: {$tasaExito}%");
        }

        // Mostrar conflictos
        if (!empty($resultado['conflictos'])) {
            $this->command->warn('⚠️  Conflictos detectados:');
            foreach (array_slice($resultado['conflictos'], 0, 5) as $conflicto) {
                $this->command->warn("   - {$conflicto['profesor']} - {$conflicto['curso']}: {$conflicto['motivo']}");
            }
            if (count($resultado['conflictos']) > 5) {
                $this->command->warn("   ... y " . (count($resultado['conflictos']) - 5) . " más");
            }
        }
    }

    /**
     * Mostrar asignaciones incompletas
     */
    protected function mostrarAsignacionesIncompletas(array $incompletas): void
    {
        $this->command->warn('📋 Asignaciones incompletas:');
        foreach (array_slice($incompletas, 0, 5) as $inc) {
            $this->command->warn(
                "   - {$inc['profesor']} - {$inc['curso']} ({$inc['grado']}): " .
                "{$inc['horas_asignadas']}/{$inc['horas_requeridas']} horas"
            );
        }
        if (count($incompletas) > 5) {
            $this->command->warn("   ... y " . (count($incompletas) - 5) . " más");
        }
    }
}
