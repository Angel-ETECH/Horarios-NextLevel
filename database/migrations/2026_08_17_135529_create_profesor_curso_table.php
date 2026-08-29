<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesor_curso', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profesor_id')
                  ->constrained('profesores')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('curso_id')
                  ->constrained('cursos')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('grado_id')
                  ->constrained('grados')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->integer('horas_asignadas')->default(0);
            $table->enum('rol', ['titular', 'asistente', 'suplente'])->default('titular');
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('profesor_id');
            $table->index('curso_id');
            $table->index('grado_id');

            // Evita duplicados
            $table->unique(['profesor_id', 'curso_id', 'grado_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesor_curso');
    }
};
