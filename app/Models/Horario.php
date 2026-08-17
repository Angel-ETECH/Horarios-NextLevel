<?php
// app/Models/Horario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profesor_id',
        'curso_id',
        'aula_id',
        'grado_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'turno',
        'tipo',
        'semana',
        'periodo_academico',
        'estado',
        'version',
        'creado_por',
        'modificado_por',
        'observacion'
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'semana' => 'integer',
        'version' => 'integer',
    ];

    // ============ RELACIONES ============

    /**
     * Un horario pertenece a un profesor
     */
    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    /**
     * Un horario pertenece a un curso
     */
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    /**
     * Un horario pertenece a un aula
     */
    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    /**
     * Un horario pertenece a un grado
     */
    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    /**
     * Un horario tiene muchos registros de historial
     */
    public function historial()
    {
        return $this->hasMany(HistorialCambio::class);
    }

    // ============ MÉTODOS ÚTILES ============

    /**
     * Verificar si hay conflicto con otro horario
     */
    public function hayConflicto($nuevoHorario)
    {
        // Verificar si se solapa con otro horario
        $seSolapa = $this->hora_inicio < $nuevoHorario->hora_fin &&
                    $this->hora_fin > $nuevoHorario->hora_inicio;

        if (!$seSolapa) {
            return false;
        }

        // Verificar conflictos específicos
        $conflictos = [
            'profesor_id' => 'profesor',
            'aula_id' => 'aula',
            'grado_id' => 'grado'
        ];

        foreach ($conflictos as $campo => $nombre) {
            if ($this->$campo == $nuevoHorario->$campo) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtener duración en horas
     */
    public function getDuracionHorasAttribute()
    {
        return $this->hora_inicio->diffInHours($this->hora_fin);
    }

    /**
     * Verificar si el horario está activo
     */
    public function estaActivo()
    {
        return $this->estado === 'activo';
    }

    /**
     * Scope para horarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope por período académico
     */
    public function scopePeriodo($query, $periodo)
    {
        return $query->where('periodo_academico', $periodo);
    }

    /**
     * Scope por día
     */
    public function scopeDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    /**
     * Scope por profesor
     */
    public function scopeProfesor($query, $profesorId)
    {
        return $query->where('profesor_id', $profesorId);
    }

    /**
     * Scope por aula
     */
    public function scopeAula($query, $aulaId)
    {
        return $query->where('aula_id', $aulaId);
    }

    /**
     * Scope por grado
     */
    public function scopeGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }
}
