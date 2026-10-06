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
            $limites = $this->limitesTurno($config->turno);
            $recesos = $this->recesosTurno($config->turno);

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

            $bloques = $this->generarBloques(
                $config->id,
                $limites['inicio'],
                $limites['fin'],
                max(30, (int) ($config->duracion_bloque_minutos ?: 45)),
                $recesos
            );

            DB::table('bloques_horarios')->insert($bloques);
        }

        $this->cancelarHorariosEnReceso();
    }

    public function down(): void
    {
        // La migración normaliza datos operativos. No se reconstruye el estado
        // anterior porque las configuraciones podían variar manualmente.
    }

    private function limitesTurno(string $turno): array
    {
        return match ($turno) {
            'mañana' => ['inicio' => '07:00', 'fin' => '12:00'],
            'tarde' => ['inicio' => '13:00', 'fin' => '20:00'],
            default => ['inicio' => '07:00', 'fin' => '20:00'],
        };
    }

    private function recesosTurno(string $turno): array
    {
        return match ($turno) {
            'mañana' => array_slice($this->recesos, 0, 2),
            'tarde' => array_slice($this->recesos, 2, 2),
            default => $this->recesos,
        };
    }

    private function generarBloques(int $configId, string $inicio, string $fin, int $duracion, array $recesos): array
    {
        $bloques = [];
        $orden = 1;
        $numeroClase = 1;
        $cursor = $this->minutos($inicio);
        $finMin = $this->minutos($fin);
        $recesosOrdenados = collect($recesos)
            ->sortBy(fn ($receso) => $this->minutos($receso['hora_inicio']))
            ->values()
            ->all();

        while ($cursor < $finMin) {
            $recesoActual = $this->recesoEnCursor($cursor, $recesosOrdenados);

            if ($recesoActual) {
                $bloques[] = $this->bloque($configId, $orden++, 'receso', $recesoActual['nombre'], $recesoActual['hora_inicio'], $recesoActual['hora_fin']);
                $cursor = $this->minutos($recesoActual['hora_fin']);
                continue;
            }

            $siguienteReceso = $this->siguienteReceso($cursor, $recesosOrdenados);
            $finClase = min($cursor + $duracion, $finMin);

            if ($siguienteReceso && $finClase > $this->minutos($siguienteReceso['hora_inicio'])) {
                // Crear el tramo restante antes del receso en lugar de
                // descartarlo y dejar un hueco en la jornada.
                $finClase = $this->minutos($siguienteReceso['hora_inicio']);
            }

            if ($finClase <= $cursor) {
                $cursor = $siguienteReceso
                    ? $this->minutos($siguienteReceso['hora_inicio'])
                    : $finMin;
                continue;
            }

            if ($finClase <= $cursor) {
                break;
            }

            $bloques[] = $this->bloque(
                $configId,
                $orden++,
                'clase',
                'Clase ' . $numeroClase,
                $this->hora($cursor),
                $this->hora($finClase),
                $numeroClase
            );

            $numeroClase++;
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

    private function siguienteReceso(int $cursor, array $recesos): ?array
    {
        foreach ($recesos as $receso) {
            if ($this->minutos($receso['hora_inicio']) > $cursor) {
                return $receso;
            }
        }

        return null;
    }

    private function cancelarHorariosEnReceso(): void
    {
        foreach ($this->recesos as $receso) {
            DB::table('horarios')
                ->where('estado', 'activo')
                ->where('hora_inicio', '<', $receso['hora_fin'])
                ->where('hora_fin', '>', $receso['hora_inicio'])
                ->update([
                    'estado' => 'cancelado',
                    'updated_at' => now(),
                ]);
        }
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
