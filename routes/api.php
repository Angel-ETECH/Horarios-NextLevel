<?php
// routes/api.php

use App\Http\Controllers\Api\DisponibilidadController;
use App\Http\Controllers\Api\AsignacionController;
use App\Http\Controllers\Api\AulaController;
use App\Http\Controllers\Api\HorarioGeneratorController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\HistorialController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ProfesorController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\GradoController;

use Illuminate\Support\Facades\Route;

// ============================================
// RUTAS DE PROFESORES
// ============================================
Route::prefix('profesores')->group(function () {
    Route::get('/', [ProfesorController::class, 'index']);
    Route::get('/estadisticas', [ProfesorController::class, 'estadisticas']);
    Route::get('/{id}', [ProfesorController::class, 'show']);
    Route::post('/', [ProfesorController::class, 'store']);
    Route::put('/{id}', [ProfesorController::class, 'update']);
    Route::delete('/{id}', [ProfesorController::class, 'destroy']);
    Route::post('/{id}/restore', [ProfesorController::class, 'restore']);
});

// ============================================
// RUTAS DE CURSOS
// ============================================
Route::prefix('cursos')->group(function () {
    Route::get('/', [CursoController::class, 'index']);
    Route::get('/estadisticas', [CursoController::class, 'estadisticas']);
    Route::get('/{id}', [CursoController::class, 'show']);
    Route::post('/', [CursoController::class, 'store']);
    Route::put('/{id}', [CursoController::class, 'update']);
    Route::delete('/{id}', [CursoController::class, 'destroy']);
    Route::post('/{id}/restore', [CursoController::class, 'restore']);
});

// ============================================
// RUTAS DE GRADOS
// ============================================
Route::prefix('grados')->group(function () {
    Route::get('/', [GradoController::class, 'index']);
    Route::get('/estadisticas', [GradoController::class, 'estadisticas']);
    Route::get('/{id}', [GradoController::class, 'show']);
    Route::post('/', [GradoController::class, 'store']);
    Route::put('/{id}', [GradoController::class, 'update']);
    Route::delete('/{id}', [GradoController::class, 'destroy']);
    Route::post('/{id}/restore', [GradoController::class, 'restore']);
});

// ============================================
// RUTAS DE DISPONIBILIDADES
// ============================================
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

// ============================================
// RUTAS DE GENERACIÓN DE HORARIOS
// ============================================
Route::prefix('horarios')->group(function () {
    // POST - Generar horarios
    Route::post('/generar', [HorarioGeneratorController::class, 'generar']);
    Route::post('/generar/profesor/{profesorId}', [HorarioGeneratorController::class, 'generarPorProfesor']);

    // GET - Estadísticas
    Route::get('/estadisticas', [HorarioGeneratorController::class, 'estadisticas']);

    // DELETE - Limpiar horarios
    Route::delete('/limpiar', [HorarioGeneratorController::class, 'limpiar']);
});

// ============================================
// RUTAS DE HORARIOS (Vista y Búsqueda)
// ============================================
Route::prefix('horarios')->group(function () {
    // GET - Listar y buscar
    Route::get('/', [HorarioController::class, 'index']);
    Route::get('/profesor/{profesorId}', [HorarioController::class, 'getByProfesor']);
    Route::get('/grado/{gradoId}', [HorarioController::class, 'getByGrado']);
    Route::get('/dia/{dia}', [HorarioController::class, 'getByDia']);
    Route::get('/{id}', [HorarioController::class, 'show']);

    // PUT - Actualizar (con historial)
    Route::put('/{id}', [HorarioController::class, 'update']);

    // DELETE - Eliminar (con historial)
    Route::delete('/{id}', [HorarioController::class, 'destroy']);
});

// ============================================
// RUTAS DE HISTORIAL Y CONTROL DE CAMBIOS
// ============================================
Route::prefix('historial')->group(function () {
    // GET - Listar historial
    Route::get('/', [HistorialController::class, 'index']);
    Route::get('/horario/{horarioId}', [HistorialController::class, 'getByHorario']);
    Route::get('/versiones/{horarioId}', [HistorialController::class, 'getVersiones']);
    Route::get('/estadisticas', [HistorialController::class, 'estadisticas']);
    Route::get('/{id}', [HistorialController::class, 'show']);

    // POST - Revertir cambios
    Route::post('/revertir/{historialId}', [HistorialController::class, 'revertir']);
});

// ============================================
// RUTAS DE EXPORTACIÓN
// ============================================
Route::prefix('exportar')->group(function () {
    // Excel
    Route::post('/excel', [ExportController::class, 'exportExcel']);
    Route::get('/excel/download', [ExportController::class, 'downloadExcel']);

    // PDF
    Route::post('/pdf', [ExportController::class, 'exportPdf']);
    Route::get('/pdf/download', [ExportController::class, 'downloadPdf']);

    // Imagen (HTML para captura)
    Route::get('/imagen/html', [ExportController::class, 'getHtmlForImage']);

    // CSV
    Route::post('/csv', [ExportController::class, 'exportCsv']);
});
