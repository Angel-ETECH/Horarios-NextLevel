<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/profesores', function () {
    return view('profesores.index');
})->name('profesores.index');

Route::get('/cursos', function () {
    return view('cursos.index');
})->name('cursos.index');

Route::get('/aulas', function () {
    return view('aulas.index');
})->name('aulas.index');