<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recuperaciones_horario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_id')->nullable()->constrained('horarios')->nullOnDelete();
            $table->date('fecha_original');
            $table->date('fecha_recuperacion');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->foreignId('aula_id')->constrained('aulas')->restrictOnDelete();
            $table->time('disponible_desde')->nullable();
            $table->time('disponible_hasta')->nullable();
            $table->boolean('disponibilidad_excepcional')->default(false);
            $table->boolean('jornada_autorizada')->default(false);
            $table->string('motivo', 255);
            $table->string('estado', 20)->default('programada');
            $table->json('original');
            $table->foreignId('creado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['horario_id', 'fecha_original']);
            $table->index(['estado', 'fecha_recuperacion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recuperaciones_horario');
    }
};
