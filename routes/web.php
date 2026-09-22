<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('dashboard');
})->name('dashboard');


/*
|--------------------------------------------------------------------------
| PROFESORES
|--------------------------------------------------------------------------
*/

Route::get('/profesores', function () {
    return view('profesores.index');
})->name('profesores.index');


/*
|--------------------------------------------------------------------------
| CURSOS
|--------------------------------------------------------------------------
*/

Route::get('/cursos', function () {
    return view('cursos.index');
})->name('cursos.index');


/*
|--------------------------------------------------------------------------
| AULAS
|--------------------------------------------------------------------------
*/

Route::get('/aulas', function () {
    return view('aulas.index');
})->name('aulas.index');


/*
|--------------------------------------------------------------------------
| DISPONIBILIDADES
|--------------------------------------------------------------------------
*/

Route::get('/disponibilidades', function () {
    return view('disponibilidades.index');
})->name('disponibilidades.index');


/*
|--------------------------------------------------------------------------
| ASIGNACIONES
|--------------------------------------------------------------------------
*/

Route::get('/asignaciones', function () {
    return view('asignaciones.index');
})->name('asignaciones.index');


/*
|--------------------------------------------------------------------------
| HORARIOS
|--------------------------------------------------------------------------
*/

Route::get('/horarios', function () {
    return view('horarios.index');
})->name('horarios.index');


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('login');
})->name('login');


/*
|--------------------------------------------------------------------------
| REGISTRO
|--------------------------------------------------------------------------
|
| Por ahora esta ruta solamente muestra el frontend.
|
| Más adelante el backend deberá encargarse de:
|
| POST /api/register
|
| y validar:
|
| - nombre
| - usuario
| - correo
| - contraseña
| - confirmación de contraseña
|
*/

Route::get('/registro', function () {
    return view('register');
})->name('register');


/*
|--------------------------------------------------------------------------
| CONSULTA PÚBLICA DE HORARIOS
|--------------------------------------------------------------------------
*/

Route::get('/consulta-horarios', function () {
    return view('horarios.consulta');
})->name('horarios.consulta');