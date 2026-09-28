@extends('layouts.app')

@section('title', 'Historial | Next Level')

@section('page-title', 'Historial')

@section('content')

<style>
    @keyframes historialEntrada {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .historial-page {
        animation: historialEntrada .35s ease-out both;
    }

    .historial-card {
        border: 1px solid rgba(27, 58, 107, .08);
        box-shadow: 0 6px 24px rgba(15, 39, 73, .055);
    }

    .historial-select,
    .historial-input {
        min-height: 44px;
    }

    .historial-native-select {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        opacity: 0 !important;
        pointer-events: none !important;
        overflow: hidden !important;
    }

    .historial-filter-select {
        position: relative;
    }

    .historial-filter-trigger {
        position: relative;
        display: flex;
        min-height: 54px;
        width: 100%;
        align-items: center;
        gap: .75rem;
        border: 1px solid #dbe5f0;
        border-radius: 1rem;
        background: #fff;
        padding: .55rem 2.75rem .55rem .6rem;
        text-align: left;
        color: #0F2749;
        box-shadow: 0 8px 24px rgba(15, 39, 73, .04);
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .historial-filter-trigger:hover {
        border-color: rgba(27, 58, 107, .28);
        box-shadow: 0 12px 28px rgba(15, 39, 73, .07);
    }

    .historial-filter-select.open .historial-filter-trigger,
    .historial-filter-trigger:focus-visible {
        border-color: #1B3A6B;
        box-shadow: 0 0 0 3px rgba(27, 58, 107, .10), 0 14px 34px rgba(15, 39, 73, .10);
        outline: none;
    }

    .historial-filter-icon {
        display: inline-flex;
        height: 38px;
        width: 38px;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        border-radius: .85rem;
        background: #eef4fb;
        color: #1B3A6B;
    }

    .historial-filter-label {
        display: block;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #8da0ba;
    }

    .historial-filter-value {
        display: block;
        overflow: hidden;
        color: #172b49;
        font-size: .9rem;
        font-weight: 800;
        line-height: 1.2;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .historial-filter-chevron {
        position: absolute;
        right: 1rem;
        top: 50%;
        height: 1rem;
        width: 1rem;
        transform: translateY(-50%);
        color: #64748b;
        transition: transform .2s ease;
    }

    .historial-filter-select.open .historial-filter-chevron {
        transform: translateY(-50%) rotate(180deg);
    }

    .historial-filter-panel {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + .5rem);
        z-index: 90;
        display: none;
        overflow: hidden;
        border: 1px solid #dbe5f0;
        border-radius: 1rem;
        background: #fff;
        padding: .45rem;
        box-shadow: 0 22px 50px rgba(15, 39, 73, .16);
    }

    .historial-filter-select.open .historial-filter-panel {
        display: block;
    }

    .historial-filter-option {
        display: flex;
        min-height: 44px;
        width: 100%;
        align-items: center;
        gap: .7rem;
        border: 0;
        border-radius: .75rem;
        background: transparent;
        padding: .55rem .75rem;
        color: #172b49;
        cursor: pointer;
        font-size: .9rem;
        font-weight: 750;
        text-align: left;
        transition: background .15s ease, color .15s ease;
    }

    .historial-filter-option:hover,
    .historial-filter-option.selected {
        background: #eef4fb;
        color: #0F2749;
    }

    .historial-filter-option-icon {
        display: inline-flex;
        height: 30px;
        width: 30px;
        flex: 0 0 30px;
        align-items: center;
        justify-content: center;
        border-radius: .65rem;
        background: #f0f5fb;
        color: #1B3A6B;
    }

    .historial-filter-check {
        margin-left: auto;
        height: 1rem;
        width: 1rem;
        color: #1B3A6B;
        opacity: 0;
    }

    .historial-filter-option.selected .historial-filter-check {
        opacity: 1;
    }

    .historial-date-field {
        position: relative;
        display: flex;
        min-height: 54px;
        width: 100%;
        align-items: center;
        gap: .75rem;
        border: 1px solid #dbe5f0;
        border-radius: 1rem;
        background: #fff;
        padding: .55rem .9rem .55rem .6rem;
        box-shadow: 0 8px 24px rgba(15, 39, 73, .04);
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .historial-date-field:focus-within {
        border-color: #1B3A6B;
        box-shadow: 0 0 0 3px rgba(27, 58, 107, .10), 0 14px 34px rgba(15, 39, 73, .10);
    }

    .historial-date-field:hover {
        border-color: rgba(27, 58, 107, .28);
        box-shadow: 0 12px 28px rgba(15, 39, 73, .07);
    }

    .historial-date-icon {
        display: inline-flex;
        height: 38px;
        width: 38px;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        border-radius: .85rem;
        background: #eef4fb;
        color: #1B3A6B;
    }

    .historial-date-label {
        display: block;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #8da0ba;
    }

    .historial-date-input {
        min-height: 21px;
        width: 100%;
        border: 0;
        background: transparent;
        color: #172b49;
        font-size: .9rem;
        font-weight: 800;
        line-height: 1.2;
        outline: none;
    }

    .historial-date-input::-webkit-calendar-picker-indicator {
        cursor: pointer;
        opacity: .72;
    }

    .historial-filter-action {
        min-height: 54px;
        border-radius: 1rem;
        box-shadow: 0 10px 25px rgba(27, 58, 107, .14);
    }

    .historial-modal-backdrop {
        background: rgba(15, 23, 42, .62);
        backdrop-filter: blur(4px);
    }

    .historial-scroll::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }

    .historial-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    .historial-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
</style>

<div id="historial-page" class="historial-page space-y-6">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <section
        class="relative overflow-hidden rounded-[26px] p-6 md:p-8"
        style="
            background:
                linear-gradient(
                    135deg,
                    #0F2749 0%,
                    #1B3A6B 58%,
                    #8D0707 100%
                );
            box-shadow: 0 20px 55px rgba(15,39,73,.20);
        "
    >

        <div
            class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full"
            style="
                background:
                    radial-gradient(
                        circle,
                        rgba(219,8,8,.30),
                        transparent 68%
                    );
            "
        ></div>

        <div
            class="pointer-events-none absolute -bottom-28 -left-20 h-72 w-72 rounded-full"
            style="
                background:
                    radial-gradient(
                        circle,
                        rgba(255,255,255,.12),
                        transparent 70%
                    );
            "
        ></div>

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <div class="mb-3 flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl border border-white/15 bg-white/10 backdrop-blur-sm"
                    >
                        <svg
                            class="h-5 w-5 text-white"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M3 12a9 9 0 1 0 3-6.7"/>
                            <path d="M3 3v6h6"/>
                            <path d="M12 7v5l3 2"/>
                        </svg>
                    </div>

                    <span class="text-xs font-bold uppercase tracking-[0.18em] text-white/55">
                        Auditoría
                    </span>

                </div>

                <h1 class="text-2xl font-extrabold tracking-tight text-white md:text-4xl">
                    Historial de cambios
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-white/65 md:text-base">
                    Consulta las modificaciones realizadas en los horarios,
                    revisa versiones anteriores y controla la trazabilidad del sistema.
                </p>

            </div>

            <div
                class="inline-flex w-fit items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-xs font-semibold text-white backdrop-blur-sm"
            >
                <span
                    class="h-2 w-2 rounded-full"
                    style="background:#86EFAC;"
                ></span>

                Datos del backend
            </div>

        </div>

    </section>


    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="historial-card rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total de cambios
                    </p>

                    <p
                        id="historial-total-cambios"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>
                </div>

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl"
                    style="background:#E0E7FF;"
                >
                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#1B3A6B"
                        stroke-width="2"
                    >
                        <path d="M4 6h16M4 12h16M4 18h10"/>
                    </svg>
                </div>

            </div>

        </div>


        <div class="historial-card rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Creaciones
                    </p>

                    <p
                        id="historial-total-creaciones"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>
                </div>

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl"
                    style="background:#DCFCE7;"
                >
                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#15803D"
                        stroke-width="2"
                    >
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                </div>

            </div>

        </div>


        <div class="historial-card rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Actualizaciones
                    </p>

                    <p
                        id="historial-total-actualizaciones"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>
                </div>

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl"
                    style="background:#DBEAFE;"
                >
                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#1D4ED8"
                        stroke-width="2"
                    >
                        <path d="M4 20h4L19 9l-4-4L4 16v4z"/>
                        <path d="m13.5 6.5 4 4"/>
                    </svg>
                </div>

            </div>

        </div>


        <div class="historial-card rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Eliminaciones
                    </p>

                    <p
                        id="historial-total-eliminaciones"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>
                </div>

                <div
                    class="flex h-12 w-12 items-center justify-center rounded-xl"
                    style="background:#FEE2E2;"
                >
                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#B91C1C"
                        stroke-width="2"
                    >
                        <path d="M3 6h18"/>
                        <path d="M8 6V4h8v2"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                    </svg>
                </div>

            </div>

        </div>

    </section>


    {{-- =========================================================
         FILTROS
    ========================================================== --}}
    <section class="historial-card rounded-2xl bg-white p-5">

        <div class="mb-5">

            <h2
                class="text-lg font-extrabold"
                style="color:#0F2749;"
            >
                Filtros
            </h2>

            <p class="mt-1 text-sm text-slate-400">
                Filtra los registros utilizando los parámetros disponibles en el backend.
            </p>

        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">

            <div>

                <label
                    for="historial-filtro-accion"
                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500"
                >
                    Acción
                </label>

                <select
                    id="historial-filtro-accion"
                    class="historial-native-select"
                >
                    <option value="">Todas</option>
                    <option value="crear">Creación</option>
                    <option value="actualizar">Actualización</option>
                    <option value="eliminar">Eliminación</option>
                    <option value="restaurar">Restauración</option>
                    <option value="revertir">Reversión</option>
                </select>

                <div class="historial-filter-select" data-historial-select="historial-filtro-accion">
                    <button type="button" class="historial-filter-trigger" aria-expanded="false">
                        <span class="historial-filter-icon">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 6h16"/>
                                <path d="M7 12h10"/>
                                <path d="M10 18h4"/>
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="historial-filter-label">Acción</span>
                            <span class="historial-filter-value">Todas</span>
                        </span>
                        <svg class="historial-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="historial-filter-panel">
                        <button type="button" class="historial-filter-option selected" data-value="">
                            <span class="historial-filter-option-icon">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                            <span>Todas</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="historial-filter-option" data-value="crear">
                            <span class="historial-filter-option-icon">C</span>
                            <span>Creación</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="historial-filter-option" data-value="actualizar">
                            <span class="historial-filter-option-icon">A</span>
                            <span>Actualización</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="historial-filter-option" data-value="eliminar">
                            <span class="historial-filter-option-icon">E</span>
                            <span>Eliminación</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="historial-filter-option" data-value="restaurar">
                            <span class="historial-filter-option-icon">R</span>
                            <span>Restauración</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="historial-filter-option" data-value="revertir">
                            <span class="historial-filter-option-icon">V</span>
                            <span>Reversión</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>


            <div>

                <label
                    for="historial-filtro-institucion"
                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500"
                >
                    Institución
                </label>

                <select
                    id="historial-filtro-institucion"
                    class="historial-native-select"
                >
                    <option value="">Todas</option>
                    <option value="colegio">Colegio</option>
                    <option value="academia">Academia</option>
                </select>

                <div class="historial-filter-select" data-historial-select="historial-filtro-institucion">
                    <button type="button" class="historial-filter-trigger" aria-expanded="false">
                        <span class="historial-filter-icon">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M3 21h18"/>
                                <path d="M5 21V7l7-4 7 4v14"/>
                                <path d="M9 21v-6h6v6"/>
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="historial-filter-label">Institución</span>
                            <span class="historial-filter-value">Todas</span>
                        </span>
                        <svg class="historial-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="historial-filter-panel">
                        <button type="button" class="historial-filter-option selected" data-value="">
                            <span class="historial-filter-option-icon">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                            <span>Todas</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="historial-filter-option" data-value="colegio">
                            <span class="historial-filter-option-icon">C</span>
                            <span>Colegio</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="historial-filter-option" data-value="academia">
                            <span class="historial-filter-option-icon">A</span>
                            <span>Academia</span>
                            <svg class="historial-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>


            <div>

                <label
                    for="historial-fecha-inicio"
                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500"
                >
                    Desde
                </label>

                <label class="historial-date-field" for="historial-fecha-inicio">
                    <span class="historial-date-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect x="3" y="4" width="18" height="18" rx="3"/>
                            <path d="M3 10h18"/>
                        </svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="historial-date-label">Desde</span>
                        <input
                            type="date"
                            id="historial-fecha-inicio"
                            class="historial-date-input"
                        >
                    </span>
                </label>

            </div>


            <div>

                <label
                    for="historial-fecha-fin"
                    class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500"
                >
                    Hasta
                </label>

                <label class="historial-date-field" for="historial-fecha-fin">
                    <span class="historial-date-icon">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M8 2v4"/>
                            <path d="M16 2v4"/>
                            <rect x="3" y="4" width="18" height="18" rx="3"/>
                            <path d="M3 10h18"/>
                        </svg>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="historial-date-label">Hasta</span>
                        <input
                            type="date"
                            id="historial-fecha-fin"
                            class="historial-date-input"
                        >
                    </span>
                </label>

            </div>


            <div class="flex items-end gap-2">

                <button
                    type="button"
                    id="historial-btn-filtrar"
                    class="historial-filter-action inline-flex flex-1 items-center justify-center gap-2 px-4 text-sm font-bold text-white transition hover:opacity-90"
                    style="background:#1B3A6B;"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M4 5h16l-6 7v5l-4 2v-7z"/>
                    </svg>

                    Filtrar
                </button>

                <button
                    type="button"
                    id="historial-btn-limpiar"
                    class="historial-filter-action inline-flex items-center justify-center border border-slate-200 px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                    title="Limpiar filtros"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M3 6h18"/>
                        <path d="M8 6V4h8v2"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                    </svg>
                </button>

            </div>

        </div>

    </section>


    {{-- =========================================================
         TABLA
    ========================================================== --}}
    <section class="historial-card overflow-hidden rounded-2xl bg-white">

        <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2
                    class="text-lg font-extrabold"
                    style="color:#0F2749;"
                >
                    Registros
                </h2>

                <p
                    id="historial-resumen"
                    class="mt-1 text-sm text-slate-400"
                >
                    Cargando historial...
                </p>

            </div>

            <button
                type="button"
                id="historial-btn-recargar"
                class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
            >
                <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M20 11a8 8 0 1 0-2.34 5.66"/>
                    <path d="M20 4v7h-7"/>
                </svg>

                Actualizar
            </button>

        </div>


        <div class="historial-scroll overflow-x-auto">

            <table class="min-w-[1100px] w-full">

                <thead class="bg-slate-50">

                    <tr class="border-b border-slate-200">

                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Usuario
                        </th>

                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Acción
                        </th>

                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Horario
                        </th>

                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Profesor
                        </th>

                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Curso
                        </th>

                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Grado
                        </th>

                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Aula
                        </th>

                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Fecha
                        </th>

                        <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody
                    id="historial-body"
                    class="divide-y divide-slate-100"
                >
                    <tr>
                        <td
                            colspan="9"
                            class="px-6 py-12 text-center text-sm text-slate-400"
                        >
                            Cargando historial...
                        </td>
                    </tr>
                </tbody>

            </table>

        </div>

    </section>

</div>


{{-- =========================================================
     MODAL DETALLE
========================================================== --}}
<div
    id="historial-modal-detalle"
    class="historial-modal-backdrop fixed inset-0 z-[100] hidden items-center justify-center p-4"
>

    <div
        class="max-h-[90vh] w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl"
    >

        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

            <div>

                <h3
                    id="historial-modal-titulo"
                    class="text-lg font-extrabold"
                    style="color:#0F2749;"
                >
                    Detalle del cambio
                </h3>

                <p
                    id="historial-modal-subtitulo"
                    class="mt-1 text-xs text-slate-400"
                >
                    Información del registro
                </p>

            </div>

            <button
                type="button"
                id="historial-modal-cerrar"
                class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>

        </div>

        <div
            id="historial-modal-contenido"
            class="historial-scroll max-h-[70vh] overflow-y-auto p-5"
        ></div>

        <div class="flex justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">

            <button
                type="button"
                id="historial-modal-revertir"
                class="hidden items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold text-white transition hover:opacity-90"
                style="background:#DB0808;"
            >
                Revertir cambio
            </button>

            <button
                type="button"
                id="historial-modal-cerrar-footer"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-100"
            >
                Cerrar
            </button>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL CONFIRMACIÓN
========================================================== --}}
<div
    id="historial-modal-confirmacion"
    class="historial-modal-backdrop fixed inset-0 z-[110] hidden items-center justify-center p-4"
>

    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">

        <div
            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full"
            style="background:#FEE2E2;"
        >
            <svg
                class="h-6 w-6"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#B91C1C"
                stroke-width="2"
            >
                <path d="M12 9v4"/>
                <path d="M12 17h.01"/>
                <path d="M10.3 3.6 2.6 17a2 2 0 0 0 1.7 3h15.4a2 2 0 0 0 1.7-3L13.7 3.6a2 2 0 0 0-3.4 0z"/>
            </svg>
        </div>

        <div class="mt-4 text-center">

            <h3
                class="text-lg font-extrabold"
                style="color:#0F2749;"
            >
                ¿Revertir este cambio?
            </h3>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                El backend intentará regresar el horario a su estado anterior.
                Esta acción también quedará registrada en el historial.
            </p>

        </div>

        <div class="mt-5">

            <label
                for="historial-motivo-reversion"
                class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500"
            >
                Motivo
            </label>

            <textarea
                id="historial-motivo-reversion"
                rows="3"
                class="w-full resize-none rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none transition focus:border-red-400 focus:ring-4 focus:ring-red-100"
                placeholder="Ej. Corrección de horario..."
            ></textarea>

        </div>

        <div class="mt-6 grid grid-cols-2 gap-3">

            <button
                type="button"
                id="historial-confirmar-cancelar"
                class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
            >
                Cancelar
            </button>

            <button
                type="button"
                id="historial-confirmar-revertir"
                class="rounded-xl px-4 py-2.5 text-sm font-bold text-white transition hover:opacity-90"
                style="background:#DB0808;"
            >
                Revertir
            </button>

        </div>

    </div>

</div>


{{-- =========================================================
     TOAST
========================================================== --}}
<div
    id="historial-toast"
    class="fixed bottom-5 right-5 z-[120] hidden max-w-sm rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-xl"
></div>

@endsection
