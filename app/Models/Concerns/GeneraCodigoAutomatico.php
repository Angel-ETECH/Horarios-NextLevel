<?php

namespace App\Models\Concerns;

use App\Services\CodigoSecuencialService;

trait GeneraCodigoAutomatico
{
    protected static function bootGeneraCodigoAutomatico(): void
    {
        static::creating(function ($modelo) {
            if (filled($modelo->codigo)) {
                return;
            }

            $modelo->codigo = app(CodigoSecuencialService::class)->siguiente($modelo->getTable(), true);
        });

        static::updating(function ($modelo) {
            if ($modelo->isDirty('codigo')) {
                $modelo->codigo = $modelo->getOriginal('codigo');
            }
        });
    }
}
