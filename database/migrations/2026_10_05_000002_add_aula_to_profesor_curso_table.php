<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('profesor_curso', 'aula_id')) {
            Schema::table('profesor_curso', function (Blueprint $table) {
                $table->foreignId('aula_id')
                    ->nullable()
                    ->after('grado_id')
                    ->constrained('aulas')
                    ->nullOnDelete()
                    ->cascadeOnUpdate();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('profesor_curso', 'aula_id')) {
            Schema::table('profesor_curso', function (Blueprint $table) {
                $table->dropConstrainedForeignId('aula_id');
            });
        }
    }
};
