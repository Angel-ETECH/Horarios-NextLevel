<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Las aulas son espacios físicos compartidos entre colegio y academia
     * (ver AulaService::getAulasDisponibles), por eso el valor por defecto
     * y el relleno inicial es 'ambos'; solo las aulas de nivel academia
     * quedan marcadas como exclusivas de academia.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('aulas', 'institucion')) {
            Schema::table('aulas', function (Blueprint $table) {
                $table->enum('institucion', ['colegio', 'academia', 'ambos'])
                    ->default('ambos')
                    ->after('nivel');
            });
        }

        DB::table('aulas')
            ->where('nivel', 'academia')
            ->update(['institucion' => 'academia']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('aulas', 'institucion')) {
            Schema::table('aulas', function (Blueprint $table) {
                $table->dropColumn('institucion');
            });
        }
    }
};
