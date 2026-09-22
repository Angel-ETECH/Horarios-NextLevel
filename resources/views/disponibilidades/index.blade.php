@extends('layouts.app')

@section('title', 'Disponibilidad - Next Level School')
@section('page-title', 'Disponibilidad')

@section('content')

<style>
    #disponibilidad-page {
        --rojo-principal: #DB0808;
        --rojo-oscuro: #8D0707;
        --azul-noche: #1B3A6B;
        --azul-oscuro: #0F2749;
        --blanco: #FFFFFF;
        --gris-borde: #DCE4EE;
        --gris-texto: #64748B;
        --gris-suave: #F8FAFC;
    }

    @keyframes disponibilidadEntrada {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes disponibilidadSelectEntrada {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #disponibilidad-page {
        animation: disponibilidadEntrada .4s ease-out both;
    }

    /* =========================================================
       HEADER
    ========================================================= */

    .disponibilidad-header {
        position: relative;

        overflow: hidden;

        border: 1px solid rgba(27,58,107,.10);
        border-radius: 18px;

        background:
            linear-gradient(
                135deg,
                rgba(27,58,107,.045),
                rgba(219,8,8,.025)
            ),
            #FFFFFF;

        box-shadow:
            0 10px 30px rgba(15,39,73,.055);
    }

    .disponibilidad-header::before {
        content: '';

        position: absolute;

        top: 0;
        bottom: 0;
        left: 0;

        width: 5px;

        background:
            linear-gradient(
                180deg,
                var(--rojo-principal),
                var(--rojo-oscuro)
            );
    }

    .disponibilidad-heading-icon {
        display: flex;

        width: 54px;
        height: 54px;

        flex: 0 0 54px;

        align-items: center;
        justify-content: center;

        border-radius: 15px;

        background:
            linear-gradient(
                135deg,
                var(--azul-noche),
                var(--azul-oscuro)
            );

        color: #FFFFFF;

        box-shadow:
            0 10px 24px rgba(15,39,73,.18);
    }

    /* =========================================================
       CARDS
    ========================================================= */

    .disponibilidad-card {
        border: 1px solid rgba(15,39,73,.08);

        box-shadow:
            0 9px 30px rgba(15,39,73,.055);

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            transform .2s ease;
    }

    .disponibilidad-card:hover {
        border-color: rgba(27,58,107,.13);

        box-shadow:
            0 14px 36px rgba(15,39,73,.075);
    }

    /* =========================================================
       SELECT NATIVO
    ========================================================= */

    .disponibilidad-native-select {
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

    .disponibilidad-custom-select {
        position: relative;

        width: 100%;

        min-width: 0;
    }

    .disponibilidad-select-trigger {
        position: relative;

        display: flex;

        width: 100%;
        min-height: 52px;

        align-items: center;

        gap: 10px;

        border:
            1px solid var(--gris-borde);

        border-radius: 13px;

        background: #FFFFFF;

        padding:
            7px
            42px
            7px
            8px;

        cursor: pointer;

        text-align: left;

        box-shadow:
            0 3px 10px rgba(15,39,73,.025);

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .disponibilidad-select-trigger:hover:not(:disabled) {
        border-color: #A8B9CE;
    }

    .disponibilidad-select-trigger:focus {
        outline: none;

        border-color: var(--azul-noche);

        box-shadow:
            0 0 0 4px rgba(27,58,107,.075);
    }

    .disponibilidad-custom-select.open
    .disponibilidad-select-trigger {
        border-color: var(--azul-noche);

        box-shadow:
            0 0 0 4px rgba(27,58,107,.075);
    }

    .disponibilidad-select-trigger:disabled {
        cursor: not-allowed;

        background: #F1F5F9;

        color: #94A3B8;
    }

    .disponibilidad-select-icon {
        display: flex;

        width: 36px;
        height: 36px;

        flex: 0 0 36px;

        align-items: center;
        justify-content: center;

        border-radius: 10px;

        background:
            linear-gradient(
                135deg,
                #EEF4FB,
                #E5EDF8
            );

        color: var(--azul-noche);
    }

    .disponibilidad-select-content {
        flex: 1;

        min-width: 0;
    }

    .disponibilidad-select-label {
        display: block;

        margin-bottom: 1px;

        color: #94A3B8;

        font-size: 8px;
        font-weight: 800;

        text-transform: uppercase;

        letter-spacing: .07em;
    }

    .disponibilidad-select-text {
        display: block;

        overflow: hidden;

        color: #334155;

        font-size: 13px;
        font-weight: 700;

        white-space: nowrap;

        text-overflow: ellipsis;
    }

    .disponibilidad-select-arrow {
        position: absolute;

        top: 50%;
        right: 14px;

        width: 17px;
        height: 17px;

        transform:
            translateY(-50%);

        color: #64748B;

        transition:
            transform .2s ease;
    }

    .disponibilidad-custom-select.open
    .disponibilidad-select-arrow {
        transform:
            translateY(-50%)
            rotate(180deg);
    }

    /* =========================================================
       MENU SELECT
    ========================================================= */

    .disponibilidad-select-menu {
        position: absolute;

        z-index: 500;

        top: calc(100% + 7px);
        left: 0;

        display: none;

        width: 100%;

        min-width: 230px;

        overflow: hidden;

        border:
            1px solid var(--gris-borde);

        border-radius: 14px;

        background: #FFFFFF;

        box-shadow:
            0 20px 55px rgba(15,39,73,.16);
    }

    .disponibilidad-custom-select.open
    .disponibilidad-select-menu {
        display: block;

        animation:
            disponibilidadSelectEntrada .16s ease;
    }

    .disponibilidad-select-search-wrap {
        position: relative;

        padding: 9px;

        border-bottom:
            1px solid #EEF2F7;

        background: #F8FAFC;
    }

    .disponibilidad-select-search-icon {
        position: absolute;

        top: 50%;
        left: 21px;

        width: 15px;
        height: 15px;

        transform:
            translateY(-50%);

        color: #94A3B8;

        pointer-events: none;
    }

    .disponibilidad-select-search {
        width: 100%;

        height: 41px;

        border:
            1px solid #DCE4EE;

        border-radius: 10px;

        padding:
            0
            11px
            0
            36px;

        color: #334155;

        font-size: 12px;

        outline: none;
    }

    .disponibilidad-select-search:focus {
        border-color: var(--azul-noche);

        box-shadow:
            0 0 0 3px rgba(27,58,107,.07);
    }

    .disponibilidad-select-options {
        max-height: 245px;

        overflow-y: auto;

        padding: 6px;
    }

    .disponibilidad-select-options::-webkit-scrollbar {
        width: 5px;
    }

    .disponibilidad-select-options::-webkit-scrollbar-thumb {
        border-radius: 999px;

        background: #CBD5E1;
    }

    .disponibilidad-select-option {
        display: flex;

        width: 100%;

        align-items: center;

        gap: 10px;

        border: 0;

        border-radius: 10px;

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

    .disponibilidad-select-option:hover {
        background: #F1F5F9;
    }

    .disponibilidad-select-option.selected {
        background: #EDF4FC;

        color: var(--azul-oscuro);
    }

    .disponibilidad-option-icon {
        display: flex;

        width: 31px;
        height: 31px;

        flex: 0 0 31px;

        align-items: center;
        justify-content: center;

        border-radius: 8px;

        background: #F1F5F9;

        color: var(--azul-noche);
    }

    .disponibilidad-option-text {
        flex: 1;

        min-width: 0;

        font-size: 12px;
        font-weight: 700;

        overflow-wrap: anywhere;
    }

    .disponibilidad-option-check {
        width: 16px;
        height: 16px;

        flex: 0 0 16px;

        color: var(--azul-noche);

        opacity: 0;
    }

    .disponibilidad-select-option.selected
    .disponibilidad-option-check {
        opacity: 1;
    }

    .disponibilidad-select-empty {
        padding:
            22px
            14px;

        color: #94A3B8;

        font-size: 12px;

        text-align: center;
    }

    /* =========================================================
       INPUT TIME
    ========================================================= */

    .disponibilidad-time {
        min-height: 52px;

        border:
            1px solid var(--gris-borde);

        border-radius: 13px;

        background: #FFFFFF;

        padding:
            0
            14px;

        color: #334155;

        font-size: 13px;
        font-weight: 600;

        outline: none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }

    .disponibilidad-time:focus {
        border-color: var(--azul-noche);

        box-shadow:
            0 0 0 4px rgba(27,58,107,.075);
    }

    /* =========================================================
       RESUMEN
    ========================================================= */

    .disponibilidad-stat {
        position: relative;

        overflow: hidden;

        border:
            1px solid rgba(15,39,73,.08);

        box-shadow:
            0 8px 26px rgba(15,39,73,.05);

        transition:
            transform .2s ease,
            box-shadow .2s ease;
    }

    .disponibilidad-stat:hover {
        transform: translateY(-2px);

        box-shadow:
            0 13px 34px rgba(15,39,73,.08);
    }

    .disponibilidad-stat::after {
        content: '';

        position: absolute;

        right: -23px;
        top: -25px;

        width: 82px;
        height: 82px;

        border-radius: 999px;

        background: rgba(27,58,107,.04);
    }

    .disponibilidad-stat-icon {
        display: flex;

        width: 46px;
        height: 46px;

        align-items: center;
        justify-content: center;

        border-radius: 13px;
    }

    /* =========================================================
       INFO
    ========================================================= */

    .disponibilidad-info-icon {
        display: flex;

        width: 40px;
        height: 40px;

        flex: 0 0 40px;

        align-items: center;
        justify-content: center;

        border-radius: 11px;

        background: rgba(27,58,107,.08);

        color: var(--azul-noche);
    }

    /* =========================================================
       GRID
    ========================================================= */

    #disponibilidad-grid {
        overflow: hidden;

        border:
            1px solid #DCE4EE;

        border-radius: 14px;

        box-shadow:
            0 5px 18px rgba(15,39,73,.04);
    }

    #disponibilidad-grid > div {
        border-color: #E2E8F0 !important;
    }

    #disponibilidad-grid > div:first-child {
        background: var(--azul-oscuro) !important;

        color: #FFFFFF;
    }

    #disponibilidad-grid .select-day {
        transition:
            background .18s ease,
            color .18s ease;
    }

    #disponibilidad-grid .select-day:hover {
        background:
            rgba(27,58,107,.06);
    }

    #disponibilidad-grid .availability-slot {
        position: relative;

        min-height: 54px;

        border-color: #E2E8F0 !important;

        background: #FFFFFF;

        transition:
            background .17s ease,
            box-shadow .17s ease;
    }

    #disponibilidad-grid .availability-slot:hover {
        background:
            rgba(219,8,8,.04);

        box-shadow:
            inset
            0 0 0 1px
            rgba(219,8,8,.10);
    }

    #disponibilidad-grid .availability-slot.selected {
        background:
            linear-gradient(
                135deg,
                #DB0808,
                #C40707
            ) !important;

        box-shadow:
            inset
            0 0 0 1px
            rgba(141,7,7,.25);
    }

    #disponibilidad-grid .availability-slot.selected::after {
        content: '✓';

        position: absolute;

        inset: 0;

        display: flex;

        align-items: center;
        justify-content: center;

        color: #FFFFFF;

        font-size: 14px;
        font-weight: 900;
    }

    /* =========================================================
       MOVIL
    ========================================================= */

    .mobile-day-tab {
        border:
            1px solid #E2E8F0;

        background:
            #F8FAFC;

        color:
            #475569;

        transition:
            all .18s ease;
    }

    .mobile-day-tab.active-mobile-day {
        border-color:
            var(--azul-noche);

        background:
            var(--azul-noche);

        color:
            #FFFFFF;

        box-shadow:
            0 5px 14px rgba(27,58,107,.18);
    }

    .mobile-day-panel .availability-slot.selected {
        background:
            linear-gradient(
                135deg,
                var(--rojo-principal),
                var(--rojo-oscuro)
            ) !important;

        color: #FFFFFF;
    }

    /* =========================================================
       BOTONES
    ========================================================= */

    .disponibilidad-action-btn {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        gap: 8px;

        border-radius: 11px;

        transition:
            transform .18s ease,
            box-shadow .18s ease,
            background .18s ease,
            border-color .18s ease;
    }

    .disponibilidad-action-btn:hover {
        transform:
            translateY(-1px);
    }

    .disponibilidad-primary-btn {
        border: 0;

        background:
            linear-gradient(
                135deg,
                var(--rojo-principal),
                var(--rojo-oscuro)
            );

        color: #FFFFFF;

        box-shadow:
            0 8px 20px rgba(219,8,8,.17);
    }

    .disponibilidad-primary-btn:hover {
        box-shadow:
            0 11px 26px rgba(219,8,8,.21);
    }

    /* =========================================================
       CHIP PERSONALIZADO
    ========================================================= */

    .chip-personalizado {
        border-color:
            rgba(27,58,107,.18) !important;

        background:
            rgba(27,58,107,.055) !important;

        color:
            var(--azul-noche) !important;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 640px) {

        .disponibilidad-heading-icon {
            width: 48px;
            height: 48px;

            flex-basis: 48px;
        }

        .disponibilidad-select-menu {
            min-width: 100%;
        }

        .disponibilidad-select-options {
            max-height: 220px;
        }

        #save-availability {
            width: 100%;
        }
    }
</style>


<div id="disponibilidad-page" class="space-y-6">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="disponibilidad-header px-6 py-6">

        <div class="flex items-center gap-4">

            <div
                class="disponibilidad-heading-icon"
                aria-hidden="true"
            >
                <svg
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M8 2v4M16 2v4"/>
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M3 10h18"/>
                    <path d="M8 15l2.2 2.2L16 12"/>
                </svg>
            </div>


            <div>

                <h1
                    class="text-2xl font-extrabold tracking-tight"
                    style="color:#0F2749;"
                >
                    Disponibilidad
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Registra únicamente los días y rangos horarios en los que cada profesor puede trabajar.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         SELECTOR PROFESOR / INSTITUCIÓN
    ========================================================== --}}

    <div class="disponibilidad-card rounded-2xl bg-white p-5">

        <div
            class="
                grid
                grid-cols-1
                gap-5
                md:max-w-4xl
                md:grid-cols-2
            "
        >

            {{-- PROFESOR --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Profesor
                </label>


                <select
                    id="profesor-select"
                    class="disponibilidad-native-select"
                >
                    <option value="">
                        Seleccionar profesor
                    </option>
                </select>


                <div
                    class="disponibilidad-custom-select"
                    data-disponibilidad-select="profesor-select"
                    data-label="Profesor"
                    data-placeholder="Seleccionar profesor"
                    data-icon="profesor"
                    data-search="true"
                ></div>

            </div>


            {{-- INSTITUCIÓN --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Institución
                </label>


                <select
                    id="institucion-select"
                    class="disponibilidad-native-select"
                >
                    <option value="colegio">
                        Colegio
                    </option>

                    <option value="academia">
                        Academia
                    </option>
                </select>


                <div
                    class="disponibilidad-custom-select"
                    data-disponibilidad-select="institucion-select"
                    data-label="Institución"
                    data-placeholder="Colegio"
                    data-icon="institucion"
                    data-search="false"
                ></div>


                <p class="mt-2 text-xs text-slate-400">
                    La disponibilidad se guarda por separado para cada institución.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         RESUMEN
    ========================================================== --}}

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div class="disponibilidad-stat rounded-2xl bg-white p-5">

            <div class="flex items-center gap-4">

                <div
                    class="
                        disponibilidad-stat-icon
                        bg-red-600
                        text-white
                    "
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                </div>


                <div>

                    <p class="text-xs font-semibold text-slate-500">
                        Bloques disponibles
                    </p>

                    <p
                        id="availability-count"
                        class="mt-1 text-2xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                </div>

            </div>

        </div>


        <div class="disponibilidad-stat rounded-2xl bg-white p-5">

            <div class="flex items-center gap-4">

                <div
                    class="
                        disponibilidad-stat-icon
                        text-white
                    "
                    style="background:#1B3A6B;"
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <path d="M8 2v4M16 2v4"/>
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M3 10h18"/>
                    </svg>
                </div>


                <div>

                    <p class="text-xs font-semibold text-slate-500">
                        Días disponibles
                    </p>

                    <p
                        id="availability-days"
                        class="mt-1 text-2xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                </div>

            </div>

        </div>


        <div class="disponibilidad-stat rounded-2xl bg-white p-5">

            <div class="flex items-center gap-4">

                <div
                    class="
                        disponibilidad-stat-icon
                        text-white
                    "
                    style="background:#0F2749;"
                >
                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <path d="M9 2h6"/>
                        <circle cx="12" cy="14" r="7"/>
                        <path d="M12 14l3-3"/>
                    </svg>
                </div>


                <div>

                    <p class="text-xs font-semibold text-slate-500">
                        Horas disponibles
                    </p>

                    <p
                        id="availability-hours"
                        class="mt-1 text-2xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ACLARACIÓN
    ========================================================== --}}

    <div
        class="
            rounded-2xl
            border
            border-blue-100
            bg-blue-50/70
            p-4
        "
    >

        <div class="flex items-start gap-3">

            <div
                class="disponibilidad-info-icon"
                aria-hidden="true"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 11v5"/>
                    <path d="M12 8h.01"/>
                </svg>
            </div>


            <div>

                <p
                    class="text-sm font-bold"
                    style="color:#0F2749;"
                >
                    Disponibilidad ≠ clase asignada
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-600">
                    Aquí solo indicas cuándo puede trabajar el profesor.
                    El día y la hora reales de cada clase se eligen después
                    en Asignaciones y se muestran finalmente en Horarios.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         PANEL PRINCIPAL
    ========================================================== --}}

    <div
        class="
            disponibilidad-card
            overflow-hidden
            rounded-2xl
            bg-white
        "
    >

        {{-- CABECERA --}}

        <div
            class="
                border-b
                border-slate-200
                px-5
                py-4
            "
            style="
                background:
                    linear-gradient(
                        180deg,
                        #FFFFFF,
                        #FBFCFE
                    );
            "
        >

            <div
                class="
                    flex
                    flex-col
                    gap-4
                    lg:flex-row
                    lg:items-center
                    lg:justify-between
                "
            >

                <div>

                    <h2
                        class="font-bold"
                        style="color:#0F2749;"
                    >
                        Horarios disponibles
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Marca los bloques en los que el profesor puede trabajar.
                        Esta disponibilidad no crea clases automáticamente.
                    </p>

                </div>


                <div
                    class="
                        flex
                        flex-wrap
                        items-center
                        gap-4
                        text-xs
                    "
                >

                    <div class="flex items-center gap-2">

                        <span
                            class="
                                h-4
                                w-4
                                rounded
                            "
                            style="background:#DB0808;"
                        ></span>

                        <span class="text-slate-600">
                            Disponible
                        </span>

                    </div>


                    <div class="flex items-center gap-2">

                        <span
                            class="
                                h-4
                                w-4
                                rounded
                                border
                                border-slate-300
                                bg-white
                            "
                        ></span>

                        <span class="text-slate-600">
                            No disponible
                        </span>

                    </div>

                </div>

            </div>

        </div>


        @php
            $horasDisponibilidad = [
                '07:00',
                '08:00',
                '09:00',
                '10:00',
                '11:00',
                '12:00',
                '13:00',
                '14:00',
                '15:00',
                '16:00',
                '17:00',
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


        {{-- =====================================================
             ESCRITORIO
        ====================================================== --}}

        <div class="hidden overflow-x-auto md:block">

            <div class="min-w-[850px] p-5">

                <div
                    id="disponibilidad-grid"
                    class="
                        grid
                        grid-cols-7
                    "
                >

                    <div
                        class="
                            border-b
                            border-r
                            border-slate-200
                            p-4
                            text-center
                        "
                    >
                        <span
                            class="
                                text-xs
                                font-bold
                                uppercase
                                tracking-wider
                            "
                        >
                            Hora
                        </span>
                    </div>


                    @foreach ($diasDisponibilidad as $dia => $nombreDia)

                        <div
                            class="
                                {{ $dia < 6 ? 'border-r' : '' }}
                                border-b
                                border-slate-200
                                bg-slate-50
                                p-3
                                text-center
                            "
                        >

                            <button
                                type="button"
                                class="
                                    select-day
                                    w-full
                                    rounded-lg
                                    px-2
                                    py-2
                                "
                                data-dia="{{ $dia }}"
                            >

                                <span
                                    class="
                                        block
                                        text-sm
                                        font-bold
                                        text-slate-700
                                    "
                                >
                                    {{ $nombreDia }}
                                </span>

                                <span
                                    class="
                                        mt-1
                                        block
                                        text-[10px]
                                        text-slate-400
                                    "
                                >
                                    Seleccionar
                                </span>

                            </button>

                        </div>

                    @endforeach


                    @foreach ($horasDisponibilidad as $hora)

                        <div
                            class="
                                border-b
                                border-r
                                border-slate-200
                                bg-slate-50
                                p-4
                                text-center
                            "
                        >

                            <span
                                class="
                                    text-xs
                                    font-semibold
                                    text-slate-600
                                "
                            >
                                {{ $hora }}
                            </span>

                        </div>


                        @for ($dia = 1; $dia <= 6; $dia++)

                            <button
                                type="button"
                                class="
                                    availability-slot
                                    border-b
                                    border-r
                                    border-slate-200
                                "
                                data-dia="{{ $dia }}"
                                data-hora="{{ $hora }}"
                                aria-label="{{ $hora }}"
                            ></button>

                        @endfor

                    @endforeach

                </div>

            </div>

        </div>


        {{-- =====================================================
             MÓVIL
        ====================================================== --}}

        <div class="p-5 md:hidden">

            <div
                class="
                    mb-4
                    flex
                    gap-2
                    overflow-x-auto
                    pb-1
                "
            >

                @foreach ($diasDisponibilidad as $dia => $nombreDia)

                    <button
                        type="button"
                        class="
                            mobile-day-tab
                            flex-shrink-0
                            rounded-lg
                            px-3
                            py-2
                            text-xs
                            font-semibold

                            {{ $dia === 1 ? 'active-mobile-day' : '' }}
                        "
                        data-dia="{{ $dia }}"
                    >
                        {{ $nombreDia }}
                    </button>

                @endforeach

            </div>


            @foreach ($diasDisponibilidad as $dia => $nombreDia)

                <div
                    class="
                        mobile-day-panel
                        {{ $dia === 1 ? '' : 'hidden' }}
                    "
                    data-dia-panel="{{ $dia }}"
                >

                    <div
                        class="
                            mb-3
                            flex
                            items-center
                            justify-between
                            gap-3
                        "
                    >

                        <span
                            class="
                                text-sm
                                font-bold
                            "
                            style="color:#0F2749;"
                        >
                            {{ $nombreDia }}
                        </span>


                        <button
                            type="button"
                            class="
                                select-day
                                disponibilidad-action-btn
                                border
                                border-slate-200
                                bg-white
                                px-3
                                py-2
                                text-xs
                                font-semibold
                                text-slate-600
                            "
                            data-dia="{{ $dia }}"
                        >

                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="m5 12 4 4L19 6"/>
                            </svg>

                            Todo el día

                        </button>

                    </div>


                    <div
                        class="
                            divide-y
                            divide-slate-100
                            overflow-hidden
                            rounded-xl
                            border
                            border-slate-200
                        "
                    >

                        @foreach ($horasDisponibilidad as $hora)

                            <button
                                type="button"
                                class="
                                    availability-slot
                                    flex
                                    w-full
                                    items-center
                                    justify-between
                                    bg-white
                                    px-4
                                    py-3
                                    text-left
                                "
                                data-dia="{{ $dia }}"
                                data-hora="{{ $hora }}"
                            >

                                <span
                                    class="
                                        text-sm
                                        font-semibold
                                        text-slate-600
                                    "
                                >
                                    {{ $hora }}
                                </span>


                                <span
                                    class="
                                        h-5
                                        w-5
                                        rounded-full
                                        border
                                        border-slate-300
                                        bg-white
                                    "
                                ></span>

                            </button>

                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>


        {{-- =====================================================
             ACCIONES
        ====================================================== --}}

        <div
            class="
                flex
                flex-col
                gap-3
                border-t
                border-slate-200
                bg-slate-50
                px-5
                py-4
                sm:flex-row
                sm:items-center
                sm:justify-between
            "
        >

            <div class="flex flex-wrap gap-2">

                <button
                    id="select-all-availability"
                    type="button"
                    class="
                        disponibilidad-action-btn
                        border
                        border-slate-200
                        bg-white
                        px-4
                        py-2.5
                        text-sm
                        font-semibold
                        text-slate-600
                    "
                >

                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m5 12 4 4L19 6"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>

                    Seleccionar todo

                </button>


                <button
                    id="clear-availability"
                    type="button"
                    class="
                        disponibilidad-action-btn
                        border
                        border-slate-200
                        bg-white
                        px-4
                        py-2.5
                        text-sm
                        font-semibold
                        text-slate-600
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

                    Limpiar

                </button>

            </div>


            <button
                id="save-availability"
                type="button"
                class="
                    disponibilidad-action-btn
                    disponibilidad-primary-btn
                    px-5
                    py-2.5
                    text-sm
                    font-bold
                "
            >

                <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"/>
                    <path d="M17 21v-8H7v8"/>
                    <path d="M7 3v5h8"/>
                </svg>

                Guardar disponibilidad

            </button>

        </div>

    </div>


    {{-- =========================================================
         HORARIOS PERSONALIZADOS
    ========================================================== --}}

    <div
        class="
            disponibilidad-card
            overflow-visible
            rounded-2xl
            bg-white
        "
    >

        <div
            class="
                border-b
                border-slate-200
                px-5
                py-4
            "
        >

            <h2
                class="font-bold"
                style="color:#0F2749;"
            >
                Horarios personalizados
            </h2>

            <p class="mt-1 text-xs text-slate-500">
                Agrega rangos exactos cuando la disponibilidad no coincida con horas completas.
            </p>

        </div>


        <div class="p-5">

            <div
                class="
                    grid
                    grid-cols-1
                    items-end
                    gap-4
                    lg:grid-cols-[1fr_1fr_1fr_auto]
                "
            >

                {{-- DÍA --}}

                <div>

                    <label
                        class="
                            mb-2
                            block
                            text-xs
                            font-semibold
                        "
                        style="color:#0F2749;"
                    >
                        Día
                    </label>


                    <select
                        id="personalizado-dia"
                        class="disponibilidad-native-select"
                    >
                        <option value="1">Lunes</option>
                        <option value="2">Martes</option>
                        <option value="3">Miércoles</option>
                        <option value="4">Jueves</option>
                        <option value="5">Viernes</option>
                        <option value="6">Sábado</option>
                    </select>


                    <div
                        class="disponibilidad-custom-select"
                        data-disponibilidad-select="personalizado-dia"
                        data-label="Día"
                        data-placeholder="Lunes"
                        data-icon="dia"
                        data-search="false"
                    ></div>

                </div>


                {{-- DESDE --}}

                <div>

                    <label
                        class="
                            mb-2
                            block
                            text-xs
                            font-semibold
                        "
                        style="color:#0F2749;"
                    >
                        Desde
                    </label>


                    <input
                        id="personalizado-inicio"
                        type="time"
                        step="60"
                        value="09:15"
                        class="
                            disponibilidad-time
                            w-full
                        "
                    >

                </div>


                {{-- HASTA --}}

                <div>

                    <label
                        class="
                            mb-2
                            block
                            text-xs
                            font-semibold
                        "
                        style="color:#0F2749;"
                    >
                        Hasta
                    </label>


                    <input
                        id="personalizado-fin"
                        type="time"
                        step="60"
                        value="10:05"
                        class="
                            disponibilidad-time
                            w-full
                        "
                    >

                </div>


                {{-- AGREGAR --}}

                <button
                    type="button"
                    id="agregar-personalizado"
                    class="
                        disponibilidad-action-btn
                        disponibilidad-primary-btn
                        h-[52px]
                        px-5
                        text-sm
                        font-bold
                    "
                >

                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>
                    </svg>

                    Agregar

                </button>

            </div>


            <div
                id="personalizados-lista"
                class="mt-5 flex flex-wrap gap-2"
            >

                <p
                    id="personalizados-vacio"
                    class="text-xs text-slate-400"
                >
                    Aún no agregaste horarios personalizados para este profesor / institución.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         INFORMACIÓN
    ========================================================== --}}

    <div
        class="
            rounded-2xl
            border
            border-slate-200
            bg-slate-50
            p-5
        "
    >

        <div class="flex items-start gap-3">

            <div
                class="disponibilidad-info-icon"
                aria-hidden="true"
            >
                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 16v-4"/>
                    <path d="M12 8h.01"/>
                </svg>
            </div>


            <div>

                <h3
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    ¿Cómo funciona?
                </h3>

                <p class="mt-1 text-sm leading-6 text-slate-600">
                    Selecciona un profesor e institución y marca los bloques
                    en los que puede trabajar.

                    Esta información se utilizará posteriormente en
                    Asignaciones para validar que una clase esté dentro
                    de la disponibilidad registrada.

                    <br><br>

                    <strong>Consejo:</strong>
                    si un profesor está disponible de 09:15 a 10:05,
                    utiliza Horarios personalizados en lugar del grid.
                </p>

            </div>

        </div>

    </div>

</div>

@endsection