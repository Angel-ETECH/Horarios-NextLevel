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


// =========================================================
// CONFIGURACIÓN DE AUTENTICACIÓN SIMULADA
// =========================================================
//
// IMPORTANTE:
//
// Actualmente solamente el login utiliza una simulación
// mediante localStorage.
//
// El registro NO almacenará contraseñas en localStorage.
//
// Cuando el backend esté terminado, esta autenticación
// simulada será reemplazada por la autenticación real.
//
// =========================================================

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
// COMPROBAR SI EXISTE SESIÓN
// =========================================================

function estaAutenticado() {

    return (
        localStorage.getItem(
            AUTH_KEY
        ) ===
        'true'
    );
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

        /*
         * Si ya inició sesión, no tiene sentido
         * permanecer en Registro.
         */
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
            () => {

                /*
                 * Solamente eliminamos información
                 * relacionada con la sesión.
                 *
                 * NO eliminamos profesores, horarios,
                 * cursos, aulas, etc.
                 */

                localStorage.removeItem(
                    'nextlevel_auth'
                );

                localStorage.removeItem(
                    'nextlevel_rol'
                );

                localStorage.removeItem(
                    'nextlevel_usuario'
                );


                window.location.replace(
                    RUTA_LOGIN
                );
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
            AUTH_KEY
        ) {

            protegerNavegacion();
        }
    }
);