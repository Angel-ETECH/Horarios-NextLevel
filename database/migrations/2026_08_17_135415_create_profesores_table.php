<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profesores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 50);
            $table->string('apellido_materno', 50)->nullable();
            $table->string('email', 100)->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('dni', 8)->unique();
            $table->enum('sexo', ['masculino', 'femenino'])->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('especialidad', 100)->nullable();
            $table->integer('carga_horaria_maxima')->default(30);
            $table->enum('estado', ['activo', 'inactivo', 'licencia'])->default('activo');
            $table->integer('carga_horaria_actual')->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nombre', 'apellido_paterno']);
            $table->index('email');
            $table->index('estado');
            $table->index('especialidad');
            $table->index('dni');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profesores');
    }
};
