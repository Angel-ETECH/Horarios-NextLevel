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

    protected $appends = [
        'puede_revertirse',
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
        return $query->where(function (Builder $q) use ($horarioId) {
            $q->where('horario_id', $horarioId)
              ->orWhere('datos_anteriores->id', $horarioId)
              ->orWhere('datos_nuevos->id', $horarioId);
        });
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
        return $query->where(function (Builder $q) use ($institucion) {
            $q->whereHas('horario', function (Builder $sub) use ($institucion) {
                $sub->where('institucion', $institucion);
            })->orWhere('datos_anteriores->institucion', $institucion)
              ->orWhere('datos_nuevos->institucion', $institucion);
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
        return $query->where(function (Builder $q) use ($profesorId) {
            $q->whereHas('horario', function (Builder $sub) use ($profesorId) {
                $sub->where('profesor_id', $profesorId);
            })->orWhere('datos_anteriores->profesor_id', $profesorId)
              ->orWhere('datos_nuevos->profesor_id', $profesorId);
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
        return $query->where(function (Builder $q) use ($gradoId) {
            $q->whereHas('horario', function (Builder $sub) use ($gradoId) {
                $sub->where('grado_id', $gradoId);
            })->orWhere('datos_anteriores->grado_id', $gradoId)
              ->orWhere('datos_nuevos->grado_id', $gradoId);
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
        return $query->where(function (Builder $q) use ($aulaId) {
            $q->whereHas('horario', function (Builder $sub) use ($aulaId) {
                $sub->where('aula_id', $aulaId);
            })->orWhere('datos_anteriores->aula_id', $aulaId)
              ->orWhere('datos_nuevos->aula_id', $aulaId);
        });
    }

    public function scopePorCurso(Builder $query, int $cursoId): Builder
    {
        return $query->where(function (Builder $q) use ($cursoId) {
            $q->whereHas('horario', function (Builder $sub) use ($cursoId) {
                $sub->where('curso_id', $cursoId);
            })->orWhere('datos_anteriores->curso_id', $cursoId)
              ->orWhere('datos_nuevos->curso_id', $cursoId);
        });
    }

    /**
     * Ordenar por fecha descendente (más recientes primero)
     *
     * Uso: HistorialCambio::recientes()->get()
     */
    public function scopeRecientes(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc')->orderBy('id', 'desc');
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

    /**
     * Indica si el registro se puede revertir desde el historial.
     * Lo consume el frontend para mostrar u ocultar el botón.
     */
    public function getPuedeRevertirseAttribute(): bool
    {
        if (($this->datos_nuevos['solo_auditoria'] ?? $this->datos_anteriores['solo_auditoria'] ?? false) === true) {
            return false;
        }
        if (!in_array($this->accion, [self::ACCION_CREAR, self::ACCION_ACTUALIZAR, self::ACCION_ELIMINAR], true)) {
            return false;
        }

        $modulo = $this->datos_nuevos['modulo']
            ?? $this->datos_anteriores['modulo']
            ?? null;

        $snapshot = $this->datos_anteriores ?? $this->datos_nuevos;
        if ($modulo === 'configuraciones') {
            return false;
        }
        $esHorario = $modulo === 'horarios' || $this->horario_id !== null
            || ($modulo === null && isset($snapshot['profesor_id'], $snapshot['curso_id'],
                $snapshot['grado_id'], $snapshot['dia_semana'], $snapshot['hora_inicio']));
        if ($esHorario) {
            return $this->horario_id !== null || isset($snapshot['id'], $snapshot['aula_id'],
                $snapshot['periodo_academico'], $snapshot['institucion'], $snapshot['turno']);
        }

        if ($this->accion === self::ACCION_ACTUALIZAR) {
            return !empty($this->datos_anteriores);
        }

        // De los demás módulos solo se revierten las eliminaciones.
        return $this->accion === self::ACCION_ELIMINAR;
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

    public const CLAVES_IGNORADAS_FALLBACK = [
        'id', 'created_at', 'updated_at', 'deleted_at', 'deleted',
        'password', 'remember_token', 'token', 'email_verified_at',
        'two_factor_secret', 'two_factor_recovery_codes', 'modulo',
        'tipo_registro', 'solo_auditoria'
    ];

    /**
     * Comparar dos valores y formatear la diferencia para mostrar
     */
    public function formatearValor(mixed $valor, ?string $campo = null): string
    {
        if (is_null($valor) || $valor === '') {
            return '(vacío)';
        }

        if (is_bool($valor)) {
            return $valor ? 'Sí' : 'No';
        }

        if (is_string($valor)) {
            $trimmed = trim($valor);
            if ((str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}'))
                || (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']'))) {
                $decoded = json_decode($trimmed, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $valor = $decoded;
                }
            }
        }

        if (is_array($valor)) {
            return $this->formatearEstructura($valor, $campo);
        }

        return (string) $valor;
    }

    /**
     * Formatear estructuras compuestas (arrays asociativos o listas).
     */
    public function formatearEstructura(array $datos, ?string $campo = null, int $nivel = 0): string
    {
        if (empty($datos)) {
            return '(vacío)';
        }

        if ($nivel > 3) {
            return '...';
        }

        if (array_is_list($datos)) {
            $items = [];
            foreach ($datos as $item) {
                if ($item === null || $item === '') {
                    continue;
                }
                if (is_array($item)) {
                    $ent = $this->formatearEntidadConocida($item);
                    $items[] = $ent ?? $this->formatearEstructura($item, null, $nivel + 1);
                } else {
                    $items[] = (string) $item;
                }
            }
            return !empty($items) ? implode(', ', $items) : '(vacío)';
        }

        $entidad = $this->formatearEntidadConocida($datos, $campo);
        if ($entidad !== null) {
            return $entidad;
        }

        $pares = [];
        foreach ($datos as $clave => $val) {
            if (in_array($clave, self::CLAVES_IGNORADAS_FALLBACK, true)) {
                continue;
            }
            if ($val === null || $val === '') {
                continue;
            }

            $etiqueta = $this->formatearNombreCampo($clave);

            if (is_bool($val)) {
                $valTexto = $val ? 'Sí' : 'No';
            } elseif (is_array($val)) {
                $entVal = $this->formatearEntidadConocida($val, $clave);
                $valTexto = $entVal ?? $this->formatearEstructura($val, $clave, $nivel + 1);
            } else {
                $valTexto = (string) $val;
            }

            if ($valTexto !== '' && $valTexto !== '(vacío)') {
                $pares[] = "{$etiqueta}: {$valTexto}";
            }
        }

        if (!empty($pares)) {
            return implode(', ', $pares);
        }

        if (isset($datos['id'])) {
            return "#{$datos['id']}";
        }

        return '(vacío)';
    }

    public function formatearEntidadConocida(array $obj, ?string $campo = null): ?string
    {
        $c = strtolower((string)$campo);

        if ($c === 'profesor' || $c === 'profesor_id' || str_contains($c, 'profesor')
            || isset($obj['apellido_paterno']) || isset($obj['apellido_materno'])
            || isset($obj['carga_horaria_maxima']) || isset($obj['carga_horaria_actual'])
            || (isset($obj['codigo']) && str_starts_with((string)$obj['codigo'], 'DOC-'))) {
            return $this->formatearProfesor($obj);
        }

        if ($c === 'curso' || $c === 'curso_id' || str_contains($c, 'curso')
            || isset($obj['duracion_minutos']) || isset($obj['horas_semanales'])) {
            return $this->formatearCurso($obj);
        }

        if ($c === 'aula' || $c === 'aula_id' || str_contains($c, 'aula')
            || isset($obj['equipamiento']) || (isset($obj['capacidad']) && (isset($obj['piso']) || isset($obj['edificio'])))) {
            return $this->formatearAula($obj);
        }

        if ($c === 'grado' || $c === 'grado_id' || str_contains($c, 'grado')
            || isset($obj['seccion']) || isset($obj['numero_estudiantes']) || isset($obj['año_academico'])) {
            return $this->formatearGrado($obj);
        }

        $titulo = trim((string)($obj['titulo'] ?? $obj['title'] ?? ''));
        $nombre = trim((string)($obj['nombre_completo'] ?? $obj['nombre'] ?? $obj['name'] ?? ''));
        $codigo = trim((string)($obj['codigo'] ?? $obj['code'] ?? ''));

        $textoPrincipal = $titulo !== '' ? $titulo : $nombre;
        if ($textoPrincipal !== '' && $codigo !== '' && !str_contains($textoPrincipal, $codigo)) {
            return "{$textoPrincipal} ({$codigo})";
        }
        if ($textoPrincipal !== '') {
            return $textoPrincipal;
        }
        if ($codigo !== '') {
            return $codigo;
        }

        return null;
    }

    public function formatearProfesor(array $obj): string
    {
        $partes = array_filter([
            trim((string)($obj['nombre'] ?? '')),
            trim((string)($obj['apellido_paterno'] ?? '')),
            trim((string)($obj['apellido_materno'] ?? '')),
        ], fn ($p) => $p !== '');

        $nombre = implode(' ', $partes);
        if ($nombre === '') {
            $nombre = trim((string)($obj['nombre_completo'] ?? $obj['name'] ?? ''));
        }
        if ($nombre === '' && isset($obj['id'])) {
            $nombre = "Profesor #{$obj['id']}";
        }

        $codigo = trim((string)($obj['codigo'] ?? ''));
        if ($nombre !== '' && $codigo !== '') {
            return str_contains($nombre, "({$codigo})") ? $nombre : "{$nombre} ({$codigo})";
        }
        if ($nombre !== '') {
            return $nombre;
        }
        if ($codigo !== '') {
            return "Profesor ({$codigo})";
        }

        return '(vacío)';
    }

    public function formatearCurso(array $obj): string
    {
        $nombre = trim((string)($obj['nombre'] ?? $obj['nombre_completo'] ?? $obj['name'] ?? ''));
        $codigo = trim((string)($obj['codigo'] ?? ''));

        if ($nombre !== '' && $codigo !== '' && !str_contains($nombre, $codigo)) {
            return "{$nombre} ({$codigo})";
        }

        return $nombre !== '' ? $nombre : ($codigo !== '' ? $codigo : (isset($obj['id']) ? "Curso #{$obj['id']}" : 'Curso'));
    }

    public function formatearAula(array $obj): string
    {
        $nombre = trim((string)($obj['nombre'] ?? $obj['name'] ?? ''));
        $codigo = trim((string)($obj['codigo'] ?? ''));

        if ($nombre !== '' && $codigo !== '' && $nombre !== $codigo && !str_contains($nombre, $codigo)) {
            return "{$nombre} ({$codigo})";
        }

        return $nombre !== '' ? $nombre : ($codigo !== '' ? $codigo : (isset($obj['id']) ? "Aula #{$obj['id']}" : 'Aula'));
    }

    public function formatearGrado(array $obj): string
    {
        $codigo = trim((string)($obj['codigo'] ?? ''));

        if (!empty($obj['nombre_completo'])) {
            $base = trim((string)$obj['nombre_completo']);
            if ($codigo !== '' && !str_contains($base, $codigo)) {
                return "{$base} ({$codigo})";
            }
            return $base;
        }

        $partes = array_filter([
            trim((string)($obj['nivel'] ?? '')),
            trim((string)($obj['grado'] ?? '')),
            !empty($obj['seccion']) ? 'Sección ' . trim((string)$obj['seccion']) : '',
        ], fn ($p) => $p !== '');

        if (!empty($partes)) {
            $base = implode(' ', $partes);
            return ($codigo !== '' && !str_contains($base, $codigo)) ? "{$base} ({$codigo})" : $base;
        }

        $nombre = trim((string)($obj['nombre'] ?? ''));
        if ($nombre !== '' && $codigo !== '' && !str_contains($nombre, $codigo)) {
            return "{$nombre} ({$codigo})";
        }

        return $nombre !== '' ? $nombre : ($codigo !== '' ? $codigo : (isset($obj['id']) ? "Grado #{$obj['id']}" : 'Grado'));
    }

    public function formatearNombreCampo(string $campo): string
    {
        $nombres = [
            'profesor' => 'Profesor',
            'profesor_id' => 'Profesor',
            'curso' => 'Curso',
            'curso_id' => 'Curso',
            'aula' => 'Aula',
            'aula_id' => 'Aula',
            'grado' => 'Grado',
            'grado_id' => 'Grado',
            'dia_semana' => 'Día',
            'hora_inicio' => 'Hora de inicio',
            'hora_fin' => 'Hora de fin',
            'turno' => 'Turno',
            'institucion' => 'Institución',
            'tipo' => 'Tipo',
            'semana' => 'Semana',
            'periodo_academico' => 'Periodo académico',
            'estado' => 'Estado',
            'version' => 'Versión',
            'creado_por' => 'Creado por',
            'modificado_por' => 'Modificado por',
            'observacion' => 'Observación',
            'profesor_nombre' => 'Nombre de profesor',
            'curso_nombre' => 'Nombre de curso',
            'aula_nombre' => 'Nombre de aula',
            'grado_nombre' => 'Nombre de grado',
            'original' => 'Horario original',
        ];

        return $nombres[$campo]
            ?? ucwords(str_replace('_', ' ', $campo));
    }
}
