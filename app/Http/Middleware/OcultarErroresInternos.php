<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class OcultarErroresInternos
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (config('app.debug') || !$response instanceof JsonResponse || $response->getStatusCode() < 500) {
            return $response;
        }

        $datos = $response->getData(true);
        if (!is_array($datos)) {
            return $response;
        }
        Log::error('La API no pudo completar una operacion.', [
            'ruta' => $request->path(), 'estado' => $response->getStatusCode(),
        ]);
        // No enviar SQL, rutas, excepciones ni detalles SMTP al navegador.
        $datos['message'] = 'No se pudo completar la operacion. Intentalo nuevamente o contacta al administrador.';
        if (array_key_exists('error', $datos)) {
            $datos['error'] = null;
        }
        unset($datos['exception'], $datos['trace'], $datos['file'], $datos['line'], $datos['errors'], $datos['data']);
        $response->setData($datos);
        return $response;
    }
}
