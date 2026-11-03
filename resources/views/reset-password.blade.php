<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Nueva contraseña - Next Level School</title>
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
            radial-gradient(circle at 15% 20%, rgba(27,58,107,.10), transparent 32%),
            radial-gradient(circle at 90% 85%, rgba(219,8,8,.06), transparent 28%),
            linear-gradient(135deg, #E8EDF3 0%, #F3F5F8 48%, #E9EDF2 100%);
    "
>
    <div class="relative min-h-screen overflow-hidden">
        <div
            class="pointer-events-none absolute -left-32 -top-32 h-96 w-96 rounded-full opacity-15 blur-3xl"
            style="background:#1B3A6B;"
        ></div>

        <div
            class="pointer-events-none absolute -bottom-40 -right-32 h-[420px] w-[420px] rounded-full opacity-10 blur-3xl"
            style="background:#DB0808;"
        ></div>

        <main
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
            <section
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
                style="box-shadow:0 30px 80px rgba(15,39,73,.16);"
            >
                <aside
                    class="
                        relative
                        hidden
                        min-h-[700px]
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
                        class="pointer-events-none absolute inset-0 h-full w-full object-cover"
                        style="object-position:52% center; filter:grayscale(100%) saturate(25%) brightness(48%) contrast(110%); transform:scale(1.025);"
                    >

                    <div
                        class="pointer-events-none absolute inset-0"
                        style="background:linear-gradient(135deg, rgba(7,18,32,.82) 0%, rgba(15,39,73,.76) 50%, rgba(12,26,43,.86) 100%);"
                    ></div>

                    <div
                        class="pointer-events-none absolute inset-x-0 bottom-0 h-72"
                        style="background:linear-gradient(to top, rgba(4,12,23,.68), transparent);"
                    ></div>

                    <div
                        class="pointer-events-none absolute inset-x-0 top-0 h-52"
                        style="background:linear-gradient(to bottom, rgba(4,12,23,.35), transparent);"
                    ></div>

                    <div
                        class="absolute right-0 top-0 z-10 h-1.5 w-36"
                        style="background:#DB0808;"
                    ></div>

                    <div class="relative z-20">
                        <div class="flex items-center gap-4">
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center">
                                <img
                                    src="{{ asset('images/logo-next-level.png') }}"
                                    alt="Next Level School"
                                    class="h-full w-full object-contain drop-shadow-xl"
                                >
                            </div>

                            <div>
                                <p class="text-2xl font-extrabold tracking-tight text-white">
                                    Next Level
                                </p>

                                <div class="mt-1 flex items-center gap-2">
                                    <span
                                        class="h-[3px] w-8 rounded-full"
                                        style="background:#DB0808;"
                                    ></span>
                                    <span class="text-sm font-semibold tracking-[0.25em] text-slate-200">
                                        SCHOOL
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="relative z-20 my-auto py-12">
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
                                class="h-2 w-2 rounded-full"
                                style="background:#DB0808;"
                            ></span>
                            Seguridad de cuenta
                        </span>

                        <h1 class="mt-7 max-w-lg text-4xl font-extrabold leading-[1.18] tracking-tight text-white">
                            Crea una clave nueva
                            <span
                                class="block"
                                style="color:#FF6262;"
                            >
                                y vuelve al sistema.
                            </span>
                        </h1>

                        <p class="mt-6 max-w-lg text-[15px] leading-7 text-slate-200">
                            Protege tu acceso administrativo con una contraseña actualizada antes de continuar.
                        </p>

                        <div class="mt-8 h-px w-20 bg-white/30"></div>
                    </div>

                    <div class="relative z-20 flex items-center gap-3 border-t border-white/15 pt-6">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/10 backdrop-blur-sm">
                            <svg
                                class="h-5 w-5 text-white"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4z"></path>
                                <path d="M9 12l2 2 4-4"></path>
                            </svg>
                        </div>

                        <div>
                            <p class="text-xs font-bold text-white">
                                Cambio verificado
                            </p>
                            <p class="mt-0.5 text-[11px] text-slate-300">
                                Token de recuperación requerido
                            </p>
                        </div>
                    </div>
                </aside>

                <div class="relative flex min-h-0 flex-col justify-center px-6 py-8 sm:px-10 sm:py-10 lg:min-h-[700px] lg:px-14 lg:py-8">
                    <div class="mb-8 flex items-center justify-center lg:hidden">
                        <div class="text-center">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-2xl bg-white p-2 shadow-lg ring-1 ring-slate-200">
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

                    <div class="mb-6">
                        <div class="mb-4 flex items-center gap-3">
                            <span
                                class="h-1 w-9 rounded-full"
                                style="background:#DB0808;"
                            ></span>
                            <span
                                class="text-xs font-bold uppercase tracking-[0.20em]"
                                style="color:#DB0808;"
                            >
                                Seguridad
                            </span>
                        </div>

                        <h2
                            class="text-3xl font-extrabold tracking-tight sm:text-4xl"
                            style="color:#0F2749;"
                        >
                            Nueva contraseña
                        </h2>

                        <p class="mt-3 text-sm leading-6 text-slate-500">
                            Crea una contraseña con al menos 6 caracteres, letras y números.
                        </p>
                    </div>

                    <div
                        id="reset-mensaje"
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

                    <form
                        id="reset-form"
                        class="space-y-4"
                        novalidate
                    >
                        <input
                            id="reset-token"
                            type="hidden"
                        >

                        <div>
                            <label
                                for="reset-email"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Correo electrónico
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M4 6h16v12H4z"></path>
                                        <path d="M4 7l8 6 8-6"></path>
                                    </svg>
                                </div>

                                <input
                                    id="reset-email"
                                    type="email"
                                    autocomplete="email"
                                    placeholder="correo@ejemplo.com"
                                    class="
                                        w-full
                                        rounded-xl
                                        border
                                        border-slate-200
                                        bg-slate-50
                                        px-4
                                        py-3.5
                                        pl-12
                                        text-sm
                                        text-slate-800
                                        outline-none
                                        transition
                                        placeholder:text-slate-400
                                        hover:border-slate-300
                                        focus:border-[#1B3A6B]
                                        focus:bg-white
                                        focus:ring-4
                                        focus:ring-blue-100
                                    "
                                >
                            </div>
                        </div>

                        <div>
                            <label
                                for="reset-password"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Contraseña
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <rect
                                            x="5"
                                            y="11"
                                            width="14"
                                            height="9"
                                            rx="2"
                                        ></rect>
                                        <path d="M8 11V8a4 4 0 0 1 8 0v3"></path>
                                    </svg>
                                </div>

                                <input
                                    id="reset-password"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Nueva contraseña"
                                    class="
                                        w-full
                                        rounded-xl
                                        border
                                        border-slate-200
                                        bg-slate-50
                                        px-4
                                        py-3.5
                                        pl-12
                                        pr-12
                                        text-sm
                                        text-slate-800
                                        outline-none
                                        transition
                                        placeholder:text-slate-400
                                        hover:border-slate-300
                                        focus:border-[#1B3A6B]
                                        focus:bg-white
                                        focus:ring-4
                                        focus:ring-blue-100
                                    "
                                >

                                <button
                                    type="button"
                                    id="btn-ver-reset-password"
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
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"></path>
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="2.5"
                                        ></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label
                                for="reset-password-confirmation"
                                class="mb-2 block text-sm font-semibold text-slate-700"
                            >
                                Confirmar contraseña
                            </label>

                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                    <svg
                                        class="h-5 w-5"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <path d="M12 3l7 4v5c0 5-3 8-7 9-4-1-7-4-7-9V7l7-4z"></path>
                                        <path d="M9 12l2 2 4-4"></path>
                                    </svg>
                                </div>

                                <input
                                    id="reset-password-confirmation"
                                    type="password"
                                    autocomplete="new-password"
                                    placeholder="Repite la contraseña"
                                    class="
                                        w-full
                                        rounded-xl
                                        border
                                        border-slate-200
                                        bg-slate-50
                                        px-4
                                        py-3.5
                                        pl-12
                                        pr-12
                                        text-sm
                                        text-slate-800
                                        outline-none
                                        transition
                                        placeholder:text-slate-400
                                        hover:border-slate-300
                                        focus:border-[#1B3A6B]
                                        focus:bg-white
                                        focus:ring-4
                                        focus:ring-blue-100
                                    "
                                >

                                <button
                                    type="button"
                                    id="btn-ver-reset-confirmation"
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
                                        <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12z"></path>
                                        <circle
                                            cx="12"
                                            cy="12"
                                            r="2.5"
                                        ></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button
                            type="submit"
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
                                shadow-blue-950/10
                                transition
                                duration-200
                                hover:-translate-y-0.5
                                hover:shadow-xl
                                active:translate-y-0
                                focus:outline-none
                                focus:ring-4
                                focus:ring-blue-100
                            "
                            style="background:linear-gradient(135deg,#0F2749 0%,#1B3A6B 100%);"
                        >
                            <span>Guardar contraseña</span>

                            <svg
                                class="h-4 w-4 transition group-hover:translate-x-0.5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M5 12h14"></path>
                                <path d="M13 6l6 6-6 6"></path>
                            </svg>
                        </button>
                    </form>

                    <div class="my-6 flex items-center gap-4">
                        <div class="h-px flex-1 bg-slate-200"></div>
                        <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400">
                            Acceso
                        </span>
                        <div class="h-px flex-1 bg-slate-200"></div>
                    </div>

                    <a
                        href="{{ route('login') }}"
                        class="
                            group
                            flex
                            w-full
                            items-center
                            justify-between
                            rounded-xl
                            border
                            border-slate-200
                            bg-white
                            px-4
                            py-3.5
                            text-sm
                            font-bold
                            transition
                            hover:border-blue-200
                            hover:bg-blue-50/50
                        "
                        style="color:#1B3A6B;"
                    >
                        <span>Volver al login</span>
                        <svg
                            class="h-5 w-5 text-slate-400 transition group-hover:-translate-x-1 group-hover:text-blue-700"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M15 18l-6-6 6-6"></path>
                        </svg>
                    </a>

                    <p class="mt-5 text-center text-[11px] text-slate-400">
                        © {{ date('Y') }} Next Level School · Gestión académica
                    </p>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('reset-form');
            const token = document.getElementById('reset-token');
            const email = document.getElementById('reset-email');
            const password = document.getElementById('reset-password');
            const confirmation = document.getElementById('reset-password-confirmation');
            const mensaje = document.getElementById('reset-mensaje');
            const btnVerPassword = document.getElementById('btn-ver-reset-password');
            const btnVerConfirmation = document.getElementById('btn-ver-reset-confirmation');
            const params = new URLSearchParams(window.location.search);

            token.value = params.get('token') || '';
            email.value = params.get('email') || '';

            btnVerPassword?.addEventListener('click', () => {
                password.type = password.type === 'password' ? 'text' : 'password';
            });

            btnVerConfirmation?.addEventListener('click', () => {
                confirmation.type = confirmation.type === 'password' ? 'text' : 'password';
            });

            function mostrarMensaje(texto, tipo = 'error') {
                mensaje.className = 'mb-5 rounded-xl border px-4 py-3 text-sm font-medium';

                if (tipo === 'ok') {
                    mensaje.classList.add('border-emerald-200', 'bg-emerald-50', 'text-emerald-700');
                } else {
                    mensaje.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
                }

                mensaje.textContent = texto;
            }

            function obtenerPrimerErrorValidacion(errores) {
                return errores
                    ? Object.values(errores).flat().find(Boolean)
                    : null;
            }

            function obtenerMensajeError(error, mensajeGeneral) {
                if (!error?.response) {
                    return 'No se pudo conectar con el servidor. Revisa que MySQL, Apache y php artisan serve estén activos.';
                }

                const status = error.response.status;
                const data = error.response.data || {};
                const primerError = obtenerPrimerErrorValidacion(data.errors);

                if (primerError) {
                    return primerError;
                }

                if (status === 419) {
                    return 'La sesión de seguridad expiró. Recarga la página e inténtalo otra vez.';
                }

                if (status === 422) {
                    return data.message || 'El enlace puede haber vencido o los datos no coinciden.';
                }

                if (status >= 500) {
                    return 'El servidor tuvo un problema. Revisa que MySQL esté encendido y que la API esté respondiendo.';
                }

                return data.message || mensajeGeneral;
            }

            function activarCarga(submitButton, texto) {
                if (!submitButton) {
                    return null;
                }

                const textoOriginal = submitButton.innerHTML;

                submitButton.disabled = true;
                submitButton.classList.add('opacity-70', 'cursor-not-allowed');
                submitButton.innerHTML = `
                    <span class="inline-flex items-center justify-center gap-2">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-white/40 border-t-white"></span>
                        ${texto}
                    </span>
                `;

                return textoOriginal;
            }

            function desactivarCarga(submitButton, textoOriginal) {
                if (!submitButton) {
                    return;
                }

                submitButton.disabled = false;
                submitButton.classList.remove('opacity-70', 'cursor-not-allowed');

                if (textoOriginal !== null) {
                    submitButton.innerHTML = textoOriginal;
                }
            }

            if (!token.value) {
                mostrarMensaje('El enlace de recuperación no incluye un token válido.');
            }

            form?.addEventListener('submit', async event => {
                event.preventDefault();

                const submitButton = form.querySelector('button[type="submit"]');
                const datos = {
                    token: token.value,
                    email: email.value.trim(),
                    password: password.value,
                    password_confirmation: confirmation.value
                };

                if (!datos.token || !datos.email || !datos.password || !datos.password_confirmation) {
                    mostrarMensaje('Completa tu correo y escribe la nueva contraseña dos veces.');
                    return;
                }

                if (datos.password.length < 6) {
                    mostrarMensaje('La contraseña debe tener al menos 6 caracteres.');
                    return;
                }

                if (datos.password !== datos.password_confirmation) {
                    mostrarMensaje('Las contraseñas no coinciden.');
                    return;
                }

                const textoOriginal = activarCarga(submitButton, 'Guardando contraseña...');

                try {
                    const response = await window.axios.post('/api/reset-password', datos);

                    mostrarMensaje(
                        response?.data?.message || 'Contraseña restablecida correctamente.',
                        'ok'
                    );

                    setTimeout(() => {
                        window.location.replace('/login');
                    }, 900);
                } catch (error) {
                    mostrarMensaje(
                        obtenerMensajeError(
                            error,
                            'No se pudo restablecer la contraseña.'
                        )
                    );
                } finally {
                    desactivarCarga(submitButton, textoOriginal);
                }
            });
        });
    </script>
</body>

</html>
