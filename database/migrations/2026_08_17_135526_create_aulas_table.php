<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 50);
            $table->integer('capacidad')->default(30);
            $table->enum('tipo', ['aula_normal', 'taller', 'auditorio', 'virtual'])->default('aula_normal');
            $table->enum('nivel', ['primaria', 'secundaria', 'academia', 'todos'])->default('todos');
            $table->text('equipamiento')->nullable();
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('codigo');
            $table->index('tipo');
            $table->index('nivel');
            $table->index('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};
