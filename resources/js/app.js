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

const AUTH_KEY =
    'nextlevel_auth';

const RUTA_LOGIN =
    '/login';

const RUTA_DASHBOARD =
    '/';

const RUTAS_PUBLICAS = [
    '/login',
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
        ) === 'true'
    );
}


// =========================================================
// MOSTRAR LA PÁGINA
// =========================================================
//
// El layout del panel utiliza "invisible" para evitar
// que se vea el contenido antes de comprobar la sesión.
//
// =========================================================

function mostrarPagina() {

    document.body.classList.remove(
        'invisible'
    );
}


// =========================================================
// PROTEGER NAVEGACIÓN
// =========================================================
//
// REGLAS:
//
// 1. /login
//    Si YA existe sesión, no puede quedarse en Login.
//    Se envía al Dashboard.
//
// 2. /consulta-horarios
//    Siempre es pública.
//
// 3. Cualquier ruta del panel
//    necesita nextlevel_auth = true.
//
// window.location.replace() evita agregar la redirección
// al historial del navegador.
//
// =========================================================

function protegerNavegacion() {

    const rutaActual =
        obtenerRutaActual();

    const autenticado =
        estaAutenticado();


    // =====================================================
    // LOGIN
    // =====================================================
    //
    // Si el usuario ya inició sesión e intenta volver
    // al Login mediante la flecha Atrás, lo devolvemos
    // inmediatamente al Dashboard.
    //
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
    // CONSULTA PÚBLICA DE HORARIOS
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
    // RUTAS PRIVADAS DEL PANEL
    // =====================================================
    //
    // Si cerró sesión y usa Atrás, no permitimos volver
    // al Dashboard ni a ningún módulo administrativo.
    //
    // =====================================================

    if (!autenticado) {

        window.location.replace(
            RUTA_LOGIN
        );

        return false;
    }


    // Sesión válida
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

                // =========================================
                // ELIMINAR SOLO DATOS DE SESIÓN
                // =========================================

                localStorage.removeItem(
                    'nextlevel_auth'
                );

                localStorage.removeItem(
                    'nextlevel_rol'
                );

                localStorage.removeItem(
                    'nextlevel_usuario'
                );


                // =========================================
                // IR AL LOGIN
                // =========================================
                //
                // replace() evita que el Dashboard quede
                // como la página inmediatamente anterior.
                //
                // =========================================

                window.location.replace(
                    RUTA_LOGIN
                );
            }
        );
    }
);


// =========================================================
// PROTECCIÓN CONTRA ATRÁS / ADELANTE
// =========================================================
//
// Los navegadores pueden recuperar una página desde
// BFCache sin volver a ejecutar DOMContentLoaded.
//
// "pageshow" se ejecuta también cuando una página vuelve
// mediante las flechas Atrás o Adelante.
//
// Por eso volvemos a comprobar la sesión aquí.
//
// =========================================================

window.addEventListener(
    'pageshow',
    () => {

        protegerNavegacion();
    }
);


// =========================================================
// DETECTAR CAMBIOS DE SESIÓN ENTRE PESTAÑAS
// =========================================================
//
// Si se cierra la sesión desde otra pestaña, esta página
// también vuelve a validar su acceso.
//
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