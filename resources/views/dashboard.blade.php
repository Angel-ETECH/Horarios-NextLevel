@extends('layouts.app')

@section('title', 'Dashboard | Next Level')

@section('page-title', 'Dashboard')

@section('content')

    <!-- BIENVENIDA -->
    <div class="mb-8">

        <h1 class="text-3xl font-bold text-slate-900">
            Bienvenido a Next Level 👋
        </h1>

        <p class="text-slate-500 mt-2">
            Administra profesores, cursos, aulas y horarios desde un solo lugar.
        </p>

    </div>


    <!-- ESTADÍSTICAS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">


        <!-- PROFESORES -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Profesores
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        25
                    </p>

                    <p class="text-xs text-emerald-600 mt-2">
                        ↑ 8% este mes
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center text-xl">
                    👨‍🏫
                </div>

            </div>

        </div>


        <!-- CURSOS -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Cursos
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        12
                    </p>

                    <p class="text-xs text-emerald-600 mt-2">
                        ↑ 4% este mes
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-xl">
                    📚
                </div>

            </div>

        </div>


        <!-- AULAS -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Aulas
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        8
                    </p>

                    <p class="text-xs text-slate-500 mt-2">
                        Disponibles actualmente
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-xl">
                    🏫
                </div>

            </div>

        </div>


        <!-- HORARIOS -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Horarios
                    </p>

                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        48
                    </p>

                    <p class="text-xs text-emerald-600 mt-2">
                        Generados
                    </p>

                </div>

                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-xl">
                    🗓️
                </div>

            </div>

        </div>

    </div>


    <!-- CONTENIDO INFERIOR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


        <!-- HORARIOS DE HOY -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200">

            <div class="p-6 border-b border-slate-200 flex items-center justify-between">

                <div>

                    <h2 class="text-lg font-semibold text-slate-900">
                        Horarios de hoy
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Clases programadas para hoy
                    </p>

                </div>

                <a href="#"
                   class="text-sm font-medium text-indigo-600 hover:text-indigo-700">

                    Ver todos →

                </a>

            </div>


            <div class="divide-y divide-slate-100">


                <div class="p-5 flex items-center gap-5">

                    <div class="w-16 text-center">

                        <p class="text-sm font-semibold text-slate-900">
                            08:00
                        </p>

                        <p class="text-xs text-slate-400">
                            AM
                        </p>

                    </div>


                    <div class="w-1 h-12 rounded-full bg-indigo-500"></div>


                    <div class="flex-1">

                        <p class="font-medium text-slate-900">
                            Matemática
                        </p>

                        <p class="text-sm text-slate-500">
                            Prof. Juan Pérez · Aula 201
                        </p>

                    </div>


                    <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">
                        En curso
                    </span>

                </div>


                <div class="p-5 flex items-center gap-5">

                    <div class="w-16 text-center">

                        <p class="text-sm font-semibold text-slate-900">
                            09:00
                        </p>

                        <p class="text-xs text-slate-400">
                            AM
                        </p>

                    </div>


                    <div class="w-1 h-12 rounded-full bg-emerald-500"></div>


                    <div class="flex-1">

                        <p class="font-medium text-slate-900">
                            Inglés
                        </p>

                        <p class="text-sm text-slate-500">
                            Prof. María López · Aula 203
                        </p>

                    </div>


                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                        Próxima
                    </span>

                </div>


                <div class="p-5 flex items-center gap-5">

                    <div class="w-16 text-center">

                        <p class="text-sm font-semibold text-slate-900">
                            10:00
                        </p>

                        <p class="text-xs text-slate-400">
                            AM
                        </p>

                    </div>


                    <div class="w-1 h-12 rounded-full bg-amber-500"></div>


                    <div class="flex-1">

                        <p class="font-medium text-slate-900">
                            Física
                        </p>

                        <p class="text-sm text-slate-500">
                            Prof. Carlos Díaz · Aula 202
                        </p>

                    </div>


                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                        Próxima
                    </span>

                </div>

            </div>

        </div>


        <!-- ACCIONES -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6">

            <h2 class="text-lg font-semibold text-slate-900">
                Acciones rápidas
            </h2>

            <p class="text-sm text-slate-500 mt-1 mb-6">
                Accede rápidamente a las funciones principales.
            </p>


            <div class="space-y-3">


                <a href="#"
                   class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-indigo-50 transition">

                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                        👨‍🏫
                    </div>

                    <div>

                        <p class="font-medium text-slate-900">
                            Agregar profesor
                        </p>

                        <p class="text-xs text-slate-500">
                            Registrar nuevo profesor
                        </p>

                    </div>

                </a>


                <a href="#"
                   class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-indigo-50 transition">

                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                        📚
                    </div>

                    <div>

                        <p class="font-medium text-slate-900">
                            Agregar curso
                        </p>

                        <p class="text-xs text-slate-500">
                            Registrar nuevo curso
                        </p>

                    </div>

                </a>


                <a href="#"
                   class="flex items-center gap-4 p-4 rounded-xl bg-slate-50 hover:bg-indigo-50 transition">

                    <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                        🗓️
                    </div>

                    <div>

                        <p class="font-medium text-slate-900">
                            Ver horarios
                        </p>

                        <p class="text-xs text-slate-500">
                            Consultar horarios
                        </p>

                    </div>

                </a>

            </div>

        </div>

    </div>

@endsection