<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_cambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_id')
                  ->constrained('horarios')
                  ->onDelete('cascade');
            $table->foreignId('usuario_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');

            // Datos del cambio
            $table->enum('accion', ['crear', 'actualizar', 'eliminar', 'restaurar']);
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('motivo', 255)->nullable();
            $table->string('ip_usuario', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index('horario_id');
            $table->index('usuario_id');
            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_cambios');
    }
};
