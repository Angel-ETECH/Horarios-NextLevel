<?php
// app/Models/Profesor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profesor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'profesores';

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
        'estado',
        'carga_horaria_actual',
        'observaciones'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'carga_horaria_maxima' => 'integer',
        'carga_horaria_actual' => 'integer',
    ];

    // ============ RELACIONES ============

    /**
     * Un profesor tiene muchas disponibilidades
     */
    public function disponibilidades()
    {
        return $this->hasMany(DisponibilidadProfesor::class);
    }

    /**
     * Un profesor puede dictar muchos cursos (a través de profesor_curso)
     */
    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'profesor_curso')
                    ->withPivot('grado_id', 'horas_asignadas', 'rol', 'activo')
                    ->withTimestamps();
    }

    /**
     * Un profesor tiene muchos horarios
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    // ============ MÉTODOS ÚTILES ============

    /**
     * Obtener el nombre completo del profesor
     */
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}";
    }

    /**
     * Verificar si el profesor está disponible en un día y hora específicos
     */
    public function estaDisponible($dia, $hora)
    {
        return $this->disponibilidades()
                    ->where('dia_semana', $dia)
                    ->where('hora_inicio', '<=', $hora)
                    ->where('hora_fin', '>=', $hora)
                    ->where('tipo', 'disponible')
                    ->exists();
    }

    /**
     * Calcular carga horaria actual
     */
    public function calcularCargaHoraria()
    {
        $totalHoras = $this->horarios()
                          ->where('estado', 'activo')
                          ->sum(\DB::raw('TIMESTAMPDIFF(HOUR, hora_inicio, hora_fin)'));

        $this->carga_horaria_actual = $totalHoras;
        $this->save();

        return $totalHoras;
    }

    /**
     * Verificar si excede la carga máxima
     */
    public function excedeCargaMaxima($nuevasHoras = 0)
    {
        $total = $this->carga_horaria_actual + $nuevasHoras;
        return $total > $this->carga_horaria_maxima;
    }

    /**
     * Obtener horarios por día
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
     * Scope para profesores activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope para profesores por especialidad
     */
    public function scopeEspecialidad($query, $especialidad)
    {
        return $query->where('especialidad', $especialidad);
    }

    /**
     * Scope para búsqueda por nombre
     */
    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'LIKE', "%{$termino}%")
                     ->orWhere('apellido_paterno', 'LIKE', "%{$termino}%")
                     ->orWhere('apellido_materno', 'LIKE', "%{$termino}%")
                     ->orWhere('email', 'LIKE', "%{$termino}%");
    }
}
