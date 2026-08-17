<?php
// app/Models/Grado.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grado extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'nivel',
        'grado',
        'seccion',
        'nombre_completo',
        'capacidad_maxima',
        'numero_estudiantes',
        'año_academico',
        'turno',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'capacidad_maxima' => 'integer',
        'numero_estudiantes' => 'integer',
        'año_academico' => 'integer',
        'activo' => 'boolean',
    ];

    // ============ RELACIONES ============

    /**
     * Un grado tiene muchos estudiantes
     */
    public function alumnos()
    {
        return $this->hasMany(Alumno::class);
    }

    /**
     * Un grado tiene muchos horarios
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Un grado tiene muchos cursos (a través de profesor_curso)
     */
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'profesor_curso')
                    ->withPivot('profesor_id', 'horas_asignadas', 'rol', 'activo')
                    ->withTimestamps();
    }

    /**
     * Un grado tiene muchos profesores (a través de profesor_curso)
     */
    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'profesor_curso')
                    ->withPivot('curso_id', 'horas_asignadas', 'rol', 'activo')
                    ->withTimestamps();
    }

    // ============ MÉTODOS ÚTILES ============

    /**
     * Obtener el nombre completo del grado
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nivel} - {$this->grado} {$this->seccion}";
    }

    /**
     * Obtener horarios del grado por día
     */
    public function horariosPorDia($dia)
    {
        return $this->horarios()
                    ->where('dia_semana', $dia)
                    ->where('estado', 'activo')
                    ->orderBy('hora_inicio')
                    ->get();
    }

    /**
     * Verificar si hay cupo disponible
     */
    public function hayCupo()
    {
        return $this->numero_estudiantes < $this->capacidad_maxima;
    }

    /**
     * Scope para grados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope por nivel
     */
    public function scopeNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Scope por año académico
     */
    public function scopeAnioAcademico($query, $anio)
    {
        return $query->where('año_academico', $anio);
    }
}
