<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Next Level School')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                    ✕
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
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg bg-indigo-600 text-white mb-2"
                >

                    <span class="text-lg">
                        🏠
                    </span>

                    <span class="font-medium">
                        Dashboard
                    </span>

                </a>


                <!-- PROFESORES -->

                <a
                    href="{{ route('profesores.index') }}"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition mb-1"
                >

                    <span class="text-lg">
                        👨‍🏫
                    </span>

                    <span>
                        Profesores
                    </span>

                </a>


                <!-- CURSOS -->

                <a
                    href="#"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition mb-1"
                >

                    <span class="text-lg">
                        📚
                    </span>

                    <span>
                        Cursos
                    </span>

                </a>


                <!-- AULAS -->

                <a
                    href="#"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition mb-1"
                >

                    <span class="text-lg">
                        🏫
                    </span>

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
                    href="#"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition mb-1"
                >

                    <span class="text-lg">
                        📅
                    </span>

                    <span>
                        Disponibilidad
                    </span>

                </a>


                <!-- ASIGNACIONES -->

                <a
                    href="#"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition mb-1"
                >

                    <span class="text-lg">
                        🔗
                    </span>

                    <span>
                        Asignaciones
                    </span>

                </a>


                <!-- HORARIOS -->

                <a
                    href="#"
                    class="sidebar-link flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition mb-1"
                >

                    <span class="text-lg">
                        🗓️
                    </span>

                    <span>
                        Horarios
                    </span>

                </a>


            </nav>


            <!-- USUARIO -->
            <div class="p-4 border-t border-slate-800">


                <div class="flex items-center gap-3">


                    <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center font-semibold">
                        A
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


            <!-- NAVBAR -->
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6">


                <div class="flex items-center gap-3">


                    <!-- BOTÓN MENÚ MÓVIL -->

                    <button
                        id="open-sidebar"
                        type="button"
                        class="lg:hidden w-10 h-10 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-700 transition"
                        aria-label="Abrir menú"
                    >

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-6 h-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M4 6h16M4 12h16M4 18h16"
                            />

                        </svg>

                    </button>


                    <!-- TÍTULO -->

                    <div>

                        <h2 class="text-lg sm:text-xl font-semibold text-slate-800">

                            @yield('page-title', 'Dashboard')

                        </h2>

                        <p class="hidden sm:block text-sm text-slate-500">

                            Sistema de gestión de horarios

                        </p>

                    </div>


                </div>


                <!-- PARTE DERECHA -->

                <div class="flex items-center gap-2 sm:gap-4">


                    <!-- NOTIFICACIONES -->

                    <button
                        type="button"
                        class="relative w-10 h-10 rounded-lg hover:bg-slate-100 flex items-center justify-center transition"
                        aria-label="Notificaciones"
                    >

                        <span class="text-lg">
                            🔔
                        </span>

                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>

                    </button>


                    <!-- PERFIL -->

                    <div class="flex items-center gap-3">


                        <div class="hidden sm:block text-right">

                            <p class="text-sm font-medium">
                                Administrador
                            </p>

                            <p class="text-xs text-slate-500">
                                Administrador
                            </p>

                        </div>


                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold">
                            A
                        </div>


                    </div>


                </div>


            </header>


            <!-- CONTENIDO -->

            <main class="flex-1 p-4 sm:p-6">

                @yield('content')

            </main>


        </div>


    </div>


</body>

</html>