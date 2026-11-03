<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('profesor_curso', function (Blueprint $table) {
            $table->string('modo_programacion', 16)->default('automatico');
            $table->json('bloques_manuales')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('profesor_curso', fn (Blueprint $table) => $table->dropColumn(['modo_programacion', 'bloques_manuales']));
    }
};
