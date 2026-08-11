@extends('layouts.app')

@section('title', 'Profesores - Next Level School')

@section('page-title', 'Profesores')

@section('content')

<div class="space-y-6">

    <!-- ESTADÍSTICAS -->

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        <!-- TOTAL -->

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Total profesores
                    </p>

                    <p
                        id="total-profesores"
                        class="mt-2 text-2xl font-bold text-slate-900"
                    >
                        4
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-100 text-xl">
                    👨‍🏫
                </div>

            </div>

        </div>


        <!-- ACTIVOS -->

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Profesores activos
                    </p>

                    <p
                        id="profesores-activos"
                        class="mt-2 text-2xl font-bold text-emerald-600"
                    >
                        3
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-100 text-xl">
                    ✓
                </div>

            </div>

        </div>


        <!-- INACTIVOS -->

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Profesores inactivos
                    </p>

                    <p
                        id="profesores-inactivos"
                        class="mt-2 text-2xl font-bold text-slate-600"
                    >
                        1
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-xl">
                    ○
                </div>

            </div>

        </div>

    </div>

    <!-- ENCABEZADO -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Profesores
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Gestiona los profesores de Next Level School.
            </p>
        </div>


        <!-- BOTÓN AGREGAR -->

        <button
    id="open-profesor-modal"
    type="button"
    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
>

            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 4v16m8-8H4"
                />
            </svg>

            Agregar profesor

        </button>

    </div>


    <!-- TARJETA PRINCIPAL -->

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">


        <!-- BARRA DE HERRAMIENTAS -->

        <div class="flex flex-col gap-4 border-b border-slate-200 p-5 lg:flex-row lg:items-center lg:justify-between">


            <!-- BUSCADOR -->

            <div class="relative w-full lg:max-w-md">

                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-slate-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                        />
                    </svg>

                </div>

                <input
                    id="buscar-profesor"
                    type="text"
                    placeholder="Buscar profesor..."
                    class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                >

            </div>


            <!-- FILTRO -->

            <select
                id="filtro-estado"
                class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
            >

                <option value="">
                    Todos los estados
                </option>

                <option value="activo">
                    Activos
                </option>

                <option value="inactivo">
                    Inactivos
                </option>

            </select>

        </div>


        <!-- TABLA -->

        <div class="overflow-x-auto">

            <table class="w-full min-w-[800px] text-left">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Profesor
                        </th>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Especialidad
                        </th>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Teléfono
                        </th>

                        <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Estado
                        </th>

                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    <tr
                        id="profesores-empty"
                        class="hidden"
                    >
                        <td
                            colspan="5"
                            class="px-6 py-16 text-center"
                        >

                            <div class="flex flex-col items-center">

                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-3xl">
                                    🔎
                                </div>

                                <h3 class="mt-4 text-base font-semibold text-slate-900">
                                    No se encontraron profesores
                                </h3>

                                <p class="mt-1 max-w-sm text-sm text-slate-500">
                                    Intenta cambiar el término de búsqueda o el filtro seleccionado.
                                </p>

                            </div>

                        </td>
                    </tr>


                    <!-- PROFESOR 1 -->

                    <tr
                        class="profesor-row transition hover:bg-slate-50"
                        data-nombre="Juan Pérez"
                        data-estado="activo"
                    >

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                                    JP
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Juan Pérez
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        juan.perez@nextlevel.edu.pe
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="px-6 py-4 text-sm text-slate-600">
                            Matemática
                        </td>


                        <td class="px-6 py-4 text-sm text-slate-600">
                            987 654 321
                        </td>


                        <td class="px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>


                        <td class="px-6 py-4">

                            <div class="flex justify-end gap-2">

                                <button
                                    type="button"
                                    class="editar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                    title="Editar"
                                    data-nombre="Juan Pérez"
                                    data-email="juan.perez@nextlevel.edu.pe"
                                    data-telefono="987 654 321"
                                    data-especialidad="matematica"
                                    data-estado="activo"
                                >
                                    ✏️
                                </button>

                                <button
                                    type="button"
                                    class="eliminar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                    title="Eliminar"
                                    data-nombre="Juan Pérez"
                                >
                                    🗑️
                                </button>

                            </div>

                        </td>

                    </tr>


                    <!-- PROFESOR 2 -->

                    <tr
                        class="profesor-row transition hover:bg-slate-50"
                        data-nombre="María López"
                        data-estado="activo"
                    >

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-semibold text-emerald-700">
                                    ML
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        María López
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        maria.lopez@nextlevel.edu.pe
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="px-6 py-4 text-sm text-slate-600">
                            Inglés
                        </td>


                        <td class="px-6 py-4 text-sm text-slate-600">
                            986 123 456
                        </td>


                        <td class="px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>


                        <td class="px-6 py-4">

                            <div class="flex justify-end gap-2">

                                <button
                                    type="button"
                                    class="editar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                    title="Editar"
                                    data-nombre="María López"
                                    data-email="maria.lopez@nextlevel.edu.pe"
                                    data-telefono="986 123 456"
                                    data-especialidad="ingles"
                                    data-estado="activo"
                                >
                                    ✏️
                                </button>

                                <button
                                    type="button"
                                    class="eliminar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                    title="Eliminar"
                                    data-nombre="María López"
                                >
                                    🗑️
                                </button>

                            </div>

                        </td>

                    </tr>


                    <!-- PROFESOR 3 -->

                    <tr
                        class="profesor-row transition hover:bg-slate-50"
                        data-nombre="Carlos Díaz"
                        data-estado="activo"
                    >

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-semibold text-amber-700">
                                    CD
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Carlos Díaz
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        carlos.diaz@nextlevel.edu.pe
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="px-6 py-4 text-sm text-slate-600">
                            Física
                        </td>


                        <td class="px-6 py-4 text-sm text-slate-600">
                            985 789 123
                        </td>


                        <td class="px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>


                        <td class="px-6 py-4">

                            <div class="flex justify-end gap-2">

                                <button
                                    type="button"
                                    class="editar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                    title="Editar"
                                    data-nombre="Carlos Díaz"
                                    data-email="carlos.diaz@nextlevel.edu.pe"
                                    data-telefono="985 789 123"
                                    data-especialidad="fisica"
                                    data-estado="activo"
                                >
                                    ✏️
                                </button>

                                <button
                                    type="button"
                                    class="eliminar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                    title="Eliminar"
                                    data-nombre="Carlos Díaz"
                                >
                                    🗑️
                                </button>

                            </div>

                        </td>

                    </tr>


                    <!-- PROFESOR 4 -->

                    <tr
                        class="profesor-row transition hover:bg-slate-50"
                        data-nombre="Ana Sánchez"
                        data-estado="inactivo"
                    >

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-violet-100 font-semibold text-violet-700">
                                    AS
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Ana Sánchez
                                    </p>

                                    <p class="text-sm text-slate-500">
                                        ana.sanchez@nextlevel.edu.pe
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="px-6 py-4 text-sm text-slate-600">
                            Comunicación
                        </td>


                        <td class="px-6 py-4 text-sm text-slate-600">
                            984 456 789
                        </td>


                        <td class="px-6 py-4">

                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                Inactivo
                            </span>

                        </td>


                        <td class="px-6 py-4">

                            <div class="flex justify-end gap-2">

                                <button
                                    type="button"
                                    class="editar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                    title="Editar"
                                    data-nombre="Ana Sánchez"
                                    data-email="ana.sanchez@nextlevel.edu.pe"
                                    data-telefono="984 456 789"
                                    data-especialidad="comunicacion"
                                    data-estado="inactivo"
                                >
                                    ✏️
                                </button>

                                <button
                                    type="button"
                                    class="eliminar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                    title="Eliminar"
                                    data-nombre="Ana Sánchez"
                                >
                                    🗑️
                                </button>

                            </div>

                        </td>

                    </tr>


                </tbody>

            </table>

        </div>


        <!-- PAGINACIÓN -->

        <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">

            <p class="text-sm text-slate-500">
                Mostrando
                <span
                    id="resultados-profesores"
                    class="font-medium text-slate-700"
                >
                    4
                </span>
                de
                <span
                    id="total-resultados-profesores"
                    class="font-medium text-slate-700"
                >
                    4
                </span>
                profesores
            </p>


            <div class="flex items-center gap-1">

                <button
                    type="button"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400"
                    disabled
                >
                    Anterior
                </button>

                <button
                    type="button"
                    class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-medium text-white"
                >
                    1
                </button>

                <button
                    type="button"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                >
                    2
                </button>

                <button
                    type="button"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                >
                    3
                </button>

                <button
                    type="button"
                    class="rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-600 hover:bg-slate-50"
                >
                    Siguiente
                </button>

            </div>

        </div>


    </div>

</div>

    <!-- MODAL AGREGAR PROFESOR -->

    <div
        id="profesor-modal"
        class="fixed inset-0 z-[100] hidden"
        aria-hidden="true"
    >

        <!-- FONDO OSCURO -->

        <div
            id="profesor-modal-overlay"
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
        ></div>


        <!-- CONTENEDOR -->

        <div class="relative flex min-h-full items-center justify-center p-4">


            <div
                class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl"
            >


                <!-- ENCABEZADO -->

                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">

                    <div>

                        <h2 class="text-lg font-semibold text-slate-900">
                            Agregar profesor
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Registra un nuevo profesor en el sistema.
                        </p>

                    </div>


                    <!-- CERRAR -->

                    <button
                        id="close-profesor-modal"
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Cerrar"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18 18 6M6 6l12 12"
                            />

                        </svg>

                    </button>

                </div>


                <!-- FORMULARIO -->

                <form id="profesor-form">


                    <div class="space-y-5 px-6 py-6">


                        <!-- NOMBRES -->

                        <div>

                            <label
                                for="nombres"
                                class="mb-2 block text-sm font-medium text-slate-700"
                            >
                                Nombres
                            </label>

                            <input
                                id="nombres"
                                name="nombres"
                                type="text"
                                placeholder="Ej. Juan Carlos"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >

                        </div>


                        <!-- APELLIDOS -->

                        <div>

                            <label
                                for="apellidos"
                                class="mb-2 block text-sm font-medium text-slate-700"
                            >
                                Apellidos
                            </label>

                            <input
                                id="apellidos"
                                name="apellidos"
                                type="text"
                                placeholder="Ej. Pérez García"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >

                        </div>


                        <!-- CORREO Y TELÉFONO -->

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">


                            <div>

                                <label
                                    for="email"
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Correo electrónico
                                </label>

                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    placeholder="correo@ejemplo.com"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                >

                            </div>


                            <div>

                                <label
                                    for="telefono"
                                    class="mb-2 block text-sm font-medium text-slate-700"
                                >
                                    Teléfono
                                </label>

                                <input
                                    id="telefono"
                                    name="telefono"
                                    type="tel"
                                    placeholder="987 654 321"
                                    class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                                >

                            </div>


                        </div>


                        <!-- ESPECIALIDAD -->

                        <div>

                            <label
                                for="especialidad"
                                class="mb-2 block text-sm font-medium text-slate-700"
                            >
                                Especialidad
                            </label>

                            <select
                                id="especialidad"
                                name="especialidad"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >

                                <option value="">
                                    Seleccionar especialidad
                                </option>

                                <option value="matematica">
                                    Matemática
                                </option>

                                <option value="comunicacion">
                                    Comunicación
                                </option>

                                <option value="ingles">
                                    Inglés
                                </option>

                                <option value="fisica">
                                    Física
                                </option>

                                <option value="quimica">
                                    Química
                                </option>

                                <option value="historia">
                                    Historia
                                </option>

                            </select>

                        </div>


                        <!-- ESTADO -->

                        <div>

                            <label
                                for="estado"
                                class="mb-2 block text-sm font-medium text-slate-700"
                            >
                                Estado
                            </label>

                            <select
                                id="estado"
                                name="estado"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
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


                    <!-- BOTONES -->

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end">


                        <button
                            id="cancel-profesor-modal"
                            type="button"
                            class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                        >
                            Cancelar
                        </button>


                        <button
                            type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700"
                        >
                            Guardar profesor
                        </button>


                    </div>


                </form>


            </div>

        </div>

    </div>

    <!-- MODAL EDITAR PROFESOR -->

    <div
        id="editar-profesor-modal"
        class="fixed inset-0 z-[100] hidden"
        aria-hidden="true"
    >

        <!-- FONDO -->

        <div
            id="editar-modal-overlay"
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
        ></div>


        <!-- CONTENIDO -->

        <div class="relative flex min-h-full items-center justify-center p-4">

            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl">


                <!-- HEADER -->

                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">

                    <div>

                        <h2 class="text-lg font-semibold text-slate-900">
                            Editar profesor
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Modifica la información del profesor.
                        </p>

                    </div>


                    <button
                        id="close-editar-modal"
                        type="button"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        ✕
                    </button>

                </div>


                <!-- FORMULARIO -->

                <form id="editar-profesor-form">

                    <div class="space-y-5 px-6 py-6">


                        <!-- NOMBRE -->

                        <div>

                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Nombre completo
                            </label>

                            <input
                                id="editar-nombre"
                                type="text"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >

                        </div>


                        <!-- CORREO -->

                        <div>

                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Correo electrónico
                            </label>

                            <input
                                id="editar-email"
                                type="email"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >

                        </div>


                        <!-- TELÉFONO -->

                        <div>

                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Teléfono
                            </label>

                            <input
                                id="editar-telefono"
                                type="tel"
                                class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >

                        </div>


                        <!-- ESPECIALIDAD -->

                        <div>

                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Especialidad
                            </label>

                            <select
                                id="editar-especialidad"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                            >

                                <option value="matematica">Matemática</option>
                                <option value="comunicacion">Comunicación</option>
                                <option value="ingles">Inglés</option>
                                <option value="fisica">Física</option>
                                <option value="quimica">Química</option>
                                <option value="historia">Historia</option>

                            </select>

                        </div>


                        <!-- ESTADO -->

                        <div>

                            <label class="mb-2 block text-sm font-medium text-slate-700">
                                Estado
                            </label>

                            <select
                                id="editar-estado"
                                class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
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


                    <!-- BOTONES -->

                    <div class="flex justify-end gap-3 border-t border-slate-200 px-6 py-4">

                        <button
                            id="cancel-editar-modal"
                            type="button"
                            class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                        >
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- MODAL ELIMINAR PROFESOR -->

    <div
        id="eliminar-profesor-modal"
        class="fixed inset-0 z-[100] hidden"
        aria-hidden="true"
    >

        <div
            id="eliminar-profesor-modal-overlay"
            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
        ></div>


        <div class="relative flex min-h-full items-center justify-center p-4">

            <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl">

                <div class="flex items-start justify-between px-6 py-5">

                    <div class="flex items-start gap-4">

                        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full bg-red-100 text-xl">
                            🗑️
                        </div>

                        <div>

                            <h2 class="text-lg font-semibold text-slate-900">
                                Eliminar profesor
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                ¿Estás seguro de eliminar a
                                <span
                                    id="eliminar-profesor-nombre"
                                    class="font-medium text-slate-700"
                                ></span>
                                ? Esta acción no se puede deshacer.
                            </p>

                        </div>

                    </div>


                    <button
                        id="close-eliminar-profesor-modal"
                        type="button"
                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    >
                        ✕
                    </button>

                </div>


                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end">

                    <button
                        id="cancel-eliminar-profesor-modal"
                        type="button"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Cancelar
                    </button>

                    <button
                        id="confirm-eliminar-profesor"
                        type="button"
                        class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        Eliminar profesor
                    </button>

                </div>

            </div>

        </div>

    </div>
@endsection