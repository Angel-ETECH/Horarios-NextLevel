<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profesor_curso', function (Blueprint $table) {
            // 1. Agregar la columna institucion (si no existe)
            if (!Schema::hasColumn('profesor_curso', 'institucion')) {
                $table->enum('institucion', ['colegio', 'academia'])
                      ->default('colegio')
                      ->after('rol');
            }

            // 2. Eliminar el índice actual (si existe)
            // Nota: Laravel no puede verificar si un índice existe fácilmente,
            // así que lo eliminamos directamente (si no existe, fallará, pero lo hacemos con try-catch o verificando manualmente)
            try {
                $table->dropUnique('unique_asignacion');
            } catch (\Exception $e) {
                // Si el índice no existe, ignorar el error
            }

            // 3. Crear el índice único incluyendo 'institucion'
            $table->unique(['profesor_id', 'curso_id', 'grado_id', 'institucion'], 'unique_asignacion');
        });
    }

    public function down(): void
    {
        Schema::table('profesor_curso', function (Blueprint $table) {
            // Eliminar el índice con institución
            $table->dropUnique('unique_asignacion');

            // Recrear el índice sin institución
            $table->unique(['profesor_id', 'curso_id', 'grado_id'], 'unique_asignacion');

            // Eliminar la columna institucion
            $table->dropColumn('institucion');
        });
    }
};
