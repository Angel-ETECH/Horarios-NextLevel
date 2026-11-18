<?php

namespace App\Http\Requests\Concerns;

trait CodigoGeneradoPorServidor
{
    protected function prepareForValidation(): void
    {
        $this->offsetUnset('codigo');
    }
}
