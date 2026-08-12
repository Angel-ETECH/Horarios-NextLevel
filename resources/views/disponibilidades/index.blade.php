@extends('layouts.app')

@section('title', 'Disponibilidad - Next Level School')

@section('page-title', 'Disponibilidad')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-900">
            Disponibilidad
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            Configura los días y horarios disponibles de cada profesor.
        </p>
    </div>


    {{-- SELECTOR DE PROFESOR --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

        <label
            for="profesor-select"
            class="mb-2 block text-sm font-medium text-slate-700"
        >
            Profesor
        </label>

        <select
            id="profesor-select"
            class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 md:max-w-md"
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


    {{-- RESUMEN DE DISPONIBILIDAD --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        {{-- BLOQUES SELECCIONADOS --}}
        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-600 text-lg text-white">
                    🕐
                </div>

                <div>

                    <p class="text-xs font-medium text-indigo-600">
                        Bloques disponibles
                    </p>

                    <p
                        id="availability-count"
                        class="text-2xl font-bold text-indigo-900"
                    >
                        0
                    </p>

                </div>

            </div>

        </div>


        {{-- DÍAS CON DISPONIBILIDAD --}}
        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-600 text-lg text-white">
                    📅
                </div>

                <div>

                    <p class="text-xs font-medium text-emerald-600">
                        Días disponibles
                    </p>

                    <p
                        id="availability-days"
                        class="text-2xl font-bold text-emerald-900"
                    >
                        0
                    </p>

                </div>

            </div>

        </div>


        {{-- HORAS --}}
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

            <div class="flex items-center gap-3">

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-700 text-lg text-white">
                    ⏱️
                </div>

                <div>

                    <p class="text-xs font-medium text-slate-600">
                        Horas disponibles
                    </p>

                    <p
                        id="availability-hours"
                        class="text-2xl font-bold text-slate-900"
                    >
                        0
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- PANEL PRINCIPAL --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        {{-- CABECERA --}}
        <div class="border-b border-slate-200 px-5 py-4">

            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Horarios disponibles
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Toca un bloque para marcarlo como disponible.
                    </p>

                </div>


                {{-- LEYENDA --}}
                <div class="flex flex-wrap items-center gap-4 text-xs">

                    <div class="flex items-center gap-2">

                        <span class="h-4 w-4 rounded bg-indigo-600"></span>

                        <span class="text-slate-600">
                            Disponible
                        </span>

                    </div>


                    <div class="flex items-center gap-2">

                        <span class="h-4 w-4 rounded border border-slate-300 bg-white"></span>

                        <span class="text-slate-600">
                            No disponible
                        </span>

                    </div>

                </div>

            </div>

        </div>


        @php
            $horasDisponibilidad = [
                '07:00', '08:00', '09:00', '10:00', '11:00',
                '12:00', '13:00', '14:00', '15:00', '16:00', '17:00',
            ];

            $diasDisponibilidad = [
                1 => 'Lunes',
                2 => 'Martes',
                3 => 'Miércoles',
                4 => 'Jueves',
                5 => 'Viernes',
                6 => 'Sábado',
            ];
        @endphp


        {{-- ========================================== --}}
        {{-- GRID - SOLO TABLET Y ESCRITORIO (md en adelante) --}}
        {{-- ========================================== --}}

        <div class="hidden overflow-x-auto md:block">

            <div class="min-w-[850px] p-5">

                <div
                    id="disponibilidad-grid"
                    class="grid grid-cols-7 overflow-hidden rounded-xl border border-slate-200"
                >

                    {{-- HORA --}}
                    <div class="border-b border-r border-slate-200 bg-slate-50 p-4 text-center">

                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Hora
                        </span>

                    </div>


                    {{-- DÍAS --}}

                    @foreach ($diasDisponibilidad as $dia => $nombreDia)

                        <div class="{{ $dia < 6 ? 'border-r' : '' }} border-b border-slate-200 bg-slate-50 p-3 text-center">

                            <button
                                type="button"
                                class="select-day w-full rounded-lg px-2 py-2 transition hover:bg-indigo-100"
                                data-dia="{{ $dia }}"
                            >
                                <span class="block text-sm font-semibold text-slate-700">
                                    {{ $nombreDia }}
                                </span>

                                <span class="mt-1 block text-[10px] text-slate-400">
                                    Seleccionar
                                </span>
                            </button>

                        </div>

                    @endforeach


                    {{-- FILAS DE HORA --}}

                    @foreach ($horasDisponibilidad as $hora)

                        <div class="border-b border-r border-slate-200 bg-slate-50 p-4 text-center">
                            <span class="text-xs font-medium text-slate-600">
                                {{ $hora }}
                            </span>
                        </div>

                        @for ($dia = 1; $dia <= 6; $dia++)

                            <button
                                type="button"
                                class="availability-slot border-b border-r border-slate-200 bg-white transition hover:bg-indigo-50"
                                data-dia="{{ $dia }}"
                                data-hora="{{ $hora }}"
                                aria-label="{{ $hora }}"
                            >
                            </button>

                        @endfor

                    @endforeach

                </div>

            </div>

        </div>


        {{-- ========================================== --}}
        {{-- VISTA MÓVIL (menos de md) --}}
        {{-- ========================================== --}}

        <div class="p-5 md:hidden">

            {{-- PESTAÑAS DE DÍA --}}
            <div class="mb-4 flex gap-2 overflow-x-auto pb-1">

                @foreach ($diasDisponibilidad as $dia => $nombreDia)

                    <button
                        type="button"
                        class="mobile-day-tab flex-shrink-0 rounded-lg px-3 py-2 text-xs font-semibold transition {{ $dia === 1 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}"
                        data-dia="{{ $dia }}"
                    >
                        {{ $nombreDia }}
                    </button>

                @endforeach

            </div>


            {{-- PANELES POR DÍA --}}

            @foreach ($diasDisponibilidad as $dia => $nombreDia)

                <div
                    class="mobile-day-panel {{ $dia === 1 ? '' : 'hidden' }}"
                    data-dia-panel="{{ $dia }}"
                >

                    <div class="mb-3 flex items-center justify-between">

                        <span class="text-sm font-semibold text-slate-700">
                            {{ $nombreDia }}
                        </span>

                        <button
                            type="button"
                            class="select-day rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-600 transition hover:bg-indigo-100"
                            data-dia="{{ $dia }}"
                        >
                            Seleccionar todo el día
                        </button>

                    </div>


                    <div class="divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200">

                        @foreach ($horasDisponibilidad as $hora)

                            <button
                                type="button"
                                class="availability-slot flex w-full items-center justify-between bg-white px-4 py-3 text-left transition hover:bg-indigo-50"
                                data-dia="{{ $dia }}"
                                data-hora="{{ $hora }}"
                            >
                                <span class="text-sm font-medium text-slate-600 [.selected_&]:text-white">
                                    {{ $hora }}
                                </span>

                                <span class="h-5 w-5 rounded-full border border-slate-300 transition [.selected_&]:border-white [.selected_&]:bg-white"></span>
                            </button>

                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>


        {{-- ACCIONES --}}
        <div class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex flex-wrap gap-2">

                <button
                    id="select-all-availability"
                    type="button"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    ✓ Seleccionar todo
                </button>


                <button
                    id="clear-availability"
                    type="button"
                    class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
                >
                    ↺ Limpiar
                </button>

            </div>


            <button
                id="save-availability"
                type="button"
                class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700"
            >
                💾 Guardar disponibilidad
            </button>

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
                    ¿Cómo funciona?
                </h3>

                <p class="mt-1 text-sm leading-6 text-indigo-800">
                    Selecciona un profesor y marca los bloques horarios
                    en los que está disponible para dictar clases.
                    Esta información será utilizada posteriormente
                    para generar los horarios automáticamente.
                </p>

            </div>

        </div>

    </div>

</div>

@endsection