<?php
// app/Models/HistorialCambio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialCambio extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios';

    protected $fillable = [
        'horario_id',
        'usuario_id',
        'accion',
        'datos_anteriores',
        'datos_nuevos',
        'motivo',
        'ip_usuario',
        'user_agent'
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    // ============ RELACIONES ============

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    // ============ SCOPES ============

    public function scopePorHorario($query, $horarioId)
    {
        return $query->where('horario_id', $horarioId);
    }

    public function scopePorAccion($query, $accion)
    {
        return $query->where('accion', $accion);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    // ============ MÉTODOS ============

    public function getDescripcionAttribute()
    {
        $acciones = [
            'crear' => 'Creación',
            'actualizar' => 'Actualización',
            'eliminar' => 'Eliminación',
            'restaurar' => 'Restauración'
        ];

        return $acciones[$this->accion] ?? $this->accion;
    }
}
