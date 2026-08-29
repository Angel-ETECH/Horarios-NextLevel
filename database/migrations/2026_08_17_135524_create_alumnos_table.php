<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 50);
            $table->string('apellido_materno', 50)->nullable();
            $table->string('dni', 8)->unique();
            $table->string('email', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->date('fecha_nacimiento');
            $table->string('direccion', 200)->nullable();
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();
            $table->foreignId('grado_id')->constrained('grados')->onDelete('restrict');
            $table->date('fecha_ingreso');
            $table->enum('estado', ['activo', 'inactivo', 'trasladado', 'retirado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('grado_id');
            $table->index('estado');
            $table->index('dni');
            $table->index('codigo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
