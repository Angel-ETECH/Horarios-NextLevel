<?php
// app/Models/Horario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Horario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'horarios';

    protected $fillable = [
        'profesor_id',
        'curso_id',
        'aula_id',
        'grado_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'turno',
        'institucion',
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
    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    public function historial()
    {
        return $this->hasMany(HistorialCambio::class);
    }

    // ============ SCOPES ============
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePeriodo($query, $periodo)
    {
        return $query->where('periodo_academico', $periodo);
    }

    public function scopeDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    public function scopeProfesor($query, $profesorId)
    {
        return $query->where('profesor_id', $profesorId);
    }

    public function scopeAula($query, $aulaId)
    {
        return $query->where('aula_id', $aulaId);
    }

    public function scopeGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }

    public function scopePorInstitucion($query, $institucion)
    {
        return $query->where('institucion', $institucion);
    }
}
