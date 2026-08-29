<?php
// app/Models/Alumno.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Alumno extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'alumnos';

    protected $fillable = [
        'codigo',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'dni',
        'email',
        'telefono',
        'fecha_nacimiento',
        'direccion',
        'genero',
        'grado_id',
        'fecha_ingreso',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso' => 'date',
    ];

    // ============ RELACIONES ============

    public function grado()
    {
        return $this->belongsTo(Grado::class);
    }

    // ============ ACCESORES ============

    public function getNombreCompletoAttribute()
    {
        return trim($this->nombre . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno);
    }

    public function getEdadAttribute()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->age : null;
    }

    // ============ SCOPES ============

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorGrado($query, $gradoId)
    {
        return $query->where('grado_id', $gradoId);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'LIKE', "%{$termino}%")
                     ->orWhere('apellido_paterno', 'LIKE', "%{$termino}%")
                     ->orWhere('apellido_materno', 'LIKE', "%{$termino}%")
                     ->orWhere('dni', 'LIKE', "%{$termino}%")
                     ->orWhere('email', 'LIKE', "%{$termino}%");
    }
}
