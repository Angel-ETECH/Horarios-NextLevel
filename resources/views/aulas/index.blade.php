@extends('layouts.app')

@section('title', 'Aulas - Next Level School')
@section('page-title', 'Aulas')

@section('content')

<style>
    :root {
        --nl-blue: #1B3A6B;
        --nl-blue-dark: #0F2749;
        --nl-red: #DB0808;
        --nl-red-dark: #8D0707;
        --nl-border: #DCE4EE;
        --nl-soft: #F8FAFC;
    }

    /* =========================================================
       GENERAL
    ========================================================= */

    @keyframes aulasEntrada {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes selectEntrada {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .aulas-page {
        animation: aulasEntrada .4s ease-out both;
    }

    .aulas-card {
        border: 1px solid rgba(27, 58, 107, .09);

        box-shadow:
            0 10px 32px rgba(15, 39, 73, .055);
    }

    .aulas-stat-card {
        position: relative;

        overflow: hidden;

        transition:
            transform .22s ease,
            box-shadow .22s ease,
            border-color .22s ease;
    }

    .aulas-stat-card:hover {
        transform: translateY(-3px);

        border-color:
            rgba(27, 58, 107, .16);

        box-shadow:
            0 15px 40px rgba(15, 39, 73, .09);
    }

    .aulas-stat-card::before {
        content: '';

        position: absolute;

        top: 0;
        left: 0;
        right: 0;

        height: 3px;
    }

    .aulas-stat-blue::before {
        background:
            linear-gradient(
                90deg,
                #1B3A6B,
                #0F2749
            );
    }

    .aulas-stat-red::before {
        background:
            linear-gradient(
                90deg,
                #DB0808,
                #8D0707
            );
    }

    .aulas-stat-gray::before {
        background:
            linear-gradient(
                90deg,
                #94A3B8,
                #64748B
            );
    }


    /* =========================================================
       INPUTS
    ========================================================= */

    .aulas-input {
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .aulas-input:focus {
        outline: none;

        border-color:
            var(--nl-blue) !important;

        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .08) !important;
    }


    /* =========================================================
       SELECT NATIVO OCULTO
    ========================================================= */

    .aula-native-select {
        position: absolute !important;

        width: 1px !important;
        height: 1px !important;

        opacity: 0 !important;

        overflow: hidden !important;

        pointer-events: none !important;
    }


    /* =========================================================
       CUSTOM SELECT
    ========================================================= */

    .aula-custom-select {
        position: relative;

        width: 100%;

        min-width: 0;
    }

    .aula-select-trigger {
        position: relative;

        display: flex;

        width: 100%;
        min-height: 48px;

        align-items: center;

        gap: 9px;

        border:
            1px solid var(--nl-border);

        border-radius: 12px;

        background: #FFFFFF;

        padding:
            7px
            40px
            7px
            8px;

        cursor: pointer;

        text-align: left;

        box-shadow:
            0 3px 9px rgba(15, 39, 73, .03);

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .aula-select-trigger:hover:not(:disabled) {
        border-color: #A9BAD0;
    }

    .aula-select-trigger:focus {
        outline: none;

        border-color: var(--nl-blue);

        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .075);
    }

    .aula-custom-select.open
    .aula-select-trigger {
        border-color: var(--nl-blue);

        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .075);
    }

    .aula-select-trigger:disabled {
        cursor: not-allowed;

        background: #F1F5F9;

        color: #94A3B8;
    }

    .aula-select-icon {
        display: flex;

        width: 33px;
        height: 33px;

        flex: 0 0 33px;

        align-items: center;
        justify-content: center;

        border-radius: 9px;

        background:
            linear-gradient(
                135deg,
                #EEF4FB,
                #E4ECF7
            );

        color: var(--nl-blue);
    }

    .aula-select-content {
        flex: 1;

        min-width: 0;
    }

    .aula-select-label {
        display: block;

        margin-bottom: 1px;

        color: #94A3B8;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;

        letter-spacing: .07em;
    }

    .aula-select-text {
        display: block;

        overflow: hidden;

        color: #334155;

        font-size: 12px;
        font-weight: 700;

        white-space: nowrap;

        text-overflow: ellipsis;
    }

    .aula-select-arrow {
        position: absolute;

        top: 50%;
        right: 13px;

        width: 16px;
        height: 16px;

        transform:
            translateY(-50%);

        color: #64748B;

        transition:
            transform .2s ease;
    }

    .aula-custom-select.open
    .aula-select-arrow {
        transform:
            translateY(-50%)
            rotate(180deg);
    }


    /* =========================================================
       MENU SELECT
    ========================================================= */

    .aula-select-menu {
        position: absolute;

        z-index: 350;

        top: calc(100% + 7px);
        left: 0;

        display: none;

        width: 100%;
        min-width: 210px;

        overflow: hidden;

        border:
            1px solid var(--nl-border);

        border-radius: 14px;

        background: #FFFFFF;

        box-shadow:
            0 20px 50px rgba(15, 39, 73, .16);
    }

    .aula-custom-select.open
    .aula-select-menu {
        display: block;

        animation:
            selectEntrada .16s ease;
    }

    .aula-select-search-wrap {
        position: relative;

        padding: 8px;

        border-bottom:
            1px solid #EEF2F7;

        background: #F8FAFC;
    }

    .aula-select-search-icon {
        position: absolute;

        top: 50%;
        left: 20px;

        width: 15px;
        height: 15px;

        transform:
            translateY(-50%);

        color: #94A3B8;

        pointer-events: none;
    }

    .aula-select-search {
        width: 100%;

        height: 40px;

        border:
            1px solid #DCE4EE;

        border-radius: 10px;

        padding:
            0
            10px
            0
            35px;

        color: #334155;

        font-size: 12px;

        outline: none;
    }

    .aula-select-search:focus {
        border-color: var(--nl-blue);

        box-shadow:
            0 0 0 3px rgba(27, 58, 107, .07);
    }

    .aula-select-options {
        max-height: 230px;

        overflow-y: auto;

        padding: 6px;
    }

    .aula-select-options::-webkit-scrollbar {
        width: 5px;
    }

    .aula-select-options::-webkit-scrollbar-thumb {
        border-radius: 999px;

        background: #CBD5E1;
    }

    .aula-select-option {
        display: flex;

        width: 100%;

        align-items: center;

        gap: 9px;

        border: 0;

        border-radius: 9px;

        background: transparent;

        padding:
            9px
            10px;

        color: #334155;

        text-align: left;

        cursor: pointer;

        transition:
            background .15s ease,
            color .15s ease;
    }

    .aula-select-option:hover {
        background: #F1F5F9;
    }

    .aula-select-option.selected {
        background: #EDF4FC;

        color: #0F2749;
    }

    .aula-option-icon {
        display: flex;

        width: 30px;
        height: 30px;

        flex: 0 0 30px;

        align-items: center;
        justify-content: center;

        border-radius: 8px;

        background: #F1F5F9;

        color: var(--nl-blue);
    }

    .aula-option-text {
        flex: 1;

        min-width: 0;

        font-size: 12px;
        font-weight: 700;

        overflow-wrap: anywhere;
    }

    .aula-option-check {
        width: 16px;
        height: 16px;

        flex: 0 0 16px;

        color: var(--nl-blue);

        opacity: 0;
    }

    .aula-select-option.selected
    .aula-option-check {
        opacity: 1;
    }

    .aula-select-empty {
        padding: 22px 14px;

        color: #94A3B8;

        font-size: 12px;

        text-align: center;
    }


    /* =========================================================
       ACCIONES TABLA
    ========================================================= */

    .aula-action-btn {
        display: inline-flex;

        width: 36px;
        height: 36px;

        align-items: center;
        justify-content: center;

        border: 1px solid #E2E8F0;

        border-radius: 10px;

        background: #FFFFFF;

        color: #64748B;

        transition:
            transform .18s ease,
            color .18s ease,
            background .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .aula-action-btn:hover {
        transform: translateY(-1px);
    }

    .aula-action-edit:hover {
        border-color:
            rgba(27, 58, 107, .25);

        background: #F1F5F9;

        color: #1B3A6B;

        box-shadow:
            0 5px 15px rgba(15, 39, 73, .07);
    }

    .aula-action-delete:hover {
        border-color:
            rgba(219, 8, 8, .22);

        background: #FFF5F5;

        color: #DB0808;

        box-shadow:
            0 5px 15px rgba(219, 8, 8, .08);
    }


    /* =========================================================
       PAGINACIÓN
    ========================================================= */

    .aulas-pagination-btn {
        display: inline-flex;

        min-width: 38px;
        height: 38px;

        align-items: center;
        justify-content: center;

        border:
            1px solid #E2E8F0;

        border-radius: 10px;

        background: #FFFFFF;

        padding:
            0
            12px;

        color: #64748B;

        font-size: 13px;
        font-weight: 600;

        transition:
            border-color .18s ease,
            color .18s ease,
            background .18s ease,
            transform .18s ease;
    }

    .aulas-pagination-btn:hover:not(:disabled) {
        transform: translateY(-1px);

        border-color: #B8C6D8;

        color: #1B3A6B;

        background: #F8FAFC;
    }

    .aulas-pagination-btn.active {
        border-color: #1B3A6B;

        background:
            linear-gradient(
                135deg,
                #1B3A6B,
                #0F2749
            );

        color: #FFFFFF;
    }

    .aulas-pagination-btn:disabled {
        cursor: not-allowed;

        opacity: .45;
    }


    /* =========================================================
       MODAL
    ========================================================= */

    .modal-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .modal-scroll::-webkit-scrollbar-thumb {
        border-radius: 999px;

        background: #CBD5E1;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 640px) {

        .aula-select-trigger {
            min-height: 50px;
        }

        .aula-select-menu {
            min-width: 100%;
        }

        .aula-select-options {
            max-height: 210px;
        }

        .aulas-pagination-btn {
            min-width: 36px;

            height: 36px;

            padding:
                0
                10px;
        }
    }
</style>


<div class="aulas-page space-y-6">


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex items-center gap-3">

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
                            rgba(219,8,8,.05)
                        );
                "
            >

                <svg
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#1B3A6B"
                    stroke-width="1.8"
                >
                    <path d="M4 21V5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5V21"/>
                    <path d="M2 21h20"/>
                    <path d="M8 8h2"/>
                    <path d="M14 8h2"/>
                    <path d="M8 12h2"/>
                    <path d="M14 12h2"/>
                    <path d="M9 21v-4h6v4"/>
                </svg>

            </div>


            <div>

                <h1
                    class="text-2xl font-extrabold tracking-tight"
                    style="color:#0F2749;"
                >
                    Aulas
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Administra las aulas disponibles para la programación de horarios.
                </p>

            </div>

        </div>


        <button
            id="open-aula-modal"
            type="button"
            class="
                inline-flex
                items-center
                justify-center
                gap-2
                rounded-xl
                px-5
                py-2.5
                text-sm
                font-semibold
                text-white
                shadow-lg
                transition
                hover:-translate-y-0.5
            "
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
            >
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 8v8"/>
                <path d="M8 12h8"/>
            </svg>

            Agregar aula

        </button>

    </div>


    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">


        {{-- TOTAL --}}

        <div class="aulas-card aulas-stat-card aulas-stat-blue rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total de aulas
                    </p>

                    <p
                        id="total-aulas"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Registradas en el sistema
                    </p>

                </div>


                <div
                    class="
                        flex h-14 w-14
                        items-center justify-center
                        rounded-2xl
                    "
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
                        stroke-width="1.8"
                    >
                        <path d="M4 21V5.5A1.5 1.5 0 0 1 5.5 4h13A1.5 1.5 0 0 1 20 5.5V21"/>
                        <path d="M2 21h20"/>
                        <path d="M8 8h2"/>
                        <path d="M14 8h2"/>
                        <path d="M9 21v-4h6v4"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- ACTIVAS --}}

        <div class="aulas-card aulas-stat-card aulas-stat-red rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Aulas activas
                    </p>

                    <p
                        id="aulas-activas"
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
                    class="
                        flex h-14 w-14
                        items-center justify-center
                        rounded-2xl
                    "
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
                        stroke-width="1.8"
                    >
                        <path d="M12 3 19 6v5c0 4.7-2.8 8-7 10-4.2-2-7-5.3-7-10V6z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- INACTIVAS --}}

        <div class="aulas-card aulas-stat-card aulas-stat-gray rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Aulas inactivas
                    </p>

                    <p
                        id="aulas-inactivas"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#64748B;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Fuera de uso actualmente
                    </p>

                </div>


                <div
                    class="
                        flex h-14 w-14
                        items-center justify-center
                        rounded-2xl
                        bg-slate-100
                        text-slate-500
                    "
                >

                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                    >
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M9 12h6"/>
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTROS
    ========================================================== --}}

    <div class="aulas-card overflow-visible rounded-2xl bg-white p-5">

        <div
            class="
                grid
                grid-cols-1
                gap-3
                lg:grid-cols-[minmax(260px,1fr)_210px_210px_210px]
            "
        >

            {{-- BUSCADOR --}}

            <div class="relative">

                <svg
                    class="
                        pointer-events-none
                        absolute
                        left-4
                        top-1/2
                        h-5 w-5
                        -translate-y-1/2
                        text-slate-400
                    "
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <circle cx="11" cy="11" r="7"/>
                    <path d="m20 20-3.5-3.5"/>
                </svg>

                <input
                    id="buscar-aula"
                    type="text"
                    placeholder="Buscar aula por nombre o código..."
                    class="
                        aulas-input
                        h-12
                        w-full
                        rounded-xl
                        border
                        border-slate-200
                        bg-white
                        pl-11
                        pr-4
                        text-sm
                        outline-none
                        placeholder:text-slate-400
                    "
                >

            </div>


            {{-- ESTADO --}}

            <div>

                <select
                    id="filtro-aula-estado"
                    class="aula-native-select"
                >
                    <option value="">
                        Todos los estados
                    </option>

                    <option value="1">
                        Activas
                    </option>

                    <option value="0">
                        Inactivas
                    </option>
                </select>

                <div
                    class="aula-custom-select"
                    data-aula-select="filtro-aula-estado"
                    data-label="Estado"
                    data-placeholder="Todos los estados"
                    data-icon="estado"
                    data-search="false"
                ></div>

            </div>


            {{-- TIPO --}}

            <div>

                <select
                    id="filtro-aula-tipo"
                    class="aula-native-select"
                >
                    <option value="">
                        Todos los tipos
                    </option>

                    <option value="aula_normal">
                        Aula normal
                    </option>

                    <option value="taller">
                        Taller
                    </option>

                    <option value="auditorio">
                        Auditorio
                    </option>

                    <option value="virtual">
                        Virtual
                    </option>
                </select>

                <div
                    class="aula-custom-select"
                    data-aula-select="filtro-aula-tipo"
                    data-label="Tipo"
                    data-placeholder="Todos los tipos"
                    data-icon="tipo"
                    data-search="false"
                ></div>

            </div>


            {{-- NIVEL --}}

            <div>

                <select
                    id="filtro-aula-nivel"
                    class="aula-native-select"
                >
                    <option value="">
                        Todos los niveles
                    </option>

                    <option value="primaria">
                        Primaria
                    </option>

                    <option value="secundaria">
                        Secundaria
                    </option>

                    <option value="academia">
                        Academia
                    </option>

                    <option value="todos">
                        Todos
                    </option>
                </select>

                <div
                    class="aula-custom-select"
                    data-aula-select="filtro-aula-nivel"
                    data-label="Nivel"
                    data-placeholder="Todos los niveles"
                    data-icon="nivel"
                    data-search="false"
                ></div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         TABLA
    ========================================================== --}}

    <div class="aulas-card overflow-hidden rounded-2xl bg-white">

        <div
            class="
                flex
                flex-col
                gap-3
                border-b
                border-slate-200
                px-5
                py-4
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
            style="
                background:
                    linear-gradient(
                        90deg,
                        rgba(27,58,107,.025),
                        #FFFFFF
                    );
            "
        >

            <div>

                <h2
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    Lista de aulas
                </h2>

                <p class="mt-1 text-xs text-slate-500">

                    Mostrando

                    <span
                        id="resultados-aulas"
                        class="font-bold"
                        style="color:#1B3A6B;"
                    >
                        0
                    </span>

                    aulas

                </p>

            </div>


            <div class="flex items-center gap-2 text-xs text-slate-400">

                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

                Datos actualizados

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[900px]">

                <thead>

                    <tr class="border-b border-slate-200 bg-slate-50">

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Aula
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Código
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Capacidad
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Tipo
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Nivel
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Estado
                        </th>

                        <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="aulas-body"
                    class="divide-y divide-slate-100"
                >
                </tbody>

            </table>

        </div>


        {{-- =====================================================
             FOOTER / PAGINACION
        ====================================================== --}}

        <div
            class="
                flex
                flex-col
                gap-4
                border-t
                border-slate-200
                bg-slate-50/60
                px-5
                py-4
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <p class="text-sm text-slate-500">

                Mostrando

                <span
                    id="resultados-aulas-footer"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    0
                </span>

                de

                <span
                    id="total-resultados-aulas"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    0
                </span>

                aulas

            </p>


            <div
                id="aulas-paginacion"
                class="
                    flex
                    flex-wrap
                    items-center
                    gap-2
                "
            >
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL AGREGAR / EDITAR
========================================================== --}}

<div
    id="aula-modal"
    class="fixed inset-0 z-[200] hidden"
    aria-hidden="true"
>

    <div
        id="aula-modal-overlay"
        class="
            absolute inset-0
            bg-slate-950/55
            backdrop-blur-sm
        "
    ></div>


    <div
        class="
            relative
            flex
            min-h-full
            items-center
            justify-center
            p-4
        "
    >

        <div
            class="
                modal-scroll
                relative
                max-h-[92vh]
                w-full
                max-w-xl
                overflow-y-auto
                rounded-2xl
                bg-white
            "
            style="
                box-shadow:
                    0 30px 80px rgba(15,39,73,.24);
            "
        >


            {{-- HEADER MODAL --}}

            <div
                class="
                    sticky
                    top-0
                    z-20
                    flex
                    items-center
                    justify-between
                    gap-4
                    border-b
                    border-slate-200
                    bg-white
                    px-6
                    py-5
                "
            >

                <div class="flex items-center gap-3">

                    <div
                        class="
                            flex
                            h-11
                            w-11
                            items-center
                            justify-center
                            rounded-xl
                        "
                        style="
                            background:
                                rgba(27,58,107,.08);
                            color:#1B3A6B;
                        "
                    >

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path d="M4 21V5h16v16"/>
                            <path d="M2 21h20"/>
                            <path d="M9 21v-4h6v4"/>
                        </svg>

                    </div>


                    <div>

                        <h2
                            id="aula-modal-title"
                            class="text-lg font-extrabold"
                            style="color:#0F2749;"
                        >
                            Agregar aula
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Completa la información del aula.
                        </p>

                    </div>

                </div>


                <button
                    id="close-aula-modal"
                    type="button"
                    class="
                        flex
                        h-9
                        w-9
                        items-center
                        justify-center
                        rounded-lg
                        text-slate-400
                        transition
                        hover:bg-slate-100
                        hover:text-slate-700
                    "
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M18 6 6 18"/>
                        <path d="M6 6l12 12"/>
                    </svg>
                </button>

            </div>


            <form id="aula-form">

                <div class="space-y-5 px-6 py-6">


                    {{-- CODIGO --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Código
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="aula-codigo"
                            type="text"
                            maxlength="20"
                            placeholder="Ej. AUL-001"
                            class="
                                aulas-input
                                w-full
                                rounded-xl
                                border
                                border-slate-200
                                px-4
                                py-2.5
                                text-sm
                                uppercase
                                outline-none
                            "
                        >

                    </div>


                    {{-- NOMBRE --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Nombre del aula
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="aula-nombre"
                            type="text"
                            maxlength="50"
                            placeholder="Ej. Aula 301"
                            class="
                                aulas-input
                                w-full
                                rounded-xl
                                border
                                border-slate-200
                                px-4
                                py-2.5
                                text-sm
                                outline-none
                            "
                        >

                    </div>


                    {{-- CAPACIDAD --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Capacidad
                            <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="aula-capacidad"
                            type="number"
                            min="1"
                            placeholder="Ej. 30"
                            class="
                                aulas-input
                                w-full
                                rounded-xl
                                border
                                border-slate-200
                                px-4
                                py-2.5
                                text-sm
                                outline-none
                            "
                        >

                    </div>


                    {{-- TIPO --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Tipo de aula
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="aula-tipo"
                            class="aula-native-select"
                        >
                            <option value="">
                                Seleccionar tipo
                            </option>

                            <option value="aula_normal">
                                Aula normal
                            </option>

                            <option value="taller">
                                Taller
                            </option>

                            <option value="auditorio">
                                Auditorio
                            </option>

                            <option value="virtual">
                                Virtual
                            </option>
                        </select>

                        <div
                            class="aula-custom-select"
                            data-aula-select="aula-tipo"
                            data-label="Tipo"
                            data-placeholder="Seleccionar tipo"
                            data-icon="tipo"
                            data-search="false"
                        ></div>

                    </div>


                    {{-- NIVEL --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Nivel
                        </label>

                        <select
                            id="aula-nivel"
                            class="aula-native-select"
                        >
                            <option value="todos">
                                Todos
                            </option>

                            <option value="primaria">
                                Primaria
                            </option>

                            <option value="secundaria">
                                Secundaria
                            </option>

                            <option value="academia">
                                Academia
                            </option>
                        </select>

                        <div
                            class="aula-custom-select"
                            data-aula-select="aula-nivel"
                            data-label="Nivel"
                            data-placeholder="Todos"
                            data-icon="nivel"
                            data-search="false"
                        ></div>

                    </div>


                    {{-- EQUIPAMIENTO --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Equipamiento
                        </label>

                        <input
                            id="aula-equipamiento"
                            type="text"
                            placeholder="Ej. Proyector, pizarra digital..."
                            class="
                                aulas-input
                                w-full
                                rounded-xl
                                border
                                border-slate-200
                                px-4
                                py-2.5
                                text-sm
                                outline-none
                            "
                        >

                    </div>


                    {{-- ESTADO --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Estado
                        </label>

                        <select
                            id="aula-estado"
                            class="aula-native-select"
                        >
                            <option value="1">
                                Activo
                            </option>

                            <option value="0">
                                Inactivo
                            </option>
                        </select>

                        <div
                            class="aula-custom-select"
                            data-aula-select="aula-estado"
                            data-label="Estado"
                            data-placeholder="Activo"
                            data-icon="estado"
                            data-search="false"
                        ></div>

                    </div>


                    {{-- OBSERVACIONES --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Observaciones
                        </label>

                        <textarea
                            id="aula-observaciones"
                            rows="3"
                            placeholder="Notas adicionales..."
                            class="
                                aulas-input
                                w-full
                                resize-none
                                rounded-xl
                                border
                                border-slate-200
                                px-4
                                py-2.5
                                text-sm
                                outline-none
                            "
                        ></textarea>

                    </div>

                </div>


                {{-- FOOTER MODAL --}}

                <div
                    class="
                        sticky
                        bottom-0
                        z-20
                        flex
                        flex-col-reverse
                        gap-3
                        border-t
                        border-slate-200
                        bg-slate-50
                        px-6
                        py-4
                        sm:flex-row
                        sm:justify-end
                    "
                >

                    <button
                        id="cancel-aula-modal"
                        type="button"
                        class="
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            px-5
                            py-2.5
                            text-sm
                            font-semibold
                            text-slate-600
                            transition
                            hover:bg-slate-100
                        "
                    >
                        Cancelar
                    </button>


                    <button
                        id="aula-submit-button"
                        type="submit"
                        class="
                            inline-flex
                            items-center
                            justify-center
                            gap-2
                            rounded-xl
                            px-5
                            py-2.5
                            text-sm
                            font-semibold
                            text-white
                            shadow-lg
                            transition
                            hover:-translate-y-0.5
                        "
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
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M5 4h11l3 3v13H5z"/>
                            <path d="M8 4v6h8V4"/>
                        </svg>

                        <span id="aula-submit-text">
                            Guardar aula
                        </span>

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL ELIMINAR
========================================================== --}}

<div
    id="delete-aula-modal"
    class="fixed inset-0 z-[220] hidden"
    aria-hidden="true"
>

    <div
        id="delete-aula-overlay"
        class="
            absolute inset-0
            bg-slate-950/55
            backdrop-blur-sm
        "
    ></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div
            class="
                w-full
                max-w-md
                rounded-2xl
                bg-white
            "
            style="
                box-shadow:
                    0 30px 80px rgba(15,39,73,.24);
            "
        >

            <div class="flex items-start justify-between gap-4 px-6 py-5">

                <div class="flex items-start gap-4">

                    <div
                        class="
                            flex
                            h-12
                            w-12
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                        "
                        style="
                            background:
                                rgba(219,8,8,.08);
                            color:#DB0808;
                        "
                    >

                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                        >
                            <path d="M3 6h18"/>
                            <path d="M8 6V4h8v2"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v5"/>
                            <path d="M14 11v5"/>
                        </svg>

                    </div>


                    <div>

                        <h2
                            class="text-lg font-extrabold"
                            style="color:#0F2749;"
                        >
                            Eliminar aula
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">

                            ¿Deseas eliminar

                            <span
                                id="delete-aula-name"
                                class="font-bold"
                                style="color:#0F2749;"
                            ></span>?

                        </p>

                    </div>

                </div>


                <button
                    id="close-delete-aula-modal"
                    type="button"
                    class="
                        flex
                        h-9
                        w-9
                        shrink-0
                        items-center
                        justify-center
                        rounded-lg
                        text-slate-400
                        transition
                        hover:bg-slate-100
                        hover:text-slate-700
                    "
                >

                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M18 6 6 18"/>
                        <path d="M6 6l12 12"/>
                    </svg>

                </button>

            </div>


            <div
                class="
                    flex
                    flex-col-reverse
                    gap-3
                    border-t
                    border-slate-200
                    bg-slate-50
                    px-6
                    py-4
                    sm:flex-row
                    sm:justify-end
                "
            >

                <button
                    id="cancel-delete-aula"
                    type="button"
                    class="
                        rounded-xl
                        border
                        border-slate-200
                        bg-white
                        px-5
                        py-2.5
                        text-sm
                        font-semibold
                        text-slate-600
                        transition
                        hover:bg-slate-100
                    "
                >
                    Cancelar
                </button>


                <button
                    id="confirm-delete-aula"
                    type="button"
                    class="
                        inline-flex
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        px-5
                        py-2.5
                        text-sm
                        font-semibold
                        text-white
                        shadow-lg
                        transition
                        hover:-translate-y-0.5
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
                        <path d="M3 6h18"/>
                        <path d="M8 6V4h8v2"/>
                        <path d="M19 6l-1 14H6L5 6"/>
                    </svg>

                    Eliminar aula

                </button>

            </div>

        </div>

    </div>

</div>

@endsection