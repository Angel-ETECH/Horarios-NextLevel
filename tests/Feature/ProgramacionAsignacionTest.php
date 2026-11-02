<?php

namespace Tests\Feature;

use App\Models\{Aula, Curso, Grado, Horario, Profesor, ProfesorCurso, DisponibilidadProfesor};
use App\Services\{DisponibilidadService, HorarioGeneratorService};
use Database\Seeders\ConfiguracionHorarioSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProgramacionAsignacionTest extends TestCase
{
    private array $datos;
    private array $plan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        foreach ([
            '0001_01_01_000000_create_users_table.php', '2026_08_17_135415_create_profesores_table.php',
            '2026_08_17_135523_create_grados_table.php', '2026_08_17_135524_create_alumnos_table.php',
            '2026_08_17_135525_create_cursos_table.php', '2026_08_17_135526_create_aulas_table.php',
            '2026_08_17_135529_create_profesor_curso_table.php', '2026_08_17_135530_create_horarios_table.php',
            '2026_08_17_135527_create_disponibilidad_profesor_table.php', '2026_08_31_111542_add_institucion_to_profesores_table.php',
            '2026_08_31_111544_add_institucion_to_horarios_table.php', '2026_08_31_111545_add_institucion_to_profesor_curso_table.php',
            '2026_08_31_111543_add_institucion_to_disponibilidad_profesor_table.php',
            '2026_09_21_000003_create_configuracion_horarios_table.php', '2026_09_21_000004_create_bloques_horarios_table.php',
            '2026_10_05_000002_add_aula_to_profesor_curso_table.php', '2026_10_12_000001_add_institucion_to_aulas_table.php',
            '2026_10_21_000001_create_recuperaciones_horario_table.php',
            '2026_10_23_000001_add_programacion_manual_to_profesor_curso.php',
        ] as $migration) (require database_path('migrations/'.$migration))->up();
        Schema::create('historial_cambios', function (Blueprint $t) {
            $t->id(); $t->foreignId('horario_id')->nullable()->constrained('horarios')->nullOnDelete();
            $t->unsignedBigInteger('usuario_id')->nullable(); $t->string('accion');
            $t->json('datos_anteriores')->nullable(); $t->json('datos_nuevos')->nullable();
            $t->string('motivo')->nullable(); $t->string('ip_usuario')->nullable();
            $t->text('user_agent')->nullable(); $t->timestamps();
        });
        $this->seed(ConfiguracionHorarioSeeder::class);
        $p = Profesor::create(['codigo' => 'P1', 'nombre' => 'Docente', 'apellido_paterno' => 'Prueba',
            'email' => 'p@example.test', 'dni' => '12345678', 'sexo' => 'masculino', 'estado' => 'activo',
            'institucion' => 'colegio', 'carga_horaria_maxima' => 40]);
        $c = Curso::create(['codigo' => 'C1', 'nombre' => 'Matemáticas', 'nivel' => 'primaria']);
        $g = Grado::create(['codigo' => 'G1', 'nivel' => 'primaria', 'grado' => '1',
            'nombre_completo' => 'Primaria 1 A', 'año_academico' => 2026, 'turno' => 'mañana']);
        $a = Aula::create(['codigo' => 'A1', 'nombre' => 'Aula 101', 'institucion' => 'ambos']);
        $this->datos = ['profesor_id' => $p->id, 'curso_id' => $c->id, 'grado_id' => $g->id,
            'aula_id' => $a->id, 'institucion' => 'colegio', 'horas_asignadas' => 2, 'rol' => 'titular', 'activo' => true];
        $this->plan = [
            ['dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '07:45'],
            ['dia_semana' => 'martes', 'hora_inicio' => '07:45', 'hora_fin' => '08:30'],
        ];
        app(DisponibilidadService::class)->reemplazar($p->id, 'colegio', [
            ['dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '13:00'],
            ['dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '08:00'],
        ]);
    }

    private function crearManual(): int
    {
        return $this->postJson('/api/asignaciones', $this->datos + ['modo_programacion' => 'manual', 'bloques_manuales' => $this->plan])
            ->assertCreated()->json('data.id');
    }

    public function test_manual_keeps_two_consecutive_tuesday_blocks_and_thursday_free(): void
    {
        $id = $this->crearManual();
        $this->assertSame(2, Horario::count());
        $this->assertSame(['martes'], Horario::pluck('dia_semana')->unique()->values()->all());
        $this->assertSame($this->plan, ProfesorCurso::findOrFail($id)->bloques_manuales);
        $this->assertSame(2, Profesor::first()->carga_horaria_actual);
        $this->postJson('/api/asignaciones/'.$id.'/desactivar')->assertOk();
        $this->assertSame(0, Horario::count());
        $this->postJson('/api/asignaciones/'.$id.'/activar')->assertOk();
        $this->assertSame(2, Horario::where('dia_semana', 'martes')->count());
        app(HorarioGeneratorService::class)->generarHorarios();
        $this->assertSame(2, Horario::count());
    }

    public function test_automatic_save_only_generates_the_saved_assignment(): void
    {
        $curso = Curso::create(['codigo' => 'C2', 'nombre' => 'Arte', 'nivel' => 'primaria']);
        $otra = ProfesorCurso::create(array_replace($this->datos, ['curso_id' => $curso->id]));
        $this->postJson('/api/asignaciones', $this->datos)->assertCreated();
        $this->assertSame(2, Horario::count());
        $this->assertSame(0, Horario::where('curso_id', $otra->curso_id)->count());
    }

    public static function diasManuales(): array
    {
        return array_map(fn ($dia) => [$dia], ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado']);
    }

    #[DataProvider('diasManuales')]
    public function test_manual_respects_any_selected_available_day(string $dia): void
    {
        app(DisponibilidadService::class)->reemplazar($this->datos['profesor_id'], 'colegio', [
            ['dia_semana' => $dia, 'hora_inicio' => '07:00', 'hora_fin' => '08:30'],
        ]);
        $this->plan = array_map(fn ($b) => array_replace($b, ['dia_semana' => $dia]), $this->plan);
        $this->crearManual();
        $this->assertSame([$dia], Horario::pluck('dia_semana')->unique()->values()->all());
        $this->assertSame(2, Horario::count());
    }

    public function test_automatic_assignment_preserves_existing_manual_choices(): void
    {
        $this->crearManual();
        $antes = Horario::orderBy('id')->get()->toArray();
        $curso = Curso::create(['codigo' => 'C2', 'nombre' => 'Arte', 'nivel' => 'primaria']);
        $this->postJson('/api/asignaciones', array_replace($this->datos, [
            'curso_id' => $curso->id, 'modo_programacion' => 'automatico',
        ]))->assertCreated();
        $this->assertSame($antes, Horario::where('curso_id', $this->datos['curso_id'])->orderBy('id')->get()->toArray());
        $this->assertSame(4, Horario::count());
        foreach (Horario::where('curso_id', $curso->id)->get() as $h) {
            $this->assertFalse($h->dia_semana === 'martes' && $h->hora_inicio->format('H:i') < '08:30');
        }
    }

    public function test_manual_rejects_recess_outside_availability_duplicates_and_wrong_count(): void
    {
        foreach ([
            [$this->plan[0]], [$this->plan[0], $this->plan[0]],
            [$this->plan[0], ['dia_semana' => 'martes', 'hora_inicio' => '08:30', 'hora_fin' => '09:15']],
            [$this->plan[0], ['dia_semana' => 'jueves', 'hora_inicio' => '07:45', 'hora_fin' => '08:30']],
        ] as $plan) {
            $this->postJson('/api/asignaciones', $this->datos + ['modo_programacion' => 'manual', 'bloques_manuales' => $plan])
                ->assertUnprocessable()->assertJsonValidationErrors('bloques_manuales');
            $this->assertSame(0, ProfesorCurso::count());
            $this->assertSame(0, Horario::count());
        }
    }

    public static function recursos(): array { return [['profesor_id'], ['aula_id'], ['grado_id']]; }

    #[DataProvider('recursos')]
    public function test_manual_rejects_conflicts_and_rolls_back_everything(string $recurso): void
    {
        $otroP = Profesor::create(['codigo' => 'P2', 'nombre' => 'Otro', 'apellido_paterno' => 'Docente',
            'email' => 'otro@example.test', 'dni' => '87654321', 'sexo' => 'masculino', 'institucion' => 'ambos']);
        $otroA = Aula::create(['codigo' => 'A2', 'nombre' => 'Aula 102']);
        $otroG = Grado::create(['codigo' => 'G2', 'nivel' => 'primaria', 'grado' => '2', 'nombre_completo' => 'Primaria 2', 'año_academico' => 2026]);
        $horario = ['profesor_id' => $otroP->id, 'aula_id' => $otroA->id, 'grado_id' => $otroG->id,
            'curso_id' => $this->datos['curso_id'], 'dia_semana' => 'martes', 'hora_inicio' => '07:30', 'hora_fin' => '08:00',
            'institucion' => 'academia', 'estado' => 'activo', 'periodo_academico' => date('Y').'-'.(date('Y')+1)];
        $horario[$recurso] = $this->datos[$recurso];
        Horario::create($horario);
        $antes = DB::table('historial_cambios')->count();
        $this->postJson('/api/asignaciones', $this->datos + ['modo_programacion' => 'manual', 'bloques_manuales' => $this->plan])
            ->assertUnprocessable();
        $this->assertSame(0, ProfesorCurso::count()); $this->assertSame(1, Horario::count());
        $this->assertSame($antes, DB::table('historial_cambios')->count());
    }

    public function test_failed_manual_edit_preserves_assignment_and_existing_blocks(): void
    {
        $id = $this->crearManual();
        $antes = Horario::all()->toArray();
        $plan = array_map(fn ($b) => array_replace($b, ['dia_semana' => 'jueves']), $this->plan);
        $this->putJson('/api/asignaciones/'.$id, ['modo_programacion' => 'manual', 'bloques_manuales' => $plan])->assertUnprocessable();
        $this->assertSame($antes, Horario::all()->toArray());
        $this->assertSame($this->plan, ProfesorCurso::findOrFail($id)->bloques_manuales);
    }

    public function test_editing_manual_block_keeps_assignment_plan_in_sync(): void
    {
        $id = $this->crearManual();
        $h = Horario::orderBy('hora_inicio')->first();
        app(\App\Services\HorarioService::class)->update($h->id, ['dia_semana' => 'jueves']);
        $this->assertSame('jueves', ProfesorCurso::findOrFail($id)->bloques_manuales[0]['dia_semana']);
        app(HorarioGeneratorService::class)->generarHorarios();
        $this->assertSame(2, Horario::count());
        $this->assertSame(1, Horario::where('dia_semana', 'jueves')->count());
    }

    public function test_drag_converts_automatic_assignment_and_preserves_plan_after_regeneration(): void
    {
        $id = $this->crearManual();
        ProfesorCurso::findOrFail($id)->update(['modo_programacion' => 'automatico', 'bloques_manuales' => null]);
        $h = Horario::orderBy('hora_inicio')->firstOrFail();
        $this->postJson('/api/horarios/'.$h->id.'/mover', [
            'dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '07:45', 'origen' => $this->plan[0],
            'aula_id' => 999,
        ])->assertOk();
        $a = ProfesorCurso::findOrFail($id);
        $this->assertSame('manual', $a->modo_programacion);
        $this->assertSame('jueves', $a->bloques_manuales[0]['dia_semana']);
        $this->assertSame($this->datos['aula_id'], $h->fresh()->aula_id);
        app(HorarioGeneratorService::class)->generarHorarios();
        $this->assertSame(2, Horario::count());
        $this->assertSame(1, Horario::where('dia_semana', 'jueves')->count());
        $this->postJson('/api/horarios/'.$h->id.'/mover', [
            'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '07:45', 'origen' => $this->plan[0],
        ])->assertUnprocessable();
        $this->assertSame('jueves', $h->fresh()->dia_semana);
    }

    #[DataProvider('recursos')]
    public function test_drag_rechecks_a_destination_that_became_occupied_after_preview(string $recurso): void
    {
        $id = $this->crearManual();
        $h = Horario::orderBy('hora_inicio')->firstOrFail();
        $opciones = $this->getJson('/api/horarios/'.$h->id.'/opciones-movimiento')->assertOk()->json('data');
        $this->assertTrue(collect($opciones)->first(fn ($b) => $b['dia_semana'] === 'jueves' && $b['hora_inicio'] === '07:00')['disponible']);
        $p = Profesor::create(['codigo' => 'P2', 'nombre' => 'Otro', 'apellido_paterno' => 'Docente',
            'email' => 'otro@example.test', 'dni' => '87654321', 'sexo' => 'masculino', 'institucion' => 'ambos']);
        $a = Aula::create(['codigo' => 'A2', 'nombre' => 'Otra aula']);
        $g = Grado::create(['codigo' => 'G2', 'nivel' => 'primaria', 'grado' => '2', 'nombre_completo' => 'Primaria 2', 'año_academico' => 2026]);
        $ocupacion = ['profesor_id' => $p->id, 'aula_id' => $a->id, 'grado_id' => $g->id,
            'curso_id' => $this->datos['curso_id'], 'dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '07:45',
            'institucion' => $recurso === 'grado_id' ? 'colegio' : 'academia', 'estado' => 'activo',
            'periodo_academico' => $h->periodo_academico];
        $ocupacion[$recurso] = $this->datos[$recurso];
        Horario::create($ocupacion);
        $this->postJson('/api/horarios/'.$h->id.'/mover', [
            'dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '07:45', 'origen' => $this->plan[0],
        ])->assertUnprocessable();
        $this->assertSame('martes', $h->fresh()->dia_semana);
        $this->assertSame($this->plan, ProfesorCurso::findOrFail($id)->bloques_manuales);
    }

    public function test_drag_checks_sibling_blocks_recess_duration_and_availability_without_mutating_data(): void
    {
        $id = $this->crearManual();
        $h = Horario::orderBy('hora_inicio')->firstOrFail();
        $opciones = $this->getJson('/api/horarios/'.$h->id.'/opciones-movimiento')->assertOk()->json('data');
        $ocupado = collect($opciones)->first(fn ($b) => $b['dia_semana'] === 'martes' && $b['hora_inicio'] === '07:45');
        $this->assertFalse($ocupado['disponible']);
        foreach ([['martes', '07:45', '08:30'], ['martes', '08:30', '09:15'], ['jueves', '07:45', '08:30'], ['jueves', '07:00', '07:30']] as [$dia, $inicio, $fin]) {
            $this->postJson('/api/horarios/'.$h->id.'/mover', [
                'dia_semana' => $dia, 'hora_inicio' => $inicio, 'hora_fin' => $fin, 'origen' => $this->plan[0],
            ])->assertUnprocessable();
        }
        $this->assertSame($this->plan, ProfesorCurso::findOrFail($id)->bloques_manuales);
        $this->assertSame('martes', $h->fresh()->dia_semana);
        $this->assertSame(2, Horario::count());
    }

    public function test_extension_is_not_blocked_by_an_unrelated_legacy_missing_availability(): void
    {
        $this->crearManual();
        Horario::create(array_merge($this->datos, ['dia_semana' => 'viernes', 'hora_inicio' => '07:00', 'hora_fin' => '07:45',
            'estado' => 'activo', 'periodo_academico' => date('Y').'-'.(date('Y')+1)]));
        $antes = Horario::all()->toArray();
        $this->putJson('/api/disponibilidades/profesor/'.$this->datos['profesor_id'].'/institucion/colegio', ['franjas' => [
            ['dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '14:00'],
            ['dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '08:00'],
        ]])->assertOk();
        $this->assertSame($antes, Horario::all()->toArray());
    }

    public function test_availability_can_expand_add_days_and_remove_unused_hours(): void
    {
        $this->crearManual(); $antes = Horario::all()->toArray();
        $url = '/api/disponibilidades/profesor/'.$this->datos['profesor_id'].'/institucion/colegio';
        foreach (['14:00', '08:30'] as $fin) {
            $this->putJson($url, ['franjas' => [
                ['dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => $fin],
                ['dia_semana' => 'miércoles', 'hora_inicio' => '14:00', 'hora_fin' => '16:00'],
            ]])->assertOk();
            $this->assertSame($antes, Horario::all()->toArray());
        }
        $libre = DisponibilidadProfesor::where('dia_semana', 'miércoles')->firstOrFail();
        $this->deleteJson('/api/disponibilidades/'.$libre->id)->assertOk();
        $this->assertSame($antes, Horario::all()->toArray());
    }

    public function test_removing_used_minutes_returns_class_identity_and_keeps_database_unchanged(): void
    {
        $this->crearManual();
        $antes = DisponibilidadProfesor::all()->toArray();
        $response = $this->putJson('/api/disponibilidades/profesor/'.$this->datos['profesor_id'].'/institucion/colegio', ['franjas' => [
            ['dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00'],
        ]])->assertUnprocessable()->assertJsonValidationErrors('disponibilidad');
        $this->assertStringContainsString('Matemáticas', $response->json('errors.disponibilidad.0'));
        $this->assertStringContainsString('07:45–08:30', $response->json('errors.disponibilidad.0'));
        $this->assertSame($antes, DisponibilidadProfesor::all()->toArray());
        $ocupada = DisponibilidadProfesor::where('dia_semana', 'martes')->firstOrFail();
        $this->deleteJson('/api/disponibilidades/'.$ocupada->id)->assertUnprocessable();
        $this->assertFalse($ocupada->fresh()->trashed());
    }

    public function test_availability_accepts_all_form_days_and_normalizes_legacy_unaccented_names(): void
    {
        $url = '/api/disponibilidades/profesor/'.$this->datos['profesor_id'].'/institucion/colegio';
        foreach ([
            ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'],
            ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'],
            [' LUNES ', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO'],
        ] as $dias) {
            $this->putJson($url, ['franjas' => array_map(fn ($dia) => [
                'dia_semana' => $dia, 'hora_inicio' => '14:00', 'hora_fin' => '16:00',
            ], $dias)])->assertOk();
            $this->assertEqualsCanonicalizing(['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'],
                DisponibilidadProfesor::pluck('dia_semana')->all());
        }
    }

    public function test_invalid_availability_day_has_spanish_message_and_preserves_existing_data(): void
    {
        $antes = DisponibilidadProfesor::all()->toArray();
        $r = $this->putJson('/api/disponibilidades/profesor/'.$this->datos['profesor_id'].'/institucion/colegio', [
            'franjas' => [['dia_semana' => 'dia-invalido', 'hora_inicio' => '14:00', 'hora_fin' => '16:00']],
        ])->assertUnprocessable()->assertJsonValidationErrors('franjas.0.dia_semana');
        $this->assertStringContainsString('El día de la disponibilidad', $r->json('errors.franjas.0.dia_semana.0') ?? $r->json('errors')['franjas.0.dia_semana'][0]);
        $this->assertSame($antes, DisponibilidadProfesor::all()->toArray());
    }
}
