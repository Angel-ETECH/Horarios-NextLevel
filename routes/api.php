<?php
// routes/api.php

use App\Http\Controllers\Api\DisponibilidadController;
use Illuminate\Support\Facades\Route;

// Rutas de Disponibilidad
Route::prefix('disponibilidades')->group(function () {
    // GET
    Route::get('/', [DisponibilidadController::class, 'index']);
    Route::get('/profesor/{profesorId}', [DisponibilidadController::class, 'getByProfesor']);
    Route::get('/bloques/{profesorId}/{dia}', [DisponibilidadController::class, 'getBloquesDisponibles']);
    Route::get('/{id}', [DisponibilidadController::class, 'show']);

    // POST
    Route::post('/', [DisponibilidadController::class, 'store']);
    Route::post('/verificar', [DisponibilidadController::class, 'verificarDisponibilidad']);

    // PUT
    Route::put('/{id}', [DisponibilidadController::class, 'update']);

    // DELETE
    Route::delete('/{id}', [DisponibilidadController::class, 'destroy']);
    Route::delete('/profesor/{profesorId}', [DisponibilidadController::class, 'destroyByProfesor']);
});
