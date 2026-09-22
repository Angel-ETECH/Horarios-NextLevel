@extends('layouts.app')

@section('title', 'Cursos - Next Level School')
@section('page-title', 'Cursos')

@section('content')

<style>

    :root {
        --nl-navy: #0F2749;
        --nl-blue: #1B3A6B;
        --nl-red: #DB0808;
        --nl-red-dark: #8D0707;
        --nl-border: #DCE4EE;
        --nl-soft: #F8FAFC;
    }


    /* =========================================================
       ANIMACIONES
    ========================================================= */

    @keyframes cursosEntrada {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    @keyframes cursoSelectEntrada {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }


    .cursos-page {
        animation:
            cursosEntrada .4s ease-out both;
    }


    /* =========================================================
       TARJETAS
    ========================================================= */

    .curso-card {
        border:
            1px solid rgba(15, 39, 73, .08);

        box-shadow:
            0 9px 30px rgba(15, 39, 73, .055);

        transition:
            transform .22s ease,
            box-shadow .22s ease,
            border-color .22s ease;
    }


    .curso-card:hover {
        border-color:
            rgba(27, 58, 107, .14);

        box-shadow:
            0 15px 38px rgba(15, 39, 73, .085);
    }


    .curso-stat-card {
        position: relative;

        overflow: hidden;
    }


    .curso-stat-card::before {
        content: '';

        position: absolute;

        top: 0;
        left: 0;
        right: 0;

        height: 3px;
    }


    .curso-stat-blue::before {
        background:
            linear-gradient(
                90deg,
                #1B3A6B,
                #0F2749
            );
    }


    .curso-stat-green::before {
        background:
            linear-gradient(
                90deg,
                #10B981,
                #059669
            );
    }


    .curso-stat-gray::before {
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

    .curso-input {
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }


    .curso-input:focus {
        outline: none;

        border-color:
            #1B3A6B !important;

        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .08) !important;
    }


    /* =========================================================
       BOTONES
    ========================================================= */

    .curso-btn-primary {
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            filter .2s ease;
    }


    .curso-btn-primary:hover {
        transform:
            translateY(-2px);

        box-shadow:
            0 12px 28px rgba(27, 58, 107, .22);

        filter:
            brightness(1.04);
    }


    .curso-btn-danger {
        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }


    .curso-btn-danger:hover {
        transform:
            translateY(-1px);

        box-shadow:
            0 10px 25px rgba(219, 8, 8, .20);
    }


    /* =========================================================
       SELECT NATIVO OCULTO
    ========================================================= */

    .curso-native-select {
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

    .curso-custom-select {
        position: relative;

        width: 100%;

        min-width: 0;
    }


    .curso-select-trigger {
        position: relative;

        display: flex;

        width: 100%;
        min-height: 50px;

        align-items: center;

        gap: 9px;

        border:
            1px solid var(--nl-border);

        border-radius: 12px;

        background: #FFFFFF;

        padding:
            7px
            41px
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


    .curso-select-trigger:hover:not(:disabled) {
        border-color:
            #A9BAD0;
    }


    .curso-select-trigger:focus {
        outline: none;

        border-color:
            #1B3A6B;

        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .075);
    }


    .curso-custom-select.open
    .curso-select-trigger {
        border-color:
            #1B3A6B;

        box-shadow:
            0 0 0 4px rgba(27, 58, 107, .075);
    }


    .curso-select-trigger:disabled {
        cursor: not-allowed;

        background:
            #F1F5F9;

        color:
            #94A3B8;
    }


    .curso-select-icon {
        display: flex;

        width: 34px;
        height: 34px;

        flex:
            0 0 34px;

        align-items: center;
        justify-content: center;

        border-radius:
            9px;

        background:
            linear-gradient(
                135deg,
                #EEF4FB,
                #E4ECF7
            );

        color:
            #1B3A6B;
    }


    .curso-select-content {
        flex: 1;

        min-width: 0;
    }


    .curso-select-label {
        display: block;

        margin-bottom: 1px;

        color:
            #94A3B8;

        font-size:
            8px;

        font-weight:
            800;

        text-transform:
            uppercase;

        letter-spacing:
            .07em;
    }


    .curso-select-text {
        display: block;

        overflow: hidden;

        color:
            #334155;

        font-size:
            12px;

        font-weight:
            700;

        white-space:
            nowrap;

        text-overflow:
            ellipsis;
    }


    .curso-select-arrow {
        position: absolute;

        top: 50%;
        right: 13px;

        width: 16px;
        height: 16px;

        transform:
            translateY(-50%);

        color:
            #64748B;

        transition:
            transform .2s ease;
    }


    .curso-custom-select.open
    .curso-select-arrow {
        transform:
            translateY(-50%)
            rotate(180deg);
    }


    /* =========================================================
       MENÚ CUSTOM SELECT
    ========================================================= */

    .curso-select-menu {
        position: absolute;

        z-index: 350;

        top:
            calc(100% + 7px);

        left: 0;

        display: none;

        width: 100%;

        min-width: 210px;

        overflow: hidden;

        border:
            1px solid var(--nl-border);

        border-radius:
            14px;

        background:
            #FFFFFF;

        box-shadow:
            0 20px 50px rgba(15, 39, 73, .16);
    }


    .curso-custom-select.open
    .curso-select-menu {
        display: block;

        animation:
            cursoSelectEntrada .16s ease;
    }


    .curso-select-search-wrap {
        position: relative;

        padding: 8px;

        border-bottom:
            1px solid #EEF2F7;

        background:
            #F8FAFC;
    }


    .curso-select-search-icon {
        position: absolute;

        top: 50%;
        left: 20px;

        width: 15px;
        height: 15px;

        transform:
            translateY(-50%);

        color:
            #94A3B8;

        pointer-events:
            none;
    }


    .curso-select-search {
        width: 100%;

        height: 40px;

        border:
            1px solid #DCE4EE;

        border-radius:
            10px;

        padding:
            0 10px 0 35px;

        color:
            #334155;

        font-size:
            12px;

        outline: none;
    }


    .curso-select-search:focus {
        border-color:
            #1B3A6B;

        box-shadow:
            0 0 0 3px rgba(27, 58, 107, .07);
    }


    .curso-select-options {
        max-height:
            230px;

        overflow-y:
            auto;

        padding:
            6px;
    }


    .curso-select-options::-webkit-scrollbar {
        width:
            5px;
    }


    .curso-select-options::-webkit-scrollbar-thumb {
        border-radius:
            999px;

        background:
            #CBD5E1;
    }


    .curso-select-option {
        display: flex;

        width: 100%;

        align-items:
            center;

        gap:
            9px;

        border:
            0;

        border-radius:
            9px;

        background:
            transparent;

        padding:
            9px 10px;

        color:
            #334155;

        text-align:
            left;

        cursor:
            pointer;

        transition:
            background .15s ease,
            color .15s ease;
    }


    .curso-select-option:hover {
        background:
            #F1F5F9;
    }


    .curso-select-option.selected {
        background:
            #EDF4FC;

        color:
            #0F2749;
    }


    .curso-option-icon {
        display: flex;

        width: 30px;
        height: 30px;

        flex:
            0 0 30px;

        align-items:
            center;

        justify-content:
            center;

        border-radius:
            8px;

        background:
            #F1F5F9;

        color:
            #1B3A6B;
    }


    .curso-option-text {
        flex: 1;

        min-width: 0;

        font-size:
            12px;

        font-weight:
            700;

        overflow-wrap:
            anywhere;
    }


    .curso-option-check {
        width: 16px;
        height: 16px;

        flex:
            0 0 16px;

        color:
            #1B3A6B;

        opacity:
            0;
    }


    .curso-select-option.selected
    .curso-option-check {
        opacity:
            1;
    }


    .curso-select-empty {
        padding:
            22px 14px;

        color:
            #94A3B8;

        font-size:
            12px;

        text-align:
            center;
    }


    /* =========================================================
       ACCIONES TABLA
    ========================================================= */

    .curso-action-btn {
        display: inline-flex;

        width: 36px;
        height: 36px;

        align-items:
            center;

        justify-content:
            center;

        border:
            1px solid #E2E8F0;

        border-radius:
            10px;

        background:
            #FFFFFF;

        color:
            #64748B;

        transition:
            transform .18s ease,
            color .18s ease,
            background .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }


    .curso-action-btn:hover {
        transform:
            translateY(-1px);
    }


    .curso-action-edit:hover {
        border-color:
            rgba(27, 58, 107, .25);

        background:
            #F1F5F9;

        color:
            #1B3A6B;

        box-shadow:
            0 5px 15px rgba(15, 39, 73, .07);
    }


    .curso-action-delete:hover {
        border-color:
            rgba(219, 8, 8, .22);

        background:
            #FFF5F5;

        color:
            #DB0808;

        box-shadow:
            0 5px 15px rgba(219, 8, 8, .08);
    }


    /* =========================================================
       PAGINACIÓN
    ========================================================= */

    .curso-pagination-btn {
        display: inline-flex;

        min-width: 38px;

        height: 38px;

        align-items:
            center;

        justify-content:
            center;

        border:
            1px solid #E2E8F0;

        border-radius:
            10px;

        background:
            #FFFFFF;

        padding:
            0 12px;

        color:
            #64748B;

        font-size:
            13px;

        font-weight:
            600;

        transition:
            border-color .18s ease,
            color .18s ease,
            background .18s ease,
            transform .18s ease;
    }


    .curso-pagination-btn:hover:not(:disabled) {
        transform:
            translateY(-1px);

        border-color:
            #B8C6D8;

        color:
            #1B3A6B;

        background:
            #F8FAFC;
    }


    .curso-pagination-btn.active {
        border-color:
            #1B3A6B;

        background:
            linear-gradient(
                135deg,
                #1B3A6B,
                #0F2749
            );

        color:
            #FFFFFF;
    }


    .curso-pagination-btn:disabled {
        cursor:
            not-allowed;

        opacity:
            .45;
    }


    /* =========================================================
       FILAS
    ========================================================= */

    .curso-row {
        transition:
            background .18s ease;
    }


    .curso-row:hover {
        background:
            linear-gradient(
                90deg,
                rgba(27, 58, 107, .025),
                rgba(219, 8, 8, .012)
            );
    }


    /* =========================================================
       MODAL
    ========================================================= */

    .modal-scroll::-webkit-scrollbar {
        width:
            5px;
    }


    .modal-scroll::-webkit-scrollbar-thumb {
        border-radius:
            999px;

        background:
            #CBD5E1;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 640px) {

        .curso-select-trigger {
            min-height:
                50px;
        }


        .curso-select-menu {
            min-width:
                100%;
        }


        .curso-select-options {
            max-height:
                210px;
        }


        .curso-pagination-btn {
            min-width:
                36px;

            height:
                36px;

            padding:
                0 10px;
        }
    }

</style>


<div class="cursos-page space-y-6">


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div
        class="
            flex
            flex-col
            gap-4
            sm:flex-row
            sm:items-center
            sm:justify-between
        "
    >

        <div class="flex items-center gap-3">

            <div
                class="
                    flex
                    h-12
                    w-12
                    items-center
                    justify-center
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
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M5 4h13a2 2 0 0 1 2 2v14H7a3 3 0 0 1-3-3V5a1 1 0 0 1 1-1Z"/>
                    <path d="M7 4v16"/>
                    <path d="M10 8h6"/>
                    <path d="M10 12h6"/>
                </svg>

            </div>


            <div>

                <h1
                    class="text-2xl font-extrabold tracking-tight"
                    style="color:#0F2749;"
                >
                    Cursos
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Gestiona los cursos disponibles en Next Level School.
                </p>

            </div>

        </div>


        <button
            id="open-curso-modal"
            type="button"
            class="
                curso-btn-primary
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
            "
            style="
                background:
                    linear-gradient(
                        135deg,
                        #1B3A6B,
                        #0F2749
                    );

                box-shadow:
                    0 8px 22px rgba(27,58,107,.18);
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

            Agregar curso

        </button>

    </div>


    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}

    <div
        class="
            grid
            grid-cols-1
            gap-4
            sm:grid-cols-3
        "
    >


        {{-- TOTAL --}}

        <div
            class="
                curso-card
                curso-stat-card
                curso-stat-blue
                rounded-2xl
                bg-white
                p-5
            "
        >

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total de cursos
                    </p>

                    <p
                        id="total-cursos"
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
                    class="
                        flex
                        h-14
                        w-14
                        items-center
                        justify-center
                        rounded-2xl
                    "
                    style="
                        background:
                            rgba(27,58,107,.08);

                        color:
                            #1B3A6B;
                    "
                >

                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <path d="M5 4h13a2 2 0 0 1 2 2v14H7a3 3 0 0 1-3-3V5a1 1 0 0 1 1-1Z"/>
                        <path d="M7 4v16"/>
                        <path d="M10 8h6"/>
                        <path d="M10 12h6"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- ACTIVOS --}}

        <div
            class="
                curso-card
                curso-stat-card
                curso-stat-green
                rounded-2xl
                bg-white
                p-5
            "
        >

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Cursos activos
                    </p>

                    <p
                        id="cursos-activos"
                        class="mt-2 text-3xl font-extrabold text-emerald-600"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Disponibles para asignación
                    </p>

                </div>


                <div
                    class="
                        flex
                        h-14
                        w-14
                        items-center
                        justify-center
                        rounded-2xl
                        bg-emerald-50
                        text-emerald-600
                    "
                >

                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle cx="12" cy="12" r="9"/>
                        <path d="m8 12 2.5 2.5L16 9"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- INACTIVOS --}}

        <div
            class="
                curso-card
                curso-stat-card
                curso-stat-gray
                rounded-2xl
                bg-white
                p-5
            "
        >

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Cursos inactivos
                    </p>

                    <p
                        id="cursos-inactivos"
                        class="mt-2 text-3xl font-extrabold text-slate-500"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Fuera de uso actualmente
                    </p>

                </div>


                <div
                    class="
                        flex
                        h-14
                        w-14
                        items-center
                        justify-center
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
                        stroke-width="2"
                    >
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M8 12h8"/>
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         TABLA PRINCIPAL
    ========================================================== --}}

    <div
        class="
            curso-card
            overflow-visible
            rounded-2xl
            bg-white
        "
    >


        {{-- =====================================================
             FILTROS
        ====================================================== --}}

        <div
            class="
                rounded-t-2xl
                border-b
                border-slate-200
                p-5
            "
            style="
                background:
                    linear-gradient(
                        180deg,
                        #FAFBFD,
                        #FFFFFF
                    );
            "
        >

            <div
                class="
                    grid
                    grid-cols-1
                    gap-3
                    md:grid-cols-[minmax(260px,1fr)_230px]
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
                            h-5
                            w-5
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
                        id="buscar-curso"
                        type="text"
                        placeholder="Buscar curso por nombre o código..."
                        class="
                            curso-input
                            h-[50px]
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
                        id="filtro-curso-estado"
                        class="curso-native-select"
                    >

                        <option value="">
                            Todos los estados
                        </option>

                        <option value="1">
                            Activos
                        </option>

                        <option value="0">
                            Inactivos
                        </option>

                    </select>


                    <div
                        class="curso-custom-select"
                        data-curso-select="filtro-curso-estado"
                        data-label="Estado"
                        data-placeholder="Todos los estados"
                        data-icon="estado"
                        data-search="false"
                    ></div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             TABLA
        ====================================================== --}}

        <div class="overflow-x-auto">

            <table class="min-w-[950px] w-full">

                <thead>

                    <tr
                        class="border-b border-slate-200 bg-slate-50"
                    >

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Curso
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Código
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Nivel
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Tipo
                        </th>

                        <th class="px-5 py-4 text-left text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Horas semanales
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
                    id="cursos-body"
                    class="divide-y divide-slate-100"
                >
                </tbody>

            </table>

        </div>


        {{-- =====================================================
             FOOTER
        ====================================================== --}}

        <div
            class="
                flex
                flex-col
                gap-4
                rounded-b-2xl
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
                    id="resultados-cursos"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    0
                </span>

                de

                <span
                    id="total-resultados-cursos"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    0
                </span>

                cursos

            </p>


            <div
                id="cursos-paginacion"
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
     MODAL AGREGAR / EDITAR CURSO
========================================================== --}}

<div
    id="curso-modal"
    class="fixed inset-0 z-[200] hidden"
    aria-hidden="true"
>

    <div
        id="curso-modal-overlay"
        class="
            absolute
            inset-0
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


            {{-- HEADER --}}

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

                            color:
                                #1B3A6B;
                        "
                    >

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.9"
                        >
                            <path d="M5 4h13a2 2 0 0 1 2 2v14H7a3 3 0 0 1-3-3V5a1 1 0 0 1 1-1Z"/>
                            <path d="M7 4v16"/>
                        </svg>

                    </div>


                    <div>

                        <h2
                            id="curso-modal-title"
                            class="text-lg font-extrabold"
                            style="color:#0F2749;"
                        >
                            Agregar curso
                        </h2>


                        <p class="mt-1 text-xs text-slate-500">
                            Completa la información académica del curso.
                        </p>

                    </div>

                </div>


                <button
                    id="close-curso-modal"
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


            <form id="curso-form">

                <div class="space-y-5 px-6 py-6">


                    {{-- CÓDIGO --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">

                            Código

                            <span class="text-red-500">
                                *
                            </span>

                        </label>


                        <input
                            id="curso-codigo"
                            type="text"
                            placeholder="Ej. MAT-001"
                            maxlength="20"
                            class="
                                curso-input
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

                            Nombre del curso

                            <span class="text-red-500">
                                *
                            </span>

                        </label>


                        <input
                            id="curso-nombre"
                            type="text"
                            placeholder="Ej. Matemática"
                            maxlength="100"
                            class="
                                curso-input
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


                    {{-- DESCRIPCIÓN --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Descripción
                        </label>


                        <textarea
                            id="curso-descripcion"
                            rows="2"
                            placeholder="Breve descripción del curso..."
                            class="
                                curso-input
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


                    {{-- NIVEL --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">

                            Nivel

                            <span class="text-red-500">
                                *
                            </span>

                        </label>


                        <select
                            id="curso-nivel"
                            class="curso-native-select"
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
                            class="curso-custom-select"
                            data-curso-select="curso-nivel"
                            data-label="Nivel"
                            data-placeholder="Todos"
                            data-icon="nivel"
                            data-search="false"
                        ></div>

                    </div>


                    {{-- TIPO --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">

                            Tipo

                            <span class="text-red-500">
                                *
                            </span>

                        </label>


                        <select
                            id="curso-tipo"
                            class="curso-native-select"
                        >

                            <option value="obligatorio">
                                Obligatorio
                            </option>

                            <option value="electivo">
                                Electivo
                            </option>

                            <option value="taller">
                                Taller
                            </option>

                        </select>


                        <div
                            class="curso-custom-select"
                            data-curso-select="curso-tipo"
                            data-label="Tipo"
                            data-placeholder="Obligatorio"
                            data-icon="tipo"
                            data-search="false"
                        ></div>

                    </div>


                    {{-- HORAS --}}

                    <div
                        class="
                            grid
                            grid-cols-1
                            gap-5
                            sm:grid-cols-2
                        "
                    >

                        <div>

                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">

                                Horas semanales

                                <span class="text-red-500">
                                    *
                                </span>

                            </label>


                            <input
                                id="curso-horas"
                                type="number"
                                min="1"
                                max="20"
                                placeholder="Ej. 5"
                                class="
                                    curso-input
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


                        <div>

                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Duración por clase
                            </label>


                            <div class="relative">

                                <input
                                    id="curso-duracion"
                                    type="number"
                                    min="30"
                                    max="180"
                                    value="60"
                                    class="
                                        curso-input
                                        w-full
                                        rounded-xl
                                        border
                                        border-slate-200
                                        px-4
                                        py-2.5
                                        pr-16
                                        text-sm
                                        outline-none
                                    "
                                >


                                <span
                                    class="
                                        pointer-events-none
                                        absolute
                                        inset-y-0
                                        right-4
                                        flex
                                        items-center
                                        text-xs
                                        font-medium
                                        text-slate-400
                                    "
                                >
                                    min
                                </span>

                            </div>

                        </div>

                    </div>


                    {{-- COLOR --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Color para el horario
                        </label>


                        <div
                            class="
                                flex
                                items-center
                                justify-between
                                rounded-xl
                                border
                                border-slate-200
                                bg-slate-50
                                p-3
                            "
                        >

                            <div class="flex items-center gap-3">

                                <input
                                    id="curso-color"
                                    type="color"
                                    value="#1B3A6B"
                                    class="
                                        h-10
                                        w-10
                                        cursor-pointer
                                        rounded-lg
                                        border
                                        border-slate-200
                                        bg-white
                                        p-1
                                    "
                                >


                                <div>

                                    <p class="text-xs font-semibold text-slate-600">
                                        Color seleccionado
                                    </p>

                                    <span
                                        id="curso-color-preview"
                                        class="text-xs font-bold"
                                        style="color:#1B3A6B;"
                                    >
                                        #1B3A6B
                                    </span>

                                </div>

                            </div>


                            <svg
                                class="h-5 w-5 text-slate-300"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                            </svg>

                        </div>

                    </div>


                    {{-- ESTADO --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Estado
                        </label>


                        <select
                            id="curso-estado"
                            class="curso-native-select"
                        >

                            <option value="1">
                                Activo
                            </option>

                            <option value="0">
                                Inactivo
                            </option>

                        </select>


                        <div
                            class="curso-custom-select"
                            data-curso-select="curso-estado"
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
                            id="curso-observaciones"
                            rows="3"
                            placeholder="Notas adicionales..."
                            class="
                                curso-input
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


                {{-- =================================================
                     BOTONES
                ================================================== --}}

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
                        id="cancel-curso-modal"
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
                        id="curso-submit-button"
                        type="submit"
                        class="
                            curso-btn-primary
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

                        <span id="curso-submit-text">
                            Guardar curso
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
    id="eliminar-curso-modal"
    class="fixed inset-0 z-[220] hidden"
    aria-hidden="true"
>

    <div
        id="eliminar-curso-modal-overlay"
        class="
            absolute
            inset-0
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
                relative
                w-full
                max-w-md
                overflow-hidden
                rounded-2xl
                bg-white
            "
            style="
                box-shadow:
                    0 30px 80px rgba(15,39,73,.24);
            "
        >

            <div
                class="
                    flex
                    items-start
                    justify-between
                    gap-4
                    px-6
                    py-5
                "
            >

                <div
                    class="
                        flex
                        items-start
                        gap-4
                    "
                >

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

                            color:
                                #DB0808;
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
                            Eliminar curso
                        </h2>


                        <p
                            class="
                                mt-1
                                text-sm
                                leading-6
                                text-slate-500
                            "
                        >

                            ¿Deseas eliminar

                            <span
                                id="eliminar-curso-nombre"
                                class="font-bold"
                                style="color:#0F2749;"
                            ></span>?

                        </p>

                    </div>

                </div>


                <button
                    id="close-eliminar-curso-modal"
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
                    id="cancel-eliminar-curso-modal"
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
                    id="confirm-eliminar-curso"
                    type="button"
                    class="
                        curso-btn-danger
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

                    Eliminar curso

                </button>

            </div>

        </div>

    </div>

</div>

@endsection