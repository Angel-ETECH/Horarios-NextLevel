@extends('layouts.app')

@section('title', 'Aulas - Next Level School')

@section('page-title', 'Aulas')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Aulas
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Administra las aulas disponibles para la programación de horarios.
            </p>
        </div>

        <button
            id="open-aula-modal"
            type="button"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
        >
            <span class="text-lg">+</span>
            Agregar aula
        </button>

    </div>


    {{-- ESTADÍSTICAS --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Total de aulas
                    </p>

                    <p
                        id="total-aulas"
                        class="mt-2 text-3xl font-bold text-slate-900"
                    >
                        6
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-100 text-2xl">
                    🏫
                </div>

            </div>

        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        Disponibles
                    </p>

                    <p
                        id="aulas-disponibles"
                        class="mt-2 text-3xl font-bold text-emerald-600"
                    >
                        5
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-2xl">
                    ✅
                </div>

            </div>

        </div>


        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-slate-500">
                        No disponibles
                    </p>

                    <p
                        id="aulas-no-disponibles"
                        class="mt-2 text-3xl font-bold text-red-600"
                    >
                        1
                    </p>
                </div>

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-2xl">
                    🚫
                </div>

            </div>

        </div>

    </div>


    {{-- FILTROS --}}
    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">

        <div class="flex flex-col gap-3 md:flex-row">

            <div class="relative flex-1">

                <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                    🔎
                </span>

                <input
                    id="buscar-aula"
                    type="text"
                    placeholder="Buscar aula..."
                    class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-4 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

            </div>


            <select
                id="filtro-aula-estado"
                class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
            >
                <option value="">Todos los estados</option>
                <option value="disponible">Disponible</option>
                <option value="no-disponible">No disponible</option>
            </select>

        </div>

    </div>


    {{-- TABLA --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">

            <div>
                <h2 class="font-semibold text-slate-900">
                    Lista de aulas
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Mostrando
                    <span id="resultados-aulas">6</span>
                    aulas
                </p>
            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[800px]">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Aula
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Capacidad
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Tipo
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Estado
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">


                    {{-- AULA 1 --}}

                    <tr
                        class="aula-row transition hover:bg-slate-50"
                        data-nombre="Aula 101"
                        data-estado="disponible"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-lg">
                                    🏫
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Aula 101
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Primer piso
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            30 alumnos
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Aula regular
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Disponible
                            </span>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-aula rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Aula 101"
                                data-capacidad="30"
                                data-tipo="Aula regular"
                                data-estado="disponible"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-aula rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                data-nombre="Aula 101"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    {{-- AULA 2 --}}

                    <tr
                        class="aula-row transition hover:bg-slate-50"
                        data-nombre="Aula 102"
                        data-estado="disponible"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-lg">
                                    🏫
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Aula 102
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Primer piso
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            35 alumnos
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Aula regular
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Disponible
                            </span>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-aula rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Aula 102"
                                data-capacidad="35"
                                data-tipo="Aula regular"
                                data-estado="disponible"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-aula rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                data-nombre="Aula 102"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    {{-- AULA 3 --}}

                    <tr
                        class="aula-row transition hover:bg-slate-50"
                        data-nombre="Laboratorio de Computación"
                        data-estado="disponible"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-lg">
                                    💻
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Laboratorio de Computación
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Segundo piso
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            25 alumnos
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Laboratorio
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Disponible
                            </span>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-aula rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Laboratorio de Computación"
                                data-capacidad="25"
                                data-tipo="Laboratorio"
                                data-estado="disponible"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-aula rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                data-nombre="Laboratorio de Computación"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    {{-- AULA 4 --}}

                    <tr
                        class="aula-row transition hover:bg-slate-50"
                        data-nombre="Laboratorio de Ciencias"
                        data-estado="disponible"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-lg">
                                    🔬
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Laboratorio de Ciencias
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Segundo piso
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            25 alumnos
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Laboratorio
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Disponible
                            </span>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-aula rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Laboratorio de Ciencias"
                                data-capacidad="25"
                                data-tipo="Laboratorio"
                                data-estado="disponible"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-aula rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                data-nombre="Laboratorio de Ciencias"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    {{-- AULA 5 --}}

                    <tr
                        class="aula-row transition hover:bg-slate-50"
                        data-nombre="Sala de Reuniones"
                        data-estado="disponible"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-lg">
                                    🪑
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Sala de Reuniones
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Primer piso
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            15 alumnos
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Sala
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Disponible
                            </span>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-aula rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Sala de Reuniones"
                                data-capacidad="15"
                                data-tipo="Sala"
                                data-estado="disponible"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-aula rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                data-nombre="Sala de Reuniones"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    {{-- AULA 6 --}}

                    <tr
                        class="aula-row transition hover:bg-slate-50"
                        data-nombre="Aula 201"
                        data-estado="no-disponible"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-lg">
                                    🏫
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Aula 201
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Segundo piso
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            40 alumnos
                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Aula regular
                        </td>


                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">
                                No disponible
                            </span>

                        </td>


                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-aula rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Aula 201"
                                data-capacidad="40"
                                data-tipo="Aula regular"
                                data-estado="no-disponible"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-aula rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                data-nombre="Aula 201"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    {{-- ESTADO VACÍO --}}

                    <tr
                        id="aulas-empty"
                        class="hidden"
                    >

                        <td
                            colspan="5"
                            class="px-6 py-12 text-center"
                        >

                            <div class="text-4xl">
                                🔎
                            </div>

                            <p class="mt-3 font-medium text-slate-900">
                                No se encontraron aulas
                            </p>

                            <p class="mt-1 text-sm text-slate-500">
                                Intenta cambiar tu búsqueda o filtro.
                            </p>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ========================================== --}}
{{-- MODAL AULA --}}
{{-- ========================================== --}}

<div
    id="aula-modal"
    class="fixed inset-0 z-[100] hidden"
    aria-hidden="true"
>

    <div
        id="aula-modal-overlay"
        class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
    ></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl">


            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">

                <div>

                    <h2
                        id="aula-modal-title"
                        class="text-lg font-semibold text-slate-900"
                    >
                        Agregar aula
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Completa la información del aula.
                    </p>

                </div>


                <button
                    id="close-aula-modal"
                    type="button"
                    class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    ✕
                </button>

            </div>


            <form id="aula-form">

                <div class="space-y-5 px-6 py-6">


                    {{-- NOMBRE --}}

                    <div>

                        <label
                            for="aula-nombre"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Nombre del aula
                        </label>

                        <input
                            id="aula-nombre"
                            type="text"
                            placeholder="Ej. Aula 301"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >

                    </div>


                    {{-- CAPACIDAD --}}

                    <div>

                        <label
                            for="aula-capacidad"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Capacidad
                        </label>

                        <input
                            id="aula-capacidad"
                            type="number"
                            min="1"
                            placeholder="Ej. 30"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >

                    </div>


                    {{-- TIPO --}}

                    <div>

                        <label
                            for="aula-tipo"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Tipo de aula
                        </label>

                        <select
                            id="aula-tipo"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >

                            <option value="">
                                Seleccionar tipo
                            </option>

                            <option value="Aula regular">
                                Aula regular
                            </option>

                            <option value="Laboratorio">
                                Laboratorio
                            </option>

                            <option value="Sala">
                                Sala
                            </option>

                        </select>

                    </div>


                    {{-- ESTADO --}}

                    <div>

                        <label
                            for="aula-estado"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Estado
                        </label>

                        <select
                            id="aula-estado"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >

                            <option value="disponible">
                                Disponible
                            </option>

                            <option value="no-disponible">
                                No disponible
                            </option>

                        </select>

                    </div>

                </div>


                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end">

                    <button
                        id="cancel-aula-modal"
                        type="button"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                    >
                        Cancelar
                    </button>


                    <button
                        id="aula-submit-button"
                        type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                    >
                        Guardar aula
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ========================================== --}}
{{-- MODAL ELIMINAR --}}
{{-- ========================================== --}}

<div
    id="delete-aula-modal"
    class="fixed inset-0 z-[110] hidden"
    aria-hidden="true"
>

    <div
        id="delete-aula-overlay"
        class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
    ></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">

            <div class="p-6 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-2xl">
                    🗑️
                </div>

                <h2 class="mt-4 text-lg font-semibold text-slate-900">
                    Eliminar aula
                </h2>

                <p class="mt-2 text-sm leading-6 text-slate-500">

                    ¿Estás seguro de que deseas eliminar

                    <span
                        id="delete-aula-name"
                        class="font-semibold text-slate-700"
                    ></span>?

                    Esta acción no se puede deshacer.

                </p>

            </div>


            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end">

                <button
                    id="cancel-delete-aula"
                    type="button"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                >
                    Cancelar
                </button>

                <button
                    id="confirm-delete-aula"
                    type="button"
                    class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
                >
                    Sí, eliminar
                </button>

            </div>

        </div>

    </div>

</div>

@endsection