<!DOCTYPE html>
<html lang="es">

<head>

    {{-- =========================================================
         CONFIGURACIÓN BÁSICA DE LA PÁGINA
         ========================================================= --}}

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Iniciar sesión - Next Level School</title>


    {{-- Favicon de la pestaña del navegador --}}
    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo2.png') }}"
    >


    {{-- CSS Y JAVASCRIPT PRINCIPAL DEL PROYECTO --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


{{-- =============================================================
     CUERPO GENERAL
     El fondo combina gris, azul y rojo de forma suave.
     ============================================================= --}}

<body
    class="min-h-screen"
    style="
        background:
            radial-gradient(
                circle at 15% 20%,
                rgba(27,58,107,.10),
                transparent 32%
            ),
            radial-gradient(
                circle at 90% 85%,
                rgba(219,8,8,.06),
                transparent 28%
            ),
            linear-gradient(
                135deg,
                #E8EDF3 0%,
                #F3F5F8 48%,
                #E9EDF2 100%
            );
    "
>


    <div class="relative min-h-screen overflow-hidden">


        {{-- =========================================================
             DECORACIÓN EXTERIOR
             Son manchas difuminadas que ayudan a combinar el fondo
             con los colores azul y rojo de la institución.
             ========================================================= --}}

        <div
            class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full opacity-15 blur-3xl"
            style="background:#1B3A6B;"
        ></div>

        <div
            class="pointer-events-none absolute -bottom-40 -right-32 h-[420px] w-[420px] rounded-full opacity-10 blur-3xl"
            style="background:#DB0808;"
        ></div>



        {{-- =========================================================
             CENTRADO DEL LOGIN
             ========================================================= --}}

        <div
            class="relative flex min-h-screen items-center justify-center px-4 py-8"
        >


            {{-- =====================================================
                 CONTENEDOR PRINCIPAL
                 En escritorio divide la pantalla en dos columnas:
                 izquierda = presentación
                 derecha   = formulario de login
                 ===================================================== --}}

            <div
                class="grid w-full max-w-6xl overflow-hidden rounded-[30px]
                       border border-slate-200 bg-white
                       shadow-2xl lg:grid-cols-2"
                style="
                    box-shadow:
                        0 30px 80px rgba(15,39,73,.16);
                "
            >



                {{-- =================================================
                     PANEL IZQUIERDO
                     Solo aparece en pantallas grandes.
                     Contiene imagen, logo y presentación.
                     ================================================= --}}

                <div
                    class="relative hidden min-h-[700px] overflow-hidden
                           px-10 py-12 text-white
                           lg:flex lg:flex-col lg:justify-between"
                >


                    {{-- FOTO DE FONDO
                         object-cover hace que la imagen llene todo
                         el panel sin deformarse. --}}

                    <img
                        src="{{ asset('images/Academia-Next-Level.jpeg') }}"
                        alt=""
                        aria-hidden="true"
                        class="pointer-events-none absolute inset-0 h-full w-full object-cover"
                        style="
                            object-position:52% center;

                            filter:
                                grayscale(100%)
                                saturate(25%)
                                brightness(48%)
                                contrast(110%);

                            transform:scale(1.025);
                        "
                    >



                    {{-- CAPA OSCURA SOBRE LA FOTO
                         Facilita la lectura del texto blanco. --}}

                    <div
                        class="pointer-events-none absolute inset-0"
                        style="
                            background:
                                linear-gradient(
                                    135deg,
                                    rgba(7,18,32,.82) 0%,
                                    rgba(15,39,73,.76) 50%,
                                    rgba(12,26,43,.86) 100%
                                );
                        "
                    ></div>



                    {{-- SOMBRA INFERIOR DE LA FOTO --}}

                    <div
                        class="pointer-events-none absolute inset-x-0 bottom-0 h-72"
                        style="
                            background:
                                linear-gradient(
                                    to top,
                                    rgba(4,12,23,.68),
                                    transparent
                                );
                        "
                    ></div>



                    {{-- SOMBRA SUPERIOR DE LA FOTO --}}

                    <div
                        class="pointer-events-none absolute inset-x-0 top-0 h-52"
                        style="
                            background:
                                linear-gradient(
                                    to bottom,
                                    rgba(4,12,23,.35),
                                    transparent
                                );
                        "
                    ></div>



                    {{-- DETALLE ROJO SUPERIOR DERECHO --}}

                    <div
                        class="absolute right-0 top-0 z-10 h-1.5 w-36"
                        style="background:#DB0808;"
                    ></div>



                    {{-- =================================================
                         LOGO Y NOMBRE DE NEXT LEVEL
                         ================================================= --}}

                    <div class="relative z-20">

                        <div class="flex items-center gap-4">


                            {{-- Logo transparente --}}
                            <div
                                class="flex h-20 w-20 shrink-0 items-center justify-center"
                            >

                                <img
                                    src="{{ asset('images/logo-next-level.png') }}"
                                    alt="Next Level School"
                                    class="h-full w-full object-contain drop-shadow-xl"
                                >

                            </div>



                            {{-- Nombre de la institución --}}
                            <div>

                                <p
                                    class="text-2xl font-extrabold tracking-tight text-white"
                                >
                                    Next Level
                                </p>


                                <div class="mt-1 flex items-center gap-2">

                                    {{-- Línea roja --}}
                                    <span
                                        class="h-[3px] w-8 rounded-full"
                                        style="background:#DB0808;"
                                    ></span>


                                    <span
                                        class="text-sm font-semibold tracking-[0.25em] text-slate-200"
                                    >
                                        SCHOOL
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>



                    {{-- =================================================
                         CONTENIDO CENTRAL DEL PANEL IZQUIERDO
                         ================================================= --}}

                    <div
                        class="relative z-20 my-auto py-12"
                    >


                        {{-- Etiqueta pequeña --}}
                        <span
                            class="inline-flex items-center gap-2
                                   rounded-full border border-white/15
                                   bg-black/20 px-4 py-2
                                   text-xs font-semibold text-white
                                   backdrop-blur-sm"
                        >

                            <span
                                class="h-2 w-2 rounded-full"
                                style="background:#DB0808;"
                            ></span>

                            Gestión académica

                        </span>



                        {{-- Título principal --}}
                        <h1
                            class="mt-7 max-w-lg text-4xl
                                   font-extrabold leading-[1.18]
                                   tracking-tight text-white"
                        >

                            Organiza tus horarios de forma

                            <span
                                class="block"
                                style="color:#FF6262;"
                            >
                                simple y eficiente.
                            </span>

                        </h1>



                        {{-- Descripción --}}
                        <p
                            class="mt-6 max-w-lg text-[15px]
                                   leading-7 text-slate-200"
                        >
                            Administra profesores, cursos, aulas,
                            disponibilidades, asignaciones y horarios
                            desde un solo lugar.
                        </p>



                        {{-- Pequeña línea decorativa --}}
                        <div
                            class="mt-8 h-px w-20 bg-white/30"
                        ></div>

                    </div>



                    {{-- =================================================
                         PARTE INFERIOR DEL PANEL IZQUIERDO
                         ================================================= --}}

                    <div
                        class="relative z-20 flex items-center gap-3
                               border-t border-white/15 pt-6"
                    >


                        {{-- Icono de seguridad --}}
                        <div
                            class="flex h-10 w-10 shrink-0
                                   items-center justify-center
                                   rounded-xl border border-white/10
                                   bg-white/10 backdrop-blur-sm"
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
                                <path
                                    d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4z"
                                />

                                <path
                                    d="M9 12l2 2 4-4"
                                />
                            </svg>

                        </div>



                        {{-- Descripción del acceso --}}
                        <div>

                            <p class="text-xs font-bold text-white">
                                Acceso administrativo
                            </p>

                            <p class="mt-0.5 text-[11px] text-slate-300">
                                Sistema de gestión de horarios
                            </p>

                        </div>

                    </div>

                </div>



                {{-- =================================================
                     PANEL DERECHO
                     Contiene el formulario de inicio de sesión.
                     ================================================= --}}

                <div
                    class="relative flex min-h-[700px]
                           flex-col justify-center
                           px-6 py-8
                           sm:px-10 sm:py-10
                           lg:px-14 lg:py-12"
                >



                    {{-- =================================================
                         LOGO PARA CELULARES Y TABLETS
                         El panel izquierdo se oculta en móvil,
                         por eso aquí se muestra nuevamente el logo.
                         ================================================= --}}

                    <div
                        class="mb-8 flex items-center justify-center lg:hidden"
                    >

                        <div class="text-center">

                            <div
                                class="mx-auto flex h-20 w-20
                                       items-center justify-center
                                       rounded-2xl bg-white p-2
                                       shadow-lg ring-1 ring-slate-200"
                            >

                                <img
                                    src="{{ asset('images/logo-next-level.png') }}"
                                    alt="Next Level School"
                                    class="h-full w-full object-contain"
                                >

                            </div>


                            <h1
                                class="mt-4 text-2xl font-extrabold"
                                style="color:#0F2749;"
                            >
                                Next Level School
                            </h1>

                        </div>

                    </div>



                    {{-- =================================================
                         ENCABEZADO DEL FORMULARIO
                         ================================================= --}}

                    <div class="mb-8">

                        <div class="mb-4 flex items-center gap-3">

                            <span
                                class="h-1 w-9 rounded-full"
                                style="background:#DB0808;"
                            ></span>

                            <span
                                class="text-xs font-bold uppercase tracking-[0.20em]"
                                style="color:#DB0808;"
                            >
                                Bienvenido
                            </span>

                        </div>


                        <h2
                            class="text-3xl font-extrabold tracking-tight sm:text-4xl"
                            style="color:#0F2749;"
                        >
                            Iniciar sesión
                        </h2>


                        <p
                            class="mt-3 text-sm leading-6 text-slate-500"
                        >
                            Ingresa tus credenciales para acceder
                            al panel administrativo.
                        </p>

                    </div>



                    {{-- =================================================
                         MENSAJES DEL LOGIN
                         Aquí aparecen errores o confirmación de acceso.
                         ================================================= --}}

                    <div
                        id="login-mensaje"
                        class="mb-5 hidden rounded-xl border
                               px-4 py-3 text-sm font-medium"
                    >
                    </div>



                    {{-- =================================================
                         FORMULARIO DE LOGIN
                         ================================================= --}}

                    <form
                        id="login-form"
                        class="space-y-5"
                        autocomplete="off"
                    >


                        {{-- =============================================
                             CAMPO USUARIO
                             ============================================= --}}

                        <div>

                            <label
                                for="usuario"
                                class="mb-2 block text-sm
                                       font-semibold text-slate-700"
                            >
                                Usuario
                            </label>


                            <div class="relative">


                                {{-- Icono de usuario --}}
                                <div
                                    class="pointer-events-none absolute
                                           inset-y-0 left-0
                                           flex items-center pl-4
                                           text-slate-400"
                                >

                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <circle
                                            cx="12"
                                            cy="8"
                                            r="4"
                                        ></circle>

                                        <path
                                            d="M4 21a8 8 0 0 1 16 0"
                                        ></path>
                                    </svg>

                                </div>



                                {{-- Entrada de usuario --}}
                                <input
                                    type="text"
                                    id="usuario"
                                    name="nextlevel_demo_usuario"
                                    autocomplete="off"
                                    placeholder="Ingresa tu usuario"
                                    class="w-full rounded-xl
                                           border border-slate-200
                                           bg-slate-50
                                           py-3.5 pl-12 pr-4
                                           text-sm text-slate-800
                                           outline-none transition
                                           hover:border-slate-300
                                           focus:border-[#1B3A6B]
                                           focus:bg-white
                                           focus:ring-4
                                           focus:ring-blue-100"
                                >

                            </div>

                        </div>



                        {{-- =============================================
                             CAMPO CONTRASEÑA
                             ============================================= --}}

                        <div>

                            <label
                                for="password"
                                class="mb-2 block text-sm
                                       font-semibold text-slate-700"
                            >
                                Contraseña
                            </label>


                            <div class="relative">


                                {{-- Icono de candado --}}
                                <div
                                    class="pointer-events-none absolute
                                           inset-y-0 left-0
                                           flex items-center pl-4
                                           text-slate-400"
                                >

                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >

                                        <rect
                                            x="4"
                                            y="10"
                                            width="16"
                                            height="10"
                                            rx="2"
                                        ></rect>

                                        <path
                                            d="M8 10V7a4 4 0 0 1 8 0v3"
                                        ></path>

                                    </svg>

                                </div>



                                {{-- Entrada de contraseña --}}
                                <input
                                    type="password"
                                    id="password"
                                    name="nextlevel_demo_password"
                                    autocomplete="new-password"
                                    placeholder="Ingresa tu contraseña"
                                    class="w-full rounded-xl
                                           border border-slate-200
                                           bg-slate-50
                                           py-3.5 pl-12 pr-12
                                           text-sm text-slate-800
                                           outline-none transition
                                           hover:border-slate-300
                                           focus:border-[#1B3A6B]
                                           focus:bg-white
                                           focus:ring-4
                                           focus:ring-blue-100"
                                >



                                {{-- Botón para mostrar u ocultar contraseña --}}
                                <button
                                    type="button"
                                    id="btn-ver-password"
                                    class="absolute inset-y-0 right-0
                                           flex items-center px-4
                                           text-slate-400 transition
                                           hover:text-slate-700"
                                    aria-label="Mostrar u ocultar contraseña"
                                >

                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >

                                        <path
                                            d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"
                                        ></path>

                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="2.5"
                                        ></circle>

                                    </svg>

                                </button>

                            </div>

                        </div>



                        {{-- =============================================
                             BOTÓN PARA INICIAR SESIÓN
                             ============================================= --}}

                        <button
                            type="submit"
                            class="group flex w-full
                                   items-center justify-center gap-2
                                   rounded-xl px-4 py-3.5
                                   text-sm font-bold text-white
                                   shadow-lg transition duration-200
                                   hover:-translate-y-0.5
                                   hover:shadow-xl
                                   active:translate-y-0"
                            style="
                                background:
                                    linear-gradient(
                                        135deg,
                                        #0F2749 0%,
                                        #1B3A6B 100%
                                    );

                                box-shadow:
                                    0 12px 28px rgba(15,39,73,.20);
                            "
                        >

                            <svg
                                class="h-5 w-5
                                       transition-transform
                                       group-hover:translate-x-0.5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >

                                <path
                                    d="M10 17l5-5-5-5"
                                ></path>

                                <path
                                    d="M15 12H3"
                                ></path>

                                <path
                                    d="M14 3h5a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-5"
                                ></path>

                            </svg>

                            Iniciar sesión

                        </button>

                    </form>



                    {{-- =================================================
                         DIVISOR ENTRE LOGIN Y CONSULTA DEL PROFESOR
                         ================================================= --}}

                    <div class="my-7 flex items-center gap-4">

                        <div class="h-px flex-1 bg-slate-200"></div>

                        <span
                            class="text-[11px] font-bold
                                   uppercase tracking-widest
                                   text-slate-400"
                        >
                            También puedes
                        </span>

                        <div class="h-px flex-1 bg-slate-200"></div>

                    </div>



                    {{-- =================================================
                         ACCESO PARA CONSULTAR HORARIO DE PROFESOR
                         No inicia sesión como administrador.
                         ================================================= --}}

                    <a
                        href="{{ route('horarios.consulta') }}"
                        class="group flex w-full
                               items-center justify-between
                               rounded-xl border border-slate-200
                               bg-white px-4 py-3.5
                               transition
                               hover:border-red-200
                               hover:bg-red-50/50"
                    >

                        <div class="flex items-center gap-3">


                            {{-- Icono profesor --}}
                            <div
                                class="flex h-10 w-10 shrink-0
                                       items-center justify-center
                                       rounded-lg bg-red-50
                                       transition
                                       group-hover:bg-red-100"
                                style="color:#DB0808;"
                            >

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >

                                    <circle
                                        cx="12"
                                        cy="8"
                                        r="3"
                                    ></circle>

                                    <path
                                        d="M6 21v-2a6 6 0 0 1 12 0v2"
                                    ></path>

                                </svg>

                            </div>



                            {{-- Texto --}}
                            <div class="text-left">

                                <p
                                    class="text-sm font-bold text-slate-800"
                                >
                                    Ver horario de profesor
                                </p>

                                <p
                                    class="mt-0.5 text-xs text-slate-500"
                                >
                                    Consulta tu horario semanal
                                </p>

                            </div>

                        </div>



                        {{-- Flecha derecha --}}
                        <svg
                            class="h-5 w-5 text-slate-400
                                   transition
                                   group-hover:translate-x-1
                                   group-hover:text-red-500"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >

                            <path
                                d="M9 18l6-6-6-6"
                            ></path>

                        </svg>

                    </a>



                    {{-- =================================================
                         CREDENCIALES DE DEMOSTRACIÓN
                         Muestra qué usuario y contraseña usar.
                         ================================================= --}}

                    <div
                        class="mt-6 overflow-hidden
                               rounded-xl border border-slate-200
                               bg-slate-50"
                    >

                        <div class="flex">


                            {{-- Línea roja lateral --}}
                            <div
                                class="w-1.5 shrink-0"
                                style="background:#DB0808;"
                            ></div>


                            <div class="flex-1 px-4 py-3">

                                <div class="flex items-start gap-3">


                                    {{-- Icono de información --}}
                                    <div
                                        class="mt-0.5 flex h-8 w-8
                                               shrink-0 items-center
                                               justify-center rounded-lg
                                               bg-white shadow-sm"
                                        style="color:#1B3A6B;"
                                    >

                                        <svg
                                            class="h-4 w-4"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="9"
                                            ></circle>

                                            <path
                                                d="M12 11v5"
                                            ></path>

                                            <path
                                                d="M12 8h.01"
                                            ></path>

                                        </svg>

                                    </div>



                                    {{-- Datos de acceso --}}
                                    <div>

                                        <p
                                            class="text-xs font-bold"
                                            style="color:#0F2749;"
                                        >
                                            Credenciales de demostración
                                        </p>


                                        <p
                                            class="mt-1.5 text-xs text-slate-500"
                                        >

                                            Usuario:

                                            <strong class="text-slate-700">
                                                admin
                                            </strong>


                                            <span
                                                class="mx-1.5 text-slate-300"
                                            >
                                                |
                                            </span>


                                            Contraseña:

                                            <strong class="text-slate-700">
                                                admin123
                                            </strong>

                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>



                    {{-- =================================================
                         PIE DEL LOGIN
                         ================================================= --}}

                    <p
                        class="mt-7 text-center
                               text-[11px] text-slate-400"
                    >
                        © 2026 Next Level School · Gestión académica
                    </p>

                </div>

            </div>

        </div>

    </div>



    {{-- =============================================================
     JAVASCRIPT DEL LOGIN SIMULADO
     No utiliza base de datos ni backend.
     ============================================================= --}}

<script>

    document.addEventListener(
        'DOMContentLoaded',
        () => {


            // =========================================================
            // ELEMENTOS DEL LOGIN
            // =========================================================

            const form =
                document.getElementById(
                    'login-form'
                );

            const usuario =
                document.getElementById(
                    'usuario'
                );

            const password =
                document.getElementById(
                    'password'
                );

            const mensaje =
                document.getElementById(
                    'login-mensaje'
                );

            const btnVerPassword =
                document.getElementById(
                    'btn-ver-password'
                );


            // =========================================================
            // COMPROBAR SI YA EXISTE UNA SESIÓN
            // =========================================================
            //
            // Si el usuario ya inició sesión y vuelve al Login
            // utilizando la flecha Atrás del navegador,
            // no permitimos que se quede aquí.
            //
            // replace() evita crear otra entrada en el historial.
            //
            // =========================================================

            if (
                localStorage.getItem(
                    'nextlevel_auth'
                ) === 'true'
            ) {

                window.location.replace('/');

                return;
            }


            // =========================================================
            // MOSTRAR / OCULTAR CONTRASEÑA
            // =========================================================

            if (btnVerPassword) {

                btnVerPassword.addEventListener(
                    'click',
                    () => {

                        password.type =
                            password.type ===
                            'password'

                                ? 'text'

                                : 'password';
                    }
                );
            }


            // =========================================================
            // MOSTRAR MENSAJES
            // =========================================================

            function mostrarMensaje(
                texto,
                tipo
            ) {

                mensaje.classList.remove(
                    'hidden',

                    'border-red-200',
                    'bg-red-50',
                    'text-red-700',

                    'border-emerald-200',
                    'bg-emerald-50',
                    'text-emerald-700'
                );


                // =====================================================
                // ERROR
                // =====================================================

                if (
                    tipo ===
                    'error'
                ) {

                    mensaje.classList.add(
                        'border-red-200',
                        'bg-red-50',
                        'text-red-700'
                    );
                }


                // =====================================================
                // CORRECTO
                // =====================================================

                else {

                    mensaje.classList.add(
                        'border-emerald-200',
                        'bg-emerald-50',
                        'text-emerald-700'
                    );
                }


                mensaje.textContent =
                    texto;
            }


            // =========================================================
            // INICIAR SESIÓN
            // =========================================================

            form.addEventListener(
                'submit',
                event => {


                    // Evita recargar la página
                    event.preventDefault();


                    // =================================================
                    // OBTENER CREDENCIALES
                    // =================================================

                    const user =
                        usuario.value.trim();

                    const pass =
                        password.value;


                    // =================================================
                    // VALIDAR CAMPOS VACÍOS
                    // =================================================

                    if (
                        !user ||
                        !pass
                    ) {

                        mostrarMensaje(
                            'Completa el usuario y la contraseña.',
                            'error'
                        );

                        return;
                    }


                    // =================================================
                    // CREDENCIALES DEL ADMINISTRADOR
                    // =================================================
                    //
                    // Usuario:
                    // admin
                    //
                    // Contraseña:
                    // admin123
                    //
                    // =================================================

                    if (
                        user ===
                        'admin' &&

                        pass ===
                        'admin123'
                    ) {


                        // =============================================
                        // GUARDAR SESIÓN
                        // =============================================

                        localStorage.setItem(
                            'nextlevel_auth',
                            'true'
                        );


                        localStorage.setItem(
                            'nextlevel_rol',
                            'administrador'
                        );


                        localStorage.setItem(
                            'nextlevel_usuario',
                            'Administrador'
                        );


                        // =============================================
                        // MENSAJE
                        // =============================================

                        mostrarMensaje(
                            'Acceso correcto. Ingresando al sistema...',
                            'ok'
                        );


                        // =============================================
                        // IR AL DASHBOARD
                        // =============================================
                        //
                        // IMPORTANTE:
                        //
                        // NO usar:
                        //
                        // window.location.href = '/';
                        //
                        // porque href agrega el Login al historial.
                        //
                        // replace() sustituye el Login por el Dashboard.
                        //
                        // =============================================

                        setTimeout(
                            () => {

                                window.location.replace(
                                    '/'
                                );

                            },
                            500
                        );


                        return;
                    }


                    // =================================================
                    // CREDENCIALES INCORRECTAS
                    // =================================================

                    mostrarMensaje(
                        'Usuario o contraseña incorrectos.',
                        'error'
                    );
                }
            );
        }
    );


    // =============================================================
    // PROTECCIÓN CUANDO SE USA ATRÁS / ADELANTE
    // =============================================================
    //
    // Si el navegador restaura el Login desde su caché
    // después de iniciar sesión, vuelve al Dashboard.
    //
    // =============================================================

    window.addEventListener(
        'pageshow',
        () => {

            const autenticado =
                localStorage.getItem(
                    'nextlevel_auth'
                );


            if (
                autenticado ===
                'true'
            ) {

                window.location.replace(
                    '/'
                );
            }
        }
    );

</script>

</body>

</html>