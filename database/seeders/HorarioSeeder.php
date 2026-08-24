<?php
// database/seeders/HorarioSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Horario;
use App\Models\ProfesorCurso;
use App\Models\Aula;
use Illuminate\Support\Facades\DB;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Iniciando generación de horarios...');

        // Verificar si hay asignaciones para generar horarios
        $totalAsignaciones = ProfesorCurso::where('activo', true)->count();

        if ($totalAsignaciones > 0) {
            $this->command->info("📚 Se encontraron {$totalAsignaciones} asignaciones activas");
            $this->generarHorariosAutomaticos();
        } else {
            $this->command->warn('⚠️  No hay asignaciones activas. Usando datos manuales...');
            $this->crearHorariosManuales();
        }
    }

    /**
     * Generar horarios automáticamente usando el motor
     */
    protected function generarHorariosAutomaticos(): void
    {
        try {
            $generator = app(\App\Services\HorarioGeneratorService::class);
            $resultado = $generator->generarHorarios();

            if ($resultado['success']) {
                $this->command->info('✅ Horarios generados automáticamente');
                $this->mostrarEstadisticas($resultado);
            } else {
                $this->command->error('❌ Error al generar horarios automáticos');
                $this->command->error($resultado['error'] ?? 'Error desconocido');
                $this->crearHorariosManuales();
            }

        } catch (\Exception $e) {
            $this->command->error('❌ Error en el generador automático: ' . $e->getMessage());
            $this->command->info('📝 Usando horarios manuales como respaldo...');
            $this->crearHorariosManuales();
        }
    }

    /**
     * Crear horarios manuales (tu seeder original)
     */
    protected function crearHorariosManuales(): void
    {
        $this->command->info('📝 Creando horarios manuales...');

        $horarios = $this->getHorariosManuales();

        // Limpiar horarios existentes (opcional)
        // Horario::truncate();

        $contador = 0;
        foreach ($horarios as $horario) {
            // Verificar que no exista duplicado
            $exists = Horario::where('profesor_id', $horario['profesor_id'])
                ->where('dia_semana', $horario['dia_semana'])
                ->where('hora_inicio', $horario['hora_inicio'])
                ->where('grado_id', $horario['grado_id'])
                ->exists();

            if (!$exists) {
                Horario::create($horario);
                $contador++;
            }
        }

        $this->command->info("✅ {$contador} horarios manuales creados");
    }

    /**
     * Mostrar estadísticas del generador
     */
    protected function mostrarEstadisticas(array $resultado): void
    {
        $this->command->info('📊 Estadísticas de generación:');
        $this->command->info('   - Horarios creados: ' . ($resultado['estadisticas']['horarios_creados'] ?? 0));
        $this->command->info('   - Total asignaciones: ' . ($resultado['estadisticas']['total_asignaciones'] ?? 0));
        $this->command->info('   - Conflictos: ' . count($resultado['conflictos'] ?? []));

        $tasaExito = $resultado['estadisticas']['tasa_exito'] ?? 0;
        if ($tasaExito > 80) {
            $this->command->info("   ✅ Tasa de éxito: {$tasaExito}%");
        } else {
            $this->command->warn("   ⚠️  Tasa de éxito: {$tasaExito}%");
        }

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
     * Obtener horarios manuales (tu seeder original)
     */
    protected function getHorariosManuales(): array
    {
        return [
            // Carlos García - Matemáticas 1ro Primaria A (6 horas: Lunes, Miércoles, Viernes - 2 horas cada día)
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

            // Juan Martínez - Ciencias Naturales 2do Secundaria A
            ['profesor_id' => 3, 'curso_id' => 10, 'aula_id' => 15, 'grado_id' => 10, 'dia_semana' => 'lunes', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => 'Clase de Ciencias Naturales'],
            ['profesor_id' => 3, 'curso_id' => 10, 'aula_id' => 15, 'grado_id' => 10, 'dia_semana' => 'miércoles', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 3, 'curso_id' => 10, 'aula_id' => 15, 'grado_id' => 10, 'dia_semana' => 'viernes', 'hora_inicio' => '14:00', 'hora_fin' => '16:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],

            // Ana Torres - Inglés 1ro Secundaria A
            ['profesor_id' => 4, 'curso_id' => 9, 'aula_id' => 13, 'grado_id' => 8, 'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => 'Clase de Inglés 1S'],
            ['profesor_id' => 4, 'curso_id' => 9, 'aula_id' => 13, 'grado_id' => 8, 'dia_semana' => 'lunes', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 4, 'curso_id' => 9, 'aula_id' => 13, 'grado_id' => 8, 'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 4, 'curso_id' => 9, 'aula_id' => 13, 'grado_id' => 8, 'dia_semana' => 'martes', 'hora_inicio' => '08:00', 'hora_fin' => '09:00', 'turno' => 'mañana', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],

            // Luis Ramírez - Historia 2do Secundaria A
            ['profesor_id' => 5, 'curso_id' => 11, 'aula_id' => 14, 'grado_id' => 10, 'dia_semana' => 'martes', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => 'Clase de Historia 2S'],
            ['profesor_id' => 5, 'curso_id' => 11, 'aula_id' => 14, 'grado_id' => 10, 'dia_semana' => 'jueves', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
            ['profesor_id' => 5, 'curso_id' => 11, 'aula_id' => 14, 'grado_id' => 10, 'dia_semana' => 'viernes', 'hora_inicio' => '13:00', 'hora_fin' => '14:00', 'turno' => 'tarde', 'tipo' => 'regular', 'semana' => 1, 'periodo_academico' => '2024-1', 'estado' => 'activo', 'version' => 1, 'creado_por' => 'admin', 'observacion' => null],
        ];
    }
}
