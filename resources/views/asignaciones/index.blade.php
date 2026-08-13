@extends('layouts.app')

@section('title', 'Asignaciones - Next Level School')

@section('page-title', 'Asignaciones')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            Asignaciones
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Asigna profesores, cursos, grados y aulas para la generación de horarios.
        </p>
    </div>


    {{-- FORMULARIO --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-5 py-4">

            <h2 class="font-semibold text-slate-900">
                Nueva asignación
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Completa los datos necesarios para crear una asignación.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 lg:grid-cols-4">

            {{-- PROFESOR --}}
            <div>

                <label
                    for="asignacion-profesor"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Profesor
                </label>

                <select
                    id="asignacion-profesor"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                    <option value="">
                        Seleccionar profesor
                    </option>

                    <option value="1">
                        Juan Pérez
                    </option>

                    <option value="2">
                        María García
                    </option>

                    <option value="3">
                        Carlos Ramírez
                    </option>

                    <option value="4">
                        Ana Torres
                    </option>

                    <option value="5">
                        Luis Mendoza
                    </option>

                </select>

            </div>


            {{-- CURSO --}}
            <div>

                <label
                    for="asignacion-curso"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Curso
                </label>

                <select
                    id="asignacion-curso"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                    <option value="">
                        Seleccionar curso
                    </option>

                    <option value="1">
                        Matemática
                    </option>

                    <option value="2">
                        Comunicación
                    </option>

                    <option value="3">
                        Inglés
                    </option>

                    <option value="4">
                        Ciencia y Tecnología
                    </option>

                    <option value="5">
                        Educación Física
                    </option>

                </select>

            </div>


            {{-- GRADO --}}
            <div>

                <label
                    for="asignacion-grado"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Grado / Sección
                </label>

                <select
                    id="asignacion-grado"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                    <option value="">
                        Seleccionar grado
                    </option>

                    <option value="1">
                        1° A
                    </option>

                    <option value="2">
                        1° B
                    </option>

                    <option value="3">
                        2° A
                    </option>

                    <option value="4">
                        2° B
                    </option>

                    <option value="5">
                        3° A
                    </option>

                    <option value="6">
                        4° A
                    </option>

                    <option value="7">
                        5° A
                    </option>

                </select>

            </div>


            {{-- AULA --}}
            <div>

                <label
                    for="asignacion-aula"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Aula
                </label>

                <select
                    id="asignacion-aula"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

                    <option value="">
                        Seleccionar aula
                    </option>

                    <option value="1">
                        Aula 101
                    </option>

                    <option value="2">
                        Aula 102
                    </option>

                    <option value="3">
                        Aula 103
                    </option>

                    <option value="4">
                        Laboratorio
                    </option>

                    <option value="5">
                        Aula de cómputo
                    </option>

                </select>

            </div>

        </div>


        {{-- HORAS --}}
        <div class="border-t border-slate-200 px-5 py-5">

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">

                {{-- HORAS SEMANALES --}}
                <div>

                    <label
                        for="horas-semanales"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Horas semanales
                    </label>

                    <input
                        id="horas-semanales"
                        type="number"
                        min="1"
                        max="40"
                        placeholder="Ej. 4"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                    >

                </div>


                {{-- HORAS POR BLOQUE --}}
                <div>

                    <label
                        for="horas-bloque"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Duración del bloque
                    </label>

                    <select
                        id="horas-bloque"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                    >

                        <option value="1">
                            1 hora
                        </option>

                        <option value="2">
                            2 horas
                        </option>

                    </select>

                </div>


                {{-- ESTADO --}}
                <div>

                    <label
                        for="asignacion-estado"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Estado
                    </label>

                    <select
                        id="asignacion-estado"
                        class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                    >

                        <option value="activo">
                            Activo
                        </option>

                        <option value="inactivo">
                            Inactivo
                        </option>

                    </select>

                </div>

            </div>

        </div>


        {{-- BOTONES --}}
        <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end">

            <button
                id="limpiar-asignacion"
                type="button"
                class="rounded-lg border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
            >
                Limpiar
            </button>

            <button
                id="guardar-asignacion"
                type="button"
                class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
            >
                + Crear asignación
            </button>

        </div>

    </div>


    {{-- LISTADO --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 md:flex-row md:items-center md:justify-between">

            <div>

                <h2 class="font-semibold text-slate-900">
                    Asignaciones actuales
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Lista de cursos asignados a cada profesor.
                </p>

            </div>


            {{-- BUSCAR --}}
            <div class="relative">

                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    🔎
                </span>

                <input
                    id="buscar-asignacion"
                    type="text"
                    placeholder="Buscar..."
                    class="w-full rounded-lg border border-slate-300 py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 md:w-64"
                >

            </div>

        </div>


        {{-- TABLA --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px] text-left">

                <thead>

                    <tr class="border-b border-slate-200 bg-slate-50">

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Profesor
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Curso
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Grado
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Aula
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Horas
                        </th>

                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Estado
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="asignaciones-body"
                    class="divide-y divide-slate-100"
                >

                    {{-- EJEMPLO 1 --}}
                    <tr class="asignacion-row hover:bg-slate-50">

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                                    JP
                                </div>

                                <span class="text-sm font-medium text-slate-800">
                                    Juan Pérez
                                </span>

                            </div>

                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            Matemática
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            1° A
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            Aula 101
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            4 h
                        </td>

                        <td class="px-5 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>

                        <td class="px-5 py-4 text-right">

                            <button
                                type="button"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                            >
                                Editar
                            </button>

                        </td>

                    </tr>


                    {{-- EJEMPLO 2 --}}
                    <tr class="asignacion-row hover:bg-slate-50">

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                                    MG
                                </div>

                                <span class="text-sm font-medium text-slate-800">
                                    María García
                                </span>

                            </div>

                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            Comunicación
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            2° A
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            Aula 102
                        </td>

                        <td class="px-5 py-4 text-sm text-slate-600">
                            5 h
                        </td>

                        <td class="px-5 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>

                        <td class="px-5 py-4 text-right">

                            <button
                                type="button"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                            >
                                Editar
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    {{-- INFORMACIÓN --}}
    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5">

        <div class="flex gap-3">

            <div class="text-xl">
                💡
            </div>

            <div>

                <h3 class="font-semibold text-indigo-900">
                    ¿Qué es una asignación?
                </h3>

                <p class="mt-1 text-sm leading-6 text-indigo-800">
                    Una asignación relaciona un profesor con un curso,
                    grado y aula, indicando además cuántas horas semanales
                    debe dictar. El generador de horarios utilizará estas
                    asignaciones junto con la disponibilidad de cada profesor.
                </p>

            </div>

        </div>

    </div>

</div>

@endsection