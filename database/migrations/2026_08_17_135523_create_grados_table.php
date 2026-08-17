<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->enum('nivel', ['primaria', 'secundaria', 'academia']);
            $table->string('grado', 20);
            $table->string('seccion', 5)->nullable();
            $table->string('nombre_completo', 50);
            $table->integer('capacidad_maxima')->default(35);
            $table->integer('numero_estudiantes')->default(0);
            $table->integer('año_academico');
            $table->enum('turno', ['mañana', 'tarde', 'noche', 'completo'])->default('mañana');
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Evita duplicados
            $table->unique(['nivel', 'grado', 'seccion', 'año_academico']);
            $table->index('nivel');
            $table->index('año_academico');
            $table->index('activo');
            $table->index('codigo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grados');
    }
};
