@extends('layouts.app')

@section('title', 'Cursos - Next Level School')

@section('page-title', 'Cursos')

@section('content')

<div class="space-y-6">

    <!-- ENCABEZADO -->

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <h1 class="text-2xl font-bold text-slate-900">
                Cursos
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Gestiona los cursos disponibles en Next Level School.
            </p>

        </div>


        <button
            id="open-curso-modal"
            type="button"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >

            <span class="text-lg leading-none">
                +
            </span>

            Agregar curso

        </button>

    </div>


    <!-- ESTADÍSTICAS -->

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">


        <!-- TOTAL -->

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Total de cursos
                    </p>

                    <p
                        id="total-cursos"
                        class="mt-2 text-2xl font-bold text-slate-900"
                    >
                        6
                    </p>

                </div>

                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-100 text-xl">
                    📚
                </div>

            </div>

        </div>


        <!-- ACTIVOS -->

        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm font-medium text-slate-500">
                        Cursos activos
                    </p>

                    <p
                        id="cursos-activos"
                        class="mt-2 text-2xl font-bold text-emerald-600"
                    >
                        5
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
                        Cursos inactivos
                    </p>

                    <p
                        id="cursos-inactivos"
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


    <!-- TABLA -->

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">


        <!-- FILTROS -->

        <div class="border-b border-slate-200 p-4 sm:p-5">

            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">


                <!-- BUSCADOR -->

                <div class="relative w-full md:max-w-md">

                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        🔎
                    </span>

                    <input
                        id="buscar-curso"
                        type="text"
                        placeholder="Buscar curso..."
                        class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-4 text-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                    >

                </div>


                <!-- FILTRO -->

                <select
                    id="filtro-curso-estado"
                    class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 md:w-48"
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

        </div>


        <!-- TABLA -->

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Curso
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Código
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Área
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Horas semanales
                        </th>

                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Estado
                        </th>

                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">


                    <!-- MATEMÁTICA -->

                    <tr
                        class="curso-row transition hover:bg-slate-50"
                        data-nombre="Matemática"
                        data-estado="activo"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-lg">
                                    📐
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Matemática
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Educación matemática
                                    </p>

                                </div>

                            </div>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            MAT-001
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Matemática
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            6 horas
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-curso rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Matemática"
                                data-codigo="MAT-001"
                                data-area="Matemática"
                                data-horas="6"
                                data-estado="activo"
                                title="Editar"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-curso rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                                data-nombre="Matemática"
                                title="Eliminar"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    <!-- COMUNICACIÓN -->

                    <tr
                        class="curso-row transition hover:bg-slate-50"
                        data-nombre="Comunicación"
                        data-estado="activo"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100 text-lg">
                                    📝
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Comunicación
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Lengua y comunicación
                                    </p>

                                </div>

                            </div>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            COM-001
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Comunicación
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            5 horas
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-curso rounded-lg p-2 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Comunicación"
                                data-codigo="COM-001"
                                data-area="Comunicación"
                                data-horas="5"
                                data-estado="activo"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-curso rounded-lg p-2 text-slate-500 hover:bg-red-50 hover:text-red-600"
                                data-nombre="Comunicación"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    <!-- INGLÉS -->

                    <tr
                        class="curso-row transition hover:bg-slate-50"
                        data-nombre="Inglés"
                        data-estado="activo"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100 text-lg">
                                    🌎
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Inglés
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Idioma extranjero
                                    </p>

                                </div>

                            </div>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            ING-001
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Idiomas
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            4 horas
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-curso rounded-lg p-2 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Inglés"
                                data-codigo="ING-001"
                                data-area="Idiomas"
                                data-horas="4"
                                data-estado="activo"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-curso rounded-lg p-2 text-slate-500 hover:bg-red-50 hover:text-red-600"
                                data-nombre="Inglés"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    <!-- FÍSICA -->

                    <tr
                        class="curso-row transition hover:bg-slate-50"
                        data-nombre="Física"
                        data-estado="activo"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-orange-100 text-lg">
                                    ⚛️
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Física
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Ciencias físicas
                                    </p>

                                </div>

                            </div>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            FIS-001
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Ciencias
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            4 horas
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-curso rounded-lg p-2 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Física"
                                data-codigo="FIS-001"
                                data-area="Ciencias"
                                data-horas="4"
                                data-estado="activo"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-curso rounded-lg p-2 text-slate-500 hover:bg-red-50 hover:text-red-600"
                                data-nombre="Física"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    <!-- QUÍMICA -->

                    <tr
                        class="curso-row transition hover:bg-slate-50"
                        data-nombre="Química"
                        data-estado="activo"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-pink-100 text-lg">
                                    🧪
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Química
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Ciencias químicas
                                    </p>

                                </div>

                            </div>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            QUI-001
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Ciencias
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            4 horas
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                Activo
                            </span>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-curso rounded-lg p-2 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Química"
                                data-codigo="QUI-001"
                                data-area="Ciencias"
                                data-horas="4"
                                data-estado="activo"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-curso rounded-lg p-2 text-slate-500 hover:bg-red-50 hover:text-red-600"
                                data-nombre="Química"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    <!-- HISTORIA -->

                    <tr
                        class="curso-row transition hover:bg-slate-50"
                        data-nombre="Historia"
                        data-estado="inactivo"
                    >

                        <td class="whitespace-nowrap px-6 py-4">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-yellow-100 text-lg">
                                    📖
                                </div>

                                <div>

                                    <p class="font-medium text-slate-900">
                                        Historia
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Ciencias sociales
                                    </p>

                                </div>

                            </div>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            HIS-001
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            Ciencias Sociales
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                            3 horas
                        </td>

                        <td class="whitespace-nowrap px-6 py-4">

                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                Inactivo
                            </span>

                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">

                            <button
                                type="button"
                                class="editar-curso rounded-lg p-2 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600"
                                data-nombre="Historia"
                                data-codigo="HIS-001"
                                data-area="Ciencias Sociales"
                                data-horas="3"
                                data-estado="inactivo"
                            >
                                ✏️
                            </button>

                            <button
                                type="button"
                                class="eliminar-curso rounded-lg p-2 text-slate-500 hover:bg-red-50 hover:text-red-600"
                                data-nombre="Historia"
                            >
                                🗑️
                            </button>

                        </td>

                    </tr>


                    <!-- ESTADO VACÍO -->

                    <tr id="cursos-empty" class="hidden">

                        <td colspan="6" class="px-6 py-16 text-center">

                            <div class="flex flex-col items-center">

                                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-3xl">
                                    🔎
                                </div>

                                <h3 class="mt-4 text-base font-semibold text-slate-900">
                                    No se encontraron cursos
                                </h3>

                                <p class="mt-1 max-w-sm text-sm text-slate-500">
                                    Intenta cambiar el término de búsqueda o el filtro seleccionado.
                                </p>

                            </div>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- PIE -->

        <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <p class="text-sm text-slate-500">

                Mostrando
                <span
                    id="resultados-cursos"
                    class="font-medium text-slate-700"
                >
                    6
                </span>
                de
                <span
                    id="total-resultados-cursos"
                    class="font-medium text-slate-700"
                >
                    6
                </span>
                cursos

            </p>

        </div>

    </div>

</div>


<!-- ========================================== -->
<!-- MODAL AGREGAR / EDITAR CURSO -->
<!-- ========================================== -->

<div
    id="curso-modal"
    class="fixed inset-0 z-[100] hidden"
    aria-hidden="true"
>

    <div
        id="curso-modal-overlay"
        class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
    ></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl">


            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">

                <div>

                    <h2
                        id="curso-modal-title"
                        class="text-lg font-semibold text-slate-900"
                    >
                        Agregar curso
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Registra un nuevo curso en el sistema.
                    </p>

                </div>


                <button
                    id="close-curso-modal"
                    type="button"
                    class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    ✕
                </button>

            </div>


            <form id="curso-form">

                <div class="space-y-5 px-6 py-6">


                    <div>

                        <label
                            for="curso-nombre"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Nombre del curso
                        </label>

                        <input
                            id="curso-nombre"
                            type="text"
                            placeholder="Ej. Matemática"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >

                    </div>


                    <div>

                        <label
                            for="curso-codigo"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Código
                        </label>

                        <input
                            id="curso-codigo"
                            type="text"
                            placeholder="Ej. MAT-001"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm uppercase outline-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >

                    </div>


                    <div>

                        <label
                            for="curso-area"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Área
                        </label>

                        <select
                            id="curso-area"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >

                            <option value="">
                                Seleccionar área
                            </option>

                            <option value="Matemática">
                                Matemática
                            </option>

                            <option value="Comunicación">
                                Comunicación
                            </option>

                            <option value="Idiomas">
                                Idiomas
                            </option>

                            <option value="Ciencias">
                                Ciencias
                            </option>

                            <option value="Ciencias Sociales">
                                Ciencias Sociales
                            </option>

                        </select>

                    </div>


                    <div>

                        <label
                            for="curso-horas"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Horas semanales
                        </label>

                        <input
                            id="curso-horas"
                            type="number"
                            min="1"
                            max="20"
                            placeholder="Ej. 5"
                            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
                        >

                    </div>


                    <div>

                        <label
                            for="curso-estado"
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Estado
                        </label>

                        <select
                            id="curso-estado"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100"
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


                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end">

                    <button
                        id="cancel-curso-modal"
                        type="button"
                        class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                    >
                        Cancelar
                    </button>

                    <button
                        id="curso-submit-button"
                        type="submit"
                        class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700"
                    >
                        Guardar curso
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- ========================================== -->
<!-- MODAL ELIMINAR CURSO -->
<!-- ========================================== -->

<div
    id="eliminar-curso-modal"
    class="fixed inset-0 z-[100] hidden"
    aria-hidden="true"
>

    <div
        id="eliminar-curso-modal-overlay"
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
                            Eliminar curso
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            ¿Estás seguro de eliminar el curso
                            <span
                                id="eliminar-curso-nombre"
                                class="font-medium text-slate-700"
                            ></span>
                            ? Esta acción no se puede deshacer.
                        </p>

                    </div>

                </div>


                <button
                    id="close-eliminar-curso-modal"
                    type="button"
                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    ✕
                </button>

            </div>


            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-6 py-4 sm:flex-row sm:justify-end">

                <button
                    id="cancel-eliminar-curso-modal"
                    type="button"
                    class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50"
                >
                    Cancelar
                </button>

                <button
                    id="confirm-eliminar-curso"
                    type="button"
                    class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700"
                >
                    Eliminar curso
                </button>

            </div>

        </div>

    </div>

</div>

@endsection