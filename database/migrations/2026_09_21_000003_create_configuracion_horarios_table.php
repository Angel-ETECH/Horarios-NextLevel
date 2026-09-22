<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_horarios', function (Blueprint $table) {
            $table->id();
            $table->enum('institucion', ['colegio', 'academia']);
            $table->enum('nivel', ['primaria', 'secundaria', 'academia']);
            $table->enum('turno', ['mañana', 'tarde', 'completo']);
            $table->string('nombre', 100);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('duracion_bloque_minutos')->default(45);
            $table->integer('año_academico');
            $table->boolean('activo')->default(true);
            $table->text('observacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['institucion', 'nivel', 'turno', 'año_academico'],
                'unique_config_horario'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_horarios');
    }
};
