<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profesor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'codigo',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'telefono',
        'dni',
        'sexo',
        'fecha_nacimiento',
        'especialidad',
        'carga_horaria_maxima',
        'carga_horaria_actual',
        'estado',
        'institucion',
        'observaciones'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'carga_horaria_maxima' => 'integer',
        'carga_horaria_actual' => 'integer'
    ];

    // Relaciones
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'profesor_curso')
                    ->withPivot('grado_id', 'horas_asignadas', 'rol', 'activo', 'institucion')
                    ->withTimestamps();
    }

    public function disponibilidades()
    {
        return $this->hasMany(DisponibilidadProfesor::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorInstitucion($query, $institucion)
    {
        return $query->whereIn('institucion', [$institucion, 'ambos']);
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }
}
