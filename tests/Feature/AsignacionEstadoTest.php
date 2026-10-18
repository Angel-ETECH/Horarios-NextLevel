<?php

namespace Tests\Feature;

use App\Models\Aula;
use App\Models\Curso;
use App\Models\Grado;
use App\Models\Horario;
use App\Models\Profesor;
use App\Models\ProfesorCurso;
use App\Models\ConfiguracionHorario;
use App\Models\DisponibilidadProfesor;
use App\Services\HorarioService;
use App\Services\ExportService;
use App\Services\DisponibilidadService;
use App\Services\HistorialService;
use App\Models\HistorialCambio;
use App\Models\User;
use App\Exports\HorarioExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Services\HorarioGeneratorService;
use App\Services\HorarioRecesoService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class AsignacionEstadoTest extends TestCase
{
    private Profesor $profesor;
    private ProfesorCurso $asignacion;

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
        $recesos = Mockery::mock(HorarioRecesoService::class);
        $recesos->shouldReceive('validarConfiguracionLista')->andReturnNull();
        $this->app->instance(HorarioRecesoService::class, $recesos);
    }

    public function test_deactivating_professor_updates_assignments_schedules_and_history(): void
    {
        Horario::create([
            'profesor_id' => $this->profesor->id, 'curso_id' => $this->asignacion->curso_id,
            'grado_id' => $this->asignacion->grado_id, 'aula_id' => $this->asignacion->aula_id,
            'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '07:45',
            'periodo_academico' => '2026', 'institucion' => 'colegio',
        ]);
        $this->putJson('/api/profesores/'.$this->profesor->id, array_merge(
            $this->profesor->toArray(), ['estado' => 'inactivo']
        ))->assertOk()->assertJsonPath('data.estado', 'inactivo');

        $this->assertFalse($this->asignacion->fresh()->activo);
        $this->assertSame(0, $this->profesor->fresh()->carga_horaria_actual);
        $this->assertSame(0, Horario::count());
        $this->assertDatabaseCount('historial_cambios', 3);
    }

    public function test_assignment_can_be_deactivated_and_reactivated(): void
    {
        $this->mockGenerator(['success' => true, 'horarios_creados' => 2]);
        $this->postJson('/api/asignaciones/'.$this->asignacion->id.'/desactivar')->assertOk();
        $this->assertFalse($this->asignacion->fresh()->activo);
        $this->postJson('/api/asignaciones/'.$this->asignacion->id.'/activar')
            ->assertOk()->assertJsonPath('data.activo', true);
        $this->assertSame(2, $this->profesor->fresh()->carga_horaria_actual);
    }

    public function test_inactive_professor_cannot_be_reactivated_through_assignment(): void
    {
        $this->profesor->update(['estado' => 'inactivo']);
        $this->asignacion->update(['activo' => false]);
        $this->postJson('/api/asignaciones/'.$this->asignacion->id.'/activar')
            ->assertStatus(422)->assertJsonValidationErrors('profesor_id');
        $this->putJson('/api/asignaciones/'.$this->asignacion->id, ['activo' => true])
            ->assertStatus(422)->assertJsonValidationErrors('profesor_id');
        $this->assertFalse($this->asignacion->fresh()->activo);
    }

    public function test_missing_aula_returns_actionable_validation(): void
    {
        $this->asignacion->update(['activo' => false, 'aula_id' => null]);
        $this->postJson('/api/asignaciones/'.$this->asignacion->id.'/activar')
            ->assertStatus(422)->assertJsonValidationErrors('aula_id');
        $this->assertFalse($this->asignacion->fresh()->activo);
    }

    public function test_failed_generation_rolls_back_activation_and_history(): void
    {
        $this->asignacion->update(['activo' => false]);
        $this->profesor->update(['carga_horaria_actual' => 0]);
        $this->mockGenerator(['success' => false, 'error' => 'Test generation failure']);
        $this->postJson('/api/asignaciones/'.$this->asignacion->id.'/activar')
            ->assertStatus(422)->assertJsonValidationErrors('horarios');
        $this->assertFalse($this->asignacion->fresh()->activo);
        $this->assertSame(0, $this->profesor->fresh()->carga_horaria_actual);
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_generator_excludes_inactive_professors(): void
    {
        $this->profesor->update(['estado' => 'inactivo']);
        $result = $this->app->make(HorarioGeneratorService::class)->generarHorarios();
        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['estadisticas']['total_asignaciones']);
    }

    public function test_creation_rolls_back_when_generation_fails(): void
    {
        $this->asignacion->delete();
        $this->mockGenerator(['success' => false]);
        $this->postJson('/api/asignaciones', [
            'profesor_id' => $this->profesor->id, 'curso_id' => $this->asignacion->curso_id,
            'grado_id' => $this->asignacion->grado_id, 'aula_id' => $this->asignacion->aula_id,
            'institucion' => 'colegio', 'horas_asignadas' => 2, 'rol' => 'titular',
        ])->assertStatus(422)->assertJsonValidationErrors('horarios');
        $this->assertDatabaseCount('profesor_curso', 0);
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_edit_rolls_back_when_generation_fails(): void
    {
        $this->mockGenerator(['success' => false]);
        $this->putJson('/api/asignaciones/'.$this->asignacion->id, ['horas_asignadas' => 3])
            ->assertStatus(422)->assertJsonValidationErrors('horarios');
        $this->assertSame(2, $this->asignacion->fresh()->horas_asignadas);
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_rejected_deletions_do_not_leave_open_transactions(): void
    {
        $this->createHorario();
        foreach (['profesores' => $this->profesor->id,
            'cursos' => $this->asignacion->curso_id, 'grados' => $this->asignacion->grado_id] as $ruta => $id) {
            $this->deleteJson('/api/'.$ruta.'/'.$id)->assertStatus(422);
            $this->assertSame(0, DB::transactionLevel(), $ruta);
        }
    }

    public function test_schedule_edit_uses_time_values_and_assigned_aula(): void
    {
        $this->availability();
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $horario = $this->createHorario();
        $edited = $this->app->make(HorarioService::class)->update($horario->id, ['observacion' => 'Editado']);
        $this->assertSame('Editado', $edited->observacion);
        $otherAula = Aula::create(['codigo' => 'A2', 'nombre' => 'Aula 102']);
        try {
            $this->app->make(HorarioService::class)->update($horario->id, ['aula_id' => $otherAula->id]);
            $this->fail('An aula different from the assignment must be rejected.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('aula_id', $exception->errors());
        }
    }

    public function test_real_generator_fills_until_recess_and_does_not_duplicate(): void
    {
        $this->availability();
        $this->configuration();
        $this->asignacion->update(['horas_asignadas' => 4]);
        $generator = $this->app->make(HorarioGeneratorService::class);
        $result = $generator->generarHorarios(['profesor_id' => $this->profesor->id]);
        $this->assertTrue($result['success'], $result['error'] ?? '');
        $this->assertSame(4, $result['horarios_creados']);
        $this->assertSame(['07:45', '08:30', '09:00', '10:15'],
            Horario::orderBy('hora_inicio')->get()->map(fn ($h) => $h->hora_fin->format('H:i'))->all());
        $again = $generator->generarHorarios(['profesor_id' => $this->profesor->id]);
        $this->assertTrue($again['success']);
        $this->assertSame(0, $again['horarios_creados']);
        $this->assertDatabaseCount('horarios', 4);
    }

    public function test_availability_can_be_deleted_and_saved_again(): void
    {
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $service = $this->app->make(DisponibilidadService::class);
        $data = ['profesor_id' => $this->profesor->id, 'dia_semana' => 'lunes',
            'hora_inicio' => '07:00', 'hora_fin' => '10:00', 'institucion' => 'colegio', 'tipo' => 'disponible'];
        $first = $service->create($data);
        $service->delete($first->id);
        $restored = $service->create($data);
        $this->assertSame($first->id, $restored->id);
        $this->assertFalse($restored->trashed());
        $this->assertDatabaseCount('historial_cambios', 3);
    }

    public function test_all_pages_render_and_protected_api_requires_login(): void
    {
        foreach (['/', '/profesores', '/cursos', '/grados', '/aulas', '/disponibilidades',
            '/asignaciones', '/horarios', '/historial', '/consulta-horarios', '/login',
            '/registro', '/forgot-password', '/reset-password'] as $ruta) {
            $this->get($ruta)->assertOk();
        }
        $this->withMiddleware();
        $this->getJson('/api/asignaciones')->assertUnauthorized();
    }

    public function test_schedule_list_and_export_include_actual_classes(): void
    {
        $this->createHorario();
        $this->getJson('/api/horarios')->assertOk()->assertJsonPath('meta.total', 1);
        $this->assertCount(1, $this->app->make(ExportService::class)->prepareData([
            'profesor_id' => $this->profesor->id,
        ]));
        $this->getJson('/api/publico/horario/profesor/'.$this->profesor->id)->assertOk();
    }

    private function createHorario(): Horario
    {
        return Horario::create([
            'profesor_id' => $this->profesor->id, 'curso_id' => $this->asignacion->curso_id,
            'grado_id' => $this->asignacion->grado_id, 'aula_id' => $this->asignacion->aula_id,
            'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '07:45',
            'turno' => 'mañana', 'periodo_academico' => '2026-2027', 'institucion' => 'colegio',
        ]);
    }

    public function test_generation_respects_requested_academic_period(): void
    {
        $this->availability();
        $this->configuration();
        $this->createHorario();
        $result = $this->app->make(HorarioGeneratorService::class)->generarHorarios([
            'profesor_id' => $this->profesor->id, 'periodo_academico' => '2027-2028',
        ]);
        $this->assertTrue($result['success'], $result['error'] ?? '');
        $this->assertSame(2, $result['horarios_creados']);
        $this->assertSame(2, Horario::where('periodo_academico', '2027-2028')->count());
    }

    public function test_generator_uses_only_latest_config_for_each_turn(): void
    {
        $this->configuration()->update(['año_academico' => 2025]);
        $latest = $this->configuration();
        $latest->update(['año_academico' => 2027]);
        $latest->bloques()->where('orden', 4)->update(['hora_inicio' => '09:30', 'hora_fin' => '10:00']);
        DisponibilidadProfesor::create([
            'profesor_id' => $this->profesor->id, 'dia_semana' => 'lunes',
            'hora_inicio' => '09:30', 'hora_fin' => '10:15', 'institucion' => 'colegio', 'tipo' => 'disponible',
        ]);
        $result = $this->app->make(HorarioGeneratorService::class)->generarHorarios();
        $this->assertTrue($result['success']);
        $this->assertSame(0, $result['horarios_creados']);
    }

    public function test_teacher_in_both_institutions_can_generate_without_institution_filter(): void
    {
        $this->profesor->update(['institucion' => 'ambos']);
        $this->availability();
        $this->configuration();
        $this->postJson('/api/horarios/generar/profesor/'.$this->profesor->id)
            ->assertOk()->assertJsonPath('success', true);
        $this->assertDatabaseCount('horarios', 2);
    }

    public function test_excel_contains_classes_without_repeated_teacher_or_frozen_panes(): void
    {
        $this->createHorario();
        $data = $this->app->make(ExportService::class)->prepareData([]);
        $path = tempnam(sys_get_temp_dir(), 'horario_test_');
        try {
            file_put_contents($path, Excel::raw(new HorarioExport($data, 'Horario prueba', [
                'profesor_id' => $this->profesor->id,
            ]), \Maatwebsite\Excel\Excel::XLSX));
            $workbook = IOFactory::load($path);
            $sheet = $workbook->getSheetByName('Horario');
            $this->assertStringContainsString('Curso', $sheet->getCell('B8')->getValue());
            $this->assertStringNotContainsString('Profesor Prueba', $sheet->getCell('B8')->getValue());
            $this->assertNull($sheet->getFreezePane());
            $this->assertSame('Detalle', $workbook->getSheet(1)->getTitle());
            $workbook->disconnectWorksheets();
        } finally {
            unlink($path);
        }
    }

    public function test_pdf_and_image_exports_render_and_pdf_is_on_public_disk(): void
    {
        $this->createHorario();
        $data = $this->app->make(ExportService::class)->prepareData([]);
        $service = $this->app->make(ExportService::class);
        $html = $service->getHtmlForImage($data, 'Horario prueba', ['profesor_id' => $this->profesor->id]);
        $this->assertStringContainsString('Curso', $html);
        $this->assertStringContainsString('Aula', $html);
        $response = $service->downloadPdf($data, 'Horario prueba');
        $this->assertStringStartsWith('%PDF', $response->getContent());
        Storage::fake('public');
        $url = $service->toPdf($data, 'Horario prueba');
        $this->assertStringContainsString('/storage/exports/', $url);
        $this->assertCount(1, Storage::disk('public')->files('exports'));
    }

    public function test_login_token_allows_api_access_and_logout_revokes_it(): void
    {
        $this->withMiddleware();
        User::create(['name' => 'Admin prueba', 'email' => 'admin@example.test', 'password' => 'Password123!']);
        $login = $this->postJson('/api/login', ['email' => 'admin@example.test', 'password' => 'Password123!']);
        $login->assertOk();
        $token = $login->json('data.token');
        $this->assertNotEmpty($token);
        $this->withToken($token)->getJson('/api/asignaciones')->assertOk();
        $this->withToken($token)->postJson('/api/auth/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_history_restores_permanently_deleted_block_from_snapshot(): void
    {
        $this->availability();
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $horario = $this->createHorario();
        $this->deleteJson('/api/horarios/'.$horario->id)->assertOk();
        $registro = HistorialCambio::where('accion', 'eliminar')->firstOrFail();
        $restored = $this->app->make(HistorialService::class)->revertirCambio($registro->id);
        $this->assertSame('07:00', $restored->hora_inicio->format('H:i'));
        $this->assertDatabaseCount('horarios', 1);
    }

    public function test_history_reversion_rejects_current_class_conflict(): void
    {
        $this->availability();
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $horario = $this->createHorario();
        $this->putJson('/api/horarios/'.$horario->id, ['hora_inicio' => '08:00', 'hora_fin' => '08:45'])->assertOk();
        $registro = HistorialCambio::where('accion', 'actualizar')->firstOrFail();
        $conflicto = $this->createHorario();
        $conflicto->update(['hora_inicio' => '07:15', 'hora_fin' => '08:00']);
        $this->postJson('/api/historial/revertir/'.$registro->id)->assertStatus(422);
        $this->assertSame('08:00', $horario->fresh()->hora_inicio->format('H:i'));
    }

    public function test_recess_update_rolls_back_when_reprogramming_is_incomplete(): void
    {
        $config = $this->configuration();
        $horario = $this->createHorario();
        $generator = Mockery::mock(HorarioGeneratorService::class);
        $generator->shouldReceive('generarHorarios')->once()->andReturn([
            'success' => true, 'horarios_creados' => 0,
            'asignaciones_incompletas' => [['horas_pendientes' => 2, 'motivo' => 'Sin disponibilidad']],
        ]);
        $this->app->instance(HorarioGeneratorService::class, $generator);
        $this->putJson('/api/configuraciones-horario/'.$config->id, ['nombre' => 'Cambio prueba'])
            ->assertStatus(422)->assertJsonValidationErrors('reprogramacion_horarios');
        $this->assertSame('Configuracion prueba', $config->fresh()->nombre);
        $this->assertNotNull($horario->fresh());
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_shared_aula_cannot_be_double_booked_by_other_teacher(): void
    {
        $this->availability();
        $this->configuration();
        $first = $this->app->make(HorarioGeneratorService::class)->generarHorarios();
        $this->assertTrue($first['success']);
        $profesor = Profesor::create([
            'codigo' => 'TEST-2', 'nombre' => 'Otro', 'apellido_paterno' => 'Profesor',
            'email' => 'otro@example.test', 'dni' => '87654321', 'estado' => 'activo',
            'institucion' => 'colegio', 'carga_horaria_maxima' => 30,
        ]);
        $grado = Grado::create([
            'codigo' => 'G2', 'nivel' => 'primaria', 'grado' => '2',
            'nombre_completo' => 'Primaria 2', 'año_academico' => 2026,
        ]);
        ProfesorCurso::create([
            'profesor_id' => $profesor->id, 'curso_id' => $this->asignacion->curso_id,
            'grado_id' => $grado->id, 'aula_id' => $this->asignacion->aula_id,
            'horas_asignadas' => 2, 'institucion' => 'colegio', 'activo' => true,
        ]);
        DisponibilidadProfesor::create([
            'profesor_id' => $profesor->id, 'dia_semana' => 'lunes', 'tipo' => 'disponible',
            'hora_inicio' => '07:00', 'hora_fin' => '08:30', 'institucion' => 'colegio',
        ]);
        $second = $this->app->make(HorarioGeneratorService::class)->generarHorarios(['profesor_id' => $profesor->id]);
        $this->assertTrue($second['success']);
        $this->assertSame(0, $second['horarios_creados']);
        $this->assertDatabaseCount('horarios', 2);
    }

    public function test_availability_replacement_preserves_original_data_when_a_range_fails(): void
    {
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $this->availability();
        $this->putJson('/api/disponibilidades/profesor/'.$this->profesor->id.'/institucion/colegio', [
            'franjas' => [
                ['dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00'],
                ['dia_semana' => 'lunes', 'hora_inicio' => '07:30', 'hora_fin' => '08:30'],
            ],
        ])->assertStatus(422);
        $this->assertSame('13:00', DisponibilidadProfesor::firstOrFail()->hora_fin->format('H:i'));
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_availability_replacement_keeps_classes_inside_availability(): void
    {
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $this->availability();
        $this->createHorario();
        $this->putJson('/api/disponibilidades/profesor/'.$this->profesor->id.'/institucion/colegio', [
            'franjas' => [['dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00']],
        ])->assertStatus(422);
        $this->assertSame('lunes', DisponibilidadProfesor::firstOrFail()->dia_semana);
    }

    public function test_adjacent_availability_ranges_cover_a_class_crossing_the_hour(): void
    {
        foreach ([['07:00', '08:00'], ['08:00', '09:00']] as [$inicio, $fin]) {
            DisponibilidadProfesor::create([
                'profesor_id' => $this->profesor->id, 'dia_semana' => 'lunes',
                'hora_inicio' => $inicio, 'hora_fin' => $fin, 'institucion' => 'colegio', 'tipo' => 'disponible',
            ]);
        }
        $this->configuration();
        $this->assertTrue($this->app->make(DisponibilidadService::class)->validarRangoEnDisponibilidad(
            $this->profesor->id, 'lunes', '07:45', '08:30', 'colegio'
        ));
        $result = $this->app->make(HorarioGeneratorService::class)->generarHorarios();
        $this->assertTrue($result['success']);
        $this->assertSame(2, $result['horarios_creados']);
    }

    public function test_deleting_availability_cannot_leave_a_scheduled_class_uncovered(): void
    {
        $this->availability();
        $this->createHorario();
        try {
            $this->app->make(DisponibilidadService::class)->deleteByInstitucion($this->profesor->id, 'colegio');
            $this->fail('Availability backing a scheduled class must be retained.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('disponibilidad', $exception->errors());
        }
        $this->assertSame(1, DisponibilidadProfesor::count());
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_course_with_active_assignments_cannot_change_level_or_be_disabled(): void
    {
        $curso = Curso::findOrFail($this->asignacion->curso_id);
        $this->putJson('/api/cursos/'.$curso->id, array_merge($curso->toArray(), ['activo' => false]))
            ->assertStatus(422);
        $this->assertTrue($curso->fresh()->activo);
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_changing_recesses_regenerates_classes_with_a_valid_continuous_configuration(): void
    {
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $this->availability();
        $config = $this->configuration();
        $this->asignacion->update(['horas_asignadas' => 4]);
        $this->app->make(HorarioGeneratorService::class)->generarHorarios();
        $bloques = $config->bloques->map(fn ($bloque) => [
            'orden' => $bloque->orden,
            'hora_inicio' => $bloque->orden === 4 ? '09:15' : $bloque->hora_inicio->format('H:i'),
            'hora_fin' => $bloque->orden === 3 ? '09:15' : $bloque->hora_fin->format('H:i'),
            'tipo' => $bloque->tipo,
        ])->all();
        $this->putJson('/api/configuraciones-horario/'.$config->id, ['bloques' => $bloques])
            ->assertOk()->assertJsonPath('reprogramacion_horarios.horarios_creados', 4);
        $this->assertSame('09:15', Horario::orderBy('hora_inicio')->get()[2]->hora_fin->format('H:i'));
        $this->assertSame(4, Horario::count());
    }

    public function test_recess_configuration_cannot_be_removed_while_in_use(): void
    {
        $config = $this->configuration();
        $this->deleteJson('/api/configuraciones-horario/'.$config->id)
            ->assertStatus(422)->assertJsonValidationErrors('configuracion');
        $this->assertNotNull($config->fresh());
    }

    public function test_class_in_another_period_does_not_block_a_valid_schedule(): void
    {
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $this->availability();
        $this->createHorario();
        $nuevo = $this->app->make(HorarioService::class)->create([
            'profesor_id' => $this->profesor->id, 'curso_id' => $this->asignacion->curso_id,
            'grado_id' => $this->asignacion->grado_id, 'aula_id' => $this->asignacion->aula_id,
            'dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '07:45',
            'turno' => 'mañana', 'periodo_academico' => '2027-2028', 'institucion' => 'colegio',
        ]);
        $this->assertSame('2027-2028', $nuevo->periodo_academico);
        $this->assertDatabaseCount('horarios', 2);
    }

    public function test_classroom_repair_is_previewed_then_applied_with_audit_and_no_time_changes(): void
    {
        $horario = $this->createHorario();
        $this->asignacion->update(['aula_id' => null]);
        $this->artisan('horarios:completar-aulas')->assertSuccessful();
        $this->assertNull($this->asignacion->fresh()->aula_id);
        $this->assertDatabaseCount('historial_cambios', 0);

        $backup = tempnam(sys_get_temp_dir(), 'test_respaldo_');
        file_put_contents($backup, '-- Respaldo aislado de prueba');
        try {
            $this->artisan('horarios:completar-aulas', ['--aplicar' => true, '--respaldo' => $backup])->assertSuccessful();
            $this->assertSame($horario->aula_id, $this->asignacion->fresh()->aula_id);
            $this->assertSame('07:00', $horario->fresh()->hora_inicio->format('H:i'));
            $this->assertSame('07:45', $horario->fresh()->hora_fin->format('H:i'));
            $this->assertDatabaseCount('historial_cambios', 1);
            $this->artisan('horarios:completar-aulas', ['--aplicar' => true, '--respaldo' => $backup])->assertSuccessful();
            $this->assertDatabaseCount('historial_cambios', 1);
        } finally {
            unlink($backup);
        }
    }

    public function test_classroom_repair_requires_backup_and_rejects_unknown_assignments_without_history(): void
    {
        $this->asignacion->update(['aula_id' => null]);
        $this->artisan('horarios:completar-aulas', ['--aplicar' => true])->assertFailed();
        $this->artisan('horarios:completar-aulas')->assertFailed();
        $this->assertNull($this->asignacion->fresh()->aula_id);
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_merged_history_can_revert_catalog_names_but_not_incompatible_course_levels(): void
    {
        $curso = Curso::findOrFail($this->asignacion->curso_id);
        $antes = $curso->toArray();
        $curso->update(['nombre' => 'Nombre nuevo']);
        $registro = app(HistorialService::class)->registrarEvento('cursos', 'actualizar', $antes, $curso->toArray());
        $this->postJson('/api/historial/revertir/'.$registro->id)->assertOk();
        $this->assertSame($antes['nombre'], $curso->fresh()->nombre);

        $registro = app(HistorialService::class)->registrarEvento('cursos', 'actualizar',
            array_replace($antes, ['nivel' => 'secundaria']), $curso->toArray());
        $this->postJson('/api/historial/revertir/'.$registro->id)->assertStatus(422);
        $this->assertSame('primaria', $curso->fresh()->nivel);
    }

    public function test_merged_history_deactivates_professor_and_assignment_together(): void
    {
        $this->createHorario();
        $antes = array_replace($this->profesor->toArray(), ['estado' => 'inactivo']);
        $registro = app(HistorialService::class)->registrarEvento('profesores', 'actualizar', $antes, $this->profesor->toArray());
        $this->postJson('/api/historial/revertir/'.$registro->id)->assertOk();
        $this->assertSame('inactivo', $this->profesor->fresh()->estado);
        $this->assertFalse($this->asignacion->fresh()->activo);
        $this->assertSame(0, Horario::count());
    }

    public function test_merged_history_cannot_activate_assignment_for_inactive_teacher(): void
    {
        $antes = $this->asignacion->toArray();
        $this->asignacion->update(['activo' => false]);
        $this->profesor->update(['estado' => 'inactivo']);
        $registro = app(HistorialService::class)->registrarEvento('asignaciones', 'eliminar', $antes, $this->asignacion->toArray());
        $this->postJson('/api/historial/revertir/'.$registro->id)->assertStatus(422);
        $this->assertFalse($this->asignacion->fresh()->activo);
        $this->assertSame(0, Horario::count());
    }

    public function test_merged_history_cannot_deactivate_an_occupied_classroom(): void
    {
        $this->createHorario();
        $aula = Aula::findOrFail($this->asignacion->aula_id);
        $registro = app(HistorialService::class)->registrarEvento('aulas', 'actualizar',
            array_replace($aula->toArray(), ['activo' => false]), $aula->toArray());
        $this->postJson('/api/historial/revertir/'.$registro->id)->assertStatus(422);
        $this->assertTrue($aula->fresh()->activo);
        $this->assertSame(1, Horario::count());
    }

    public function test_merged_history_does_not_restore_availability_during_recess(): void
    {
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $this->configuration();
        $disponibilidad = DisponibilidadProfesor::create([
            'profesor_id' => $this->profesor->id, 'dia_semana' => 'lunes',
            'hora_inicio' => '09:00', 'hora_fin' => '09:30', 'institucion' => 'colegio', 'tipo' => 'disponible',
        ]);
        $registro = app(HistorialService::class)->registrarEvento('disponibilidad', 'eliminar', $disponibilidad->toArray());
        $disponibilidad->delete();
        $this->postJson('/api/historial/revertir/'.$registro->id)->assertStatus(422);
        $this->assertTrue($disponibilidad->fresh()->trashed());
    }

    public function test_merged_history_exposes_reversion_for_physically_deleted_schedules(): void
    {
        $this->app->instance(HorarioRecesoService::class, new HorarioRecesoService());
        $this->availability();
        $horario = $this->createHorario();
        $this->deleteJson('/api/horarios/'.$horario->id)->assertOk();
        $registro = HistorialCambio::where('accion', 'eliminar')->firstOrFail();
        $this->assertNull($registro->horario_id);
        $this->assertTrue($registro->puede_revertirse);
        $this->postJson('/api/historial/revertir/'.$registro->id)->assertOk();
        $this->assertNotNull(Horario::find($horario->id));
    }

    private function availability(): void
    {
        DisponibilidadProfesor::create([
            'profesor_id' => $this->profesor->id, 'dia_semana' => 'lunes',
            'hora_inicio' => '07:00', 'hora_fin' => '13:00', 'institucion' => 'colegio', 'tipo' => 'disponible',
        ]);
    }

    private function configuration(): ConfiguracionHorario
    {
        $config = ConfiguracionHorario::create([
            'institucion' => 'colegio', 'nivel' => 'primaria', 'turno' => 'mañana',
            'nombre' => 'Configuracion prueba', 'hora_inicio' => '07:00', 'hora_fin' => '13:00',
            'duracion_bloque_minutos' => 45, 'año_academico' => 2026, 'activo' => true,
        ]);
        foreach ([
            ['07:00', '07:45', 'clase'], ['07:45', '08:30', 'clase'],
            ['08:30', '09:00', 'clase'], ['09:00', '09:30', 'receso'],
            ['09:30', '10:15', 'clase'], ['10:15', '11:00', 'clase'],
            ['11:00', '11:30', 'receso'], ['11:30', '12:15', 'clase'], ['12:15', '13:00', 'clase'],
        ] as $i => [$inicio, $fin, $tipo]) {
            $config->bloques()->create(['orden' => $i + 1, 'hora_inicio' => $inicio,
                'hora_fin' => $fin, 'tipo' => $tipo, 'nombre' => $tipo]);
        }
        return $config;
    }

    private function mockGenerator(array $result): void
    {
        $generator = Mockery::mock(HorarioGeneratorService::class);
        $generator->shouldReceive('generarHorarios')->once()->andReturn($result);
        $this->app->instance(HorarioGeneratorService::class, $generator);
    }
}
