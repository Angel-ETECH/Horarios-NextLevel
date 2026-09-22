<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disponibilidad_profesor', function (Blueprint $table) {
            $table->enum('institucion', ['colegio', 'academia'])
                  ->default('colegio')
                  ->after('turno');

            // Eliminar el índice único existente
            $table->dropUnique('disp_prof_unique');

            // Crear el nuevo índice único incluyendo la nueva columna 'institucion'
            $table->unique(['profesor_id', 'dia_semana', 'hora_inicio', 'institucion'],
                          'disp_prof_unique'); // Mantengo el mismo nombre para consistencia
        });
    }

    public function down(): void
    {
        Schema::table('disponibilidad_profesor', function (Blueprint $table) {
            // Eliminar el nuevo índice único
            $table->dropUnique('disp_prof_unique');

            // Recrear el índice único original sin 'institucion'
            $table->unique(['profesor_id', 'dia_semana', 'hora_inicio'], 'disp_prof_unique');

            // Eliminar la columna 'institucion'
            $table->dropColumn('institucion');
        });
    }
};
