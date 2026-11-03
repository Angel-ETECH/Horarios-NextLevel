<?php

namespace Tests\Feature;

use App\Models\{Aula, Curso, Grado, Horario, Profesor, ProfesorCurso, RecuperacionHorario};
use App\Services\{ExportService, HorarioCalendarioService};
use Carbon\CarbonImmutable;
use Database\Seeders\ConfiguracionHorarioSeeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class RecuperacionHorarioTest extends TestCase
{
    private Profesor $profesor;
    private ProfesorCurso $asignacion;
    private Horario $horario;
    private string $lunes;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();

        // Use the portable schema migrations; the history ALTER statements target MySQL.
        foreach ([
            '0001_01_01_000000_create_users_table.php',
            '2026_08_17_135415_create_profesores_table.php',
            '2026_08_17_135523_create_grados_table.php',
            '2026_08_17_135524_create_alumnos_table.php',
            '2026_08_17_135525_create_cursos_table.php',
            '2026_08_17_135526_create_aulas_table.php',
            '2026_08_17_135529_create_profesor_curso_table.php',
            '2026_08_17_135530_create_horarios_table.php',
            '2026_08_17_135527_create_disponibilidad_profesor_table.php',
            '2026_08_31_111542_add_institucion_to_profesores_table.php',
            '2026_08_31_111544_add_institucion_to_horarios_table.php',
            '2026_08_31_111545_add_institucion_to_profesor_curso_table.php',
            '2026_08_31_111543_add_institucion_to_disponibilidad_profesor_table.php',
            '2026_09_21_000003_create_configuracion_horarios_table.php',
            '2026_09_21_000004_create_bloques_horarios_table.php',
            '2026_09_22_091535_create_personal_access_tokens_table.php',
            '2026_10_05_000002_add_aula_to_profesor_curso_table.php',
            '2026_10_12_000001_add_institucion_to_aulas_table.php',
        ] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        Schema::create('historial_cambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('horario_id')->nullable()->constrained('horarios')->nullOnDelete();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('accion');
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('motivo')->nullable();
            $table->string('ip_usuario')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        $this->profesor = Profesor::create([
            'codigo' => 'TEST-1', 'nombre' => 'Profesor', 'apellido_paterno' => 'Prueba',
            'email' => 'profesor@example.test', 'dni' => '12345678', 'sexo' => 'masculino',
            'estado' => 'activo', 'institucion' => 'colegio', 'carga_horaria_maxima' => 30,
            'carga_horaria_actual' => 2,
        ]);
        $curso = Curso::create(['codigo' => 'C1', 'nombre' => 'Curso', 'nivel' => 'primaria']);
        $grado = Grado::create([
            'codigo' => 'G1', 'nivel' => 'primaria', 'grado' => '1',
            'nombre_completo' => 'Primaria 1', 'año_academico' => 2026,
        ]);
        $aula = Aula::create(['codigo' => 'A1', 'nombre' => 'Aula 101']);
        $this->asignacion = ProfesorCurso::create([
            'profesor_id' => $this->profesor->id, 'curso_id' => $curso->id,
            'grado_id' => $grado->id, 'aula_id' => $aula->id,
            'horas_asignadas' => 2, 'institucion' => 'colegio', 'activo' => true,
        ]);
        (require database_path('migrations/2026_10_21_000001_create_recuperaciones_horario_table.php'))->up();
        $this->seed(ConfiguracionHorarioSeeder::class);
        $this->profesor->update(['institucion' => 'ambos']);
        $this->lunes = CarbonImmutable::now()->next('monday')->toDateString();
        $this->horario = Horario::create([
            'profesor_id' => $this->profesor->id, 'curso_id' => $this->asignacion->curso_id,
            'grado_id' => $this->asignacion->grado_id, 'aula_id' => $this->asignacion->aula_id,
            'institucion' => 'colegio', 'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '07:45',
            'turno' => 'mañana', 'periodo_academico' => '2026', 'estado' => 'activo',
        ]);
    }


    private function payload(array $overrides = []): array
    {
        return array_replace([
            'horario_id' => $this->horario->id, 'fecha_original' => $this->lunes,
            'fecha_recuperacion' => CarbonImmutable::parse($this->lunes)->addDays(5)->toDateString(),
            'aula_id' => $this->asignacion->aula_id, 'hora_inicio' => '14:00',
            'disponibilidad_excepcional' => true, 'disponible_desde' => '14:00',
            'disponible_hasta' => '18:00', 'jornada_autorizada' => true, 'motivo' => 'Prueba de recuperacion',
        ], $overrides);
    }

    public function test_recovery_preserves_template_and_minutes_and_only_changes_one_week(): void
    {
        $before = $this->horario->refresh()->getRawOriginal();
        $payload = $this->payload(['fecha_recuperacion' => CarbonImmutable::parse($this->lunes)->addDays(6)->toDateString(), 'hora_fin' => '19:00']);
        $this->postJson('/api/recuperaciones', $payload)->assertCreated()->assertJsonPath('data.hora_fin', '14:45');
        $this->assertSame($before, $this->horario->fresh()->getRawOriginal());
        $this->assertSame(2, $this->asignacion->fresh()->horas_asignadas);
        $this->assertDatabaseCount('disponibilidad_profesor', 0);
        $week = app(HorarioCalendarioService::class)->consultar(['semana_inicio' => $this->lunes]);
        $this->assertCount(1, $week);
        $this->assertSame('domingo', $week->first()->dia_semana);
        $this->assertSame('recuperacion', $week->first()->tipo);
        $this->assertLessThan(0, $week->first()->id);
        $next = app(HorarioCalendarioService::class)->consultar(['semana_inicio' => CarbonImmutable::parse($this->lunes)->addWeek()->toDateString()]);
        $this->assertSame('lunes', $next->sole()->dia_semana);
        $this->assertDatabaseCount('historial_cambios', 1);
    }

    public function test_pending_recoveries_include_future_weeks_without_a_history_search(): void
    {
        $target = CarbonImmutable::parse($this->lunes)->addDays(19)->toDateString();
        $id = $this->postJson('/api/recuperaciones', $this->payload(['fecha_recuperacion' => $target]))->assertCreated()->json('data.id');
        $this->getJson('/api/recuperaciones?vigentes=1')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $id);
        $this->postJson('/api/recuperaciones/'.$id.'/cancelar', ['motivo' => 'Cancelar recuperacion de prueba'])->assertOk();
        $this->getJson('/api/recuperaciones?vigentes=1')->assertOk()->assertJsonCount(0, 'data');
        RecuperacionHorario::whereKey($id)->update(['estado' => 'programada', 'fecha_recuperacion' => today()->subDay()->toDateString()]);
        $this->getJson('/api/recuperaciones?vigentes=1')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/recuperaciones?vigentes=incorrecto')->assertUnprocessable();
    }

    public function test_past_recoveries_remain_audited_but_do_not_block_current_template_or_room(): void
    {
        $room = Aula::create(['codigo' => 'PAS', 'nombre' => 'Aula recuperacion pasada', 'institucion' => 'ambos']);
        $data = $this->payload(['aula_id' => $room->id]);
        $id = $this->postJson('/api/recuperaciones', $data)->assertCreated()->json('data.id');
        $snapshot = RecuperacionHorario::findOrFail($id)->original;
        $this->travelTo(CarbonImmutable::parse($data['fecha_recuperacion'])->addDay()->startOfDay());
        try {
            $this->getJson('/api/recuperaciones?vigentes=1')->assertOk()->assertJsonCount(0, 'data');
            $this->horario->update(['hora_inicio' => '07:15', 'hora_fin' => '08:00']);
            app(\App\Services\AulaService::class)->update($room->id, ['activo' => false]);
            $this->assertSame($snapshot, RecuperacionHorario::findOrFail($id)->original);
            $this->assertDatabaseCount('historial_cambios', 1);
            $this->assertSame(['revisadas' => 0, 'incidencias' => []], app(\App\Services\RecuperacionHorarioService::class)->auditar());
        } finally { $this->travelBack(); }
    }

    public function test_recovery_can_move_to_another_week_without_hiding_the_wrong_monday(): void
    {
        $target = CarbonImmutable::parse($this->lunes)->addDays(12)->toDateString();
        $this->postJson('/api/recuperaciones', $this->payload(['fecha_recuperacion' => $target]))->assertCreated();
        $service = app(HorarioCalendarioService::class);
        $this->assertCount(0, $service->consultar(['semana_inicio' => $this->lunes]));
        $this->assertCount(2, $service->consultar(['semana_inicio' => $target]));
    }

    public function test_public_consultation_and_exports_include_the_same_dated_recovery_and_room(): void
    {
        $aula = Aula::create(['codigo' => 'R2', 'nombre' => 'Aula recuperacion', 'institucion' => 'ambos']);
        $payload = $this->payload(['aula_id' => $aula->id, 'fecha_recuperacion' => CarbonImmutable::parse($this->lunes)->addDays(6)->toDateString()]);
        $this->postJson('/api/recuperaciones', $payload)->assertCreated();
        foreach (['profesor' => $this->profesor->id, 'grado' => $this->asignacion->grado_id, 'aula' => $aula->id, 'curso' => $this->asignacion->curso_id] as $tipo => $id) {
            $this->getJson('/api/publico/horario/'.$tipo.'/'.$id.'?institucion=colegio&semana_inicio='.$this->lunes)
                ->assertOk()->assertJsonCount(1, 'data.horarios.domingo')->assertJsonCount(0, 'data.horarios.lunes');
        }
        $export = app(ExportService::class)->prepareData(['semana_inicio' => $this->lunes, 'aula_id' => $aula->id]);
        $this->assertSame($aula->id, $export->sole()->aula_id);
        $sheets = (new \App\Exports\HorarioExport($export, 'Recuperacion', ['semana_inicio' => $this->lunes]))->sheets();
        $matriz = $sheets[0]->array();
        $this->assertSame('Domingo', $matriz[6][7]);
        $this->assertStringContainsString('Recuperacion', $matriz[7][7]);
        $this->assertStringContainsString('Recuperacion:', app(ExportService::class)->getHtmlForImage($export));
        $path = tempnam(sys_get_temp_dir(), 'recovery_xlsx_');
        try {
            file_put_contents($path, \Maatwebsite\Excel\Facades\Excel::raw(new \App\Exports\HorarioExport($export, 'Recuperacion', [
                'semana_inicio' => $this->lunes, 'profesor_id' => $this->profesor->id,
            ]), \Maatwebsite\Excel\Excel::XLSX));
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $this->assertStringContainsString('Recuperacion', $book->getSheet(0)->getCell('H8')->getValue());
            $this->assertStringContainsString(now()->format('d/m/Y'), $book->getSheet(0)->getCell('F1')->getValue());
            $this->assertNull($book->getSheet(0)->getFreezePane());
            $this->assertStringContainsString('Recuperacion', $book->getSheet(1)->getCell('F7')->getValue());
            $this->assertStringContainsString(CarbonImmutable::parse($payload['fecha_recuperacion'])->format('d/m/Y'), $book->getSheet(1)->getCell('B7')->getValue());
            $book->disconnectWorksheets();
        } finally { unlink($path); }
    }

    public static function invalidDestinations(): array
    {
        return [
            'unconfirmed availability' => [['disponibilidad_excepcional' => false], 'disponibilidad'],
            'missing extraordinary range' => [['disponible_hasta' => null], 'disponible_hasta'],
            'range too short' => [['disponible_hasta' => '14:30'], 'disponibilidad'],
            'wrong original weekday' => [['fecha_original' => '2026-10-27'], 'fecha_original'],
            'unapproved afternoon' => [['jornada_autorizada' => false], 'jornada_autorizada'],
            'class during primary recess' => [['hora_inicio' => '08:30', 'disponible_desde' => '07:00'], 'receso'],
            'class beyond closing time' => [['hora_inicio' => '19:30', 'disponible_hasta' => '20:00'], 'hora_inicio'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('invalidDestinations')]
    public function test_invalid_destination_is_rejected_without_writing(array $changes, string $field): void
    {
        if ($field === 'fecha_original') $changes['fecha_original'] = CarbonImmutable::parse($this->lunes)->addDay()->toDateString();
        $this->postJson('/api/recuperaciones', $this->payload($changes))->assertUnprocessable()->assertJsonValidationErrors($field);
        $this->assertDatabaseCount('recuperaciones_horario', 0);
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_options_are_only_complete_slots_and_cannot_bypass_validation(): void
    {
        $data = $this->payload(['buscar_desde' => '08:00', 'buscar_hasta' => '10:00', 'disponible_desde' => '07:00']);
        unset($data['hora_inicio'], $data['motivo']);
        $options = $this->postJson('/api/recuperaciones/opciones', $data)->assertOk()->json('data.opciones');
        $this->assertNotEmpty($options);
        foreach ($options as $option) {
            $this->assertTrue($option['hora_fin'] <= '08:30' || $option['hora_inicio'] >= '08:50');
        }
        $this->assertDatabaseCount('recuperaciones_horario', 0);
    }

    public function test_sunday_requires_explicit_institutional_approval(): void
    {
        $this->postJson('/api/recuperaciones', $this->payload([
            'fecha_recuperacion' => CarbonImmutable::parse($this->lunes)->addDays(6)->toDateString(),
            'jornada_autorizada' => false,
        ]))->assertUnprocessable()->assertJsonValidationErrors('jornada_autorizada');
    }

    public function test_duplicate_recovery_is_rejected_and_cancel_restores_original(): void
    {
        $id = $this->postJson('/api/recuperaciones', $this->payload())->assertCreated()->json('data.id');
        $this->postJson('/api/recuperaciones', $this->payload())->assertUnprocessable();
        $this->postJson('/api/recuperaciones/'.$id.'/cancelar', ['motivo' => 'Se restablece la clase original'])->assertOk();
        $this->assertSame('lunes', app(HorarioCalendarioService::class)->consultar(['semana_inicio' => $this->lunes])->sole()->dia_semana);
        $this->postJson('/api/recuperaciones', $this->payload())->assertCreated();
        $this->assertDatabaseCount('recuperaciones_horario', 1);
        $this->assertDatabaseCount('historial_cambios', 3);
    }

    public static function conflicts(): array { return [['profesor_id'], ['aula_id'], ['grado_id']]; }

    #[\PHPUnit\Framework\Attributes\DataProvider('conflicts')]
    public function test_date_specific_conflicts_are_checked_across_institutions(string $field): void
    {
        $teacher = Profesor::create(['codigo' => 'T2', 'nombre' => 'Otro', 'apellido_paterno' => 'Profesor', 'dni' => '87654321', 'email' => 'otro@example.test', 'sexo' => 'masculino', 'institucion' => 'ambos']);
        $grade = Grado::create(['codigo' => 'G2', 'nivel' => 'academia', 'grado' => '1', 'nombre_completo' => 'Academia 1', 'año_academico' => 2026]);
        $room = Aula::create(['codigo' => 'A2', 'nombre' => 'Otra aula', 'institucion' => 'ambos']);
        $params = [
            'profesor_id' => $teacher->id, 'curso_id' => $this->asignacion->curso_id, 'grado_id' => $grade->id, 'aula_id' => $room->id,
            'dia_semana' => 'sábado', 'hora_inicio' => '14:00', 'hora_fin' => '14:45', 'periodo_academico' => '2026',
            'institucion' => 'academia', 'estado' => 'activo',
        ];
        $params[$field] = $this->horario->$field;
        Horario::create($params);
        $this->postJson('/api/recuperaciones', $this->payload())->assertUnprocessable()->assertJsonValidationErrors($field);
    }

    public function test_original_template_is_protected_until_recovery_is_cancelled(): void
    {
        $this->postJson('/api/recuperaciones', $this->payload())->assertCreated();
        $this->putJson('/api/horarios/'.$this->horario->id, ['hora_inicio' => '07:45', 'hora_fin' => '08:30'])->assertUnprocessable();
        $this->deleteJson('/api/horarios/'.$this->horario->id)->assertUnprocessable();
        $this->assertDatabaseHas('horarios', ['id' => $this->horario->id]);
    }

    public function test_bad_week_is_rejected_and_writes_require_authentication(): void
    {
        $this->getJson('/api/horarios?semana_inicio=no-fecha')->assertUnprocessable();
        $this->withMiddleware();
        $this->postJson('/api/recuperaciones', $this->payload())->assertUnauthorized();
    }

    public function test_normal_availability_can_be_used_without_creating_an_exception_range(): void
    {
        \App\Models\DisponibilidadProfesor::create([
            'profesor_id' => $this->profesor->id, 'dia_semana' => 'sábado', 'institucion' => 'colegio',
            'hora_inicio' => '14:00', 'hora_fin' => '16:00', 'tipo' => 'disponible',
        ]);
        $this->postJson('/api/recuperaciones', $this->payload(['disponibilidad_excepcional' => false]))->assertCreated();
        $this->assertDatabaseCount('disponibilidad_profesor', 1);
    }

    public function test_a_second_recovery_cannot_take_the_same_room_or_teacher_slot(): void
    {
        $this->postJson('/api/recuperaciones', $this->payload())->assertCreated();
        $other = $this->horario->replicate();
        $other->hora_inicio = '07:45';
        $other->hora_fin = '08:30';
        $other->save();
        $this->postJson('/api/recuperaciones', $this->payload(['horario_id' => $other->id]))
            ->assertUnprocessable()->assertJsonValidationErrors('profesor_id');
        $this->assertDatabaseCount('recuperaciones_horario', 1);
    }

    public function test_cancelling_cannot_restore_an_original_slot_taken_by_another_recovery(): void
    {
        $first = $this->postJson('/api/recuperaciones', $this->payload())->assertCreated()->json('data.id');
        $other = $this->horario->replicate();
        $other->hora_inicio = '09:35';
        $other->hora_fin = '10:20';
        $other->save();
        $this->postJson('/api/recuperaciones', $this->payload([
            'horario_id' => $other->id, 'fecha_recuperacion' => $this->lunes,
            'hora_inicio' => '07:00', 'disponible_desde' => '07:00',
        ]))->assertCreated();
        $this->postJson('/api/recuperaciones/'.$first.'/cancelar', ['motivo' => 'Cancelar primera recuperacion'])
            ->assertUnprocessable()->assertJsonValidationErrors('profesor_id');
        $this->assertDatabaseHas('recuperaciones_horario', ['id' => $first, 'estado' => 'programada']);
    }

    public function test_incompatible_room_and_inactive_teacher_are_rejected(): void
    {
        $room = Aula::create(['codigo' => 'ACA', 'nombre' => 'Solo Academia', 'institucion' => 'academia']);
        $this->postJson('/api/recuperaciones', $this->payload(['aula_id' => $room->id]))->assertUnprocessable()->assertJsonValidationErrors('aula_id');
        $this->profesor->update(['estado' => 'inactivo']);
        $this->postJson('/api/recuperaciones', $this->payload())->assertUnprocessable()->assertJsonValidationErrors('horario_id');
        $this->assertDatabaseCount('recuperaciones_horario', 0);
    }

    public function test_related_deactivation_and_regeneration_roll_back_with_clear_validation(): void
    {
        $this->postJson('/api/recuperaciones', $this->payload())->assertCreated();
        $this->putJson('/api/profesores/'.$this->profesor->id, array_merge($this->profesor->toArray(), ['estado' => 'inactivo']))
            ->assertUnprocessable()->assertJsonValidationErrors('recuperacion');
        $this->assertSame('activo', $this->profesor->fresh()->estado);
        $this->assertTrue($this->asignacion->fresh()->activo);
        $this->deleteJson('/api/asignaciones/'.$this->asignacion->id)->assertUnprocessable()->assertJsonValidationErrors('recuperacion');
        $this->deleteJson('/api/asignaciones/'.$this->asignacion->id.'/permanente')->assertUnprocessable()->assertJsonValidationErrors('recuperacion');
        $this->postJson('/api/horarios/generar', ['periodo_academico' => '2026', 'profesor_id' => $this->profesor->id, 'limpiar_anteriores' => true])
            ->assertUnprocessable()->assertJsonValidationErrors('recuperacion');
        $this->deleteJson('/api/horarios/generar/limpiar', ['profesor_id' => $this->profesor->id, 'confirmar' => true])
            ->assertUnprocessable()->assertJsonValidationErrors('recuperacion');
        $this->postJson('/api/horarios/generar/profesor/'.$this->profesor->id, ['periodo_academico' => '2026'])->assertOk();
        $this->assertDatabaseCount('horarios', 1);
        $this->assertDatabaseCount('historial_cambios', 1);
        $this->assertTrue($this->asignacion->fresh()->activo);
    }

    public function test_recovery_room_is_protected_even_without_regular_classes_in_it(): void
    {
        $room = Aula::create(['codigo' => 'REC', 'nombre' => 'Solo recuperaciones', 'institucion' => 'ambos', 'capacidad' => 40]);
        $this->horario->grado->update(['numero_estudiantes' => 35]);
        $this->postJson('/api/recuperaciones', $this->payload(['aula_id' => $room->id]))->assertCreated();
        $service = app(\App\Services\AulaService::class);
        foreach ([['activo' => false], ['institucion' => 'academia'], ['capacidad' => 20]] as $change) {
            try { $service->update($room->id, $change); $this->fail('Se permitio invalidar un aula reservada.'); }
            catch (\Illuminate\Validation\ValidationException $e) { $this->assertArrayHasKey('recuperacion', $e->errors()); }
        }
        $this->deleteJson('/api/aulas/'.$room->id)->assertUnprocessable()->assertJsonValidationErrors('recuperacion');
        $this->assertTrue($room->fresh()->activo);
        $this->assertSame(40, $room->fresh()->capacidad);
        $this->assertSame('ambos', $room->fresh()->institucion);
    }

    public function test_audit_accepts_database_time_seconds_and_detects_changed_duration(): void
    {
        $id = $this->postJson('/api/recuperaciones', $this->payload())->assertCreated()->json('data.id');
        RecuperacionHorario::whereKey($id)->update(['disponible_desde' => '14:00:00', 'disponible_hasta' => '18:00:00']);
        $service = app(\App\Services\RecuperacionHorarioService::class);
        $this->assertSame(['revisadas' => 1, 'incidencias' => []], $service->auditar());
        RecuperacionHorario::whereKey($id)->update(['hora_fin' => '15:00:00']);
        $result = $service->auditar();
        $this->assertCount(1, $result['incidencias']);
        $this->assertStringContainsString('minutos', $result['incidencias'][0]['detalle']);
    }

    public function test_a_regular_schedule_cannot_be_created_over_a_dated_recovery(): void
    {
        $this->postJson('/api/recuperaciones', $this->payload())->assertCreated();
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(\App\Services\RecuperacionHorarioService::class)->validarNuevaPlantilla([
            'dia_semana' => 'sábado', 'hora_inicio' => '14:30', 'hora_fin' => '15:15', 'periodo_academico' => '2026',
            'profesor_id' => $this->profesor->id, 'grado_id' => $this->asignacion->grado_id, 'aula_id' => $this->asignacion->aula_id,
        ]);
    }
}
