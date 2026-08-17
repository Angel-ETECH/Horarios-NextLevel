<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->integer('horas_semanales')->default(4);
            $table->integer('duracion_minutos')->default(60);
            $table->enum('nivel', ['primaria', 'secundaria', 'academia', 'todos'])->default('todos');
            $table->enum('tipo', ['obligatorio', 'electivo', 'taller'])->default('obligatorio');
            $table->string('color', 7)->default('#3490dc');
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('codigo');
            $table->index('nombre');
            $table->index('nivel');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
