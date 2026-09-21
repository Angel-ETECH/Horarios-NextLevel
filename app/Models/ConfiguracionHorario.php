<?php
// app/Models/ConfiguracionHorario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfiguracionHorario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'configuracion_horarios';

    protected $fillable = [
        'institucion',
        'nivel',
        'turno',
        'nombre',
        'hora_inicio',
        'hora_fin',
        'duracion_bloque_minutos',
        'año_academico',
        'activo',
        'observacion',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'duracion_bloque_minutos' => 'integer',
        'año_academico' => 'integer',
        'activo' => 'boolean',
    ];

    // ============ RELACIONES ============
    public function bloques()
    {
        return $this->hasMany(BloqueHorario::class, 'configuracion_horario_id')
                    ->orderBy('orden');
    }

    public function bloquesClase()
    {
        return $this->hasMany(BloqueHorario::class, 'configuracion_horario_id')
                    ->where('tipo', 'clase')
                    ->orderBy('orden');
    }

    public function bloquesReceso()
    {
        return $this->hasMany(BloqueHorario::class, 'configuracion_horario_id')
                    ->where('tipo', 'receso')
                    ->orderBy('orden');
    }

    // ============ SCOPES ============
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorInstitucion($query, $institucion)
    {
        return $query->where('institucion', $institucion);
    }

    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    public function scopePorTurno($query, $turno)
    {
        return $query->where('turno', $turno);
    }

    public function scopeDelAño($query, $año)
    {
        return $query->where('año_academico', $año);
    }
}
