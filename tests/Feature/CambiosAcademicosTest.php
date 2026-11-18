<?php

namespace Tests\Feature;

use App\Models\{Aula, Curso, Grado, Profesor, ProfesorCurso, Horario};
use App\Services\HorarioGeneratorService;

class CambiosAcademicosTest extends ProgramacionAsignacionTest
{
    public function test_catalogos_muestran_primero_los_nuevos_y_editar_no_cambia_su_posicion(): void
    {
        foreach (['profesores' => Profesor::class, 'cursos' => Curso::class,
            'aulas' => Aula::class, 'grados' => Grado::class] as $catalogo => $modelo) {
            $anterior = $modelo::firstOrFail();
            $anterior->forceFill(['created_at' => '2026-01-01 08:00:00'])->save();
            $nuevos = [];
            foreach ([1, 2] as $numero) {
                $nuevo = $anterior->replicate();
                $nuevo->codigo = 'TEST-'.$numero;
                if ($catalogo === 'profesores') {
                    $nuevo->email = 'orden'.$numero.'@example.test';
                    $nuevo->dni = '9999999'.$numero;
                }
                $nuevo->created_at = '2026-02-01 08:00:00';
                $nuevo->save();
                $nuevos[] = $nuevo->id;
            }
            $anterior->forceFill(['updated_at' => '2026-03-01 08:00:00'])->save();
            $respuesta = $this->getJson('/api/'.$catalogo.'?per_page=100')->assertOk();
            $filas = $respuesta->json($catalogo === 'aulas' ? 'data' : 'data.data');
            $this->assertSame([$nuevos[1], $nuevos[0], $anterior->id], array_column($filas, 'id'));
            $respuesta = $this->getJson('/api/'.$catalogo.'?per_page=100')->assertOk();
            $this->assertSame($filas, $respuesta->json($catalogo === 'aulas' ? 'data' : 'data.data'));
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        (require database_path('migrations/2026_11_17_000001_add_niveles_compartidos_to_cursos.php'))->up();
        (require database_path('migrations/2026_11_17_000002_create_secuencias_codigos.php'))->up();
    }

    public function test_codigos_generados_y_aulas_sin_nivel(): void
    {
        $aula = $this->postJson('/api/aulas', [
            'nombre' => 'Compartida', 'capacidad' => 30, 'tipo' => 'aula_normal', 'institucion' => 'ambos',
        ])->assertCreated()->json('data');
        $this->assertSame('AUL-001', $aula['codigo']);
        $this->assertSame('todos', $aula['nivel']);
        $curso = $this->postJson('/api/cursos', $this->cursoPayload())->assertCreated()->json('data');
        $this->assertSame('CUR-001', $curso['codigo']);
        $this->assertSame(['primaria', 'secundaria'], $curso['niveles']);
        $this->assertTrue(Curso::find($curso['id'])->admiteNivel('secundaria'));
        $this->assertFalse(Curso::find($curso['id'])->admiteNivel('academia'));
        $this->getJson('/api/cursos?nivel=academia')->assertDontSee($curso['codigo']);
        $this->getJson('/api/cursos?nivel=secundaria')->assertSee($curso['codigo']);
    }

    public static function duraciones(): array
    {
        return array_map(fn ($duracion) => [$duracion], [30, 35, 40, 45, 50, 55, 60]);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('duraciones')]
    public function test_duracion_elegida_genera_bloques_correctos(int $duracion): void
    {
        $curso = $this->postJson('/api/cursos', array_replace($this->cursoPayload(),
            ['duracion_minutos' => $duracion]))->assertCreated()->json('data');
        $data = [
            'profesor_id' => Profesor::first()->id, 'curso_id' => $curso['id'],
            'grado_id' => Grado::first()->id, 'aula_id' => Aula::first()->id,
            'institucion' => 'colegio', 'horas_asignadas' => 2, 'rol' => 'titular', 'activo' => true,
        ];
        $opciones = app(HorarioGeneratorService::class)->opcionesManuales($data);
        $this->assertSame(\Carbon\Carbon::parse('07:00')->addMinutes($duracion)->format('H:i'), $opciones[0]['hora_fin']);
        $this->postJson('/api/asignaciones', $data)->assertCreated();
        $bloques = Horario::where('curso_id', $curso['id'])->get();
        $this->assertCount(2, $bloques);
        foreach ($bloques as $bloque) {
            $this->assertLessThanOrEqual($duracion, abs($bloque->hora_fin->diffInMinutes($bloque->hora_inicio)));
        }
    }

    public function test_niveles_vacios_y_duraciones_invalidas_se_rechazan(): void
    {
        $this->postJson('/api/cursos', array_replace($this->cursoPayload(), ['niveles' => []]))
            ->assertUnprocessable()->assertJsonValidationErrors('niveles');
        $this->postJson('/api/cursos', array_replace($this->cursoPayload(), ['duracion_minutos' => 37]))
            ->assertUnprocessable()->assertJsonValidationErrors('duracion_minutos');
    }

    public function test_codigo_del_profesor_y_turno_de_academia(): void
    {
        $profesor = $this->postJson('/api/profesores', [
            'nombre' => 'Docente nuevo', 'apellido_paterno' => 'Prueba', 'email' => 'nuevo@example.test',
            'dni' => '87654321', 'sexo' => 'masculino', 'estado' => 'activo',
            'institucion' => 'ambos', 'carga_horaria_maxima' => 40,
        ])->assertCreated()->json('data');
        $this->assertSame('DOC-001', $profesor['codigo']);
        $grado = $this->postJson('/api/grados', [
            'nivel' => 'academia', 'grado' => 'Aula 2', 'seccion' => 'A1',
            'capacidad_maxima' => 30, 'numero_estudiantes' => 10,
            'año_academico' => (int) date('Y'), 'turno' => 'tarde', 'activo' => true,
        ])->assertCreated()->json('data');
        $this->assertSame('GRA-001', $grado['codigo']);
        $this->assertSame('tarde', $grado['turno']);
    }

    public function test_no_se_puede_quitar_un_nivel_utilizado(): void
    {
        $curso = $this->postJson('/api/cursos', $this->cursoPayload())->assertCreated()->json('data');
        ProfesorCurso::create([
            'profesor_id' => Profesor::first()->id, 'curso_id' => $curso['id'],
            'grado_id' => Grado::first()->id, 'aula_id' => Aula::first()->id,
            'institucion' => 'colegio', 'horas_asignadas' => 1, 'rol' => 'titular', 'activo' => true,
        ]);
        $this->putJson('/api/cursos/'.$curso['id'], array_replace($this->cursoPayload(),
            ['niveles' => ['secundaria']]))->assertUnprocessable();
        $this->assertSame(['primaria', 'secundaria'], Curso::find($curso['id'])->niveles);
    }

    public function test_disponibilidad_antigua_compartida_no_permite_doble_clase(): void
    {
        $profesor = Profesor::first();
        $profesor->update(['institucion' => 'ambos']);
        // Datos anteriores a la reserva exclusiva: deben seguir protegidos contra doble clase.
        \Illuminate\Support\Facades\DB::table('disponibilidad_profesor')->insert([
            'profesor_id' => $profesor->id, 'institucion' => 'academia', 'tipo' => 'disponible',
            'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00',
        ]);
        $curso = $this->postJson('/api/cursos', array_replace($this->cursoPayload(),
            ['niveles' => ['primaria', 'academia']]))->assertCreated()->json('data');
        $datos = ['profesor_id' => $profesor->id, 'curso_id' => $curso['id'],
            'grado_id' => Grado::first()->id, 'aula_id' => Aula::first()->id,
            'institucion' => 'colegio', 'horas_asignadas' => 1, 'activo' => true, 'rol' => 'titular'];
        $datos['modo_programacion'] = 'manual';
        $datos['bloques_manuales'] = [['dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '07:35']];
        $this->postJson('/api/asignaciones', $datos)->assertCreated();
        $grado = Grado::create(['nombre_completo' => 'Academia Aula 3 A1', 'nivel' => 'academia', 'grado' => 'Aula 3', 'seccion' => 'A1',
            'año_academico' => date('Y'), 'turno' => 'mañana']);
        $opciones = app(HorarioGeneratorService::class)->opcionesManuales(array_replace($datos,
            ['grado_id' => $grado->id, 'institucion' => 'academia']));
        $ocupado = collect($opciones)->first(fn ($b) => $b['dia_semana'] === 'martes' && $b['hora_inicio'] === '07:00');
        $this->assertFalse($ocupado['disponible']);
        $this->assertSame('Profesor ocupado', $ocupado['motivo']);
    }

    public static function reservasInstitucionales(): array
    {
        return [
            ['academia', '07:00', '08:00'], ['academia', '07:30', '08:30'],
            ['academia', '12:30', '13:30'], ['colegio', '07:00', '08:00'],
            ['colegio', '07:30', '08:30'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('reservasInstitucionales')]
    public function test_reservas_impiden_cruces_totales_y_parciales_en_ambas_instituciones(string $destino, string $inicio, string $fin): void
    {
        $profesor = Profesor::first();
        $profesor->update(['institucion' => 'ambos']);
        $ruta = '/api/disponibilidades/profesor/'.$profesor->id.'/institucion/';
        if ($destino === 'colegio') {
            $this->putJson($ruta.'colegio', ['franjas' => []])->assertOk();
            $this->putJson($ruta.'academia', ['franjas' => [
                ['dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00'],
            ]])->assertOk();
        }
        $antes = \App\Models\DisponibilidadProfesor::orderBy('id')->get()->toArray();
        $this->putJson($ruta.$destino, ['franjas' => [
            ['dia_semana' => 'martes', 'hora_inicio' => $inicio, 'hora_fin' => $fin],
        ]])->assertUnprocessable()->assertJsonValidationErrors('reserva');
        $this->assertSame($antes, \App\Models\DisponibilidadProfesor::orderBy('id')->get()->toArray());
    }

    public function test_reserva_admite_limites_contiguos_y_otro_dia_y_se_libera_al_retirar(): void
    {
        $p = Profesor::first();
        $p->update(['institucion' => 'ambos']);
        $ruta = '/api/disponibilidades/profesor/'.$p->id.'/institucion/';
        $this->putJson($ruta.'academia', ['franjas' => [
            ['dia_semana' => 'martes', 'hora_inicio' => '13:00', 'hora_fin' => '14:00'],
            ['dia_semana' => 'lunes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00'],
        ]])->assertOk();
        $this->putJson($ruta.'colegio', ['franjas' => []])->assertOk();
        $this->putJson($ruta.'academia', ['franjas' => [
            ['dia_semana' => 'jueves', 'hora_inicio' => '07:00', 'hora_fin' => '08:00'],
        ]])->assertOk();
    }

    public function test_crear_y_editar_directamente_no_evade_reserva_y_clases_quedan_intactas(): void
    {
        $p = Profesor::first();
        $p->update(['institucion' => 'ambos']);
        $this->postJson('/api/asignaciones', [
            'profesor_id' => $p->id, 'curso_id' => Curso::first()->id,
            'grado_id' => Grado::first()->id, 'aula_id' => Aula::first()->id,
            'institucion' => 'colegio', 'horas_asignadas' => 1, 'activo' => true, 'rol' => 'titular',
        ])->assertCreated();
        $clases = Horario::orderBy('id')->get()->toArray();
        $data = ['profesor_id' => $p->id, 'institucion' => 'academia', 'tipo' => 'disponible',
            'dia_semana' => 'martes', 'hora_inicio' => '07:00', 'hora_fin' => '08:00', 'turno' => 'mañana'];
        $this->postJson('/api/disponibilidades', $data)->assertUnprocessable()->assertJsonValidationErrors('reserva');
        $nuevo = $this->postJson('/api/disponibilidades', array_replace($data, ['dia_semana' => 'lunes']))
            ->assertCreated()->json('data.id');
        $this->putJson('/api/disponibilidades/'.$nuevo, $data)->assertUnprocessable()->assertJsonValidationErrors('reserva');
        $this->assertSame('lunes', \App\Models\DisponibilidadProfesor::findOrFail($nuevo)->dia_semana);
        $this->assertSame($clases, Horario::orderBy('id')->get()->toArray());
        $this->putJson('/api/disponibilidades/profesor/'.$p->id.'/institucion/colegio', ['franjas' => []])
            ->assertUnprocessable();
        $this->assertSame($clases, Horario::orderBy('id')->get()->toArray());
    }

    private function cursoPayload(): array
    {
        return ['nombre' => 'Curso compartido', 'horas_semanales' => 2, 'duracion_minutos' => 35,
            'nivel' => 'todos', 'niveles' => ['primaria', 'secundaria'], 'tipo' => 'obligatorio', 'activo' => true];
    }

    public function test_secuencia_no_se_reserva_al_consultar_y_no_reutiliza_eliminados(): void
    {
        $servicio = app(\App\Services\CodigoSecuencialService::class);
        $this->assertSame('AUL-001', $servicio->siguiente('aulas'));
        $this->assertSame('AUL-001', $servicio->siguiente('aulas'));
        $aula = Aula::create(['nombre' => 'Primera', 'capacidad' => 20]);
        $this->assertSame('AUL-001', $aula->codigo);
        $aula->delete();
        $segunda = Aula::create(['nombre' => 'Segunda', 'capacidad' => 20]);
        $this->assertSame('AUL-002', $segunda->codigo);
        try {
            \Illuminate\Support\Facades\DB::transaction(function () {
                Aula::create(['nombre' => 'Fallida', 'capacidad' => 20]);
                throw new \RuntimeException('Cancelar prueba');
            });
        } catch (\RuntimeException $exception) {
            $this->assertSame('Cancelar prueba', $exception->getMessage());
        }
        $this->assertSame('AUL-003', $servicio->siguiente('aulas'));
        $this->getJson('/api/codigos/aulas/siguiente')->assertOk()->assertJson(['codigo' => 'AUL-003']);
    }
}
