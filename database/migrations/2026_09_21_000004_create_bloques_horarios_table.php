<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloques_horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configuracion_horario_id')
                  ->constrained('configuracion_horarios')
                  ->onDelete('cascade');
            $table->integer('orden');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->enum('tipo', ['clase', 'receso']);
            $table->string('nombre', 50)->nullable();
            $table->integer('numero_bloque')->nullable();
            $table->timestamps();

            $table->index(['configuracion_horario_id', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloques_horarios');
    }
};
