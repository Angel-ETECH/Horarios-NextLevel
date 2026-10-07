<?php

// routes/api.php

use App\Http\Controllers\Api\AsignacionController;
use App\Http\Controllers\Api\AulaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConfiguracionHorarioController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\DisponibilidadController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\GradoController;
use App\Http\Controllers\Api\HistorialController;
use App\Http\Controllers\Api\HorarioController;
use App\Http\Controllers\Api\HorarioGeneratorController;
use App\Http\Controllers\Api\ProfesorController;
use App\Http\Controllers\Api\PublicController;
use Illuminate\Support\Facades\Route;

// ============================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================

Route::post('/login', [
    AuthController::class,
    'login'
]);

Route::post('/register', [
    AuthController::class,
    'register'
]);

Route::post('/forgot-password', [
    AuthController::class,
    'forgotPassword'
]);

Route::post('/reset-password', [
    AuthController::class,
    'resetPassword'
]);

// ============================================
// CONSULTA PÚBLICA
// ============================================

Route::prefix('publico')->group(function () {
    Route::get('/instituciones', [
        PublicController::class,
        'instituciones'
    ]);

    Route::get('/profesores', [
        PublicController::class,
        'listarProfesores'
    ]);

    Route::get('/grados', [
        PublicController::class,
        'listarGrados'
    ]);

    Route::get('/aulas', [
        PublicController::class,
        'listarAulas'
    ]);

    Route::get('/cursos', [
        PublicController::class,
        'listarCursos'
    ]);

    Route::get('/horario/profesor/{id}', [
        PublicController::class,
        'horarioProfesor'
    ]);

    Route::get('/horario/profesor/{id}/pdf', [
        PublicController::class,
        'descargarPdfProfesor'
    ]);

    Route::get('/horario/profesor/{id}/imagen', [
        PublicController::class,
        'imagenProfesor'
    ]);

    Route::get('/horario/grado/{id}', [
        PublicController::class,
        'horarioGrado'
    ]);

    Route::get('/horario/aula/{id}', [
        PublicController::class,
        'horarioAula'
    ]);

    Route::get('/horario/curso/{id}', [
        PublicController::class,
        'horarioCurso'
    ]);
});

// ============================================
// RUTAS PROTEGIDAS
// ============================================

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [
            AuthController::class,
            'logout'
        ]);

        Route::get('/user', [
            AuthController::class,
            'user'
        ]);
    });

    Route::prefix('profesores')->group(function () {
        Route::get('/', [ProfesorController::class, 'index']);
        Route::get('/estadisticas', [ProfesorController::class, 'estadisticas']);
        Route::get('/{id}', [ProfesorController::class, 'show']);
        Route::post('/', [ProfesorController::class, 'store']);
        Route::put('/{id}', [ProfesorController::class, 'update']);
        Route::delete('/{id}', [ProfesorController::class, 'destroy']);
        Route::post('/{id}/restore', [ProfesorController::class, 'restore']);
    });

    Route::prefix('cursos')->group(function () {
        Route::get('/', [CursoController::class, 'index']);
        Route::get('/estadisticas', [CursoController::class, 'estadisticas']);
        Route::get('/{id}', [CursoController::class, 'show']);
        Route::post('/', [CursoController::class, 'store']);
        Route::put('/{id}', [CursoController::class, 'update']);
        Route::delete('/{id}', [CursoController::class, 'destroy']);
        Route::post('/{id}/restore', [CursoController::class, 'restore']);
    });

    Route::prefix('grados')->group(function () {
        Route::get('/', [GradoController::class, 'index']);
        Route::get('/estadisticas', [GradoController::class, 'estadisticas']);
        Route::get('/{id}', [GradoController::class, 'show']);
        Route::post('/', [GradoController::class, 'store']);
        Route::put('/{id}', [GradoController::class, 'update']);
        Route::delete('/{id}', [GradoController::class, 'destroy']);
        Route::post('/{id}/restore', [GradoController::class, 'restore']);
    });

    Route::prefix('disponibilidades')->group(function () {
        Route::get('/', [DisponibilidadController::class, 'index']);
        Route::get('/estadisticas', [DisponibilidadController::class, 'getEstadisticas']);
        Route::get('/agrupadas/{profesorId}', [DisponibilidadController::class, 'getAgrupadasPorDia']);
        Route::get('/profesor/{profesorId}', [DisponibilidadController::class, 'getByProfesor']);
        Route::get('/profesor/{profesorId}/institucion/{institucion}', [DisponibilidadController::class, 'getByProfesorEInstitucion']);
        Route::get('/bloques/{profesorId}/{dia}', [DisponibilidadController::class, 'getBloquesDisponibles']);
        Route::post('/', [DisponibilidadController::class, 'store']);
        Route::post('/verificar', [DisponibilidadController::class, 'verificarDisponibilidad']);
        Route::post('/verificar-rango', [DisponibilidadController::class, 'verificarRango']);
        Route::put('/{id}', [DisponibilidadController::class, 'update']);
        Route::delete('/profesor/{profesorId}/institucion/{institucion}', [DisponibilidadController::class, 'destroyByInstitucion']);
        Route::delete('/profesor/{profesorId}', [DisponibilidadController::class, 'destroyByProfesor']);
        Route::delete('/{id}', [DisponibilidadController::class, 'destroy']);
        Route::get('/{id}', [DisponibilidadController::class, 'show']);
    });

    Route::prefix('asignaciones')->group(function () {
        Route::get('/', [AsignacionController::class, 'index']);
        Route::get('/profesor/{profesorId}', [AsignacionController::class, 'getByProfesor']);
        Route::get('/curso/{cursoId}', [AsignacionController::class, 'getByCurso']);
        Route::get('/grado/{gradoId}', [AsignacionController::class, 'getByGrado']);
        Route::get('/profesores-disponibles/{cursoId}/{gradoId}', [AsignacionController::class, 'getProfesoresDisponibles']);
        Route::get('/estadisticas', [AsignacionController::class, 'estadisticas']);
        Route::get('/{id}', [AsignacionController::class, 'show']);
        Route::post('/', [AsignacionController::class, 'store']);
        Route::post('/{id}/desactivar', [AsignacionController::class, 'desactivar']);
        Route::post('/{id}/activar', [AsignacionController::class, 'activar']);
        Route::put('/{id}', [AsignacionController::class, 'update']);
        Route::delete('/{id}/permanente', [AsignacionController::class, 'destroyPermanent']);
        Route::delete('/{id}', [AsignacionController::class, 'destroy']);
    });

    Route::prefix('aulas')->group(function () {
        Route::get('/', [AulaController::class, 'index']);
        Route::get('/nivel/{nivel}', [AulaController::class, 'getByNivel']);
        Route::get('/estadisticas', [AulaController::class, 'estadisticas']);
        Route::get('/disponibilidad/disponibles', [AulaController::class, 'getDisponibles']);
        Route::get('/disponibilidad/verificar', [AulaController::class, 'verificarDisponibilidad']);
        Route::get('/disponibles/{dia}/{horaInicio}/{horaFin}', [AulaController::class, 'getDisponiblesPorRuta']);
        Route::post('/', [AulaController::class, 'store']);
        Route::get('/{id}', [AulaController::class, 'show']);
        Route::put('/{id}', [AulaController::class, 'update']);
        Route::delete('/{id}', [AulaController::class, 'destroy']);
    });

    Route::prefix('configuraciones-horario')->group(function () {
        Route::get('/vigente', [ConfiguracionHorarioController::class, 'vigente']);
        Route::get('/recesos', [ConfiguracionHorarioController::class, 'recesos']);
        Route::get('/', [ConfiguracionHorarioController::class, 'index']);
        Route::get('/{id}', [ConfiguracionHorarioController::class, 'show']);
        Route::post('/', [ConfiguracionHorarioController::class, 'store']);
        Route::put('/{id}', [ConfiguracionHorarioController::class, 'update']);
        Route::delete('/{id}', [ConfiguracionHorarioController::class, 'destroy']);
    });

    Route::prefix('horarios/generar')->group(function () {
        Route::post('/', [HorarioGeneratorController::class, 'generar']);
        Route::post('/profesor/{profesorId}', [HorarioGeneratorController::class, 'generarPorProfesor']);
        Route::post('/grado/{gradoId}', [HorarioGeneratorController::class, 'generarPorGrado']);
        Route::get('/vista-previa', [HorarioGeneratorController::class, 'vistaPrevia']);
        Route::get('/configuraciones', [HorarioGeneratorController::class, 'configuraciones']);
        Route::get('/estadisticas', [HorarioGeneratorController::class, 'estadisticas']);
        Route::delete('/limpiar', [HorarioGeneratorController::class, 'limpiar']);
    });

    Route::prefix('horarios')->group(function () {
        Route::get('/', [HorarioController::class, 'index']);
        Route::get('/profesor/{profesorId}', [HorarioController::class, 'getByProfesor']);
        Route::get('/grado/{gradoId}', [HorarioController::class, 'getByGrado']);
        Route::get('/dia/{dia}', [HorarioController::class, 'getByDia']);
        Route::get('/{id}', [HorarioController::class, 'show']);
        Route::put('/{id}', [HorarioController::class, 'update']);
        Route::delete('/{id}', [HorarioController::class, 'destroy']);
    });

    Route::prefix('historial')->group(function () {
        Route::get('/', [HistorialController::class, 'index']);
        Route::get('/horario/{horarioId}', [HistorialController::class, 'getByHorario']);
        Route::get('/versiones/{horarioId}', [HistorialController::class, 'getVersiones']);
        Route::get('/estadisticas', [HistorialController::class, 'estadisticas']);
        Route::get('/{id}', [HistorialController::class, 'show']);
        Route::post('/revertir/{historialId}', [HistorialController::class, 'revertir']);
        Route::post('/restaurar/{horarioId}', [HistorialController::class, 'restaurar']);
    });

    Route::prefix('exportar')->group(function () {
        Route::get('/filtros', [ExportController::class, 'filtrosDisponibles']);
        Route::post('/excel', [ExportController::class, 'exportExcel']);
        Route::get('/excel/download', [ExportController::class, 'downloadExcel']);
        Route::post('/pdf', [ExportController::class, 'exportPdf']);
        Route::get('/pdf/download', [ExportController::class, 'downloadPdf']);
        Route::get('/imagen/html', [ExportController::class, 'getHtmlForImage']);
        Route::post('/csv', [ExportController::class, 'exportCsv']);
    });
});
