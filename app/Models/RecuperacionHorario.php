<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecuperacionHorario extends Model
{
    protected $table = 'recuperaciones_horario';

    protected $guarded = ['id'];

    protected $casts = [
        'fecha_original' => 'date:Y-m-d', 'fecha_recuperacion' => 'date:Y-m-d',
        'original' => 'array', 'disponibilidad_excepcional' => 'boolean',
        'jornada_autorizada' => 'boolean',
    ];

    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }
}
