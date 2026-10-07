<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $recesos = [
        ['nombre' => 'Receso mañana 1', 'hora_inicio' => '09:00', 'hora_fin' => '09:30'],
        ['nombre' => 'Receso mañana 2', 'hora_inicio' => '11:00', 'hora_fin' => '11:30'],
        ['nombre' => 'Receso tarde 1', 'hora_inicio' => '15:00', 'hora_fin' => '15:30'],
        ['nombre' => 'Receso tarde 2', 'hora_inicio' => '17:00', 'hora_fin' => '17:30'],
    ];

    public function up(): void
    {
        $configs = DB::table('configuracion_horarios')
            ->whereNull('deleted_at')
            ->where('activo', true)
            ->get();

        foreach ($configs as $config) {
            $limites = match ($config->turno) {
                'mañana' => ['inicio' => '07:00', 'fin' => '12:00'],
                'tarde' => ['inicio' => '13:00', 'fin' => '20:00'],
                default => ['inicio' => '07:00', 'fin' => '20:00'],
            };

            $recesos = match ($config->turno) {
                'mañana' => array_slice($this->recesos, 0, 2),
                'tarde' => array_slice($this->recesos, 2, 2),
                default => $this->recesos,
            };

            $bloques = $this->generarBloques(
                (int) $config->id,
                $limites['inicio'],
                $limites['fin'],
                max(30, (int) ($config->duracion_bloque_minutos ?: 45)),
                $recesos
            );

            DB::transaction(function () use ($config, $limites, $bloques): void {
                DB::table('configuracion_horarios')
                    ->where('id', $config->id)
                    ->update([
                        'hora_inicio' => $limites['inicio'],
                        'hora_fin' => $limites['fin'],
                        'updated_at' => now(),
                    ]);

                DB::table('bloques_horarios')
                    ->where('configuracion_horario_id', $config->id)
                    ->delete();

                DB::table('bloques_horarios')->insert($bloques);
            });
        }
    }

    public function down(): void
    {
        // No se reconstruye el estado anterior de las configuraciones.
    }

    private function generarBloques(int $configId, string $inicio, string $fin, int $duracion, array $recesos): array
    {
        $bloques = [];
        $orden = 1;
        $numeroClase = 1;
        $cursor = $this->minutos($inicio);
        $finMin = $this->minutos($fin);

        usort($recesos, fn ($a, $b) => $this->minutos($a['hora_inicio']) <=> $this->minutos($b['hora_inicio']));

        while ($cursor < $finMin) {
            $recesoActual = $this->recesoEnCursor($cursor, $recesos);

            if ($recesoActual) {
                $bloques[] = $this->bloque(
                    $configId,
                    $orden++,
                    'receso',
                    $recesoActual['nombre'],
                    $recesoActual['hora_inicio'],
                    $recesoActual['hora_fin']
                );
                $cursor = $this->minutos($recesoActual['hora_fin']);
                continue;
            }

            $siguienteReceso = collect($recesos)->first(
                fn ($receso) => $this->minutos($receso['hora_inicio']) > $cursor
            );
            $finClase = min($cursor + $duracion, $finMin);

            if ($siguienteReceso && $finClase > $this->minutos($siguienteReceso['hora_inicio'])) {
                $finClase = $this->minutos($siguienteReceso['hora_inicio']);
            }

            if ($finClase <= $cursor) {
                $cursor = $siguienteReceso
                    ? $this->minutos($siguienteReceso['hora_inicio'])
                    : $finMin;
                continue;
            }

            $bloques[] = $this->bloque(
                $configId,
                $orden++,
                'clase',
                'Clase ' . $numeroClase,
                $this->hora($cursor),
                $this->hora($finClase),
                $numeroClase++
            );
            $cursor = $finClase;
        }

        return $bloques;
    }

    private function bloque(int $configId, int $orden, string $tipo, string $nombre, string $inicio, string $fin, ?int $numero = null): array
    {
        return [
            'configuracion_horario_id' => $configId,
            'orden' => $orden,
            'hora_inicio' => $inicio,
            'hora_fin' => $fin,
            'tipo' => $tipo,
            'nombre' => $nombre,
            'numero_bloque' => $numero,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    private function recesoEnCursor(int $cursor, array $recesos): ?array
    {
        foreach ($recesos as $receso) {
            if ($cursor === $this->minutos($receso['hora_inicio'])) {
                return $receso;
            }
        }

        return null;
    }

    private function minutos(string $hora): int
    {
        [$horas, $minutos] = array_map('intval', explode(':', substr($hora, 0, 5)));

        return $horas * 60 + $minutos;
    }

    private function hora(int $minutos): string
    {
        return sprintf('%02d:%02d', intdiv($minutos, 60), $minutos % 60);
    }
};
