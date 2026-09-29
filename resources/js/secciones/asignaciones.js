document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // ELEMENTOS
    // =========================================================

    const profesorSelect =
        document.getElementById('asignacion-profesor');

    const institucionSelect =
        document.getElementById('asignacion-institucion');

    const cursoSelect =
        document.getElementById('asignacion-curso');

    const gradoSelect =
        document.getElementById('asignacion-grado');

    const horasInput =
        document.getElementById('asignacion-horas');

    const rolSelect =
        document.getElementById('asignacion-rol');

    const estadoSelect =
        document.getElementById('asignacion-estado');

    const observacionesInput =
        document.getElementById('asignacion-observaciones');

    const observacionesContador =
        document.getElementById(
            'asignacion-observaciones-contador'
        );

    const guardarButton =
        document.getElementById('guardar-asignacion');

    const guardarTexto =
        document.getElementById('guardar-asignacion-texto');

    const limpiarButton =
        document.getElementById('limpiar-asignacion');

    const buscarInput =
        document.getElementById('buscar-asignacion');

    const filtroInstitucion =
        document.getElementById(
            'filtro-institucion-asignaciones'
        );

    const tabla =
        document.getElementById('asignaciones-body');

    const estadoAsignacion =
        document.getElementById('estado-asignacion');


    if (
        !profesorSelect ||
        !institucionSelect ||
        !cursoSelect ||
        !gradoSelect ||
        !horasInput ||
        !rolSelect ||
        !estadoSelect ||
        !guardarButton ||
        !tabla
    ) {
        return;
    }


    // =========================================================
    // ESTADO
    // =========================================================

    let profesores = [];
    let cursos = [];
    let grados = [];
    let asignaciones = [];

    let asignacionEditandoId = null;
    let cargando = false;


    // =========================================================
    // UTILIDADES
    // =========================================================

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
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }


    function obtenerMensajeError(error) {

        const response =
            error?.response?.data;

        if (!response) {
            return 'No se pudo conectar con el servidor.';
        }


        if (response.error) {

            if (
                typeof response.error === 'string'
            ) {
                return response.error;
            }
        }


        if (response.message) {
            return response.message;
        }


        if (response.errors) {

            const mensajes =
                Object.values(response.errors)
                    .flat();

            if (mensajes.length) {
                return mensajes[0];
            }
        }


        return 'Ocurrió un error inesperado.';
    }


    function nombreProfesor(profesor) {

        if (!profesor) {
            return 'Profesor no disponible';
        }


        const partes = [
            profesor.nombre,
            profesor.apellido_paterno,
            profesor.apellido_materno
        ]
            .filter(Boolean);


        if (partes.length) {
            return partes.join(' ');
        }


        return (
            profesor.nombre_completo ||
            profesor.name ||
            'Profesor'
        );
    }


    function nombreCurso(curso) {

        return (
            curso?.nombre ||
            curso?.nombre_completo ||
            curso?.codigo ||
            'Curso'
        );
    }


    function nombreGrado(grado) {

        if (!grado) {
            return 'Grado no disponible';
        }


        return (
            grado.nombre_completo ||
            grado.nombre ||
            grado.descripcion ||
            `Grado ${grado.id}`
        );
    }


    function capitalizar(valor) {

        const texto =
            String(valor ?? '');

        if (!texto) {
            return '';
        }


        return (
            texto.charAt(0).toUpperCase() +
            texto.slice(1)
        );
    }


    function obtenerInstitucionProfesor(profesor) {

        return normalizarTexto(
            profesor?.institucion ??
            profesor?.instituciones ??
            ''
        );
    }


    // =========================================================
    // AXIOS
    // =========================================================

    function axiosDisponible() {

        if (!window.axios) {

            console.error(
                'Axios no está disponible.'
            );

            mostrarEstado(
                false,
                'No se pudo inicializar la conexión con la API.'
            );

            return false;
        }


        return true;
    }


    // =========================================================
    // RESPUESTAS PAGINADAS
    // =========================================================

    function extraerPaginador(response) {

        const cuerpo =
            response?.data;

        if (!cuerpo) {
            return null;
        }


        /*
         * Formato actual:
         *
         * {
         *   status: "success",
         *   data: {
         *      current_page: 1,
         *      data: [...]
         *   }
         * }
         */

        if (
            cuerpo.data &&
            typeof cuerpo.data === 'object' &&
            !Array.isArray(cuerpo.data) &&
            Array.isArray(cuerpo.data.data)
        ) {
            return cuerpo.data;
        }


        /*
         * Soporte por si algún endpoint
         * devuelve directamente el paginator.
         */

        if (
            typeof cuerpo === 'object' &&
            Array.isArray(cuerpo.data) &&
            cuerpo.current_page !== undefined
        ) {
            return cuerpo;
        }


        return null;
    }


    function extraerColeccion(response) {

        const cuerpo =
            response?.data;


        if (!cuerpo) {
            return [];
        }


        const paginador =
            extraerPaginador(response);


        if (paginador) {
            return paginador.data;
        }


        if (
            Array.isArray(cuerpo.data)
        ) {
            return cuerpo.data;
        }


        if (
            Array.isArray(cuerpo)
        ) {
            return cuerpo;
        }


        return [];
    }


    async function cargarTodasLasPaginas(url, params = {}) {

        const primeraRespuesta =
            await window.axios.get(
                url,
                {
                    params: {
                        ...params,
                        page: 1
                    }
                }
            );


        const paginador =
            extraerPaginador(
                primeraRespuesta
            );


        if (!paginador) {

            return extraerColeccion(
                primeraRespuesta
            );
        }


        let registros = [
            ...(
                Array.isArray(paginador.data)
                    ? paginador.data
                    : []
            )
        ];


        const paginaActual =
            Number(
                paginador.current_page ||
                1
            );


        const ultimaPagina =
            Number(
                paginador.last_page ||
                paginaActual
            );


        if (ultimaPagina <= paginaActual) {
            return registros;
        }


        for (
            let pagina =
                paginaActual + 1;

            pagina <= ultimaPagina;

            pagina++
        ) {

            const respuesta =
                await window.axios.get(
                    url,
                    {
                        params: {
                            ...params,
                            page: pagina
                        }
                    }
                );


            registros.push(
                ...extraerColeccion(
                    respuesta
                )
            );
        }


        return registros;
    }


    // =========================================================
    // ICONOS CUSTOM SELECT
    // =========================================================

    function iconoSelector(tipo) {

        const iconos = {

            profesor: `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M20 21a8 8 0 0 0-16 0"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            `,

            institucion: `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M3 21h18"/>
                    <path d="M5 21V8l7-4 7 4v13"/>
                    <path d="M9 21v-5h6v5"/>
                </svg>
            `,

            curso: `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
                </svg>
            `,

            grado: `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m2 10 10-5 10 5-10 5Z"/>
                    <path d="M6 12v5c3 2 9 2 12 0v-5"/>
                </svg>
            `,

            rol: `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="M4 21v-2a4 4 0 0 1 4-4h8a4 4 0 0 1 4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                    <path d="m16 11 2 2 4-4"/>
                </svg>
            `,

            estado: `
                <svg
                    width="18"
                    height="18"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 2.5 2.5L16 9"/>
                </svg>
            `
        };


        return (
            iconos[tipo] ||
            iconos.curso
        );
    }


    // =========================================================
    // CUSTOM SELECT
    // =========================================================

    function obtenerWrapperSelect(select) {

        if (!select) {
            return null;
        }


        return document.querySelector(
            `[data-custom-select="${select.id}"]`
        );
    }


    function cerrarCustomSelect(wrapper) {

        if (!wrapper) {
            return;
        }


        wrapper.classList.remove('open');


        const trigger =
            wrapper.querySelector(
                '.nl-select-trigger'
            );


        trigger?.setAttribute(
            'aria-expanded',
            'false'
        );
    }


    function cerrarTodosCustomSelect(
        excepto = null
    ) {

        document
            .querySelectorAll(
                '[data-custom-select]'
            )
            .forEach(wrapper => {

                if (wrapper !== excepto) {
                    cerrarCustomSelect(wrapper);
                }
            });
    }


    function renderizarOpcionesCustom(
        wrapper,
        busqueda = ''
    ) {

        if (!wrapper) {
            return;
        }


        const select =
            document.getElementById(
                wrapper.dataset.customSelect
            );


        const contenedor =
            wrapper.querySelector(
                '.nl-select-options'
            );


        if (
            !select ||
            !contenedor
        ) {
            return;
        }


        const textoBusqueda =
            normalizarTexto(busqueda);


        const opciones =
            Array.from(select.options)
                .filter(option => {

                    if (
                        !option.value ||
                        option.disabled
                    ) {
                        return false;
                    }


                    return normalizarTexto(
                        option.textContent
                    ).includes(
                        textoBusqueda
                    );
                });


        if (!opciones.length) {

            contenedor.innerHTML = `
                <div class="nl-select-empty">
                    No hay opciones disponibles
                </div>
            `;

            return;
        }


        contenedor.innerHTML =
            opciones
                .map(option => {

                    const seleccionada =
                        String(select.value) ===
                        String(option.value);


                    return `
                        <button
                            type="button"
                            class="
                                nl-option
                                ${
                                    seleccionada
                                        ? 'selected'
                                        : ''
                                }
                            "
                            data-value="${escaparHTML(option.value)}"
                        >

                            <span class="nl-option-icon">
                                ${
                                    iconoSelector(
                                        wrapper.dataset.icon
                                    )
                                }
                            </span>

                            <span class="nl-option-text">
                                ${
                                    escaparHTML(
                                        option.textContent
                                    )
                                }
                            </span>

                            <svg
                                class="nl-option-check"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="m5 12 4 4L19 6"/>
                            </svg>

                        </button>
                    `;
                })
                .join('');


        contenedor
            .querySelectorAll('.nl-option')
            .forEach(boton => {

                boton.addEventListener(
                    'click',
                    () => {

                        select.value =
                            boton.dataset.value;


                        select.dispatchEvent(
                            new Event(
                                'change',
                                {
                                    bubbles: true
                                }
                            )
                        );


                        actualizarCustomSelect(
                            select
                        );


                        cerrarCustomSelect(
                            wrapper
                        );
                    }
                );
            });
    }


    function actualizarCustomSelect(select) {

        if (!select) {
            return;
        }


        const wrapper =
            obtenerWrapperSelect(select);


        if (!wrapper) {
            return;
        }


        const trigger =
            wrapper.querySelector(
                '.nl-select-trigger'
            );


        const texto =
            wrapper.querySelector(
                '.nl-select-text'
            );


        if (
            !trigger ||
            !texto
        ) {
            return;
        }


        trigger.disabled =
            select.disabled;


        const opcion =
            select.options[
                select.selectedIndex
            ];


        if (
            opcion &&
            opcion.value
        ) {

            texto.textContent =
                opcion.textContent;

        } else {

            texto.textContent =
                wrapper.dataset.placeholder ||
                'Seleccionar';
        }


        renderizarOpcionesCustom(
            wrapper
        );
    }


    function construirCustomSelect(wrapper) {

        if (
            !wrapper ||
            wrapper.dataset.ready === 'true'
        ) {
            return;
        }


        const selectId =
            wrapper.dataset.customSelect;


        const select =
            document.getElementById(
                selectId
            );


        if (!select) {
            return;
        }


        const etiqueta =
            wrapper.dataset.label ||
            'Seleccionar';


        const placeholder =
            wrapper.dataset.placeholder ||
            'Seleccionar';


        const permitirBusqueda =
            wrapper.dataset.search !== 'false';


        const tipoIcono =
            wrapper.dataset.icon ||
            'curso';


        wrapper.innerHTML = `

            <button
                type="button"
                class="nl-select-trigger"
                aria-expanded="false"
            >

                <span class="nl-select-icon">
                    ${iconoSelector(tipoIcono)}
                </span>

                <span class="nl-select-content">

                    <span class="nl-select-label">
                        ${escaparHTML(etiqueta)}
                    </span>

                    <span class="nl-select-text">
                        ${escaparHTML(placeholder)}
                    </span>

                </span>

                <svg
                    class="nl-select-arrow"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m6 9 6 6 6-6"/>
                </svg>

            </button>


            <div class="nl-select-menu">

                ${
                    permitirBusqueda

                        ? `
                            <div class="nl-select-search-wrap">

                                <svg
                                    class="nl-select-search-icon"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m20 20-3.5-3.5"/>
                                </svg>

                                <input
                                    type="text"
                                    class="nl-select-search"
                                    autocomplete="off"
                                    placeholder="Buscar..."
                                >

                            </div>
                        `

                        : ''
                }

                <div class="nl-select-options"></div>

            </div>
        `;


        wrapper.dataset.ready = 'true';


        const trigger =
            wrapper.querySelector(
                '.nl-select-trigger'
            );


        const buscador =
            wrapper.querySelector(
                '.nl-select-search'
            );


        trigger.addEventListener(
            'click',
            () => {

                if (trigger.disabled) {
                    return;
                }


                cerrarTodosCustomSelect(
                    wrapper
                );


                wrapper.classList.toggle(
                    'open'
                );


                trigger.setAttribute(
                    'aria-expanded',
                    wrapper.classList.contains(
                        'open'
                    )
                        ? 'true'
                        : 'false'
                );


                if (
                    wrapper.classList.contains(
                        'open'
                    )
                ) {

                    renderizarOpcionesCustom(
                        wrapper
                    );


                    if (buscador) {

                        buscador.value = '';


                        setTimeout(
                            () =>
                                buscador.focus(),
                            40
                        );
                    }
                }
            }
        );


        buscador?.addEventListener(
            'input',
            () => {

                renderizarOpcionesCustom(
                    wrapper,
                    buscador.value
                );
            }
        );


        buscador?.addEventListener(
            'keydown',
            event => {

                if (
                    event.key === 'Escape'
                ) {

                    cerrarCustomSelect(
                        wrapper
                    );


                    trigger.focus();
                }
            }
        );


        actualizarCustomSelect(
            select
        );
    }


    function actualizarTodosCustomSelect() {

        [
            profesorSelect,
            institucionSelect,
            cursoSelect,
            gradoSelect,
            rolSelect,
            estadoSelect,
            filtroInstitucion
        ].forEach(
            actualizarCustomSelect
        );
    }


    document
        .querySelectorAll(
            '[data-custom-select]'
        )
        .forEach(
            construirCustomSelect
        );


    document.addEventListener(
        'click',
        event => {

            const wrapper =
                event.target.closest(
                    '[data-custom-select]'
                );


            if (!wrapper) {
                cerrarTodosCustomSelect();
            }
        }
    );


    // =========================================================
    // MENSAJES
    // =========================================================

    function ocultarEstado() {

        if (!estadoAsignacion) {
            return;
        }


        estadoAsignacion.classList.add(
            'hidden'
        );


        estadoAsignacion.innerHTML = '';
    }


    function mostrarEstado(
        valido,
        mensaje
    ) {

        if (!estadoAsignacion) {
            return;
        }


        estadoAsignacion.className =
            'border-t border-slate-200 px-5 py-4';


        estadoAsignacion.innerHTML = `

            <div
                class="
                    rounded-xl
                    border
                    px-4 py-3
                    text-sm font-medium
                    ${
                        valido
                            ? `
                                border-emerald-200
                                bg-emerald-50
                                text-emerald-700
                            `
                            : `
                                border-red-200
                                bg-red-50
                                text-red-700
                            `
                    }
                "
            >
                ${escaparHTML(mensaje)}
            </div>
        `;
    }


    // =========================================================
    // PROFESORES
    // =========================================================

    function profesorCompatibleInstitucion(
        profesor,
        institucion
    ) {

        if (!institucion) {
            return true;
        }


        const valor =
            obtenerInstitucionProfesor(
                profesor
            );


        /*
         * Si el backend no tiene información
         * de institución en el profesor,
         * no lo ocultamos.
         */

        if (!valor) {
            return true;
        }


        return (
            valor === institucion ||
            valor === 'ambos'
        );
    }


    function cargarSelectProfesores() {

        const valorAnterior =
            profesorSelect.value;


        const institucion =
            normalizarTexto(
                institucionSelect.value
            );


        profesorSelect.innerHTML = `
            <option value="">
                Seleccionar profesor
            </option>
        `;


        profesores
            .filter(profesor => {

                return (
                    normalizarTexto(
                        profesor.estado
                    ) === 'activo' &&
                    profesorCompatibleInstitucion(
                        profesor,
                        institucion
                    )
                );
            })
            .sort(
                (a, b) =>
                    nombreProfesor(a)
                        .localeCompare(
                            nombreProfesor(b),
                            'es'
                        )
            )
            .forEach(profesor => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    profesor.id;


                const codigo =
                    profesor.codigo
                        ? ` · ${profesor.codigo}`
                        : '';


                option.textContent =
                    `${nombreProfesor(profesor)}${codigo}`;


                profesorSelect.appendChild(
                    option
                );
            });


        const existeAnterior =
            Array.from(
                profesorSelect.options
            ).some(
                option =>
                    String(option.value) ===
                    String(valorAnterior)
            );


        profesorSelect.value =
            existeAnterior
                ? valorAnterior
                : '';


        actualizarCustomSelect(
            profesorSelect
        );
    }


    // =========================================================
    // CURSOS
    // =========================================================

    function cargarSelectCursos() {

        const valorAnterior =
            cursoSelect.value;


        const institucion =
            normalizarTexto(
                institucionSelect.value
            );


        cursoSelect.innerHTML = `
            <option value="">
                Seleccionar curso
            </option>
        `;


        if (!institucion) {

            cursoSelect.disabled = true;

            actualizarCustomSelect(
                cursoSelect
            );

            return;
        }


        const filtrados =
            cursos
                .filter(curso => {

                    if (
                        curso.activo === false ||
                        Number(curso.activo) === 0
                    ) {
                        return false;
                    }


                    const nivel =
                        normalizarTexto(
                            curso.nivel
                        );


                    if (
                        institucion ===
                        'academia'
                    ) {

                        return (
                            nivel === 'academia' ||
                            nivel === 'todos'
                        );
                    }


                    if (
                        institucion ===
                        'colegio'
                    ) {

                        return [
                            'primaria',
                            'secundaria',
                            'todos'
                        ].includes(
                            nivel
                        );
                    }


                    return true;
                })
                .sort(
                    (a, b) =>
                        nombreCurso(a)
                            .localeCompare(
                                nombreCurso(b),
                                'es'
                            )
                );


        filtrados.forEach(
            curso => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    curso.id;


                option.textContent =
                    nombreCurso(curso);


                cursoSelect.appendChild(
                    option
                );
            }
        );


        cursoSelect.disabled =
            filtrados.length === 0;


        const existeAnterior =
            Array.from(
                cursoSelect.options
            ).some(
                option =>
                    String(option.value) ===
                    String(valorAnterior)
            );


        cursoSelect.value =
            existeAnterior
                ? valorAnterior
                : '';


        actualizarCustomSelect(
            cursoSelect
        );
    }


    // =========================================================
    // GRADOS
    // =========================================================

    function cargarSelectGrados() {

        const valorAnterior =
            gradoSelect.value;


        const institucion =
            normalizarTexto(
                institucionSelect.value
            );


        const curso =
            cursos.find(
                item =>
                    String(item.id) ===
                    String(cursoSelect.value)
            );


        const nivelCurso =
            normalizarTexto(
                curso?.nivel
            );


        gradoSelect.innerHTML = `
            <option value="">
                Seleccionar grado
            </option>
        `;


        if (!institucion) {

            gradoSelect.disabled = true;

            actualizarCustomSelect(
                gradoSelect
            );

            return;
        }


        let filtrados =
            grados
                .filter(grado => {

                    if (
                        grado.activo === false ||
                        Number(grado.activo) === 0
                    ) {
                        return false;
                    }


                    const nivel =
                        normalizarTexto(
                            grado.nivel
                        );


                    if (
                        institucion ===
                        'academia'
                    ) {

                        return (
                            nivel === 'academia'
                        );
                    }


                    if (
                        institucion ===
                        'colegio'
                    ) {

                        return [
                            'primaria',
                            'secundaria'
                        ].includes(
                            nivel
                        );
                    }


                    return true;
                });


        /*
         * Si se eligió un curso de nivel específico,
         * mostramos solamente grados compatibles.
         */

        if (
            nivelCurso &&
            nivelCurso !== 'todos'
        ) {

            filtrados =
                filtrados.filter(
                    grado =>
                        normalizarTexto(
                            grado.nivel
                        ) === nivelCurso
                );
        }


        filtrados
            .sort(
                (a, b) => {

                    const nivelA =
                        normalizarTexto(
                            a.nivel
                        );


                    const nivelB =
                        normalizarTexto(
                            b.nivel
                        );


                    if (
                        nivelA !== nivelB
                    ) {

                        return nivelA
                            .localeCompare(
                                nivelB,
                                'es'
                            );
                    }


                    return nombreGrado(a)
                        .localeCompare(
                            nombreGrado(b),
                            'es',
                            {
                                numeric: true
                            }
                        );
                }
            )
            .forEach(grado => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    grado.id;


                const nivel =
                    grado.nivel
                        ? capitalizar(
                            grado.nivel
                        )
                        : '';


                option.textContent =
                    nivel
                        ? `${nombreGrado(grado)} · ${nivel}`
                        : nombreGrado(grado);


                gradoSelect.appendChild(
                    option
                );
            });


        gradoSelect.disabled =
            filtrados.length === 0;


        const existeAnterior =
            Array.from(
                gradoSelect.options
            ).some(
                option =>
                    String(option.value) ===
                    String(valorAnterior)
            );


        gradoSelect.value =
            existeAnterior
                ? valorAnterior
                : '';


        actualizarCustomSelect(
            gradoSelect
        );
    }


    // =========================================================
    // CONTADOR OBSERVACIONES
    // =========================================================

    function actualizarContadorObservaciones() {

        if (
            !observacionesInput ||
            !observacionesContador
        ) {
            return;
        }


        observacionesContador.textContent =
            `${observacionesInput.value.length} / 255`;
    }


    // =========================================================
    // CARGAR CATÁLOGOS
    // =========================================================

    async function cargarCatalogos() {

        if (!axiosDisponible()) {
            return;
        }


        try {

            const [
                profesoresAPI,
                cursosAPI,
                gradosAPI
            ] =
                await Promise.all([

                    cargarTodasLasPaginas(
                        '/api/profesores'
                    ),

                    cargarTodasLasPaginas(
                        '/api/cursos'
                    ),

                    cargarTodasLasPaginas(
                        '/api/grados'
                    )
                ]);


            profesores =
                Array.isArray(profesoresAPI)
                    ? profesoresAPI
                    : [];


            cursos =
                Array.isArray(cursosAPI)
                    ? cursosAPI
                    : [];


            grados =
                Array.isArray(gradosAPI)
                    ? gradosAPI
                    : [];


            cargarSelectProfesores();

            cargarSelectCursos();

            cargarSelectGrados();


            actualizarTodosCustomSelect();


        } catch (error) {

            console.error(
                'Error cargando catálogos:',
                error
            );


            mostrarEstado(
                false,
                obtenerMensajeError(error)
            );
        }
    }


    // =========================================================
    // CARGAR ASIGNACIONES
    // =========================================================

    async function cargarAsignaciones() {

        if (!axiosDisponible()) {
            return;
        }


        tabla.innerHTML = `

            <tr>
                <td
                    colspan="8"
                    class="
                        px-5 py-14
                        text-center
                        text-sm
                        text-slate-400
                    "
                >
                    Cargando asignaciones...
                </td>
            </tr>
        `;


        try {

            const response =
                await window.axios.get(
                    '/api/asignaciones'
                );


            const data =
                response?.data?.data;


            asignaciones =
                Array.isArray(data)
                    ? data
                    : [];


            renderizarAsignaciones();


        } catch (error) {

            console.error(
                'Error cargando asignaciones:',
                error
            );


            tabla.innerHTML = `

                <tr>

                    <td
                        colspan="8"
                        class="
                            px-5 py-14
                            text-center
                            text-sm
                            text-red-500
                        "
                    >
                        ${escaparHTML(
                            obtenerMensajeError(error)
                        )}
                    </td>

                </tr>
            `;
        }
    }


    // =========================================================
    // TABLA
    // =========================================================

    function renderizarAsignaciones() {

        tabla.innerHTML = '';


        const textoBusqueda =
            normalizarTexto(
                buscarInput?.value
            );


        const filtro =
            normalizarTexto(
                filtroInstitucion?.value
            );


        const filtradas =
            asignaciones
                .filter(asignacion => {

                    if (
                        filtro &&
                        normalizarTexto(
                            asignacion.institucion
                        ) !== filtro
                    ) {
                        return false;
                    }


                    const profesor =
                        asignacion.profesor;


                    const curso =
                        asignacion.curso;


                    const grado =
                        asignacion.grado;


                    const contenido =
                        normalizarTexto(
                            [
                                nombreProfesor(profesor),
                                nombreCurso(curso),
                                nombreGrado(grado),
                                asignacion.institucion,
                                asignacion.rol,
                                asignacion.horas_asignadas,
                                asignacion.observaciones
                            ].join(' ')
                        );


                    return (
                        !textoBusqueda ||
                        contenido.includes(
                            textoBusqueda
                        )
                    );
                });


        if (!filtradas.length) {

            tabla.innerHTML = `

                <tr>

                    <td
                        colspan="8"
                        class="
                            px-5 py-14
                            text-center
                            text-slate-500
                        "
                    >

                        <div
                            class="
                                flex flex-col
                                items-center
                            "
                        >

                            <div
                                class="
                                    mb-3
                                    flex h-12 w-12
                                    items-center justify-center
                                    rounded-xl
                                    bg-slate-100
                                    text-slate-400
                                "
                            >

                                <svg
                                    class="h-6 w-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >
                                    <path d="M9 6h11M9 12h11M9 18h11"/>
                                    <circle cx="4.5" cy="6" r="1.5"/>
                                    <circle cx="4.5" cy="12" r="1.5"/>
                                    <circle cx="4.5" cy="18" r="1.5"/>
                                </svg>

                            </div>

                            <p class="font-semibold">
                                No hay asignaciones
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-sm
                                    text-slate-400
                                "
                            >
                                No se encontraron registros para mostrar.
                            </p>

                        </div>

                    </td>

                </tr>
            `;


            return;
        }


        filtradas
            .slice()
            .sort(
                (a, b) =>
                    nombreProfesor(
                        a.profesor
                    ).localeCompare(
                        nombreProfesor(
                            b.profesor
                        ),
                        'es'
                    )
            )
            .forEach(asignacion => {

                const profesor =
                    asignacion.profesor;


                const curso =
                    asignacion.curso;


                const grado =
                    asignacion.grado;


                const profesorNombre =
                    nombreProfesor(
                        profesor
                    );


                const iniciales =
                    profesorNombre
                        .split(' ')
                        .filter(Boolean)
                        .map(
                            parte =>
                                parte.charAt(0)
                        )
                        .join('')
                        .substring(
                            0,
                            2
                        )
                        .toUpperCase();


                const activo =
                    asignacion.activo === true ||
                    asignacion.activo === 1 ||
                    asignacion.activo === '1';


                const estadoHTML =
                    activo

                        ? `
                            <span
                                class="
                                    inline-flex
                                    rounded-full
                                    bg-emerald-100
                                    px-2.5 py-1
                                    text-xs
                                    font-medium
                                    text-emerald-700
                                "
                            >
                                Activo
                            </span>
                        `

                        : `
                            <span
                                class="
                                    inline-flex
                                    rounded-full
                                    bg-slate-100
                                    px-2.5 py-1
                                    text-xs
                                    font-medium
                                    text-slate-600
                                "
                            >
                                Inactivo
                            </span>
                        `;


                const institucion =
                    normalizarTexto(
                        asignacion.institucion
                    ) === 'academia'
                        ? 'Academia'
                        : 'Colegio';


                const fila =
                    document.createElement(
                        'tr'
                    );


                fila.className =
                    'asignacion-row asignaciones-table-row';


                fila.dataset.id =
                    asignacion.id;


                fila.innerHTML = `

                    <td class="px-5 py-4">

                        <div
                            class="
                                flex items-center
                                gap-3
                            "
                        >

                            <div
                                class="
                                    flex h-9 w-9
                                    shrink-0
                                    items-center justify-center
                                    rounded-full
                                    bg-blue-50
                                    text-xs
                                    font-bold
                                    text-[#1B3A6B]
                                "
                            >
                                ${escaparHTML(iniciales)}
                            </div>

                            <div class="min-w-0">

                                <p
                                    class="
                                        truncate
                                        text-sm
                                        font-semibold
                                        text-slate-800
                                    "
                                >
                                    ${escaparHTML(
                                        profesorNombre
                                    )}
                                </p>

                                ${
                                    profesor?.codigo
                                        ? `
                                            <p
                                                class="
                                                    mt-0.5
                                                    text-xs
                                                    text-slate-400
                                                "
                                            >
                                                ${escaparHTML(
                                                    profesor.codigo
                                                )}
                                            </p>
                                        `
                                        : ''
                                }

                            </div>

                        </div>

                    </td>


                    <td
                        class="
                            px-5 py-4
                            text-sm
                            text-slate-600
                        "
                    >
                        ${institucion}
                    </td>


                    <td
                        class="
                            px-5 py-4
                            text-sm
                            text-slate-600
                        "
                    >
                        ${escaparHTML(
                            nombreCurso(curso)
                        )}
                    </td>


                    <td
                        class="
                            px-5 py-4
                            text-sm
                            text-slate-600
                        "
                    >
                        ${escaparHTML(
                            nombreGrado(grado)
                        )}
                    </td>


                    <td class="px-5 py-4">

                        <span
                            class="
                                inline-flex
                                rounded-lg
                                bg-blue-50
                                px-2.5 py-1
                                text-xs
                                font-semibold
                                text-blue-700
                            "
                        >
                            ${escaparHTML(
                                asignacion.horas_asignadas
                            )} h
                        </span>

                    </td>


                    <td
                        class="
                            px-5 py-4
                            text-sm
                            text-slate-600
                        "
                    >
                        ${escaparHTML(
                            capitalizar(
                                asignacion.rol
                            )
                        )}
                    </td>


                    <td class="px-5 py-4">
                        ${estadoHTML}
                    </td>


                    <td
                        class="
                            px-5 py-4
                            text-right
                        "
                    >

                        <div
                            class="
                                inline-flex
                                items-center
                                gap-1
                            "
                        >

                            <button
                                type="button"
                                class="
                                    editar-asignacion
                                    inline-flex
                                    h-9 w-9
                                    items-center justify-center
                                    rounded-lg
                                    text-blue-600
                                    transition
                                    hover:bg-blue-50
                                    hover:text-blue-800
                                "
                                data-id="${asignacion.id}"
                                title="Editar"
                            >

                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M12 20h9"/>
                                    <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                                </svg>

                            </button>


                            ${
                                activo

                                    ? `
                                        <button
                                            type="button"
                                            class="
                                                desactivar-asignacion
                                                inline-flex
                                                h-9 w-9
                                                items-center justify-center
                                                rounded-lg
                                                text-red-500
                                                transition
                                                hover:bg-red-50
                                                hover:text-red-700
                                            "
                                            data-id="${asignacion.id}"
                                            title="Desactivar"
                                        >

                                            <svg
                                                class="h-4 w-4"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <circle cx="12" cy="12" r="9"/>
                                                <path d="m9 9 6 6M15 9l-6 6"/>
                                            </svg>

                                        </button>
                                    `

                                    : `
                                        <button
                                            type="button"
                                            class="
                                                activar-asignacion
                                                inline-flex
                                                h-9 w-9
                                                items-center justify-center
                                                rounded-lg
                                                text-emerald-600
                                                transition
                                                hover:bg-emerald-50
                                                hover:text-emerald-800
                                            "
                                            data-id="${asignacion.id}"
                                            title="Activar"
                                        >

                                            <svg
                                                class="h-4 w-4"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            >
                                                <circle cx="12" cy="12" r="9"/>
                                                <path d="m8 12 2.5 2.5L16 9"/>
                                            </svg>

                                        </button>
                                    `
                            }

                        </div>

                    </td>
                `;


                tabla.appendChild(
                    fila
                );
            });
    }


    // =========================================================
    // FORMULARIO
    // =========================================================

    function obtenerPayload() {

        return {

            profesor_id:
                Number(
                    profesorSelect.value
                ),

            institucion:
                institucionSelect.value,

            curso_id:
                Number(
                    cursoSelect.value
                ),

            grado_id:
                Number(
                    gradoSelect.value
                ),

            horas_asignadas:
                Number(
                    horasInput.value
                ),

            rol:
                rolSelect.value,

            activo:
                estadoSelect.value === '1',

            observaciones:
                observacionesInput?.value
                    ?.trim() || null
        };
    }


    function validarFormulario() {

        if (!profesorSelect.value) {

            alert(
                'Selecciona un profesor.'
            );

            return false;
        }


        if (!institucionSelect.value) {

            alert(
                'Selecciona una institución.'
            );

            return false;
        }


        if (!cursoSelect.value) {

            alert(
                'Selecciona un curso.'
            );

            return false;
        }


        if (!gradoSelect.value) {

            alert(
                'Selecciona un grado.'
            );

            return false;
        }


        const horas =
            Number(
                horasInput.value
            );


        if (
            !Number.isInteger(horas) ||
            horas < 1 ||
            horas > 40
        ) {

            alert(
                'Las horas asignadas deben ser un número entero entre 1 y 40.'
            );

            return false;
        }


        if (!rolSelect.value) {

            alert(
                'Selecciona un rol.'
            );

            return false;
        }


        return true;
    }


    function bloquearFormulario(
        bloquear
    ) {

        cargando =
            bloquear;


        guardarButton.disabled =
            bloquear;


        limpiarButton.disabled =
            bloquear;


        if (guardarTexto) {

            guardarTexto.textContent =
                bloquear

                    ? 'Guardando...'

                    : (
                        asignacionEditandoId
                            ? 'Actualizar asignación'
                            : 'Crear asignación'
                    );
        }
    }


    function limpiarFormulario() {

        asignacionEditandoId =
            null;


        profesorSelect.value =
            '';


        institucionSelect.value =
            '';


        cursoSelect.innerHTML = `
            <option value="">
                Seleccionar curso
            </option>
        `;


        gradoSelect.innerHTML = `
            <option value="">
                Seleccionar grado
            </option>
        `;


        cursoSelect.disabled =
            true;


        gradoSelect.disabled =
            true;


        horasInput.value =
            '';


        rolSelect.value =
            '';


        estadoSelect.value =
            '1';


        if (observacionesInput) {
            observacionesInput.value = '';
        }


        cargarSelectProfesores();


        actualizarContadorObservaciones();


        ocultarEstado();


        if (guardarTexto) {
            guardarTexto.textContent =
                'Crear asignación';
        }


        actualizarTodosCustomSelect();
    }


    // =========================================================
    // GUARDAR / ACTUALIZAR
    // =========================================================

    async function guardarAsignacion() {

        if (
            cargando ||
            !validarFormulario()
        ) {
            return;
        }


        const payload =
            obtenerPayload();


        bloquearFormulario(
            true
        );


        ocultarEstado();


        try {

            let response;


            if (asignacionEditandoId) {

                response =
                    await window.axios.put(
                        `/api/asignaciones/${asignacionEditandoId}`,
                        payload
                    );

            } else {

                response =
                    await window.axios.post(
                        '/api/asignaciones',
                        payload
                    );
            }


            const mensaje =
                response?.data?.message ||
                (
                    asignacionEditandoId
                        ? 'Asignación actualizada correctamente.'
                        : 'Asignación creada correctamente.'
                );


            mostrarEstado(
                true,
                mensaje
            );


            const eraEdicion =
                Boolean(
                    asignacionEditandoId
                );


            limpiarFormulario();


            /*
             * limpiarFormulario oculta el mensaje,
             * por eso lo mostramos de nuevo.
             */

            mostrarEstado(
                true,
                mensaje
            );


            await cargarAsignaciones();


            alert(
                eraEdicion
                    ? 'Asignación actualizada correctamente.'
                    : 'Asignación creada correctamente.'
            );


        } catch (error) {

            console.error(
                'Error guardando asignación:',
                error
            );


            const mensaje =
                obtenerMensajeError(error);


            mostrarEstado(
                false,
                mensaje
            );


            alert(mensaje);


        } finally {

            bloquearFormulario(
                false
            );
        }
    }


    // =========================================================
    // EDITAR
    // =========================================================

    function editarAsignacion(id) {

        const asignacion =
            asignaciones.find(
                item =>
                    String(item.id) ===
                    String(id)
            );


        if (!asignacion) {

            alert(
                'No se encontró la asignación.'
            );

            return;
        }


        asignacionEditandoId =
            asignacion.id;


        institucionSelect.value =
            asignacion.institucion ||
            'colegio';


        cargarSelectProfesores();

        cargarSelectCursos();


        profesorSelect.value =
            String(
                asignacion.profesor_id
            );


        cursoSelect.value =
            String(
                asignacion.curso_id
            );


        cargarSelectGrados();


        gradoSelect.value =
            String(
                asignacion.grado_id
            );


        horasInput.value =
            asignacion.horas_asignadas ??
            '';


        rolSelect.value =
            asignacion.rol ||
            '';


        estadoSelect.value =
            (
                asignacion.activo === true ||
                asignacion.activo === 1 ||
                asignacion.activo === '1'
            )
                ? '1'
                : '0';


        if (observacionesInput) {

            observacionesInput.value =
                asignacion.observaciones ||
                '';
        }


        actualizarContadorObservaciones();


        actualizarTodosCustomSelect();


        ocultarEstado();


        if (guardarTexto) {

            guardarTexto.textContent =
                'Actualizar asignación';
        }


        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }


    // =========================================================
    // ACTIVAR / DESACTIVAR
    // =========================================================

    async function cambiarEstadoAsignacion(
        id,
        activar
    ) {

        const accion =
            activar
                ? 'activar'
                : 'desactivar';


        const pregunta =
            activar

                ? '¿Deseas activar esta asignación?'

                : '¿Deseas desactivar esta asignación?';


        if (!confirm(pregunta)) {
            return;
        }


        try {

            const response =
                await window.axios.post(
                    `/api/asignaciones/${id}/${accion}`
                );


            alert(
                response?.data?.message ||
                (
                    activar
                        ? 'Asignación activada correctamente.'
                        : 'Asignación desactivada correctamente.'
                )
            );


            if (
                String(asignacionEditandoId) ===
                String(id)
            ) {
                limpiarFormulario();
            }


            await cargarAsignaciones();


        } catch (error) {

            console.error(
                `Error al ${accion} asignación:`,
                error
            );


            alert(
                obtenerMensajeError(error)
            );
        }
    }


    // =========================================================
    // EVENTOS TABLA
    // =========================================================

    tabla.addEventListener(
        'click',
        event => {

            const botonEditar =
                event.target.closest(
                    '.editar-asignacion'
                );


            if (botonEditar) {

                editarAsignacion(
                    botonEditar.dataset.id
                );

                return;
            }


            const botonDesactivar =
                event.target.closest(
                    '.desactivar-asignacion'
                );


            if (botonDesactivar) {

                cambiarEstadoAsignacion(
                    botonDesactivar.dataset.id,
                    false
                );

                return;
            }


            const botonActivar =
                event.target.closest(
                    '.activar-asignacion'
                );


            if (botonActivar) {

                cambiarEstadoAsignacion(
                    botonActivar.dataset.id,
                    true
                );
            }
        }
    );


    // =========================================================
    // EVENTOS FORMULARIO
    // =========================================================

    institucionSelect.addEventListener(
        'change',
        () => {

            cursoSelect.value =
                '';


            gradoSelect.value =
                '';


            cargarSelectProfesores();

            cargarSelectCursos();

            cargarSelectGrados();


            actualizarTodosCustomSelect();
        }
    );


    cursoSelect.addEventListener(
        'change',
        () => {

            gradoSelect.value =
                '';


            cargarSelectGrados();


            actualizarCustomSelect(
                cursoSelect
            );
        }
    );


    profesorSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                profesorSelect
            );
        }
    );


    gradoSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                gradoSelect
            );
        }
    );


    rolSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                rolSelect
            );
        }
    );


    estadoSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                estadoSelect
            );
        }
    );


    observacionesInput?.addEventListener(
        'input',
        actualizarContadorObservaciones
    );


    guardarButton.addEventListener(
        'click',
        guardarAsignacion
    );


    limpiarButton?.addEventListener(
        'click',
        limpiarFormulario
    );


    buscarInput?.addEventListener(
        'input',
        renderizarAsignaciones
    );


    filtroInstitucion?.addEventListener(
        'change',
        renderizarAsignaciones
    );


    // =========================================================
    // INICIAL
    // =========================================================

    async function iniciar() {

        cursoSelect.disabled =
            true;


        gradoSelect.disabled =
            true;


        actualizarContadorObservaciones();


        actualizarTodosCustomSelect();


        await cargarCatalogos();


        await cargarAsignaciones();
    }


    iniciar();

});
