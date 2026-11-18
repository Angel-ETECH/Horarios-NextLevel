<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CodigoSecuencialService
{
    public const CATALOGOS = [
        'profesores' => 'DOC', 'cursos' => 'CUR', 'aulas' => 'AUL', 'grados' => 'GRA',
    ];

    public function siguiente(string $tabla, bool $reservar = false): string
    {
        $prefijo = self::CATALOGOS[$tabla] ?? null;
        if (!$prefijo) {
            throw ValidationException::withMessages(['catalogo' => 'Catálogo no válido.']);
        }

        return DB::transaction(function () use ($tabla, $prefijo, $reservar) {
            $consulta = DB::table('secuencias_codigos')->where('catalogo', $tabla);
            $secuencia = ($reservar ? $consulta->lockForUpdate() : $consulta)->first();
            if (!$secuencia) {
                throw ValidationException::withMessages(['codigo' => 'Falta instalar la secuencia de códigos. Ejecuta las migraciones.']);
            }
            $numero = $secuencia->ultimo_numero;
            do {
                $numero++;
                $codigo = $prefijo.'-'.str_pad((string) $numero, 3, '0', STR_PAD_LEFT);
            } while (DB::table($tabla)->where('codigo', $codigo)->exists());

            if ($reservar) {
                DB::table('secuencias_codigos')->where('catalogo', $tabla)->update(['ultimo_numero' => $numero]);
            }
            return $codigo;
        });
    }
}
