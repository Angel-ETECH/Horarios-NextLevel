<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->json('niveles')->nullable();
            $table->boolean('usar_duracion_curso')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('cursos', fn (Blueprint $table) => $table->dropColumn(['niveles', 'usar_duracion_curso']));
    }
};
