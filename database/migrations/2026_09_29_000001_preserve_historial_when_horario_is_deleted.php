<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE historial_cambios DROP FOREIGN KEY historial_cambios_horario_id_foreign');
        DB::statement('ALTER TABLE historial_cambios MODIFY horario_id BIGINT UNSIGNED NULL');
        DB::statement(
            'ALTER TABLE historial_cambios
             ADD CONSTRAINT historial_cambios_horario_id_foreign
             FOREIGN KEY (horario_id) REFERENCES horarios(id)
             ON DELETE SET NULL'
        );
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE historial_cambios DROP FOREIGN KEY historial_cambios_horario_id_foreign');
        DB::statement(
            'DELETE FROM historial_cambios
             WHERE horario_id IS NULL'
        );
        DB::statement('ALTER TABLE historial_cambios MODIFY horario_id BIGINT UNSIGNED NOT NULL');
        DB::statement(
            'ALTER TABLE historial_cambios
             ADD CONSTRAINT historial_cambios_horario_id_foreign
             FOREIGN KEY (horario_id) REFERENCES horarios(id)
             ON DELETE CASCADE'
        );
    }
};
