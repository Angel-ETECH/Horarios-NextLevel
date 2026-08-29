<?php
// app/Models/Aula.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aula extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'aulas';

    protected $fillable = [
        'codigo',
        'nombre',
        'capacidad',
        'tipo',
        'nivel',
        'edificio',
        'piso',
        'equipamiento',
        'activo',
        'observaciones'
    ];

    protected $casts = [
        'capacidad' => 'integer',
        'activo' => 'boolean',
    ];

    // ============ RELACIONES ============

    /**
     * Un aula tiene muchos horarios
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    // ============ MÉTODOS ÚTILES ============

    /**
     * Verificar si el aula está ocupada en un día y hora
     */
    public function estaOcupada($dia, $horaInicio, $horaFin, $excluirId = null)
    {
        $query = $this->horarios()
                      ->where('dia_semana', $dia)
                      ->where('estado', 'activo')
                      ->where(function($q) use ($horaInicio, $horaFin) {
                          $q->whereBetween('hora_inicio', [$horaInicio, $horaFin])
                            ->orWhereBetween('hora_fin', [$horaInicio, $horaFin])
                            ->orWhere(function($sub) use ($horaInicio, $horaFin) {
                                $sub->where('hora_inicio', '<=', $horaInicio)
                                    ->where('hora_fin', '>=', $horaFin);
                            });
                      });

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        return $query->exists();
    }

    /**
     * Verificar si el aula tiene capacidad suficiente
     */
    public function tieneCapacidad($cantidadEstudiantes)
    {
        return $this->capacidad >= $cantidadEstudiantes;
    }

    /**
     * Scope para aulas disponibles para un nivel específico
     */
    public function scopeParaNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel)
                     ->orWhere('nivel', 'todos');
    }

    /**
     * Scope para aulas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope por tipo de aula
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
}
