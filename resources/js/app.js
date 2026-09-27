import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] =
    'XMLHttpRequest';


// =========================================================
// MÓDULOS DEL SISTEMA
// =========================================================

import './secciones/sidebar';
import './secciones/dashboard';
import './secciones/profesores';
import './secciones/cursos';
import './secciones/aulas';
import './secciones/disponibilidad';
import './secciones/asignaciones';
import './secciones/horarios';
import './secciones/historial';
import './secciones/grados';


// =========================================================
// CONFIGURACIÓN DE AUTENTICACIÓN REAL
// =========================================================

const TOKEN_KEY =
    'nextlevel_token';

const USER_KEY =
    'nextlevel_usuario';

const AUTH_KEY =
    'nextlevel_auth';

const AUTH_NOTICE_KEY =
    'nextlevel_auth_notice';


const RUTA_LOGIN =
    '/login';

const RUTA_REGISTRO =
    '/registro';

const RUTA_DASHBOARD =
    '/';


const RUTAS_PUBLICAS = [

    '/login',

    '/registro',

    '/forgot-password',

    '/reset-password',

    '/consulta-horarios'

];


// =========================================================
// CONFIGURAR TOKEN EN AXIOS
// =========================================================

function configurarTokenAxios() {

    const token =
        obtenerTokenSesion();


    if (token) {

        window.axios.defaults.headers.common[
            'Authorization'
        ] =
            `Bearer ${token}`;

    } else {

        delete window.axios.defaults.headers.common[
            'Authorization'
        ];
    }
}


function normalizarToken(
    valor
) {

    const token =
        String(valor || '').trim();

    if (
        !token ||
        token === 'null' ||
        token === 'undefined'
    ) {
        return '';
    }

    return token;
}


function obtenerTokenSesion() {

    return normalizarToken(
        localStorage.getItem(
            TOKEN_KEY
        )
    );
}


// Ejecutar inmediatamente al cargar app.js
configurarTokenAxios();


// =========================================================
// OBTENER RUTA ACTUAL
// =========================================================

function obtenerRutaActual() {

    return window.location.pathname;
}


// =========================================================
// COMPROBAR SI UNA RUTA ES PÚBLICA
// =========================================================

function esRutaPublica(ruta) {

    return RUTAS_PUBLICAS.some(
        rutaPublica =>

            ruta === rutaPublica ||

            ruta.startsWith(
                `${rutaPublica}/`
            )
    );
}


// =========================================================
// COMPROBAR SI EXISTE SESIÓN REAL
// =========================================================

function estaAutenticado() {

    const token =
        obtenerTokenSesion();

    const authAnterior =
        localStorage.getItem(
            AUTH_KEY
        );


    if (token) {
        return true;
    }

    if (authAnterior) {
        limpiarSesion();
    }

    return false;
}


// =========================================================
// MOSTRAR LA PÁGINA
// =========================================================

function mostrarPagina() {

    document.body.classList.remove(
        'invisible'
    );
}


// =========================================================
// LIMPIAR SESIÓN
// =========================================================

function limpiarSesion() {

    localStorage.removeItem(
        TOKEN_KEY
    );

    localStorage.removeItem(
        USER_KEY
    );

    localStorage.removeItem(
        AUTH_KEY
    );

    localStorage.removeItem(
        'nextlevel_rol'
    );


    configurarTokenAxios();
}


function obtenerUsuarioActual() {

    try {

        const usuario =
            JSON.parse(
            localStorage.getItem(
                USER_KEY
            ) ||
            '{}'
        );

        if (
            usuario &&
            typeof usuario === 'object'
        ) {
            delete usuario.password;
            delete usuario.password_confirmation;
            delete usuario.remember_token;
        }

        return usuario;

    } catch (error) {

        localStorage.removeItem(
            USER_KEY
        );

        return {};
    }
}


function obtenerNombreUsuario() {

    const usuario =
        obtenerUsuarioActual();

    return (
        usuario.name ||
        usuario.nombre ||
        usuario.nombre_completo ||
        usuario.email ||
        'Administrador'
    );
}


function actualizarUsuarioEnInterfaz() {

    const nombreEl =
        document.getElementById(
            'sidebar-nombre-usuario'
        );

    if (!nombreEl) {
        return;
    }

    nombreEl.textContent =
        obtenerNombreUsuario();
}


// =========================================================
// AVISO DE SESIÓN
// =========================================================

function guardarAvisoSesion(
    mensaje
) {

    sessionStorage.setItem(
        AUTH_NOTICE_KEY,
        mensaje
    );
}


// =========================================================
// ESTADO DE BOTONES
// =========================================================

function activarCargaBoton(
    boton,
    texto
) {

    if (!boton) {
        return null;
    }

    const contenidoOriginal =
        boton.innerHTML;

    boton.disabled =
        true;

    boton.setAttribute(
        'aria-busy',
        'true'
    );

    boton.classList.add(
        'opacity-70',
        'cursor-not-allowed'
    );

    boton.innerHTML =
        `<span class="inline-flex items-center justify-center gap-2">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-current/30 border-t-current"></span>
            ${texto}
        </span>`;

    return contenidoOriginal;
}


function desactivarCargaBoton(
    boton,
    contenidoOriginal
) {

    if (!boton) {
        return;
    }

    boton.disabled =
        false;

    boton.removeAttribute(
        'aria-busy'
    );

    boton.classList.remove(
        'opacity-70',
        'cursor-not-allowed'
    );

    if (
        contenidoOriginal !==
        null
    ) {

        boton.innerHTML =
            contenidoOriginal;
    }
}


// =========================================================
// PROTEGER NAVEGACIÓN
// =========================================================

function protegerNavegacion() {

    const rutaActual =
        obtenerRutaActual();


    const autenticado =
        estaAutenticado();


    // =====================================================
    // LOGIN
    // =====================================================

    if (
        rutaActual ===
        RUTA_LOGIN
    ) {

        if (autenticado) {

            window.location.replace(
                RUTA_DASHBOARD
            );

            return false;
        }


        mostrarPagina();

        return true;
    }


    // =====================================================
    // REGISTRO
    // =====================================================

    if (
        rutaActual ===
        RUTA_REGISTRO
    ) {

        if (autenticado) {

            window.location.replace(
                RUTA_DASHBOARD
            );

            return false;
        }


        mostrarPagina();

        return true;
    }


    // =====================================================
    // DEMÁS RUTAS PÚBLICAS
    // =====================================================

    if (
        esRutaPublica(
            rutaActual
        )
    ) {

        mostrarPagina();

        return true;
    }


    // =====================================================
    // RUTAS PRIVADAS
    // =====================================================

    if (!autenticado) {

        window.location.replace(
            RUTA_LOGIN
        );

        return false;
    }


    mostrarPagina();

    return true;
}


// =========================================================
// INTERCEPTOR 401
// =========================================================

window.axios.interceptors.response.use(

    response =>
        response,

    error => {

        if (
            error?.response?.status ===
            401
        ) {

            const rutaActual =
                obtenerRutaActual();


            limpiarSesion();


            if (
                !esRutaPublica(
                    rutaActual
                )
            ) {

                guardarAvisoSesion(
                    'Tu sesión expiró. Vuelve a iniciar sesión para continuar.'
                );

                window.location.replace(
                    RUTA_LOGIN
                );
            }
        }


        return Promise.reject(
            error
        );
    }
);


// =========================================================
// VALIDACIÓN INICIAL
// =========================================================

document.addEventListener(
    'DOMContentLoaded',
    () => {

        protegerNavegacion();

        actualizarUsuarioEnInterfaz();


        // =================================================
        // CERRAR SESIÓN
        // =================================================

        const btnCerrarSesion =
            document.getElementById(
                'btn-cerrar-sesion'
            );


        if (!btnCerrarSesion) {
            return;
        }


        btnCerrarSesion.addEventListener(
            'click',
            async () => {

                if (
                    btnCerrarSesion.disabled
                ) {
                    return;
                }

                const token =
                    obtenerTokenSesion();

                const contenidoOriginal =
                    activarCargaBoton(
                        btnCerrarSesion,
                        'Cerrando sesión...'
                    );

                try {

                    if (token) {

                        await window.axios.post(
                            '/api/auth/logout'
                        );
                    }

                } catch (error) {

                    console.error(
                        'Error al cerrar sesión:',
                        error
                    );

                } finally {

                    limpiarSesion();

                    guardarAvisoSesion(
                        'Sesión cerrada correctamente.'
                    );

                    desactivarCargaBoton(
                        btnCerrarSesion,
                        contenidoOriginal
                    );

                    window.location.replace(
                        RUTA_LOGIN
                    );
                }
            }
        );
    }
);


// =========================================================
// ATRÁS / ADELANTE DEL NAVEGADOR
// =========================================================

window.addEventListener(
    'pageshow',
    () => {

        configurarTokenAxios();

        protegerNavegacion();
    }
);


// =========================================================
// CAMBIOS DE SESIÓN ENTRE PESTAÑAS
// =========================================================

window.addEventListener(
    'storage',
    event => {

        if (
            event.key ===
            TOKEN_KEY ||
            event.key ===
            AUTH_KEY ||
            event.key ===
            USER_KEY
        ) {

            configurarTokenAxios();

            protegerNavegacion();
        }
    }
);
