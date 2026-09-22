<?php
// app/Models/HistorialCambio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HistorialCambio extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios';

    protected $fillable = [
        'horario_id',
        'usuario_id',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'motivo',
        'ip_usuario',
        'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ============ CONSTANTES DE ACCIONES ============

    public const ACCION_CREAR = 'crear';
    public const ACCION_ACTUALIZAR = 'actualizar';
    public const ACCION_ELIMINAR = 'eliminar';
    public const ACCION_RESTAURAR = 'restaurar';
    public const ACCION_REVERTIR = 'revertir';

    public const ACCIONES = [
        self::ACCION_CREAR,
        self::ACCION_ACTUALIZAR,
        self::ACCION_ELIMINAR,
        self::ACCION_RESTAURAR,
        self::ACCION_REVERTIR,
    ];

    // ============ RELACIONES ============

    /**
     * Un registro de historial pertenece a un horario
     * (Se usa withTrashed para poder consultar cambios de horarios eliminados)
     */
    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id')->withTrashed();
    }

    /**
     * Un registro de historial pertenece a un usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // ============ SCOPES ============

    /**
     * Filtrar por horario específico
     *
     * Uso: HistorialCambio::porHorario(5)->get()
     */
    public function scopePorHorario(Builder $query, int $horarioId): Builder
    {
        return $query->where('horario_id', $horarioId);
    }

    /**
     * Alias en inglés (compatibilidad)
     */
    public function scopeDeHorario(Builder $query, int $horarioId): Builder
    {
        return $this->scopePorHorario($query, $horarioId);
    }

    /**
     * Filtrar por acción
     *
     * Uso: HistorialCambio::porAccion('crear')->get()
     */
    public function scopePorAccion(Builder $query, string $accion): Builder
    {
        return $query->where('accion', $accion);
    }

    /**
     * Alias en inglés (compatibilidad)
     */
    public function scopeAccion(Builder $query, string $accion): Builder
    {
        return $this->scopePorAccion($query, $accion);
    }

    /**
     * Filtrar por usuario
     *
     * Uso: HistorialCambio::porUsuario(1)->get()
     */
    public function scopePorUsuario(Builder $query, int $usuarioId): Builder
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Alias en inglés (compatibilidad)
     */
    public function scopeUsuario(Builder $query, int $usuarioId): Builder
    {
        return $this->scopePorUsuario($query, $usuarioId);
    }

    /**
     * Filtrar por rango de fechas
     *
     * Uso: HistorialCambio::porFecha('2024-01-01', '2024-12-31')->get()
     */
    public function scopePorFecha(Builder $query, string $fechaInicio, string $fechaFin): Builder
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Alias en inglés (compatibilidad)
     */
    public function scopeFecha(Builder $query, string $fechaInicio, string $fechaFin): Builder
    {
        return $this->scopePorFecha($query, $fechaInicio, $fechaFin);
    }

    /**
     * Filtrar por institución (a través del horario)
     *
     * Uso: HistorialCambio::porInstitucion('colegio')->get()
     */
    public function scopePorInstitucion(Builder $query, string $institucion): Builder
    {
        return $query->whereHas('horario', function (Builder $q) use ($institucion) {
            $q->where('institucion', $institucion);
        });
    }

    /**
     * Alias en inglés (compatibilidad)
     */
    public function scopeInstitucion(Builder $query, string $institucion): Builder
    {
        return $this->scopePorInstitucion($query, $institucion);
    }

    /**
     * Filtrar por profesor (a través del horario)
     *
     * Uso: HistorialCambio::porProfesor(3)->get()
     */
    public function scopePorProfesor(Builder $query, int $profesorId): Builder
    {
        return $query->whereHas('horario', function (Builder $q) use ($profesorId) {
            $q->where('profesor_id', $profesorId);
        });
    }

    /**
     * Alias en inglés (compatibilidad)
     */
    public function scopeProfesor(Builder $query, int $profesorId): Builder
    {
        return $this->scopePorProfesor($query, $profesorId);
    }

    /**
     * Filtrar por grado (a través del horario)
     *
     * Uso: HistorialCambio::porGrado(2)->get()
     */
    public function scopePorGrado(Builder $query, int $gradoId): Builder
    {
        return $query->whereHas('horario', function (Builder $q) use ($gradoId) {
            $q->where('grado_id', $gradoId);
        });
    }

    /**
     * Alias en inglés (compatibilidad)
     */
    public function scopeGrado(Builder $query, int $gradoId): Builder
    {
        return $this->scopePorGrado($query, $gradoId);
    }

    /**
     * Filtrar por aula (NUEVO)
     *
     * Uso: HistorialCambio::porAula(1)->get()
     */
    public function scopePorAula(Builder $query, int $aulaId): Builder
    {
        return $query->whereHas('horario', function (Builder $q) use ($aulaId) {
            $q->where('aula_id', $aulaId);
        });
    }

    /**
     * Ordenar por fecha descendente (más recientes primero)
     *
     * Uso: HistorialCambio::recientes()->get()
     */
    public function scopeRecientes(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Ordenar por fecha ascendente (más antiguos primero)
     *
     * Uso: HistorialCambio::antiguos()->get()
     */
    public function scopeAntiguos(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Filtrar solo cambios de creación
     */
    public function scopeCreaciones(Builder $query): Builder
    {
        return $query->where('accion', self::ACCION_CREAR);
    }

    /**
     * Filtrar solo cambios de actualización
     */
    public function scopeActualizaciones(Builder $query): Builder
    {
        return $query->where('accion', self::ACCION_ACTUALIZAR);
    }

    /**
     * Filtrar solo eliminaciones
     */
    public function scopeEliminaciones(Builder $query): Builder
    {
        return $query->where('accion', self::ACCION_ELIMINAR);
    }

    // ============ ACCESSORS ============

    /**
     * Obtener el nombre del usuario (o "Sistema" si es null)
     *
     * Uso: $historial->usuario_nombre
     */
    public function getUsuarioNombreAttribute(): string
    {
        return $this->usuario?->name ?? 'Sistema';
    }

    /**
     * Obtener la acción formateada (con acentos y mayúscula)
     *
     * Uso: $historial->accion_formateada
     */
    public function getAccionFormateadaAttribute(): string
    {
        return match ($this->accion) {
            self::ACCION_CREAR => 'Creación',
            self::ACCION_ACTUALIZAR => 'Actualización',
            self::ACCION_ELIMINAR => 'Eliminación',
            self::ACCION_RESTAURAR => 'Restauración',
            self::ACCION_REVERTIR => 'Reversión',
            default => ucfirst($this->accion),
        };
    }

    /**
     * Alias de accion_formateada (compatibilidad con el original)
     *
     * Uso: $historial->descripcion
     */
    public function getDescripcionAttribute(): string
    {
        return $this->getAccionFormateadaAttribute();
    }

    /**
     * Obtener un resumen del cambio (para mostrar en listados)
     *
     * Uso: $historial->resumen
     */
    public function getResumenAttribute(): string
    {
        return sprintf(
            '%s realizó una %s en el horario #%d',
            $this->usuario_nombre,
            strtolower($this->accion_formateada),
            $this->horario_id
        );
    }

    /**
     * Verificar si el cambio tiene datos anteriores
     *
     * Uso: $historial->tiene_datos_anteriores
     */
    public function getTieneDatosAnterioresAttribute(): bool
    {
        return !empty($this->datos_anteriores);
    }

    /**
     * Verificar si el cambio tiene datos nuevos
     *
     * Uso: $historial->tiene_datos_nuevos
     */
    public function getTieneDatosNuevosAttribute(): bool
    {
        return !empty($this->datos_nuevos);
    }

    /**
     * Obtener los campos que cambiaron (comparando datos_anteriores vs datos_nuevos)
     *
     * Uso: $historial->campos_cambiados
     */
    public function getCamposCambiadosAttribute(): array
    {
        if (!$this->datos_anteriores || !$this->datos_nuevos) {
            return [];
        }

        $anteriores = $this->datos_anteriores;
        $nuevos = $this->datos_nuevos;
        $cambiados = [];

        // Campos en nuevo que son diferentes
        foreach ($nuevos as $campo => $valorNuevo) {
            $valorAnterior = $anteriores[$campo] ?? null;
            if ($valorAnterior !== $valorNuevo) {
                $cambiados[$campo] = [
                    'anterior' => $valorAnterior,
                    'nuevo' => $valorNuevo,
                ];
            }
        }

        return $cambiados;
    }

    // ============ MÉTODOS DE UTILIDAD ============

    /**
     * Verificar si es un cambio de creación
     */
    public function esCreacion(): bool
    {
        return $this->accion === self::ACCION_CREAR;
    }

    /**
     * Verificar si es un cambio de actualización
     */
    public function esActualizacion(): bool
    {
        return $this->accion === self::ACCION_ACTUALIZAR;
    }

    /**
     * Verificar si es una eliminación
     */
    public function esEliminacion(): bool
    {
        return $this->accion === self::ACCION_ELIMINAR;
    }

    /**
     * Verificar si es una restauración
     */
    public function esRestauracion(): bool
    {
        return $this->accion === self::ACCION_RESTAURAR;
    }

    /**
     * Verificar si es una reversión
     */
    public function esReversion(): bool
    {
        return $this->accion === self::ACCION_REVERTIR;
    }

    /**
     * Obtener solo los campos que cambiaron (nombres)
     *
     * Uso: $historial->obtenerNombresCamposCambiados()
     */
    public function obtenerNombresCamposCambiados(): array
    {
        return array_keys($this->campos_cambiados);
    }

    /**
     * Comparar dos valores y formatear la diferencia para mostrar
     */
    public function formatearValor(mixed $valor): string
    {
        if (is_null($valor)) {
            return '(vacío)';
        }

        if (is_bool($valor)) {
            return $valor ? 'Sí' : 'No';
        }

        if (is_array($valor)) {
            return json_encode($valor, JSON_UNESCAPED_UNICODE);
        }

        return (string) $valor;
    }
}
