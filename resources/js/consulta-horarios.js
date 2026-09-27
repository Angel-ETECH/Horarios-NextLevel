document.addEventListener('DOMContentLoaded', () => {

    // =============================================================
    // ELEMENTOS PRINCIPALES
    // =============================================================

    const botonesInstitucion =
        document.querySelectorAll('.institucion-consulta');

    const botonesTipo =
        document.querySelectorAll('.tipo-consulta');

    const botonTipoGrado =
        document.getElementById('btn-tipo-grado') ||
        document.querySelector('[data-tipo="grado"]');

    const selector =
        document.getElementById('consulta-selector');

    const selectorLabel =
        document.getElementById('consulta-label');

    const btnConsultar =
        document.getElementById('btn-consultar-horario');

    const btnNuevaConsulta =
        document.getElementById('btn-nueva-consulta');

    const mensaje =
        document.getElementById('consulta-mensaje');

    const consultaInfo =
        document.getElementById('consulta-info');

    const consultaTipoInfo =
        document.getElementById('consulta-tipo-info');

    const consultaNombre =
        document.getElementById('consulta-nombre');

    const consultaDetalle =
        document.getElementById('consulta-detalle');

    const consultaInstitucion =
        document.getElementById('consulta-institucion');

    const horarioContainer =
        document.getElementById('horario-container');

    const horarioRender =
        document.getElementById('horario-render');

    const horarioNavegacion =
        document.getElementById('horario-navegacion');


    // =============================================================
    // CUSTOM SELECT
    // =============================================================

    const customSelect =
        document.getElementById('custom-select');

    const customButton =
        document.getElementById('custom-select-button');

    const customIcon =
        document.getElementById('custom-select-icon');

    const customSmall =
        document.getElementById('custom-select-small');

    const customText =
        document.getElementById('custom-select-text');

    const customSearch =
        document.getElementById('custom-select-search');

    const customOptions =
        document.getElementById('custom-select-options');


    // =============================================================
    // DESCARGAS
    // =============================================================

    const accionesDescarga =
        document.getElementById('acciones-descarga');

    const btnDescargarImagen =
        document.getElementById('btn-descargar-imagen');

    const btnDescargarPdf =
        document.getElementById('btn-descargar-pdf');


    // =============================================================
    // NAVEGACIÓN MÓVIL
    // =============================================================

    const btnDiaAnterior =
        document.getElementById('horario-dia-anterior');

    const btnDiaSiguiente =
        document.getElementById('horario-dia-siguiente');

    const horarioDiaActual =
        document.getElementById('horario-dia-actual');

    const horarioDiaContador =
        document.getElementById('horario-dia-contador');


    // =============================================================
    // VALIDACIÓN INICIAL
    // =============================================================

    if (
        !selector ||
        !btnConsultar ||
        !horarioRender
    ) {
        return;
    }


    // =============================================================
    // API PÚBLICA
    // =============================================================

    const API = {

        profesores:
            '/api/publico/profesores',

        grados:
            '/api/publico/grados',

        aulas:
            '/api/publico/aulas',

        cursos:
            '/api/publico/cursos',

        horarioProfesor:
            id =>
                `/api/publico/horario/profesor/${id}`,

        horarioProfesorPdf:
            id =>
                `/api/publico/horario/profesor/${id}/pdf`,

        horarioProfesorImagen:
            id =>
                `/api/publico/horario/profesor/${id}/imagen`,

        horarioGrado:
            id =>
                `/api/publico/horario/grado/${id}`,

        horarioAula:
            id =>
                `/api/publico/horario/aula/${id}`,

        horarioCurso:
            id =>
                `/api/publico/horario/curso/${id}`
    };


    // =============================================================
    // ESTADO
    // =============================================================

    let institucionSeleccionada = '';

    let tipoSeleccionado = '';

    let diaMovilActual = 0;

    let registroSeleccionadoActual = null;

    let horariosSeleccionadosActuales = [];

    let opcionesSelectorActuales = [];

    let temporizadorBusqueda = null;

    let consultaBusquedaActual = 0;

    let cargandoOpciones = false;

    let touchInicioX = null;


    // =============================================================
    // DÍAS
    // =============================================================

    const DIAS = [
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes',
        'Sábado'
    ];


    // =============================================================
    // UTILIDADES
    // =============================================================

    function normalizarTexto(valor) {

        return String(valor ?? '')
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(
                /[\u0300-\u036f]/g,
                ''
            );
    }


    function escaparHTML(valor) {

        return String(valor ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    function limpiarNombreArchivo(texto) {

        return normalizarTexto(texto)
            .replace(
                /[^a-z0-9]+/g,
                '_'
            )
            .replace(
                /^_+|_+$/g,
                ''
            ) || 'horario';
    }


    function normalizarDia(dia) {

        const valor =
            normalizarTexto(dia);

        const mapa = {

            '1':
                'Lunes',

            '2':
                'Martes',

            '3':
                'Miércoles',

            '4':
                'Jueves',

            '5':
                'Viernes',

            '6':
                'Sábado',

            lunes:
                'Lunes',

            martes:
                'Martes',

            miercoles:
                'Miércoles',

            jueves:
                'Jueves',

            viernes:
                'Viernes',

            sabado:
                'Sábado'
        };

        return mapa[valor] || '';
    }


    function horaCorta(hora) {

        if (!hora) {
            return '';
        }

        const texto =
            String(hora);

        const coincidencia =
            texto.match(
                /(\d{2}):(\d{2})/
            );

        if (coincidencia) {

            return `${coincidencia[1]}:${coincidencia[2]}`;
        }

        return texto;
    }


    function aMinutos(hora) {

        const corta =
            horaCorta(hora);

        const partes =
            corta.split(':');

        if (
            partes.length <
            2
        ) {
            return 0;
        }

        return (
            Number(partes[0]) *
            60
        ) +
        Number(partes[1]);
    }


    function obtenerMensajeError(
        error,
        fallback = 'Ocurrió un error.'
    ) {

        if (
            typeof error ===
            'string'
        ) {
            return error;
        }

        if (
            error?.message
        ) {
            return error.message;
        }

        return fallback;
    }


    // =============================================================
    // PETICIONES
    // =============================================================

    async function apiGet(
        url,
        params = {}
    ) {

        const query =
            new URLSearchParams();

        Object.entries(
            params
        ).forEach(
            ([clave, valor]) => {

                if (
                    valor !== undefined &&
                    valor !== null &&
                    String(valor).trim() !== ''
                ) {

                    query.set(
                        clave,
                        valor
                    );
                }
            }
        );

        const ruta =
            query.toString()
                ? `${url}?${query.toString()}`
                : url;


        const respuesta =
            await fetch(
                ruta,
                {
                    method:
                        'GET',

                    headers: {
                        Accept:
                            'application/json'
                    }
                }
            );


        let data = {};

        try {

            data =
                await respuesta.json();

        } catch (error) {

            data = {};
        }


        if (
            !respuesta.ok
        ) {

            let mensajeError =
                data?.message ||
                `Error HTTP ${respuesta.status}`;

            if (
                data?.errors
            ) {

                const errores =
                    Object.values(
                        data.errors
                    ).flat();

                if (
                    errores.length
                ) {

                    mensajeError =
                        errores[0];
                }
            }


            throw new Error(
                mensajeError
            );
        }


        return data;
    }


    // =============================================================
    // NOMBRES
    // =============================================================

    function nombreProfesor(
        profesor
    ) {

        if (!profesor) {
            return 'Profesor';
        }

        return (
            profesor.nombre_completo ||

            [
                profesor.nombre,
                profesor.apellido_paterno,
                profesor.apellido_materno
            ]
                .filter(Boolean)
                .join(' ')
                .replace(
                    /\s+/g,
                    ' '
                )
                .trim() ||

            `Profesor ${profesor.id}`
        );
    }


    function nombreCurso(
        curso
    ) {

        if (!curso) {
            return 'Curso';
        }

        return (
            curso.nombre ||
            curso.nombre_curso ||
            curso.descripcion ||
            curso.codigo ||
            `Curso ${curso.id}`
        );
    }


    function nombreAula(
        aula
    ) {

        if (!aula) {
            return 'Aula';
        }

        return (
            aula.nombre ||
            aula.nombre_aula ||
            aula.codigo ||
            `Aula ${aula.id}`
        );
    }


    function obtenerNivelGrado(
        grado
    ) {

        if (!grado) {
            return '';
        }

        const normalizado =
            normalizarTexto(
                grado.nivel ||
                grado.nivel_educativo ||
                ''
            );

        if (
            normalizado ===
            'primaria'
        ) {
            return 'Primaria';
        }

        if (
            normalizado ===
            'secundaria'
        ) {
            return 'Secundaria';
        }

        if (
            normalizado ===
            'academia'
        ) {
            return 'Academia';
        }

        return '';
    }


    function nombreGrado(
        grado
    ) {

        if (!grado) {
            return 'Grado';
        }

        const base =
            grado.nombre_completo ||

            grado.nombre ||

            [
                grado.grado,
                grado.seccion
            ]
                .filter(Boolean)
                .join(' ')
                .trim() ||

            `Grado ${grado.id}`;

        const nivel =
            obtenerNivelGrado(
                grado
            );

        if (
            nivel &&
            !normalizarTexto(
                base
            ).includes(
                normalizarTexto(
                    nivel
                )
            )
        ) {

            return `${base} · ${nivel}`;
        }

        return base;
    }


    function codigoRegistro(
        registro
    ) {

        return String(
            registro?.codigo ||
            ''
        ).trim();
    }


    function obtenerNombreRegistro(
        tipo,
        registro
    ) {

        switch (tipo) {

            case 'profesor':

                return nombreProfesor(
                    registro
                );

            case 'grado':

                return nombreGrado(
                    registro
                );

            case 'aula':

                return nombreAula(
                    registro
                );

            case 'curso':

                return nombreCurso(
                    registro
                );

            default:

                return '';
        }
    }


    function etiquetaRegistro(
        tipo,
        registro
    ) {

        if (
            registro?.label
        ) {
            return registro.label;
        }

        const codigo =
            codigoRegistro(
                registro
            );

        const nombre =
            obtenerNombreRegistro(
                tipo,
                registro
            );

        return codigo
            ? `${codigo} - ${nombre}`
            : nombre;
    }


    // =============================================================
    // ICONOS
    // =============================================================

    function iconoTipo(tipo) {

        if (
            tipo ===
            'profesor'
        ) {

            return `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M20 21a8 8 0 0 0-16 0"/>
                </svg>
            `;
        }


        if (
            tipo ===
            'grado'
        ) {

            return `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <path d="m2 10 10-5 10 5-10 5Z"/>
                    <path d="M6 12v5c3 2 9 2 12 0v-5"/>
                </svg>
            `;
        }


        if (
            tipo ===
            'aula'
        ) {

            return `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <path d="M4 21V5h16v16"/>
                    <path d="M2 21h20"/>
                    <path d="M8 9h2"/>
                    <path d="M14 9h2"/>
                </svg>
            `;
        }


        return `
            <svg
                width="18"
                height="18"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.9"
            >
                <path d="M5 4h13a2 2 0 0 1 2 2v14H7a3 3 0 0 1-3-3V5a1 1 0 0 1 1-1Z"/>
                <path d="M7 4v16"/>
                <path d="M10 8h6"/>
            </svg>
        `;
    }


    // =============================================================
    // BOTONES DE INSTITUCIÓN
    // =============================================================

    function actualizarBotonesInstitucion() {

        botonesInstitucion.forEach(
            boton => {

                const activo =
                    boton.dataset.institucion ===
                    institucionSeleccionada;


                boton.classList.toggle(
                    'border-[#1B3A6B]',
                    activo
                );

                boton.classList.toggle(
                    'bg-[#0F2749]',
                    activo
                );

                boton.classList.toggle(
                    'text-white',
                    activo
                );

                boton.classList.toggle(
                    'border-slate-200',
                    !activo
                );

                boton.classList.toggle(
                    'bg-white',
                    !activo
                );

                boton.classList.toggle(
                    'text-slate-700',
                    !activo
                );
            }
        );
    }


    // =============================================================
    // BOTONES DE TIPO
    // =============================================================

    function actualizarDisponibilidadTipos() {

        botonTipoGrado?.classList.remove(
            'hidden'
        );
    }


    function actualizarBotonesTipo() {

        botonesTipo.forEach(
            boton => {

                const activo =
                    boton.dataset.tipo ===
                    tipoSeleccionado;


                boton.classList.toggle(
                    'border-[#1B3A6B]',
                    activo
                );

                boton.classList.toggle(
                    'bg-[#0F2749]',
                    activo
                );

                boton.classList.toggle(
                    'text-white',
                    activo
                );

                boton.classList.toggle(
                    'border-slate-200',
                    !activo
                );

                boton.classList.toggle(
                    'bg-white',
                    !activo
                );

                boton.classList.toggle(
                    'text-slate-700',
                    !activo
                );
            }
        );
    }


    // =============================================================
    // RESULTADOS
    // =============================================================

    function limpiarResultado() {

        mensaje?.classList.add(
            'hidden'
        );

        consultaInfo?.classList.add(
            'hidden'
        );

        horarioContainer?.classList.add(
            'hidden'
        );

        horarioRender.innerHTML =
            '';

        accionesDescarga?.classList.remove(
            'visible'
        );

        registroSeleccionadoActual =
            null;

        horariosSeleccionadosActuales =
            [];
    }


    function reiniciarConsulta() {

        tipoSeleccionado =
            '';

        selector.value =
            '';

        selector.disabled =
            true;

        selector.innerHTML = `
            <option value="">
                Selecciona cómo deseas consultar el horario
            </option>
        `;

        opcionesSelectorActuales =
            [];

        if (
            selectorLabel
        ) {

            selectorLabel.textContent =
                'Seleccionar';
        }

        if (
            customSearch
        ) {

            customSearch.value =
                '';
        }

        actualizarBotonesTipo();

        actualizarCustomSelectVisual();

        cerrarCustomSelect();

        limpiarResultado();

        mostrarMensaje(
            'Listo. Elige nuevamente el tipo de consulta y selecciona una opción.',
            'info'
        );

        document
            .getElementById(
                'consulta-formulario'
            )
            ?.scrollIntoView({
                behavior:
                    'smooth',

                block:
                    'start'
            });
    }


    function mostrarMensaje(
        texto,
        tipo = 'error'
    ) {

        if (!mensaje) {
            return;
        }


        mensaje.className =
            'mb-6 rounded-xl border px-4 py-3 text-sm';


        if (
            tipo ===
            'error'
        ) {

            mensaje.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );

        } else {

            mensaje.classList.add(
                'border-blue-200',
                'bg-blue-50',
                'text-blue-700'
            );
        }


        mensaje.textContent =
            texto;

        mensaje.classList.remove(
            'hidden'
        );
    }


    function activarCargaBoton(
        boton,
        texto
    ) {

        if (!boton) {
            return null;
        }

        const textoOriginal =
            boton.innerHTML;

        boton.disabled =
            true;

        boton.classList.add(
            'opacity-70',
            'cursor-not-allowed'
        );

        boton.innerHTML =
            `<span class="inline-flex items-center justify-center gap-2">
                <span class="h-4 w-4 animate-spin rounded-full border-2 border-current/30 border-t-current"></span>
                ${escaparHTML(texto)}
            </span>`;

        return textoOriginal;
    }


    function desactivarCargaBoton(
        boton,
        textoOriginal
    ) {

        if (!boton) {
            return;
        }

        boton.disabled =
            false;

        boton.classList.remove(
            'opacity-70',
            'cursor-not-allowed'
        );

        if (
            textoOriginal !==
            null
        ) {
            boton.innerHTML =
                textoOriginal;
        }
    }


    // =============================================================
    // ENDPOINTS SEGÚN TIPO
    // =============================================================

    function endpointListado(
        tipo
    ) {

        const mapa = {

            profesor:
                API.profesores,

            grado:
                API.grados,

            aula:
                API.aulas,

            curso:
                API.cursos
        };

        return mapa[tipo] || null;
    }


    function endpointHorario(
        tipo,
        id
    ) {

        const mapa = {

            profesor:
                API.horarioProfesor,

            grado:
                API.horarioGrado,

            aula:
                API.horarioAula,

            curso:
                API.horarioCurso
        };


        const funcion =
            mapa[tipo];

        return funcion
            ? funcion(id)
            : null;
    }


    // =============================================================
    // CUSTOM SELECT
    // =============================================================

    function cerrarCustomSelect() {

        customSelect?.classList.remove(
            'open'
        );

        customButton?.setAttribute(
            'aria-expanded',
            'false'
        );
    }


    function abrirCustomSelect() {

        if (
            !customButton ||
            customButton.disabled
        ) {
            return;
        }


        customSelect?.classList.add(
            'open'
        );

        customButton.setAttribute(
            'aria-expanded',
            'true'
        );


        if (
            customSearch
        ) {

            customSearch.value =
                '';

            customSearch.placeholder =
                'Buscar por nombre o código...';
        }


        renderOpcionesCustom();


        setTimeout(
            () => {
                customSearch?.focus();
            },
            30
        );
    }


    function actualizarCustomSelectVisual() {

        if (
            !customButton
        ) {
            return;
        }


        customButton.disabled =
            selector.disabled;


        if (
            customSmall
        ) {

            customSmall.textContent =
                selectorLabel?.textContent ||
                'Seleccionar';
        }


        if (
            customIcon
        ) {

            customIcon.innerHTML =
                iconoTipo(
                    tipoSeleccionado ||
                    'curso'
                );
        }


        const opcion =
            selector.options[
                selector.selectedIndex
            ];


        if (
            customText
        ) {

            customText.textContent =
                opcion?.textContent ||
                'Seleccionar';
        }
    }


    function renderOpcionesCustom() {

        if (
            !customOptions
        ) {
            return;
        }


        if (
            cargandoOpciones
        ) {

            customOptions.innerHTML = `
                <div class="custom-select-empty">
                    Buscando...
                </div>
            `;

            return;
        }


        if (
            !opcionesSelectorActuales.length
        ) {

            customOptions.innerHTML = `
                <div class="custom-select-empty">
                    No hay resultados disponibles
                </div>
            `;

            return;
        }


        let html =
            '';

        let ultimoNivel =
            null;


        opcionesSelectorActuales.forEach(
            item => {

                if (
                    tipoSeleccionado ===
                    'grado' &&
                    item.nivel &&
                    item.nivel !==
                    ultimoNivel
                ) {

                    ultimoNivel =
                        item.nivel;


                    html += `
                        <div class="custom-select-title">
                            ${escaparHTML(
                                item.nivel
                            )}
                        </div>
                    `;
                }


                const seleccionado =
                    String(
                        selector.value
                    ) ===
                    String(
                        item.id
                    );


                html += `

                    <button
                        type="button"
                        class="
                            custom-select-option
                            ${
                                seleccionado
                                    ? 'selected'
                                    : ''
                            }
                        "
                        data-id="${escaparHTML(
                            item.id
                        )}"
                    >

                        <span class="custom-option-icon">

                            ${iconoTipo(
                                tipoSeleccionado
                            )}

                        </span>


                        <span
                            class="custom-option-text"
                            style="
                                display:flex;
                                flex-direction:column;
                                gap:2px;
                            "
                        >

                            <span>

                                ${escaparHTML(
                                    item.nombre
                                )}

                            </span>


                            ${
                                item.codigo

                                    ? `
                                        <span
                                            style="
                                                font-size:9px;
                                                color:#94A3B8;
                                                font-weight:800;
                                                letter-spacing:.04em;
                                            "
                                        >
                                            CÓDIGO:
                                            ${escaparHTML(
                                                item.codigo
                                            )}
                                        </span>
                                    `

                                    : ''
                            }

                        </span>


                        <svg
                            class="custom-option-check"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.2"
                        >
                            <path d="m5 12 4 4L19 6"/>
                        </svg>

                    </button>
                `;
            }
        );


        customOptions.innerHTML =
            html;


        customOptions
            .querySelectorAll(
                '.custom-select-option'
            )
            .forEach(
                button => {

                    button.addEventListener(
                        'click',
                        () => {

                            selector.value =
                                button.dataset.id;


                            selector.dispatchEvent(
                                new Event(
                                    'change',
                                    {
                                        bubbles:
                                            true
                                    }
                                )
                            );


                            actualizarCustomSelectVisual();

                            cerrarCustomSelect();
                        }
                    );
                }
            );
    }


    // =============================================================
    // CARGAR OPCIONES REALES
    // =============================================================

    async function cargarOpciones(
        busqueda = ''
    ) {

        if (
            !institucionSeleccionada ||
            !tipoSeleccionado
        ) {
            return;
        }


        const endpoint =
            endpointListado(
                tipoSeleccionado
            );


        if (!endpoint) {
            return;
        }


        const numeroConsulta =
            ++consultaBusquedaActual;


        cargandoOpciones =
            true;


        renderOpcionesCustom();


        try {

            const respuesta =
                await apiGet(
                    endpoint,
                    {
                        institucion:
                            institucionSeleccionada,

                        search:
                            busqueda.trim()
                    }
                );


            if (
                numeroConsulta !==
                consultaBusquedaActual
            ) {
                return;
            }


            const lista =
                Array.isArray(
                    respuesta?.data
                )
                    ? respuesta.data
                    : [];


            opcionesSelectorActuales =
                lista.map(
                    registro => {

                        const nivel =
                            tipoSeleccionado ===
                            'grado'
                                ? obtenerNivelGrado(
                                    registro
                                )
                                : '';


                        return {

                            id:
                                String(
                                    registro.id
                                ),

                            registro,

                            codigo:
                                codigoRegistro(
                                    registro
                                ),

                            nombre:
                                etiquetaRegistro(
                                    tipoSeleccionado,
                                    registro
                                ),

                            nivel:
                                nivel
                        };
                    }
                );


            actualizarSelectNativo();

        } catch (error) {

            console.error(
                'Error cargando opciones:',
                error
            );


            opcionesSelectorActuales =
                [];


            mostrarMensaje(
                obtenerMensajeError(
                    error,
                    'No se pudieron cargar las opciones.'
                )
            );

        } finally {

            if (
                numeroConsulta ===
                consultaBusquedaActual
            ) {

                cargandoOpciones =
                    false;

                renderOpcionesCustom();
            }
        }
    }


    function actualizarSelectNativo() {

        const valorActual =
            selector.value;


        selector.innerHTML =
            '';


        const etiquetas = {

            profesor:
                'profesor',

            grado:
                'grado',

            aula:
                'aula',

            curso:
                'curso'
        };


        const placeholder =
            document.createElement(
                'option'
            );


        placeholder.value =
            '';

        placeholder.textContent =
            `Seleccione ${
                etiquetas[
                    tipoSeleccionado
                ] ||
                'opción'
            }`;


        selector.appendChild(
            placeholder
        );


        opcionesSelectorActuales.forEach(
            item => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    item.id;

                option.textContent =
                    item.nombre;


                selector.appendChild(
                    option
                );
            }
        );


        const existeValor =
            opcionesSelectorActuales.some(
                item =>
                    String(item.id) ===
                    String(valorActual)
            );


        selector.value =
            existeValor
                ? valorActual
                : '';


        selector.disabled =
            false;


        actualizarCustomSelectVisual();
    }


    // =============================================================
    // ACTUALIZAR SELECTOR
    // =============================================================

    async function actualizarSelector() {

        limpiarResultado();

        cerrarCustomSelect();


        selector.innerHTML =
            '';

        selector.disabled =
            true;

        opcionesSelectorActuales =
            [];


        if (
            !institucionSeleccionada
        ) {

            if (
                selectorLabel
            ) {

                selectorLabel.textContent =
                    'Seleccionar';
            }


            selector.innerHTML = `
                <option value="">
                    Primero selecciona una institución
                </option>
            `;


            actualizarCustomSelectVisual();

            return;
        }


        if (
            !tipoSeleccionado
        ) {

            if (
                selectorLabel
            ) {

                selectorLabel.textContent =
                    'Seleccionar';
            }


            selector.innerHTML = `
                <option value="">
                    Selecciona el tipo de consulta
                </option>
            `;


            actualizarCustomSelectVisual();

            return;
        }


        const etiquetas = {

            profesor:
                'Profesor',

            grado:
                'Grado',

            aula:
                'Aula',

            curso:
                'Curso'
        };


        if (
            selectorLabel
        ) {

            selectorLabel.textContent =
                etiquetas[
                    tipoSeleccionado
                ];
        }


        selector.innerHTML = `
            <option value="">
                Cargando datos...
            </option>
        `;


        actualizarCustomSelectVisual();


        await cargarOpciones();
    }


    // =============================================================
    // INFORMACIÓN DEL RESULTADO
    // =============================================================

    function mostrarInformacion(
        registro,
        cantidadClases
    ) {

        const etiquetas = {

            profesor:
                'Profesor',

            grado:
                'Grado',

            aula:
                'Aula',

            curso:
                'Curso'
        };


        if (
            consultaTipoInfo
        ) {

            consultaTipoInfo.textContent =
                etiquetas[
                    tipoSeleccionado
                ] ||
                'Resultado';
        }


        if (
            consultaNombre
        ) {

            consultaNombre.textContent =
                etiquetaRegistro(
                    tipoSeleccionado,
                    registro
                );
        }


        if (
            consultaDetalle
        ) {

            consultaDetalle.textContent =
                `${cantidadClases} ${
                    cantidadClases ===
                    1
                        ? 'clase programada'
                        : 'clases programadas'
                }`;
        }


        const institucionTexto =
            institucionSeleccionada ===
            'colegio'
                ? 'Colegio'
                : 'Academia';


        if (
            consultaInstitucion
        ) {

            consultaInstitucion.textContent =
                institucionTexto;


            consultaInstitucion.className =

                institucionSeleccionada ===
                'colegio'

                    ? 'inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-[#1B3A6B]'

                    : 'inline-flex w-fit items-center rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-[#DB0808]';
        }


        accionesDescarga?.classList.toggle(
            'visible',
            tipoSeleccionado ===
            'profesor' &&
            cantidadClases >
            0
        );


        consultaInfo?.classList.remove(
            'hidden'
        );
    }


    // =============================================================
    // CONVERTIR HORARIOS
    // =============================================================

    function aListaHorarios(
        horariosOrganizados
    ) {

        if (
            Array.isArray(
                horariosOrganizados
            )
        ) {

            return horariosOrganizados;
        }


        if (
            !horariosOrganizados ||
            typeof horariosOrganizados !==
            'object'
        ) {

            return [];
        }


        return Object.values(
            horariosOrganizados
        )
            .filter(
                Array.isArray
            )
            .flat();
    }


    function filtrarInstitucionHorario(
        horario
    ) {

        const institucion =
            normalizarTexto(
                horario?.institucion
            );


        if (!institucion) {
            return true;
        }


        return institucion ===
            normalizarTexto(
                institucionSeleccionada
            );
    }


    // =============================================================
    // CONTENIDO DE CLASE
    // =============================================================

    function crearContenidoClase(
        horario
    ) {

        const profesor =
            horario.profesor ||
            (
                tipoSeleccionado ===
                'profesor'
                    ? registroSeleccionadoActual
                    : null
            );


        const curso =
            horario.curso ||
            (
                tipoSeleccionado ===
                'curso'
                    ? registroSeleccionadoActual
                    : null
            );


        const aula =
            horario.aula ||
            (
                tipoSeleccionado ===
                'aula'
                    ? registroSeleccionadoActual
                    : null
            );


        const grado =
            horario.grado ||
            (
                tipoSeleccionado ===
                'grado'
                    ? registroSeleccionadoActual
                    : null
            );


        return `

            <article class="horario-clase">

                <p class="horario-curso">

                    ${escaparHTML(
                        nombreCurso(
                            curso
                        )
                    )}

                </p>


                ${
                    tipoSeleccionado !==
                    'profesor'

                        ? `
                            <p class="horario-profesor">

                                ${escaparHTML(
                                    nombreProfesor(
                                        profesor
                                    )
                                )}

                            </p>
                        `

                        : ''
                }


                <div class="horario-datos">

                    ${
                        grado

                            ? `
                                <p>
                                    ${escaparHTML(
                                        nombreGrado(
                                            grado
                                        )
                                    )}
                                </p>
                            `

                            : ''
                    }


                    <p>

                        ${escaparHTML(
                            nombreAula(
                                aula
                            )
                        )}

                    </p>


                    <p class="horario-hora">

                        ${escaparHTML(
                            horaCorta(
                                horario.hora_inicio
                            )
                        )}

                        -

                        ${escaparHTML(
                            horaCorta(
                                horario.hora_fin
                            )
                        )}

                    </p>

                </div>

            </article>
        `;
    }


    // =============================================================
    // RENDERIZAR HORARIO
    // =============================================================

    function renderizarHorario(
        horarios
    ) {

        horarioRender.innerHTML =
            '';


        const ordenados =
            [...horarios]
                .sort(
                    (a, b) => {

                        const diaA =
                            DIAS.indexOf(
                                normalizarDia(
                                    a.dia_semana
                                )
                            );


                        const diaB =
                            DIAS.indexOf(
                                normalizarDia(
                                    b.dia_semana
                                )
                            );


                        if (
                            diaA !==
                            diaB
                        ) {

                            return diaA -
                                diaB;
                        }


                        return (
                            aMinutos(
                                a.hora_inicio
                            ) -
                            aMinutos(
                                b.hora_inicio
                            )
                        );
                    }
                );


        const grid =
            document.createElement(
                'div'
            );


        grid.className =
            'horario-grid';


        DIAS.forEach(
            (dia, index) => {

                const horariosDia =
                    ordenados.filter(
                        horario =>
                            normalizarDia(
                                horario.dia_semana
                            ) ===
                            dia
                    );


                const columna =
                    document.createElement(
                        'section'
                    );


                columna.className =
                    `horario-dia ${
                        index === 0
                            ? 'dia-activo'
                            : ''
                    }`;


                columna.dataset.diaIndex =
                    index;


                columna.innerHTML = `

                    <div class="horario-dia-header">

                        <h4 class="horario-dia-titulo">
                            ${dia}
                        </h4>

                    </div>


                    <div class="horario-dia-contenido">

                        ${
                            horariosDia.length

                                ? horariosDia
                                    .map(
                                        crearContenidoClase
                                    )
                                    .join('')

                                : `
                                    <div class="horario-vacio">
                                        Sin clases
                                    </div>
                                `
                        }

                    </div>
                `;


                grid.appendChild(
                    columna
                );
            }
        );


        horarioRender.appendChild(
            grid
        );


        horarioContainer.classList.remove(
            'hidden'
        );

        horarioNavegacion?.classList.remove(
            'hidden'
        );


        diaMovilActual =
            0;


        actualizarDiaMovil();
    }

    function renderizarHorarioVacio(
        titulo,
        descripcion
    ) {

        horarioRender.innerHTML = `

            <div class="horario-estado-vacio">

                <div class="horario-estado-vacio-icono">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="5" width="18" height="16" rx="2"></rect>
                        <path d="M8 3v4M16 3v4M3 10h18"></path>
                        <path d="M9 15h6"></path>
                    </svg>
                </div>

                <h3>
                    ${escaparHTML(titulo)}
                </h3>

                <p>
                    ${escaparHTML(descripcion)}
                </p>

            </div>
        `;

        horarioNavegacion?.classList.add(
            'hidden'
        );

        horarioContainer.classList.remove(
            'hidden'
        );
    }


    // =============================================================
    // NAVEGACIÓN MÓVIL
    // =============================================================

    function actualizarDiaMovil() {

        const columnas =
            horarioRender.querySelectorAll(
                '.horario-dia'
            );


        columnas.forEach(
            (columna, index) => {

                columna.classList.toggle(
                    'dia-activo',
                    index ===
                    diaMovilActual
                );
            }
        );


        if (
            horarioDiaActual
        ) {

            const nodoTexto =
                horarioDiaActual.childNodes[0];


            if (
                nodoTexto
            ) {

                nodoTexto.textContent =
                    `${DIAS[diaMovilActual]} `;
            }
        }


        if (
            horarioDiaContador
        ) {

            horarioDiaContador.textContent =
                `${diaMovilActual + 1} de ${DIAS.length}`;
        }


        if (
            btnDiaAnterior
        ) {

            btnDiaAnterior.disabled =
                diaMovilActual ===
                0;
        }


        if (
            btnDiaSiguiente
        ) {

            btnDiaSiguiente.disabled =
                diaMovilActual ===
                DIAS.length -
                1;
        }
    }


    btnDiaAnterior?.addEventListener(
        'click',
        () => {

            if (
                diaMovilActual >
                0
            ) {

                diaMovilActual--;

                actualizarDiaMovil();
            }
        }
    );


    btnDiaSiguiente?.addEventListener(
        'click',
        () => {

            if (
                diaMovilActual <
                DIAS.length -
                1
            ) {

                diaMovilActual++;

                actualizarDiaMovil();
            }
        }
    );


    // =============================================================
    // CONSULTAR HORARIO REAL
    // =============================================================

    async function consultarHorario() {

        mensaje?.classList.add(
            'hidden'
        );


        if (
            !institucionSeleccionada
        ) {

            mostrarMensaje(
                'Selecciona primero una institución.'
            );

            return;
        }


        if (
            !tipoSeleccionado
        ) {

            mostrarMensaje(
                'Selecciona cómo deseas consultar el horario.'
            );

            return;
        }


        const id =
            selector.value;


        if (!id) {

            mostrarMensaje(
                'Selecciona una opción antes de consultar.'
            );

            return;
        }


        const opcion =
            opcionesSelectorActuales.find(
                item =>
                    String(item.id) ===
                    String(id)
            );


        if (!opcion) {

            mostrarMensaje(
                'No se encontró el registro seleccionado.'
            );

            return;
        }


        const endpoint =
            endpointHorario(
                tipoSeleccionado,
                id
            );


        if (!endpoint) {

            mostrarMensaje(
                'No existe una ruta para esta consulta.'
            );

            return;
        }


        const textoBotonOriginal =
            activarCargaBoton(
                btnConsultar,
                'Consultando...'
            );


        try {

            const respuesta =
                await apiGet(
                    endpoint
                );


            const resultado =
                respuesta?.data ||
                {};


            const registro =
                resultado.info ||
                opcion.registro;


            const horarios =
                aListaHorarios(
                    resultado.horarios
                )
                    .filter(
                        filtrarInstitucionHorario
                    );


            registroSeleccionadoActual =
                registro;


            horariosSeleccionadosActuales =
                horarios;


            mostrarInformacion(
                registro,
                horarios.length
            );


            if (
                !horarios.length
            ) {

                mostrarMensaje(
                    'No existen clases programadas para esta consulta en la institución seleccionada.',
                    'info'
                );

                renderizarHorarioVacio(
                    'Sin clases programadas',
                    'La selección no tiene horarios registrados para la institución actual. Prueba con otra opción o consulta a administración.'
                );

                return;
            }


            renderizarHorario(
                horarios
            );

        } catch (error) {

            console.error(
                'Error consultando horario:',
                error
            );


            mostrarMensaje(
                obtenerMensajeError(
                    error,
                    'No se pudo consultar el horario.'
                )
            );

        } finally {

            desactivarCargaBoton(
                btnConsultar,
                textoBotonOriginal
            );
        }
    }


    // =============================================================
    // DESCARGAR PDF REAL
    // =============================================================

    btnDescargarPdf?.addEventListener(
        'click',
        async () => {

            if (
                tipoSeleccionado !==
                'profesor' ||
                !registroSeleccionadoActual ||
                !horariosSeleccionadosActuales.length
            ) {

                mostrarMensaje(
                    'Primero consulta el horario de un profesor.'
                );

                return;
            }


            const profesorId =
                registroSeleccionadoActual.id;


            const textoOriginal =
                activarCargaBoton(
                    btnDescargarPdf,
                    'Descargando PDF...'
                );


            try {

                const url =
                    `${API.horarioProfesorPdf(
                        profesorId
                    )}?institucion=${encodeURIComponent(
                        institucionSeleccionada
                    )}`;


                const respuesta =
                    await fetch(
                        url,
                        {
                            method:
                                'GET',

                            headers: {
                                Accept:
                                    'application/pdf, application/json'
                            }
                        }
                    );


                if (
                    !respuesta.ok
                ) {

                    let mensajeError =
                        'No se pudo descargar el PDF.';


                    try {

                        const data =
                            await respuesta.json();


                        mensajeError =
                            data?.message ||
                            mensajeError;

                    } catch (error) {
                        // La respuesta no fue JSON.
                    }


                    throw new Error(
                        mensajeError
                    );
                }


                const blob =
                    await respuesta.blob();


                const urlTemporal =
                    URL.createObjectURL(
                        blob
                    );


                const enlace =
                    document.createElement(
                        'a'
                    );


                const codigo =
                    codigoRegistro(
                        registroSeleccionadoActual
                    ) ||
                    registroSeleccionadoActual.id;


                enlace.href =
                    urlTemporal;


                enlace.download =
                    `Horario_${limpiarNombreArchivo(
                        codigo
                    )}_${institucionSeleccionada}.pdf`;


                document.body.appendChild(
                    enlace
                );


                enlace.click();


                enlace.remove();


                setTimeout(
                    () => {

                        URL.revokeObjectURL(
                            urlTemporal
                        );
                    },
                    1000
                );


                mostrarMensaje(
                    'PDF preparado correctamente.',
                    'info'
                );

            } catch (error) {

                console.error(
                    'Error descargando PDF:',
                    error
                );


                mostrarMensaje(
                    obtenerMensajeError(
                        error,
                        'No se pudo descargar el PDF.'
                    )
                );

            } finally {

                desactivarCargaBoton(
                    btnDescargarPdf,
                    textoOriginal
                );
            }
        }
    );


    // =============================================================
    // DESCARGAR IMAGEN REAL
    // =============================================================

    btnDescargarImagen?.addEventListener(
        'click',
        async () => {

            if (
                tipoSeleccionado !==
                'profesor' ||
                !registroSeleccionadoActual ||
                !horariosSeleccionadosActuales.length
            ) {

                mostrarMensaje(
                    'Primero consulta el horario de un profesor.'
                );

                return;
            }


            if (
                typeof html2canvas ===
                'undefined'
            ) {

                mostrarMensaje(
                    'No se pudo cargar el generador de imagen.'
                );

                return;
            }


            const profesorId =
                registroSeleccionadoActual.id;


            const textoOriginal =
                activarCargaBoton(
                    btnDescargarImagen,
                    'Generando imagen...'
                );


            let iframe =
                null;


            try {

                // =================================================
                // PEDIR AL BACKEND EL HTML PROFESIONAL
                // =================================================

                const respuesta =
                    await apiGet(
                        API.horarioProfesorImagen(
                            profesorId
                        ),
                        {
                            institucion:
                                institucionSeleccionada
                        }
                    );


                const html =
                    respuesta?.data?.html;


                if (!html) {

                    throw new Error(
                        'El servidor no devolvió el contenido para generar la imagen.'
                    );
                }


                // =================================================
                // CREAR IFRAME FUERA DE LA PANTALLA
                // =================================================

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
                            '-10000px',

                        top:
                            '0',

                        width:
                            '1250px',

                        height:
                            '2200px',

                        border:
                            '0',

                        opacity:
                            '0',

                        pointerEvents:
                            'none',

                        background:
                            '#FFFFFF'
                    }
                );


                document.body.appendChild(
                    iframe
                );


                const documentoIframe =
                    iframe.contentDocument ||
                    iframe.contentWindow?.document;


                if (
                    !documentoIframe
                ) {

                    throw new Error(
                        'No se pudo preparar la vista de la imagen.'
                    );
                }


                // =================================================
                // CARGAR EL HTML
                // =================================================

                documentoIframe.open();

                documentoIframe.write(
                    html
                );

                documentoIframe.close();


                await new Promise(
                    resolve => {

                        setTimeout(
                            resolve,
                            200
                        );
                    }
                );


                if (
                    documentoIframe.fonts?.ready
                ) {

                    try {

                        await documentoIframe.fonts.ready;

                    } catch (error) {

                        console.warn(
                            'No se pudo esperar la carga de fuentes.',
                            error
                        );
                    }
                }


                // =================================================
                // OBTENER CONTENEDOR DE EXPORTACIÓN
                // =================================================

                const contenedor =
                    documentoIframe.getElementById(
                        'horario-container'
                    );


                if (
                    !contenedor
                ) {

                    throw new Error(
                        'No se encontró el horario preparado para exportar.'
                    );
                }


                // =================================================
                // GENERAR PNG
                // =================================================

                const canvas =
                    await html2canvas(
                        contenedor,
                        {
                            scale:
                                2,

                            backgroundColor:
                                '#FFFFFF',

                            useCORS:
                                true,

                            logging:
                                false,

                            scrollX:
                                0,

                            scrollY:
                                0,

                            windowWidth:
                                1250
                        }
                    );


                const enlace =
                    document.createElement(
                        'a'
                    );


                enlace.download =
                    respuesta?.data?.nombre_archivo ||
                    `Horario_${limpiarNombreArchivo(
                        nombreProfesor(
                            registroSeleccionadoActual
                        )
                    )}.png`;


                enlace.href =
                    canvas.toDataURL(
                        'image/png',
                        1
                    );


                document.body.appendChild(
                    enlace
                );


                enlace.click();


                enlace.remove();


                mostrarMensaje(
                    'Imagen preparada correctamente.',
                    'info'
                );

            } catch (error) {

                console.error(
                    'Error generando imagen:',
                    error
                );


                mostrarMensaje(
                    obtenerMensajeError(
                        error,
                        'No se pudo generar la imagen del horario.'
                    )
                );

            } finally {

                iframe?.remove();


                desactivarCargaBoton(
                    btnDescargarImagen,
                    textoOriginal
                );
            }
        }
    );


    // =============================================================
    // INSTITUCIÓN
    // =============================================================

    botonesInstitucion.forEach(
        boton => {

            boton.addEventListener(
                'click',
                async () => {

                    institucionSeleccionada =
                        boton.dataset.institucion;


                    tipoSeleccionado =
                        '';


                    selector.value =
                        '';


                    if (
                        customSearch
                    ) {

                        customSearch.value =
                            '';
                    }


                    actualizarBotonesInstitucion();

                    actualizarDisponibilidadTipos();

                    actualizarBotonesTipo();


                    await actualizarSelector();
                }
            );
        }
    );


    // =============================================================
    // TIPO
    // =============================================================

    botonesTipo.forEach(
        boton => {

            boton.addEventListener(
                'click',
                async () => {

                    if (
                        !institucionSeleccionada
                    ) {

                        mostrarMensaje(
                            'Selecciona primero Colegio o Academia.'
                        );

                        return;
                    }


                    tipoSeleccionado =
                        boton.dataset.tipo;


                    if (
                        customSearch
                    ) {

                        customSearch.value =
                            '';
                    }


                    actualizarBotonesTipo();


                    await actualizarSelector();
                }
            );
        }
    );


    // =============================================================
    // SELECT NATIVO
    // =============================================================

    selector.addEventListener(
        'change',
        () => {

            actualizarCustomSelectVisual();

            limpiarResultado();
        }
    );


    // =============================================================
    // CUSTOM SELECT
    // =============================================================

    customButton?.addEventListener(
        'click',
        () => {

            if (
                customSelect?.classList.contains(
                    'open'
                )
            ) {

                cerrarCustomSelect();

            } else {

                abrirCustomSelect();
            }
        }
    );


    customSearch?.addEventListener(
        'input',
        () => {

            clearTimeout(
                temporizadorBusqueda
            );


            const termino =
                customSearch.value.trim();


            temporizadorBusqueda =
                setTimeout(
                    () => {

                        cargarOpciones(
                            termino
                        );
                    },
                    300
                );
        }
    );


    customSearch?.addEventListener(
        'keydown',
        event => {

            if (
                event.key ===
                'Enter'
            ) {

                event.preventDefault();


                const primera =
                    customOptions?.querySelector(
                        '.custom-select-option'
                    );


                primera?.click();
            }


            if (
                event.key ===
                'Escape'
            ) {

                cerrarCustomSelect();
            }
        }
    );


    document.addEventListener(
        'click',
        event => {

            if (
                !event.target.closest(
                    '#custom-select'
                )
            ) {

                cerrarCustomSelect();
            }
        }
    );


    // =============================================================
    // CONSULTAR
    // =============================================================

    btnConsultar.addEventListener(
        'click',
        consultarHorario
    );


    btnNuevaConsulta?.addEventListener(
        'click',
        reiniciarConsulta
    );


    // =============================================================
    // SWIPE MÓVIL
    // =============================================================

    horarioRender.addEventListener(
        'touchstart',
        event => {

            touchInicioX =
                event.changedTouches[
                    0
                ]?.clientX ??
                null;
        },
        {
            passive:
                true
        }
    );


    horarioRender.addEventListener(
        'touchend',
        event => {

            if (
                touchInicioX ===
                null
            ) {

                return;
            }


            const finX =
                event.changedTouches[
                    0
                ]?.clientX ??
                touchInicioX;


            const diferencia =
                finX -
                touchInicioX;


            if (
                Math.abs(
                    diferencia
                ) <
                45
            ) {

                touchInicioX =
                    null;

                return;
            }


            if (
                diferencia <
                0 &&
                diaMovilActual <
                DIAS.length -
                1
            ) {

                diaMovilActual++;

            } else if (
                diferencia >
                0 &&
                diaMovilActual >
                0
            ) {

                diaMovilActual--;
            }


            actualizarDiaMovil();


            touchInicioX =
                null;
        },
        {
            passive:
                true
        }
    );


    // =============================================================
    // INICIO
    // =============================================================

    actualizarDisponibilidadTipos();

    actualizarBotonesInstitucion();

    actualizarBotonesTipo();

    actualizarSelector();

});
