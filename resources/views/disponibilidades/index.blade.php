@extends('layouts.app')

@section('title', 'Disponibilidad - Next Level School')
@section('page-title', 'Disponibilidad')

@section('content')

<style>
    /* =========================================================
       DISPONIBILIDAD - TEMA NEXT LEVEL SCHOOL
       Solo estilos visuales. No modifica la lógica de la vista.
    ========================================================== */
    #disponibilidad-page {
        --rojo-principal: #db0808;
        --rojo-oscuro: #8d0707;
        --azul-noche: #1B3A6B;
        --azul-oscuro: #0F2749;
        --blanco: #FFFFFF;
        --gris-borde: #e3e8ef;
        --gris-texto: #687386;
    }

    /* Encabezado */
    #disponibilidad-page > div:first-child {
        position: relative;
        overflow: hidden;
        padding: 1.4rem 1.5rem;
        border: 1px solid rgba(27, 58, 107, .12);
        border-radius: 1rem;
        background: linear-gradient(135deg, rgba(27, 58, 107, .06), rgba(219, 8, 8, .035)), var(--blanco);
        box-shadow: 0 8px 24px rgba(15, 39, 73, .06);
    }

    #disponibilidad-page > div:first-child::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, var(--rojo-principal), var(--rojo-oscuro));
    }

    #disponibilidad-page > div:first-child h1 {
        color: var(--azul-oscuro) !important;
        letter-spacing: -.025em;
    }

    #disponibilidad-page > div:first-child p {
        color: var(--gris-texto) !important;
    }

    /* Tarjetas y paneles */
    #disponibilidad-page > .rounded-xl,
    #disponibilidad-page > .grid > .rounded-xl {
        border-color: var(--gris-borde) !important;
        box-shadow: 0 8px 26px rgba(15, 39, 73, .06);
    }

    #disponibilidad-page > .rounded-xl {
        border-radius: 1rem !important;
    }

    /* Formularios */
    #disponibilidad-page select,
    #disponibilidad-page input[type='time'] {
        min-height: 44px;
        border-color: #d8dee8 !important;
        border-radius: .75rem !important;
        background-color: var(--blanco);
        color: var(--azul-oscuro);
        box-shadow: 0 1px 2px rgba(15, 39, 73, .03);
    }

    #disponibilidad-page select:hover,
    #disponibilidad-page input[type='time']:hover {
        border-color: rgba(27, 58, 107, .42) !important;
    }

    #disponibilidad-page select:focus,
    #disponibilidad-page input[type='time']:focus {
        border-color: var(--azul-noche) !important;
        box-shadow: 0 0 0 3px rgba(27, 58, 107, .12) !important;
    }

    #disponibilidad-page label {
        color: var(--azul-oscuro) !important;
        font-weight: 600 !important;
    }

    /* Resumen */
    #disponibilidad-page > .grid.sm\:grid-cols-3 > div {
        position: relative;
        overflow: hidden;
        background: var(--blanco) !important;
        border-color: var(--gris-borde) !important;
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    #disponibilidad-page > .grid.sm\:grid-cols-3 > div:hover {
        transform: translateY(-2px);
        border-color: rgba(27, 58, 107, .22) !important;
        box-shadow: 0 12px 30px rgba(15, 39, 73, .09);
    }

    #disponibilidad-page > .grid.sm\:grid-cols-3 > div::after {
        content: '';
        position: absolute;
        right: -22px;
        top: -26px;
        width: 78px;
        height: 78px;
        border-radius: 999px;
        background: rgba(27, 58, 107, .045);
    }

    #disponibilidad-page > .grid.sm\:grid-cols-3 > div:nth-child(1) > div > div:first-child {
        background: var(--rojo-principal) !important;
        box-shadow: 0 6px 16px rgba(219, 8, 8, .20);
    }

    #disponibilidad-page > .grid.sm\:grid-cols-3 > div:nth-child(2) > div > div:first-child {
        background: var(--azul-noche) !important;
        box-shadow: 0 6px 16px rgba(27, 58, 107, .20);
    }

    #disponibilidad-page > .grid.sm\:grid-cols-3 > div:nth-child(3) > div > div:first-child {
        background: var(--azul-oscuro) !important;
        box-shadow: 0 6px 16px rgba(15, 39, 73, .20);
    }

    #disponibilidad-page > .grid.sm\:grid-cols-3 > div p:first-child {
        color: var(--gris-texto) !important;
    }

    #availability-count,
    #availability-days,
    #availability-hours {
        color: var(--azul-oscuro) !important;
    }

    /* Cabeceras de panel */
    #disponibilidad-page .overflow-hidden.rounded-xl > .border-b {
        border-color: var(--gris-borde) !important;
        background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
    }

    #disponibilidad-page h2,
    #disponibilidad-page h3 {
        color: var(--azul-oscuro) !important;
    }

    /* Mantiene compatibilidad visual con clases que pueda manejar tu JS */
    #disponibilidad-page .bg-indigo-600 {
        background-color: var(--rojo-principal) !important;
    }

    /* Grid escritorio */
    #disponibilidad-grid {
        border-color: #dce2ea !important;
        border-radius: .9rem !important;
        box-shadow: 0 4px 16px rgba(15, 39, 73, .04);
    }

    #disponibilidad-grid > div {
        border-color: #e2e7ee !important;
        background: #f5f7fa !important;
    }

    #disponibilidad-grid > div:first-child {
        background: var(--azul-oscuro) !important;
    }

    #disponibilidad-grid > div:first-child span {
        color: var(--blanco) !important;
    }

    #disponibilidad-grid .select-day {
        border-radius: .65rem !important;
    }

    #disponibilidad-grid .select-day:hover {
        background: rgba(219, 8, 8, .075) !important;
    }

    #disponibilidad-grid .select-day span:first-child {
        color: var(--azul-oscuro) !important;
    }

    #disponibilidad-grid .availability-slot {
        min-height: 52px;
        border-color: #e4e8ef !important;
        background: var(--blanco) !important;
        position: relative;
    }

    #disponibilidad-grid .availability-slot:hover {
        background: rgba(219, 8, 8, .055) !important;
        box-shadow: inset 0 0 0 1px rgba(219, 8, 8, .12);
    }

    #disponibilidad-grid .availability-slot.selected {
        background: linear-gradient(135deg, var(--rojo-principal), #c30707) !important;
        box-shadow: inset 0 0 0 1px rgba(141, 7, 7, .24);
    }

    #disponibilidad-grid .availability-slot.selected::after {
        content: '✓';
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--blanco);
        font-size: .82rem;
        font-weight: 800;
    }

    /* Vista móvil */
    #disponibilidad-page .mobile-day-tab {
        border: 1px solid #e0e5ec;
        background: #f5f7fa !important;
        color: var(--azul-noche) !important;
    }

    #disponibilidad-page .mobile-day-tab.bg-indigo-600,
    #disponibilidad-page .mobile-day-tab.text-white {
        border-color: var(--azul-noche) !important;
        background: var(--azul-noche) !important;
        color: var(--blanco) !important;
        box-shadow: 0 5px 14px rgba(27, 58, 107, .18);
    }

    #disponibilidad-page .mobile-day-panel .select-day {
        border-color: rgba(219, 8, 8, .2) !important;
        background: rgba(219, 8, 8, .055) !important;
        color: var(--rojo-principal) !important;
    }

    #disponibilidad-page .mobile-day-panel .select-day:hover {
        background: rgba(219, 8, 8, .10) !important;
    }

    #disponibilidad-page .mobile-day-panel .availability-slot:hover {
        background: rgba(27, 58, 107, .045) !important;
    }

    #disponibilidad-page .mobile-day-panel .availability-slot.selected {
        background: linear-gradient(135deg, var(--rojo-principal), var(--rojo-oscuro)) !important;
    }

    #disponibilidad-page .mobile-day-panel .availability-slot.selected span {
        color: var(--blanco) !important;
    }

    /* Botones de acción */
    #disponibilidad-page #select-all-availability,
    #disponibilidad-page #clear-availability {
        border-color: #d8dee7 !important;
        background: var(--blanco) !important;
        color: var(--azul-oscuro) !important;
        border-radius: .7rem !important;
        box-shadow: 0 1px 2px rgba(15, 39, 73, .04);
    }

    #disponibilidad-page #select-all-availability:hover {
        border-color: rgba(27, 58, 107, .35) !important;
        background: rgba(27, 58, 107, .05) !important;
    }

    #disponibilidad-page #clear-availability:hover {
        border-color: rgba(219, 8, 8, .30) !important;
        background: rgba(219, 8, 8, .05) !important;
        color: var(--rojo-principal) !important;
    }

    #disponibilidad-page #save-availability,
    #disponibilidad-page #agregar-personalizado {
        border-radius: .7rem !important;
        background: linear-gradient(135deg, var(--rojo-principal), #c40808) !important;
        color: var(--blanco) !important;
        box-shadow: 0 7px 18px rgba(219, 8, 8, .18) !important;
    }

    #disponibilidad-page #save-availability:hover,
    #disponibilidad-page #agregar-personalizado:hover {
        background: linear-gradient(135deg, #c90808, var(--rojo-oscuro)) !important;
        transform: translateY(-1px);
        box-shadow: 0 9px 22px rgba(141, 7, 7, .22) !important;
    }

    #disponibilidad-page #save-availability:active,
    #disponibilidad-page #agregar-personalizado:active {
        transform: translateY(0);
    }

    #disponibilidad-page .border-t.bg-slate-50 {
        border-color: var(--gris-borde) !important;
        background: #f8fafc !important;
    }

    #personalizados-lista {
        min-height: 24px;
    }

    /* Caja informativa final */
    #disponibilidad-page > div:last-child {
        border-color: rgba(27, 58, 107, .14) !important;
        background: linear-gradient(135deg, rgba(27, 58, 107, .065), rgba(219, 8, 8, .025)) !important;
        box-shadow: none !important;
    }

    #disponibilidad-page > div:last-child h3 {
        color: var(--azul-oscuro) !important;
    }

    #disponibilidad-page > div:last-child p {
        color: #47566d !important;
    }

    #disponibilidad-page .text-slate-500,
    #disponibilidad-page .text-slate-400 {
        color: var(--gris-texto) !important;
    }

    #disponibilidad-page button,
    #disponibilidad-page select,
    #disponibilidad-page input {
        transition: all .18s ease;
    }

    /* Iconografía */
    #disponibilidad-page .ui-icon {
        width: 1rem;
        height: 1rem;
        flex: 0 0 auto;
        stroke-width: 1.9;
    }

    #disponibilidad-page .ui-icon-lg {
        width: 1.25rem;
        height: 1.25rem;
        flex: 0 0 auto;
        stroke-width: 1.8;
    }

    #disponibilidad-page .summary-icon {
        width: 42px;
        height: 42px;
        border-radius: .8rem;
    }

    #disponibilidad-page .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
    }

    #disponibilidad-page .info-icon-box {
        display: flex;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        align-items: center;
        justify-content: center;
        border-radius: .75rem;
        color: var(--azul-noche);
        background: rgba(27, 58, 107, .09);
        border: 1px solid rgba(27, 58, 107, .10);
    }

    #disponibilidad-page .page-heading-icon {
        display: flex;
        width: 44px;
        height: 44px;
        flex: 0 0 44px;
        align-items: center;
        justify-content: center;
        border-radius: .8rem;
        color: var(--blanco);
        background: linear-gradient(135deg, var(--azul-noche), var(--azul-oscuro));
        box-shadow: 0 8px 18px rgba(15, 39, 73, .18);
    }

    @media (max-width: 640px) {
        #disponibilidad-page {
            gap: 1rem;
        }

        #disponibilidad-page > div:first-child {
            padding: 1.1rem 1.15rem;
        }

        #disponibilidad-page #save-availability {
            width: 100%;
        }
    }
</style>


<div id="disponibilidad-page" class="space-y-6">

    {{-- HEADER --}}
    <div>
        <div class="flex items-center gap-3">
            <div class="page-heading-icon" aria-hidden="true">
                <svg class="ui-icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8 2v4M16 2v4"/>
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M3 10h18M8 15l2.2 2.2L16 12"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Disponibilidad</h1>
                <p class="mt-1 text-sm text-slate-500">Registra únicamente los días y rangos horarios en los que cada profesor puede trabajar.</p>
            </div>
        </div>
    </div>

    {{-- SELECTOR DE PROFESOR E INSTITUCIÓN --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:max-w-2xl">
            <div>
                <label for="profesor-select" class="mb-2 block text-sm font-medium text-slate-700">
                    Profesor
                </label>
                <select id="profesor-select" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    <option value="">Seleccionar profesor</option>
                </select>
            </div>
            <div>
                <label for="institucion-select" class="mb-2 block text-sm font-medium text-slate-700">
                    Institución
                </label>
                <select id="institucion-select" class="w-full rounded-lg border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                    <option value="colegio">Colegio</option>
                    <option value="academia">Academia</option>
                </select>
                <p class="mt-1 text-xs text-slate-500">La disponibilidad se guarda por separado para cada institución.</p>
            </div>
        </div>
    </div>

    {{-- RESUMEN DE DISPONIBILIDAD --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-4">
            <div class="flex items-center gap-3">
                <div class="summary-icon flex items-center justify-center bg-indigo-600 text-white" aria-hidden="true">
                    <svg class="ui-icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-indigo-600">Bloques disponibles</p>
                    <p id="availability-count" class="text-2xl font-bold text-indigo-900">0</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
            <div class="flex items-center gap-3">
                <div class="summary-icon flex items-center justify-center bg-emerald-600 text-white" aria-hidden="true">
                    <svg class="ui-icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 2v4M16 2v4"/>
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M3 10h18M8 15h.01M12 15h.01M16 15h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-emerald-600">Días disponibles</p>
                    <p id="availability-days" class="text-2xl font-bold text-emerald-900">0</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <div class="flex items-center gap-3">
                <div class="summary-icon flex items-center justify-center bg-slate-700 text-white" aria-hidden="true">
                    <svg class="ui-icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 2h6M12 14l3-3"/>
                        <circle cx="12" cy="14" r="7"/>
                        <path d="M18.4 8.6 20 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-600">Horas disponibles</p>
                    <p id="availability-hours" class="text-2xl font-bold text-slate-900">0</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ACLARACIÓN DE FLUJO --}}
    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">
        <div class="flex gap-3">
            <div class="text-lg">ℹ️</div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Disponibilidad ≠ clase asignada</p>
                <p class="mt-1 text-xs leading-5 text-slate-600">
                    Aquí solo indicas cuándo puede trabajar el profesor. El día y la hora reales de cada clase
                    se elegirán después en Asignaciones y se mostrarán en Horarios.
                </p>
            </div>
        </div>
    </div>

    {{-- PANEL PRINCIPAL --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

        {{-- CABECERA --}}
        <div class="border-b border-slate-200 px-5 py-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="font-semibold text-slate-900">Horarios disponibles</h2>
                    <p class="mt-1 text-xs text-slate-500">Marca los bloques en los que el profesor puede trabajar. Esta disponibilidad no crea clases automáticamente.</p>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="h-4 w-4 rounded bg-indigo-600"></span>
                        <span class="text-slate-600">Disponible</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-4 w-4 rounded border border-slate-300 bg-white"></span>
                        <span class="text-slate-600">No disponible</span>
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

        {{-- GRID - SOLO TABLET Y ESCRITORIO --}}
        <div class="hidden overflow-x-auto md:block">
            <div class="min-w-[850px] p-5">
                <div id="disponibilidad-grid" class="grid grid-cols-7 overflow-hidden rounded-xl border border-slate-200">

                    {{-- HORA --}}
                    <div class="border-b border-r border-slate-200 bg-slate-50 p-4 text-center">
                        <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Hora</span>
                    </div>

                    {{-- DÍAS --}}
                    @foreach ($diasDisponibilidad as $dia => $nombreDia)
                        <div class="{{ $dia < 6 ? 'border-r' : '' }} border-b border-slate-200 bg-slate-50 p-3 text-center">
                            <button type="button" class="select-day w-full rounded-lg px-2 py-2 transition hover:bg-indigo-100" data-dia="{{ $dia }}">
                                <span class="block text-sm font-semibold text-slate-700">{{ $nombreDia }}</span>
                                <span class="mt-1 block text-[10px] text-slate-400">Seleccionar</span>
                            </button>
                        </div>
                    @endforeach

                    {{-- FILAS DE HORA --}}
                    @foreach ($horasDisponibilidad as $hora)
                        <div class="border-b border-r border-slate-200 bg-slate-50 p-4 text-center">
                            <span class="text-xs font-medium text-slate-600">{{ $hora }}</span>
                        </div>
                        @for ($dia = 1; $dia <= 6; $dia++)
                            <button type="button" class="availability-slot border-b border-r border-slate-200 bg-white transition hover:bg-indigo-50" data-dia="{{ $dia }}" data-hora="{{ $hora }}" aria-label="{{ $hora }}"></button>
                        @endfor
                    @endforeach

                </div>
            </div>
        </div>

        {{-- VISTA MÓVIL --}}
        <div class="p-5 md:hidden">
            <div class="mb-4 flex gap-2 overflow-x-auto pb-1">
                @foreach ($diasDisponibilidad as $dia => $nombreDia)
                    <button type="button" class="mobile-day-tab flex-shrink-0 rounded-lg px-3 py-2 text-xs font-semibold transition {{ $dia === 1 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-600' }}" data-dia="{{ $dia }}">
                        {{ $nombreDia }}
                    </button>
                @endforeach
            </div>

            @foreach ($diasDisponibilidad as $dia => $nombreDia)
                <div class="mobile-day-panel {{ $dia === 1 ? '' : 'hidden' }}" data-dia-panel="{{ $dia }}">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-700">{{ $nombreDia }}</span>
                        <button type="button" class="select-day action-btn rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-600 transition hover:bg-indigo-100" data-dia="{{ $dia }}">
                            <svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 11 3 3L22 4"/><path d="M5 3h11a2 2 0 0 1 2 2v4M5 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-6"/></svg>
                            <span>Seleccionar todo el día</span>
                        </button>
                    </div>

                    <div class="divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200">
                        @foreach ($horasDisponibilidad as $hora)
                            <button type="button" class="availability-slot flex w-full items-center justify-between bg-white px-4 py-3 text-left transition hover:bg-indigo-50" data-dia="{{ $dia }}" data-hora="{{ $hora }}">
                                <span class="text-sm font-medium text-slate-600 [.selected_&]:text-white">{{ $hora }}</span>
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
                <button id="select-all-availability" type="button" class="action-btn rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                    <svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    <span>Seleccionar todo</span>
                </button>
                <button id="clear-availability" type="button" class="action-btn rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">
                    <svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>
                    <span>Limpiar</span>
                </button>
            </div>
            <button id="save-availability" type="button" class="action-btn rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/><path d="M17 21v-8H7v8M7 3v5h8"/></svg>
                <span>Guardar disponibilidad</span>
            </button>
        </div>
    </div>

    {{-- HORARIOS PERSONALIZADOS --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="font-semibold text-slate-900">Horarios personalizados</h2>
            <p class="mt-1 text-xs text-slate-500">Agrega rangos exactos cuando la disponibilidad no coincida con las horas completas del grid (ej. 09:15 a 10:05).</p>
        </div>

        <div class="p-5">
            <div class="grid grid-cols-1 items-end gap-3 sm:grid-cols-[1fr_1fr_1fr_auto]">
                <div>
                    <label for="personalizado-dia" class="mb-1.5 block text-xs font-medium text-slate-600">Día</label>
                    <select id="personalizado-dia" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                    </select>
                </div>

                <div>
                    <label for="personalizado-inicio" class="mb-1.5 block text-xs font-medium text-slate-600">Desde</label>
                    <input id="personalizado-inicio" type="time" step="60" value="09:15" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                </div>

                <div>
                    <label for="personalizado-fin" class="mb-1.5 block text-xs font-medium text-slate-600">Hasta</label>
                    <input id="personalizado-fin" type="time" step="60" value="10:05" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                </div>

                <button type="button" id="agregar-personalizado" class="action-btn rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                    <svg class="ui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                    <span>Agregar</span>
                </button>
            </div>

            <div id="personalizados-lista" class="mt-4 flex flex-wrap gap-2">
                <p id="personalizados-vacio" class="text-xs text-slate-400">Aún no agregaste horarios personalizados para este profesor / institución.</p>
            </div>
        </div>
    </div>

    {{-- INFORMACIÓN --}}
    <div class="rounded-xl border border-indigo-100 bg-indigo-50 p-5">
        <div class="flex gap-3">
            <div class="info-icon-box" aria-hidden="true">
                <svg class="ui-icon-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 16v-4M12 8h.01"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-indigo-900">¿Cómo funciona?</h3>
                <p class="mt-1 text-sm leading-6 text-indigo-800">
                    Selecciona un profesor y marca los bloques horarios en los que está disponible para dictar clases.
                    Esta información será utilizada posteriormente para generar los horarios automáticamente.
                    <br><br>
                    <strong>Consejo:</strong> Si un profesor tiene disponibilidad de 9:15 a 10:05, usa los "Horarios personalizados" en lugar de marcar el grid.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection