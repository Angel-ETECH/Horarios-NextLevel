<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Next Level School')</title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo2.png') }}"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        /* =========================================================
           SIDEBAR
        ========================================================= */

        #sidebar-nav {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        #sidebar-nav::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .sidebar-link {
            min-height: 44px;
        }

        @media (min-width: 1024px) {
            #sidebar-nav {
                overflow-y: auto;
            }
        }
    </style>
</head>


<body class="invisible bg-slate-100 text-slate-800">


<div class="min-h-screen">


    {{-- =========================================================
         OVERLAY MÓVIL
    ========================================================== --}}
    <div
        id="sidebar-overlay"
        class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"
    ></div>



    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}
    <aside
        id="sidebar"
        class="
            fixed inset-y-0 left-0 z-50
            flex w-72 -translate-x-full flex-col
            bg-slate-900 text-white
            transition-transform duration-300 ease-in-out
            lg:translate-x-0
        "
    >


        {{-- =====================================================
             LOGO
        ====================================================== --}}
        <div
            class="
                flex h-20 shrink-0
                items-center justify-between
                border-b border-slate-800
                px-5
            "
        >

            <div class="flex min-w-0 items-center gap-3">

                <div
                    class="
                        flex h-14 w-14 shrink-0
                        items-center justify-center
                    "
                >
                    <img
                        src="{{ asset('images/logo-next-level.png') }}"
                        alt="Next Level School"
                        class="h-full w-full object-contain"
                    >
                </div>


                <div class="min-w-0">

                    <h1
                        class="
                            truncate
                            text-lg font-bold text-white
                        "
                    >
                        Next Level
                    </h1>

                    <p
                        class="
                            truncate
                            text-xs text-slate-400
                        "
                    >
                        School
                    </p>

                </div>

            </div>


            {{-- CERRAR SIDEBAR EN MÓVIL --}}
            <button
                id="close-sidebar"
                type="button"
                class="
                    flex h-9 w-9 shrink-0
                    items-center justify-center
                    rounded-lg
                    text-slate-400
                    transition
                    hover:bg-slate-800
                    hover:text-white
                    lg:hidden
                "
                aria-label="Cerrar menú"
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
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>

        </div>



        {{-- =====================================================
             NAVEGACIÓN
        ====================================================== --}}
        <nav
            id="sidebar-nav"
            class="
                min-h-0 flex-1
                overflow-y-auto
                px-4 py-4
            "
        >


            {{-- =================================================
                 PRINCIPAL
            ================================================== --}}
            <p
                class="
                    mb-2 px-3
                    text-[11px] font-medium
                    uppercase tracking-[0.14em]
                    text-slate-500
                "
            >
                Principal
            </p>



            {{-- DASHBOARD --}}
            <a
                href="/"
                class="
                    sidebar-link
                    mb-1 flex items-center gap-3
                    rounded-xl px-3 py-2.5
                    transition

                    {{
                        request()->is('/')
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                "
            >

                <svg
                    class="h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 12 12 4l9 8"/>
                    <path d="M5 10v10h5v-6h4v6h5V10"/>
                </svg>

                <span class="font-medium">
                    Dashboard
                </span>

            </a>



            {{-- PROFESORES --}}
            <a
                href="{{ route('profesores.index') }}"
                class="
                    sidebar-link
                    mb-1 flex items-center gap-3
                    rounded-xl px-3 py-2.5
                    transition

                    {{
                        request()->routeIs('profesores.*')
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                "
            >

                <svg
                    class="h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M5 21a7 7 0 0 1 14 0"/>
                </svg>

                <span>
                    Profesores
                </span>

            </a>



            {{-- CURSOS --}}
            <a
                href="{{ route('cursos.index') }}"
                class="
                    sidebar-link
                    mb-1 flex items-center gap-3
                    rounded-xl px-3 py-2.5
                    transition

                    {{
                        request()->routeIs('cursos.*')
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                "
            >

                <svg
                    class="h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M7 8h10M7 12h10M7 16h6"/>
                </svg>

                <span>
                    Cursos
                </span>

            </a>



            {{-- AULAS --}}
            <a
                href="{{ route('aulas.index') }}"
                class="
                    sidebar-link
                    mb-1 flex items-center gap-3
                    rounded-xl px-3 py-2.5
                    transition

                    {{
                        request()->routeIs('aulas.*')
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                "
            >

                <svg
                    class="h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="4" width="18" height="16" rx="1"/>
                    <path d="M8 2v4M16 2v4M3 10h18"/>
                </svg>

                <span>
                    Aulas
                </span>

            </a>
            <a
    href="{{ route('grados.index') }}"
    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg mb-1
           {{ request()->routeIs('grados.*')
                ? 'bg-indigo-600 text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white transition' }}"
>
    <svg
        class="w-5 h-5 flex-shrink-0"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <path d="M4 19.5V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14.5"/>
        <path d="M8 7h8M8 11h8M8 15h5"/>
    </svg>

    <span>
        Grados
    </span>
</a>



            {{-- =================================================
                 GESTIÓN
            ================================================== --}}
            <p
                class="
                    mb-2 mt-5 px-3
                    text-[11px] font-medium
                    uppercase tracking-[0.14em]
                    text-slate-500
                "
            >
                Gestión
            </p>
            



            {{-- DISPONIBILIDAD --}}
            <a
                href="{{ route('disponibilidades.index') }}"
                class="
                    sidebar-link
                    mb-1 flex items-center gap-3
                    rounded-xl px-3 py-2.5
                    transition

                    {{
                        request()->routeIs('disponibilidades.*')
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                "
            >

                <svg
                    class="h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M3 10h18M8 2v4M16 2v4"/>
                </svg>

                <span>
                    Disponibilidad
                </span>

            </a>



            {{-- ASIGNACIONES --}}
            <a
                href="{{ route('asignaciones.index') }}"
                class="
                    sidebar-link
                    mb-1 flex items-center gap-3
                    rounded-xl px-3 py-2.5
                    transition

                    {{
                        request()->routeIs('asignaciones.*')
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                "
            >

                <svg
                    class="h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                </svg>

                <span>
                    Asignaciones
                </span>

            </a>



            {{-- HORARIOS --}}
            <a
                href="{{ route('horarios.index') }}"
                class="
                    sidebar-link
                    mb-1 flex items-center gap-3
                    rounded-xl px-3 py-2.5
                    transition

                    {{
                        request()->routeIs('horarios.*')
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                "
            >

                <svg
                    class="h-5 w-5 shrink-0"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>

                <span>
                    Horarios
                </span>

            </a>



            {{-- HISTORIAL --}}
            <a
                href="{{ route('historial.index') }}"
                class="
                    sidebar-link
                    mb-1 flex items-center gap-3
                    rounded-xl px-3 py-2.5
                    transition

                    {{
                        request()->routeIs('historial.*')
                            ? 'bg-indigo-600 text-white shadow-sm'
                            : 'text-slate-300 hover:bg-slate-800 hover:text-white'
                    }}
                "
            >

                <svg
                    class="h-5 w-5 shrink-0"
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

                <span>
                    Historial
                </span>

            </a>


            {{-- ESPACIO FINAL PARA QUE NUNCA QUEDE PEGADO --}}
            <div class="h-3"></div>


        </nav>



        {{-- =====================================================
             USUARIO
        ====================================================== --}}
        <div
            class="
                shrink-0
                border-t border-slate-800
                bg-slate-900
                px-4 py-3
            "
        >


            <div class="flex items-center gap-3">


                <div
                    class="
                        flex h-10 w-10 shrink-0
                        items-center justify-center
                        rounded-full
                        bg-indigo-500
                    "
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
                        <circle cx="12" cy="7" r="4"/>
                        <path d="M5 21a7 7 0 0 1 14 0"/>
                    </svg>

                </div>


                <div class="min-w-0 flex-1">

                    <p
                        id="sidebar-nombre-usuario"
                        class="
                            truncate
                            text-sm font-semibold text-white
                        "
                    >
                        Administrador
                    </p>

                    <p
                        class="
                            truncate
                            text-xs text-slate-400
                        "
                    >
                        Panel de administración
                    </p>

                </div>


            </div>



            {{-- CERRAR SESIÓN --}}
            <button
                type="button"
                id="btn-cerrar-sesion"
                class="
                    mt-3 flex w-full
                    items-center justify-center gap-2
                    rounded-lg
                    border border-slate-700
                    px-3 py-2
                    text-sm font-medium text-slate-300
                    transition
                    hover:border-red-500/40
                    hover:bg-red-500/10
                    hover:text-red-400
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
                    <path d="M10 17l5-5-5-5"/>
                    <path d="M15 12H3"/>
                    <path d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"/>
                </svg>

                <span>
                    Cerrar sesión
                </span>

            </button>


        </div>


    </aside>



    {{-- =========================================================
         CONTENIDO PRINCIPAL
    ========================================================== --}}
    <div
        class="
            min-h-screen
            lg:ml-72
        "
    >


        {{-- =====================================================
             HEADER MÓVIL
        ====================================================== --}}
        <div
            class="
                border-b border-slate-200
                bg-white
                px-4 py-3
                lg:hidden
            "
        >

            <div class="flex items-center justify-between">


                <button
                    id="open-sidebar"
                    type="button"
                    class="
                        flex h-10 w-10
                        items-center justify-center
                        rounded-lg
                        text-slate-700
                        transition
                        hover:bg-slate-100
                    "
                    aria-label="Abrir menú"
                >

                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>

                </button>


                <div class="flex items-center gap-2">

                    <img
                        src="{{ asset('images/logo-next-level.png') }}"
                        alt="Next Level School"
                        class="h-9 w-9 object-contain"
                    >

                    <span
                        class="text-sm font-bold"
                        style="color:#0F2749;"
                    >
                        Next Level
                    </span>

                </div>


            </div>

        </div>



        {{-- =====================================================
             CONTENIDO
        ====================================================== --}}
        <main class="min-h-screen p-4 sm:p-6">

            @yield('content')

        </main>


    </div>


</div>


</body>

</html>
