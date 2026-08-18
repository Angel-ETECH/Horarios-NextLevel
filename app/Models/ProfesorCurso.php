<?php
// app/Models/ProfesorCurso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfesorCurso extends Model
{
    use HasFactory;

    protected $table = 'profesor_curso';

    protected $fillable = [
        'profesor_id',
        'curso_id',
        'grado_id',
        'horas_asignadas',
        'rol',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'horas_asignadas' => 'integer',
        'activo' => 'boolean',
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

    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    // ============ SCOPES ============

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorProfesor($query, $profesorId)
    {
        return $query->where('profesor_id', $profesorId);
    }

    public function scopePorCurso($query, $cursoId)
    {
        return $query->where('curso_id', $cursoId);
    }

    public function scopePorGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }

    public function scopeTitulares($query)
    {
        return $query->where('rol', 'titular');
    }
}
