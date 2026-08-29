<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();

            // Claves foráneas
            $table->foreignId('profesor_id')
                  ->constrained('profesores')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->foreignId('curso_id')
                  ->constrained('cursos')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->foreignId('aula_id')
                  ->constrained('aulas')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
            $table->foreignId('grado_id')
                  ->constrained('grados')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            // Datos del horario
            $table->enum('dia_semana', [
                'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'
            ]);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('turno', ['mañana', 'tarde', 'noche'])->nullable();

            // Información adicional
            $table->enum('tipo', ['regular', 'recuperacion', 'cambio'])->default('regular');
            $table->integer('semana')->default(1);
            $table->string('periodo_academico', 20);
            $table->enum('estado', ['activo', 'modificado', 'cancelado'])->default('activo');
            $table->integer('version')->default(1);
            $table->string('creado_por', 100)->nullable();
            $table->string('modificado_por', 100)->nullable();
            $table->text('observacion')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ============================================
            // RESTRICCIONES ÚNICAS (Evitan cruces)
            // ============================================

            // 1. Sin cruce de profesor
            $table->unique(['profesor_id', 'dia_semana', 'hora_inicio', 'periodo_academico'],
                          'unique_profesor_horario');

            // 2. Sin cruce de aula
            $table->unique(['aula_id', 'dia_semana', 'hora_inicio', 'periodo_academico'],
                          'unique_aula_horario');

            // 3. Sin cruce de grado
            $table->unique(['grado_id', 'dia_semana', 'hora_inicio', 'periodo_academico'],
                          'unique_grado_horario');

            // Índices para búsquedas
            $table->index('dia_semana');
            $table->index('turno');
            $table->index('estado');
            $table->index('periodo_academico');
            $table->index(['profesor_id', 'dia_semana']);
            $table->index(['aula_id', 'dia_semana']);
            $table->index(['grado_id', 'dia_semana']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
