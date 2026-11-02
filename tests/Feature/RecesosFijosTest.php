<?php

namespace Tests\Feature;

use App\Models\ConfiguracionHorario;
use App\Services\HorarioRecesoService;
use Database\Seeders\ConfiguracionHorarioSeeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RecesosFijosTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        foreach (['2026_09_21_000003_create_configuracion_horarios_table.php',
            '2026_09_21_000004_create_bloques_horarios_table.php'] as $migration) {
            (require database_path('migrations/'.$migration))->up();
        }
        Schema::create('historial_cambios', fn (Blueprint $table) => $table->id());
    }

    public static function perfiles(): array
    {
        return [
            ['colegio', 'primaria', [['08:30', '08:50'], ['10:20', '10:40']]],
            ['colegio', 'secundaria', [['09:15', '09:35'], ['11:10', '11:25']]],
            ['academia', 'academia', [['08:50', '09:10'], ['10:40', '10:50']]],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('perfiles')]
    public function test_confirmed_ranges_are_returned_even_without_database_configuration(string $institucion, string $nivel, array $rangos): void
    {
        $this->withoutMiddleware();
        $data = $this->getJson('/api/configuraciones-horario/recesos?'.http_build_query(compact('institucion', 'nivel')))
            ->assertOk()->assertJsonPath('recesos_fijos', true)->json('data');
        $this->assertSame($rangos, array_map(fn ($r) => [$r['hora_inicio'], $r['hora_fin']], $data));
        $this->assertDatabaseCount('configuracion_horarios', 0);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('perfiles')]
    public function test_recess_boundaries_are_exclusive_and_payload_shift_cannot_bypass_them(string $institucion, string $nivel, array $rangos): void
    {
        $service = app(HorarioRecesoService::class);
        foreach ($rangos as [$inicio, $fin]) {
            $service->validarNoChocaConReceso($institucion, $nivel, 'mañana', \Carbon\Carbon::parse($inicio)->subMinute()->format('H:i'), $inicio);
            $service->validarNoChocaConReceso($institucion, $nivel, 'mañana', $fin, \Carbon\Carbon::parse($fin)->addMinute()->format('H:i'));
            try {
                $service->validarNoChocaConReceso($institucion, $nivel, 'tarde', $inicio, $fin);
                $this->fail('Un turno incorrecto no debe permitir invadir un receso.');
            } catch (ValidationException $exception) {
                $this->assertArrayHasKey('receso', $exception->errors());
            }
        }
    }

    public function test_other_levels_do_not_block_professor_availability_or_classes(): void
    {
        $service = app(HorarioRecesoService::class);
        $this->assertCount(0, $service->recesosParaDisponibilidad('colegio'));
        $this->assertCount(2, $service->recesosParaDisponibilidad('colegio', 'primaria'));
        $this->assertCount(2, $service->recesosParaDisponibilidad('academia'));
        $service->validarNoChocaConReceso('colegio', 'primaria', 'mañana', '09:15', '09:35');
        $service->validarNoChocaConReceso('colegio', 'secundaria', 'mañana', '08:30', '08:50');
        $this->assertNull($service->rangoChocaConRecesoInstitucion('colegio', '09:15', '09:35'));
        $this->assertNotNull($service->rangoChocaConRecesoInstitucion('academia', '08:50', '09:10'));
    }

    public function test_afternoon_and_complete_shifts_do_not_invent_missing_recesses(): void
    {
        $service = app(HorarioRecesoService::class);
        foreach (self::perfiles() as [$institucion, $nivel]) {
            $this->assertCount(0, $service->recesosVigentes($institucion, $nivel, 'tarde'));
            $this->assertCount(2, $service->recesosVigentes($institucion, $nivel, 'completo'));
        }
    }

    public function test_reading_legacy_configurations_uses_fixed_recesses_without_writing(): void
    {
        $this->withoutMiddleware();
        $config = ConfiguracionHorario::create([
            'institucion' => 'colegio', 'nivel' => 'secundaria', 'turno' => 'mañana',
            'nombre' => 'Legado', 'hora_inicio' => '07:00', 'hora_fin' => '13:10',
            'duracion_bloque_minutos' => 45, 'año_academico' => 2026, 'activo' => true,
        ]);
        $config->bloques()->create(['orden' => 1, 'tipo' => 'receso', 'hora_inicio' => '11:10', 'hora_fin' => '11:30']);
        $data = $this->getJson('/api/configuraciones-horario/'.$config->id)->assertOk()->json('data.bloques');
        $recesos = array_values(array_filter($data, fn ($b) => $b['tipo'] === 'receso'));
        $this->assertSame('11:25', \Carbon\Carbon::parse($recesos[1]['hora_fin'])->format('H:i'));
        $this->assertSame('11:30', $config->bloques()->sole()->hora_fin->format('H:i'));
        $this->assertDatabaseCount('historial_cambios', 0);
    }

    public function test_seeded_configurations_are_continuous_and_match_fixed_policy(): void
    {
        $this->seed(ConfiguracionHorarioSeeder::class);
        $service = app(HorarioRecesoService::class);
        foreach (ConfiguracionHorario::with('bloques')->get() as $config) {
            $service->validarBloques($config->bloques->toArray(), $config->turno,
                $config->hora_inicio->format('H:i'), $config->hora_fin->format('H:i'));
            $this->assertCount($config->turno === 'tarde' ? 0 : 2, $config->bloques->where('tipo', 'receso'));
        }
    }

    public function test_generator_preview_and_counts_use_fixed_policy_instead_of_legacy_blocks(): void
    {
        $this->withoutMiddleware();
        $config = ConfiguracionHorario::create([
            'institucion' => 'academia', 'nivel' => 'academia', 'turno' => 'tarde',
            'nombre' => 'Tarde legado', 'hora_inicio' => '15:30', 'hora_fin' => '20:00',
            'duracion_bloque_minutos' => 90, 'año_academico' => 2026, 'activo' => true,
        ]);
        $config->bloques()->create(['orden' => 1, 'tipo' => 'receso', 'hora_inicio' => '17:00', 'hora_fin' => '17:10']);
        $this->getJson('/api/horarios/generar/vista-previa?institucion=academia&nivel=academia&turno=tarde')
            ->assertOk()->assertJsonPath('data.total_recesos', 0)
            ->assertJsonPath('data.total_bloques_clase', 3)->assertJsonCount(3, 'data.bloques');
        $this->getJson('/api/horarios/generar/configuraciones')->assertOk()
            ->assertJsonPath('data.0.total_recesos', 0)->assertJsonPath('data.0.total_bloques', 3);
        $this->assertSame('receso', $config->bloques()->sole()->tipo);
    }
}
