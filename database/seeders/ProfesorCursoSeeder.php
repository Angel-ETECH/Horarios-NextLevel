<?php
// database/seeders/ProfesorCursoSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfesorCurso;
use App\Models\Aula;
use App\Models\Grado;
use App\Models\Curso;

class ProfesorCursoSeeder extends Seeder
{
    public function run(): void
    {
        $relaciones = [
            // ==========================================
            // Carlos García enseña Matemáticas
            // ==========================================
            ['profesor_id' => 1, 'curso_id' => 1, 'grado_id' => 1, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 1ro Primaria A'],
            ['profesor_id' => 1, 'curso_id' => 1, 'grado_id' => 2, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 1ro Primaria B'],
            ['profesor_id' => 1, 'curso_id' => 1, 'grado_id' => 3, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 2do Primaria A'],
            ['profesor_id' => 1, 'curso_id' => 7, 'grado_id' => 8, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 1ro Secundaria A'],
            ['profesor_id' => 1, 'curso_id' => 7, 'grado_id' => 9, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas 1ro Secundaria B'],
            ['profesor_id' => 1, 'curso_id' => 12, 'grado_id' => 14, 'horas_asignadas' => 8, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Matemáticas Pre Academia A'],

            // ==========================================
            // María Rodríguez enseña Comunicación
            // ==========================================
            ['profesor_id' => 2, 'curso_id' => 2, 'grado_id' => 1, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación 1ro Primaria A'],
            ['profesor_id' => 2, 'curso_id' => 2, 'grado_id' => 2, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación 1ro Primaria B'],
            ['profesor_id' => 2, 'curso_id' => 8, 'grado_id' => 8, 'horas_asignadas' => 5, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación 1ro Secundaria A'],
            ['profesor_id' => 2, 'curso_id' => 8, 'grado_id' => 9, 'horas_asignadas' => 5, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación 1ro Secundaria B'],
            ['profesor_id' => 2, 'curso_id' => 13, 'grado_id' => 14, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Comunicación Pre Academia A'],

            // ==========================================
            // Juan Martínez enseña Ciencias Naturales
            // ==========================================
            ['profesor_id' => 3, 'curso_id' => 3, 'grado_id' => 3, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Ciencia y Tecnología 2do Primaria A'],
            ['profesor_id' => 3, 'curso_id' => 10, 'grado_id' => 10, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Ciencias Naturales 2do Secundaria A'],
            ['profesor_id' => 3, 'curso_id' => 15, 'grado_id' => 14, 'horas_asignadas' => 6, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Ciencias Integradas Pre Academia A'],

            // ==========================================
            // Ana Torres enseña Inglés
            // ==========================================
            ['profesor_id' => 4, 'curso_id' => 5, 'grado_id' => 3, 'horas_asignadas' => 3, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Inglés 2do Primaria A'],
            ['profesor_id' => 4, 'curso_id' => 9, 'grado_id' => 8, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Inglés 1ro Secundaria A'],
            ['profesor_id' => 4, 'curso_id' => 14, 'grado_id' => 14, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Inglés Pre Academia A'],

            // ==========================================
            // Luis Ramírez enseña Historia
            // ==========================================
            ['profesor_id' => 5, 'curso_id' => 4, 'grado_id' => 4, 'horas_asignadas' => 3, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Personal Social 3ro Primaria A'],
            ['profesor_id' => 5, 'curso_id' => 11, 'grado_id' => 10, 'horas_asignadas' => 4, 'rol' => 'titular', 'activo' => 1, 'observaciones' => 'Historia y Geografía 2do Secundaria A'],
        ];

        $creadas = 0;
        $sinAula = 0;

        foreach ($relaciones as $relacion) {
            // ✅ BUSCAR AULA ADECUADA AUTOMÁTICAMENTE
            $aulaId = $this->buscarAulaAdecuada(
                $relacion['grado_id'],
                $relacion['curso_id']
            );

            if (!$aulaId) {
                $this->command->warn(
                    "⚠️  No se encontró aula para: {$relacion['observaciones']}"
                );
                $sinAula++;
            }

            $relacion['aula_id'] = $aulaId;

            // ✅ USAR updateOrCreate para evitar duplicados
            ProfesorCurso::updateOrCreate(
                [
                    'profesor_id' => $relacion['profesor_id'],
                    'curso_id' => $relacion['curso_id'],
                    'grado_id' => $relacion['grado_id'],
                ],
                $relacion
            );

            $creadas++;
        }

        $this->command->info("✅ {$creadas} relaciones profesor-curso creadas");

        if ($sinAula > 0) {
            $this->command->warn("⚠️  {$sinAula} relaciones sin aula asignada");
        }
    }

    /**
     * Buscar un aula adecuada para un grado y curso específicos
     *
     * Criterios:
     * - Nivel compatible (primaria, secundaria, academia, todos)
     * - Capacidad suficiente para los estudiantes del grado
     * - Tipo adecuado según el curso (laboratorio para ciencias, etc.)
     * - Aula activa
     */
    private function buscarAulaAdecuada(int $gradoId, int $cursoId): ?int
    {
        $grado = Grado::find($gradoId);
        $curso = Curso::find($cursoId);

        if (!$grado || !$curso) {
            return null;
        }

        $numeroEstudiantes = $grado->numero_estudiantes ?? 0;

        // ==========================================
        // 1. DETERMINAR TIPO DE AULA NECESARIA
        // ==========================================
        $tipoAulaNecesaria = $this->determinarTipoAula($curso);

        // ==========================================
        // 2. BUSCAR AULA QUE CUMPLA LOS CRITERIOS
        // ==========================================
        $query = Aula::where('activo', true)
            ->where('capacidad', '>=', $numeroEstudiantes)
            ->where(function ($q) use ($grado) {
                $q->where('nivel', $grado->nivel)
                  ->orWhere('nivel', 'todos');
            });

        // Primero intentar con el tipo específico
        if ($tipoAulaNecesaria) {
            $aula = (clone $query)
                ->where('tipo', $tipoAulaNecesaria)
                ->orderBy('capacidad', 'asc')
                ->first();

            if ($aula) {
                return $aula->id;
            }
        }

        // Si no hay del tipo específico, buscar cualquiera
        $aula = $query
            ->orderBy('capacidad', 'asc')
            ->first();

        return $aula?->id;
    }

    /**
     * Determinar el tipo de aula necesaria según el curso
     */
    private function determinarTipoAula(Curso $curso): ?string
    {
        $nombre = strtolower($curso->nombre ?? '');
        $codigo = strtolower($curso->codigo ?? '');

        // Laboratorio para ciencias
        if (
            str_contains($nombre, 'ciencia') ||
            str_contains($nombre, 'biolog') ||
            str_contains($nombre, 'quimic') ||
            str_contains($nombre, 'fisic') ||
            str_contains($nombre, 'laboratorio') ||
            str_contains($codigo, 'lab')
        ) {
            return 'laboratorio';
        }

        // Taller para arte, música, etc.
        if (
            str_contains($nombre, 'arte') ||
            str_contains($nombre, 'musica') ||
            str_contains($nombre, 'taller') ||
            str_contains($nombre, 'danza')
        ) {
            return 'taller';
        }

        // Aula normal para el resto
        return 'aula_normal';
    }
}
