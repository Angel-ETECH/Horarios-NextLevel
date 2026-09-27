<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Crear cuenta - Next Level School</title>

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo2.png') }}"
    >

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

</head>


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


    {{-- DECORACIÓN --}}

    <div
        class="
            pointer-events-none
            absolute
            -left-32
            -top-32
            h-96
            w-96
            rounded-full
            opacity-15
            blur-3xl
        "
        style="background:#1B3A6B;"
    ></div>


    <div
        class="
            pointer-events-none
            absolute
            -bottom-40
            -right-32
            h-[420px]
            w-[420px]
            rounded-full
            opacity-10
            blur-3xl
        "
        style="background:#DB0808;"
    ></div>


    <div
        class="
            relative
            flex
            min-h-screen
            items-center
            justify-center
            px-4
            py-4
            sm:py-8
        "
    >


        <div
            class="
                grid
                w-full
                max-w-6xl
                overflow-hidden
                rounded-3xl
                border
                border-slate-200
                bg-white
                shadow-2xl
                lg:grid-cols-2
            "
            style="
                box-shadow:
                    0 30px 80px rgba(15,39,73,.16);
            "
        >


            {{-- =================================================
                 PANEL IZQUIERDO
            ================================================== --}}

            <div
                class="
                    relative
                    hidden
                    min-h-[720px]
                    overflow-hidden
                    px-10
                    py-12
                    text-white
                    lg:flex
                    lg:flex-col
                    lg:justify-between
                "
            >


                <img
                    src="{{ asset('images/Academia-Next-Level.jpeg') }}"
                    alt=""
                    aria-hidden="true"
                    class="
                        pointer-events-none
                        absolute
                        inset-0
                        h-full
                        w-full
                        object-cover
                    "
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


                <div
                    class="pointer-events-none absolute inset-0"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                rgba(7,18,32,.84) 0%,
                                rgba(15,39,73,.77) 50%,
                                rgba(12,26,43,.88) 100%
                            );
                    "
                ></div>


                <div
                    class="
                        pointer-events-none
                        absolute
                        inset-x-0
                        bottom-0
                        h-72
                    "
                    style="
                        background:
                            linear-gradient(
                                to top,
                                rgba(4,12,23,.72),
                                transparent
                            );
                    "
                ></div>


                <div
                    class="
                        absolute
                        right-0
                        top-0
                        z-10
                        h-1.5
                        w-36
                    "
                    style="background:#DB0808;"
                ></div>


                {{-- LOGO --}}

                <div class="relative z-20">

                    <div class="flex items-center gap-4">

                        <div
                            class="
                                flex
                                h-20
                                w-20
                                shrink-0
                                items-center
                                justify-center
                            "
                        >

                            <img
                                src="{{ asset('images/logo-next-level.png') }}"
                                alt="Next Level School"
                                class="
                                    h-full
                                    w-full
                                    object-contain
                                    drop-shadow-xl
                                "
                            >

                        </div>


                        <div>

                            <p
                                class="
                                    text-2xl
                                    font-extrabold
                                    tracking-tight
                                    text-white
                                "
                            >
                                Next Level
                            </p>


                            <div class="mt-1 flex items-center gap-2">

                                <span
                                    class="
                                        h-[3px]
                                        w-8
                                        rounded-full
                                    "
                                    style="background:#DB0808;"
                                ></span>


                                <span
                                    class="
                                        text-sm
                                        font-semibold
                                        tracking-[0.25em]
                                        text-slate-200
                                    "
                                >
                                    SCHOOL
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- TEXTO --}}

                <div
                    class="
                        relative
                        z-20
                        my-auto
                        py-12
                    "
                >

                    <span
                        class="
                            inline-flex
                            items-center
                            gap-2
                            rounded-full
                            border
                            border-white/15
                            bg-black/20
                            px-4
                            py-2
                            text-xs
                            font-semibold
                            text-white
                            backdrop-blur-sm
                        "
                    >

                        <span
                            class="
                                h-2
                                w-2
                                rounded-full
                            "
                            style="background:#DB0808;"
                        ></span>

                        Gestión académica

                    </span>


                    <h1
                        class="
                            mt-7
                            max-w-lg
                            text-4xl
                            font-extrabold
                            leading-[1.18]
                            tracking-tight
                            text-white
                        "
                    >

                        Crea tu cuenta para acceder al

                        <span
                            class="block"
                            style="color:#FF6262;"
                        >
                            sistema académico.
                        </span>

                    </h1>


                    <p
                        class="
                            mt-6
                            max-w-lg
                            text-[15px]
                            leading-7
                            text-slate-200
                        "
                    >
                        Regístrate con tu usuario y correo para acceder
                        posteriormente al sistema cuando el backend de
                        autenticación esté conectado.
                    </p>


                    <div class="mt-8 h-px w-20 bg-white/30"></div>

                </div>


                {{-- INFORMACIÓN INFERIOR --}}

                <div
                    class="
                        relative
                        z-20
                        flex
                        items-center
                        gap-3
                        border-t
                        border-white/15
                        pt-6
                    "
                >

                    <div
                        class="
                            flex
                            h-10
                            w-10
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                            border
                            border-white/10
                            bg-white/10
                            backdrop-blur-sm
                        "
                    >

                        <svg
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>

                    </div>


                    <div>

                        <p class="text-xs font-bold text-white">
                            Registro seguro
                        </p>

                        <p class="mt-0.5 text-[11px] text-slate-300">
                            Las credenciales serán gestionadas por el backend.
                        </p>

                    </div>

                </div>

            </div>


            {{-- =================================================
                 FORMULARIO DERECHO
            ================================================== --}}

            <div
                class="
                    relative
                    flex
                    min-h-0
                    flex-col
                    justify-center
                    px-6
                    py-8
                    sm:px-10
                    sm:py-10
                    lg:px-14
                    lg:min-h-[720px]
                    lg:py-12
                "
            >


                {{-- LOGO MÓVIL --}}

                <div
                    class="
                        mb-7
                        flex
                        items-center
                        justify-center
                        lg:hidden
                    "
                >

                    <div class="text-center">

                        <div
                            class="
                                mx-auto
                                flex
                                h-20
                                w-20
                                items-center
                                justify-center
                                rounded-2xl
                                bg-white
                                p-2
                                shadow-lg
                                ring-1
                                ring-slate-200
                            "
                        >

                            <img
                                src="{{ asset('images/logo-next-level.png') }}"
                                alt="Next Level School"
                                class="h-full w-full object-contain"
                            >

                        </div>


                        <h1
                            class="
                                mt-4
                                text-2xl
                                font-extrabold
                            "
                            style="color:#0F2749;"
                        >
                            Next Level School
                        </h1>

                    </div>

                </div>


                {{-- CABECERA --}}

                <div class="mb-7">

                    <div class="mb-4 flex items-center gap-3">

                        <span
                            class="
                                h-1
                                w-9
                                rounded-full
                            "
                            style="background:#DB0808;"
                        ></span>


                        <span
                            class="
                                text-xs
                                font-bold
                                uppercase
                                tracking-[0.20em]
                            "
                            style="color:#DB0808;"
                        >
                            Nueva cuenta
                        </span>

                    </div>


                    <h2
                        class="
                            text-3xl
                            font-extrabold
                            tracking-tight
                            sm:text-4xl
                        "
                        style="color:#0F2749;"
                    >
                        Crear cuenta
                    </h2>


                    <p
                        class="
                            mt-3
                            text-sm
                            leading-6
                            text-slate-500
                        "
                    >
                        Registra tu nombre, correo, contraseña y
                        código de invitación para acceder al sistema.
                    </p>

                </div>


                {{-- MENSAJE --}}

                <div
                    id="registro-mensaje"
                    role="status"
                    aria-live="polite"
                    class="
                        mb-5
                        hidden
                        rounded-xl
                        border
                        px-4
                        py-3
                        text-sm
                        font-medium
                    "
                ></div>


                {{-- FORMULARIO --}}

                <form
                    id="registro-form"
                    class="space-y-5"
                    autocomplete="off"
                    novalidate
                >


                    {{-- NOMBRE --}}

                    <div>

                        <label
                            for="registro-usuario"
                            class="
                                mb-2
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            "
                        >
                            Nombre
                        </label>


                        <div class="relative">

                            <div
                                class="
                                    pointer-events-none
                                    absolute
                                    inset-y-0
                                    left-0
                                    flex
                                    items-center
                                    pl-4
                                    text-slate-400
                                "
                            >

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <circle cx="12" cy="7" r="3"/>
                                    <path d="M5 20v-1a7 7 0 0 1 14 0v1"/>
                                </svg>

                            </div>


                            <input
                                id="registro-usuario"
                                type="text"
                                autocomplete="name"
                                placeholder="Ej. Administrador"
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-slate-50
                                    py-3.5
                                    pl-12
                                    pr-4
                                    text-sm
                                    text-slate-800
                                    outline-none
                                    transition
                                    hover:border-slate-300
                                    focus:border-[#1B3A6B]
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-blue-100
                                "
                            >

                        </div>

                    </div>


                    {{-- CORREO --}}

                    <div>

                        <label
                            for="registro-email"
                            class="
                                mb-2
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            "
                        >
                            Correo electrónico
                        </label>


                        <div class="relative">

                            <div
                                class="
                                    pointer-events-none
                                    absolute
                                    inset-y-0
                                    left-0
                                    flex
                                    items-center
                                    pl-4
                                    text-slate-400
                                "
                            >

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path d="m4 7 8 6 8-6"/>
                                </svg>

                            </div>


                            <input
                                id="registro-email"
                                type="email"
                                autocomplete="email"
                                placeholder="correo@ejemplo.com"
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-slate-50
                                    py-3.5
                                    pl-12
                                    pr-4
                                    text-sm
                                    text-slate-800
                                    outline-none
                                    transition
                                    hover:border-slate-300
                                    focus:border-[#1B3A6B]
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-blue-100
                                "
                            >

                        </div>

                    </div>


                    {{-- CÓDIGO DE INVITACIÓN --}}

                    <div>

                        <label
                            for="registro-codigo-invitacion"
                            class="
                                mb-2
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            "
                        >
                            Código de invitación
                        </label>


                        <div class="relative">

                            <div
                                class="
                                    pointer-events-none
                                    absolute
                                    inset-y-0
                                    left-0
                                    flex
                                    items-center
                                    pl-4
                                    text-slate-400
                                "
                            >

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M15 7a4 4 0 1 1-2.83-3.83"/>
                                    <path d="M12 12 21 3"/>
                                    <path d="m16 3 5 5"/>
                                    <path d="m19 6-2 2"/>
                                </svg>

                            </div>


                            <input
                                id="registro-codigo-invitacion"
                                type="text"
                                autocomplete="one-time-code"
                                placeholder="Código entregado por administración"
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-slate-50
                                    py-3.5
                                    pl-12
                                    pr-4
                                    text-sm
                                    text-slate-800
                                    outline-none
                                    transition
                                    hover:border-slate-300
                                    focus:border-[#1B3A6B]
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-blue-100
                                "
                            >

                        </div>

                        <p
                            id="registro-codigo-ayuda"
                            class="mt-2 text-xs font-medium text-slate-400"
                        >
                            Ingresa el código entregado por administración.
                        </p>

                    </div>


                    {{-- CONTRASEÑA --}}

                    <div>

                        <label
                            for="registro-password"
                            class="
                                mb-2
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            "
                        >
                            Contraseña
                        </label>


                        <div class="relative">

                            <div
                                class="
                                    pointer-events-none
                                    absolute
                                    inset-y-0
                                    left-0
                                    flex
                                    items-center
                                    pl-4
                                    text-slate-400
                                "
                            >

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <rect x="4" y="10" width="16" height="10" rx="2"/>
                                    <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                                </svg>

                            </div>


                            <input
                                id="registro-password"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Mínimo 6 caracteres"
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-slate-50
                                    py-3.5
                                    pl-12
                                    pr-12
                                    text-sm
                                    text-slate-800
                                    outline-none
                                    transition
                                    hover:border-slate-300
                                    focus:border-[#1B3A6B]
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-blue-100
                                "
                            >


                            <button
                                type="button"
                                id="ver-registro-password"
                                class="
                                    absolute
                                    inset-y-0
                                    right-0
                                    flex
                                    items-center
                                    px-4
                                    text-slate-400
                                    transition
                                    hover:text-slate-700
                                "
                                aria-label="Mostrar u ocultar contraseña"
                            >

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>

                            </button>

                        </div>

                        <p
                            id="registro-password-ayuda"
                            class="mt-2 text-xs font-medium text-slate-400"
                        >
                            Usa al menos 6 caracteres.
                        </p>

                    </div>


                    {{-- CONFIRMAR CONTRASEÑA --}}

                    <div>

                        <label
                            for="registro-password-confirmation"
                            class="
                                mb-2
                                block
                                text-sm
                                font-semibold
                                text-slate-700
                            "
                        >
                            Confirmar contraseña
                        </label>


                        <div class="relative">

                            <div
                                class="
                                    pointer-events-none
                                    absolute
                                    inset-y-0
                                    left-0
                                    flex
                                    items-center
                                    pl-4
                                    text-slate-400
                                "
                            >

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4z"/>
                                    <path d="M9 12l2 2 4-4"/>
                                </svg>

                            </div>


                            <input
                                id="registro-password-confirmation"
                                type="password"
                                autocomplete="new-password"
                                placeholder="Repite la contraseña"
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-slate-50
                                    py-3.5
                                    pl-12
                                    pr-12
                                    text-sm
                                    text-slate-800
                                    outline-none
                                    transition
                                    hover:border-slate-300
                                    focus:border-[#1B3A6B]
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-blue-100
                                "
                            >


                            <button
                                type="button"
                                id="ver-registro-confirmacion"
                                class="
                                    absolute
                                    inset-y-0
                                    right-0
                                    flex
                                    items-center
                                    px-4
                                    text-slate-400
                                    transition
                                    hover:text-slate-700
                                "
                                aria-label="Mostrar u ocultar confirmación de contraseña"
                            >

                                <svg
                                    class="h-5 w-5"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"/>
                                    <circle cx="12" cy="12" r="2.5"/>
                                </svg>

                            </button>

                        </div>

                        <p
                            id="registro-confirmacion-ayuda"
                            class="mt-2 text-xs font-medium text-slate-400"
                        >
                            Repite la contraseña para confirmar.
                        </p>

                    </div>


                    {{-- BOTÓN --}}

                    <button
                        type="submit"
                        id="btn-crear-cuenta"
                        class="
                            group
                            flex
                            w-full
                            items-center
                            justify-center
                            gap-2
                            rounded-xl
                            px-4
                            py-3.5
                            text-sm
                            font-bold
                            text-white
                            shadow-lg
                            transition
                            duration-200
                            hover:-translate-y-0.5
                            hover:shadow-xl
                        "
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
                            class="h-5 w-5"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle cx="9" cy="8" r="4"/>
                            <path d="M3 21a6 6 0 0 1 12 0"/>
                            <path d="M19 8v6"/>
                            <path d="M16 11h6"/>
                        </svg>

                        Crear cuenta

                    </button>

                </form>


                {{-- IR AL LOGIN --}}

                <div class="my-6 flex items-center gap-4">

                    <div class="h-px flex-1 bg-slate-200"></div>

                    <span
                        class="
                            text-[11px]
                            font-bold
                            uppercase
                            tracking-widest
                            text-slate-400
                        "
                    >
                        ¿Ya tienes cuenta?
                    </span>

                    <div class="h-px flex-1 bg-slate-200"></div>

                </div>


                <a
                    href="{{ route('login') }}"
                    class="
                        flex
                        w-full
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        border
                        border-slate-200
                        bg-white
                        px-4
                        py-3.5
                        text-sm
                        font-bold
                        text-[#1B3A6B]
                        transition
                        hover:border-blue-200
                        hover:bg-blue-50/50
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

                    Iniciar sesión

                </a>


                <p
                    class="
                        mt-7
                        text-center
                        text-[11px]
                        text-slate-400
                    "
                >
                    © 2026 Next Level School · Gestión académica
                </p>

            </div>

        </div>

    </div>

</div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    () => {

        const form =
            document.getElementById(
                'registro-form'
            );


        const usuario =
            document.getElementById(
                'registro-usuario'
            );


        const email =
            document.getElementById(
                'registro-email'
            );


        const codigoInvitacion =
            document.getElementById(
                'registro-codigo-invitacion'
            );


        const password =
            document.getElementById(
                'registro-password'
            );


        const confirmation =
            document.getElementById(
                'registro-password-confirmation'
            );


        const mensaje =
            document.getElementById(
                'registro-mensaje'
            );


        const btnPassword =
            document.getElementById(
                'ver-registro-password'
            );


        const btnConfirmation =
            document.getElementById(
                'ver-registro-confirmacion'
            );

        const codigoAyuda =
            document.getElementById(
                'registro-codigo-ayuda'
            );

        const passwordAyuda =
            document.getElementById(
                'registro-password-ayuda'
            );

        const confirmationAyuda =
            document.getElementById(
                'registro-confirmacion-ayuda'
            );

        const TOKEN_KEY =
            'nextlevel_token';

        const USER_KEY =
            'nextlevel_usuario';

        const AUTH_KEY =
            'nextlevel_auth';


        function haySesionActiva() {

            return Boolean(
                obtenerTokenSeguro()
            );
        }

        function obtenerTokenSeguro(
            valor = localStorage.getItem(
                TOKEN_KEY
            )
        ) {

            const token =
                String(valor || '').trim();

            return (
                token &&
                token !== 'null' &&
                token !== 'undefined'
            )
                ? token
                : '';
        }


        function limpiarUsuarioParaSesion(
            user
        ) {

            const usuario =
                {
                    ...(user || {})
                };

            delete usuario.password;
            delete usuario.password_confirmation;
            delete usuario.remember_token;

            return usuario;
        }


        function guardarSesionAutenticada(
            token,
            user
        ) {

            const tokenSeguro =
                obtenerTokenSeguro(
                    token
                );

            if (
                !tokenSeguro
            ) {
                throw new Error(
                    'El servidor no devolvió un token válido.'
                );
            }

            localStorage.setItem(
                TOKEN_KEY,
                tokenSeguro
            );

            localStorage.setItem(
                USER_KEY,
                JSON.stringify(
                    limpiarUsuarioParaSesion(
                        user
                    )
                )
            );

            localStorage.setItem(
                AUTH_KEY,
                'true'
            );

            window.axios
                .defaults
                .headers
                .common[
                    'Authorization'
                ] =
                    `Bearer ${tokenSeguro}`;
        }


        // =====================================================
        // SI YA ESTÁ LOGUEADO
        // =====================================================

        if (
            haySesionActiva()
        ) {

            window.location.replace('/');

            return;
        }


        // =====================================================
        // MOSTRAR / OCULTAR CONTRASEÑA
        // =====================================================

        function alternarPassword(
            input
        ) {

            if (!input) {
                return;
            }


            input.type =
                input.type === 'password'
                    ? 'text'
                    : 'password';
        }


        btnPassword?.addEventListener(
            'click',
            () => {

                alternarPassword(
                    password
                );
            }
        );


        btnConfirmation?.addEventListener(
            'click',
            () => {

                alternarPassword(
                    confirmation
                );
            }
        );


        // =====================================================
        // MENSAJES
        // =====================================================

        function mostrarMensaje(
            texto,
            tipo = 'error'
        ) {

            mensaje.className =
                'mb-5 rounded-xl border px-4 py-3 text-sm font-medium';


            if (
                tipo === 'ok'
            ) {

                mensaje.classList.add(
                    'border-emerald-200',
                    'bg-emerald-50',
                    'text-emerald-700'
                );

            } else if (
                tipo === 'info'
            ) {

                mensaje.classList.add(
                    'border-blue-200',
                    'bg-blue-50',
                    'text-blue-700'
                );

            } else {

                mensaje.classList.add(
                    'border-red-200',
                    'bg-red-50',
                    'text-red-700'
                );
            }


            mensaje.textContent =
                texto;
        }

        function obtenerPrimerErrorValidacion(
            errores
        ) {

            return errores
                ? Object.values(errores)
                    .flat()
                    .find(Boolean)
                : null;
        }


        function obtenerMensajeError(
            error,
            mensajeGeneral
        ) {

            if (
                !error?.response
            ) {
                return 'No se pudo conectar con el servidor. Revisa que MySQL, Apache y php artisan serve estén activos.';
            }

            const status =
                error.response.status;

            const data =
                error.response.data || {};

            const primerError =
                obtenerPrimerErrorValidacion(
                    data.errors
                );

            if (
                primerError
            ) {
                return primerError;
            }

            if (
                status ===
                419
            ) {
                return 'La sesión de seguridad expiró. Recarga la página e inténtalo otra vez.';
            }

            if (
                status ===
                422
            ) {
                return data.message ||
                    'Revisa los datos del formulario. Hay campos que no cumplen las reglas.';
            }

            if (
                status >=
                500
            ) {
                return 'El servidor tuvo un problema. Revisa que MySQL esté encendido y que la API esté respondiendo.';
            }

            return data.message ||
                mensajeGeneral;
        }


        function activarCarga(
            submitButton,
            texto
        ) {

            if (
                !submitButton
            ) {
                return null;
            }

            const textoOriginal =
                submitButton.innerHTML;

            submitButton.disabled =
                true;

            submitButton.classList.add(
                'opacity-70',
                'cursor-not-allowed'
            );

            submitButton.innerHTML =
                `<span class="inline-flex items-center justify-center gap-2">
                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                    ${texto}
                </span>`;

            return textoOriginal;
        }


        function desactivarCarga(
            submitButton,
            textoOriginal
        ) {

            if (
                !submitButton
            ) {
                return;
            }

            submitButton.disabled =
                false;

            submitButton.classList.remove(
                'opacity-70',
                'cursor-not-allowed'
            );

            if (
                textoOriginal !==
                null
            ) {
                submitButton.innerHTML =
                    textoOriginal;
            }
        }


        // =====================================================
        // VALIDAR EMAIL
        // =====================================================

        function emailValido(
            valor
        ) {

            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
                valor
            );
        }


        // =====================================================
        // VALIDAR NOMBRE
        // =====================================================

        function usuarioValido(
            valor
        ) {

            return valor.length <= 100 &&
                valor.trim().length >= 2 &&
                /^[a-zA-ZÁÉÍÓÚÜÑáéíóúüñ\s'.-]+$/.test(
                valor
            );
        }


        // =====================================================
        // VALIDACIÓN EN VIVO
        // =====================================================

        function actualizarAyuda(
            elemento,
            texto,
            tipo = 'neutral'
        ) {

            if (
                !elemento
            ) {
                return;
            }

            elemento.textContent =
                texto;

            elemento.classList.remove(
                'text-slate-400',
                'text-red-600',
                'text-emerald-600'
            );

            elemento.classList.add(
                tipo === 'ok'
                    ? 'text-emerald-600'
                    : tipo === 'error'
                        ? 'text-red-600'
                        : 'text-slate-400'
            );
        }


        function validarCodigoEnVivo() {

            if (
                !codigoInvitacion
            ) {
                return;
            }

            const valor =
                codigoInvitacion.value.trim();

            if (
                !valor
            ) {
                actualizarAyuda(
                    codigoAyuda,
                    'Ingresa el código entregado por administración.'
                );

                return;
            }

            actualizarAyuda(
                codigoAyuda,
                'Código listo para validar.',
                'ok'
            );
        }


        function validarPasswordEnVivo() {

            if (
                !password
            ) {
                return;
            }

            if (
                !password.value
            ) {
                actualizarAyuda(
                    passwordAyuda,
                    'Usa al menos 6 caracteres.'
                );

            } else if (
                password.value.length <
                6
            ) {

                actualizarAyuda(
                    passwordAyuda,
                    'La contraseña todavía necesita 6 caracteres.',
                    'error'
                );

            } else {

                actualizarAyuda(
                    passwordAyuda,
                    'Contraseña válida.',
                    'ok'
                );
            }

            validarConfirmacionEnVivo();
        }


        function validarConfirmacionEnVivo() {

            if (
                !confirmation
            ) {
                return;
            }

            if (
                !confirmation.value
            ) {
                actualizarAyuda(
                    confirmationAyuda,
                    'Repite la contraseña para confirmar.'
                );

                return;
            }

            if (
                confirmation.value !==
                password.value
            ) {

                actualizarAyuda(
                    confirmationAyuda,
                    'Las contraseñas no coinciden.',
                    'error'
                );

                return;
            }

            actualizarAyuda(
                confirmationAyuda,
                'Las contraseñas coinciden.',
                'ok'
            );
        }


        codigoInvitacion?.addEventListener(
            'input',
            validarCodigoEnVivo
        );

        password?.addEventListener(
            'input',
            validarPasswordEnVivo
        );

        confirmation?.addEventListener(
            'input',
            validarConfirmacionEnVivo
        );


        // =====================================================
        // SUBMIT
        // =====================================================

        form.addEventListener(
            'submit',
            async event => {

                event.preventDefault();


                const datos = {

                    name:
                        usuario.value.trim(),

                    email:
                        email.value.trim(),

                    password:
                        password.value,

                    password_confirmation:
                        confirmation.value,

                    codigo_invitacion:
                        codigoInvitacion.value.trim()
                };

                validarCodigoEnVivo();
                validarPasswordEnVivo();
                validarConfirmacionEnVivo();


                // ---------------------------------------------
                // CAMPOS VACÍOS
                // ---------------------------------------------

                if (
                    !datos.name ||
                    !datos.email ||
                    !datos.codigo_invitacion ||
                    !datos.password ||
                    !datos.password_confirmation
                ) {

                    mostrarMensaje(
                        'Completa tu nombre, correo, código de invitación y contraseña.',
                        'error'
                    );

                    return;
                }


                // ---------------------------------------------
                // NOMBRE
                // ---------------------------------------------

                if (
                    !usuarioValido(
                        datos.name
                    )
                ) {

                    mostrarMensaje(
                        'Ingresa un nombre válido de hasta 100 caracteres.',
                        'error'
                    );

                    return;
                }


                // ---------------------------------------------
                // EMAIL
                // ---------------------------------------------

                if (
                    !emailValido(
                        datos.email
                    )
                ) {

                    mostrarMensaje(
                        'Ingresa un correo electrónico válido.',
                        'error'
                    );

                    return;
                }


                // ---------------------------------------------
                // CONTRASEÑA
                // ---------------------------------------------

                if (
                    datos.password.length <
                    6
                ) {

                    mostrarMensaje(
                        'La contraseña debe tener al menos 6 caracteres.',
                        'error'
                    );

                    return;
                }


                if (
                    datos.password !==
                    datos.password_confirmation
                ) {

                    mostrarMensaje(
                        'Las contraseñas no coinciden.',
                        'error'
                    );

                    return;
                }


                const submitButton =
                    form.querySelector(
                        'button[type="submit"]'
                    );

                const textoOriginal =
                    activarCarga(
                        submitButton,
                        'Creando cuenta...'
                    );


                try {

                    const response =
                        await window.axios.post(
                            '/api/register',
                            datos
                        );

                    const data =
                        response?.data?.data;

                    const token =
                        data?.token;

                    const user =
                        data?.user;

                    if (
                        !token
                    ) {
                        throw new Error(
                            'El servidor no devolvió un token.'
                        );
                    }

                    guardarSesionAutenticada(
                        token,
                        user
                    );

                    mostrarMensaje(
                        'Cuenta creada correctamente. Ingresando al sistema...',
                        'ok'
                    );

                    setTimeout(
                        () => {
                            window.location.replace('/');
                        },
                        500
                    );

                } catch (
                    error
                ) {

                    mostrarMensaje(
                        obtenerMensajeError(
                            error,
                            'No se pudo crear la cuenta.'
                        ),
                        'error'
                    );

                } finally {

                    desactivarCarga(
                        submitButton,
                        textoOriginal
                    );
                }
            }
        );
    }
);

</script>

</body>

</html>
