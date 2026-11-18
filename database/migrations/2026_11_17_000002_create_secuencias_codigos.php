<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('secuencias_codigos', function (Blueprint $table) {
            $table->string('catalogo')->primary();
            $table->unsignedBigInteger('ultimo_numero')->default(0);
        });
        foreach (\App\Services\CodigoSecuencialService::CATALOGOS as $tabla => $prefijo) {
            $maximo = 0;
            foreach (DB::table($tabla)->select('codigo')->cursor() as $fila) {
                if (preg_match('/^'.preg_quote($prefijo, '/').'-(\d+)$/', $fila->codigo, $partes)) {
                    $maximo = max($maximo, (int) $partes[1]);
                }
            }
            DB::table('secuencias_codigos')->insert(['catalogo' => $tabla, 'ultimo_numero' => $maximo]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('secuencias_codigos');
    }
};
