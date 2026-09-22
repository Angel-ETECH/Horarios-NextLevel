@extends('layouts.app')

@section('title', 'Dashboard | Next Level')

@section('page-title', 'Dashboard')

@section('content')

<style>

    /* =========================================================
       DASHBOARD
    ========================================================= */

    @keyframes dashboardEntrada {

        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dashboard-page {
        animation: dashboardEntrada .4s ease-out both;
    }


    .dashboard-card {
        border: 1px solid rgba(27,58,107,.08);
        box-shadow: 0 6px 24px rgba(15,39,73,.055);
        transition:
            transform .2s ease,
            box-shadow .2s ease,
            border-color .2s ease;
    }


    .dashboard-card:hover {
        transform: translateY(-2px);
        border-color: rgba(27,58,107,.14);
        box-shadow: 0 14px 36px rgba(15,39,73,.09);
    }


    .dashboard-action {
        transition:
            transform .2s ease,
            background .2s ease,
            border-color .2s ease,
            box-shadow .2s ease;
    }


    .dashboard-action:hover {
        transform: translateX(4px);
    }

</style>


<div class="dashboard-page space-y-6">


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
                    #1B3A6B 55%,
                    #8D0707 100%
                );

            box-shadow:
                0 20px 55px rgba(15,39,73,.20);
        "
    >

        {{-- DECORACIÓN --}}
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


        <div
            class="pointer-events-none absolute inset-0 opacity-[0.035]"
            style="
                background-image:
                    linear-gradient(
                        rgba(255,255,255,.35) 1px,
                        transparent 1px
                    ),
                    linear-gradient(
                        90deg,
                        rgba(255,255,255,.35) 1px,
                        transparent 1px
                    );

                background-size:
                    42px 42px;
            "
        ></div>


        <div
            class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between"
        >

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
                            <path d="M22 10v6"/>
                            <path d="M2 10l10-5 10 5-10 5z"/>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>

                    </div>


                    <span
                        class="text-xs font-bold uppercase tracking-[0.18em] text-white/55"
                    >
                        Panel académico
                    </span>

                </div>


                <h1
                    class="text-2xl font-extrabold tracking-tight text-white md:text-4xl"
                >
                    Bienvenido a

                    <span
                        style="color:#FF7373;"
                    >
                        Next Level
                    </span>
                </h1>


                <p
                    class="mt-2 max-w-xl text-sm leading-6 text-white/65 md:text-base"
                >
                    Administra profesores, cursos, aulas y horarios
                    desde un solo lugar.
                </p>

            </div>


            {{-- FECHA --}}
            <div
                id="dashboard-fecha"
                class="inline-flex w-fit items-center gap-2 rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-xs font-semibold text-white backdrop-blur-sm"
            >

                <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <rect
                        x="3"
                        y="4"
                        width="18"
                        height="18"
                        rx="2"
                    />

                    <path
                        d="M3 10h18M8 2v4M16 2v4"
                    />
                </svg>

                Cargando fecha...

            </div>

        </div>



        {{-- =====================================================
             MINI ESTADÍSTICAS
        ====================================================== --}}

        <div
            class="relative z-10 mt-7 grid grid-cols-2 gap-4 border-t border-white/10 pt-6 md:grid-cols-4"
        >

            <div>

                <p
                    class="text-[10px] font-bold uppercase tracking-wider text-white/40"
                >
                    Profesores
                </p>

                <p
                    id="header-total-profesores"
                    class="mt-1 text-xl font-extrabold text-white"
                >
                    0
                </p>

            </div>


            <div>

                <p
                    class="text-[10px] font-bold uppercase tracking-wider text-white/40"
                >
                    Cursos
                </p>

                <p
                    id="header-total-cursos"
                    class="mt-1 text-xl font-extrabold text-white"
                >
                    0
                </p>

            </div>


            <div>

                <p
                    class="text-[10px] font-bold uppercase tracking-wider text-white/40"
                >
                    Aulas
                </p>

                <p
                    id="header-total-aulas"
                    class="mt-1 text-xl font-extrabold text-white"
                >
                    0
                </p>

            </div>


            <div>

                <p
                    class="text-[10px] font-bold uppercase tracking-wider text-white/40"
                >
                    Clases programadas
                </p>

                <p
                    id="header-total-horarios"
                    class="mt-1 text-xl font-extrabold text-white"
                >
                    0
                </p>

            </div>

        </div>

    </section>



    {{-- =========================================================
         ESTADÍSTICAS PRINCIPALES
    ========================================================== --}}

    <section
        class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4"
    >


        {{-- PROFESORES --}}
        <a
            href="{{ route('profesores.index') }}"
            class="dashboard-card group relative overflow-hidden rounded-2xl bg-white p-5"
        >

            <div
                class="absolute inset-x-0 top-0 h-1"
                style="
                    background:
                        linear-gradient(
                            90deg,
                            #1B3A6B,
                            #0F2749
                        );
                "
            ></div>


            <div class="flex items-center justify-between">

                <div>

                    <p
                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                    >
                        Profesores
                    </p>

                    <p
                        id="dashboard-total-profesores"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Registrados
                    </p>

                </div>


                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl transition group-hover:scale-105"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                #E0E7FF,
                                #C7D2FE
                            );
                    "
                >

                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#1B3A6B"
                        stroke-width="2"
                    >
                        <circle
                            cx="12"
                            cy="7"
                            r="4"
                        />

                        <path
                            d="M5.5 21a6.5 6.5 0 0 1 13 0"
                        />
                    </svg>

                </div>

            </div>

        </a>



        {{-- CURSOS --}}
        <a
            href="{{ route('cursos.index') }}"
            class="dashboard-card group relative overflow-hidden rounded-2xl bg-white p-5"
        >

            <div
                class="absolute inset-x-0 top-0 h-1"
                style="
                    background:
                        linear-gradient(
                            90deg,
                            #DB0808,
                            #8D0707
                        );
                "
            ></div>


            <div class="flex items-center justify-between">

                <div>

                    <p
                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                    >
                        Cursos
                    </p>

                    <p
                        id="dashboard-total-cursos"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Registrados
                    </p>

                </div>


                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl transition group-hover:scale-105"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                #FEE2E2,
                                #FECACA
                            );
                    "
                >

                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#8D0707"
                        stroke-width="2"
                    >
                        <path
                            d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v17H6.5A2.5 2.5 0 0 0 4 22z"
                        />

                        <path d="M4 5.5v14"/>
                    </svg>

                </div>

            </div>

        </a>



        {{-- AULAS --}}
        <a
            href="{{ route('aulas.index') }}"
            class="dashboard-card group relative overflow-hidden rounded-2xl bg-white p-5"
        >

            <div
                class="absolute inset-x-0 top-0 h-1"
                style="
                    background:
                        linear-gradient(
                            90deg,
                            #F59E0B,
                            #D97706
                        );
                "
            ></div>


            <div class="flex items-center justify-between">

                <div>

                    <p
                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                    >
                        Aulas
                    </p>

                    <p
                        id="dashboard-total-aulas"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Registradas
                    </p>

                </div>


                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl transition group-hover:scale-105"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                #FEF3C7,
                                #FDE68A
                            );
                    "
                >

                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#92400E"
                        stroke-width="2"
                    >
                        <path d="M4 21V5h16v16"/>
                        <path d="M2 21h20"/>
                        <path d="M8 9h2M14 9h2M8 13h2M14 13h2"/>
                    </svg>

                </div>

            </div>

        </a>



        {{-- HORARIOS --}}
        <a
            href="{{ route('horarios.index') }}"
            class="dashboard-card group relative overflow-hidden rounded-2xl bg-white p-5"
        >

            <div
                class="absolute inset-x-0 top-0 h-1"
                style="
                    background:
                        linear-gradient(
                            90deg,
                            #8B5CF6,
                            #6D28D9
                        );
                "
            ></div>


            <div class="flex items-center justify-between">

                <div>

                    <p
                        class="text-xs font-bold uppercase tracking-wider text-slate-400"
                    >
                        Clases
                    </p>

                    <p
                        id="dashboard-total-horarios"
                        class="mt-2 text-3xl font-extrabold"
                        style="color:#0F2749;"
                    >
                        0
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Programadas
                    </p>

                </div>


                <div
                    class="flex h-14 w-14 items-center justify-center rounded-2xl transition group-hover:scale-105"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                #F3E8FF,
                                #E9D5FF
                            );
                    "
                >

                    <svg
                        class="h-7 w-7"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#6D28D9"
                        stroke-width="2"
                    >
                        <rect
                            x="3"
                            y="5"
                            width="18"
                            height="16"
                            rx="2"
                        />

                        <path d="M8 3v4M16 3v4M3 10h18"/>
                    </svg>

                </div>

            </div>

        </a>

    </section>



    {{-- =========================================================
         PARTE INFERIOR
    ========================================================== --}}

    <section
        class="grid grid-cols-1 gap-5 xl:grid-cols-3"
    >


        {{-- =====================================================
             HORARIOS DE HOY
        ====================================================== --}}

        <div
            class="dashboard-card overflow-hidden rounded-2xl bg-white xl:col-span-2"
        >

            <div
                class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between"
            >

                <div>

                    <div class="flex items-center gap-2">

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#0F2749"
                            stroke-width="2"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="16"
                                rx="2"
                            />

                            <path
                                d="M8 3v4M16 3v4M3 10h18"
                            />
                        </svg>


                        <h2
                            class="text-lg font-extrabold"
                            style="color:#0F2749;"
                        >
                            Horarios de hoy
                        </h2>

                    </div>


                    <p class="mt-1 text-sm text-slate-400">
                        Clases programadas para el día actual.
                    </p>

                </div>


                <a
                    href="{{ route('horarios.index') }}"
                    class="inline-flex items-center gap-1 text-sm font-bold transition hover:opacity-70"
                    style="color:#DB0808;"
                >
                    Ver todos

                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m9 18 6-6-6-6"/>
                    </svg>

                </a>

            </div>



            {{-- JS PINTA LAS CLASES REALES --}}
            <div
                id="dashboard-horarios-hoy"
            >

                <div
                    class="px-6 py-12 text-center text-sm text-slate-400"
                >
                    Cargando horarios...
                </div>

            </div>



            <div
                class="flex items-center justify-between border-t border-slate-200 bg-slate-50/60 px-5 py-3"
            >

                <span
                    id="dashboard-total-hoy"
                    class="text-[10px] font-bold uppercase tracking-wider text-slate-400"
                >
                    0 clases programadas
                </span>


                <span
                    class="inline-flex items-center gap-2 text-[10px] font-semibold text-slate-400"
                >

                    <span
                        class="h-2 w-2 rounded-full"
                        style="background:#1B3A6B;"
                    ></span>

                    Hoy

                </span>

            </div>

        </div>



        {{-- =====================================================
             ACCIONES RÁPIDAS
        ====================================================== --}}

        <div
            class="dashboard-card rounded-2xl bg-white p-5"
        >

            <div>

                <div class="flex items-center gap-2">

                    <svg
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#0F2749"
                        stroke-width="2.2"
                    >
                        <path
                            d="M13 2 3 14h9l-1 8 10-12h-9z"
                        />
                    </svg>


                    <h2
                        class="text-lg font-extrabold"
                        style="color:#0F2749;"
                    >
                        Acciones rápidas
                    </h2>

                </div>


                <p
                    class="mt-1 text-sm text-slate-400"
                >
                    Accede directamente a las funciones principales.
                </p>

            </div>



            <div class="mt-5 space-y-3">


                {{-- PROFESOR --}}
                <a
                    href="{{ route('profesores.index') }}"
                    class="dashboard-action flex items-center gap-3 rounded-xl border border-transparent bg-slate-50 p-3.5 hover:border-indigo-200 hover:bg-indigo-50"
                >

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        style="background:#E0E7FF;"
                    >

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#1B3A6B"
                            stroke-width="2"
                        >
                            <circle
                                cx="10"
                                cy="8"
                                r="4"
                            />

                            <path
                                d="M3 21a7 7 0 0 1 14 0M19 8v6M16 11h6"
                            />
                        </svg>

                    </div>


                    <div class="min-w-0 flex-1">

                        <p
                            class="text-sm font-bold"
                            style="color:#0F2749;"
                        >
                            Agregar profesor
                        </p>

                        <p class="text-xs text-slate-400">
                            Ir al módulo de profesores
                        </p>

                    </div>


                    <span class="text-slate-300">
                        →
                    </span>

                </a>



                {{-- CURSO --}}
                <a
                    href="{{ route('cursos.index') }}"
                    class="dashboard-action flex items-center gap-3 rounded-xl border border-transparent bg-slate-50 p-3.5 hover:border-red-200 hover:bg-red-50"
                >

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        style="background:#FEE2E2;"
                    >
                        📘
                    </div>


                    <div class="min-w-0 flex-1">

                        <p
                            class="text-sm font-bold"
                            style="color:#0F2749;"
                        >
                            Agregar curso
                        </p>

                        <p class="text-xs text-slate-400">
                            Ir al módulo de cursos
                        </p>

                    </div>


                    <span class="text-slate-300">
                        →
                    </span>

                </a>



                {{-- ASIGNACIÓN --}}
                <a
                    href="{{ route('asignaciones.index') }}"
                    class="dashboard-action flex items-center gap-3 rounded-xl border border-transparent bg-slate-50 p-3.5 hover:border-blue-200 hover:bg-blue-50"
                >

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        style="background:#DBEAFE;"
                    >
                        🔗
                    </div>


                    <div class="min-w-0 flex-1">

                        <p
                            class="text-sm font-bold"
                            style="color:#0F2749;"
                        >
                            Crear asignación
                        </p>

                        <p class="text-xs text-slate-400">
                            Programar una nueva clase
                        </p>

                    </div>


                    <span class="text-slate-300">
                        →
                    </span>

                </a>



                {{-- HORARIOS --}}
                <a
                    href="{{ route('horarios.index') }}"
                    class="dashboard-action flex items-center gap-3 rounded-xl border border-transparent bg-slate-50 p-3.5 hover:border-purple-200 hover:bg-purple-50"
                >

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        style="background:#F3E8FF;"
                    >
                        🗓️
                    </div>


                    <div class="min-w-0 flex-1">

                        <p
                            class="text-sm font-bold"
                            style="color:#0F2749;"
                        >
                            Ver horarios
                        </p>

                        <p class="text-xs text-slate-400">
                            Consultar horario académico
                        </p>

                    </div>


                    <span class="text-slate-300">
                        →
                    </span>

                </a>

            </div>

        </div>

    </section>

</div>

@endsection