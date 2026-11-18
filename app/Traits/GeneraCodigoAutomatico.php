<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Throwable;

trait GeneraCodigoAutomatico
{
    /**
     * Boot del trait para asignar código automático en creating
     * y asegurar que el código sea inmutable en updating.
     */
    public static function bootGeneraCodigoAutomatico(): void
    {
        static::creating(function (Model $model) {
            if (!isset($model->codigo) || trim((string) $model->codigo) === '') {
                $model->codigo = $model->generarSiguienteCodigo();
            }
        });

        static::updating(function (Model $model) {
            // El código no debe cambiar nunca una vez creado
            if ($model->isDirty('codigo') && $model->getOriginal('codigo') !== null) {
                $model->codigo = $model->getOriginal('codigo');
            }
        });
    }

    /**
     * Obtener el prefijo configurado para este modelo.
     */
    public function getPrefijoCodigo(): string
    {
        if (property_exists($this, 'prefijoCodigo') && !empty($this->prefijoCodigo)) {
            return (string) $this->prefijoCodigo;
        }

        if (defined(static::class . '::PREFIJO_CODIGO')) {
            return (string) constant(static::class . '::PREFIJO_CODIGO');
        }

        return match (class_basename(static::class)) {
            'Profesor' => 'PROF',
            'Curso' => 'CUR',
            'Aula' => 'AUL',
            'Grado' => 'GRA',
            default => strtoupper(substr(class_basename(static::class), 0, 3)),
        };
    }

    /**
     * Generar el siguiente código con formato PREFIJO-NNN.
     * Consulta con withTrashed() para no reutilizar códigos de registros eliminados.
     */
    public function generarSiguienteCodigo(): string
    {
        $prefijo = $this->getPrefijoCodigo();

        $query = static::withTrashed()->where('codigo', 'like', "{$prefijo}-%");

        if (DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        $codigos = $query->pluck('codigo');

        $maxNumero = 0;
        $pattern = '/^' . preg_quote($prefijo, '/') . '-(\d+)$/';

        foreach ($codigos as $codigo) {
            if (preg_match($pattern, (string) $codigo, $matches)) {
                $numero = (int) $matches[1];
                if ($numero > $maxNumero) {
                    $maxNumero = $numero;
                }
            }
        }

        $siguienteNumero = $maxNumero + 1;
        $numeroFormateado = str_pad((string) $siguienteNumero, 3, '0', STR_PAD_LEFT);

        return "{$prefijo}-{$numeroFormateado}";
    }

    /**
     * Ejecuta una operación con reintento ante violación de unicidad (SQLSTATE 23000).
     */
    public static function ejecutarConReintento(callable $operacion, int $maxIntentos = 3): mixed
    {
        for ($intento = 1; $intento <= $maxIntentos; $intento++) {
            try {
                return $operacion();
            } catch (Throwable $e) {
                if ($intento < $maxIntentos && static::esErrorViolacionUnicidad($e)) {
                    usleep(50000 * $intento);
                    continue;
                }
                throw $e;
            }
        }

        throw new \RuntimeException('Se superó el número máximo de reintentos para generar el código.');
    }

    /**
     * Determina si la excepción corresponde a un error de duplicado / unicidad SQLSTATE 23000.
     */
    public static function esErrorViolacionUnicidad(Throwable $e): bool
    {
        if ($e instanceof QueryException) {
            $sqlState = $e->errorInfo[0] ?? null;
            $errorCode = $e->errorInfo[1] ?? null;
            if ($sqlState === '23000' || $errorCode === 1062 || $e->getCode() == 23000) {
                return true;
            }
        }

        $prev = $e->getPrevious();
        if ($prev instanceof Throwable) {
            return static::esErrorViolacionUnicidad($prev);
        }

        return false;
    }
}
