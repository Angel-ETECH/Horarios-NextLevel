@extends('layouts.app')

@section('title', 'Horarios - Next Level School')
@section('page-title', 'Horarios')

@section('content')

<style>
    :root{
        --rojo-principal:#db0808;
        --rojo-oscuro:#8d0707;
        --azul-noche:#1B3A6B;
        --azul-oscuro:#0F2749;
        --blanco:#FFFFFF;
    }

    @keyframes horarios-slide-up {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .horarios-page{
        animation: horarios-slide-up .45s ease-out both;
    }

    .horarios-card{
        border: 1px solid rgba(27,58,107,.10);
        box-shadow: 0 8px 28px rgba(15,39,73,.06);
    }

    .horarios-card-hover{
        transition:
            transform .25s ease,
            box-shadow .25s ease,
            border-color .25s ease;
    }

    .horarios-card-hover:hover{
        transform: translateY(-3px);
        box-shadow: 0 16px 42px rgba(15,39,73,.10);
        border-color: rgba(27,58,107,.18);
    }

    .horarios-input{
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .horarios-input:focus{
        border-color: var(--azul-noche) !important;
        box-shadow: 0 0 0 4px rgba(27,58,107,.09) !important;
    }

    .horarios-icon{
        box-shadow: inset 0 0 0 1px rgba(27,58,107,.08);
    }

    .vista-btn{
        position: relative;
        overflow: hidden;
    }

    .vista-btn::after{
        content: '';
        position: absolute;
        inset: auto 0 0 0;
        height: 3px;
        background: linear-gradient(
            90deg,
            var(--rojo-principal),
            var(--rojo-oscuro)
        );
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .25s ease;
    }

    .vista-btn:hover::after{
        transform: scaleX(1);
    }

    #horarios-render-container{
        min-height: 160px;
    }

    /* ==========================================
       DRAG & DROP
    ========================================== */

    .horario-clase{
        cursor: grab;

        transition:
            transform .18s ease,
            box-shadow .18s ease,
            opacity .18s ease;
    }

    .horario-clase:hover{
        transform: translateY(-2px);

        box-shadow:
            0 8px 20px
            rgba(15,39,73,.12);
    }

    .horario-clase:active{
        cursor: grabbing;
    }

    .horario-clase.dragging{
        opacity: .35;
        transform: scale(.97);
    }

    .horario-dropzone{
        transition:
            background .18s ease,
            outline .18s ease;
    }

    .horario-dropzone.drag-over{
        background:
            rgba(27,58,107,.08)
            !important;

        outline:
            2px dashed
            #1B3A6B;

        outline-offset:
            -4px;
    }

    /* ==========================================
       SCROLL DE TABLA
    ========================================== */

    .horario-scroll{
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 transparent;
    }

    .horario-scroll::-webkit-scrollbar{
        height: 8px;
    }

    .horario-scroll::-webkit-scrollbar-track{
        background: transparent;
    }

    .horario-scroll::-webkit-scrollbar-thumb{
        background: #cbd5e1;
        border-radius: 999px;
    }

    /* ==========================================
       RESPONSIVE
    ========================================== */

    @media (max-width: 640px){

        #horarios-render-container{
            padding: 1rem;
        }

        .horario-clase{
            min-width: 130px;
        }

    }

    /* ==========================================
       IMPRESIÓN
    ========================================== */

    @media print{

        aside,
        header,
        #btn-excel,
        #btn-pdf,
        #btn-reset,
        .institucion-tab,
        .vista-btn,
        #modo-edicion-horario,
        #horario-mensaje{
            display: none !important;
        }

        body{
            background: white !important;
        }

        main{
            padding: 0 !important;
        }

        .horarios-card{
            box-shadow: none !important;
            border-color: #e2e8f0 !important;
        }

        .horario-clase{
            cursor: default !important;
        }

    }
</style>


<div class="space-y-6 horarios-page">

    {{-- =========================================================
         HEADER
    ========================================================== --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex items-center gap-3">

            <div
                class="horarios-icon flex h-12 w-12 items-center justify-center rounded-2xl"
                style="background:linear-gradient(145deg,rgba(27,58,107,.12),rgba(219,8,8,.06));"
            >

                <svg
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#1B3A6B"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    aria-hidden="true"
                >
                    <rect
                        x="3"
                        y="5"
                        width="18"
                        height="16"
                        rx="2"
                    />

                    <path d="M8 3v4M16 3v4M3 10h18"/>

                    <path d="M8 14h3M13 14h3M8 18h3"/>
                </svg>

            </div>


            <div>

                <h1
                    class="text-2xl font-extrabold tracking-tight"
                    style="color:#0F2749;"
                >
                    Horarios académicos
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Consulta y organiza los horarios por profesor,
                    aula o grado.
                </p>

            </div>

        </div>


        {{-- EXPORTACIONES --}}
        <div class="flex flex-wrap gap-2">

            <button
                type="button"
                id="btn-excel"
                class="inline-flex items-center gap-2 rounded-xl border bg-white px-4 py-2.5 text-sm font-semibold transition hover:-translate-y-0.5"
                style="border-color:rgba(27,58,107,.18); color:#1B3A6B;"
            >

                <svg
                    class="h-4.5 w-4.5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 4h16v16H4z"/>
                    <path d="M4 9h16M9 4v16"/>
                    <path d="m12 13 4 4m0-4-4 4"/>
                </svg>

                Exportar Excel

            </button>


            <button
                type="button"
                id="btn-pdf"
                class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:-translate-y-0.5"
                style="background:linear-gradient(135deg,#db0808,#8d0707); box-shadow:0 8px 22px rgba(219,8,8,.18);"
            >

                <svg
                    class="h-4.5 w-4.5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M7 3h7l4 4v14H7z"/>
                    <path d="M14 3v5h5"/>
                    <path d="M9.5 14h5M9.5 17h4"/>
                </svg>

                Exportar PDF

            </button>

        </div>

    </div>


    {{-- =========================================================
         INSTITUCIÓN
    ========================================================== --}}

    <div class="horarios-card rounded-2xl bg-white p-2">

        <div class="flex flex-wrap gap-2">

            <button
                type="button"
                class="institucion-tab inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-sm"
                style="background:linear-gradient(135deg,#1B3A6B,#0F2749);"
                data-institucion="colegio"
            >

                <svg
                    class="h-4.5 w-4.5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 21h18"/>
                    <path d="M5 21V7l7-4 7 4v14"/>
                    <path d="M9 10h2M13 10h2M9 14h2M13 14h2"/>
                </svg>

                Colegio

            </button>


            <button
                type="button"
                class="institucion-tab inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                data-institucion="academia"
            >

                <svg
                    class="h-4.5 w-4.5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m3 10 9-5 9 5-9 5z"/>
                    <path d="M7 12.5V17c3 2 7 2 10 0v-4.5"/>
                    <path d="M21 10v6"/>
                </svg>

                Academia

            </button>

        </div>

    </div>


    {{-- =========================================================
         EDICIÓN DEL HORARIO
    ========================================================== --}}

    <div
        class="rounded-2xl border p-4"
        style="
            border-color:rgba(27,58,107,.15);
            background:linear-gradient(
                135deg,
                rgba(27,58,107,.055),
                rgba(219,8,8,.02)
            );
        "
    >

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-start gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-lg"
                    style="box-shadow:0 4px 16px rgba(15,39,73,.06);"
                >
                    ✋
                </div>


                <div>

                    <p
                        class="text-sm font-bold"
                        style="color:#0F2749;"
                    >
                        Edición manual del horario
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        El administrador o director puede arrastrar una clase
                        hacia otro día y hora. Antes de moverla se validará
                        disponibilidad del profesor, aula y grado.
                    </p>

                </div>

            </div>


            <span
                id="modo-edicion-horario"
                class="inline-flex shrink-0 items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700"
            >

                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

                Edición habilitada

            </span>

        </div>

    </div>


    {{-- =========================================================
         RESUMEN
    ========================================================== --}}

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        {{-- PROFESORES --}}
        <div class="horarios-card horarios-card-hover rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Profesores con clases
                    </p>

                    <p
                        class="mt-1 text-2xl font-bold text-slate-900"
                        id="total-profesores"
                    >
                        0
                    </p>

                </div>


                <div
                    class="horarios-icon flex h-12 w-12 items-center justify-center rounded-2xl"
                    style="background:rgba(27,58,107,.08);"
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
                        <circle cx="12" cy="8" r="3"/>
                        <path d="M6 21v-2a6 6 0 0 1 12 0v2"/>
                        <path d="M4 5h5M15 5h5"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- AULAS --}}
        <div class="horarios-card horarios-card-hover rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Aulas en uso
                    </p>

                    <p
                        class="mt-1 text-2xl font-bold text-slate-900"
                        id="total-aulas"
                    >
                        0
                    </p>

                </div>


                <div
                    class="horarios-icon flex h-12 w-12 items-center justify-center rounded-2xl"
                    style="background:rgba(219,8,8,.07);"
                >

                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#db0808"
                        stroke-width="1.9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M4 21V6h16v15"/>
                        <path d="M2 21h20"/>
                        <path d="M8 10h2M14 10h2M8 14h2M14 14h2"/>
                        <path d="M10 21v-3h4v3"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- CLASES --}}
        <div class="horarios-card horarios-card-hover rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Clases programadas
                    </p>

                    <p
                        class="mt-1 text-2xl font-bold text-slate-900"
                        id="total-clases"
                    >
                        0
                    </p>

                </div>


                <div
                    class="horarios-icon flex h-12 w-12 items-center justify-center rounded-2xl"
                    style="background:rgba(27,58,107,.08);"
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
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21.5z"/>
                        <path d="M20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5A2.5 2.5 0 0 1 20 21.5z"/>
                    </svg>

                </div>

            </div>

        </div>


        {{-- ESTADO --}}
        <div class="horarios-card horarios-card-hover rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-slate-500">
                        Estado
                    </p>

                    <p
                        class="mt-1 flex items-center gap-2 text-sm font-semibold"
                        style="color:#1B3A6B;"
                    >

                        <span
                            id="estado-punto"
                            class="h-2.5 w-2.5 rounded-full"
                            style="background:#10b981;"
                        >
                        </span>

                        <span id="estado-horario">
                            Sin conflictos
                        </span>

                    </p>

                    <p class="mt-1 text-[11px] text-slate-400">
                        Validado sobre las clases asignadas
                    </p>

                </div>


                <div
                    class="horarios-icon flex h-12 w-12 items-center justify-center rounded-2xl"
                    style="background:rgba(219,8,8,.07);"
                >

                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#db0808"
                        stroke-width="1.9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M12 3 19 6v5c0 4.7-2.8 8-7 10-4.2-2-7-5.3-7-10V6z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         TIPO DE VISTA
    ========================================================== --}}

    <div class="horarios-card rounded-2xl bg-white p-5">

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    ¿Qué horario deseas consultar?
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Consulta el horario por profesor, aula o grado.
                </p>

            </div>


            <label class="inline-flex items-center gap-2 text-sm text-slate-600">

                <input
                    type="checkbox"
                    id="chk-semana-completa"
                    class="h-4 w-4 rounded border-slate-300"
                    style="accent-color:#1B3A6B;"
                >

                Mostrar semana completa

            </label>

        </div>


        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">

            {{-- PROFESOR --}}
            <button
                type="button"
                class="vista-btn rounded-2xl border-2 p-4 text-left transition hover:-translate-y-0.5"
                style="border-color:#1B3A6B; background:rgba(27,58,107,.045);"
                data-vista="profesor"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-white"
                        style="background:linear-gradient(135deg,#1B3A6B,#0F2749);"
                    >
                        👨‍🏫
                    </div>


                    <div>

                        <p class="font-semibold text-slate-900">
                            Por profesor
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Consulta qué clases tiene cada profesor.
                        </p>

                    </div>

                </div>

            </button>


            {{-- AULA --}}
            <button
                type="button"
                class="vista-btn rounded-2xl border-2 border-slate-200 bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-slate-300"
                data-vista="aula"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                        style="background:rgba(219,8,8,.07);"
                    >
                        🏫
                    </div>


                    <div>

                        <p class="font-semibold text-slate-900">
                            Por aula
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Consulta las clases programadas en cada aula.
                        </p>

                    </div>

                </div>

            </button>


            {{-- AULA SEMANA COMPLETA --}}
            <button
                type="button"
                class="vista-btn rounded-2xl border-2 border-slate-200 bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-slate-300"
                data-vista="aula-completa"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                        style="background:rgba(27,58,107,.07);"
                    >
                        🗓️
                    </div>


                    <div>

                        <p class="font-semibold text-slate-900">
                            Aula completa
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Semana completa con espacios libres.
                        </p>

                    </div>

                </div>

            </button>


            {{-- GRADO --}}
            <button
                type="button"
                class="vista-btn rounded-2xl border-2 border-slate-200 bg-white p-4 text-left transition hover:-translate-y-0.5 hover:border-slate-300"
                data-vista="grado"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                        style="background:rgba(219,8,8,.07);"
                    >
                        🎓
                    </div>


                    <div>

                        <p class="font-semibold text-slate-900">
                            Por grado
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Consulta las clases de un grado o sección.
                        </p>

                    </div>

                </div>

            </button>

        </div>

    </div>


    {{-- =========================================================
         FILTROS
    ========================================================== --}}

    <div class="horarios-card rounded-2xl bg-white p-5">

        <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    Filtros
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Filtra la información que deseas visualizar y exportar.
                </p>

            </div>


            <button
                type="button"
                id="btn-reset"
                class="inline-flex items-center gap-1.5 text-sm font-semibold transition hover:opacity-75"
                style="color:#db0808;"
            >
                Restablecer filtros
            </button>

        </div>


        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

            {{-- PROFESOR --}}
            <div>

                <label
                    for="horario-profesor"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Profesor
                </label>

                <select
                    id="horario-profesor"
                    class="horarios-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition"
                >
                    <option value="todos">
                        Todos los profesores
                    </option>
                </select>

            </div>


            {{-- AULA --}}
            <div>

                <label
                    for="horario-aula"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Aula
                </label>

                <select
                    id="horario-aula"
                    class="horarios-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition"
                >
                    <option value="todos">
                        Todas las aulas
                    </option>
                </select>

            </div>


            {{-- GRADO --}}
            <div>

                <label
                    for="horario-grado"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Grado / Sección
                </label>

                <select
                    id="horario-grado"
                    class="horarios-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition"
                >
                    <option value="todos">
                        Todos
                    </option>
                </select>

            </div>


            {{-- CURSO --}}
            <div>

                <label
                    for="horario-curso"
                    class="mb-2 block text-sm font-medium text-slate-700"
                >
                    Curso
                </label>

                <select
                    id="horario-curso"
                    class="horarios-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none transition"
                >
                    <option value="todos">
                        Todos los cursos
                    </option>
                </select>

            </div>

        </div>

    </div>


    {{-- =========================================================
         HORARIOS
    ========================================================== --}}

    <div class="horarios-card overflow-hidden rounded-2xl bg-white">

        {{-- CABECERA --}}
        <div
            class="flex items-center gap-3 border-b border-slate-200 px-5 py-4"
            style="background:linear-gradient(90deg,rgba(27,58,107,.035),#fff);"
        >

            <div
                class="flex h-10 w-10 items-center justify-center rounded-xl"
                style="background:rgba(27,58,107,.08);"
            >

                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#1B3A6B"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 19V5"/>
                    <path d="M4 19h16"/>
                    <path d="M8 16v-4M12 16V8M16 16v-6"/>
                </svg>

            </div>


            <div>

                <h2
                    id="titulo-horario"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    Horario por profesor
                </h2>

                <p
                    id="subtitulo-horario"
                    class="mt-1 text-xs text-slate-500"
                >
                    Consulta las clases asignadas a cada profesor.
                </p>

            </div>

        </div>


        {{-- CONTENEDOR JS --}}
        <div
            id="horarios-render-container"
            class="space-y-6 p-5"
        >

            {{-- JS renderiza aquí --}}

        </div>

    </div>


    {{-- =========================================================
         INFORMACIÓN
    ========================================================== --}}

    <div
        class="rounded-2xl border p-5"
        style="
            border-color:rgba(27,58,107,.12);
            background:linear-gradient(
                135deg,
                rgba(27,58,107,.055),
                rgba(219,8,8,.025)
            );
        "
    >

        <div class="flex gap-3">

            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white"
                style="box-shadow:0 4px 16px rgba(15,39,73,.06);"
            >
                ℹ️
            </div>


            <div>

                <h3
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    ¿Cómo funciona el horario?
                </h3>

                <p
                    class="mt-1 text-sm leading-6"
                    style="color:#1B3A6B;"
                >

                    El horario muestra únicamente las clases que fueron creadas
                    desde la sección de Asignaciones.

                    <br><br>

                    La disponibilidad de un profesor no aparece como una clase
                    hasta que el administrador realiza una asignación.

                    <br><br>

                    <strong>Edición manual:</strong>
                    si el administrador o director necesita reorganizar una clase,
                    puede arrastrarla a otro día u hora siempre que el profesor
                    esté disponible y no exista conflicto con el aula, profesor
                    o grado.

                </p>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     MENSAJE DRAG & DROP
========================================================= --}}

<div
    id="horario-mensaje"
    class="fixed bottom-5 right-5 z-[100] hidden max-w-sm rounded-2xl border bg-white p-4 shadow-2xl"
>
</div>


{{-- =========================================================
     LIBRERÍAS DE EXPORTACIÓN
========================================================= --}}

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>

@endsection