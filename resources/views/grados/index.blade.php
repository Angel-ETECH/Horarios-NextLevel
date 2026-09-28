@extends('layouts.app')

@section('title', 'Grados - Next Level School')
@section('page-title', 'Grados')

@section('content')

<style>
    .grado-card {
        border: 1px solid rgba(15, 39, 73, .08);
        box-shadow: 0 8px 28px rgba(15, 39, 73, .05);
    }

    .grado-card-hover {
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .grado-card-hover:hover {
        transform: translateY(-2px);
        border-color: rgba(27, 58, 107, .15);
        box-shadow: 0 12px 34px rgba(15, 39, 73, .08);
    }

    .grado-stat-card {
        min-height: 126px;
    }

    .grado-stat-blue {
        border-top: 4px solid #1B3A6B;
    }

    .grado-stat-red {
        border-top: 4px solid #DB0808;
    }

    .grado-stat-gray {
        border-top: 4px solid #94A3B8;
    }

    .grado-input:focus {
        border-color: #1B3A6B;
        box-shadow: 0 0 0 3px rgba(27, 58, 107, .08);
    }

    .grado-native-select {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        opacity: 0 !important;
        pointer-events: none !important;
        overflow: hidden !important;
    }

    .grado-filter-select {
        position: relative;
    }

    .grado-filter-trigger {
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
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }

    .grado-filter-trigger:hover {
        border-color: rgba(27, 58, 107, .28);
        box-shadow: 0 12px 28px rgba(15, 39, 73, .07);
    }

    .grado-filter-select.open .grado-filter-trigger,
    .grado-filter-trigger:focus-visible {
        border-color: #1B3A6B;
        box-shadow: 0 0 0 3px rgba(27, 58, 107, .10), 0 14px 34px rgba(15, 39, 73, .10);
        outline: none;
    }

    .grado-filter-icon {
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

    .grado-filter-label {
        display: block;
        font-size: .62rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #8da0ba;
    }

    .grado-filter-value {
        display: block;
        overflow: hidden;
        color: #172b49;
        font-size: .9rem;
        font-weight: 800;
        line-height: 1.2;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .grado-filter-chevron {
        position: absolute;
        right: 1rem;
        top: 50%;
        height: 1rem;
        width: 1rem;
        transform: translateY(-50%);
        color: #64748b;
        transition: transform .2s ease;
    }

    .grado-filter-select.open .grado-filter-chevron {
        transform: translateY(-50%) rotate(180deg);
    }

    .grado-filter-panel {
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

    .grado-filter-select.open .grado-filter-panel {
        display: block;
    }

    .grado-filter-option {
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

    .grado-filter-option:hover,
    .grado-filter-option.selected {
        background: #eef4fb;
        color: #0F2749;
    }

    .grado-filter-option-icon {
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

    .grado-filter-check {
        margin-left: auto;
        height: 1rem;
        width: 1rem;
        color: #1B3A6B;
        opacity: 0;
    }

    .grado-filter-option.selected .grado-filter-check {
        opacity: 1;
    }

    .custom-select-wrapper {
        position: relative;
    }

    .custom-select-trigger {
        width: 100%;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        border: 1px solid #e2e8f0;
        border-radius: .75rem;
        background: #fff;
        padding: .65rem .9rem;
        font-size: .875rem;
        color: #334155;
        cursor: pointer;
        transition: border-color .2s ease, box-shadow .2s ease;
    }

    .custom-select-trigger:hover {
        border-color: #cbd5e1;
    }

    .custom-select-wrapper.open .custom-select-trigger {
        border-color: #1B3A6B;
        box-shadow: 0 0 0 3px rgba(27, 58, 107, .08);
    }

    .custom-select-panel {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 6px);
        z-index: 80;
        display: none;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: .85rem;
        background: white;
        box-shadow: 0 18px 45px rgba(15, 39, 73, .14);
    }

    .custom-select-wrapper.open .custom-select-panel {
        display: block;
    }

    .custom-select-option {
        width: 100%;
        border: 0;
        background: white;
        padding: .7rem .9rem;
        text-align: left;
        font-size: .875rem;
        color: #334155;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }

    .custom-select-option:hover {
        background: #f8fafc;
        color: #0F2749;
    }

    .custom-select-option.selected {
        background: rgba(27, 58, 107, .07);
        color: #0F2749;
        font-weight: 600;
    }

    .grado-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .28rem .6rem;
        font-size: .72rem;
        font-weight: 700;
    }
</style>

<div class="space-y-6">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex items-center gap-4">

            <div
                class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl"
                style="
                    background:
                        linear-gradient(
                            145deg,
                            rgba(27,58,107,.12),
                            rgba(219,8,8,.05)
                        );
                    color:#1B3A6B;
                "
            >
                <svg
                    class="h-7 w-7"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 19.5V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14.5"/>
                    <path d="M8 7h8"/>
                    <path d="M8 11h8"/>
                    <path d="M8 15h5"/>
                    <path d="M3 21h18"/>
                </svg>
            </div>


            <div>

                <h1
                    class="text-2xl font-extrabold tracking-tight sm:text-3xl"
                    style="color:#0F2749;"
                >
                    Grados
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Administra grados, secciones, turnos y capacidades académicas.
                </p>

            </div>
        </div>


        <button
            type="button"
            id="open-grado-modal"
            class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5"
            style="
                background:
                    linear-gradient(
                        135deg,
                        #1B3A6B,
                        #0F2749
                    );
            "
        >
            <svg
                class="h-5 w-5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 8v8"/>
                <path d="M8 12h8"/>
            </svg>

            Agregar grado
        </button>

    </div>



    {{-- =========================================================
         MENSAJE
    ========================================================== --}}
    <div
        id="grado-mensaje"
        class="hidden rounded-xl border px-4 py-3 text-sm"
    ></div>



    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="grado-card grado-stat-card grado-stat-blue grado-card-hover rounded-2xl bg-white p-5">
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total de grados
                    </p>

                    <p
                        id="total-grados"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Registrados en el sistema
                    </p>
                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl"
                    style="
                        background:
                            linear-gradient(
                                145deg,
                                #E8EEFA,
                                #DCE7F7
                            );
                        color:#1B3A6B;
                    "
                >
                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M4 19.5V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14.5"/>
                        <path d="M8 7h8M8 11h8M8 15h5"/>
                    </svg>
                </div>

            </div>
        </div>


        <div class="grado-card grado-stat-card grado-stat-red grado-card-hover rounded-2xl bg-white p-5">
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Grados activos
                    </p>

                    <p
                        id="grados-activos"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#DB0808;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Disponibles para asignación
                    </p>
                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl"
                    style="
                        background:
                            linear-gradient(
                                145deg,
                                rgba(219,8,8,.10),
                                rgba(141,7,7,.05)
                            );
                        color:#DB0808;
                    "
                >
                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M12 3 19 6v5c0 4.7-2.8 8-7 10-4.2-2-7-5.3-7-10V6z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>

            </div>
        </div>


        <div class="grado-card grado-stat-card grado-stat-blue grado-card-hover rounded-2xl bg-white p-5">
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total estudiantes
                    </p>

                    <p
                        id="total-estudiantes-grados"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#1B3A6B;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Matriculados actualmente
                    </p>
                </div>

                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl"
                    style="background:#E8EEF7; color:#1B3A6B;"
                >
                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>

            </div>
        </div>


        <div class="grado-card grado-stat-card grado-stat-gray grado-card-hover rounded-2xl bg-white p-5">
            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Capacidad total
                    </p>

                    <p
                        id="capacidad-total-grados"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#64748B;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Cupos disponibles por grado
                    </p>
                </div>

                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M3 3h18v18H3z"/>
                        <path d="M8 8h8v8H8z"/>
                    </svg>
                </div>

            </div>
        </div>

    </div>



    {{-- =========================================================
         FILTROS
    ========================================================== --}}
    <div class="grado-card rounded-2xl bg-white p-5">

        <div class="mb-5">
            <h2
                class="font-bold"
                style="color:#0F2749;"
            >
                Buscar y filtrar
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Consulta por nombre, código, grado, sección, nivel, turno, año académico o estado.
            </p>
        </div>


        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">

            {{-- BUSCADOR --}}
            <div class="xl:col-span-2">

                <label
                    for="buscar-grado"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Buscar
                </label>

                <div class="relative">

                    <svg
                        class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    <input
                        id="buscar-grado"
                        type="text"
                        placeholder="Código, grado, sección..."
                        class="grado-input w-full rounded-xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm outline-none"
                    >

                </div>
            </div>


            {{-- NIVEL --}}
            <div>

                <label
                    for="filtro-grado-nivel"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Nivel
                </label>

                <select
                    id="filtro-grado-nivel"
                    class="grado-native-select"
                >
                    <option value="">Todos</option>
                    <option value="primaria">Primaria</option>
                    <option value="secundaria">Secundaria</option>
                    <option value="academia">Academia</option>
                </select>

                <div class="grado-filter-select" data-filter-select="filtro-grado-nivel">
                    <button type="button" class="grado-filter-trigger" aria-expanded="false">
                        <span class="grado-filter-icon">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m4 19 8-14 8 14"/>
                                <path d="M8.5 13h7"/>
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="grado-filter-label">Nivel</span>
                            <span class="grado-filter-value">Todos</span>
                        </span>
                        <svg class="grado-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="grado-filter-panel">
                        <button type="button" class="grado-filter-option selected" data-value="">
                            <span class="grado-filter-option-icon">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                            <span>Todos</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="primaria">
                            <span class="grado-filter-option-icon">P</span>
                            <span>Primaria</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="secundaria">
                            <span class="grado-filter-option-icon">S</span>
                            <span>Secundaria</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="academia">
                            <span class="grado-filter-option-icon">A</span>
                            <span>Academia</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>


            {{-- TURNO --}}
            <div>

                <label
                    for="filtro-grado-turno"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Turno
                </label>

                <select
                    id="filtro-grado-turno"
                    class="grado-native-select"
                >
                    <option value="">Todos</option>
                    <option value="mañana">Mañana</option>
                    <option value="tarde">Tarde</option>
                    <option value="noche">Noche</option>
                    <option value="completo">Completo</option>
                </select>

                <div class="grado-filter-select" data-filter-select="filtro-grado-turno">
                    <button type="button" class="grado-filter-trigger" aria-expanded="false">
                        <span class="grado-filter-icon">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="8"/>
                                <path d="M12 8v4l3 2"/>
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="grado-filter-label">Turno</span>
                            <span class="grado-filter-value">Todos</span>
                        </span>
                        <svg class="grado-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="grado-filter-panel">
                        <button type="button" class="grado-filter-option selected" data-value="">
                            <span class="grado-filter-option-icon">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                            <span>Todos</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="mañana">
                            <span class="grado-filter-option-icon">M</span>
                            <span>Mañana</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="tarde">
                            <span class="grado-filter-option-icon">T</span>
                            <span>Tarde</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="noche">
                            <span class="grado-filter-option-icon">N</span>
                            <span>Noche</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="completo">
                            <span class="grado-filter-option-icon">C</span>
                            <span>Completo</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>


            {{-- ESTADO --}}
            <div>

                <label
                    for="filtro-grado-estado"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Estado
                </label>

                <select
                    id="filtro-grado-estado"
                    class="grado-native-select"
                >
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>

                <div class="grado-filter-select" data-filter-select="filtro-grado-estado">
                    <button type="button" class="grado-filter-trigger" aria-expanded="false">
                        <span class="grado-filter-icon">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 13c0 5-3.5 7-8 8-4.5-1-8-3-8-8V5l8-3 8 3v8Z"/>
                                <path d="m9 12 2 2 4-4"/>
                            </svg>
                        </span>
                        <span class="min-w-0">
                            <span class="grado-filter-label">Estado</span>
                            <span class="grado-filter-value">Todos</span>
                        </span>
                        <svg class="grado-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>

                    <div class="grado-filter-panel">
                        <button type="button" class="grado-filter-option selected" data-value="">
                            <span class="grado-filter-option-icon">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </span>
                            <span>Todos</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="1">
                            <span class="grado-filter-option-icon">A</span>
                            <span>Activo</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                        <button type="button" class="grado-filter-option" data-value="0">
                            <span class="grado-filter-option-icon">I</span>
                            <span>Inactivo</span>
                            <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                        </button>
                    </div>
                </div>

            </div>


            {{-- AÑO --}}
            <div>

                <label
                    for="filtro-grado-anio"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Año académico
                </label>

                <input
                    id="filtro-grado-anio"
                    type="number"
                    min="2020"
                    max="2100"
                    placeholder="Ej. 2026"
                    class="grado-input w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none"
                >

            </div>

        </div>


        <div class="mt-4 flex justify-end">

            <button
                type="button"
                id="limpiar-filtros-grado"
                class="inline-flex items-center gap-2 text-sm font-semibold transition hover:opacity-70"
                style="color:#DB0808;"
            >
                <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                </svg>

                Limpiar filtros
            </button>

        </div>

    </div>



    {{-- =========================================================
         TABLA
    ========================================================== --}}
    <div class="grado-card overflow-hidden rounded-2xl bg-white">

        <div
            class="flex flex-col gap-2 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            style="background:linear-gradient(180deg,#FAFBFD,#FFFFFF);"
        >

            <div>
                <h2
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    Lista de grados
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Registros académicos disponibles en el sistema.
                </p>
            </div>


            <div class="text-xs text-slate-500">
                Resultados:
                <span
                    id="resultados-grados"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    0
                </span>
            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-slate-200">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Código
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Grado / Sección
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Nivel
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Estudiantes
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Año
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Turno
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Estado
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="grados-body"
                    class="divide-y divide-slate-100 bg-white"
                >

                    <tr>
                        <td
                            colspan="8"
                            class="px-6 py-12 text-center text-sm text-slate-400"
                        >
                            Cargando grados...
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>


        <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <p class="text-xs text-slate-500">

                Mostrando

                <span
                    id="resultados-grados-footer"
                    class="font-semibold text-slate-700"
                >
                    0
                </span>

                resultados

            </p>


            <div
                id="grados-paginacion"
                class="flex flex-wrap items-center gap-2"
            ></div>

        </div>

    </div>

</div>



{{-- =========================================================
     MODAL CREAR / EDITAR
========================================================== --}}
<div
    id="grado-modal"
    class="fixed inset-0 z-[100] hidden items-center justify-center p-4"
>

    <div
        id="grado-modal-overlay"
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
    ></div>


    <div
        class="relative z-10 max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl"
    >

        <div
            class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4"
        >

            <div>

                <p
                    class="text-[10px] font-bold uppercase tracking-[0.18em]"
                    style="color:#DB0808;"
                >
                    Gestión académica
                </p>

                <h2
                    id="grado-modal-title"
                    class="mt-1 text-xl font-extrabold"
                    style="color:#0F2749;"
                >
                    Nuevo grado
                </h2>

            </div>


            <button
                type="button"
                id="close-grado-modal"
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


        <form
            id="grado-form"
            class="p-5"
        >

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                {{-- CÓDIGO --}}
                <div>

                    <label
                        for="grado-codigo"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Código
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="grado-codigo"
                        name="codigo"
                        type="text"
                        maxlength="20"
                        placeholder="Ej. GR-PRI-1A"
                        required
                        class="grado-input w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none"
                    >

                    <p class="mt-1 text-xs text-slate-400">
                        Máximo 20 caracteres.
                    </p>

                </div>


                {{-- NIVEL --}}
                <div>

                    <label
                        for="grado-nivel"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Nivel
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="grado-nivel"
                        name="nivel"
                        required
                        class="grado-native-select"
                    >
                        <option value="">Seleccionar nivel</option>
                        <option value="primaria">Primaria</option>
                        <option value="secundaria">Secundaria</option>
                        <option value="academia">Academia</option>
                    </select>

                    <div class="grado-filter-select" data-filter-select="grado-nivel">
                        <button type="button" class="grado-filter-trigger" aria-expanded="false">
                            <span class="grado-filter-icon">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="m4 19 8-14 8 14"/>
                                    <path d="M8.5 13h7"/>
                                </svg>
                            </span>
                            <span class="min-w-0">
                                <span class="grado-filter-label">Nivel</span>
                                <span class="grado-filter-value">Seleccionar nivel</span>
                            </span>
                            <svg class="grado-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div class="grado-filter-panel">
                            <button type="button" class="grado-filter-option selected" data-value="">
                                <span class="grado-filter-option-icon">?</span>
                                <span>Seleccionar nivel</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                            <button type="button" class="grado-filter-option" data-value="primaria">
                                <span class="grado-filter-option-icon">P</span>
                                <span>Primaria</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                            <button type="button" class="grado-filter-option" data-value="secundaria">
                                <span class="grado-filter-option-icon">S</span>
                                <span>Secundaria</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                            <button type="button" class="grado-filter-option" data-value="academia">
                                <span class="grado-filter-option-icon">A</span>
                                <span>Academia</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>


                {{-- GRADO --}}
                <div>

                    <label
                        for="grado-grado"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Grado
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="grado-grado"
                        name="grado"
                        type="text"
                        maxlength="20"
                        placeholder="Ej. 1ro"
                        required
                        class="grado-input w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none"
                    >

                </div>


                {{-- SECCIÓN --}}
                <div>

                    <label
                        for="grado-seccion"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Sección
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="grado-seccion"
                        name="seccion"
                        type="text"
                        maxlength="5"
                        placeholder="Ej. A"
                        required
                        class="grado-input w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none"
                    >

                </div>


                {{-- CAPACIDAD --}}
                <div>

                    <label
                        for="grado-capacidad"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Capacidad máxima
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="grado-capacidad"
                        name="capacidad_maxima"
                        type="number"
                        min="1"
                        max="30"
                        value="30"
                        required
                        class="grado-input w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none"
                    >

                </div>


                {{-- ESTUDIANTES --}}
                <div>

                    <label
                        for="grado-estudiantes"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Número de estudiantes
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="grado-estudiantes"
                        name="numero_estudiantes"
                        type="number"
                        min="0"
                        max="30"
                        value="0"
                        required
                        class="grado-input w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none"
                    >

                </div>


                {{-- AÑO ACADÉMICO --}}
                <div>

                    <label
                        for="grado-anio"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Año académico
                        <span class="text-red-500">*</span>
                    </label>

                    <input
                        id="grado-anio"
                        name="año_academico"
                        type="number"
                        min="{{ now()->year - 1 }}"
                        max="{{ now()->year + 5 }}"
                        value="{{ now()->year }}"
                        required
                        class="grado-input w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none"
                    >

                </div>


                {{-- TURNO --}}
                <div>

                    <label
                        for="grado-turno"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Turno
                        <span class="text-red-500">*</span>
                    </label>

                    <select
                        id="grado-turno"
                        name="turno"
                        required
                        class="grado-native-select"
                    >
                        <option value="">Seleccionar turno</option>
                        <option value="mañana">Mañana</option>
                        <option value="tarde">Tarde</option>
                        <option value="noche">Noche</option>
                        <option value="completo">Completo</option>
                    </select>

                    <div class="grado-filter-select" data-filter-select="grado-turno">
                        <button type="button" class="grado-filter-trigger" aria-expanded="false">
                            <span class="grado-filter-icon">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="8"/>
                                    <path d="M12 8v4l3 2"/>
                                </svg>
                            </span>
                            <span class="min-w-0">
                                <span class="grado-filter-label">Turno</span>
                                <span class="grado-filter-value">Seleccionar turno</span>
                            </span>
                            <svg class="grado-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div class="grado-filter-panel">
                            <button type="button" class="grado-filter-option selected" data-value="">
                                <span class="grado-filter-option-icon">?</span>
                                <span>Seleccionar turno</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                            <button type="button" class="grado-filter-option" data-value="mañana">
                                <span class="grado-filter-option-icon">M</span>
                                <span>Mañana</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                            <button type="button" class="grado-filter-option" data-value="tarde">
                                <span class="grado-filter-option-icon">T</span>
                                <span>Tarde</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                            <button type="button" class="grado-filter-option" data-value="noche">
                                <span class="grado-filter-option-icon">N</span>
                                <span>Noche</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                            <button type="button" class="grado-filter-option" data-value="completo">
                                <span class="grado-filter-option-icon">C</span>
                                <span>Completo</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>


                {{-- ESTADO --}}
                <div class="md:col-span-2">

                    <label
                        for="grado-estado"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Estado
                    </label>

                    <select
                        id="grado-estado"
                        name="activo"
                        class="grado-native-select"
                    >
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>

                    <div class="grado-filter-select" data-filter-select="grado-estado">
                        <button type="button" class="grado-filter-trigger" aria-expanded="false">
                            <span class="grado-filter-icon">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 13c0 5-3.5 7-8 8-4.5-1-8-3-8-8V5l8-3 8 3v8Z"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                            </span>
                            <span class="min-w-0">
                                <span class="grado-filter-label">Estado</span>
                                <span class="grado-filter-value">Activo</span>
                            </span>
                            <svg class="grado-filter-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>

                        <div class="grado-filter-panel">
                            <button type="button" class="grado-filter-option selected" data-value="1">
                                <span class="grado-filter-option-icon">A</span>
                                <span>Activo</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                            <button type="button" class="grado-filter-option" data-value="0">
                                <span class="grado-filter-option-icon">I</span>
                                <span>Inactivo</span>
                                <svg class="grado-filter-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 6 9 17l-5-5"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

            </div>


            <div
                id="grado-form-errors"
                class="mt-5 hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            ></div>


            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">

                <button
                    type="button"
                    id="cancel-grado-modal"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    id="grado-submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white transition hover:opacity-90"
                    style="background:#0F2749;"
                >

                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M5 12l4 4L19 6"/>
                    </svg>

                    <span id="grado-submit-text">
                        Guardar grado
                    </span>

                </button>

            </div>

        </form>

    </div>

</div>



{{-- =========================================================
     MODAL ELIMINAR
========================================================== --}}
<div
    id="delete-grado-modal"
    class="fixed inset-0 z-[110] hidden items-center justify-center p-4"
>

    <div
        id="delete-grado-overlay"
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
    ></div>


    <div
        class="relative z-10 w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
    >

        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600">

            <svg
                class="h-6 w-6"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >
                <path d="M3 6h18"/>
                <path d="M8 6V4h8v2"/>
                <path d="M19 6l-1 14H6L5 6"/>
                <path d="M10 11v5M14 11v5"/>
            </svg>

        </div>


        <h3
            class="mt-4 text-lg font-extrabold"
            style="color:#0F2749;"
        >
            Eliminar grado
        </h3>


        <p class="mt-2 text-sm leading-6 text-slate-500">
            ¿Seguro que deseas eliminar
            <span
                id="delete-grado-name"
                class="font-semibold text-slate-700"
            ></span>?
        </p>


        <p class="mt-2 text-xs leading-5 text-slate-400">
            El backend no permitirá eliminar un grado que tenga alumnos asignados.
        </p>


        <div class="mt-6 flex justify-end gap-3">

            <button
                type="button"
                id="cancel-delete-grado"
                class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Cancelar
            </button>


            <button
                type="button"
                id="confirm-delete-grado"
                class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
            >
                Eliminar
            </button>

        </div>

    </div>

</div>

@endsection
