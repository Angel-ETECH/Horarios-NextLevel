<?php
// app/Models/BloqueHorario.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BloqueHorario extends Model
{
    use HasFactory;

    protected $table = 'bloques_horarios';

    protected $fillable = [
        'configuracion_horario_id',
        'orden',
        'hora_inicio',
        'hora_fin',
        'tipo',
        'nombre',
        'numero_bloque',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'orden' => 'integer',
        'numero_bloque' => 'integer',
    ];

    // ============ RELACIONES ============
    public function configuracion()
    {
        return $this->belongsTo(ConfiguracionHorario::class, 'configuracion_horario_id');
    }

    // ============ SCOPES ============
    public function scopeClases($query)
    {
        return $query->where('tipo', 'clase');
    }

    public function scopeRecesos($query)
    {
        return $query->where('tipo', 'receso');
    }
}
