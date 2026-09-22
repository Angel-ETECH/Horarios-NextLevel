@extends('layouts.app')

@section('title', 'Asignaciones - Next Level School')
@section('page-title', 'Asignaciones')

@section('content')

<style>
    :root {
        --rojo-principal: #DB0808;
        --rojo-oscuro: #8D0707;
        --azul-noche: #1B3A6B;
        --azul-oscuro: #0F2749;
        --borde: #DCE4EE;
        --fondo-suave: #F8FAFC;
    }

    @keyframes asignacionesEntrada {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .asignaciones-page {
        animation: asignacionesEntrada .4s ease-out both;
    }

    .asignaciones-card {
        border: 1px solid rgba(27, 58, 107, .09);
        box-shadow: 0 10px 35px rgba(15, 39, 73, .06);
    }

    /* =========================================================
       SELECT NATIVO OCULTO
    ========================================================= */

    .nl-native-select {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        opacity: 0 !important;
        pointer-events: none !important;
        overflow: hidden !important;
    }

    /* =========================================================
       CUSTOM SELECT
    ========================================================= */

    .nl-select {
        position: relative;
        width: 100%;
        min-width: 0;
    }

    .nl-select-trigger {
        position: relative;
        display: flex;
        width: 100%;
        min-height: 52px;
        align-items: center;
        gap: 11px;

        border: 1px solid var(--borde);
        border-radius: 13px;

        background: #FFFFFF;

        padding: 8px 44px 8px 9px;

        text-align: left;
        cursor: pointer;

        box-shadow: 0 3px 10px rgba(15, 39, 73, .035);

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            transform .2s ease,
            background .2s ease;
    }

    .nl-select-trigger:hover:not(:disabled) {
        border-color: #A9BAD0;
        box-shadow: 0 7px 18px rgba(15, 39, 73, .07);
    }

    .nl-select-trigger:focus {
        outline: none;
        border-color: var(--azul-noche);
        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .08),
            0 7px 18px rgba(15, 39, 73, .07);
    }

    .nl-select.open .nl-select-trigger {
        border-color: var(--azul-noche);
        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .08),
            0 7px 18px rgba(15, 39, 73, .07);
    }

    .nl-select-trigger:disabled {
        cursor: not-allowed;
        background: #F1F5F9;
        color: #94A3B8;
        box-shadow: none;
    }

    .nl-select-icon {
        display: flex;
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        align-items: center;
        justify-content: center;

        border-radius: 10px;

        background: linear-gradient(
            135deg,
            #EEF4FB,
            #E3EBF5
        );

        color: var(--azul-noche);
    }

    .nl-select-trigger:disabled .nl-select-icon {
        background: #E2E8F0;
        color: #94A3B8;
    }

    .nl-select-content {
        flex: 1;
        min-width: 0;
    }

    .nl-select-label {
        display: block;
        margin-bottom: 2px;

        color: #94A3B8;

        font-size: 9px;
        font-weight: 800;
        line-height: 1.15;

        text-transform: uppercase;
        letter-spacing: .075em;
    }

    .nl-select-text {
        display: block;

        color: #334155;

        font-size: 13px;
        font-weight: 700;
        line-height: 1.3;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nl-select-trigger:disabled .nl-select-text {
        color: #94A3B8;
    }

    .nl-select-arrow {
        position: absolute;
        right: 15px;
        top: 50%;

        width: 17px;
        height: 17px;

        transform: translateY(-50%);

        color: #64748B;

        transition: transform .2s ease;
    }

    .nl-select.open .nl-select-arrow {
        transform:
            translateY(-50%)
            rotate(180deg);
    }

    /* =========================================================
       MENÚ CUSTOM SELECT
    ========================================================= */

    .nl-select-menu {
        position: absolute;
        z-index: 300;

        top: calc(100% + 7px);
        left: 0;

        display: none;

        width: 100%;
        min-width: 220px;

        overflow: hidden;

        border: 1px solid var(--borde);
        border-radius: 15px;

        background: #FFFFFF;

        box-shadow:
            0 22px 55px rgba(15, 39, 73, .17);
    }

    .nl-select.open .nl-select-menu {
        display: block;
        animation: menuEntrada .16s ease;
    }

    @keyframes menuEntrada {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .nl-select-search-wrap {
        position: relative;

        padding: 9px;

        border-bottom: 1px solid #EEF2F7;

        background: #F8FAFC;
    }

    .nl-select-search-icon {
        position: absolute;

        left: 22px;
        top: 50%;

        width: 15px;
        height: 15px;

        transform: translateY(-50%);

        color: #94A3B8;
    }

    .nl-select-search {
        width: 100%;
        height: 41px;

        border: 1px solid #DCE4EE;
        border-radius: 10px;

        background: #FFFFFF;

        padding: 0 12px 0 37px;

        color: #334155;

        font-size: 12px;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }

    .nl-select-search:focus {
        border-color: var(--azul-noche);
        box-shadow: 0 0 0 3px rgba(27, 58, 107, .07);
    }

    .nl-select-options {
        max-height: 240px;
        overflow-y: auto;
        padding: 7px;
    }

    .nl-select-options::-webkit-scrollbar {
        width: 5px;
    }

    .nl-select-options::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #CBD5E1;
    }

    .nl-option {
        display: flex;

        width: 100%;
        min-width: 0;

        align-items: center;
        gap: 9px;

        border: 0;
        border-radius: 10px;

        background: transparent;

        padding: 9px 10px;

        color: #334155;
        text-align: left;

        cursor: pointer;

        transition:
            background .15s ease,
            color .15s ease;
    }

    .nl-option:hover {
        background: #F1F5F9;
    }

    .nl-option.selected {
        background: #EDF4FC;
        color: var(--azul-oscuro);
    }

    .nl-option-icon {
        display: flex;

        width: 32px;
        height: 32px;

        flex: 0 0 32px;

        align-items: center;
        justify-content: center;

        border-radius: 9px;

        background: #F1F5F9;

        color: var(--azul-noche);
    }

    .nl-option.selected .nl-option-icon {
        background: #DDEAF8;
    }

    .nl-option-text {
        flex: 1;
        min-width: 0;

        font-size: 12px;
        font-weight: 700;
        line-height: 1.35;

        white-space: normal;
        overflow-wrap: anywhere;
    }

    .nl-option-check {
        width: 17px;
        height: 17px;

        flex: 0 0 17px;

        color: var(--azul-noche);

        opacity: 0;
    }

    .nl-option.selected .nl-option-check {
        opacity: 1;
    }

    .nl-select-empty {
        padding: 25px 15px;

        color: #94A3B8;

        font-size: 12px;
        text-align: center;
    }

    /* =========================================================
       INPUTS
    ========================================================= */

    .asignaciones-input {
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .asignaciones-input:focus {
        outline: none;

        border-color:
            var(--azul-noche) !important;

        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .08) !important;
    }

    /* =========================================================
       DÍAS
    ========================================================= */

    .dia-asignacion-label {
        transition:
            border-color .18s ease,
            background .18s ease,
            color .18s ease,
            box-shadow .18s ease,
            transform .18s ease;
    }

    .dia-asignacion-label:hover {
        border-color: #A9BAD0;
    }

    .dia-asignacion-label.activo {
        border-color: var(--azul-noche);

        background: rgba(27, 58, 107, .07);

        color: var(--azul-oscuro);

        box-shadow:
            0 0 0 3px rgba(27, 58, 107, .055);
    }

    /* =========================================================
       BOTONES
    ========================================================= */

    .asignaciones-primary {
        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }

    .asignaciones-primary:hover {
        transform: translateY(-2px);

        box-shadow:
            0 12px 28px rgba(219, 8, 8, .22);
    }

    .asignaciones-secondary {
        transition:
            transform .2s ease,
            border-color .2s ease,
            background .2s ease;
    }

    .asignaciones-secondary:hover {
        transform: translateY(-1px);

        border-color: rgba(27, 58, 107, .25);

        background: #F8FAFC;
    }

    .asignaciones-table-row {
        transition: background .18s ease;
    }

    .asignaciones-table-row:hover {
        background: linear-gradient(
            90deg,
            rgba(27, 58, 107, .025),
            rgba(219, 8, 8, .015)
        );
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 640px) {

        .nl-select-trigger {
            min-height: 50px;

            padding:
                7px 40px
                7px 8px;

            gap: 8px;
        }

        .nl-select-icon {
            width: 34px;
            height: 34px;
            flex-basis: 34px;
        }

        .nl-select-text {
            font-size: 12px;
        }

        .nl-select-menu {
            min-width: 100%;
        }

        .nl-select-options {
            max-height: 215px;
        }
    }

</style>


<div class="asignaciones-page space-y-6">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="flex items-start gap-3">

        <div
            class="
                flex h-12 w-12
                items-center justify-center
                rounded-2xl
            "
            style="
                background:
                    linear-gradient(
                        145deg,
                        rgba(27,58,107,.12),
                        rgba(219,8,8,.06)
                    );
            "
        >

            <svg
                class="h-6 w-6"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#1B3A6B"
                stroke-width="1.9"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M9 6h11M9 12h11M9 18h11"/>
                <circle cx="4.5" cy="6" r="1.5"/>
                <circle cx="4.5" cy="12" r="1.5"/>
                <circle cx="4.5" cy="18" r="1.5"/>
            </svg>

        </div>

        <div>

            <h1
                class="text-2xl font-extrabold tracking-tight"
                style="color:#0F2749;"
            >
                Asignaciones
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Asigna manualmente una clase respetando la disponibilidad del profesor.
            </p>

        </div>

    </div>


    {{-- =========================================================
         INFORMACIÓN
    ========================================================== --}}

    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">

        <div class="flex items-start gap-3">

            <div
                class="
                    flex h-9 w-9
                    shrink-0
                    items-center justify-center
                    rounded-xl
                    bg-blue-100
                    text-blue-700
                "
            >

                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 11v5"/>
                    <path d="M12 8h.01"/>
                </svg>

            </div>

            <div>

                <p class="text-sm font-semibold text-slate-800">
                    Asignación manual validada
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-600">
                    El horario debe respetar la disponibilidad registrada del profesor
                    y no generar conflictos con otras clases.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FORMULARIO
    ========================================================== --}}

    <div class="asignaciones-card overflow-visible rounded-2xl bg-white">

        <div
            class="
                flex items-center gap-3
                rounded-t-2xl
                border-b border-slate-200
                px-5 py-4
            "
            style="
                background:
                    linear-gradient(
                        90deg,
                        rgba(27,58,107,.035),
                        #fff
                    );
            "
        >

            <div
                class="
                    flex h-10 w-10
                    items-center justify-center
                    rounded-xl
                "
                style="background:rgba(27,58,107,.08);"
            >

                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#1B3A6B"
                    stroke-width="1.9"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v8M8 12h8"/>
                </svg>

            </div>

            <div>

                <h2
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    Nueva asignación
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Elige profesor, institución, curso, aula, días y horario.
                </p>

            </div>

        </div>


        {{-- =====================================================
             CAMPOS
        ====================================================== --}}

        <div
            class="
                grid grid-cols-1
                gap-x-5 gap-y-5
                p-5
                md:grid-cols-2
                xl:grid-cols-4
            "
        >

            {{-- PROFESOR --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Profesor
                    <span class="text-red-600">*</span>
                </label>

                <select
                    id="asignacion-profesor"
                    class="nl-native-select"
                >
                    <option value="">
                        Seleccionar profesor
                    </option>
                </select>

                <div
                    class="nl-select"
                    data-custom-select="asignacion-profesor"
                    data-label="Profesor"
                    data-search="true"
                    data-placeholder="Seleccionar profesor"
                    data-icon="profesor"
                ></div>

            </div>


            {{-- INSTITUCIÓN --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Institución
                    <span class="text-red-600">*</span>
                </label>

                <select
                    id="asignacion-institucion"
                    class="nl-native-select"
                >
                    <option value="">
                        Seleccionar institución
                    </option>

                    <option value="colegio">
                        Colegio
                    </option>

                    <option value="academia">
                        Academia
                    </option>
                </select>

                <div
                    class="nl-select"
                    data-custom-select="asignacion-institucion"
                    data-label="Institución"
                    data-search="false"
                    data-placeholder="Seleccionar institución"
                    data-icon="institucion"
                ></div>

            </div>


            {{-- CURSO --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Curso / Carrera
                    <span class="text-red-600">*</span>
                </label>

                <select
                    id="asignacion-curso"
                    class="nl-native-select"
                >
                    <option value="">
                        Seleccionar curso
                    </option>
                </select>

                <div
                    class="nl-select"
                    data-custom-select="asignacion-curso"
                    data-label="Curso"
                    data-search="true"
                    data-placeholder="Seleccionar curso"
                    data-icon="curso"
                ></div>

            </div>


            {{-- GRADO --}}

            <div id="grado-container">

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Grado / Sección
                </label>

                <select
                    id="asignacion-grado"
                    class="nl-native-select"
                >
                    <option value="">
                        Seleccionar grado
                    </option>
                </select>

                <div
                    class="nl-select"
                    data-custom-select="asignacion-grado"
                    data-label="Grado"
                    data-search="true"
                    data-placeholder="Seleccionar grado"
                    data-icon="grado"
                ></div>

                <p
                    id="grado-help"
                    class="mt-1.5 text-xs text-slate-500"
                >
                    Selecciona el grado y sección del colegio.
                </p>

            </div>


            {{-- AULA --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Aula
                    <span class="text-red-600">*</span>
                </label>

                <select
                    id="asignacion-aula"
                    class="nl-native-select"
                >
                    <option value="">
                        Seleccionar aula
                    </option>
                </select>

                <div
                    class="nl-select"
                    data-custom-select="asignacion-aula"
                    data-label="Aula"
                    data-search="true"
                    data-placeholder="Seleccionar aula"
                    data-icon="aula"
                ></div>

            </div>


            {{-- DÍAS --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Días
                    <span class="text-red-600">*</span>
                </label>

                <div
                    id="asignacion-dias"
                    class="
                        min-h-[52px]
                        rounded-xl
                        border border-slate-200
                        bg-white
                        p-2
                    "
                >

                    <span class="px-2 text-xs text-slate-400">
                        Selecciona profesor e institución.
                    </span>

                </div>

                <p class="mt-1.5 text-xs text-slate-500">
                    Puedes seleccionar uno o varios días disponibles.
                </p>

            </div>


            {{-- HORA INICIO --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Hora inicio
                    <span class="text-red-600">*</span>
                </label>

                <input
                    id="asignacion-hora-inicio"
                    type="time"
                    step="60"
                    disabled
                    class="
                        asignaciones-input
                        min-h-[52px]
                        w-full
                        rounded-xl
                        border border-slate-200
                        bg-white
                        px-4
                        text-sm
                        outline-none
                        disabled:bg-slate-100
                        disabled:text-slate-400
                    "
                >

            </div>


            {{-- HORA FIN --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Hora fin
                    <span class="text-red-600">*</span>
                </label>

                <input
                    id="asignacion-hora-fin"
                    type="time"
                    step="60"
                    disabled
                    class="
                        asignaciones-input
                        min-h-[52px]
                        w-full
                        rounded-xl
                        border border-slate-200
                        bg-white
                        px-4
                        text-sm
                        outline-none
                        disabled:bg-slate-100
                        disabled:text-slate-400
                    "
                >

            </div>

        </div>


        {{-- =====================================================
             DISPONIBILIDAD
        ====================================================== --}}

        <div class="border-t border-slate-200 px-5 py-5">

            <div class="mb-3">

                <h3
                    class="text-sm font-bold"
                    style="color:#0F2749;"
                >
                    Disponibilidad del profesor
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Rangos configurados en el módulo Disponibilidad.
                </p>

            </div>

            <div
                id="resumen-disponibilidad-profesor"
                class="flex flex-wrap gap-2"
            >

                <span class="text-xs text-slate-400">
                    Selecciona profesor e institución.
                </span>

            </div>

            <div
                id="estado-disponibilidad-asignacion"
                class="mt-4 hidden rounded-xl border p-3 text-sm"
            ></div>

        </div>


        {{-- =====================================================
             ESTADO
        ====================================================== --}}

        <div
            class="
                border-t border-slate-200
                px-5 py-5
            "
            style="background:rgba(248,250,252,.55);"
        >

            <div class="max-w-sm">

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Estado
                </label>

                <select
                    id="asignacion-estado"
                    class="nl-native-select"
                >
                    <option value="activo">
                        Activo
                    </option>

                    <option value="inactivo">
                        Inactivo
                    </option>
                </select>

                <div
                    class="nl-select"
                    data-custom-select="asignacion-estado"
                    data-label="Estado"
                    data-search="false"
                    data-placeholder="Seleccionar estado"
                    data-icon="estado"
                ></div>

            </div>

        </div>


        {{-- =====================================================
             BOTONES
        ====================================================== --}}

        <div
            class="
                flex flex-col gap-3
                rounded-b-2xl
                border-t border-slate-200
                px-5 py-4
                sm:flex-row
                sm:justify-end
            "
            style="
                background:
                    linear-gradient(
                        0deg,
                        #fff,
                        #fafbfc
                    );
            "
        >

            <button
                id="limpiar-asignacion"
                type="button"
                class="
                    asignaciones-secondary
                    inline-flex items-center
                    justify-center
                    gap-2
                    rounded-xl
                    border border-slate-200
                    bg-white
                    px-5 py-2.5
                    text-sm font-semibold
                    text-slate-600
                "
            >
                Limpiar
            </button>

            <button
                id="guardar-asignacion"
                type="button"
                class="
                    asignaciones-primary
                    inline-flex items-center
                    justify-center
                    gap-2
                    rounded-xl
                    px-5 py-2.5
                    text-sm font-semibold
                    text-white
                "
                style="
                    background:
                        linear-gradient(
                            135deg,
                            #DB0808,
                            #8D0707
                        );
                "
            >

                <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M12 5v14M5 12h14"/>
                </svg>

                Crear asignación

            </button>

        </div>

    </div>


    {{-- =========================================================
         TABLA
    ========================================================== --}}

    <div class="asignaciones-card overflow-hidden rounded-2xl bg-white">

        <div
            class="
                flex flex-col gap-3
                border-b border-slate-200
                px-5 py-4
                md:flex-row
                md:items-center
                md:justify-between
            "
        >

            <div>

                <h2
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    Asignaciones actuales
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Clases actualmente ubicadas en el horario.
                </p>

            </div>

            <input
                id="buscar-asignacion"
                type="text"
                placeholder="Buscar asignación..."
                class="
                    asignaciones-input
                    w-full
                    rounded-xl
                    border border-slate-200
                    px-4 py-2.5
                    text-sm
                    outline-none
                    md:w-72
                "
            >

        </div>

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1100px] text-left">

                <thead class="bg-slate-50">

                    <tr class="border-b border-slate-200">

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Profesor
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Curso
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Grado
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Aula
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Día
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Horario
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Estado
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Acciones
                        </th>

                    </tr>

                </thead>

                <tbody
                    id="asignaciones-body"
                    class="divide-y divide-slate-100"
                ></tbody>

            </table>

        </div>

    </div>

</div>

@endsection