<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            // 1. Agregar la columna 'institucion'
            $table->enum('institucion', ['colegio', 'academia'])
                  ->default('colegio')
                  ->after('turno');

            // 2. Eliminar los índices únicos originales
            $table->dropUnique('unique_profesor_horario');
            $table->dropUnique('unique_aula_horario');
            $table->dropUnique('unique_grado_horario');

            // 3. Crear de nuevo los índices únicos incluyendo 'institucion'
            // IMPORTANTE: Mantenemos los MISMOS nombres para consistencia
            $table->unique(['profesor_id', 'dia_semana', 'hora_inicio', 'periodo_academico', 'institucion'],
                          'unique_profesor_horario');
            $table->unique(['aula_id', 'dia_semana', 'hora_inicio', 'periodo_academico', 'institucion'],
                          'unique_aula_horario');
            $table->unique(['grado_id', 'dia_semana', 'hora_inicio', 'periodo_academico', 'institucion'],
                          'unique_grado_horario');
        });
    }

    public function down(): void
    {
        Schema::table('horarios', function (Blueprint $table) {
            // 1. Eliminar los índices (con los mismos nombres)
            $table->dropUnique('unique_profesor_horario');
            $table->dropUnique('unique_aula_horario');
            $table->dropUnique('unique_grado_horario');

            // 2. Recrear los índices originales sin 'institucion' (mismos nombres)
            $table->unique(['profesor_id', 'dia_semana', 'hora_inicio', 'periodo_academico'], 'unique_profesor_horario');
            $table->unique(['aula_id', 'dia_semana', 'hora_inicio', 'periodo_academico'], 'unique_aula_horario');
            $table->unique(['grado_id', 'dia_semana', 'hora_inicio', 'periodo_academico'], 'unique_grado_horario');

            // 3. Eliminar la columna 'institucion'
            $table->dropColumn('institucion');
        });
    }
};
