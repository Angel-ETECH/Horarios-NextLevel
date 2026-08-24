<?php
// routes/api.php

use App\Http\Controllers\Api\DisponibilidadController;
use App\Http\Controllers\Api\AsignacionController;
use App\Http\Controllers\Api\AulaController;

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

// ============================================
// RUTAS DE ASIGNACIONES
// ============================================
Route::prefix('asignaciones')->group(function () {
    // GET
    Route::get('/', [AsignacionController::class, 'index']);
    Route::get('/profesor/{profesorId}', [AsignacionController::class, 'getByProfesor']);
    Route::get('/curso/{cursoId}', [AsignacionController::class, 'getByCurso']);
    Route::get('/grado/{gradoId}', [AsignacionController::class, 'getByGrado']);
    Route::get('/profesores-disponibles/{cursoId}/{gradoId}', [AsignacionController::class, 'getProfesoresDisponibles']);
    Route::get('/estadisticas', [AsignacionController::class, 'estadisticas']);
    Route::get('/{id}', [AsignacionController::class, 'show']);

    // POST
    Route::post('/', [AsignacionController::class, 'store']);
    Route::post('/{id}/desactivar', [AsignacionController::class, 'desactivar']);
    Route::post('/{id}/activar', [AsignacionController::class, 'activar']);

    // PUT
    Route::put('/{id}', [AsignacionController::class, 'update']);

    // DELETE
    Route::delete('/{id}', [AsignacionController::class, 'destroy']);
});

// ============================================
// RUTAS DE AULAS
// ============================================
Route::prefix('aulas')->group(function () {
    // GET
    Route::get('/', [AulaController::class, 'index']);
    Route::get('/disponibles/{dia}/{horaInicio}/{horaFin}', [AulaController::class, 'getDisponibles']);
    Route::get('/estadisticas', [AulaController::class, 'estadisticas']);
    Route::get('/{id}', [AulaController::class, 'show']);

    // POST
    Route::post('/', [AulaController::class, 'store']);

    // PUT
    Route::put('/{id}', [AulaController::class, 'update']);

    // DELETE
    Route::delete('/{id}', [AulaController::class, 'destroy']);
});
