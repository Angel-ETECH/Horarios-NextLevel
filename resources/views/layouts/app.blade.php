<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Next Level School')</title>

    @vite(['resources/css/app.css', 'resources/js/schedule-engine.js', 'resources/js/app.js'])

</head>


<body class="bg-slate-100 text-slate-800">


    <div class="min-h-screen">


        <!-- OVERLAY PARA MÓVIL -->
        <div
            id="sidebar-overlay"
            class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"
        ></div>


        <!-- SIDEBAR -->
        <aside
            id="sidebar"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white flex flex-col
                   transform -translate-x-full lg:translate-x-0
                   transition-transform duration-300 ease-in-out"
        >


            <!-- LOGO -->
            <div class="h-24 flex items-center justify-between px-6 border-b border-slate-800">

                <div class="flex items-center gap-3">

                    <div class="w-17 h-17 flex items-center justify-center shrink-0">
                        <img
                            src="{{ asset('images/logo-next-level.png') }}"
                            alt="Next Level School"
                            class="w-full h-full object-contain"
                        >
                    </div>

                    <div>

                        <h1 class="font-bold text-lg">
                            Next Level
                        </h1>

                        <p class="text-xs text-slate-400">
                            School
                        </p>

                    </div>

                </div>


                <!-- BOTÓN CERRAR EN MÓVIL -->
                <button
                    id="close-sidebar"
                    type="button"
                    class="lg:hidden w-9 h-9 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition"
                    aria-label="Cerrar menú"
                >
                    <!-- Icono SVG: X -->
                    <svg class="w-5 h-5 mx-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>

            </div>


            <!-- MENU -->
            <nav class="flex-1 px-4 py-6 overflow-y-auto">


                <!-- PRINCIPAL -->

                <p class="text-xs uppercase tracking-wider text-slate-500 px-3 mb-3">
                    Principal
                </p>


                <!-- DASHBOARD -->

                <a
                    href="/"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg mb-2 {{ request()->is('/') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white transition' }}"
                >

                    <!-- Icono SVG: Home -->
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                    </svg>

                    <span class="font-medium">
                        Dashboard
                    </span>

                </a>


                <!-- PROFESORES -->

                <a
                    href="{{ route('profesores.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg mb-1 {{ request()->routeIs('profesores.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white transition' }}"
                >

                    <!-- Icono SVG: User -->
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/>
                    </svg>

                    <span>
                        Profesores
                    </span>

                </a>


                <!-- CURSOS -->

                <a
                    href="{{ route('cursos.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg mb-1 {{ request()->routeIs('cursos.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white transition' }}"
                >

                    <!-- Icono SVG: Book -->
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6H20M4 12H20M4 18H14"/>
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                    </svg>

                    <span>
                        Cursos
                    </span>

                </a>


                <!-- AULAS -->

                <a
                    href="{{ route('aulas.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg mb-1 {{ request()->routeIs('aulas.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white transition' }}"
                >

                    <!-- Icono SVG: Building -->
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="16" rx="1"/>
                        <path d="M8 2V6M16 2V6M3 10H21"/>
                    </svg>

                    <span>
                        Aulas
                    </span>

                </a>


                <!-- GESTIÓN -->

                <p class="text-xs uppercase tracking-wider text-slate-500 px-3 mt-8 mb-3">
                    Gestión
                </p>


                <!-- DISPONIBILIDAD -->

                <a
                    href="{{ route('disponibilidades.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg mb-1 {{ request()->routeIs('disponibilidades.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white transition' }}"
                >

                    <!-- Icono SVG: Calendar -->
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <path d="M3 10H21M8 2V6M16 2V6"/>
                    </svg>

                    <span>
                        Disponibilidad
                    </span>

                </a>


                <!-- ASIGNACIONES -->

                <a
                    href="{{ route('asignaciones.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg mb-1 {{ request()->routeIs('asignaciones.*') ? 'bg-indigo-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white transition' }}"
                >

                    <!-- Icono SVG: Link -->
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>

                    <span>
                        Asignaciones
                    </span>

                </a>


                <!-- HORARIOS -->

                <a
                    href="{{ route('horarios.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition mb-1"
                >

                    <!-- Icono SVG: Clock -->
                    <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>

                    <span>
                        Horarios
                    </span>

                </a>


            </nav>


            <!-- USUARIO -->
            <div class="p-4 border-t border-slate-800">


                <div class="flex items-center gap-3">


                    <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center font-semibold">
                        <!-- Icono SVG: User -->
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/>
                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/>
                        </svg>
                    </div>


                    <div class="flex-1 min-w-0">

                        <p class="text-sm font-medium truncate">
                            Administrador
                        </p>

                        <p class="text-xs text-slate-400 truncate">
                            Panel de administración
                        </p>

                    </div>


                </div>


            </div>


        </aside>


        <!-- CONTENIDO PRINCIPAL -->
        <div class="lg:ml-72 min-h-screen flex flex-col">
            <!-- BOTÓN DE MENÚ SOLO EN MÓVIL -->
            <div class="lg:hidden bg-white border-b border-slate-200 px-4 py-3">
                <button
                    id="open-sidebar"
                    type="button"
                    class="w-10 h-10 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-700 transition"
                    aria-label="Abrir menú"
                >
                    <svg
                        class="w-6 h-6"
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
            </div>

            <!-- CONTENIDO -->

            <main class="flex-1 p-4 sm:p-6">

                @yield('content')

            </main>


        </div>


    </div>


</body>

</html>