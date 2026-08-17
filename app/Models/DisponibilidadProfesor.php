<?php
// app/Models/DisponibilidadProfesor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DisponibilidadProfesor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'disponibilidad_profesor';

    protected $fillable = [
        'profesor_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'tipo',
        'turno',
        'observacion'
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    // ============ RELACIONES ============

    /**
     * Una disponibilidad pertenece a un profesor
     */
    public function profesor()
    {
        return $this->belongsTo(Profesor::class);
    }

    // ============ MÉTODOS ÚTILES ============

    /**
     * Verificar si una hora está dentro del bloque de disponibilidad
     */
    public function contieneHora($hora)
    {
        return $this->hora_inicio <= $hora && $hora <= $this->hora_fin;
    }

    /**
     * Obtener duración en horas
     */
    public function getDuracionHorasAttribute()
    {
        return $this->hora_inicio->diffInHours($this->hora_fin);
    }

    /**
     * Scope para disponibilidades activas (tipo disponible)
     */
    public function scopeDisponibles($query)
    {
        return $query->where('tipo', 'disponible');
    }

    /**
     * Scope por día
     */
    public function scopeDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    /**
     * Scope por turno
     */
    public function scopeTurno($query, $turno)
    {
        return $query->where('turno', $turno);
    }
}
