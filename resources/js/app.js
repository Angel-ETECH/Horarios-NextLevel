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


const RUTA_LOGIN =
    '/login';

const RUTA_REGISTRO =
    '/registro';

const RUTA_DASHBOARD =
    '/';


const RUTAS_PUBLICAS = [

    '/login',

    '/registro',

    '/consulta-horarios'

];


// =========================================================
// CONFIGURAR TOKEN EN AXIOS
// =========================================================

function configurarTokenAxios() {

    const token =
        localStorage.getItem(
            TOKEN_KEY
        );


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
        localStorage.getItem(
            TOKEN_KEY
        );


    return Boolean(token);
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

                const token =
                    localStorage.getItem(
                        TOKEN_KEY
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
            TOKEN_KEY
        ) {

            configurarTokenAxios();

            protegerNavegacion();
        }
    }
);