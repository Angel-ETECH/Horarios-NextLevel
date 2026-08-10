<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/profesores', function () {
    return view('profesores.index');
})->name('profesores.index');