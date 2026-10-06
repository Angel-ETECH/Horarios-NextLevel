document.addEventListener('DOMContentLoaded', () => {
    const contenedor = document.getElementById('horarios-render-container');

    if (!contenedor) {
        return;
    }

    // =========================================================
    // HELPERS DOM
    // =========================================================

    const $ = (id) => document.getElementById(id);

    // =========================================================
    // ELEMENTOS PRINCIPALES
    // =========================================================

    const tabsInstitucion =
        document.querySelectorAll('.institucion-tab');

    const vistaBotones =
        document.querySelectorAll('.vista-btn');

    const chkCompleto =
        $('chk-semana-completa');

    const filtroProfesor =
        $('horario-profesor');

    const filtroAula =
        $('horario-aula');

    const filtroGrado =
        $('horario-grado');

    const filtroCurso =
        $('horario-curso');

    const btnReset =
        $('btn-reset');

    const tituloEl =
        $('titulo-horario');

    const subtituloEl =
        $('subtitulo-horario');

    const statProfesores =
        $('total-profesores');

    const statAulas =
        $('total-aulas');

    const statClases =
        $('total-clases');

    const statEstado =
        $('estado-horario');

    const statEstadoPunto =
        $('estado-punto');

    const mensajeEl =
        $('horario-mensaje');

    const modoEdicionEl =
        $('modo-edicion-horario');

    const configNivel =
        $('config-horario-nivel');

    const configTurno =
        $('config-horario-turno');

    const configNombre =
        $('config-horario-nombre');

    const configRecesoInicio =
        $('config-receso-inicio');

    const configRecesoMinutos =
        $('config-receso-minutos');

    const configStatus =
        $('config-horario-status');

    const configBloquesLista =
        $('config-bloques-lista');

    const btnAgregarBloqueClase =
        $('btn-agregar-bloque-clase');

    const btnAgregarBloqueReceso =
        $('btn-agregar-bloque-receso');

    const btnGuardarConfigHorario =
        $('btn-guardar-config-horario');

    // =========================================================
    // MODAL ELIMINAR
    // =========================================================

    const eliminarHorarioModal =
        $('eliminar-horario-modal');

    const eliminarHorarioOverlay =
        $('eliminar-horario-overlay');

    const closeEliminarHorarioModal =
        $('close-eliminar-horario-modal');

    const cancelEliminarHorarioModal =
        $('cancel-eliminar-horario-modal');

    const confirmEliminarHorario =
        $('confirm-eliminar-horario');

    const eliminarHorarioProfesor =
        $('eliminar-horario-profesor');

    const eliminarHorarioCurso =
        $('eliminar-horario-curso');

    const eliminarHorarioDia =
        $('eliminar-horario-dia');

    const eliminarHorarioHora =
        $('eliminar-horario-hora');

    const eliminarHorarioAula =
        $('eliminar-horario-aula');

    // =========================================================
    // API REAL
    // =========================================================

    const API = {
        horarios:
            '/api/horarios',

        grados:
            '/api/grados',

        disponibilidades:
            '/api/disponibilidades',

        configuraciones:
            '/api/configuraciones-horario',

        exportPdf:
            '/api/exportar/pdf/download',

        exportImagen:
            '/api/exportar/imagen/html',
    };

    // =========================================================
    // DÍAS
    // =========================================================

    const DIAS = [
        {
            numero: 1,
            api: 'lunes',
            texto: 'Lunes',
            corto: 'LUN',
        },
        {
            numero: 2,
            api: 'martes',
            texto: 'Martes',
            corto: 'MAR',
        },
        {
            numero: 3,
            api: 'miercoles',
            texto: 'Miércoles',
            corto: 'MIÉ',
        },
        {
            numero: 4,
            api: 'jueves',
            texto: 'Jueves',
            corto: 'JUE',
        },
        {
            numero: 5,
            api: 'viernes',
            texto: 'Viernes',
            corto: 'VIE',
        },
        {
            numero: 6,
            api: 'sabado',
            texto: 'Sábado',
            corto: 'SÁB',
        },
    ];

    // =========================================================
    // CONFIGURACIÓN DE TIMELINE
    // =========================================================

    const TIMELINE = {
        /*
         * Inicio fijo solicitado.
         *
         * 07:00
         */
        inicio:
            420,

        /*
         * Todo horario muestra como mínimo
         * hasta las 20:00.
         */
        finMinimo:
            1200,

        /*
         * Límite máximo permitido: 20:00.
         */
        finMaximo:
            1200,

        /*
         * Retícula cada 15 minutos.
         */
        pasoLinea:
            15,

        /*
         * Etiquetas:
         *
         * 07:00
         * 07:30
         * 08:00
         * ...
         */
        etiquetaCada:
            30,

        /*
         * Drag & drop con precisión
         * de 5 minutos.
         */
        pasoMovimiento:
            5,

        /*
         * Una clase de 45 minutos:
         *
         * 45 × 2.25 = 101.25px
         *
         * Esto evita cards apretadas.
         */
        pxPorMinuto:
            2.25,

        /*
         * Espacio superior e inferior
         * para no cortar 07:00 ni 20:00.
         */
        paddingSuperior:
            24,

        paddingInferior:
            24,

        /*
         * Eje izquierdo.
         */
        anchoHora:
            94,

        /*
         * Día sin superposición.
         */
        anchoDia:
            185,

        /*
         * Cada carril adicional.
         */
        anchoCarril:
            145,

        /*
         * Máximo cuando existen
         * varias clases simultáneas.
         */
        anchoDiaMax:
            475,
    };

    // =========================================================
    // ESTADO
    // =========================================================

    let institucionActiva =
        'colegio';

    let vistaActual =
        'profesor';

    let horariosCache =
        [];

    let gradosCache =
        [];

    let disponibilidadesCache =
        [];

    let configuracionesHorarioCache =
        [];

    let configuracionHorarioActual =
        null;

    let bloquesConfiguracionHorario =
        [];

    let gruposVisibles =
        [];

    let bloqueArrastradoId =
        null;

    let horarioAEliminarId =
        null;

    let cargando =
        false;

    // =========================================================
    // UTILIDADES
    // =========================================================

    const esc = (valor) =>
        String(
            valor ?? ''
        )
            .replace(
                /&/g,
                '&amp;'
            )
            .replace(
                /</g,
                '&lt;'
            )
            .replace(
                />/g,
                '&gt;'
            )
            .replace(
                /"/g,
                '&quot;'
            )
            .replace(
                /'/g,
                '&#039;'
            );

    const normalizarTexto = (valor) =>
        String(
            valor ?? ''
        )
            .trim()
            .toLowerCase()
            .normalize(
                'NFD'
            )
            .replace(
                /[\u0300-\u036f]/g,
                ''
            )
            .replace(
                /\s+/g,
                ' '
            );

    const normalizarInstitucion = (valor) =>
        normalizarTexto(
            valor ||
            'colegio'
        );

    // =========================================================
    // HORAS
    // =========================================================

    function horaCorta(
        valor
    ) {
        if (!valor) {
            return '';
        }

        const match =
            String(
                valor
            ).match(
                /(\d{1,2}):(\d{2})/
            );

        if (!match) {
            return '';
        }

        return (
            `${String(
                match[1]
            ).padStart(
                2,
                '0'
            )}:${match[2]}`
        );
    }

    function aMinutos(
        hora
    ) {
        const texto =
            horaCorta(
                hora
            );

        if (!texto) {
            return 0;
        }

        const [
            horas,
            minutos,
        ] =
            texto
                .split(':')
                .map(
                    Number
                );

        return (
            (horas || 0) *
            60
            +
            (minutos || 0)
        );
    }

    function aTexto(
        minutos
    ) {
        const total =
            Math.max(
                0,
                Math.round(
                    minutos
                )
            );

        const horas =
            String(
                Math.floor(
                    total /
                    60
                )
            ).padStart(
                2,
                '0'
            );

        const mins =
            String(
                total %
                60
            ).padStart(
                2,
                '0'
            );

        return (
            `${horas}:${mins}`
        );
    }

    const duracionMinutos = (horario) =>
        Math.max(
            0,

            aMinutos(
                horario.hora_fin
            )
            -
            aMinutos(
                horario.hora_inicio
            )
        );

    const redondearArriba = (
        minutos,
        paso = 30
    ) =>
        Math.ceil(
            minutos /
            paso
        )
        *
        paso;

    const existeTraslape = (
        inicioA,
        finA,
        inicioB,
        finB
    ) =>
        (
            aMinutos(
                inicioA
            )
            <
            aMinutos(
                finB
            )
        )
        &&
        (
            aMinutos(
                inicioB
            )
            <
            aMinutos(
                finA
            )
        );

    function determinarTurno(
        hora
    ) {
        const minutos =
            aMinutos(
                hora
            );

        if (
            minutos <
            720
        ) {
            return 'mañana';
        }

        if (
            minutos <
            1080
        ) {
            return 'tarde';
        }

        return 'noche';
    }

    // =========================================================
    // DÍAS
    // =========================================================

    function diaANumero(
        valor
    ) {
        if (
            valor === null
            ||
            valor === undefined
            ||
            valor === ''
        ) {
            return 0;
        }

        const numero =
            Number(
                valor
            );

        if (
            Number.isInteger(
                numero
            )
            &&
            numero >= 1
            &&
            numero <= 6
        ) {
            return numero;
        }

        const normal =
            normalizarTexto(
                valor
            );

        return (
            DIAS.find(
                (dia) =>
                    normalizarTexto(
                        dia.api
                    ) ===
                    normal
                    ||
                    normalizarTexto(
                        dia.texto
                    ) ===
                    normal
            )?.numero
            ||
            0
        );
    }

    const diaATextoApi = (valor) =>
        DIAS.find(
            (dia) =>
                dia.numero ===
                diaANumero(
                    valor
                )
        )?.api
        ||
        normalizarTexto(
            valor
        );

    const diaATexto = (valor) =>
        DIAS.find(
            (dia) =>
                dia.numero ===
                diaANumero(
                    valor
                )
        )?.texto
        ||
        String(
            valor ||
            ''
        );

    // =========================================================
    // RESPUESTAS API
    // =========================================================

    function extraerLista(
        payload
    ) {
        if (
            Array.isArray(
                payload
            )
        ) {
            return payload;
        }

        if (
            Array.isArray(
                payload?.data
            )
        ) {
            return payload.data;
        }

        if (
            Array.isArray(
                payload
                    ?.data
                    ?.data
            )
        ) {
            return payload
                .data
                .data;
        }

        return [];
    }

    function extraerMeta(
        payload
    ) {
        if (
            payload?.meta
        ) {
            return payload.meta;
        }

        if (
            payload?.data
            &&
            !Array.isArray(
                payload.data
            )
            &&
            payload.data.last_page
        ) {
            return {
                current_page:
                    payload
                        .data
                        .current_page,

                last_page:
                    payload
                        .data
                        .last_page,

                total:
                    payload
                        .data
                        .total,
            };
        }

        return null;
    }

    async function cargarTodasLasPaginas(
        url,
        params = {}
    ) {
        const primera =
            await window
                .axios
                .get(
                    url,
                    {
                        params: {
                            ...params,

                            per_page:
                                100,

                            page:
                                1,
                        },
                    }
                );

        const lista = [
            ...extraerLista(
                primera.data
            ),
        ];

        const ultima =
            Number(
                extraerMeta(
                    primera.data
                )?.last_page
                ||
                1
            );

        for (
            let pagina = 2;
            pagina <= ultima;
            pagina++
        ) {
            const respuesta =
                await window
                    .axios
                    .get(
                        url,
                        {
                            params: {
                                ...params,

                                per_page:
                                    100,

                                page:
                                    pagina,
                            },
                        }
                    );

            lista.push(
                ...extraerLista(
                    respuesta.data
                )
            );
        }

        return lista;
    }

    function obtenerMensajeError(
        error,
        fallback = 'Ocurrió un error.'
    ) {
        const data =
            error
                ?.response
                ?.data;

        if (
            data?.message
        ) {
            return data.message;
        }

        if (
            typeof data?.error ===
            'string'
        ) {
            return data.error;
        }

        if (
            data?.errors
            &&
            typeof data.errors ===
            'object'
        ) {
            return (
                Object
                    .values(
                        data.errors
                    )
                    .flat()[0]
                ||
                fallback
            );
        }

        return fallback;
    }

    // =========================================================
    // MENSAJES
    // =========================================================

    function mostrarMensaje(
        texto,
        tipo = 'ok'
    ) {
        if (!mensajeEl) {
            return;
        }

        const estilos = {
            ok:
                'border-emerald-200 bg-emerald-50 text-emerald-700',

            error:
                'border-red-200 bg-red-50 text-red-700',

            info:
                'border-blue-200 bg-blue-50 text-blue-700',

            warning:
                'border-amber-200 bg-amber-50 text-amber-800',
        };

        mensajeEl.className =
            `
                fixed
                bottom-5
                right-5
                z-[100]
                max-w-md
                rounded-2xl
                border
                p-4
                text-sm
                font-medium
                shadow-2xl
                ${estilos[tipo] || estilos.info}
            `;

        mensajeEl.textContent =
            texto;

        mensajeEl
            .classList
            .remove(
                'hidden'
            );

        clearTimeout(
            mostrarMensaje.timer
        );

        mostrarMensaje.timer =
            setTimeout(
                () => {
                    mensajeEl
                        .classList
                        .add(
                            'hidden'
                        );
                },
                4500
            );
    }

    // =========================================================
    // CONFIGURACION DE BLOQUES Y RECESOS
    // =========================================================

    function nombreInstitucionActual() {
        return institucionActiva === 'academia'
            ? 'Academia'
            : 'Colegio';
    }

    function nombreConfigHorarioDefault() {
        const nivel =
            configNivel?.value
            ||
            (
                institucionActiva === 'academia'
                    ? 'academia'
                    : 'primaria'
            );

        const turno =
            configTurno?.value
            ||
            'mañana';

        return `${nombreInstitucionActual()} ${nivel} - ${turno}`;
    }

    function actualizarOpcionesNivelConfig() {
        if (!configNivel) {
            return;
        }

        const opciones =
            institucionActiva === 'academia'
                ? [
                    {
                        valor:
                            'academia',

                        texto:
                            'Academia',
                    },
                ]
                : [
                    {
                        valor:
                            'primaria',

                        texto:
                            'Primaria',
                    },
                    {
                        valor:
                            'secundaria',

                        texto:
                            'Secundaria',
                    },
                ];

        const actual =
            configNivel.value;

        configNivel.innerHTML =
            opciones
                .map(
                    (opcion) =>
                        `<option value="${esc(opcion.valor)}">${esc(opcion.texto)}</option>`
                )
                .join(
                    ''
                );

        configNivel.value =
            opciones.some(
                (opcion) =>
                    opcion.valor ===
                    actual
            )
                ? actual
                : opciones[0].valor;
    }

    function setEstadoConfigHorario(
        texto,
        tipo = 'info'
    ) {
        if (!configStatus) {
            return;
        }

        const estilos = {
            info:
                'bg-slate-100 text-slate-600',

            ok:
                'bg-emerald-50 text-emerald-700',

            error:
                'bg-red-50 text-red-700',
        };

        configStatus.className =
            `rounded-full px-4 py-2 text-xs font-semibold ${estilos[tipo] || estilos.info}`;

        configStatus.textContent =
            texto;
    }

    function crearBloqueConfig(
        tipo = 'clase'
    ) {
        const ultimo =
            bloquesConfiguracionHorario
                .map(
                    (bloque) =>
                        aMinutos(
                            bloque.hora_fin
                        )
                )
                .filter(
                    Boolean
                )
                .sort(
                    (a, b) =>
                        b - a
                )[0];

        const inicio =
            ultimo
            ||
            420;

        const fin =
            tipo === 'receso'
                ? inicio + 15
                : inicio + 45;

        return {
            orden:
                bloquesConfiguracionHorario.length + 1,

            tipo,

            nombre:
                tipo === 'receso'
                    ? 'Receso'
                    : 'Clase',

            hora_inicio:
                aTexto(
                    Math.min(
                        inicio,
                        1199
                    )
                ),

            hora_fin:
                aTexto(
                    Math.min(
                        fin,
                        1200
                    )
                ),

            numero_bloque:
                tipo === 'clase'
                    ? bloquesConfiguracionHorario.filter(
                        (bloque) =>
                            bloque.tipo ===
                            'clase'
                    ).length + 1
                    : null,
        };
    }

    function normalizarBloquesConfig() {
        let numeroClase =
            1;

        return bloquesConfiguracionHorario
            .map(
                (bloque) => ({
                    orden:
                        Number(
                            bloque.orden
                        )
                        ||
                        0,

                    tipo:
                        bloque.tipo === 'receso'
                            ? 'receso'
                            : 'clase',

                    nombre:
                        String(
                            bloque.nombre
                            ||
                            ''
                        ).trim(),

                    hora_inicio:
                        horaCorta(
                            bloque.hora_inicio
                        ),

                    hora_fin:
                        horaCorta(
                            bloque.hora_fin
                        ),

                    numero_bloque:
                        bloque.numero_bloque
                            ? Number(
                                bloque.numero_bloque
                            )
                            : null,
                })
            )
            .filter(
                (bloque) =>
                    bloque.hora_inicio
                    &&
                    bloque.hora_fin
            )
            .sort(
                (a, b) =>
                    aMinutos(
                        a.hora_inicio
                    )
                    -
                    aMinutos(
                        b.hora_inicio
                    )
            )
            .map(
                (bloque, index) => {
                    const esClase =
                        bloque.tipo ===
                        'clase';

                    return {
                        ...bloque,

                        orden:
                            index + 1,

                        nombre:
                            bloque.nombre
                            ||
                            (
                                esClase
                                    ? `Clase ${numeroClase}`
                                    : 'Receso'
                            ),

                        numero_bloque:
                            esClase
                                ? numeroClase++
                                : null,
                    };
                }
            );
    }

    function obtenerMinutosRecesoConfig() {
        const valor =
            Number(
                configRecesoMinutos?.value
            );

        if (
            Number.isNaN(
                valor
            )
        ) {
            return 15;
        }

        return Math.min(
            60,
            Math.max(
                5,
                valor
            )
        );
    }

    function obtenerInicioRecesoConfig() {
        return horaCorta(
            configRecesoInicio?.value
        )
        ||
        '09:00';
    }

    function recesosBaseConfig() {
        const recesos =
            [
                {
                    nombre:
                        'Receso mañana 1',
                    hora_inicio:
                        '09:00',
                    hora_fin:
                        '09:30',
                },
                {
                    nombre:
                        'Receso mañana 2',
                    hora_inicio:
                        '11:00',
                    hora_fin:
                        '11:30',
                },
                {
                    nombre:
                        'Receso tarde 1',
                    hora_inicio:
                        '15:00',
                    hora_fin:
                        '15:30',
                },
                {
                    nombre:
                        'Receso tarde 2',
                    hora_inicio:
                        '17:00',
                    hora_fin:
                        '17:30',
                },
            ];

        if (
            configTurno?.value ===
            'mañana'
        ) {
            return recesos.slice(
                0,
                2
            );
        }

        if (
            configTurno?.value ===
            'tarde'
        ) {
            return recesos.slice(
                2
            );
        }

        return recesos;
    }

    function limitesTurnoConfig() {
        if (
            configTurno?.value ===
            'mañana'
        ) {
            return {
                inicio:
                    '07:00',
                fin:
                    '12:00',
            };
        }

        if (
            configTurno?.value ===
            'tarde'
        ) {
            return {
                inicio:
                    '13:00',
                fin:
                    '20:00',
            };
        }

        return {
            inicio:
                '07:00',
            fin:
                '20:00',
        };
    }

    function aplicarMinutosRecesoConfig(
        bloquesBase
    ) {
        void bloquesBase;

        const limites =
            limitesTurnoConfig();

        const recesos =
            recesosBaseConfig();

        const bloques =
            [];

        let numeroClase =
            1;

        let cursor =
            aMinutos(
                limites.inicio
            );

        const finTurno =
            aMinutos(
                limites.fin
            );

        const duracionClase =
            45;

        while (
            cursor <
            finTurno
        ) {
            const recesoActual =
                recesos.find(
                    receso =>
                        aMinutos(
                            receso.hora_inicio
                        ) ===
                        cursor
                );

            if (
                recesoActual
            ) {
                bloques.push({
                    orden:
                        bloques.length + 1,
                    tipo:
                        'receso',
                    nombre:
                        recesoActual.nombre,
                    hora_inicio:
                        recesoActual.hora_inicio,
                    hora_fin:
                        recesoActual.hora_fin,
                    numero_bloque:
                        null,
                });

                cursor =
                    aMinutos(
                        recesoActual.hora_fin
                    );

                continue;
            }

            const siguienteReceso =
                recesos.find(
                    receso =>
                        aMinutos(
                            receso.hora_inicio
                        ) >
                        cursor
                );

            let finClase =
                Math.min(
                    cursor + duracionClase,
                    finTurno
                );

            if (
                siguienteReceso &&
                finClase >
                    aMinutos(
                        siguienteReceso.hora_inicio
                    )
            ) {
                // El último bloque antes del receso puede ser más corto.
                // Así el horario queda continuo y termina exactamente al
                // comenzar el receso, sin dejar minutos sin asignar.
                finClase = aMinutos(
                    siguienteReceso.hora_inicio
                );
            }

            if (finClase <= cursor) {
                cursor = siguienteReceso
                    ? aMinutos(siguienteReceso.hora_inicio)
                    : finTurno;
                continue;
            }

            bloques.push({
                orden:
                    bloques.length + 1,
                tipo:
                    'clase',
                nombre:
                    `Clase ${numeroClase}`,
                hora_inicio:
                    aTexto(
                        cursor
                    ),
                hora_fin:
                    aTexto(
                        finClase
                    ),
                numero_bloque:
                    numeroClase,
            });

            numeroClase++;
            cursor =
                finClase;
        }

        return bloques;
    }

    function renderBloquesConfig() {
        if (!configBloquesLista) {
            return;
        }

        bloquesConfiguracionHorario =
            aplicarMinutosRecesoConfig(
                normalizarBloquesConfig()
            );

        if (
            !bloquesConfiguracionHorario.length
        ) {
            configBloquesLista.innerHTML =
                `
                    <div class="px-4 py-8 text-center text-sm text-slate-500">
                        Agrega bloques de clase y recesos para este turno.
                    </div>
                `;

            return;
        }

        configBloquesLista.innerHTML =
            bloquesConfiguracionHorario
                .map(
                    (bloque) => `
                        <div
                            class="rounded-2xl border p-4 ${bloque.tipo === 'receso' ? 'border-red-100 bg-red-50' : 'border-slate-200 bg-white'}"
                        >
                            <p class="text-xs font-bold uppercase tracking-wide ${bloque.tipo === 'receso' ? 'text-red-500' : 'text-slate-400'}">
                                ${bloque.tipo === 'receso' ? 'Receso' : `Clase ${esc(bloque.numero_bloque || '')}`}
                            </p>

                            <p class="mt-2 text-sm font-bold text-slate-900">
                                ${esc(bloque.nombre)}
                            </p>

                            <p class="mt-1 text-sm font-semibold ${bloque.tipo === 'receso' ? 'text-red-700' : 'text-[#1B3A6B]'}">
                                ${esc(bloque.hora_inicio)} - ${esc(bloque.hora_fin)}
                            </p>
                        </div>
                    `
                )
                .join(
                    ''
                );
    }

    function validarBloquesConfig(
        bloques
    ) {
        if (
            !bloques.some(
                (bloque) =>
                    bloque.tipo ===
                    'clase'
            )
        ) {
            return 'Agrega al menos un bloque de clase.';
        }

        for (
            let index = 0;
            index < bloques.length;
            index++
        ) {
            const bloque =
                bloques[index];

            const inicio =
                aMinutos(
                    bloque.hora_inicio
                );

            const fin =
                aMinutos(
                    bloque.hora_fin
                );

            if (
                inicio < 420
                ||
                fin > 1200
            ) {
                return 'Los bloques deben estar entre 07:00 y 20:00.';
            }

            if (
                fin <= inicio
            ) {
                return 'Cada bloque debe terminar después de iniciar.';
            }

            const siguiente =
                bloques[index + 1];

            if (
                siguiente
                &&
                aMinutos(
                    siguiente.hora_inicio
                ) < fin
            ) {
                return 'Hay bloques cruzados. Ajusta las horas antes de guardar.';
            }
        }

        return null;
    }

    function mapearBloquesConfig(
        bloques = []
    ) {
        bloquesConfiguracionHorario =
            bloques.map(
                (bloque) => ({
                    orden:
                        bloque.orden,

                    tipo:
                        bloque.tipo,

                    nombre:
                        bloque.nombre,

                    hora_inicio:
                        horaCorta(
                            bloque.hora_inicio
                        ),

                    hora_fin:
                        horaCorta(
                            bloque.hora_fin
                        ),

                    numero_bloque:
                        bloque.numero_bloque,
                })
            );
    }

    function configuracionInicialBloques() {
        bloquesConfiguracionHorario =
            [
                {
                    orden:
                        1,

                    tipo:
                        'clase',

                    nombre:
                        'Clase 1',

                    hora_inicio:
                        '07:00',

                    hora_fin:
                        '07:45',

                    numero_bloque:
                        1,
                },
                {
                    orden:
                        2,

                    tipo:
                        'receso',

                    nombre:
                        'Receso',

                    hora_inicio:
                        '09:15',

                    hora_fin:
                        '09:30',

                    numero_bloque:
                        null,
                },
            ];
    }

    async function cargarConfigHorario() {
        if (
            !configNivel
            ||
            !configTurno
            ||
            !configBloquesLista
        ) {
            return;
        }

        actualizarOpcionesNivelConfig();

        setEstadoConfigHorario(
            'Cargando configuración...',
            'info'
        );

        try {
            const configuraciones =
                await cargarTodasLasPaginas(
                    API.configuraciones,
                    {
                        institucion:
                            institucionActiva,

                        nivel:
                            configNivel.value,

                        turno:
                            configTurno.value,
                    }
                );

            configuracionHorarioActual =
                configuraciones
                    .filter(
                        (config) =>
                            normalizarInstitucion(
                                config.institucion
                            ) ===
                            institucionActiva
                            &&
                            String(
                                config.nivel
                            ) ===
                            String(
                                configNivel.value
                            )
                            &&
                            String(
                                config.turno
                            ) ===
                            String(
                                configTurno.value
                            )
                    )
                    .sort(
                        (a, b) =>
                            Number(
                                b.año_academico
                                ||
                                0
                            )
                            -
                            Number(
                                a.año_academico
                                ||
                                0
                            )
                    )[0]
                ||
                null;

            if (
                configuracionHorarioActual
            ) {
                if (
                    configNombre
                ) {
                    configNombre.value =
                        configuracionHorarioActual.nombre
                        ||
                        nombreConfigHorarioDefault();
                }

                mapearBloquesConfig(
                    configuracionHorarioActual.bloques
                    ||
                    []
                );

                const primerReceso =
                    normalizarBloquesConfig()
                        .find(
                            (bloque) =>
                                bloque.tipo ===
                                'receso'
                        );

                if (
                    configRecesoMinutos
                ) {
                    configRecesoMinutos.value =
                        primerReceso
                            ? Math.max(
                                5,
                                aMinutos(
                                    primerReceso.hora_fin
                                )
                                -
                                aMinutos(
                                    primerReceso.hora_inicio
                                )
                            )
                            : 15;
                }

                if (
                    configRecesoInicio
                ) {
                    configRecesoInicio.value =
                        primerReceso
                            ? horaCorta(
                                primerReceso.hora_inicio
                            )
                            : '09:00';
                }

                setEstadoConfigHorario(
                    'Configuración activa cargada',
                    'ok'
                );
            } else {
                if (
                    configNombre
                ) {
                    configNombre.value =
                        nombreConfigHorarioDefault();
                }

                configuracionInicialBloques();

                if (
                    configRecesoMinutos
                ) {
                    configRecesoMinutos.value =
                        15;
                }

                if (
                    configRecesoInicio
                ) {
                    configRecesoInicio.value =
                        '09:00';
                }

                setEstadoConfigHorario(
                    'Sin guardar para esta selección',
                    'info'
                );
            }

            renderBloquesConfig();
        } catch (error) {
            console.error(
                'Error cargando configuración de horario:',
                error
            );

            configuracionHorarioActual =
                null;

            configuracionInicialBloques();

            renderBloquesConfig();

            setEstadoConfigHorario(
                'No se pudo cargar la configuración',
                'error'
            );
        }
    }

    async function guardarConfigHorario() {
        if (
            !configNivel
            ||
            !configTurno
        ) {
            return;
        }

        const bloques =
            aplicarMinutosRecesoConfig(
                normalizarBloquesConfig()
            );

        const errorValidacion =
            validarBloquesConfig(
                bloques
            );

        if (
            errorValidacion
        ) {
            mostrarMensaje(
                errorValidacion,
                'error'
            );

            return;
        }

        const inicio =
            bloques[0].hora_inicio;

        const fin =
            bloques[
                bloques.length - 1
            ].hora_fin;

        const payload = {
            institucion:
                institucionActiva,

            nivel:
                configNivel.value,

            turno:
                configTurno.value,

            nombre:
                String(
                    configNombre?.value
                    ||
                    nombreConfigHorarioDefault()
                ).trim(),

            hora_inicio:
                inicio,

            hora_fin:
                fin,

            duracion_bloque_minutos:
                45,

            año_academico:
                new Date().getFullYear(),

            activo:
                true,

            bloques,
        };

        const original =
            btnGuardarConfigHorario?.textContent;

        try {
            if (
                btnGuardarConfigHorario
            ) {
                btnGuardarConfigHorario.disabled =
                    true;

                btnGuardarConfigHorario.textContent =
                    'Guardando...';
            }

            const respuesta =
                configuracionHorarioActual?.id
                    ? await window
                        .axios
                        .put(
                            `${API.configuraciones}/${configuracionHorarioActual.id}`,
                            payload
                        )
                    : await window
                        .axios
                        .post(
                            API.configuraciones,
                            payload
                        );

            configuracionHorarioActual =
                respuesta
                    .data
                    ?.data
                ||
                configuracionHorarioActual;

            const reprogramacion =
                respuesta
                    .data
                    ?.reprogramacion_horarios;

            const incompletas =
                Array.isArray(
                    reprogramacion?.asignaciones_incompletas
                )
                    ? reprogramacion.asignaciones_incompletas.length
                    : 0;

            const errores =
                Array.isArray(
                    reprogramacion?.errores
                )
                    ? reprogramacion.errores.length
                    : 0;

            const mensajeReprogramacion =
                reprogramacion?.total_eliminados > 0
                    ? `Configuración guardada. Se adaptaron ${reprogramacion.horarios_creados || 0} clase(s) al nuevo receso.`
                    : 'Configuración de bloques guardada.';

            mostrarMensaje(
                incompletas || errores
                    ? `${mensajeReprogramacion} Revisa ${incompletas + errores} asignación(es) pendiente(s).`
                    : mensajeReprogramacion,
                incompletas || errores
                    ? 'warning'
                    : 'ok'
            );

            await cargarConfigHorario();

            await cargarDatos({
                silencioso:
                    true,
            });
        } catch (error) {
            console.error(
                'Error guardando configuración de horario:',
                error
            );

            mostrarMensaje(
                obtenerMensajeError(
                    error,
                    'No se pudo guardar la configuración.'
                ),
                'error'
            );
        } finally {
            if (
                btnGuardarConfigHorario
            ) {
                btnGuardarConfigHorario.disabled =
                    false;

                btnGuardarConfigHorario.textContent =
                    original
                    ||
                    'Guardar configuración';
            }
        }
    }

    // =========================================================
    // PERMISOS
    // =========================================================

    function puedeEditarHorario() {
        let rol =
            '';

        try {
            const usuario =
                JSON.parse(
                    localStorage.getItem(
                        'nextlevel_usuario'
                    )
                    ||
                    '{}'
                );

            rol =
                usuario?.rol
                ||
                usuario?.role
                ||
                '';
        } catch (_) {
            rol =
                '';
        }

        rol =
            String(
                rol
                ||
                localStorage.getItem(
                    'nextlevel_rol'
                )
                ||
                'administrador'
            )
                .trim()
                .toLowerCase();

        return [
            'administrador',
            'director',
        ].includes(
            rol
        );
    }

    if (
        modoEdicionEl
        &&
        !puedeEditarHorario()
    ) {
        modoEdicionEl.innerHTML =
            `
                <span
                    class="
                        h-2
                        w-2
                        rounded-full
                        bg-slate-400
                    "
                ></span>

                Solo lectura
            `;

        modoEdicionEl.className =
            `
                inline-flex
                shrink-0
                items-center
                gap-2
                rounded-full
                bg-slate-100
                px-3
                py-1.5
                text-xs
                font-semibold
                text-slate-600
            `;
    }

    // =========================================================
    // NOMBRES
    // =========================================================

    function nombreProfesor(
        profesor
    ) {
        if (!profesor) {
            return (
                'Profesor no disponible'
            );
        }

        if (
            profesor.nombre_completo
        ) {
            return (
                profesor.nombre_completo
            );
        }

        const nombre =
            profesor.nombre
            ||
            profesor.nombres
            ||
            '';

        const apellidos = [
            profesor.apellido_paterno,
            profesor.apellido_materno,
            profesor.apellido,
        ]
            .filter(
                Boolean
            )
            .join(
                ' '
            );

        return (
            `${nombre} ${apellidos}`
                .replace(
                    /\s+/g,
                    ' '
                )
                .trim()
            ||
            'Profesor'
        );
    }

    const nombreAula = (aula) =>
        aula?.nombre
        ||
        aula?.nombre_aula
        ||
        aula?.codigo
        ||
        'Aula no disponible';

    const nombreCurso = (curso) =>
        curso?.nombre
        ||
        curso?.nombre_curso
        ||
        curso?.codigo
        ||
        'Curso no disponible';

    function nombreGrado(
        grado
    ) {
        if (!grado) {
            return 'Sin grado';
        }

        return (
            grado.nombre_completo
            ||
            grado.nombre
            ||
            `${grado.grado || ''} ${grado.seccion || ''}`
                .trim()
            ||
            'Grado'
        );
    }

    function datosClase(
        bloque
    ) {
        return {
            profesor:
                nombreProfesor(
                    bloque.profesor
                ),

            aula:
                nombreAula(
                    bloque.aula
                ),

            grado:
                nombreGrado(
                    bloque.grado
                ),

            curso:
                nombreCurso(
                    bloque.curso
                ),
        };
    }

    // =========================================================
    // SELECTS PERSONALIZADOS
    // =========================================================

    function iconoFiltro(
        tipo
    ) {
        const iconos = {
            profesor:
                `
                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <circle
                            cx="12"
                            cy="7"
                            r="4"
                        />

                        <path
                            d="M20 21a8 8 0 0 0-16 0"
                        />
                    </svg>
                `,

            aula:
                `
                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <rect
                            x="3"
                            y="4"
                            width="18"
                            height="16"
                            rx="2"
                        />

                        <path
                            d="
                                M7 8h3
                                M14 8h3
                                M7 12h3
                                M14 12h3
                            "
                        />
                    </svg>
                `,

            grado:
                `
                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <path
                            d="m2 10 10-5 10 5-10 5Z"
                        />

                        <path
                            d="M6 12v5c3 2 9 2 12 0v-5"
                        />
                    </svg>
                `,

            curso:
                `
                    <svg
                        width="18"
                        height="18"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                    >
                        <path
                            d="
                                M4 19.5
                                A2.5 2.5 0 0 1 6.5 17
                                H20
                            "
                        />

                        <path
                            d="
                                M6.5 2
                                H20
                                v20
                                H6.5
                                A2.5 2.5 0 0 1 4 19.5
                                v-15
                                A2.5 2.5 0 0 1 6.5 2Z
                            "
                        />
                    </svg>
                `,
        };

        return (
            iconos[tipo]
            ||
            iconos.curso
        );
    }

    const wrapperFiltro = (select) =>
        select
            ? document.querySelector(
                `[data-horario-select="${select.id}"]`
            )
            : null;

    function cerrarFiltro(
        wrapper
    ) {
        wrapper
            ?.classList
            .remove(
                'open'
            );

        wrapper
            ?.querySelector(
                '.horario-custom-trigger'
            )
            ?.setAttribute(
                'aria-expanded',
                'false'
            );
    }

    function cerrarTodosFiltros(
        excepto = null
    ) {
        document
            .querySelectorAll(
                '[data-horario-select]'
            )
            .forEach(
                (wrapper) => {
                    if (
                        wrapper !==
                        excepto
                    ) {
                        cerrarFiltro(
                            wrapper
                        );
                    }
                }
            );
    }

    function renderOpcionesFiltro(
        wrapper,
        busqueda = ''
    ) {
        const select =
            $(
                wrapper
                    .dataset
                    .horarioSelect
            );

        const container =
            wrapper.querySelector(
                '.horario-custom-options'
            );

        if (
            !select
            ||
            !container
        ) {
            return;
        }

        const query =
            normalizarTexto(
                busqueda
            );

        const opciones =
            Array
                .from(
                    select.options
                )
                .filter(
                    (option) =>
                        normalizarTexto(
                            option.textContent
                        ).includes(
                            query
                        )
                );

        if (
            !opciones.length
        ) {
            container.innerHTML =
                `
                    <div
                        class="
                            horario-custom-empty
                        "
                    >
                        No se encontraron resultados
                    </div>
                `;

            return;
        }

        container.innerHTML =
            opciones
                .map(
                    (option) =>
                        `
                            <button
                                type="button"
                                class="
                                    horario-custom-option
                                    ${
                                        String(
                                            select.value
                                        ) ===
                                        String(
                                            option.value
                                        )
                                            ? 'selected'
                                            : ''
                                    }
                                "
                                data-value="${esc(
                                    option.value
                                )}"
                            >
                                <span
                                    class="
                                        horario-option-icon
                                    "
                                >
                                    ${iconoFiltro(
                                        wrapper
                                            .dataset
                                            .icon
                                    )}
                                </span>

                                <span
                                    class="
                                        horario-option-text
                                    "
                                >
                                    ${esc(
                                        option.textContent
                                    )}
                                </span>

                                <svg
                                    class="
                                        horario-option-check
                                    "
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.2"
                                >
                                    <path
                                        d="m5 12 4 4L19 6"
                                    />
                                </svg>
                            </button>
                        `
                )
                .join(
                    ''
                );

        container
            .querySelectorAll(
                '.horario-custom-option'
            )
            .forEach(
                (button) => {
                    button.addEventListener(
                        'click',
                        () => {
                            select.value =
                                button
                                    .dataset
                                    .value;

                            select.dispatchEvent(
                                new Event(
                                    'change',
                                    {
                                        bubbles:
                                            true,
                                    }
                                )
                            );

                            cerrarFiltro(
                                wrapper
                            );
                        }
                    );
                }
            );
    }

    function actualizarCustomFiltro(
        select
    ) {
        if (!select) {
            return;
        }

        const wrapper =
            wrapperFiltro(
                select
            );

        if (!wrapper) {
            return;
        }

        const trigger =
            wrapper.querySelector(
                '.horario-custom-trigger'
            );

        const texto =
            wrapper.querySelector(
                '.horario-custom-text'
            );

        if (
            !trigger
            ||
            !texto
        ) {
            return;
        }

        trigger.disabled =
            select.disabled;

        texto.textContent =
            select.options[
                select.selectedIndex
            ]?.textContent
            ||
            wrapper
                .dataset
                .placeholder
            ||
            'Seleccionar';

        renderOpcionesFiltro(
            wrapper
        );
    }

    function construirCustomFiltro(
        wrapper
    ) {
        if (
            !wrapper
            ||
            wrapper.dataset.ready ===
            'true'
        ) {
            return;
        }

        const select =
            $(
                wrapper
                    .dataset
                    .horarioSelect
            );

        if (!select) {
            return;
        }

        const label =
            wrapper
                .dataset
                .label
            ||
            'Filtro';

        const placeholder =
            wrapper
                .dataset
                .placeholder
            ||
            'Seleccionar';

        const icono =
            wrapper
                .dataset
                .icon
            ||
            'curso';

        wrapper.innerHTML =
            `
                <button
                    type="button"
                    class="
                        horario-custom-trigger
                    "
                    aria-expanded="false"
                >
                    <span
                        class="
                            horario-custom-icon
                        "
                    >
                        ${iconoFiltro(
                            icono
                        )}
                    </span>

                    <span
                        class="
                            horario-custom-content
                        "
                    >
                        <span
                            class="
                                horario-custom-small
                            "
                        >
                            ${esc(
                                label
                            )}
                        </span>

                        <span
                            class="
                                horario-custom-text
                            "
                        >
                            ${esc(
                                placeholder
                            )}
                        </span>
                    </span>

                    <svg
                        class="
                            horario-custom-arrow
                        "
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="m6 9 6 6 6-6"
                        />
                    </svg>
                </button>

                <div
                    class="
                        horario-custom-menu
                    "
                >
                    <div
                        class="
                            horario-custom-search-wrap
                        "
                    >
                        <svg
                            class="
                                horario-custom-search-icon
                            "
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <circle
                                cx="11"
                                cy="11"
                                r="7"
                            />

                            <path
                                d="m20 20-3.5-3.5"
                            />
                        </svg>

                        <input
                            type="text"
                            class="
                                horario-custom-search
                            "
                            autocomplete="off"
                            placeholder="Buscar..."
                        >
                    </div>

                    <div
                        class="
                            horario-custom-options
                        "
                    ></div>
                </div>
            `;

        wrapper.dataset.ready =
            'true';

        const trigger =
            wrapper.querySelector(
                '.horario-custom-trigger'
            );

        const buscador =
            wrapper.querySelector(
                '.horario-custom-search'
            );

        trigger.addEventListener(
            'click',
            () => {
                if (
                    trigger.disabled
                ) {
                    return;
                }

                cerrarTodosFiltros(
                    wrapper
                );

                wrapper
                    .classList
                    .toggle(
                        'open'
                    );

                trigger.setAttribute(
                    'aria-expanded',

                    wrapper
                        .classList
                        .contains(
                            'open'
                        )
                        ? 'true'
                        : 'false'
                );

                if (
                    wrapper
                        .classList
                        .contains(
                            'open'
                        )
                ) {
                    buscador.value =
                        '';

                    renderOpcionesFiltro(
                        wrapper
                    );

                    setTimeout(
                        () => {
                            buscador.focus();
                        },
                        30
                    );
                }
            }
        );

        buscador.addEventListener(
            'input',
            () => {
                renderOpcionesFiltro(
                    wrapper,
                    buscador.value
                );
            }
        );

        buscador.addEventListener(
            'keydown',
            (event) => {
                if (
                    event.key ===
                    'Escape'
                ) {
                    cerrarFiltro(
                        wrapper
                    );

                    trigger.focus();
                }
            }
        );

        actualizarCustomFiltro(
            select
        );
    }

    const actualizarTodosCustomFiltros = () =>
        [
            filtroProfesor,
            filtroAula,
            filtroGrado,
            filtroCurso,
        ]
            .filter(
                Boolean
            )
            .forEach(
                actualizarCustomFiltro
            );

    document
        .querySelectorAll(
            '[data-horario-select]'
        )
        .forEach(
            construirCustomFiltro
        );

    document.addEventListener(
        'click',
        (event) => {
            if (
                !event.target.closest(
                    '[data-horario-select]'
                )
            ) {
                cerrarTodosFiltros();
            }
        }
    );

    // =========================================================
    // NORMALIZAR HORARIO
    // =========================================================

    function normalizarHorario(
        horario
    ) {
        return {
            ...horario,

            dia_numero:
                diaANumero(
                    horario.dia_semana
                ),

            hora_inicio:
                horaCorta(
                    horario.hora_inicio
                ),

            hora_fin:
                horaCorta(
                    horario.hora_fin
                ),
        };
    }

    // =========================================================
    // CARGAR DATOS
    // =========================================================

    async function cargarDatos({
        silencioso = false,
    } = {}) {
        if (
            cargando
        ) {
            return;
        }

        cargando =
            true;

        try {
            const [
                horarios,
                grados,
                disponibilidades,
                configuraciones,
            ] =
                await Promise.all([
                    cargarTodasLasPaginas(
                        API.horarios,
                        {
                            institucion:
                                institucionActiva,
                        }
                    ),

                    cargarTodasLasPaginas(
                        API.grados,
                        {
                            activo:
                                1,
                        }
                    )
                        .catch(
                            () => []
                        ),

                    cargarTodasLasPaginas(
                        API.disponibilidades,
                        {
                            institucion:
                                institucionActiva,
                        }
                    )
                        .catch(
                            () => []
                        ),

                    cargarTodasLasPaginas(
                        API.configuraciones,
                        {
                            institucion:
                                institucionActiva,
                        }
                    )
                        .catch(
                            () => []
                        ),
                ]);

            horariosCache =
                horarios.map(
                    normalizarHorario
                );

            gradosCache =
                grados;

            disponibilidadesCache =
                disponibilidades;

            configuracionesHorarioCache =
                configuraciones;

            render();
        } catch (error) {
            console.error(
                'Error cargando horarios:',
                error
            );

            horariosCache =
                [];

            gradosCache =
                [];

            configuracionesHorarioCache =
                [];

            if (
                !silencioso
            ) {
                mostrarMensaje(
                    obtenerMensajeError(
                        error,
                        'No se pudieron cargar los horarios.'
                    ),
                    'error'
                );
            }

            render();
        } finally {
            cargando =
                false;
        }
    }

    const buscarHorario = (id) =>
        horariosCache.find(
            (horario) =>
                String(
                    horario.id
                ) ===
                String(
                    id
                )
        );

    const horarioEsDeInstitucion = (
        horario
    ) =>
        normalizarInstitucion(
            horario.institucion
        ) ===
        institucionActiva;

    function gradoEsDeInstitucionActiva(
        grado
    ) {
        if (
            grado?.activo === false
            ||
            Number(
                grado?.activo
            ) === 0
        ) {
            return false;
        }

        const nivel =
            normalizarTexto(
                grado?.nivel
            );

        if (
            institucionActiva ===
            'academia'
        ) {
            return nivel === 'academia';
        }

        return [
            'primaria',
            'secundaria',
        ].includes(
            nivel
        );
    }

    // =========================================================
    // FILTROS
    // =========================================================

    function poblarSelect(
        select,
        opciones,
        textoDefault
    ) {
        if (!select) {
            return;
        }

        const actual =
            select.value
            ||
            'todos';

        select.innerHTML =
            `
                <option
                    value="todos"
                >
                    ${esc(
                        textoDefault
                    )}
                </option>
            `;

        opciones
            .sort(
                (a, b) =>
                    String(
                        a.texto
                    ).localeCompare(
                        String(
                            b.texto
                        ),
                        'es',
                        {
                            numeric:
                                true,
                        }
                    )
            )
            .forEach(
                (opcion) => {
                    const option =
                        document.createElement(
                            'option'
                        );

                    option.value =
                        String(
                            opcion.id
                        );

                    option.textContent =
                        opcion.texto;

                    select.appendChild(
                        option
                    );
                }
            );

        select.value =
            Array
                .from(
                    select.options
                )
                .some(
                    (option) =>
                        option.value ===
                        actual
                )
                ? actual
                : 'todos';

        actualizarCustomFiltro(
            select
        );
    }

    function cargarFiltros(
        bloques
    ) {
        const profesores =
            new Map();

        const aulas =
            new Map();

        const grados =
            new Map();

        const cursos =
            new Map();

        gradosCache
            .filter(
                gradoEsDeInstitucionActiva
            )
            .forEach(
                (grado) => {
                    grados.set(
                        String(
                            grado.id
                        ),
                        {
                            id:
                                grado.id,

                            texto:
                                nombreGrado(
                                    grado
                                ),
                        }
                    );
                }
            );

        bloques.forEach(
            (bloque) => {
                if (
                    bloque.profesor?.id !=
                    null
                ) {
                    profesores.set(
                        String(
                            bloque.profesor.id
                        ),
                        {
                            id:
                                bloque.profesor.id,

                            texto:
                                nombreProfesor(
                                    bloque.profesor
                                ),
                        }
                    );
                }

                if (
                    bloque.aula?.id !=
                    null
                ) {
                    aulas.set(
                        String(
                            bloque.aula.id
                        ),
                        {
                            id:
                                bloque.aula.id,

                            texto:
                                nombreAula(
                                    bloque.aula
                                ),
                        }
                    );
                }

                if (
                    bloque.grado?.id !=
                    null
                ) {
                    grados.set(
                        String(
                            bloque.grado.id
                        ),
                        {
                            id:
                                bloque.grado.id,

                            texto:
                                nombreGrado(
                                    bloque.grado
                                ),
                        }
                    );
                }

                if (
                    bloque.curso?.id !=
                    null
                ) {
                    cursos.set(
                        String(
                            bloque.curso.id
                        ),
                        {
                            id:
                                bloque.curso.id,

                            texto:
                                nombreCurso(
                                    bloque.curso
                                ),
                        }
                    );
                }
            }
        );

        poblarSelect(
            filtroProfesor,
            [
                ...profesores.values(),
            ],
            'Todos los profesores'
        );

        poblarSelect(
            filtroAula,
            [
                ...aulas.values(),
            ],
            'Todas las aulas'
        );

        poblarSelect(
            filtroGrado,
            [
                ...grados.values(),
            ],
            'Todos los grados'
        );

        poblarSelect(
            filtroCurso,
            [
                ...cursos.values(),
            ],
            'Todos los cursos'
        );

        actualizarTodosCustomFiltros();
    }

    function aplicarFiltros(
        bloques
    ) {
        return bloques.filter(
            (bloque) => {
                if (
                    filtroProfesor?.value !==
                    'todos'
                    &&
                    String(
                        bloque.profesor_id
                    ) !==
                    filtroProfesor.value
                ) {
                    return false;
                }

                if (
                    filtroAula?.value !==
                    'todos'
                    &&
                    String(
                        bloque.aula_id
                    ) !==
                    filtroAula.value
                ) {
                    return false;
                }

                if (
                    filtroGrado?.value !==
                    'todos'
                    &&
                    String(
                        bloque.grado_id
                    ) !==
                    filtroGrado.value
                ) {
                    return false;
                }

                if (
                    filtroCurso?.value !==
                    'todos'
                    &&
                    String(
                        bloque.curso_id
                    ) !==
                    filtroCurso.value
                ) {
                    return false;
                }

                return true;
            }
        );
    }

    function limpiarFiltros() {
        [
            filtroProfesor,
            filtroAula,
            filtroGrado,
            filtroCurso,
        ]
            .filter(
                Boolean
            )
            .forEach(
                (filtro) => {
                    filtro.value =
                        'todos';
                }
            );

        actualizarTodosCustomFiltros();
    }

    const obtenerBloquesVisibles = () =>
        aplicarFiltros(
            horariosCache.filter(
                horarioEsDeInstitucion
            )
        );

    // =========================================================
    // DISPONIBILIDAD
    // =========================================================

    function profesorDisponible(
        bloque,
        dia,
        horaInicio,
        horaFin
    ) {
        if (
            !disponibilidadesCache.length
        ) {
            return true;
        }

        const diaApi =
            diaATextoApi(
                dia
            );

        const rangos =
            disponibilidadesCache.filter(
                (item) => {
                    const profesorId =
                        item.profesor_id
                        ??
                        item.profesorId;

                    const institucion =
                        normalizarInstitucion(
                            item.institucion
                        );

                    const diaItem =
                        diaATextoApi(
                            item.dia_semana
                            ??
                            item.dia
                        );

                    const tipo =
                        normalizarTexto(
                            item.tipo
                            ||
                            'disponible'
                        );

                    const activo =
                        item.activo ===
                        undefined
                            ? true
                            : Boolean(
                                item.activo
                            );

                    return (
                        String(
                            profesorId
                        ) ===
                        String(
                            bloque.profesor_id
                        )
                        &&
                        institucion ===
                        normalizarInstitucion(
                            bloque.institucion
                        )
                        &&
                        diaItem ===
                        diaApi
                        &&
                        tipo !==
                        'no_disponible'
                        &&
                        activo
                    );
                }
            );

        if (
            !rangos.length
        ) {
            return false;
        }

        return rangos.some(
            (rango) => {
                const inicio =
                    horaCorta(
                        rango.hora_inicio
                        ??
                        rango.horaInicio
                    );

                const fin =
                    horaCorta(
                        rango.hora_fin
                        ??
                        rango.horaFin
                    );

                return (
                    inicio
                    &&
                    fin
                    &&
                    aMinutos(
                        horaInicio
                    ) >=
                    aMinutos(
                        inicio
                    )
                    &&
                    aMinutos(
                        horaFin
                    ) <=
                    aMinutos(
                        fin
                    )
                );
            }
        );
    }

    function nivelHorario(
        bloque
    ) {
        const nivel =
            bloque.grado?.nivel
            ||
            bloque.nivel
            ||
            bloque.curso?.nivel
            ||
            (
                normalizarInstitucion(
                    bloque.institucion
                ) ===
                'academia'
                    ? 'academia'
                    : ''
            );

        return normalizarTexto(
            nivel
        );
    }

    function turnoHorario(
        bloque
    ) {
        return normalizarTexto(
            bloque.turno
            ||
            determinarTurno(
                bloque.hora_inicio
            )
        );
    }

    function configuracionParaHorario(
        bloque
    ) {
        const institucion =
            normalizarInstitucion(
                bloque.institucion
                ||
                institucionActiva
            );

        const nivel =
            nivelHorario(
                bloque
            );

        const turno =
            turnoHorario(
                bloque
            );

        return configuracionesHorarioCache
            .filter(
                (config) => {
                    const configInstitucion =
                        normalizarInstitucion(
                            config.institucion
                        );

                    const configNivel =
                        normalizarTexto(
                            config.nivel
                        );

                    const configTurno =
                        normalizarTexto(
                            config.turno
                        );

                    return (
                        configInstitucion ===
                        institucion
                        &&
                        (
                            !nivel
                            ||
                            configNivel ===
                            nivel
                            ||
                            configNivel ===
                            'todos'
                        )
                        &&
                        (
                            configTurno ===
                            turno
                            ||
                            configTurno ===
                            'completo'
                        )
                    );
                }
            )
            .sort(
                (a, b) =>
                    Number(
                        b.año_academico
                        ||
                        0
                    )
                    -
                    Number(
                        a.año_academico
                        ||
                        0
                    )
            )[0]
            ||
            null;
    }

    function recesosParaBloque(
        bloque
    ) {
        const config =
            configuracionParaHorario(
                bloque
            );

        return (
            config?.bloques
            ||
            []
        )
            .filter(
                (item) =>
                    item.tipo ===
                    'receso'
            )
            .map(
                (item) => ({
                    nombre:
                        item.nombre
                        ||
                        'Receso',

                    hora_inicio:
                        horaCorta(
                            item.hora_inicio
                        ),

                    hora_fin:
                        horaCorta(
                            item.hora_fin
                        ),
                })
            )
            .filter(
                (item) =>
                    item.hora_inicio
                    &&
                    item.hora_fin
            );
    }

    function recesosParaGrupo(
        grupo
    ) {
        const mapa =
            new Map();

        grupo.bloques.forEach(
            (bloque) => {
                recesosParaBloque(
                    bloque
                ).forEach(
                    (receso) => {
                        const clave =
                            `${receso.hora_inicio}-${receso.hora_fin}-${receso.nombre}`;

                        mapa.set(
                            clave,
                            receso
                        );
                    }
                );
            }
        );

        return [
            ...mapa.values(),
        ].sort(
            (a, b) =>
                aMinutos(
                    a.hora_inicio
                )
                -
                aMinutos(
                    b.hora_inicio
                )
        );
    }

    function horarioChocaConReceso(
        bloque,
        horaInicio,
        horaFin
    ) {
        return recesosParaBloque(
            bloque
        ).find(
            (receso) =>
                existeTraslape(
                    horaInicio,
                    horaFin,
                    receso.hora_inicio,
                    receso.hora_fin
                )
        );
    }

    // =========================================================
    // VALIDAR MOVIMIENTO
    // =========================================================

    function validarMovimiento(
        bloque,
        dia,
        horaInicio,
        horaFin
    ) {
        const datos =
            datosClase(
                bloque
            );

        if (
            aMinutos(
                horaFin
            ) <=
            aMinutos(
                horaInicio
            )
        ) {
            return {
                valido:
                    false,

                mensaje:
                    'La hora de fin debe ser posterior a la hora de inicio.',
            };
        }

        if (
            aMinutos(
                horaInicio
            ) <
            TIMELINE.inicio
            ||
            aMinutos(
                horaFin
            ) >
            TIMELINE.finMaximo
        ) {
            return {
                valido:
                    false,

                mensaje:
                    'La clase debe mantenerse entre las 07:00 y las 20:00.',
            };
        }

        if (
            !profesorDisponible(
                bloque,
                dia,
                horaInicio,
                horaFin
            )
        ) {
            return {
                valido:
                    false,

                mensaje:
                    `${datos.profesor} no está disponible el ${diaATexto(
                        dia
                    )} de ${horaInicio} a ${horaFin}.`,
            };
        }

        const receso =
            horarioChocaConReceso(
                bloque,
                horaInicio,
                horaFin
            );

        if (
            receso
        ) {
            return {
                valido:
                    false,

                mensaje:
                    `No se puede poner una clase durante el receso (${receso.hora_inicio} - ${receso.hora_fin}).`,
            };
        }

        for (
            const otro of
            horariosCache
        ) {
            if (
                String(
                    otro.id
                ) ===
                String(
                    bloque.id
                )
            ) {
                continue;
            }

            if (
                normalizarInstitucion(
                    otro.institucion
                ) !==
                normalizarInstitucion(
                    bloque.institucion
                )
            ) {
                continue;
            }

            if (
                diaANumero(
                    otro.dia_semana
                ) !==
                Number(
                    dia
                )
            ) {
                continue;
            }

            if (
                !existeTraslape(
                    horaInicio,
                    horaFin,
                    otro.hora_inicio,
                    otro.hora_fin
                )
            ) {
                continue;
            }

            const datosOtro =
                datosClase(
                    otro
                );

            if (
                String(
                    otro.profesor_id
                ) ===
                String(
                    bloque.profesor_id
                )
            ) {
                return {
                    valido:
                        false,

                    mensaje:
                        `${datos.profesor} ya tiene ${datosOtro.curso} de ${otro.hora_inicio} a ${otro.hora_fin}.`,
                };
            }

            if (
                bloque.aula_id
                &&
                String(
                    otro.aula_id
                ) ===
                String(
                    bloque.aula_id
                )
            ) {
                return {
                    valido:
                        false,

                    mensaje:
                        `${datos.aula} ya está ocupada por ${datosOtro.curso} de ${otro.hora_inicio} a ${otro.hora_fin}.`,
                };
            }

            if (
                bloque.grado_id
                &&
                String(
                    otro.grado_id
                ) ===
                String(
                    bloque.grado_id
                )
            ) {
                return {
                    valido:
                        false,

                    mensaje:
                        `${datos.grado} ya tiene ${datosOtro.curso} de ${otro.hora_inicio} a ${otro.hora_fin}.`,
                };
            }
        }

        return {
            valido:
                true,

            mensaje:
                'Horario disponible.',
        };
    }

    // =========================================================
    // CONFLICTOS
    // =========================================================

    function tienenConflicto(
        bloqueA,
        bloqueB
    ) {
        if (
            diaANumero(
                bloqueA.dia_semana
            ) !==
            diaANumero(
                bloqueB.dia_semana
            )
        ) {
            return false;
        }

        if (
            !existeTraslape(
                bloqueA.hora_inicio,
                bloqueA.hora_fin,
                bloqueB.hora_inicio,
                bloqueB.hora_fin
            )
        ) {
            return false;
        }

        const mismoProfesor =
            String(
                bloqueA.profesor_id
            ) ===
            String(
                bloqueB.profesor_id
            );

        const mismaAula =
            bloqueA.aula_id
            &&
            bloqueB.aula_id
            &&
            String(
                bloqueA.aula_id
            ) ===
            String(
                bloqueB.aula_id
            );

        const mismoGrado =
            bloqueA.grado_id
            &&
            bloqueB.grado_id
            &&
            String(
                bloqueA.grado_id
            ) ===
            String(
                bloqueB.grado_id
            );

        return Boolean(
            mismoProfesor
            ||
            mismaAula
            ||
            mismoGrado
        );
    }

    function detectarConflictosExistentes(
        bloques
    ) {
        for (
            let i = 0;
            i < bloques.length;
            i++
        ) {
            for (
                let j = i + 1;
                j < bloques.length;
                j++
            ) {
                if (
                    tienenConflicto(
                        bloques[i],
                        bloques[j]
                    )
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    function actualizarEstadisticas(
        bloques
    ) {
        if (
            statProfesores
        ) {
            statProfesores.textContent =
                new Set(
                    bloques
                        .map(
                            (item) =>
                                item.profesor_id
                        )
                        .filter(
                            Boolean
                        )
                ).size;
        }

        if (
            statAulas
        ) {
            statAulas.textContent =
                new Set(
                    bloques
                        .map(
                            (item) =>
                                item.aula_id
                        )
                        .filter(
                            Boolean
                        )
                ).size;
        }

        if (
            statClases
        ) {
            statClases.textContent =
                bloques.length;
        }

        const conflicto =
            detectarConflictosExistentes(
                bloques
            );

        if (
            statEstado
        ) {
            statEstado.textContent =
                conflicto
                    ? 'Revisar horarios'
                    : 'Sin conflictos';
        }

        if (
            statEstadoPunto
        ) {
            statEstadoPunto.style.background =
                conflicto
                    ? '#F59E0B'
                    : '#10B981';
        }
    }

    // =========================================================
    // AGRUPAR
    // =========================================================

    const getAgrupaPor = () =>
        vistaActual ===
        'aula-completa'
            ? 'aula'
            : vistaActual;

    function agruparBloques(
        bloques
    ) {
        const grupos =
            new Map();

        const tipo =
            getAgrupaPor();

        bloques.forEach(
            (bloque) => {
                let id;
                let nombre;
                let clave;

                if (
                    tipo ===
                    'profesor'
                ) {
                    id =
                        bloque.profesor_id;

                    nombre =
                        nombreProfesor(
                            bloque.profesor
                        );

                    clave =
                        `prof-${id}`;
                } else if (
                    tipo ===
                    'aula'
                ) {
                    id =
                        bloque.aula_id;

                    nombre =
                        nombreAula(
                            bloque.aula
                        );

                    clave =
                        `aula-${id}`;
                } else {
                    id =
                        bloque.grado_id;

                    nombre =
                        nombreGrado(
                            bloque.grado
                        );

                    clave =
                        `grado-${id}`;
                }

                if (!id) {
                    return;
                }

                if (
                    !grupos.has(
                        clave
                    )
                ) {
                    grupos.set(
                        clave,
                        {
                            clave,
                            id,
                            tipo,
                            nombre,
                            bloques: [],
                        }
                    );
                }

                grupos
                    .get(
                        clave
                    )
                    .bloques
                    .push(
                        bloque
                    );
            }
        );

        return [
            ...grupos.values(),
        ];
    }

    // =========================================================
    // TÍTULO SEGÚN VISTA
    // =========================================================

    function actualizarTituloVista() {
        const configuracion = {
            profesor: [
                'Horario por profesor',
                'Consulta las clases asignadas a cada profesor.',
            ],

            aula: [
                'Horario por aula',
                'Consulta las clases programadas en cada aula.',
            ],

            'aula-completa': [
                'Horario general por aulas',
                'Vista semanal según la hora real de cada clase.',
            ],

            grado: [
                'Horario por grado',
                'Consulta las clases correspondientes a cada grado o sección.',
            ],
        };

        const actual =
            configuracion[
                vistaActual
            ]
            ||
            configuracion.profesor;

        if (
            tituloEl
        ) {
            tituloEl.textContent =
                actual[0];
        }

        if (
            subtituloEl
        ) {
            subtituloEl.textContent =
                actual[1];
        }
    }

    // =========================================================
    // COLORES
    // =========================================================

    const COLORES = [
        {
            bg:
                '#ECFDF5',

            border:
                '#A7F3D0',

            titulo:
                '#065F46',

            acento:
                '#10B981',
        },

        {
            bg:
                '#EEF2FF',

            border:
                '#C7D2FE',

            titulo:
                '#312E81',

            acento:
                '#4F46E5',
        },

        {
            bg:
                '#EFF6FF',

            border:
                '#BFDBFE',

            titulo:
                '#1E3A8A',

            acento:
                '#2563EB',
        },

        {
            bg:
                '#FFF7ED',

            border:
                '#FED7AA',

            titulo:
                '#9A3412',

            acento:
                '#EA580C',
        },

        {
            bg:
                '#F5F3FF',

            border:
                '#DDD6FE',

            titulo:
                '#5B21B6',

            acento:
                '#7C3AED',
        },
    ];

    const cacheColor =
        new Map();

    function colorCurso(
        nombre
    ) {
        const key =
            nombre
            ||
            'Curso';

        if (
            !cacheColor.has(
                key
            )
        ) {
            cacheColor.set(
                key,

                COLORES[
                    cacheColor.size
                    %
                    COLORES.length
                ]
            );
        }

        return (
            cacheColor.get(
                key
            )
        );
    }

    // =========================================================
    // ESTILOS DE TIMELINE
    // =========================================================

    function inyectarEstilosTimeline() {
        $(
            'nextlevel-horario-timeline-styles'
        )
            ?.remove();

        const style =
            document.createElement(
                'style'
            );

        style.id =
            'nextlevel-horario-timeline-styles';

        style.textContent =
            `

            /* ================================================
               SCROLL HORIZONTAL
            ================================================= */

            .horario-timeline-scroll{
                width:100%;
                overflow-x:auto;
                overflow-y:hidden;
                scrollbar-width:thin;
                scrollbar-color:#CBD5E1 transparent;
                overscroll-behavior-x:contain;
            }

            .horario-timeline-scroll::-webkit-scrollbar{
                height:9px;
            }

            .horario-timeline-scroll::-webkit-scrollbar-thumb{
                border-radius:999px;
                background:#CBD5E1;
            }

            .horario-timeline-scroll::-webkit-scrollbar-track{
                background:#F8FAFC;
            }

            /* ================================================
               GRID
            ================================================= */

            .horario-timeline-grid{
                width:100%;
                min-width:max-content;
                background:#FFFFFF;
            }

            .horario-timeline-head,
            .horario-timeline-body{
                display:grid;
            }

            /* ================================================
               CABECERA
            ================================================= */

            .horario-timeline-head{
                position:sticky;
                top:0;
                z-index:35;
                border-bottom:1px solid #DCE4EE;
                background:#F8FAFC;
            }

            .horario-timeline-head-time{
                position:sticky;
                left:0;
                z-index:46;
                display:flex;
                min-height:70px;
                align-items:center;
                justify-content:center;
                border-right:1px solid #DCE4EE;
                background:#F8FAFC;
                color:#64748B;
                font-size:10px;
                font-weight:900;
                letter-spacing:.08em;
                text-transform:uppercase;
            }

            .horario-timeline-head-day{
                display:flex;
                min-height:70px;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                border-right:1px solid #DCE4EE;
                background:#F8FAFC;
                margin:4px 4px 0;
                border:1px solid rgba(148,163,184,.14);
                border-bottom:0;
                border-radius:14px 14px 0 0;
                padding:0 12px;
            }

            .horario-timeline-head-day:last-child{
                border-right:0;
            }

            .horario-timeline-head-day strong{
                color:#0F2749;
                font-size:13px;
                font-weight:900;
                line-height:1.1;
            }

            .horario-timeline-head-day::before{
                content:"▦";
                display:flex;
                width:28px;
                height:28px;
                align-items:center;
                justify-content:center;
                margin-bottom:5px;
                border-radius:9px;
                background:rgba(59,130,246,.12);
                color:#2563EB;
                font-size:17px;
                font-weight:900;
                box-shadow:inset 0 0 0 1px rgba(59,130,246,.08);
            }

            .horario-timeline-head-day:nth-child(3)::before{
                background:rgba(16,185,129,.14);
            }

            .horario-timeline-head-day:nth-child(4)::before{
                background:rgba(245,158,11,.16);
            }

            .horario-timeline-head-day:nth-child(5)::before{
                background:rgba(124,58,237,.14);
            }

            .horario-timeline-head-day:nth-child(6)::before{
                background:rgba(236,72,153,.14);
            }

            .horario-timeline-head-day:nth-child(7)::before{
                background:rgba(59,130,246,.12);
            }

            .horario-timeline-head-day strong,
            .horario-timeline-head-day span{
                position:relative;
                z-index:1;
            }

            .horario-timeline-head-day span{
                margin-top:5px;
                color:#94A3B8;
                font-size:9px;
                font-weight:800;
                letter-spacing:.06em;
            }

            /* ================================================
               COLUMNA HORA
            ================================================= */

            .horario-timeline-axis{
                position:sticky;
                left:0;
                z-index:25;
                border-right:1px solid #DCE4EE;
                background:#FFFFFF;
            }

            .horario-timeline-axis-label{
                position:absolute;
                left:0;
                right:0;
                transform:translateY(-50%);
                padding-right:16px;
                color:#8A99B0;
                font-size:9px;
                font-weight:700;
                text-align:right;
                line-height:1;
                white-space:nowrap;
                pointer-events:none;
            }

            .horario-timeline-axis-label.major{
                color:#0F2749;
                font-size:11px;
                font-weight:900;
            }

            /* ================================================
               COLUMNAS
            ================================================= */

            .horario-timeline-day{
                position:relative;
                min-width:0;
                border-right:1px solid #E2E8F0;
                background:#FFFFFF;
            }

            /*
             * La retícula empieza DESPUÉS del padding.
             * Así 07:00 nunca queda pegado ni oculto.
             */

            .horario-timeline-day::before{
                content:"";
                position:absolute;
                top:${TIMELINE.paddingSuperior}px;
                right:0;
                bottom:${TIMELINE.paddingInferior}px;
                left:0;

                background-image:
                    repeating-linear-gradient(
                        to bottom,
                        transparent 0,
                        transparent calc(
                            var(--timeline-step) - 1px
                        ),
                        #EEF2F7 calc(
                            var(--timeline-step) - 1px
                        ),
                        #EEF2F7 var(--timeline-step)
                    ),

                    repeating-linear-gradient(
                        to bottom,
                        transparent 0,
                        transparent calc(
                            var(--timeline-hour) - 1px
                        ),
                        rgba(15,39,73,.10) calc(
                            var(--timeline-hour) - 1px
                        ),
                        rgba(15,39,73,.10) var(--timeline-hour)
                    );

                pointer-events:none;
            }

            .horario-timeline-day:last-child{
                border-right:0;
            }

            .horario-timeline-day.drag-over{
                background-color:rgba(27,58,107,.025);
                outline:2px dashed rgba(27,58,107,.45);
                outline-offset:-3px;
            }

            .horario-timeline-day.drag-invalid{
                background-color:rgba(219,8,8,.035);
                outline-color:rgba(219,8,8,.65);
            }

            /* ================================================
               SIN CLASES
            ================================================= */

            .horario-timeline-empty{
                position:absolute;
                top:${TIMELINE.paddingSuperior + 18}px;
                left:50%;
                transform:translateX(-50%);
                color:#CBD5E1;
                font-size:9px;
                font-weight:900;
                letter-spacing:.08em;
                text-transform:uppercase;
                white-space:nowrap;
                pointer-events:none;
            }

            .horario-timeline-receso{
                position:absolute;
                left:8px;
                right:8px;
                z-index:4;
                display:flex;
                align-items:center;
                justify-content:center;
                border:1px dashed #FCA5A5;
                border-radius:12px;
                background:rgba(254,226,226,.74);
                color:#B91C1C;
                font-size:10px;
                font-weight:900;
                letter-spacing:.02em;
                text-transform:uppercase;
                pointer-events:none;
                box-shadow:0 3px 10px rgba(185,28,28,.05);
            }

            .horario-timeline-receso::before{
                content:"☕";
                display:inline-flex;
                width:24px;
                height:24px;
                align-items:center;
                justify-content:center;
                margin-right:7px;
                border-radius:8px;
                background:rgba(255,255,255,.58);
                font-size:13px;
                text-transform:none;
            }

            /* ================================================
               POSICIÓN CLASE
            ================================================= */

            .horario-timeline-item{
                position:absolute;
                z-index:8;
                min-width:0;
                padding:0 4px;
            }

            .horario-timeline-item:hover{
                z-index:30;
            }

            /* ================================================
               CARD
            ================================================= */

            .horario-clase-timeline{
                position:relative;
                display:flex;
                width:100%;
                height:100%;
                overflow:hidden;
                border:1px solid;
                border-radius:14px;

                box-shadow:
                    0 4px 14px
                    rgba(15,39,73,.08);

                transition:
                    transform .15s ease,
                    box-shadow .15s ease;
            }

            .horario-clase-timeline:hover{
                transform:translateY(-1px);

                box-shadow:
                    0 10px 24px
                    rgba(15,39,73,.14);
            }

            .horario-card-accent{
                width:5px;
                flex:0 0 5px;
            }

            .horario-card-content{
                display:flex;
                min-width:0;
                flex:1;
                flex-direction:column;
                justify-content:flex-start;
                gap:3px;

                padding:
                    11px
                    43px
                    10px
                    42px;
            }

            .horario-card-symbol{
                position:absolute;
                top:10px;
                left:14px;
                display:flex;
                width:25px;
                height:25px;
                align-items:center;
                justify-content:center;
                border-radius:8px;
                background:rgba(255,255,255,.62);
                color:inherit;
            }

            .horario-card-symbol svg{
                width:15px;
                height:15px;
            }

            .horario-grupo-cabecera{
                display:flex;
                flex-wrap:wrap;
                min-width:0;
                align-items:center;
                gap:14px;
            }

            .horario-grupo-icono{
                display:flex;
                width:52px;
                height:52px;
                flex:0 0 52px;
                align-items:center;
                justify-content:center;
                border-radius:16px;
                background:linear-gradient(145deg,#6366F1,#4F46E5);
                color:#FFFFFF;
                box-shadow:0 8px 18px rgba(79,70,229,.22);
            }

            .horario-grupo-icono svg{
                width:27px;
                height:27px;
            }

            .horario-grupo-titulo{
                min-width:0;
            }

            .horario-grupo-titulo h3{
                overflow:hidden;
                color:#0F2749;
                font-size:20px;
                font-weight:900;
                line-height:1.15;
                text-overflow:ellipsis;
                white-space:nowrap;
            }

            .horario-grupo-titulo p{
                margin-top:5px;
                color:#7186A5;
                font-size:13px;
                font-weight:700;
            }

            .horario-timeline-head-time{
                gap:8px;
                color:#0F2749;
                font-size:12px;
                letter-spacing:0;
            }

            .horario-timeline-head-time::before{
                content:"◷";
                display:flex;
                width:28px;
                height:28px;
                align-items:center;
                justify-content:center;
                border-radius:9px;
                background:#EFF6FF;
                color:#1D4ED8;
                font-size:19px;
                font-weight:700;
            }

            .horario-card-title{
                color:inherit;
                font-size:11px;
                font-weight:900;
                line-height:1.18;
                overflow:hidden;

                display:-webkit-box;
                -webkit-box-orient:vertical;
                -webkit-line-clamp:2;

                word-break:normal;
            }

            .horario-card-secondary{
                color:inherit;
                font-size:9px;
                font-weight:800;
                line-height:1.15;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap;
            }

            .horario-card-third{
                color:#64748B;
                font-size:8.5px;
                font-weight:650;
                line-height:1.15;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap;
            }

            .horario-card-time{
                display:inline-flex;
                width:max-content;
                max-width:100%;
                align-items:center;
                justify-content:center;

                margin-top:auto;

                border:
                    1px solid
                    rgba(148,163,184,.28);

                border-radius:8px;

                background:
                    rgba(255,255,255,.96);

                padding:
                    4px
                    8px;

                color:#0F2749;
                font-size:8.5px;
                font-weight:900;
                line-height:1;
                white-space:nowrap;

                box-shadow:
                    0 2px 5px
                    rgba(15,39,73,.04);
            }

            /* ================================================
               ELIMINAR
            ================================================= */

            .horario-clase-timeline
            .btn-eliminar-horario{
                position:absolute;
                top:8px;
                right:8px;

                display:flex;

                width:30px;
                height:30px;

                align-items:center;
                justify-content:center;

                border:1px solid #FECACA;
                border-radius:9px;

                background:
                    rgba(255,255,255,.97);

                color:#DB0808;

                box-shadow:
                    0 3px 8px
                    rgba(219,8,8,.08);

                transition:
                    background .15s ease,
                    transform .15s ease;
            }

            .horario-clase-timeline
            .btn-eliminar-horario:hover{
                background:#FFF1F2;
                transform:scale(1.04);
            }

            /* ================================================
               CLASES MUY CORTAS
            ================================================= */

            .horario-clase-timeline.compacta
            .horario-card-content{
                gap:2px;
                padding-top:8px;
                padding-bottom:7px;
            }

            .horario-clase-timeline.muy-compacta
            .horario-card-third{
                display:none;
            }

            .horario-clase-timeline.muy-compacta
            .horario-card-time{
                padding:
                    3px
                    6px;

                font-size:8px;
            }

            /* ================================================
               DRAG
            ================================================= */

            .horario-clase.dragging{
                opacity:.42;
            }

            .horario-drop-preview{
                position:absolute;
                z-index:50;

                left:5px;
                right:5px;

                height:3px;

                border-radius:999px;
                background:#10B981;

                pointer-events:none;
            }

            .horario-drop-preview.invalid{
                background:#DB0808;
            }

            .horario-drop-preview-label{
                position:absolute;
                top:-14px;
                right:2px;

                border-radius:999px;
                background:#0F2749;

                padding:
                    4px
                    8px;

                color:#FFFFFF;

                font-size:8px;
                font-weight:900;

                white-space:nowrap;

                box-shadow:
                    0 3px 10px
                    rgba(15,39,73,.18);
            }

            .horario-drop-preview.invalid
            .horario-drop-preview-label{
                background:#DB0808;
            }

            @media(
                max-width:900px
            ){
                .horario-timeline-head-time,
                .horario-timeline-axis{
                    min-width:82px;
                }
            }
        `;

        document.head.appendChild(
            style
        );
    }

    // =========================================================
    // RANGO GLOBAL
    // =========================================================

    function obtenerFinTimelineGlobal() {
        const finales =
            obtenerBloquesVisibles()
                .map(
                    (bloque) =>
                        aMinutos(
                            bloque.hora_fin
                        )
                )
                .filter(
                    (minuto) =>
                        minuto >
                        0
                );

        const ultimaClase =
            finales.length
                ? Math.max(
                    ...finales
                )
                : TIMELINE.finMinimo;

        let hasta =
            Math.max(
                TIMELINE.finMinimo,
                ultimaClase
            );

        /*
         * Si hay una clase después del mínimo visible,
         * dejamos media hora adicional.
         */
        if (
            ultimaClase >
            TIMELINE.finMinimo
        ) {
            hasta =
                ultimaClase +
                30;
        }

        return Math.min(
            TIMELINE.finMaximo,

            redondearArriba(
                hasta,
                30
            )
        );
    }

    function rangoTimeline() {
        /*
         * MUY IMPORTANTE:
         *
         * Este rango es GLOBAL.
         *
         * Todos:
         * - profesores
         * - aulas
         * - grados
         *
         * usan exactamente la misma escala.
         */

        const desde =
            TIMELINE.inicio;

        const hasta =
            obtenerFinTimelineGlobal();

        const altoUtil =
            (
                hasta -
                desde
            )
            *
            TIMELINE.pxPorMinuto;

        return {
            desde,
            hasta,

            paddingSuperior:
                TIMELINE.paddingSuperior,

            paddingInferior:
                TIMELINE.paddingInferior,

            altoUtil,

            alto:
                altoUtil
                +
                TIMELINE.paddingSuperior
                +
                TIMELINE.paddingInferior,
        };
    }

    // =========================================================
    // DATOS CARD
    // =========================================================

    function datosTarjeta(
        bloque
    ) {
        const datos =
            datosClase(
                bloque
            );

        if (
            getAgrupaPor() ===
            'profesor'
        ) {
            return {
                principal:
                    datos.curso,

                secundario:
                    datos.aula,

                tercero:
                    datos.grado,
            };
        }

        if (
            getAgrupaPor() ===
            'aula'
        ) {
            return {
                principal:
                    datos.curso,

                secundario:
                    datos.profesor,

                tercero:
                    datos.grado,
            };
        }

        return {
            principal:
                datos.curso,

            secundario:
                datos.profesor,

            tercero:
                datos.aula,
        };
    }

    // =========================================================
    // SUPERPOSICIONES
    // =========================================================

    function calcularDistribucionDia(
        bloques
    ) {
        const elementos = [
            ...bloques,
        ]
            .map(
                (bloque) => ({
                    bloque,

                    inicio:
                        aMinutos(
                            bloque.hora_inicio
                        ),

                    fin:
                        aMinutos(
                            bloque.hora_fin
                        ),

                    lane:
                        0,

                    totalLanes:
                        1,
                })
            )
            .filter(
                (item) =>
                    item.inicio >
                    0
                    &&
                    item.fin >
                    item.inicio
            )
            .sort(
                (a, b) =>
                    a.inicio -
                    b.inicio
                    ||
                    a.fin -
                    b.fin
            );

        let grupoActual =
            [];

        let finalGrupo =
            -1;

        const cerrarGrupo = () => {
            if (
                !grupoActual.length
            ) {
                return;
            }

            const total =
                Math.max(
                    1,

                    ...grupoActual.map(
                        (item) =>
                            item.lane +
                            1
                    )
                );

            grupoActual.forEach(
                (item) => {
                    item.totalLanes =
                        total;
                }
            );

            grupoActual =
                [];

            finalGrupo =
                -1;
        };

        elementos.forEach(
            (item) => {
                if (
                    grupoActual.length
                    &&
                    item.inicio >=
                    finalGrupo
                ) {
                    cerrarGrupo();
                }

                const ocupados =
                    new Set(
                        grupoActual
                            .filter(
                                (existente) =>
                                    existente.fin >
                                    item.inicio
                            )
                            .map(
                                (existente) =>
                                    existente.lane
                            )
                    );

                let lane =
                    0;

                while (
                    ocupados.has(
                        lane
                    )
                ) {
                    lane++;
                }

                item.lane =
                    lane;

                grupoActual.push(
                    item
                );

                finalGrupo =
                    Math.max(
                        finalGrupo,
                        item.fin
                    );
            }
        );

        cerrarGrupo();

        return elementos;
    }

    function calcularLanesDia(
        grupo,
        diaNumero
    ) {
        const distribucion =
            calcularDistribucionDia(
                grupo.bloques.filter(
                    (bloque) =>
                        diaANumero(
                            bloque.dia_semana
                        ) ===
                        diaNumero
                )
            );

        return distribucion.length
            ? Math.max(
                1,

                ...distribucion.map(
                    (item) =>
                        item.totalLanes
                )
            )
            : 1;
    }

    function anchoDia(
        lanes
    ) {
        if (
            lanes <=
            1
        ) {
            return (
                TIMELINE.anchoDia
            );
        }

        return Math.min(
            TIMELINE.anchoDiaMax,

            TIMELINE.anchoDia
            +
            (
                lanes -
                1
            )
            *
            TIMELINE.anchoCarril
        );
    }

    function plantillaColumnas(
        grupo
    ) {
        const columnas =
            DIAS.map(
                (dia) => {
                    const ancho =
                        anchoDia(
                            calcularLanesDia(
                                grupo,
                                dia.numero
                            )
                        );

                    return `minmax(${ancho}px, 1fr)`;
                }
            );

        return (
            `${TIMELINE.anchoHora}px `
            +
            columnas.join(
                ' '
            )
        );
    }

    // =========================================================
    // CARD DE CLASE
    // =========================================================

    function tarjetaHorario(
        bloque,
        alto
    ) {
        const datos =
            datosTarjeta(
                bloque
            );

        const color =
            colorCurso(
                datos.principal
            );

        const compacta =
            alto <
            82;

        const muyCompacta =
            alto <
            66;

        const eliminar =
            puedeEditarHorario()
                ? `
                    <button
                        type="button"
                        class="
                            btn-eliminar-horario
                        "
                        data-eliminar-horario-id="${esc(
                            bloque.id
                        )}"
                        draggable="false"
                        title="Eliminar clase"
                    >
                        <svg
                            width="13"
                            height="13"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M3 6h18"
                            />

                            <path
                                d="M8 6V4h8v2"
                            />

                            <path
                                d="M19 6l-1 14H6L5 6"
                            />

                            <path
                                d="
                                    M10 11v5
                                    M14 11v5
                                "
                            />
                        </svg>
                    </button>
                `
                : '';

        return `
            <article
                class="
                    horario-clase
                    horario-clase-timeline
                    ${
                        compacta
                            ? 'compacta'
                            : ''
                    }
                    ${
                        muyCompacta
                            ? 'muy-compacta'
                            : ''
                    }
                "
                draggable="${
                    puedeEditarHorario()
                        ? 'true'
                        : 'false'
                }"
                data-horario-id="${esc(
                    bloque.id
                )}"
                style="
                    background:${color.bg};
                    border-color:${color.border};
                    color:${color.titulo};
                "
                title="${esc(
                    `${datos.principal} | ${datos.secundario} | ${datos.tercero} | ${bloque.hora_inicio} - ${bloque.hora_fin}`
                )}"
            >
                <div
                    class="
                        horario-card-accent
                    "
                    style="
                        background:${color.acento};
                    "
                ></div>

                <span
                    class="horario-card-symbol"
                    aria-hidden="true"
                >
                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v15.5A2.5 2.5 0 0 0 17.5 16H4z" />
                        <path d="M4 5.5V19a2 2 0 0 0 2 2h11.5A2.5 2.5 0 0 0 20 18.5" />
                    </svg>
                </span>

                ${eliminar}

                <div
                    class="
                        horario-card-content
                    "
                >
                    <div
                        class="
                            horario-card-title
                        "
                    >
                        ${esc(
                            datos.principal
                        )}
                    </div>

                    <div
                        class="
                            horario-card-secondary
                        "
                        style="
                            color:${color.acento};
                        "
                    >
                        ${esc(
                            datos.secundario
                        )}
                    </div>

                    <div
                        class="
                            horario-card-third
                        "
                    >
                        ${esc(
                            datos.tercero
                        )}
                    </div>

                    <div
                        class="
                            horario-card-time
                        "
                    >
                        ${esc(
                            bloque.hora_inicio
                        )}

                        &nbsp;–&nbsp;

                        ${esc(
                            bloque.hora_fin
                        )}
                    </div>
                </div>
            </article>
        `;
    }

    // =========================================================
    // EJE HORARIO
    // =========================================================

    function crearEtiquetasTiempo(
        rango
    ) {
        const etiquetas =
            [];

        for (
            let minuto = rango.desde;
            minuto <= rango.hasta;
            minuto += TIMELINE.etiquetaCada
        ) {
            const top =
                rango.paddingSuperior
                +
                (
                    minuto -
                    rango.desde
                )
                *
                TIMELINE.pxPorMinuto;

            etiquetas.push(
                `
                    <span
                        class="
                            horario-timeline-axis-label
                            ${
                                minuto %
                                60 ===
                                0
                                    ? 'major'
                                    : ''
                            }
                        "
                        style="
                            top:${top}px;
                        "
                    >
                        ${aTexto(
                            minuto
                        )}
                    </span>
                `
            );
        }

        return etiquetas.join(
            ''
        );
    }

    // =========================================================
    // RENDER DÍA
    // =========================================================

    function renderDiaTimeline(
        grupo,
        dia,
        rango
    ) {
        const bloquesDia =
            grupo.bloques.filter(
                (bloque) =>
                    diaANumero(
                        bloque.dia_semana
                    ) ===
                    dia.numero
            );

        const recesos =
            recesosParaGrupo(
                grupo
            )
                .map(
                    (receso) => {
                        const inicio =
                            aMinutos(
                                receso.hora_inicio
                            );

                        const fin =
                            aMinutos(
                                receso.hora_fin
                            );

                        if (
                            fin <= rango.desde
                            ||
                            inicio >= rango.hasta
                        ) {
                            return '';
                        }

                        const top =
                            rango.paddingSuperior
                            +
                            (
                                Math.max(
                                    inicio,
                                    rango.desde
                                )
                                -
                                rango.desde
                            )
                            *
                            TIMELINE.pxPorMinuto;

                        const alto =
                            Math.max(
                                26,
                                (
                                    Math.min(
                                        fin,
                                        rango.hasta
                                    )
                                    -
                                    Math.max(
                                        inicio,
                                        rango.desde
                                    )
                                )
                                *
                                TIMELINE.pxPorMinuto
                                -
                                4
                            );

                        return `
                            <div
                                class="horario-timeline-receso"
                                style="
                                    top:${top + 2}px;
                                    height:${alto}px;
                                "
                            >
                                Receso · ${esc(receso.hora_inicio)} - ${esc(receso.hora_fin)}
                            </div>
                        `;
                    }
                )
                .join(
                    ''
                );

        const tieneRecesos =
            Boolean(
                recesos.trim()
            );

        const items =
            calcularDistribucionDia(
                bloquesDia
            )
                .map(
                    (item) => {
                        const top =
                            rango.paddingSuperior
                            +
                            (
                                item.inicio -
                                rango.desde
                            )
                            *
                            TIMELINE.pxPorMinuto;

                        const alto =
                            Math.max(
                                44,

                                (
                                    item.fin -
                                    item.inicio
                                )
                                *
                                TIMELINE.pxPorMinuto
                                -
                                6
                            );

                        const porcentaje =
                            100 /
                            item.totalLanes;

                        const left =
                            item.lane
                            *
                            porcentaje;

                        return `
                            <div
                                class="
                                    horario-timeline-item
                                "
                                style="
                                    top:${top + 3}px;
                                    height:${alto}px;
                                    left:calc(
                                        ${left}% + 4px
                                    );
                                    width:calc(
                                        ${porcentaje}% - 8px
                                    );
                                "
                            >
                                ${tarjetaHorario(
                                    item.bloque,
                                    alto
                                )}
                            </div>
                        `;
                    }
                )
                .join(
                    ''
                );

        return `
            <div
                class="
                    horario-timeline-day
                    horario-dropzone
                "
                data-grupo="${esc(
                    grupo.clave
                )}"
                data-dia="${dia.numero}"
                data-rango-desde="${rango.desde}"
                data-rango-hasta="${rango.hasta}"
                data-px-minuto="${TIMELINE.pxPorMinuto}"
                data-padding-superior="${rango.paddingSuperior}"
                style="
                    height:${rango.alto}px;

                    --timeline-step:${
                        TIMELINE.pasoLinea
                        *
                        TIMELINE.pxPorMinuto
                    }px;

                    --timeline-hour:${
                        60
                        *
                        TIMELINE.pxPorMinuto
                    }px;
                "
            >
                ${
                    bloquesDia.length
                    ||
                    tieneRecesos
                        ? ''
                        : `
                            <span
                                class="
                                    horario-timeline-empty
                                "
                            >
                                Sin clases
                            </span>
                        `
                }

                ${recesos}

                ${items}
            </div>
        `;
    }

    // =========================================================
    // RENDER GRUPO
    // =========================================================

    function renderTablaGrupo(
        grupo
    ) {
        const rango =
            rangoTimeline();

        const columnas =
            plantillaColumnas(
                grupo
            );

        const headers =
            DIAS
                .map(
                    (dia) =>
                        `
                            <div
                                class="
                                    horario-timeline-head-day
                                "
                            >
                                <strong>
                                    ${dia.texto}
                                </strong>

                                <span>
                                    ${dia.corto}
                                </span>
                            </div>
                        `
                )
                .join(
                    ''
                );

        const dias =
            DIAS
                .map(
                    (dia) =>
                        renderDiaTimeline(
                            grupo,
                            dia,
                            rango
                        )
                )
                .join(
                    ''
                );

        return `
            <section
                class="
                    horario-card
                    overflow-hidden
                    rounded-2xl
                    border
                    border-slate-200
                    bg-white
                    shadow-sm
                "
                data-grupo-card="${esc(
                    grupo.clave
                )}"
            >
                <div
                    class="
                        horario-grupo-cabecera

                        border-b
                        border-slate-200
                        px-5
                        py-4
                    "
                >
                    <span
                        class="horario-grupo-icono"
                        aria-hidden="true"
                    >
                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M3 6.5 12 2l9 4.5v5.2c0 4.7-3.8 8.8-9 10.3-5.2-1.5-9-5.6-9-10.3z" />
                            <path d="m8 11 4 2 4-2" />
                            <path d="M12 13v6" />
                        </svg>
                    </span>

                    <div class="horario-grupo-titulo">
                        <h3
                        >
                            ${esc(
                                grupo.nombre
                            )}
                        </h3>

                        <p
                        >
                            ${grupo.bloques.length}

                            ${
                                grupo.bloques.length ===
                                1
                                    ? 'clase programada'
                                    : 'clases programadas'
                            }
                        </p>
                    </div>

                    <div
                        class="
                            ml-auto
                            flex
                            flex-wrap
                            items-center
                            gap-2
                        "
                    >
                        <span
                            class="
                                rounded-full
                                px-3
                                py-1.5
                                text-[11px]
                                font-semibold
                            "
                            style="
                                background:#F1F5F9;
                                color:#1B3A6B;
                            "
                        >
                            ${
                                institucionActiva ===
                                'colegio'
                                    ? 'Colegio'
                                    : 'Academia'
                            }
                        </span>

                        <button
                            type="button"
                            class="
                                horario-export-btn
                                btn-exportar-grupo
                            "
                            data-exportar-grupo="${esc(
                                grupo.clave
                            )}"
                            data-formato="excel"
                        >
                            Excel
                        </button>

                        <button
                            type="button"
                            class="
                                horario-export-btn
                                btn-exportar-grupo
                            "
                            data-exportar-grupo="${esc(
                                grupo.clave
                            )}"
                            data-formato="imagen"
                        >
                            Imagen
                        </button>

                        <button
                            type="button"
                            class="
                                horario-export-btn
                                horario-export-btn-pdf
                                btn-exportar-grupo
                            "
                            data-exportar-grupo="${esc(
                                grupo.clave
                            )}"
                            data-formato="pdf"
                        >
                            PDF
                        </button>
                    </div>
                </div>

                <div
                    class="
                        horario-timeline-scroll
                    "
                >
                    <div
                        class="
                            horario-timeline-grid
                        "
                    >
                        <div
                            class="
                                horario-timeline-head
                            "
                            style="
                                grid-template-columns:
                                    ${columnas};
                            "
                        >
                            <div
                                class="
                                    horario-timeline-head-time
                                "
                            >
                                Hora
                            </div>

                            ${headers}
                        </div>

                        <div
                            class="
                                horario-timeline-body
                            "
                            style="
                                grid-template-columns:
                                    ${columnas};
                            "
                        >
                            <div
                                class="
                                    horario-timeline-axis
                                "
                                style="
                                    height:${rango.alto}px;
                                "
                            >
                                ${crearEtiquetasTiempo(
                                    rango
                                )}
                            </div>

                            ${dias}
                        </div>
                    </div>
                </div>
            </section>
        `;
    }

    // =========================================================
    // RENDER GENERAL
    // =========================================================

    function render() {
        const institucionales =
            horariosCache.filter(
                horarioEsDeInstitucion
            );

        cargarFiltros(
            institucionales
        );

        const filtrados =
            aplicarFiltros(
                institucionales
            );

        actualizarEstadisticas(
            filtrados
        );

        actualizarTituloVista();

        if (
            !filtrados.length
        ) {
            gruposVisibles =
                [];

            contenedor.innerHTML =
                `
                    <div
                        class="
                            rounded-2xl
                            border
                            border-dashed
                            border-slate-300
                            bg-slate-50
                            px-6
                            py-14
                            text-center
                        "
                    >
                        <h3
                            class="
                                font-bold
                                text-slate-800
                            "
                        >
                            Sin horarios
                        </h3>

                        <p
                            class="
                                mt-2
                                text-sm
                                text-slate-500
                            "
                        >
                            No existen clases que coincidan
                            con los filtros seleccionados.
                        </p>
                    </div>
                `;

            return;
        }

        gruposVisibles =
            agruparBloques(
                filtrados
            );

        contenedor.innerHTML =
            gruposVisibles
                .map(
                    renderTablaGrupo
                )
                .join(
                    ''
                );

        activarDragDrop();
    }

    // =========================================================
    // MODAL ELIMINAR
    // =========================================================

    function abrirModalEliminarHorario(
        id
    ) {
        const bloque =
            buscarHorario(
                id
            );

        if (!bloque) {
            mostrarMensaje(
                'No se encontró la clase.',
                'error'
            );

            return;
        }

        horarioAEliminarId =
            bloque.id;

        const datos =
            datosClase(
                bloque
            );

        if (
            eliminarHorarioProfesor
        ) {
            eliminarHorarioProfesor.textContent =
                datos.profesor;
        }

        if (
            eliminarHorarioCurso
        ) {
            eliminarHorarioCurso.textContent =
                datos.curso;
        }

        if (
            eliminarHorarioDia
        ) {
            eliminarHorarioDia.textContent =
                diaATexto(
                    bloque.dia_semana
                );
        }

        if (
            eliminarHorarioHora
        ) {
            eliminarHorarioHora.textContent =
                `${bloque.hora_inicio} - ${bloque.hora_fin}`;
        }

        if (
            eliminarHorarioAula
        ) {
            eliminarHorarioAula.textContent =
                datos.aula;
        }

        eliminarHorarioModal
            ?.classList
            .remove(
                'hidden'
            );

        document.body
            .classList
            .add(
                'overflow-hidden'
            );
    }

    function cerrarModalEliminarHorario() {
        horarioAEliminarId =
            null;

        eliminarHorarioModal
            ?.classList
            .add(
                'hidden'
            );

        document.body
            .classList
            .remove(
                'overflow-hidden'
            );
    }

    // =========================================================
    // ELIMINAR REAL
    // =========================================================

    async function eliminarHorarioSeleccionado() {
        if (
            horarioAEliminarId ===
            null
        ) {
            return;
        }

        const id =
            horarioAEliminarId;

        if (
            confirmEliminarHorario
        ) {
            confirmEliminarHorario.disabled =
                true;
        }

        try {
            await window
                .axios
                .delete(
                    `${API.horarios}/${id}`,
                    {
                        data: {
                            motivo:
                                'Eliminación manual desde módulo Horarios',
                        },
                    }
                );

            cerrarModalEliminarHorario();

            mostrarMensaje(
                'Horario eliminado correctamente.'
            );

            await cargarDatos({
                silencioso:
                    true,
            });
        } catch (error) {
            mostrarMensaje(
                obtenerMensajeError(
                    error,
                    'No se pudo eliminar el horario.'
                ),
                'error'
            );
        } finally {
            if (
                confirmEliminarHorario
            ) {
                confirmEliminarHorario.disabled =
                    false;
            }
        }
    }

    // =========================================================
    // CLAVE DE GRUPO
    // =========================================================

    function claveGrupoBloque(
        bloque
    ) {
        if (
            getAgrupaPor() ===
            'profesor'
        ) {
            return (
                `prof-${bloque.profesor_id}`
            );
        }

        if (
            getAgrupaPor() ===
            'aula'
        ) {
            return (
                `aula-${bloque.aula_id}`
            );
        }

        return (
            `grado-${bloque.grado_id}`
        );
    }

    // =========================================================
    // MOVER CLASE REAL
    // =========================================================

    async function moverClase(
        id,
        dia,
        horaInicio
    ) {
        const bloque =
            buscarHorario(
                id
            );

        if (!bloque) {
            mostrarMensaje(
                'No se encontró la clase que deseas mover.',
                'error'
            );

            return false;
        }

        const duracion =
            duracionMinutos(
                bloque
            );

        if (
            duracion <=
            0
        ) {
            mostrarMensaje(
                'La duración de la clase no es válida.',
                'error'
            );

            return false;
        }

        const horaFin =
            aTexto(
                aMinutos(
                    horaInicio
                )
                +
                duracion
            );

        const validacion =
            validarMovimiento(
                bloque,
                dia,
                horaInicio,
                horaFin
            );

        if (
            !validacion.valido
        ) {
            mostrarMensaje(
                validacion.mensaje,
                'error'
            );

            return false;
        }

        try {
            const respuesta =
                await window
                    .axios
                    .put(
                        `${API.horarios}/${bloque.id}`,
                        {
                            dia_semana:
                                diaATextoApi(
                                    dia
                                ),

                            hora_inicio:
                                horaInicio,

                            hora_fin:
                                horaFin,

                            turno:
                                determinarTurno(
                                    horaInicio
                                ),

                            modificado_por:
                                'frontend',

                            motivo:
                                'Cambio manual mediante arrastrar y soltar',
                        }
                    );

            const actualizado =
                respuesta
                    .data
                    ?.data;

            if (
                actualizado
            ) {
                const indice =
                    horariosCache.findIndex(
                        (horario) =>
                            String(
                                horario.id
                            ) ===
                            String(
                                bloque.id
                            )
                    );

                if (
                    indice !==
                    -1
                ) {
                    horariosCache[
                        indice
                    ] =
                        normalizarHorario(
                            actualizado
                        );
                }
            } else {
                await cargarDatos({
                    silencioso:
                        true,
                });
            }

            render();

            mostrarMensaje(
                `Clase movida al ${diaATexto(
                    dia
                )} de ${horaInicio} a ${horaFin}.`
            );

            return true;
        } catch (error) {
            mostrarMensaje(
                obtenerMensajeError(
                    error,
                    'No se pudo mover la clase.'
                ),
                'error'
            );

            return false;
        }
    }

    // =========================================================
    // DRAG PREVIEW
    // =========================================================

    function quitarPreviewDrop(
        zona
    ) {
        zona
            ?.querySelector(
                '.horario-drop-preview'
            )
            ?.remove();

        zona
            ?.classList
            .remove(
                'drag-over',
                'drag-invalid'
            );

        if (
            zona
        ) {
            delete zona
                .dataset
                .horaPreview;
        }
    }

    const limpiarTodosPreviewDrop = () =>
        contenedor
            .querySelectorAll(
                '.horario-dropzone'
            )
            .forEach(
                quitarPreviewDrop
            );

    function minutoDesdeEvento(
        zona,
        event,
        bloque
    ) {
        const rect =
            zona
                .getBoundingClientRect();

        const desde =
            Number(
                zona
                    .dataset
                    .rangoDesde
            );

        const hasta =
            Number(
                zona
                    .dataset
                    .rangoHasta
            );

        const pxMinuto =
            Number(
                zona
                    .dataset
                    .pxMinuto
                ||
                TIMELINE.pxPorMinuto
            );

        const paddingSuperior =
            Number(
                zona
                    .dataset
                    .paddingSuperior
                ||
                TIMELINE.paddingSuperior
            );

        const duracion =
            duracionMinutos(
                bloque
            );

        const y =
            Math.max(
                0,

                Math.min(
                    rect.height,

                    event.clientY
                    -
                    rect.top
                )
            );

        let minuto =
            desde
            +
            Math.max(
                0,

                y -
                paddingSuperior
            )
            /
            pxMinuto;

        minuto =
            Math.round(
                minuto /
                TIMELINE.pasoMovimiento
            )
            *
            TIMELINE.pasoMovimiento;

        return Math.max(
            desde,

            Math.min(
                Math.max(
                    desde,

                    hasta -
                    duracion
                ),

                minuto
            )
        );
    }

    function mostrarPreviewDrop(
        zona,
        minuto,
        bloque,
        valido
    ) {
        quitarPreviewDrop(
            zona
        );

        const desde =
            Number(
                zona
                    .dataset
                    .rangoDesde
            );

        const pxMinuto =
            Number(
                zona
                    .dataset
                    .pxMinuto
                ||
                TIMELINE.pxPorMinuto
            );

        const paddingSuperior =
            Number(
                zona
                    .dataset
                    .paddingSuperior
                ||
                TIMELINE.paddingSuperior
            );

        const top =
            paddingSuperior
            +
            (
                minuto -
                desde
            )
            *
            pxMinuto;

        const horaInicio =
            aTexto(
                minuto
            );

        const horaFin =
            aTexto(
                minuto
                +
                duracionMinutos(
                    bloque
                )
            );

        const preview =
            document.createElement(
                'div'
            );

        preview.className =
            `
                horario-drop-preview
                ${
                    valido
                        ? ''
                        : 'invalid'
                }
            `;

        preview.style.top =
            `${top}px`;

        preview.innerHTML =
            `
                <span
                    class="
                        horario-drop-preview-label
                    "
                >
                    ${esc(
                        horaInicio
                    )}

                    -

                    ${esc(
                        horaFin
                    )}
                </span>
            `;

        zona.appendChild(
            preview
        );

        zona.dataset.horaPreview =
            horaInicio;

        zona
            .classList
            .add(
                'drag-over'
            );

        zona
            .classList
            .toggle(
                'drag-invalid',
                !valido
            );
    }

    // =========================================================
    // DRAG & DROP
    // =========================================================

    function activarDragDrop() {
        if (
            !puedeEditarHorario()
        ) {
            return;
        }

        contenedor
            .querySelectorAll(
                '.horario-clase'
            )
            .forEach(
                (clase) => {
                    clase.addEventListener(
                        'dragstart',
                        (event) => {
                            if (
                                event.target.closest(
                                    '.btn-eliminar-horario'
                                )
                            ) {
                                event.preventDefault();

                                return;
                            }

                            bloqueArrastradoId =
                                clase
                                    .dataset
                                    .horarioId;

                            clase
                                .classList
                                .add(
                                    'dragging'
                                );

                            event.dataTransfer.effectAllowed =
                                'move';

                            event
                                .dataTransfer
                                .setData(
                                    'text/plain',
                                    bloqueArrastradoId
                                );
                        }
                    );

                    clase.addEventListener(
                        'dragend',
                        () => {
                            bloqueArrastradoId =
                                null;

                            clase
                                .classList
                                .remove(
                                    'dragging'
                                );

                            limpiarTodosPreviewDrop();
                        }
                    );
                }
            );

        contenedor
            .querySelectorAll(
                '.horario-dropzone'
            )
            .forEach(
                (zona) => {
                    zona.addEventListener(
                        'dragover',
                        (event) => {
                            event.preventDefault();

                            const id =
                                bloqueArrastradoId
                                ||
                                event
                                    .dataTransfer
                                    .getData(
                                        'text/plain'
                                    );

                            const bloque =
                                buscarHorario(
                                    id
                                );

                            if (!bloque) {
                                return;
                            }

                            if (
                                zona
                                    .dataset
                                    .grupo !==
                                claveGrupoBloque(
                                    bloque
                                )
                            ) {
                                quitarPreviewDrop(
                                    zona
                                );

                                zona
                                    .classList
                                    .add(
                                        'drag-over',
                                        'drag-invalid'
                                    );

                                event.dataTransfer.dropEffect =
                                    'none';

                                return;
                            }

                            const dia =
                                Number(
                                    zona
                                        .dataset
                                        .dia
                                );

                            const minuto =
                                minutoDesdeEvento(
                                    zona,
                                    event,
                                    bloque
                                );

                            const horaInicio =
                                aTexto(
                                    minuto
                                );

                            const horaFin =
                                aTexto(
                                    minuto
                                    +
                                    duracionMinutos(
                                        bloque
                                    )
                                );

                            const validacion =
                                validarMovimiento(
                                    bloque,
                                    dia,
                                    horaInicio,
                                    horaFin
                                );

                            mostrarPreviewDrop(
                                zona,
                                minuto,
                                bloque,
                                validacion.valido
                            );

                            event.dataTransfer.dropEffect =
                                validacion.valido
                                    ? 'move'
                                    : 'none';
                        }
                    );

                    zona.addEventListener(
                        'dragleave',
                        (event) => {
                            if (
                                event.relatedTarget
                                &&
                                zona.contains(
                                    event.relatedTarget
                                )
                            ) {
                                return;
                            }

                            quitarPreviewDrop(
                                zona
                            );
                        }
                    );

                    zona.addEventListener(
                        'drop',
                        async (
                            event
                        ) => {
                            event.preventDefault();

                            const id =
                                event
                                    .dataTransfer
                                    .getData(
                                        'text/plain'
                                    )
                                ||
                                bloqueArrastradoId;

                            const bloque =
                                buscarHorario(
                                    id
                                );

                            if (!bloque) {
                                limpiarTodosPreviewDrop();

                                return;
                            }

                            if (
                                zona
                                    .dataset
                                    .grupo !==
                                claveGrupoBloque(
                                    bloque
                                )
                            ) {
                                limpiarTodosPreviewDrop();

                                mostrarMensaje(
                                    'Solo puedes cambiar el día y la hora dentro del mismo profesor, aula o grado.',
                                    'error'
                                );

                                return;
                            }

                            const dia =
                                Number(
                                    zona
                                        .dataset
                                        .dia
                                );

                            const horaInicio =
                                zona
                                    .dataset
                                    .horaPreview
                                ||
                                aTexto(
                                    minutoDesdeEvento(
                                        zona,
                                        event,
                                        bloque
                                    )
                                );

                            const horaFin =
                                aTexto(
                                    aMinutos(
                                        horaInicio
                                    )
                                    +
                                    duracionMinutos(
                                        bloque
                                    )
                                );

                            const validacion =
                                validarMovimiento(
                                    bloque,
                                    dia,
                                    horaInicio,
                                    horaFin
                                );

                            limpiarTodosPreviewDrop();

                            if (
                                !validacion.valido
                            ) {
                                mostrarMensaje(
                                    validacion.mensaje,
                                    'error'
                                );

                                return;
                            }

                            await moverClase(
                                id,
                                dia,
                                horaInicio
                            );
                        }
                    );
                }
            );
    }

    // =========================================================
    // EXPORTACIONES REALES
    // =========================================================

    function obtenerFiltrosExportacion(
        grupo = null
    ) {
        const params = {
            institucion:
                institucionActiva,
        };

        if (
            grupo
        ) {
            if (
                grupo.tipo ===
                'profesor'
            ) {
                params.profesor_id =
                    grupo.id;
            }

            if (
                grupo.tipo ===
                'aula'
            ) {
                params.aula_id =
                    grupo.id;
            }

            if (
                grupo.tipo ===
                'grado'
            ) {
                params.grado_id =
                    grupo.id;
            }
        } else {
            if (
                filtroProfesor?.value
                &&
                filtroProfesor.value !==
                'todos'
            ) {
                params.profesor_id =
                    filtroProfesor.value;
            }

            if (
                filtroAula?.value
                &&
                filtroAula.value !==
                'todos'
            ) {
                params.aula_id =
                    filtroAula.value;
            }

            if (
                filtroGrado?.value
                &&
                filtroGrado.value !==
                'todos'
            ) {
                params.grado_id =
                    filtroGrado.value;
            }
        }

        if (
            filtroCurso?.value
            &&
            filtroCurso.value !==
            'todos'
        ) {
            params.curso_id =
                filtroCurso.value;
        }

        return params;
    }

    function tituloExportacion(
        grupo = null
    ) {
        const institucion =
            institucionActiva ===
            'colegio'
                ? 'Colegio'
                : 'Academia';

        if (
            grupo
        ) {
            return (
                `Horario de ${grupo.nombre} - ${institucion}`
            );
        }

        return (
            `${
                tituloEl?.textContent
                ||
                'Horario Académico'
            } - ${institucion}`
        );
    }

    const nombreSeguroArchivo = (
        valor
    ) =>
        normalizarTexto(
            valor
        )
            .replace(
                /[^a-z0-9]+/g,
                '_'
            )
            .replace(
                /^_+|_+$/g,
                ''
            )
        ||
        'horario';

    function descargarBlob(
        blob,
        nombre
    ) {
        const url =
            URL.createObjectURL(
                blob
            );

        const enlace =
            document.createElement(
                'a'
            );

        enlace.href =
            url;

        enlace.download =
            nombre;

        document.body.appendChild(
            enlace
        );

        enlace.click();

        enlace.remove();

        setTimeout(
            () => {
                URL.revokeObjectURL(
                    url
                );
            },
            1200
        );
    }

    // =========================================================
    // PDF DESDE Laravel / exports/horario-pdf.blade.php
    // =========================================================

    async function exportarPdf(
        bloques,
        nombre,
        grupo = null
    ) {
        if (
            !bloques.length
        ) {
            mostrarMensaje(
                'No hay datos para exportar.',
                'info'
            );

            return;
        }

        try {
            mostrarMensaje(
                'Generando PDF...',
                'info'
            );

            const respuesta =
                await window
                    .axios
                    .get(
                        API.exportPdf,
                        {
                            params: {
                                ...obtenerFiltrosExportacion(
                                    grupo
                                ),

                                titulo:
                                    tituloExportacion(
                                        grupo
                                    ),
                            },

                            responseType:
                                'blob',
                        }
                    );

            const blob =
                new Blob(
                    [
                        respuesta.data,
                    ],
                    {
                        type:
                            'application/pdf',
                    }
                );

            descargarBlob(
                blob,

                `${
                    nombreSeguroArchivo(
                        nombre
                        ||
                        grupo?.nombre
                        ||
                        'horario'
                    )
                }.pdf`
            );

            mostrarMensaje(
                'PDF descargado correctamente.'
            );
        } catch (error) {
            console.error(
                'Error exportando PDF:',
                error
            );

            mostrarMensaje(
                obtenerMensajeError(
                    error,
                    'No se pudo generar el PDF.'
                ),
                'error'
            );
        }
    }

    // =========================================================
    // IMAGEN DESDE Laravel / exports/horario-imagen.blade.php
    // =========================================================

    async function exportarImagenGrupo(
        grupo
    ) {
        if (
            !grupo
                ?.bloques
                ?.length
        ) {
            mostrarMensaje(
                'No hay datos para generar la imagen.',
                'info'
            );

            return;
        }

        if (
            typeof html2canvas ===
            'undefined'
        ) {
            mostrarMensaje(
                'No se cargó la librería para generar la imagen.',
                'error'
            );

            return;
        }

        let iframe =
            null;

        try {
            mostrarMensaje(
                'Generando imagen...',
                'info'
            );

            const respuesta =
                await window
                    .axios
                    .get(
                        API.exportImagen,
                        {
                            params: {
                                ...obtenerFiltrosExportacion(
                                    grupo
                                ),

                                titulo:
                                    tituloExportacion(
                                        grupo
                                    ),
                            },
                        }
                    );

            const html =
                respuesta
                    .data
                    ?.data
                    ?.html;

            if (!html) {
                throw new Error(
                    'El servidor no devolvió el HTML de exportación.'
                );
            }

            /*
             * El HTML se carga en un iframe aislado.
             *
             * Esto evita que html2canvas lea
             * colores oklch de Tailwind.
             */
            iframe =
                document.createElement(
                    'iframe'
                );

            iframe.setAttribute(
                'aria-hidden',
                'true'
            );

            Object.assign(
                iframe.style,
                {
                    position:
                        'fixed',

                    left:
                        '-12000px',

                    top:
                        '0',

                    width:
                        '1200px',

                    height:
                        '2200px',

                    opacity:
                        '0',

                    pointerEvents:
                        'none',

                    border:
                        '0',
                }
            );

            document.body.appendChild(
                iframe
            );

            const documento =
                iframe.contentDocument
                ||
                iframe
                    .contentWindow
                    .document;

            documento.open();

            documento.write(
                html
            );

            documento.close();

            await new Promise(
                (resolve) =>
                    setTimeout(
                        resolve,
                        300
                    )
            );

            if (
                documento.fonts?.ready
            ) {
                await documento
                    .fonts
                    .ready
                    .catch(
                        () => {}
                    );
            }

            const elemento =
                documento.getElementById(
                    'horario-container'
                )
                ||
                documento.body;

            const ancho =
                Math.max(
                    1680,
                    elemento.scrollWidth,
                    documento
                        .body
                        .scrollWidth
                );

            const alto =
                Math.max(
                    900,
                    elemento.scrollHeight,
                    documento
                        .body
                        .scrollHeight
                );

            iframe.style.width =
                `${ancho}px`;

            iframe.style.height =
                `${alto}px`;

            await new Promise(
                (resolve) =>
                    iframe.contentWindow
                        .requestAnimationFrame(
                            resolve
                        )
            );

            const rect =
                elemento.getBoundingClientRect();

            const anchoCaptura =
                Math.ceil(
                    Math.max(
                        rect.width,
                        elemento.scrollWidth
                    )
                );

            const altoCaptura =
                Math.ceil(
                    Math.max(
                        rect.height,
                        elemento.scrollHeight
                    )
                );

            const canvas =
                await html2canvas(
                    elemento,
                    {
                        scale:
                            2,

                        backgroundColor:
                            '#FFFFFF',

                        logging:
                            false,

                        useCORS:
                            true,

                        width:
                            anchoCaptura,

                        height:
                            altoCaptura,

                        windowWidth:
                            anchoCaptura,

                        windowHeight:
                            altoCaptura,
                    }
                );

            const blob =
                await new Promise(
                    (resolve) =>
                        canvas.toBlob(
                            resolve,
                            'image/png',
                            1
                        )
                );

            if (!blob) {
                throw new Error(
                    'No se pudo construir la imagen PNG.'
                );
            }

            descargarBlob(
                blob,

                `${
                    nombreSeguroArchivo(
                        `horario_${grupo.nombre}`
                    )
                }.png`
            );

            mostrarMensaje(
                'Imagen descargada correctamente.'
            );
        } catch (error) {
            console.error(
                'Error generando imagen:',
                error
            );

            mostrarMensaje(
                obtenerMensajeError(
                    error,
                    'No se pudo generar la imagen.'
                ),
                'error'
            );
        } finally {
            iframe?.remove();
        }
    }

    // =========================================================
    // EXCEL
    // =========================================================

    function ordenarBloquesExportacion(
        bloques
    ) {
        return [
            ...bloques,
        ].sort(
            (a, b) =>
                diaANumero(
                    a.dia_semana
                )
                -
                diaANumero(
                    b.dia_semana
                )
                ||
                aMinutos(
                    a.hora_inicio
                )
                -
                aMinutos(
                    b.hora_inicio
                )
        );
    }

    function exportarExcel(
        bloques,
        nombre = null
    ) {
        if (
            !bloques.length
        ) {
            mostrarMensaje(
                'No hay datos para exportar.',
                'info'
            );

            return;
        }

        if (
            typeof XLSX ===
            'undefined'
        ) {
            mostrarMensaje(
                'No se cargó la librería de Excel.',
                'error'
            );

            return;
        }

        const filas = [[
            'Institución',
            'Día',
            'Hora inicio',
            'Hora fin',
            'Profesor',
            'Curso',
            'Grado',
            'Aula',
        ]];

        ordenarBloquesExportacion(
            bloques
        ).forEach(
            (bloque) => {
                const datos =
                    datosClase(
                        bloque
                    );

                filas.push([
                    institucionActiva ===
                    'colegio'
                        ? 'Colegio'
                        : 'Academia',

                    diaATexto(
                        bloque.dia_semana
                    ),

                    bloque.hora_inicio,

                    bloque.hora_fin,

                    datos.profesor,

                    datos.curso,

                    datos.grado,

                    datos.aula,
                ]);
            }
        );

        const hoja =
            XLSX
                .utils
                .aoa_to_sheet(
                    filas
                );

        const libro =
            XLSX
                .utils
                .book_new();

        XLSX
            .utils
            .book_append_sheet(
                libro,
                hoja,
                'Horarios'
            );

        const nombreArchivo =
            nombre
                ? nombreSeguroArchivo(
                    `horario_${nombre}_${institucionActiva}`
                )
                : `horarios_${institucionActiva}`;

        XLSX.writeFile(
            libro,
            `${nombreArchivo}.xlsx`
        );

        mostrarMensaje(
            'Excel descargado correctamente.'
        );
    }

    // =========================================================
    // EVENTOS EN CONTENEDOR
    // =========================================================

    contenedor.addEventListener(
        'click',
        async (
            event
        ) => {
            const botonEliminar =
                event.target.closest(
                    '.btn-eliminar-horario'
                );

            if (
                botonEliminar
            ) {
                event.preventDefault();

                event.stopPropagation();

                abrirModalEliminarHorario(
                    botonEliminar
                        .dataset
                        .eliminarHorarioId
                );

                return;
            }

            const botonExportar =
                event.target.closest(
                    '.btn-exportar-grupo'
                );

            if (!botonExportar) {
                return;
            }

            event.preventDefault();

            const grupo =
                gruposVisibles.find(
                    (item) =>
                        item.clave ===
                        botonExportar
                            .dataset
                            .exportarGrupo
                );

            if (!grupo) {
                mostrarMensaje(
                    'No se encontró el horario.',
                    'error'
                );

                return;
            }

            const contenidoOriginal =
                botonExportar.innerHTML;

            botonExportar.disabled =
                true;

            botonExportar.textContent =
                'Generando...';

            try {
                const formato =
                    botonExportar
                        .dataset
                        .formato;

                if (
                    formato ===
                    'excel'
                ) {
                    exportarExcel(
                        grupo.bloques,

                        grupo.nombre
                    );
                } else if (
                    formato ===
                    'pdf'
                ) {
                    await exportarPdf(
                        grupo.bloques,

                        `horario_${grupo.nombre}_${institucionActiva}`,

                        grupo
                    );
                } else {
                    await exportarImagenGrupo(
                        grupo
                    );
                }
            } finally {
                botonExportar.disabled =
                    false;

                botonExportar.innerHTML =
                    contenidoOriginal;
            }
        }
    );

    // =========================================================
    // MODAL
    // =========================================================

    closeEliminarHorarioModal
        ?.addEventListener(
            'click',
            cerrarModalEliminarHorario
        );

    cancelEliminarHorarioModal
        ?.addEventListener(
            'click',
            cerrarModalEliminarHorario
        );

    eliminarHorarioOverlay
        ?.addEventListener(
            'click',
            cerrarModalEliminarHorario
        );

    confirmEliminarHorario
        ?.addEventListener(
            'click',
            eliminarHorarioSeleccionado
        );

    // =========================================================
    // INSTITUCIÓN
    // =========================================================

    tabsInstitucion.forEach(
        (tab) =>
            tab.addEventListener(
                'click',
                () => {
                    institucionActiva =
                        tab
                            .dataset
                            .institucion;

                    tabsInstitucion.forEach(
                        (boton) => {
                            const activo =
                                boton ===
                                tab;

                            boton.style.background =
                                activo
                                    ? 'linear-gradient(135deg,#1B3A6B,#0F2749)'
                                    : '#FFFFFF';

                            boton.style.color =
                                activo
                                    ? '#FFFFFF'
                                    : '#475569';
                        }
                    );

                    limpiarFiltros();

                    cerrarTodosFiltros();

                    cargarConfigHorario();

                    cargarDatos();
                }
            )
    );

    // =========================================================
    // BLOQUES Y RECESOS
    // =========================================================

    configNivel
        ?.addEventListener(
            'change',
            cargarConfigHorario
        );

    configTurno
        ?.addEventListener(
            'change',
            cargarConfigHorario
        );

    configRecesoInicio
        ?.addEventListener(
            'input',
            () => {
                renderBloquesConfig();
            }
        );

    configRecesoMinutos
        ?.addEventListener(
            'input',
            () => {
                renderBloquesConfig();
            }
        );

    btnGuardarConfigHorario
        ?.addEventListener(
            'click',
            guardarConfigHorario
        );

    configBloquesLista
        ?.addEventListener(
            'input',
            (event) => {
                const campo =
                    event
                        .target
                        ?.dataset
                        ?.configCampo;

                const fila =
                    event
                        .target
                        ?.closest(
                            '[data-config-index]'
                        );

                if (
                    !campo
                    ||
                    !fila
                ) {
                    return;
                }

                const index =
                    Number(
                        fila.dataset.configIndex
                    );

                if (
                    !bloquesConfiguracionHorario[index]
                ) {
                    return;
                }

                bloquesConfiguracionHorario[index][campo] =
                    campo === 'numero_bloque'
                        ? Number(
                            event.target.value
                        )
                        : event.target.value;
            }
        );

    configBloquesLista
        ?.addEventListener(
            'change',
            (event) => {
                const campo =
                    event
                        .target
                        ?.dataset
                        ?.configCampo;

                const fila =
                    event
                        .target
                        ?.closest(
                            '[data-config-index]'
                        );

                if (
                    !campo
                    ||
                    !fila
                ) {
                    return;
                }

                const index =
                    Number(
                        fila.dataset.configIndex
                    );

                if (
                    !bloquesConfiguracionHorario[index]
                ) {
                    return;
                }

                bloquesConfiguracionHorario[index][campo] =
                    campo === 'numero_bloque'
                        ? Number(
                            event.target.value
                        )
                        : event.target.value;

                renderBloquesConfig();
            }
        );

    configBloquesLista
        ?.addEventListener(
            'click',
            (event) => {
                const boton =
                    event
                        .target
                        ?.closest(
                            '[data-config-eliminar]'
                        );

                if (
                    !boton
                ) {
                    return;
                }

                bloquesConfiguracionHorario.splice(
                    Number(
                        boton.dataset.configEliminar
                    ),
                    1
                );

                renderBloquesConfig();
            }
        );

    // =========================================================
    // VISTAS
    // =========================================================

    vistaBotones.forEach(
        (boton) =>
            boton.addEventListener(
                'click',
                () => {
                    vistaActual =
                        boton
                            .dataset
                            .vista;

                    vistaBotones.forEach(
                        (item) => {
                            const activo =
                                item ===
                                boton;

                            item.style.borderColor =
                                activo
                                    ? '#1B3A6B'
                                    : '#E2E8F0';

                            item.style.background =
                                activo
                                    ? 'rgba(27,58,107,.05)'
                                    : '#FFFFFF';
                        }
                    );

                    if (
                        chkCompleto
                    ) {
                        chkCompleto.checked =
                            vistaActual ===
                            'aula-completa';

                        chkCompleto.disabled =
                            vistaActual ===
                            'aula-completa';
                    }

                    limpiarFiltros();

                    render();
                }
            )
    );

    chkCompleto
        ?.addEventListener(
            'change',
            () => {
                if (
                    chkCompleto.checked
                ) {
                    vistaActual =
                        'aula-completa';
                } else if (
                    vistaActual ===
                    'aula-completa'
                ) {
                    vistaActual =
                        'aula';
                }

                vistaBotones.forEach(
                    (item) => {
                        const activo =
                            item
                                .dataset
                                .vista ===
                            vistaActual;

                        item.style.borderColor =
                            activo
                                ? '#1B3A6B'
                                : '#E2E8F0';

                        item.style.background =
                            activo
                                ? 'rgba(27,58,107,.05)'
                                : '#FFFFFF';
                    }
                );

                limpiarFiltros();

                render();
            }
        );

    // =========================================================
    // FILTROS
    // =========================================================

    [
        filtroProfesor,
        filtroAula,
        filtroGrado,
        filtroCurso,
    ]
        .filter(
            Boolean
        )
        .forEach(
            (filtro) =>
                filtro.addEventListener(
                    'change',
                    () => {
                        actualizarCustomFiltro(
                            filtro
                        );

                        render();
                    }
                )
        );

    btnReset
        ?.addEventListener(
            'click',
            () => {
                limpiarFiltros();

                cerrarTodosFiltros();

                render();
            }
        );

    // =========================================================
    // ACTUALIZACIÓN
    // =========================================================

    window.addEventListener(
        'focus',
        () =>
            cargarDatos({
                silencioso:
                    true,
            })
    );

    // =========================================================
    // INICIO
    // =========================================================

    inyectarEstilosTimeline();

    actualizarTodosCustomFiltros();

    cargarConfigHorario();

    cargarDatos();
});
