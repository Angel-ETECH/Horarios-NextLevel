<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disponibilidad_profesor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesor_id')
                  ->constrained('profesores')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->enum('dia_semana', [
                'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'
            ]);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('tipo', ['disponible', 'no_disponible'])->default('disponible');
            $table->enum('turno', ['mañana', 'tarde', 'noche'])->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('profesor_id');
            $table->index('dia_semana');
            $table->index('turno');

            // Evita duplicados
            $table->unique(['profesor_id', 'dia_semana', 'hora_inicio', 'hora_fin'], 'disp_prof_unique');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disponibilidad_profesor');
    }
};
