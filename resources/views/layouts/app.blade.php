<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Next Level School')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-100 text-slate-800">

    <div class="min-h-screen flex">

        <!-- SIDEBAR -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col">

            <!-- LOGO -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800">

                <div class="flex items-center gap-3">

                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-lg">
                        NL
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

            </div>


            <!-- MENU -->
            <nav class="flex-1 px-4 py-6">

                <p class="text-xs uppercase tracking-wider text-slate-500 px-3 mb-3">
                    Principal
                </p>

                <a href="/"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg bg-indigo-600 text-white mb-2">

                    <span>🏠</span>

                    <span class="font-medium">
                        Dashboard
                    </span>

                </a>


                <a href="#"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">

                    <span>👨‍🏫</span>

                    <span>
                        Profesores
                    </span>

                </a>


                <a href="#"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">

                    <span>📚</span>

                    <span>
                        Cursos
                    </span>

                </a>


                <a href="#"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">

                    <span>🏫</span>

                    <span>
                        Aulas
                    </span>

                </a>


                <p class="text-xs uppercase tracking-wider text-slate-500 px-3 mt-8 mb-3">
                    Gestión
                </p>


                <a href="#"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">

                    <span>📅</span>

                    <span>
                        Disponibilidad
                    </span>

                </a>


                <a href="#"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">

                    <span>🔗</span>

                    <span>
                        Asignaciones
                    </span>

                </a>


                <a href="#"
                   class="flex items-center gap-3 px-3 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">

                    <span>🗓️</span>

                    <span>
                        Horarios
                    </span>

                </a>

            </nav>


            <!-- USER -->
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
        <div class="flex-1 flex flex-col min-w-0">

            <!-- NAVBAR -->
            <header class="h-20 bg-white border-b border-slate-200 flex items-center justify-between px-6">

                <div>

                    <h2 class="text-xl font-semibold text-slate-800">
                        @yield('page-title', 'Dashboard')
                    </h2>

                    <p class="text-sm text-slate-500">
                        Sistema de gestión de horarios
                    </p>

                </div>


                <div class="flex items-center gap-4">

                    <!-- NOTIFICACIONES -->
                    <button class="relative w-10 h-10 rounded-lg hover:bg-slate-100 flex items-center justify-center transition">

                        🔔

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
            <main class="flex-1 p-6">

                @yield('content')

            </main>

        </div>

    </div>

</body>
</html>