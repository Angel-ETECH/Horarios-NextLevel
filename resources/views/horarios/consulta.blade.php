<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0"
    >

    <title>Consultar horario - Next Level School</title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo2.png') }}"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/consulta-horarios.js'
    ])


    <style>

        :root {
            --azul-noche: #1B3A6B;
            --azul-oscuro: #0F2749;
            --rojo: #DB0808;
            --rojo-oscuro: #8D0707;
            --borde: #DCE4EE;
        }


        /* =========================================================
           GENERAL
        ========================================================= */

        html,
        body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        img,
        svg {
            max-width: 100%;
        }

        body {
            margin: 0;
        }


        /* =========================================================
           SELECT NATIVO OCULTO
        ========================================================= */

        #consulta-selector {
            display: none;
        }


        /* =========================================================
           CUSTOM SELECT
        ========================================================= */

        .custom-select {
            position: relative;
            width: 100%;
            min-width: 0;
        }

        .custom-select-button {
            position: relative;

            display: flex;

            width: 100%;
            min-height: 58px;

            align-items: center;

            gap: 12px;

            border: 1px solid var(--borde);
            border-radius: 15px;

            background: #FFFFFF;

            padding: 9px 48px 9px 10px;

            color: #334155;

            text-align: left;

            cursor: pointer;

            box-shadow:
                0 3px 10px rgba(15,39,73,.04);

            transition:
                border-color .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        .custom-select-button:hover:not(:disabled) {
            border-color: #A9BAD0;

            box-shadow:
                0 7px 18px rgba(15,39,73,.08);
        }

        .custom-select-button:focus {
            outline: none;

            border-color: var(--azul-noche);

            box-shadow:
                0 0 0 4px rgba(27,58,107,.09);
        }

        .custom-select.open
        .custom-select-button {
            border-color: var(--azul-noche);

            box-shadow:
                0 0 0 4px rgba(27,58,107,.09);
        }

        .custom-select-button:disabled {
            cursor: not-allowed;

            background: #F1F5F9;

            color: #94A3B8;
        }

        .custom-select-icon {
            display: flex;

            width: 38px;
            height: 38px;

            flex: 0 0 38px;

            align-items: center;
            justify-content: center;

            border-radius: 11px;

            background:
                linear-gradient(
                    135deg,
                    #EEF4FB,
                    #E4ECF7
                );

            color: var(--azul-noche);
        }

        .custom-select-content {
            flex: 1;

            min-width: 0;
        }

        .custom-select-small {
            display: block;

            margin-bottom: 2px;

            color: #94A3B8;

            font-size: 9px;
            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: .08em;
        }

        #custom-select-text {
            display: block;

            max-width: 100%;

            overflow: hidden;

            color: #334155;

            font-size: 13px;
            font-weight: 700;

            white-space: nowrap;

            text-overflow: ellipsis;
        }

        .custom-select-arrow {
            position: absolute;

            top: 50%;
            right: 16px;

            width: 18px;
            height: 18px;

            transform: translateY(-50%);

            color: #64748B;

            transition: transform .2s ease;
        }

        .custom-select.open
        .custom-select-arrow {
            transform:
                translateY(-50%)
                rotate(180deg);
        }


        /* =========================================================
           MENU
        ========================================================= */

        .custom-select-menu {
            position: absolute;

            z-index: 150;

            top: calc(100% + 8px);
            left: 0;

            display: none;

            width: 100%;

            overflow: hidden;

            border: 1px solid var(--borde);

            border-radius: 16px;

            background: #FFFFFF;

            box-shadow:
                0 22px 55px rgba(15,39,73,.17);
        }

        .custom-select.open
        .custom-select-menu {
            display: block;
        }

        .custom-select-search-wrap {
            position: relative;

            padding: 10px;

            border-bottom: 1px solid #EEF2F7;

            background: #F8FAFC;
        }

        .custom-select-search-icon {
            position: absolute;

            top: 50%;
            left: 23px;

            width: 16px;
            height: 16px;

            transform: translateY(-50%);

            color: #94A3B8;
        }

        #custom-select-search {
            width: 100%;

            height: 43px;

            border: 1px solid #DCE4EE;

            border-radius: 11px;

            padding: 0 12px 0 39px;

            color: #334155;

            font-size: 12px;

            outline: none;
        }

        #custom-select-search:focus {
            border-color: var(--azul-noche);

            box-shadow:
                0 0 0 3px rgba(27,58,107,.07);
        }

        .custom-select-options {
            max-height: 260px;

            overflow-y: auto;

            padding: 7px;
        }

        .custom-select-option {
            display: flex;

            width: 100%;

            align-items: center;

            gap: 10px;

            border: 0;

            border-radius: 11px;

            background: transparent;

            padding: 10px;

            color: #334155;

            text-align: left;

            cursor: pointer;
        }

        .custom-select-option:hover {
            background: #F1F5F9;
        }

        .custom-select-option.selected {
            background: #EEF4FB;

            color: var(--azul-oscuro);
        }

        .custom-option-icon {
            display: flex;

            width: 34px;
            height: 34px;

            flex: 0 0 34px;

            align-items: center;
            justify-content: center;

            border-radius: 10px;

            background: #F1F5F9;

            color: var(--azul-noche);
        }

        .custom-option-text {
            flex: 1;

            min-width: 0;

            font-size: 12px;
            font-weight: 700;
        }

        .custom-option-check {
            width: 17px;
            height: 17px;

            color: var(--azul-noche);

            opacity: 0;
        }

        .custom-select-option.selected
        .custom-option-check {
            opacity: 1;
        }

        .custom-select-title {
            padding:
                10px
                12px
                5px;

            color: #94A3B8;

            font-size: 10px;
            font-weight: 800;

            text-transform: uppercase;

            letter-spacing: .08em;
        }

        .custom-select-empty {
            padding: 25px 15px;

            color: #94A3B8;

            font-size: 12px;

            text-align: center;
        }


        /* =========================================================
           RESULTADO
        ========================================================= */

        #consulta-info {
            width: 100%;
        }

        .resultado-icono {
            display: flex;

            width: 48px;
            height: 48px;

            flex: 0 0 48px;

            align-items: center;
            justify-content: center;

            border-radius: 14px;

            background:
                linear-gradient(
                    135deg,
                    #EDF3FA,
                    #E2EAF5
                );

            color: var(--azul-noche);
        }


        /* =========================================================
           BOTONES DESCARGA
        ========================================================= */

        #acciones-descarga {
            display: none;
        }

        #acciones-descarga.visible {
            display: flex;
        }

        .btn-descarga {
            display: inline-flex;

            min-height: 42px;

            align-items: center;
            justify-content: center;

            gap: 8px;

            border-radius: 11px;

            padding:
                0
                15px;

            font-size: 12px;
            font-weight: 700;

            transition:
                transform .18s ease,
                box-shadow .18s ease,
                background .18s ease;
        }

        .btn-descarga:hover {
            transform: translateY(-1px);
        }

        .btn-descarga-imagen {
            border:
                1px solid #DCE4EE;

            background: #FFFFFF;

            color: var(--azul-noche);
        }

        .btn-descarga-imagen:hover {
            background: #F8FAFC;

            box-shadow:
                0 6px 16px rgba(15,39,73,.07);
        }

        .btn-descarga-pdf {
            border: 0;

            background:
                linear-gradient(
                    135deg,
                    var(--rojo),
                    var(--rojo-oscuro)
                );

            color: #FFFFFF;

            box-shadow:
                0 7px 18px rgba(219,8,8,.17);
        }

        .btn-descarga-pdf:hover {
            box-shadow:
                0 10px 24px rgba(219,8,8,.22);
        }


        /* =========================================================
           HORARIO
        ========================================================= */

        #horario-container,
        #horario-render {
            width: 100%;

            max-width: 100%;
        }

        .horario-grid {
            display: grid;

            grid-template-columns:
                repeat(6, minmax(0, 1fr));

            gap: 12px;

            width: 100%;
        }

        .horario-dia {
            min-width: 0;

            overflow: hidden;

            border: 1px solid #E2E8F0;

            border-radius: 16px;

            background: #F8FAFC;
        }

        .horario-dia-header {
            border-bottom:
                1px solid #E2E8F0;

            background: #FFFFFF;

            padding:
                14px 10px;

            text-align: center;
        }

        .horario-dia-titulo {
            color: var(--azul-oscuro);

            font-size: 14px;
            font-weight: 800;
        }

        .horario-dia-contenido {
            display: flex;

            flex-direction: column;

            gap: 10px;

            padding: 10px;
        }

        .horario-clase {
            width: 100%;

            min-width: 0;

            border:
                1px solid #DCE5EF;

            border-left:
                4px solid var(--azul-noche);

            border-radius: 13px;

            background: #FFFFFF;

            padding: 12px;

            box-shadow:
                0 4px 12px rgba(15,39,73,.06);
        }

        .horario-curso {
            color: var(--azul-oscuro);

            font-size: 13px;
            font-weight: 800;
        }

        .horario-profesor {
            margin-top: 6px;

            color: #64748B;

            font-size: 11px;
        }

        .horario-datos {
            display: flex;

            flex-direction: column;

            gap: 4px;

            margin-top: 10px;

            color: #64748B;

            font-size: 11px;
        }

        .horario-hora {
            color: var(--azul-noche);

            font-weight: 800;
        }

        .horario-vacio {
            border:
                1px dashed #CBD5E1;

            border-radius: 12px;

            background: #FFFFFF;

            padding: 30px 12px;

            color: #94A3B8;

            font-size: 11px;

            text-align: center;
        }


        /* =========================================================
           MÓVIL
        ========================================================= */

        #horario-navegacion {
            display: none;
        }

        @media (
            max-width:1024px
        ) and (
            min-width:641px
        ) {

            .horario-grid {
                grid-template-columns:
                    repeat(
                        3,
                        minmax(0,1fr)
                    );
            }
        }

        @media (max-width:640px) {

            .custom-select-button {
                min-height: 56px;
            }

            .custom-select-menu {
                max-height: 285px;
            }

            .custom-select-options {
                max-height: 215px;
            }

            #acciones-descarga.visible {
                width: 100%;

                flex-direction: column;
            }

            .btn-descarga {
                width: 100%;
            }

            #horario-navegacion {
                display: flex;

                align-items: center;
                justify-content: space-between;

                gap: 8px;

                border-bottom:
                    1px solid #E2E8F0;

                background:
                    linear-gradient(
                        135deg,
                        #F8FAFC,
                        #EFF6FF
                    );

                padding: 11px 12px;
            }

            .horario-nav-btn {
                display: inline-flex;

                width: 38px;
                height: 38px;

                align-items: center;
                justify-content: center;

                border:
                    1px solid #D7E0EA;

                border-radius: 11px;

                background: #FFFFFF;

                color: var(--azul-oscuro);
            }

            .horario-nav-btn:disabled {
                opacity: .35;
            }

            #horario-dia-actual {
                color: var(--azul-oscuro);

                font-size: 12px;
                font-weight: 800;

                text-align: center;
            }

            #horario-dia-contador {
                display: block;

                margin-top: 2px;

                color: #94A3B8;

                font-size: 9px;
            }

            #horario-render {
                padding: 12px;
            }

            .horario-grid {
                display: block;
            }

            .horario-dia {
                display: none;
            }

            .horario-dia.dia-activo {
                display: block;
            }
        }


        /* =========================================================
           EXPORTACIÓN / CAPTURA
        ========================================================= */

        .exportando-horario
        #horario-navegacion {
            display: none !important;
        }

        .exportando-horario
        .horario-grid {
            display: grid !important;

            grid-template-columns:
                repeat(6, minmax(0,1fr)) !important;

            gap: 8px !important;
        }

        .exportando-horario
        .horario-dia {
            display: block !important;
        }


        /* =========================================================
           IMPRESIÓN PDF
        ========================================================= */

        @media print {

            @page {
                size: landscape;
                margin: 10mm;
            }

            body {
                background: #FFFFFF !important;
            }

            header,
            #consulta-formulario,
            #consulta-mensaje,
            #acciones-descarga,
            #horario-navegacion,
            footer {
                display: none !important;
            }

            main {
                max-width: none !important;

                padding: 0 !important;
            }

            #consulta-info {
                display: block !important;

                margin-bottom: 15px !important;

                border: 0 !important;

                box-shadow: none !important;
            }

            #horario-container {
                display: block !important;

                border: 0 !important;

                box-shadow: none !important;
            }

            #horario-render {
                padding: 0 !important;
            }

            .horario-grid {
                display: grid !important;

                grid-template-columns:
                    repeat(6, minmax(0,1fr)) !important;

                gap: 7px !important;
            }

            .horario-dia {
                display: block !important;

                break-inside: avoid;
            }

            .horario-clase {
                break-inside: avoid;

                box-shadow: none !important;
            }
        }

    </style>

</head>


<body
    class="min-h-screen text-slate-800"
    style="
        background:
            radial-gradient(
                circle at 10% 15%,
                rgba(27,58,107,.10),
                transparent 28%
            ),
            radial-gradient(
                circle at 90% 85%,
                rgba(219,8,8,.05),
                transparent 26%
            ),
            linear-gradient(
                135deg,
                #E8EDF3 0%,
                #F4F6F9 50%,
                #E9EDF2 100%
            );
    "
>

<div class="min-h-screen">


    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <header
        class="border-b border-white/10 text-white shadow-lg"
        style="
            background:
                linear-gradient(
                    135deg,
                    #0F2749,
                    #1B3A6B
                );
        "
    >

        <div
            class="
                mx-auto
                flex
                max-w-7xl
                items-center
                justify-between
                gap-4
                px-4
                py-4
                sm:px-6
            "
        >

            <div class="flex items-center gap-3">

                <img
                    src="{{ asset('images/logo-next-level.png') }}"
                    alt="Next Level School"
                    class="h-14 w-14 object-contain drop-shadow-lg"
                >

                <div>

                    <h1 class="text-lg font-extrabold text-white">
                        Next Level School
                    </h1>

                    <p class="text-xs text-slate-300">
                        Consulta de horarios
                    </p>

                </div>

            </div>


            <a
                href="/login"
                class="
                    inline-flex
                    items-center
                    gap-2
                    rounded-xl
                    border
                    border-white/15
                    bg-white/10
                    px-4
                    py-2.5
                    text-sm
                    font-semibold
                    text-white
                    backdrop-blur
                    transition
                    hover:bg-white/15
                "
            >

                <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m15 18-6-6 6-6"/>
                </svg>

                <span class="hidden sm:inline">
                    Volver
                </span>

            </a>

        </div>

    </header>


    {{-- =========================================================
         MAIN
    ========================================================== --}}

    <main
        class="
            mx-auto
            max-w-7xl
            px-4
            py-8
            sm:px-6
            lg:py-10
        "
    >


        {{-- =====================================================
             TITULO
        ====================================================== --}}

        <div class="mb-7 text-center">

            <span
                class="
                    inline-flex
                    items-center
                    rounded-full
                    bg-blue-50
                    px-3
                    py-1.5
                    text-xs
                    font-bold
                    text-[#1B3A6B]
                "
            >
                Consulta pública
            </span>

            <h2
                class="
                    mt-4
                    text-2xl
                    font-extrabold
                    tracking-tight
                    sm:text-3xl
                "
                style="color:#0F2749;"
            >
                Consulta tu horario
            </h2>

            <p
                class="
                    mx-auto
                    mt-2
                    max-w-2xl
                    text-sm
                    leading-6
                    text-slate-500
                "
            >
                Selecciona la institución y la forma en que deseas
                consultar el horario académico.
            </p>

        </div>


        {{-- =====================================================
             FORMULARIO
        ====================================================== --}}

        <section
            id="consulta-formulario"
            class="
                mb-6
                rounded-3xl
                border
                border-slate-200
                bg-white
                p-5
                shadow-xl
                shadow-slate-900/5
                sm:p-7
            "
        >


            {{-- INSTITUCIÓN --}}

            <div
                class="
                    rounded-2xl
                    border
                    border-slate-200
                    bg-slate-50/70
                    p-4
                "
            >

                <div class="mb-4 flex items-center gap-3">

                    <div
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center
                            rounded-xl
                            bg-[#0F2749]
                            text-xs
                            font-extrabold
                            text-white
                        "
                    >
                        01
                    </div>

                    <div>

                        <h3 class="font-bold text-[#0F2749]">
                            Institución
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Selecciona Colegio o Academia.
                        </p>

                    </div>

                </div>


                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                    <button
                        type="button"
                        class="
                            institucion-consulta
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            px-4
                            py-4
                            text-sm
                            font-semibold
                            text-slate-700
                            shadow-sm
                            transition
                        "
                        data-institucion="colegio"
                    >
                        Colegio
                    </button>


                    <button
                        type="button"
                        class="
                            institucion-consulta
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            px-4
                            py-4
                            text-sm
                            font-semibold
                            text-slate-700
                            shadow-sm
                            transition
                        "
                        data-institucion="academia"
                    >
                        Academia
                    </button>

                </div>

            </div>


            {{-- TIPO --}}

            <div
                class="
                    mt-5
                    rounded-2xl
                    border
                    border-slate-200
                    bg-slate-50/70
                    p-4
                "
            >

                <div class="mb-4 flex items-center gap-3">

                    <div
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center
                            rounded-xl
                            bg-[#0F2749]
                            text-xs
                            font-extrabold
                            text-white
                        "
                    >
                        02
                    </div>

                    <div>

                        <h3 class="font-bold text-[#0F2749]">
                            Consultar horario por
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Selecciona cómo deseas buscar el horario.
                        </p>

                    </div>

                </div>


                <div
                    class="
                        grid
                        grid-cols-2
                        gap-3
                        lg:grid-cols-4
                    "
                >

                    <button
                        type="button"
                        class="
                            tipo-consulta
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            px-3
                            py-4
                            text-sm
                            font-semibold
                            text-slate-700
                            shadow-sm
                            transition
                        "
                        data-tipo="profesor"
                    >
                        Profesor
                    </button>


                    <button
                        id="btn-tipo-grado"
                        type="button"
                        class="
                            tipo-consulta
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            px-3
                            py-4
                            text-sm
                            font-semibold
                            text-slate-700
                            shadow-sm
                            transition
                        "
                        data-tipo="grado"
                    >
                        Grado
                    </button>


                    <button
                        type="button"
                        class="
                            tipo-consulta
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            px-3
                            py-4
                            text-sm
                            font-semibold
                            text-slate-700
                            shadow-sm
                            transition
                        "
                        data-tipo="aula"
                    >
                        Aula
                    </button>


                    <button
                        type="button"
                        class="
                            tipo-consulta
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            px-3
                            py-4
                            text-sm
                            font-semibold
                            text-slate-700
                            shadow-sm
                            transition
                        "
                        data-tipo="curso"
                    >
                        Curso
                    </button>

                </div>

            </div>


            {{-- SELECCIONAR --}}

            <div
                class="
                    mt-5
                    rounded-2xl
                    border
                    border-slate-200
                    bg-slate-50/70
                    p-4
                "
            >

                <div class="mb-4 flex items-center gap-3">

                    <div
                        class="
                            flex
                            h-10
                            w-10
                            items-center
                            justify-center
                            rounded-xl
                            bg-[#DB0808]
                            text-xs
                            font-extrabold
                            text-white
                        "
                    >
                        03
                    </div>

                    <div>

                        <h3 class="font-bold text-[#0F2749]">
                            Seleccionar
                        </h3>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Elige la opción específica que deseas consultar.
                        </p>

                    </div>

                </div>


                <label
                    id="consulta-label"
                    class="
                        mb-2
                        block
                        text-sm
                        font-semibold
                        text-slate-700
                    "
                >
                    Seleccionar
                </label>


                <select
                    id="consulta-selector"
                    disabled
                >
                    <option value="">
                        Selecciona primero una institución
                    </option>
                </select>


                <div
                    id="custom-select"
                    class="custom-select"
                >

                    <button
                        id="custom-select-button"
                        type="button"
                        class="custom-select-button"
                        disabled
                    >

                        <span
                            id="custom-select-icon"
                            class="custom-select-icon"
                        ></span>


                        <span class="custom-select-content">

                            <span
                                id="custom-select-small"
                                class="custom-select-small"
                            >
                                Seleccionar
                            </span>

                            <span id="custom-select-text">
                                Selecciona primero una institución
                            </span>

                        </span>


                        <svg
                            class="custom-select-arrow"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m6 9 6 6 6-6"/>
                        </svg>

                    </button>


                    <div
                        id="custom-select-menu"
                        class="custom-select-menu"
                    >

                        <div class="custom-select-search-wrap">

                            <svg
                                class="custom-select-search-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle cx="11" cy="11" r="7"/>
                                <path d="m20 20-3.5-3.5"/>
                            </svg>


                            <input
                                id="custom-select-search"
                                type="text"
                                placeholder="Buscar..."
                                autocomplete="off"
                            >

                        </div>


                        <div
                            id="custom-select-options"
                            class="custom-select-options"
                        ></div>

                    </div>

                </div>


                <button
                    id="btn-consultar-horario"
                    type="button"
                    class="
                        mt-4
                        inline-flex
                        w-full
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        px-5
                        py-3.5
                        text-sm
                        font-bold
                        text-white
                        shadow-lg
                        transition
                        hover:-translate-y-0.5
                        sm:w-auto
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
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m20 20-3.5-3.5"/>
                    </svg>

                    Ver horario

                </button>

            </div>

        </section>


        {{-- MENSAJE --}}

        <div
            id="consulta-mensaje"
            class="mb-6 hidden"
        ></div>


        {{-- =====================================================
             INFORMACION RESULTADO
        ====================================================== --}}

        <section
            id="consulta-info"
            class="
                mb-5
                hidden
                rounded-2xl
                border
                border-slate-200
                bg-white
                p-5
                shadow-lg
                shadow-slate-900/5
            "
        >

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

                <div class="flex min-w-0 items-center gap-4">

                    <div class="resultado-icono">

                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect x="3" y="5" width="18" height="16" rx="2"/>
                            <path d="M8 3v4M16 3v4"/>
                            <path d="M3 10h18"/>
                        </svg>

                    </div>


                    <div class="min-w-0">

                        <p
                            id="consulta-tipo-info"
                            class="
                                text-[10px]
                                font-extrabold
                                uppercase
                                tracking-widest
                                text-slate-400
                            "
                        >
                            Resultado
                        </p>


                        <h3
                            id="consulta-nombre"
                            class="
                                mt-1
                                text-xl
                                font-extrabold
                                text-[#0F2749]
                            "
                        ></h3>


                        <p
                            id="consulta-detalle"
                            class="mt-1 text-sm text-slate-500"
                        ></p>

                    </div>

                </div>


                <div class="flex flex-col gap-3 sm:items-end">

                    <span
                        id="consulta-institucion"
                        class="
                            inline-flex
                            w-fit
                            items-center
                            rounded-full
                            bg-blue-50
                            px-3
                            py-1.5
                            text-xs
                            font-bold
                            text-[#1B3A6B]
                        "
                    >
                    </span>


                    {{-- SOLO SE MUESTRA PARA PROFESORES --}}
                    <div
                        id="acciones-descarga"
                        class="flex-wrap gap-2"
                    >

                        <button
                            id="btn-descargar-imagen"
                            type="button"
                            class="
                                btn-descarga
                                btn-descarga-imagen
                            "
                        >

                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect x="3" y="4" width="18" height="16" rx="2"/>
                                <circle cx="9" cy="10" r="2"/>
                                <path d="m21 15-5-5L5 20"/>
                            </svg>

                            Descargar imagen

                        </button>


                        <button
                            id="btn-descargar-pdf"
                            type="button"
                            class="
                                btn-descarga
                                btn-descarga-pdf
                            "
                        >

                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M6 3h9l4 4v14H6z"/>
                                <path d="M15 3v5h5"/>
                                <path d="M12 11v6"/>
                                <path d="m9 14 3 3 3-3"/>
                            </svg>

                            Descargar PDF

                        </button>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             HORARIO
        ====================================================== --}}

        <section
            id="horario-container"
            class="
                hidden
                overflow-hidden
                rounded-2xl
                border
                border-slate-200
                bg-white
                shadow-lg
                shadow-slate-900/5
            "
        >

            <div
                id="horario-navegacion"
            >

                <button
                    id="horario-dia-anterior"
                    type="button"
                    class="horario-nav-btn"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m15 18-6-6 6-6"/>
                    </svg>
                </button>


                <div id="horario-dia-actual">

                    Lunes

                    <span
                        id="horario-dia-contador"
                    >
                        1 de 6
                    </span>

                </div>


                <button
                    id="horario-dia-siguiente"
                    type="button"
                    class="horario-nav-btn"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </button>

            </div>


            <div
                id="horario-render"
                class="p-4 sm:p-5"
            >
            </div>

        </section>

    </main>


    <footer class="px-4 py-7 text-center text-xs text-slate-400">

        Next Level School · Sistema de horarios académicos

    </footer>

</div>


{{-- =========================================================
     HTML2CANVAS
     Solo se usa para descargar el horario como imagen.
========================================================== --}}

<script
    src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"
></script>

</body>
</html>