<?php
// app/Models/Curso.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cursos';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'horas_semanales',
        'duracion_minutos',
        'nivel',
        'tipo',
        'color',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'horas_semanales' => 'integer',
        'duracion_minutos' => 'integer',
        'activo' => 'boolean',
    ];

    // ============ RELACIONES ============

    /**
     * Un curso puede ser dictado por muchos profesores
     */
    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'profesor_curso')
                    ->withPivot('grado_id', 'horas_asignadas', 'rol', 'activo')
                    ->withTimestamps();
    }

    /**
     * Un curso tiene muchos horarios
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    /**
     * Un curso puede estar en muchos grados
     */
    public function grados()
    {
        return $this->belongsToMany(Grado::class, 'profesor_curso')
                    ->withPivot('profesor_id', 'horas_asignadas', 'rol', 'activo')
                    ->withTimestamps();
    }

    // ============ MÉTODOS ÚTILES ============

    /**
     * Obtener el nombre completo del curso con código
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->codigo} - {$this->nombre}";
    }

    /**
     * Scope para cursos activos
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
     * Scope para búsqueda
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'LIKE', "%{$termino}%")
                     ->orWhere('codigo', 'LIKE', "%{$termino}%");
    }
}
