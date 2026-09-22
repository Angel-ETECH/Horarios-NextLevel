@extends('layouts.app')

@section('title', 'Profesores - Next Level School')
@section('page-title', 'Profesores')

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

    @keyframes profesoresEntrada {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes profesorSelectEntrada {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .profesores-page {
        animation: profesoresEntrada .4s ease-out both;
    }

    /* =========================================================
       TARJETAS
    ========================================================= */

    .profesor-card {
        border: 1px solid rgba(15,39,73,.08);
        box-shadow: 0 9px 30px rgba(15,39,73,.055);

        transition:
            transform .22s ease,
            box-shadow .22s ease,
            border-color .22s ease;
    }

    .profesor-card:hover {
        border-color: rgba(27,58,107,.14);
        box-shadow: 0 15px 38px rgba(15,39,73,.085);
    }

    .profesor-stat-card {
        position: relative;
        overflow: hidden;
    }

    .profesor-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
    }

    .profesor-stat-blue::before {
        background: linear-gradient(90deg,#1B3A6B,#0F2749);
    }

    .profesor-stat-green::before {
        background: linear-gradient(90deg,#10B981,#059669);
    }

    .profesor-stat-gray::before {
        background: linear-gradient(90deg,#94A3B8,#64748B);
    }

    /* =========================================================
       INPUTS
    ========================================================= */

    .profesor-input {
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .profesor-input:focus {
        outline: none;
        border-color: #1B3A6B !important;
        box-shadow: 0 0 0 4px rgba(27,58,107,.08) !important;
    }

    /* =========================================================
       BOTONES
    ========================================================= */

    .profesor-btn-primary {
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            filter .2s ease;
    }

    .profesor-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(27,58,107,.22);
        filter: brightness(1.04);
    }

    .profesor-btn-danger {
        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }

    .profesor-btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 25px rgba(219,8,8,.20);
    }

    /* =========================================================
       SELECT NATIVO OCULTO
    ========================================================= */

    .profesor-native-select {
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

    .profesor-custom-select {
        position: relative;
        width: 100%;
        min-width: 0;
    }

    .profesor-select-trigger {
        position: relative;

        display: flex;
        width: 100%;
        min-height: 50px;

        align-items: center;
        gap: 9px;

        border: 1px solid var(--nl-border);
        border-radius: 12px;
        background: #FFFFFF;

        padding: 7px 41px 7px 8px;

        cursor: pointer;
        text-align: left;

        box-shadow: 0 3px 9px rgba(15,39,73,.03);

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .profesor-select-trigger:hover:not(:disabled) {
        border-color: #A9BAD0;
    }

    .profesor-select-trigger:focus {
        outline: none;
        border-color: #1B3A6B;
        box-shadow: 0 0 0 4px rgba(27,58,107,.075);
    }

    .profesor-custom-select.open .profesor-select-trigger {
        border-color: #1B3A6B;
        box-shadow: 0 0 0 4px rgba(27,58,107,.075);
    }

    .profesor-select-trigger:disabled {
        cursor: not-allowed;
        background: #F1F5F9;
        color: #94A3B8;
    }

    .profesor-select-icon {
        display: flex;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;

        align-items: center;
        justify-content: center;

        border-radius: 9px;

        background: linear-gradient(135deg,#EEF4FB,#E4ECF7);
        color: #1B3A6B;
    }

    .profesor-select-content {
        flex: 1;
        min-width: 0;
    }

    .profesor-select-label {
        display: block;
        margin-bottom: 1px;

        color: #94A3B8;

        font-size: 8px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .07em;
    }

    .profesor-select-text {
        display: block;

        overflow: hidden;

        color: #334155;

        font-size: 12px;
        font-weight: 700;

        white-space: nowrap;
        text-overflow: ellipsis;
    }

    .profesor-select-arrow {
        position: absolute;
        top: 50%;
        right: 13px;

        width: 16px;
        height: 16px;

        transform: translateY(-50%);
        color: #64748B;

        transition: transform .2s ease;
    }

    .profesor-custom-select.open .profesor-select-arrow {
        transform: translateY(-50%) rotate(180deg);
    }

    /* =========================================================
       MENÚ SELECT
    ========================================================= */

    .profesor-select-menu {
        position: absolute;
        z-index: 350;

        top: calc(100% + 7px);
        left: 0;

        display: none;

        width: 100%;
        min-width: 215px;

        overflow: hidden;

        border: 1px solid var(--nl-border);
        border-radius: 14px;

        background: #FFFFFF;

        box-shadow: 0 20px 50px rgba(15,39,73,.16);
    }

    .profesor-custom-select.open .profesor-select-menu {
        display: block;
        animation: profesorSelectEntrada .16s ease;
    }

    .profesor-select-options {
        max-height: 230px;
        overflow-y: auto;
        padding: 6px;
    }

    .profesor-select-options::-webkit-scrollbar {
        width: 5px;
    }

    .profesor-select-options::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: #CBD5E1;
    }

    .profesor-select-option {
        display: flex;

        width: 100%;

        align-items: center;
        gap: 9px;

        border: 0;
        border-radius: 9px;

        background: transparent;

        padding: 9px 10px;

        color: #334155;
        text-align: left;

        cursor: pointer;

        transition:
            background .15s ease,
            color .15s ease;
    }

    .profesor-select-option:hover {
        background: #F1F5F9;
    }

    .profesor-select-option.selected {
        background: #EDF4FC;
        color: #0F2749;
    }

    .profesor-option-icon {
        display: flex;

        width: 30px;
        height: 30px;
        flex: 0 0 30px;

        align-items: center;
        justify-content: center;

        border-radius: 8px;

        background: #F1F5F9;

        color: #1B3A6B;
    }

    .profesor-option-text {
        flex: 1;
        min-width: 0;

        font-size: 12px;
        font-weight: 700;

        overflow-wrap: anywhere;
    }

    .profesor-option-check {
        width: 16px;
        height: 16px;
        flex: 0 0 16px;

        color: #1B3A6B;
        opacity: 0;
    }

    .profesor-select-option.selected .profesor-option-check {
        opacity: 1;
    }

    /* =========================================================
       ACCIONES
    ========================================================= */

    .profesor-action-btn {
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

    .profesor-action-btn:hover {
        transform: translateY(-1px);
    }

    .profesor-action-edit:hover {
        border-color: rgba(27,58,107,.25);
        background: #F1F5F9;
        color: #1B3A6B;
        box-shadow: 0 5px 15px rgba(15,39,73,.07);
    }

    .profesor-action-delete:hover {
        border-color: rgba(219,8,8,.22);
        background: #FFF5F5;
        color: #DB0808;
        box-shadow: 0 5px 15px rgba(219,8,8,.08);
    }

    /* =========================================================
       PAGINACIÓN
    ========================================================= */

    .profesor-pagination-btn {
        display: inline-flex;

        min-width: 38px;
        height: 38px;

        align-items: center;
        justify-content: center;

        border: 1px solid #E2E8F0;
        border-radius: 10px;

        background: #FFFFFF;

        padding: 0 12px;

        color: #64748B;

        font-size: 13px;
        font-weight: 600;

        transition:
            border-color .18s ease,
            color .18s ease,
            background .18s ease,
            transform .18s ease;
    }

    .profesor-pagination-btn:hover:not(:disabled) {
        transform: translateY(-1px);

        border-color: #B8C6D8;
        color: #1B3A6B;
        background: #F8FAFC;
    }

    .profesor-pagination-btn.active {
        border-color: #1B3A6B;

        background: linear-gradient(135deg,#1B3A6B,#0F2749);

        color: #FFFFFF;
    }

    .profesor-pagination-btn:disabled {
        cursor: not-allowed;
        opacity: .45;
    }

    /* =========================================================
       FILAS
    ========================================================= */

    .profesor-row {
        transition: background .18s ease;
    }

    .profesor-row:hover {
        background:
            linear-gradient(
                90deg,
                rgba(27,58,107,.025),
                rgba(219,8,8,.012)
            );
    }

    /* =========================================================
       INSTITUCIONES
    ========================================================= */

    .institucion-profesor-card {
        transition:
            border-color .18s ease,
            background .18s ease,
            box-shadow .18s ease;
    }

    .institucion-profesor-card:has(input:checked) {
        border-color: rgba(27,58,107,.30);
        background: #F0F6FD;
        box-shadow: 0 0 0 3px rgba(27,58,107,.04);
    }

    .institucion-profesor-card.academia:has(input:checked) {
        border-color: rgba(219,8,8,.25);
        background: #FFF5F5;
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

    @media (max-width: 640px) {
        .profesor-select-menu {
            min-width: 100%;
        }

        .profesor-pagination-btn {
            min-width: 36px;
            height: 36px;
            padding: 0 10px;
        }
    }
</style>


<div class="profesores-page space-y-6">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex items-center gap-3">

            <div
                class="flex h-12 w-12 items-center justify-center rounded-2xl"
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
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M2.5 21a6.5 6.5 0 0 1 13 0"/>
                    <path d="M17 8h5"/>
                    <path d="M19.5 5.5v5"/>
                </svg>
            </div>

            <div>
                <h1
                    class="text-2xl font-extrabold tracking-tight"
                    style="color:#0F2749;"
                >
                    Profesores
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Gestiona los profesores registrados en Next Level School.
                </p>
            </div>

        </div>


        <button
            id="open-profesor-modal"
            type="button"
            class="
                profesor-btn-primary
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
                <circle cx="9" cy="7" r="4"/>
                <path d="M3 21a6 6 0 0 1 12 0"/>
                <path d="M19 8v6M16 11h6"/>
            </svg>

            Agregar profesor
        </button>

    </div>


    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div
            class="
                profesor-card
                profesor-stat-card
                profesor-stat-blue
                rounded-2xl
                bg-white
                p-5
            "
        >
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Total profesores
                    </p>

                    <p
                        id="total-profesores"
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
                    style="background:rgba(27,58,107,.08);color:#1B3A6B;"
                >
                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M5 21a7 7 0 0 1 14 0"/>
                    </svg>
                </div>

            </div>
        </div>


        <div
            class="
                profesor-card
                profesor-stat-card
                profesor-stat-green
                rounded-2xl
                bg-white
                p-5
            "
        >
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Profesores activos
                    </p>

                    <p
                        id="profesores-activos"
                        class="mt-2 text-3xl font-extrabold text-emerald-600"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Disponibles actualmente
                    </p>
                </div>

                <div
                    class="
                        flex h-14 w-14
                        items-center justify-center
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


        <div
            class="
                profesor-card
                profesor-stat-card
                profesor-stat-gray
                rounded-2xl
                bg-white
                p-5
            "
        >
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Profesores inactivos
                    </p>

                    <p
                        id="profesores-inactivos"
                        class="mt-2 text-3xl font-extrabold text-slate-500"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Inactivos o en licencia
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
         TABLA
    ========================================================== --}}

    <div class="profesor-card overflow-visible rounded-2xl bg-white">

        {{-- FILTROS --}}

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
                    lg:grid-cols-[minmax(280px,1fr)_230px]
                "
            >

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
                        id="buscar-profesor"
                        type="text"
                        placeholder="Buscar por nombre, DNI o especialidad..."
                        class="
                            profesor-input
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


                <div>

                    <select
                        id="filtro-estado"
                        class="profesor-native-select"
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

                        <option value="licencia">
                            Licencia
                        </option>
                    </select>


                    <div
                        class="profesor-custom-select"
                        data-profesor-select="filtro-estado"
                        data-label="Estado"
                        data-placeholder="Todos los estados"
                        data-icon="estado"
                    ></div>

                </div>

            </div>

        </div>


        {{-- TABLA --}}

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1000px] text-left">

                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">

                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Profesor
                        </th>

                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            DNI
                        </th>

                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Especialidad
                        </th>

                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Teléfono
                        </th>

                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Institución
                        </th>

                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Carga horaria
                        </th>

                        <th class="px-5 py-4 text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Estado
                        </th>

                        <th class="px-5 py-4 text-right text-xs font-bold uppercase tracking-wider text-[#1B3A6B]">
                            Acciones
                        </th>

                    </tr>
                </thead>


                <tbody
                    id="profesores-body"
                    class="divide-y divide-slate-100"
                >
                </tbody>

            </table>

        </div>


        {{-- FOOTER --}}

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
                    id="resultados-profesores"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    0
                </span>

                de

                <span
                    id="total-resultados-profesores"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    0
                </span>

                profesores

            </p>


            <div
                id="profesores-paginacion"
                class="flex flex-wrap items-center gap-2"
            >
            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL AGREGAR
========================================================== --}}

<div
    id="profesor-modal"
    class="fixed inset-0 z-[200] hidden"
    aria-hidden="true"
>

    <div
        id="profesor-modal-overlay"
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
    ></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div
            class="
                modal-scroll
                relative
                max-h-[92vh]
                w-full
                max-w-2xl
                overflow-y-auto
                rounded-2xl
                bg-white
            "
            style="box-shadow:0 30px 80px rgba(15,39,73,.24);"
        >

            <div
                class="
                    sticky top-0 z-20
                    flex items-center justify-between
                    gap-4
                    border-b border-slate-200
                    bg-white
                    px-6 py-5
                "
            >

                <div class="flex items-center gap-3">

                    <div
                        class="
                            flex h-11 w-11
                            items-center justify-center
                            rounded-xl
                        "
                        style="background:rgba(27,58,107,.08);color:#1B3A6B;"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M3 21a6 6 0 0 1 12 0"/>
                            <path d="M19 8v6M16 11h6"/>
                        </svg>
                    </div>

                    <div>
                        <h2
                            class="text-lg font-extrabold"
                            style="color:#0F2749;"
                        >
                            Agregar profesor
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Registra la información del profesor.
                        </p>
                    </div>

                </div>


                <button
                    id="close-profesor-modal"
                    type="button"
                    class="
                        flex h-9 w-9
                        items-center justify-center
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


            <form id="profesor-form">

                <div class="space-y-5 px-6 py-6">

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Código <span class="text-red-500">*</span>
                        </label>

                        <input
                            id="codigo"
                            type="text"
                            placeholder="Ej. PROF-001"
                            class="
                                profesor-input
                                w-full rounded-xl
                                border border-slate-200
                                px-4 py-2.5
                                text-sm outline-none
                            "
                        >
                    </div>


                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Nombres <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="nombre"
                                type="text"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Apellido Paterno <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="apellido_paterno"
                                type="text"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Apellido Materno
                            </label>

                            <input
                                id="apellido_materno"
                                type="text"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                DNI <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="dni"
                                type="text"
                                maxlength="8"
                                inputmode="numeric"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Correo electrónico <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="email"
                                type="email"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Teléfono
                            </label>

                            <input
                                id="telefono"
                                type="tel"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>

                    </div>


                    {{-- SEXO --}}

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Sexo
                        </label>

                        <select
                            id="sexo"
                            class="profesor-native-select"
                        >
                            <option value="">
                                Seleccionar
                            </option>

                            <option value="masculino">
                                Masculino
                            </option>

                            <option value="femenino">
                                Femenino
                            </option>
                        </select>


                        <div
                            class="profesor-custom-select"
                            data-profesor-select="sexo"
                            data-label="Sexo"
                            data-placeholder="Seleccionar"
                            data-icon="usuario"
                        ></div>
                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Fecha de nacimiento
                        </label>

                        <input
                            id="fecha_nacimiento"
                            type="date"
                            class="
                                profesor-input
                                w-full rounded-xl
                                border border-slate-200
                                px-4 py-2.5
                                text-sm outline-none
                            "
                        >
                    </div>


                    {{-- ESPECIALIDAD --}}

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Especialidad
                        </label>

                        <select
                            id="especialidad"
                            class="profesor-native-select"
                        >
                            <option value="">Seleccionar especialidad</option>
                            <option value="matematica">Matemática</option>
                            <option value="comunicacion">Comunicación</option>
                            <option value="ingles">Inglés</option>
                            <option value="fisica">Física</option>
                            <option value="quimica">Química</option>
                            <option value="historia">Historia</option>
                            <option value="arte">Arte</option>
                            <option value="musica">Música</option>
                            <option value="educacion_fisica">Educación Física</option>
                        </select>


                        <div
                            class="profesor-custom-select"
                            data-profesor-select="especialidad"
                            data-label="Especialidad"
                            data-placeholder="Seleccionar especialidad"
                            data-icon="especialidad"
                        ></div>
                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Carga horaria máxima
                        </label>

                        <input
                            id="carga_horaria_maxima"
                            type="number"
                            min="1"
                            max="40"
                            value="30"
                            class="
                                profesor-input
                                w-full rounded-xl
                                border border-slate-200
                                px-4 py-2.5
                                text-sm outline-none
                            "
                        >
                    </div>


                    {{-- INSTITUCIONES --}}

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Institución(es) donde dicta clases
                            <span class="text-red-500">*</span>
                        </label>

                        <p class="mb-3 text-xs text-slate-400">
                            Puede trabajar en Colegio, Academia o ambas.
                        </p>


                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                            <label
                                class="
                                    institucion-profesor-card
                                    flex cursor-pointer
                                    items-center gap-3
                                    rounded-xl
                                    border border-slate-200
                                    bg-slate-50
                                    px-4 py-3
                                "
                            >
                                <input
                                    type="checkbox"
                                    name="instituciones[]"
                                    value="colegio"
                                    class="h-4 w-4 rounded"
                                    style="accent-color:#1B3A6B;"
                                >

                                <div
                                    class="
                                        flex h-9 w-9
                                        items-center justify-center
                                        rounded-lg bg-white
                                        text-[#1B3A6B]
                                    "
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M4 21V6h16v15"/>
                                        <path d="M2 21h20"/>
                                        <path d="M8 10h2M14 10h2"/>
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-[#0F2749]">
                                        Colegio
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Primaria / Secundaria
                                    </p>
                                </div>
                            </label>


                            <label
                                class="
                                    institucion-profesor-card
                                    academia
                                    flex cursor-pointer
                                    items-center gap-3
                                    rounded-xl
                                    border border-slate-200
                                    bg-slate-50
                                    px-4 py-3
                                "
                            >
                                <input
                                    type="checkbox"
                                    name="instituciones[]"
                                    value="academia"
                                    class="h-4 w-4 rounded"
                                    style="accent-color:#DB0808;"
                                >

                                <div
                                    class="
                                        flex h-9 w-9
                                        items-center justify-center
                                        rounded-lg bg-white
                                        text-[#DB0808]
                                    "
                                >
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="m3 10 9-5 9 5-9 5z"/>
                                        <path d="M7 12.5V17c3 2 7 2 10 0v-4.5"/>
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-[#0F2749]">
                                        Academia
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Preparación académica
                                    </p>
                                </div>
                            </label>

                        </div>
                    </div>


                    {{-- ESTADO --}}

                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Estado
                        </label>

                        <select
                            id="estado"
                            class="profesor-native-select"
                        >
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="licencia">Licencia</option>
                        </select>


                        <div
                            class="profesor-custom-select"
                            data-profesor-select="estado"
                            data-label="Estado"
                            data-placeholder="Activo"
                            data-icon="estado"
                        ></div>
                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Observaciones
                        </label>

                        <textarea
                            id="observaciones"
                            rows="3"
                            class="
                                profesor-input
                                w-full resize-none
                                rounded-xl
                                border border-slate-200
                                px-4 py-2.5
                                text-sm outline-none
                            "
                        ></textarea>
                    </div>

                </div>


                <div
                    class="
                        sticky bottom-0 z-20
                        flex flex-col-reverse
                        gap-3
                        border-t border-slate-200
                        bg-slate-50
                        px-6 py-4
                        sm:flex-row
                        sm:justify-end
                    "
                >

                    <button
                        id="cancel-profesor-modal"
                        type="button"
                        class="
                            rounded-xl
                            border border-slate-200
                            bg-white
                            px-5 py-2.5
                            text-sm font-semibold
                            text-slate-600
                            transition
                            hover:bg-slate-100
                        "
                    >
                        Cancelar
                    </button>


                    <button
                        type="submit"
                        class="
                            profesor-btn-primary
                            inline-flex
                            items-center justify-center
                            gap-2
                            rounded-xl
                            px-5 py-2.5
                            text-sm font-semibold
                            text-white
                        "
                        style="background:linear-gradient(135deg,#1B3A6B,#0F2749);"
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

                        Guardar profesor
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =========================================================
     MODAL EDITAR
========================================================== --}}

<div
    id="editar-profesor-modal"
    class="fixed inset-0 z-[210] hidden"
    aria-hidden="true"
>

    <div
        id="editar-modal-overlay"
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
    ></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div
            class="
                modal-scroll
                relative
                max-h-[92vh]
                w-full
                max-w-2xl
                overflow-y-auto
                rounded-2xl
                bg-white
            "
            style="box-shadow:0 30px 80px rgba(15,39,73,.24);"
        >

            <div
                class="
                    sticky top-0 z-20
                    flex items-center justify-between
                    gap-4
                    border-b border-slate-200
                    bg-white
                    px-6 py-5
                "
            >

                <div class="flex items-center gap-3">

                    <div
                        class="
                            flex h-11 w-11
                            items-center justify-center
                            rounded-xl
                        "
                        style="background:rgba(27,58,107,.08);color:#1B3A6B;"
                    >
                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                        </svg>
                    </div>

                    <div>
                        <h2
                            class="text-lg font-extrabold"
                            style="color:#0F2749;"
                        >
                            Editar profesor
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Modifica los datos registrados.
                        </p>
                    </div>

                </div>


                <button
                    id="close-editar-modal"
                    type="button"
                    class="
                        flex h-9 w-9
                        items-center justify-center
                        rounded-lg
                        text-slate-400
                        transition
                        hover:bg-slate-100
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


            <form id="editar-profesor-form">

                <div class="space-y-5 px-6 py-6">

                    <input
                        type="hidden"
                        id="editar-id"
                    >


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Código
                        </label>

                        <input
                            id="editar-codigo"
                            type="text"
                            readonly
                            class="
                                w-full cursor-not-allowed
                                rounded-xl
                                border border-slate-200
                                bg-slate-100
                                px-4 py-2.5
                                text-sm text-slate-500
                            "
                        >
                    </div>


                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Nombres
                            </label>

                            <input
                                id="editar-nombre"
                                type="text"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Apellido Paterno
                            </label>

                            <input
                                id="editar-apellido_paterno"
                                type="text"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Apellido Materno
                            </label>

                            <input
                                id="editar-apellido_materno"
                                type="text"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                DNI
                            </label>

                            <input
                                id="editar-dni"
                                type="text"
                                maxlength="8"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>

                    </div>


                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Correo
                            </label>

                            <input
                                id="editar-email"
                                type="email"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>


                        <div>
                            <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                                Teléfono
                            </label>

                            <input
                                id="editar-telefono"
                                type="tel"
                                class="
                                    profesor-input
                                    w-full rounded-xl
                                    border border-slate-200
                                    px-4 py-2.5
                                    text-sm outline-none
                                "
                            >
                        </div>

                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Sexo
                        </label>

                        <select
                            id="editar-sexo"
                            class="profesor-native-select"
                        >
                            <option value="">Seleccionar</option>
                            <option value="masculino">Masculino</option>
                            <option value="femenino">Femenino</option>
                        </select>


                        <div
                            class="profesor-custom-select"
                            data-profesor-select="editar-sexo"
                            data-label="Sexo"
                            data-placeholder="Seleccionar"
                            data-icon="usuario"
                        ></div>
                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Fecha de nacimiento
                        </label>

                        <input
                            id="editar-fecha_nacimiento"
                            type="date"
                            class="
                                profesor-input
                                w-full rounded-xl
                                border border-slate-200
                                px-4 py-2.5
                                text-sm outline-none
                            "
                        >
                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Especialidad
                        </label>

                        <select
                            id="editar-especialidad"
                            class="profesor-native-select"
                        >
                            <option value="">Seleccionar especialidad</option>
                            <option value="matematica">Matemática</option>
                            <option value="comunicacion">Comunicación</option>
                            <option value="ingles">Inglés</option>
                            <option value="fisica">Física</option>
                            <option value="quimica">Química</option>
                            <option value="historia">Historia</option>
                            <option value="arte">Arte</option>
                            <option value="musica">Música</option>
                            <option value="educacion_fisica">Educación Física</option>
                        </select>


                        <div
                            class="profesor-custom-select"
                            data-profesor-select="editar-especialidad"
                            data-label="Especialidad"
                            data-placeholder="Seleccionar especialidad"
                            data-icon="especialidad"
                        ></div>
                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Carga horaria máxima
                        </label>

                        <input
                            id="editar-carga_horaria_maxima"
                            type="number"
                            min="1"
                            max="40"
                            class="
                                profesor-input
                                w-full rounded-xl
                                border border-slate-200
                                px-4 py-2.5
                                text-sm outline-none
                            "
                        >
                    </div>


                    {{-- INSTITUCIONES EDITAR --}}

                    <div>

                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Institución(es) donde dicta clases
                        </label>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                            <label
                                class="
                                    institucion-profesor-card
                                    flex cursor-pointer
                                    items-center gap-3
                                    rounded-xl
                                    border border-slate-200
                                    bg-slate-50
                                    px-4 py-3
                                "
                            >
                                <input
                                    id="editar-institucion-colegio"
                                    type="checkbox"
                                    value="colegio"
                                    class="h-4 w-4 rounded"
                                    style="accent-color:#1B3A6B;"
                                >

                                <div>
                                    <p class="text-sm font-semibold text-[#0F2749]">
                                        Colegio
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Primaria / Secundaria
                                    </p>
                                </div>
                            </label>


                            <label
                                class="
                                    institucion-profesor-card
                                    academia
                                    flex cursor-pointer
                                    items-center gap-3
                                    rounded-xl
                                    border border-slate-200
                                    bg-slate-50
                                    px-4 py-3
                                "
                            >
                                <input
                                    id="editar-institucion-academia"
                                    type="checkbox"
                                    value="academia"
                                    class="h-4 w-4 rounded"
                                    style="accent-color:#DB0808;"
                                >

                                <div>
                                    <p class="text-sm font-semibold text-[#0F2749]">
                                        Academia
                                    </p>

                                    <p class="text-xs text-slate-400">
                                        Preparación académica
                                    </p>
                                </div>
                            </label>

                        </div>

                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Estado
                        </label>

                        <select
                            id="editar-estado"
                            class="profesor-native-select"
                        >
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                            <option value="licencia">Licencia</option>
                        </select>


                        <div
                            class="profesor-custom-select"
                            data-profesor-select="editar-estado"
                            data-label="Estado"
                            data-placeholder="Activo"
                            data-icon="estado"
                        ></div>
                    </div>


                    <div>
                        <label class="mb-2 block text-sm font-semibold text-[#0F2749]">
                            Observaciones
                        </label>

                        <textarea
                            id="editar-observaciones"
                            rows="3"
                            class="
                                profesor-input
                                w-full resize-none
                                rounded-xl
                                border border-slate-200
                                px-4 py-2.5
                                text-sm outline-none
                            "
                        ></textarea>
                    </div>

                </div>


                <div
                    class="
                        sticky bottom-0 z-20
                        flex flex-col-reverse
                        gap-3
                        border-t border-slate-200
                        bg-slate-50
                        px-6 py-4
                        sm:flex-row
                        sm:justify-end
                    "
                >

                    <button
                        id="cancel-editar-modal"
                        type="button"
                        class="
                            rounded-xl
                            border border-slate-200
                            bg-white
                            px-5 py-2.5
                            text-sm font-semibold
                            text-slate-600
                            transition
                            hover:bg-slate-100
                        "
                    >
                        Cancelar
                    </button>


                    <button
                        type="submit"
                        class="
                            profesor-btn-primary
                            rounded-xl
                            px-5 py-2.5
                            text-sm font-semibold
                            text-white
                        "
                        style="background:linear-gradient(135deg,#1B3A6B,#0F2749);"
                    >
                        Guardar cambios
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
    id="eliminar-profesor-modal"
    class="fixed inset-0 z-[220] hidden"
    aria-hidden="true"
>

    <div
        id="eliminar-profesor-modal-overlay"
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
    ></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div
            class="w-full max-w-md overflow-hidden rounded-2xl bg-white"
            style="box-shadow:0 30px 80px rgba(15,39,73,.24);"
        >

            <div class="flex items-start justify-between gap-4 px-6 py-5">

                <div class="flex items-start gap-4">

                    <div
                        class="
                            flex h-12 w-12
                            shrink-0
                            items-center justify-center
                            rounded-xl
                        "
                        style="background:rgba(219,8,8,.08);color:#DB0808;"
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
                            Eliminar profesor
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            ¿Deseas eliminar a
                            <span
                                id="eliminar-profesor-nombre"
                                class="font-bold"
                                style="color:#0F2749;"
                            ></span>?
                        </p>
                    </div>

                </div>


                <button
                    id="close-eliminar-profesor-modal"
                    type="button"
                    class="
                        flex h-9 w-9
                        items-center justify-center
                        rounded-lg
                        text-slate-400
                        transition
                        hover:bg-slate-100
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
                    flex flex-col-reverse
                    gap-3
                    border-t border-slate-200
                    bg-slate-50
                    px-6 py-4
                    sm:flex-row
                    sm:justify-end
                "
            >

                <button
                    id="cancel-eliminar-profesor-modal"
                    type="button"
                    class="
                        rounded-xl
                        border border-slate-200
                        bg-white
                        px-5 py-2.5
                        text-sm font-semibold
                        text-slate-600
                    "
                >
                    Cancelar
                </button>


                <button
                    id="confirm-eliminar-profesor"
                    type="button"
                    class="
                        profesor-btn-danger
                        inline-flex
                        items-center justify-center
                        gap-2
                        rounded-xl
                        px-5 py-2.5
                        text-sm font-semibold
                        text-white
                    "
                    style="background:linear-gradient(135deg,#DB0808,#8D0707);"
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

                    Eliminar profesor
                </button>

            </div>

        </div>

    </div>

</div>

@endsection