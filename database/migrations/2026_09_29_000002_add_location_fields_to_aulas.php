<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('aulas', function (Blueprint $table) {
            if (!Schema::hasColumn('aulas', 'edificio')) {
                $table->string('edificio', 50)->nullable()->after('nivel');
            }

            if (!Schema::hasColumn('aulas', 'piso')) {
                $table->string('piso', 10)->nullable()->after('edificio');
            }
        });
    }

    public function down(): void
    {
        Schema::table('aulas', function (Blueprint $table) {
            if (Schema::hasColumn('aulas', 'piso')) {
                $table->dropColumn('piso');
            }

            if (Schema::hasColumn('aulas', 'edificio')) {
                $table->dropColumn('edificio');
            }
        });
    }
};
