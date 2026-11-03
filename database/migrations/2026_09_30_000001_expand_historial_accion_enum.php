<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE historial_cambios
             MODIFY accion ENUM('crear','actualizar','eliminar','restaurar','revertir') NOT NULL"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE historial_cambios
             MODIFY accion ENUM('crear','actualizar','eliminar','restaurar') NOT NULL"
        );
    }
};
