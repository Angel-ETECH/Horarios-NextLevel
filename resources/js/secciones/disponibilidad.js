document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // ELEMENTOS PRINCIPALES
    // =========================================================

    const profesorSelect =
        document.getElementById('profesor-select');

    const institucionSelect =
        document.getElementById('institucion-select');

    const slots =
        document.querySelectorAll('.availability-slot');


    if (
        !profesorSelect ||
        !institucionSelect ||
        slots.length === 0
    ) {
        return;
    }


    const selectAllButton =
        document.getElementById('select-all-availability');

    const clearButton =
        document.getElementById('clear-availability');

    const saveButton =
        document.getElementById('save-availability');

    const countElement =
        document.getElementById('availability-count');

    const daysElement =
        document.getElementById('availability-days');

    const hoursElement =
        document.getElementById('availability-hours');

    const dayButtons =
        document.querySelectorAll('.select-day');

    const mobileDayTabs =
        document.querySelectorAll('.mobile-day-tab');

    const mobileDayPanels =
        document.querySelectorAll('.mobile-day-panel');


    // =========================================================
    // HORARIOS PERSONALIZADOS
    // =========================================================

    const diaPersonalizadoSelect =
        document.getElementById('personalizado-dia');

    const inicioPersonalizadoInput =
        document.getElementById('personalizado-inicio');

    const finPersonalizadoInput =
        document.getElementById('personalizado-fin');

    const agregarPersonalizadoButton =
        document.getElementById('agregar-personalizado');

    const listaPersonalizadosEl =
        document.getElementById('personalizados-lista');

    const personalizadosVacioEl =
        document.getElementById('personalizados-vacio');


    // =========================================================
    // API
    // =========================================================

    const API_PROFESORES =
        '/api/profesores';

    const API_DISPONIBILIDADES =
        '/api/disponibilidades';


    const DURACION_BLOQUE_GRID_MIN =
        60;

    const HORA_MINIMA_PERMITIDA =
        '07:00';

    const HORA_MAXIMA_PERMITIDA =
        '20:00';


    // =========================================================
    // DÍAS
    // =========================================================

    const diasBackend = {
        1: 'lunes',
        2: 'martes',
        3: 'miercoles',
        4: 'jueves',
        5: 'viernes',
        6: 'sabado',
        7: 'domingo'
    };


    const diasCortos = {
        1: 'Lun',
        2: 'Mar',
        3: 'Mié',
        4: 'Jue',
        5: 'Vie',
        6: 'Sáb',
        7: 'Dom'
    };


    const diasNumericos = {
        lunes: 1,
        martes: 2,
        miercoles: 3,
        miércoles: 3,
        jueves: 4,
        viernes: 5,
        sabado: 6,
        sábado: 6,
        domingo: 7
    };


    // =========================================================
    // ESTADO
    // =========================================================

    const disponibilidades =
        {};

    const personalizados =
        {};

    let cargando =
        false;

    let guardando =
        false;


    // =========================================================
    // UTILIDADES
    // =========================================================

    function normalizarTexto(valor) {

        return String(
            valor ?? ''
        )
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(
                /[\u0300-\u036f]/g,
                ''
            );
    }


    function esc(valor) {

        return String(
            valor ?? ''
        )
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    function horaCorta(hora) {

        if (!hora) {
            return '';
        }

        return String(
            hora
        ).substring(
            0,
            5
        );
    }


    function minutosHora(hora) {

        if (!hora) {
            return 0;
        }

        const [
            h,
            m
        ] =
            hora
                .substring(0, 5)
                .split(':')
                .map(Number);

        return (
            h * 60 +
            m
        );
    }


    function minutosEntre(
        horaInicio,
        horaFin
    ) {

        return Math.max(
            0,
            minutosHora(horaFin) -
            minutosHora(horaInicio)
        );
    }


    function generarIdTemporal() {

        return `${Date.now()}-${Math.random()
            .toString(36)
            .slice(2, 8)}`;
    }


    function obtenerTurno(
        horaInicio
    ) {

        const hora =
            Number(
                String(
                    horaInicio
                ).split(':')[0]
            );


        if (hora < 12) {
            return 'mañana';
        }


        if (hora < 18) {
            return 'tarde';
        }


        return 'noche';
    }


    function nombreDiaANumero(
        dia
    ) {

        if (
            dia === null ||
            dia === undefined
        ) {
            return null;
        }


        if (
            !Number.isNaN(
                Number(dia)
            )
        ) {

            const numero =
                Number(dia);

            if (
                numero >= 1 &&
                numero <= 7
            ) {
                return numero;
            }
        }


        return diasNumericos[
            normalizarTexto(
                dia
            )
        ] ?? null;
    }


    function extraerColeccion(
        respuesta
    ) {

        const body =
            respuesta?.data;


        if (
            Array.isArray(
                body
            )
        ) {
            return body;
        }


        if (
            Array.isArray(
                body?.data
            )
        ) {
            return body.data;
        }


        if (
            Array.isArray(
                body?.data?.data
            )
        ) {
            return body.data.data;
        }


        if (
            Array.isArray(
                body?.profesores
            )
        ) {
            return body.profesores;
        }


        return [];
    }


    function mensajeError(
        error,
        defecto =
            'Ocurrió un error inesperado.'
    ) {

        const errores =
            error?.response?.data?.errors;


        if (
            errores &&
            typeof errores ===
            'object'
        ) {

            const primerGrupo =
                Object.values(
                    errores
                )[0];


            if (
                Array.isArray(
                    primerGrupo
                ) &&
                primerGrupo.length
            ) {
                return primerGrupo[0];
            }
        }


        return (
            error?.response?.data?.message ||
            error?.response?.data?.error ||
            defecto
        );
    }


    function parsearInstituciones(
        profesor
    ) {

        const valor =
            profesor?.instituciones;


        if (
            Array.isArray(
                valor
            )
        ) {
            return valor;
        }


        if (
            typeof valor ===
            'string'
        ) {

            try {

                const convertido =
                    JSON.parse(
                        valor
                    );


                if (
                    Array.isArray(
                        convertido
                    )
                ) {
                    return convertido;
                }

            } catch {
                // Si no es JSON seguimos abajo.
            }


            return valor
                .split(',')
                .map(
                    item =>
                        item.trim()
                )
                .filter(Boolean);
        }


        return [];
    }


    // =========================================================
    // ICONOS CUSTOM SELECT
    // =========================================================

    function iconoSelect(
        tipo
    ) {

        const iconos = {

            profesor: `
                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M20 21a8 8 0 0 0-16 0"/>
                </svg>
            `,

            institucion: `
                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <path d="M4 21V6h16v15"/>
                    <path d="M2 21h20"/>
                    <path d="M8 10h2"/>
                    <path d="M14 10h2"/>
                    <path d="M9 21v-4h6v4"/>
                </svg>
            `,

            dia: `
                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <path d="M8 2v4M16 2v4"/>
                    <rect x="3" y="5" width="18" height="16" rx="2"/>
                    <path d="M3 10h18"/>
                </svg>
            `
        };


        return (
            iconos[tipo] ||
            iconos.profesor
        );
    }


    function obtenerWrapperSelect(
        select
    ) {

        if (!select) {
            return null;
        }


        return document.querySelector(
            `[data-disponibilidad-select="${select.id}"]`
        );
    }


    function cerrarCustomSelect(
        wrapper
    ) {

        wrapper?.classList.remove(
            'open'
        );


        wrapper
            ?.querySelector(
                '.disponibilidad-select-trigger'
            )
            ?.setAttribute(
                'aria-expanded',
                'false'
            );
    }


    function cerrarTodosCustomSelects(
        excepto = null
    ) {

        document
            .querySelectorAll(
                '[data-disponibilidad-select]'
            )
            .forEach(
                wrapper => {

                    if (
                        wrapper !==
                        excepto
                    ) {

                        cerrarCustomSelect(
                            wrapper
                        );
                    }
                }
            );
    }


    function renderOpcionesCustomSelect(
        wrapper,
        busqueda = ''
    ) {

        const select =
            document.getElementById(
                wrapper.dataset
                    .disponibilidadSelect
            );


        const container =
            wrapper.querySelector(
                '.disponibilidad-select-options'
            );


        if (
            !select ||
            !container
        ) {
            return;
        }


        const query =
            normalizarTexto(
                busqueda
            );


        const opciones =
            Array.from(
                select.options
            )
                .filter(
                    option =>

                        !option.disabled &&

                        normalizarTexto(
                            option.textContent
                        ).includes(
                            query
                        )
                );


        if (
            !opciones.length
        ) {

            container.innerHTML = `
                <div class="disponibilidad-select-empty">
                    No hay opciones disponibles
                </div>
            `;

            return;
        }


        container.innerHTML =
            opciones
                .map(
                    option => {

                        const selected =
                            String(
                                option.value
                            ) ===
                            String(
                                select.value
                            );


                        return `
                            <button
                                type="button"
                                class="
                                    disponibilidad-select-option
                                    ${
                                        selected
                                            ? 'selected'
                                            : ''
                                    }
                                "
                                data-value="${esc(
                                    option.value
                                )}"
                            >

                                <span class="disponibilidad-option-icon">

                                    ${iconoSelect(
                                        wrapper.dataset.icon
                                    )}

                                </span>


                                <span class="disponibilidad-option-text">

                                    ${esc(
                                        option.textContent
                                    )}

                                </span>


                                <svg
                                    class="disponibilidad-option-check"
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
                )
                .join('');


        container
            .querySelectorAll(
                '.disponibilidad-select-option'
            )
            .forEach(
                button => {

                    button.addEventListener(
                        'click',
                        () => {

                            select.value =
                                button.dataset.value;


                            select.dispatchEvent(
                                new Event(
                                    'change',
                                    {
                                        bubbles:
                                            true
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
                }
            );
    }


    function actualizarCustomSelect(
        select
    ) {

        if (!select) {
            return;
        }


        const wrapper =
            obtenerWrapperSelect(
                select
            );


        if (!wrapper) {
            return;
        }


        const trigger =
            wrapper.querySelector(
                '.disponibilidad-select-trigger'
            );


        const texto =
            wrapper.querySelector(
                '.disponibilidad-select-text'
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


        texto.textContent =
            opcion?.textContent ||
            wrapper.dataset.placeholder ||
            'Seleccionar';


        renderOpcionesCustomSelect(
            wrapper
        );
    }


    function construirCustomSelect(
        wrapper
    ) {

        if (
            !wrapper ||
            wrapper.dataset.ready ===
            'true'
        ) {
            return;
        }


        const select =
            document.getElementById(
                wrapper.dataset
                    .disponibilidadSelect
            );


        if (!select) {
            return;
        }


        const label =
            wrapper.dataset.label ||
            'Seleccionar';


        const placeholder =
            wrapper.dataset.placeholder ||
            'Seleccionar';


        const icono =
            wrapper.dataset.icon ||
            'profesor';


        const usarBusqueda =
            wrapper.dataset.search !==
            'false';


        wrapper.innerHTML = `

            <button
                type="button"
                class="disponibilidad-select-trigger"
                aria-expanded="false"
            >

                <span class="disponibilidad-select-icon">

                    ${iconoSelect(
                        icono
                    )}

                </span>


                <span class="disponibilidad-select-content">

                    <span class="disponibilidad-select-label">

                        ${esc(
                            label
                        )}

                    </span>


                    <span class="disponibilidad-select-text">

                        ${esc(
                            placeholder
                        )}

                    </span>

                </span>


                <svg
                    class="disponibilidad-select-arrow"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m6 9 6 6 6-6"/>
                </svg>

            </button>


            <div class="disponibilidad-select-menu">

                ${
                    usarBusqueda

                        ? `
                            <div class="disponibilidad-select-search-wrap">

                                <svg
                                    class="disponibilidad-select-search-icon"
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
                                    class="disponibilidad-select-search"
                                    placeholder="Buscar..."
                                    autocomplete="off"
                                >

                            </div>
                        `

                        : ''
                }


                <div class="disponibilidad-select-options">
                </div>

            </div>
        `;


        wrapper.dataset.ready =
            'true';


        const trigger =
            wrapper.querySelector(
                '.disponibilidad-select-trigger'
            );


        const search =
            wrapper.querySelector(
                '.disponibilidad-select-search'
            );


        trigger.addEventListener(
            'click',
            () => {

                if (
                    trigger.disabled
                ) {
                    return;
                }


                cerrarTodosCustomSelects(
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

                    if (search) {
                        search.value = '';
                    }


                    renderOpcionesCustomSelect(
                        wrapper
                    );


                    if (search) {

                        setTimeout(
                            () =>
                                search.focus(),
                            40
                        );
                    }
                }
            }
        );


        search?.addEventListener(
            'input',
            () => {

                renderOpcionesCustomSelect(
                    wrapper,
                    search.value
                );
            }
        );


        actualizarCustomSelect(
            select
        );
    }


    function actualizarTodosCustomSelects() {

        [
            profesorSelect,
            institucionSelect,
            diaPersonalizadoSelect
        ]
            .filter(Boolean)
            .forEach(
                actualizarCustomSelect
            );
    }


    document
        .querySelectorAll(
            '[data-disponibilidad-select]'
        )
        .forEach(
            construirCustomSelect
        );


    document.addEventListener(
        'click',
        event => {

            if (
                !event.target.closest(
                    '[data-disponibilidad-select]'
                )
            ) {

                cerrarTodosCustomSelects();
            }
        }
    );


    // =========================================================
    // BLOQUEAR INTERFAZ
    // =========================================================

    function bloquearInterfaz(
        estado
    ) {

        cargando =
            estado;


        profesorSelect.disabled =
            estado;


        institucionSelect.disabled =
            estado;


        if (saveButton) {

            saveButton.disabled =
                estado ||
                guardando;
        }


        actualizarCustomSelect(
            profesorSelect
        );

        actualizarCustomSelect(
            institucionSelect
        );
    }


    // =========================================================
    // CARGAR PROFESORES DESDE API
    // =========================================================

    async function cargarProfesores() {

        const seleccionado =
            profesorSelect.value;


        const institucionActual =
            institucionSelect.value ||
            'colegio';


        profesorSelect.innerHTML = `
            <option value="">
                Cargando profesores...
            </option>
        `;


        actualizarCustomSelect(
            profesorSelect
        );


        try {

            const response =
                await window.axios.get(
                    API_PROFESORES,
                    {
                        params: {
                            estado:
                                'activo',

                            per_page:
                                100
                        }
                    }
                );


            let profesores =
                extraerColeccion(
                    response
                );


            profesores =
                profesores
                    .filter(
                        profesor => {

                            if (
                                profesor.estado &&
                                profesor.estado !==
                                'activo'
                            ) {
                                return false;
                            }


                            const instituciones =
                                parsearInstituciones(
                                    profesor
                                );


                            if (
                                instituciones.length ===
                                0
                            ) {
                                return true;
                            }


                            return instituciones
                                .map(
                                    normalizarTexto
                                )
                                .includes(
                                    normalizarTexto(
                                        institucionActual
                                    )
                                );
                        }
                    )
                    .sort(
                        (a, b) => {

                            const nombreA =
                                [
                                    a.nombre,
                                    a.apellido_paterno,
                                    a.apellido_materno
                                ]
                                    .filter(Boolean)
                                    .join(' ');


                            const nombreB =
                                [
                                    b.nombre,
                                    b.apellido_paterno,
                                    b.apellido_materno
                                ]
                                    .filter(Boolean)
                                    .join(' ');


                            return nombreA.localeCompare(
                                nombreB,
                                'es'
                            );
                        }
                    );


            profesorSelect.innerHTML = `
                <option value="">
                    Seleccionar profesor
                </option>
            `;


            profesores.forEach(
                profesor => {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        String(
                            profesor.id
                        );


                    const nombreCompleto =
                        [
                            profesor.nombre,
                            profesor.apellido_paterno,
                            profesor.apellido_materno
                        ]
                            .filter(Boolean)
                            .join(' ');


                    option.textContent =
                        profesor.codigo

                            ? `${nombreCompleto} · ${profesor.codigo}`

                            : nombreCompleto;


                    profesorSelect.appendChild(
                        option
                    );
                }
            );


            const existe =
                Array.from(
                    profesorSelect.options
                ).some(
                    option =>
                        option.value ===
                        seleccionado
                );


            profesorSelect.value =
                existe
                    ? seleccionado
                    : '';


            if (
                profesores.length ===
                0
            ) {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    '';

                option.disabled =
                    true;


                option.textContent =
                    institucionActual ===
                    'academia'

                        ? 'No hay profesores activos para Academia'

                        : 'No hay profesores activos para Colegio';


                profesorSelect.appendChild(
                    option
                );
            }


        } catch (error) {

            console.error(
                'Error cargando profesores:',
                error
            );


            profesorSelect.innerHTML = `
                <option value="">
                    Error al cargar profesores
                </option>
            `;


            alert(
                mensajeError(
                    error,
                    'No se pudieron cargar los profesores.'
                )
            );
        }


        actualizarCustomSelect(
            profesorSelect
        );
    }


    // =========================================================
    // CLAVE PROFESOR + INSTITUCIÓN
    // =========================================================

    function claveActual() {

        return `${
            profesorSelect.value
        }-${
            institucionSelect.value ||
            'colegio'
        }`;
    }


    // =========================================================
    // OBTENER BLOQUES SELECCIONADOS
    // =========================================================

    function obtenerSeleccionados() {

        const ids =
            new Set();


        document
            .querySelectorAll(
                '.availability-slot.selected'
            )
            .forEach(
                slot => {

                    if (
                        slot.dataset.dia &&
                        slot.dataset.hora
                    ) {

                        ids.add(
                            `${slot.dataset.dia}-${slot.dataset.hora}`
                        );
                    }
                }
            );


        return Array.from(
            ids
        );
    }


    function personalizadosActuales() {

        if (
            !profesorSelect.value
        ) {
            return [];
        }


        return personalizados[
            claveActual()
        ] || [];
    }


    // =========================================================
    // RESUMEN
    // =========================================================

    function actualizarResumen() {

        const seleccionados =
            obtenerSeleccionados();


        const dias =
            new Set();


        seleccionados.forEach(
            identificador => {

                const [
                    dia
                ] =
                    identificador.split(
                        '-'
                    );


                dias.add(
                    String(
                        dia
                    )
                );
            }
        );


        let minutosTotales =
            seleccionados.length *
            DURACION_BLOQUE_GRID_MIN;


        const personalizadosData =
            personalizadosActuales();


        personalizadosData.forEach(
            item => {

                dias.add(
                    String(
                        item.dia
                    )
                );


                minutosTotales +=
                    minutosEntre(
                        item.horaInicio,
                        item.horaFin
                    );
            }
        );


        if (countElement) {

            countElement.textContent =
                seleccionados.length +
                personalizadosData.length;
        }


        if (daysElement) {

            daysElement.textContent =
                dias.size;
        }


        if (hoursElement) {

            const horas =
                minutosTotales /
                60;


            hoursElement.textContent =
                Number.isInteger(
                    horas
                )
                    ? horas
                    : horas.toFixed(
                        1
                    );
        }
    }


    // =========================================================
    // MARCAR SLOT
    // =========================================================

    function marcarSlot(
        slot,
        seleccionado
    ) {

        slot.classList.toggle(
            'selected',
            seleccionado
        );


        if (
            slot.closest(
                '.mobile-day-panel'
            )
        ) {

            const texto =
                slot.querySelector(
                    'span:first-child'
                );


            if (texto) {

                texto.classList.toggle(
                    'text-white',
                    seleccionado
                );


                texto.classList.toggle(
                    'text-slate-600',
                    !seleccionado
                );
            }
        }
    }


    function limpiarGrid() {

        slots.forEach(
            slot =>
                marcarSlot(
                    slot,
                    false
                )
        );
    }


    // =========================================================
    // CARGAR DISPONIBILIDAD VISUAL
    // =========================================================

    function cargarDisponibilidadVisual() {

        limpiarGrid();


        if (
            !profesorSelect.value
        ) {

            actualizarResumen();
            return;
        }


        const datos =
            disponibilidades[
                claveActual()
            ] || [];


        slots.forEach(
            slot => {

                const id =
                    `${slot.dataset.dia}-${slot.dataset.hora}`;


                marcarSlot(
                    slot,
                    datos.includes(
                        id
                    )
                );
            }
        );


        actualizarResumen();
    }


    // =========================================================
    // DISPONIBILIDAD DESDE API
    // =========================================================

    async function obtenerDisponibilidadesProfesor(
        profesorId
    ) {

        const response =
            await window.axios.get(
                `${API_DISPONIBILIDADES}/profesor/${profesorId}`
            );


        return extraerColeccion(
            response
        );
    }


    function disponibilidadAEstadoVisual(
        guardadas,
        clave
    ) {

        const idsGrid =
            [];

        const custom =
            [];


        guardadas.forEach(
            item => {

                if (
                    item.tipo &&
                    item.tipo !==
                    'disponible'
                ) {
                    return;
                }


                const dia =
                    nombreDiaANumero(
                        item.dia_semana ??
                        item.dia
                    );


                const horaInicio =
                    horaCorta(
                        item.hora_inicio ??
                        item.horaInicio
                    );


                const horaFin =
                    horaCorta(
                        item.hora_fin ??
                        item.horaFin
                    );


                if (
                    !dia ||
                    !horaInicio ||
                    !horaFin
                ) {
                    return;
                }


                const inicioMin =
                    minutosHora(
                        horaInicio
                    );


                const finMin =
                    minutosHora(
                        horaFin
                    );


                const duracion =
                    finMin -
                    inicioMin;


                const minutoInicial =
                    inicioMin %
                    60;


                const minutoFinal =
                    finMin %
                    60;


                const alineado =
                    minutoInicial ===
                        0 &&
                    minutoFinal ===
                        0 &&
                    duracion >
                        0 &&
                    duracion %
                        DURACION_BLOQUE_GRID_MIN ===
                        0;


                if (alineado) {

                    let cursor =
                        inicioMin;


                    while (
                        cursor <
                        finMin
                    ) {

                        const hora =
                            Math.floor(
                                cursor /
                                60
                            );


                        const minuto =
                            cursor %
                            60;


                        idsGrid.push(
                            `${dia}-${String(
                                hora
                            ).padStart(
                                2,
                                '0'
                            )}:${String(
                                minuto
                            ).padStart(
                                2,
                                '0'
                            )}`
                        );


                        cursor +=
                            DURACION_BLOQUE_GRID_MIN;
                    }

                } else {

                    custom.push({
                        id:
                            String(
                                item.id ??
                                generarIdTemporal()
                            ),

                        backendId:
                            item.id ??
                            null,

                        dia,

                        horaInicio,

                        horaFin
                    });
                }
            }
        );


        disponibilidades[
            clave
        ] =
            Array.from(
                new Set(
                    idsGrid
                )
            );


        personalizados[
            clave
        ] =
            custom;
    }


    async function cargarDisponibilidadAPI() {

        const profesorId =
            profesorSelect.value;


        const institucion =
            institucionSelect.value ||
            'colegio';


        limpiarGrid();


        if (
            !profesorId
        ) {

            renderizarPersonalizados();
            actualizarResumen();
            return;
        }


        const clave =
            claveActual();


        bloquearInterfaz(
            true
        );


        try {

            const todas =
                await obtenerDisponibilidadesProfesor(
                    profesorId
                );


            const filtradas =
                todas.filter(
                    item =>

                        normalizarTexto(
                            item.institucion ??
                            'colegio'
                        ) ===
                        normalizarTexto(
                            institucion
                        )
                );


            disponibilidadAEstadoVisual(
                filtradas,
                clave
            );


            cargarDisponibilidadVisual();

            renderizarPersonalizados();


        } catch (error) {

            console.error(
                'Error cargando disponibilidad:',
                error
            );


            disponibilidades[
                clave
            ] =
                [];

            personalizados[
                clave
            ] =
                [];


            cargarDisponibilidadVisual();

            renderizarPersonalizados();


            alert(
                mensajeError(
                    error,
                    'No se pudo cargar la disponibilidad del profesor.'
                )
            );

        } finally {

            bloquearInterfaz(
                false
            );
        }
    }


    // =========================================================
    // CAMBIOS DE SELECT
    // =========================================================

    profesorSelect.addEventListener(
        'change',
        async () => {

            actualizarCustomSelect(
                profesorSelect
            );


            await cargarDisponibilidadAPI();
        }
    );


    institucionSelect.addEventListener(
        'change',
        async () => {

            actualizarCustomSelect(
                institucionSelect
            );

            limpiarGrid();


            const claveAnterior =
                Object.keys(
                    disponibilidades
                );


            claveAnterior.forEach(
                clave => {

                    delete disponibilidades[
                        clave
                    ];

                    delete personalizados[
                        clave
                    ];
                }
            );


            await cargarProfesores();


            renderizarPersonalizados();

            actualizarResumen();
        }
    );


    diaPersonalizadoSelect?.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                diaPersonalizadoSelect
            );
        }
    );


    // =========================================================
    // CLICK SLOT
    // =========================================================

    slots.forEach(
        slot => {

            slot.addEventListener(
                'click',
                () => {

                    if (
                        cargando ||
                        guardando
                    ) {
                        return;
                    }


                    if (
                        !profesorSelect.value
                    ) {

                        alert(
                            'Primero selecciona un profesor.'
                        );

                        profesorSelect.focus();

                        return;
                    }


                    const dia =
                        slot.dataset.dia;


                    const hora =
                        slot.dataset.hora;


                    const seleccionar =
                        !slot.classList.contains(
                            'selected'
                        );


                    document
                        .querySelectorAll(
                            `.availability-slot[data-dia="${dia}"][data-hora="${hora}"]`
                        )
                        .forEach(
                            copia => {

                                marcarSlot(
                                    copia,
                                    seleccionar
                                );
                            }
                        );


                    disponibilidades[
                        claveActual()
                    ] =
                        obtenerSeleccionados();


                    actualizarResumen();
                }
            );
        }
    );


    // =========================================================
    // DÍA COMPLETO
    // =========================================================

    dayButtons.forEach(
        button => {

            button.addEventListener(
                'click',
                () => {

                    if (
                        cargando ||
                        guardando
                    ) {
                        return;
                    }


                    if (
                        !profesorSelect.value
                    ) {

                        alert(
                            'Primero selecciona un profesor.'
                        );

                        return;
                    }


                    const dia =
                        button.dataset.dia;


                    const bloques =
                        document.querySelectorAll(
                            `.availability-slot[data-dia="${dia}"]`
                        );


                    const grupos =
                        new Map();


                    bloques.forEach(
                        slot => {

                            const id =
                                `${slot.dataset.dia}-${slot.dataset.hora}`;


                            if (
                                !grupos.has(
                                    id
                                )
                            ) {

                                grupos.set(
                                    id,
                                    []
                                );
                            }


                            grupos
                                .get(
                                    id
                                )
                                .push(
                                    slot
                                );
                        }
                    );


                    const todosSeleccionados =
                        Array.from(
                            grupos.values()
                        )
                            .every(
                                copias =>
                                    copias.some(
                                        slot =>
                                            slot.classList.contains(
                                                'selected'
                                            )
                                    )
                            );


                    grupos.forEach(
                        copias => {

                            copias.forEach(
                                slot => {

                                    marcarSlot(
                                        slot,
                                        !todosSeleccionados
                                    );
                                }
                            );
                        }
                    );


                    disponibilidades[
                        claveActual()
                    ] =
                        obtenerSeleccionados();


                    actualizarResumen();
                }
            );
        }
    );


    // =========================================================
    // SELECCIONAR TODO
    // =========================================================

    selectAllButton?.addEventListener(
        'click',
        () => {

            if (
                cargando ||
                guardando
            ) {
                return;
            }


            if (
                !profesorSelect.value
            ) {

                alert(
                    'Primero selecciona un profesor.'
                );

                return;
            }


            slots.forEach(
                slot =>
                    marcarSlot(
                        slot,
                        true
                    )
            );


            disponibilidades[
                claveActual()
            ] =
                obtenerSeleccionados();


            actualizarResumen();
        }
    );


    // =========================================================
    // LIMPIAR
    // =========================================================

    clearButton?.addEventListener(
        'click',
        () => {

            if (
                cargando ||
                guardando
            ) {
                return;
            }


            if (
                !profesorSelect.value
            ) {

                alert(
                    'Primero selecciona un profesor.'
                );

                return;
            }


            limpiarGrid();


            disponibilidades[
                claveActual()
            ] =
                [];


            personalizados[
                claveActual()
            ] =
                [];


            renderizarPersonalizados();

            actualizarResumen();
        }
    );


    // =========================================================
    // MOBILE DAYS
    // =========================================================

    mobileDayTabs.forEach(
        tab => {

            tab.addEventListener(
                'click',
                () => {

                    const dia =
                        tab.dataset.dia;


                    mobileDayPanels.forEach(
                        panel => {

                            panel.classList.toggle(
                                'hidden',
                                panel.dataset.diaPanel !==
                                dia
                            );
                        }
                    );


                    mobileDayTabs.forEach(
                        otro => {

                            otro.classList.toggle(
                                'active-mobile-day',
                                otro === tab
                            );
                        }
                    );
                }
            );
        }
    );


    // =========================================================
    // GRID A FRANJAS
    // =========================================================

    function bloquesGridAFranjas(
        identificadores
    ) {

        const porDia =
            {};


        identificadores.forEach(
            identificador => {

                const separador =
                    identificador.indexOf(
                        '-'
                    );


                const dia =
                    identificador.substring(
                        0,
                        separador
                    );


                const hora =
                    identificador.substring(
                        separador + 1
                    );


                if (
                    !porDia[dia]
                ) {

                    porDia[dia] =
                        [];
                }


                porDia[dia].push(
                    hora
                );
            }
        );


        const franjas =
            [];


        Object.keys(
            porDia
        )
            .forEach(
                dia => {

                    const horas =
                        Array.from(
                            new Set(
                                porDia[dia]
                            )
                        )
                            .sort();


                    let inicio =
                        null;


                    let anterior =
                        null;


                    horas.forEach(
                        hora => {

                            if (
                                inicio ===
                                null
                            ) {

                                inicio =
                                    hora;

                                anterior =
                                    hora;

                                return;
                            }


                            const esperado =
                                minutosHora(
                                    anterior
                                ) +
                                DURACION_BLOQUE_GRID_MIN;


                            const esperadoTexto =
                                `${String(
                                    Math.floor(
                                        esperado /
                                        60
                                    )
                                ).padStart(
                                    2,
                                    '0'
                                )}:${String(
                                    esperado %
                                    60
                                ).padStart(
                                    2,
                                    '0'
                                )}`;


                            if (
                                hora !==
                                esperadoTexto
                            ) {

                                const fin =
                                    minutosHora(
                                        anterior
                                    ) +
                                    DURACION_BLOQUE_GRID_MIN;


                                franjas.push({

                                    dia_semana:
                                        Number(
                                            dia
                                        ),

                                    hora_inicio:
                                        inicio,

                                    hora_fin:
                                        `${String(
                                            Math.floor(
                                                fin /
                                                60
                                            )
                                        ).padStart(
                                            2,
                                            '0'
                                        )}:${String(
                                            fin %
                                            60
                                        ).padStart(
                                            2,
                                            '0'
                                        )}`
                                });


                                inicio =
                                    hora;
                            }


                            anterior =
                                hora;
                        }
                    );


                    if (
                        inicio !==
                            null &&
                        anterior !==
                            null
                    ) {

                        const fin =
                            minutosHora(
                                anterior
                            ) +
                            DURACION_BLOQUE_GRID_MIN;


                        franjas.push({

                            dia_semana:
                                Number(
                                    dia
                                ),

                            hora_inicio:
                                inicio,

                            hora_fin:
                                `${String(
                                    Math.floor(
                                        fin /
                                        60
                                    )
                                ).padStart(
                                    2,
                                    '0'
                                )}:${String(
                                    fin %
                                    60
                                ).padStart(
                                    2,
                                    '0'
                                )}`
                        });
                    }
                }
            );


        return franjas;
    }


    // =========================================================
    // PERSONALIZADOS
    // =========================================================

    function renderizarPersonalizados() {

        if (
            !listaPersonalizadosEl
        ) {
            return;
        }


        listaPersonalizadosEl
            .querySelectorAll(
                '.chip-personalizado'
            )
            .forEach(
                item =>
                    item.remove()
            );


        const items =
            personalizadosActuales();


        personalizadosVacioEl?.classList.toggle(
            'hidden',
            items.length >
            0
        );


        items
            .slice()
            .sort(
                (a, b) =>
                    a.dia -
                    b.dia ||
                    a.horaInicio.localeCompare(
                        b.horaInicio
                    )
            )
            .forEach(
                item => {

                    const chip =
                        document.createElement(
                            'span'
                        );


                    chip.className =
                        'chip-personalizado inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold';


                    chip.innerHTML = `

                        ${diasCortos[item.dia] ?? 'Día'}

                        ·

                        ${item.horaInicio}

                        –

                        ${item.horaFin}


                        <button
                            type="button"
                            class="
                                quitar-personalizado
                                inline-flex
                                h-5 w-5
                                items-center
                                justify-center
                                rounded-full
                                transition
                                hover:bg-white
                            "
                            data-id="${esc(
                                item.id
                            )}"
                            title="Eliminar horario"
                        >

                            <svg
                                width="12"
                                height="12"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.2"
                            >
                                <path d="M18 6 6 18"/>
                                <path d="M6 6l12 12"/>
                            </svg>

                        </button>
                    `;


                    listaPersonalizadosEl
                        .appendChild(
                            chip
                        );
                }
            );


        actualizarResumen();
    }


    agregarPersonalizadoButton?.addEventListener(
        'click',
        () => {

            if (
                cargando ||
                guardando
            ) {
                return;
            }


            if (
                !profesorSelect.value
            ) {

                alert(
                    'Primero selecciona un profesor.'
                );

                return;
            }


            const dia =
                Number(
                    diaPersonalizadoSelect?.value
                );


            const horaInicio =
                inicioPersonalizadoInput?.value;


            const horaFin =
                finPersonalizadoInput?.value;


            if (
                !dia ||
                !horaInicio ||
                !horaFin
            ) {

                alert(
                    'Completa el día, hora de inicio y hora de fin.'
                );

                return;
            }


            if (
                horaInicio >=
                horaFin
            ) {

                alert(
                    'La hora de fin debe ser posterior a la hora de inicio.'
                );

                return;
            }


            if (
                horaInicio <
                HORA_MINIMA_PERMITIDA
                ||
                horaFin >
                HORA_MAXIMA_PERMITIDA
            ) {

                alert(
                    'La disponibilidad debe estar entre las 07:00 y las 20:00.'
                );

                return;
            }


            const clave =
                claveActual();


            if (
                !personalizados[
                    clave
                ]
            ) {

                personalizados[
                    clave
                ] =
                    [];
            }


            const traslapePersonalizado =
                personalizados[
                    clave
                ]
                    .some(
                        item =>

                            item.dia ===
                                dia &&

                            horaInicio <
                                item.horaFin &&

                            item.horaInicio <
                                horaFin
                    );


            if (
                traslapePersonalizado
            ) {

                alert(
                    'Ese horario se traslapa con otro horario personalizado del mismo día.'
                );

                return;
            }


            personalizados[
                clave
            ]
                .push({

                    id:
                        generarIdTemporal(),

                    backendId:
                        null,

                    dia,

                    horaInicio,

                    horaFin
                });


            inicioPersonalizadoInput.value =
                '';

            finPersonalizadoInput.value =
                '';


            renderizarPersonalizados();
        }
    );


    listaPersonalizadosEl?.addEventListener(
        'click',
        event => {

            const button =
                event.target.closest(
                    '.quitar-personalizado'
                );


            if (
                !button ||
                !profesorSelect.value
            ) {
                return;
            }


            const clave =
                claveActual();


            personalizados[
                clave
            ] =
                (
                    personalizados[
                        clave
                    ] || []
                )
                    .filter(
                        item =>
                            String(
                                item.id
                            ) !==
                            String(
                                button.dataset.id
                            )
                    );


            renderizarPersonalizados();
        }
    );


    // =========================================================
    // VALIDAR TRASLAPES
    // =========================================================

    function existenTraslapes(
        franjas
    ) {

        const porDia =
            {};


        franjas.forEach(
            franja => {

                const dia =
                    franja.dia_semana;


                if (
                    !porDia[dia]
                ) {
                    porDia[dia] =
                        [];
                }


                porDia[dia].push(
                    franja
                );
            }
        );


        return Object
            .values(
                porDia
            )
            .some(
                lista => {

                    const ordenadas =
                        lista
                            .slice()
                            .sort(
                                (a, b) =>
                                    a.hora_inicio.localeCompare(
                                        b.hora_inicio
                                    )
                            );


                    for (
                        let i = 1;
                        i < ordenadas.length;
                        i++
                    ) {

                        if (
                            ordenadas[i]
                                .hora_inicio <
                            ordenadas[i - 1]
                                .hora_fin
                        ) {
                            return true;
                        }
                    }


                    return false;
                }
            );
    }


    // =========================================================
    // GUARDAR EN API
    // =========================================================

    async function eliminarDisponibilidadesActuales(
        profesorId,
        institucion
    ) {

        const todas =
            await obtenerDisponibilidadesProfesor(
                profesorId
            );


        const actuales =
            todas.filter(
                item =>

                    String(
                        item.profesor_id
                    ) ===
                        String(
                            profesorId
                        ) &&

                    normalizarTexto(
                        item.institucion ??
                        'colegio'
                    ) ===
                        normalizarTexto(
                            institucion
                        )
            );


        if (
            actuales.length ===
            0
        ) {
            return;
        }


        await Promise.all(
            actuales
                .filter(
                    item =>
                        item.id
                )
                .map(
                    item =>
                        window.axios.delete(
                            `${API_DISPONIBILIDADES}/${item.id}`
                        )
                )
        );
    }


    async function crearDisponibilidades(
        profesorId,
        institucion,
        franjas
    ) {

        for (
            const franja of
            franjas
        ) {

            const diaBackend =
                diasBackend[
                    Number(
                        franja.dia_semana
                    )
                ];


            if (
                !diaBackend
            ) {

                throw new Error(
                    'Se encontró un día inválido.'
                );
            }


            const payload = {

                profesor_id:
                    Number(
                        profesorId
                    ),

                dia_semana:
                    diaBackend,

                hora_inicio:
                    franja.hora_inicio,

                hora_fin:
                    franja.hora_fin,

                tipo:
                    'disponible',

                turno:
                    obtenerTurno(
                        franja.hora_inicio
                    ),

                institucion,

                observacion:
                    null
            };


            await window.axios.post(
                API_DISPONIBILIDADES,
                payload
            );
        }
    }


    saveButton?.addEventListener(
        'click',
        async () => {

            if (
                guardando ||
                cargando
            ) {
                return;
            }


            const profesorId =
                profesorSelect.value;


            const institucion =
                institucionSelect.value ||
                'colegio';


            if (
                !profesorId
            ) {

                alert(
                    'Primero selecciona un profesor.'
                );

                profesorSelect.focus();

                return;
            }


            disponibilidades[
                claveActual()
            ] =
                obtenerSeleccionados();


            const seleccionados =
                disponibilidades[
                    claveActual()
                ] || [];


            const franjasGrid =
                bloquesGridAFranjas(
                    seleccionados
                );


            const personalizadosData =
                personalizadosActuales();


            const todasLasFranjas = [

                ...franjasGrid,

                ...personalizadosData.map(
                    item => ({

                        dia_semana:
                            Number(
                                item.dia
                            ),

                        hora_inicio:
                            item.horaInicio,

                        hora_fin:
                            item.horaFin
                    })
                )
            ];


            if (
                existenTraslapes(
                    todasLasFranjas
                )
            ) {

                alert(
                    'Hay horarios que se traslapan. Revisa los bloques y horarios personalizados.'
                );

                return;
            }


            const sinHorarios =
                todasLasFranjas.length ===
                0;


            if (
                sinHorarios
            ) {

                const confirmar =
                    window.confirm(
                        'No hay ningún horario seleccionado.\n\nSi continúas, se eliminará la disponibilidad guardada de este profesor para esta institución.\n\n¿Deseas continuar?'
                    );


                if (
                    !confirmar
                ) {
                    return;
                }
            }


            const nombreProfesor =
                profesorSelect.options[
                    profesorSelect.selectedIndex
                ]?.text ||
                'Profesor';


            guardando =
                true;


            saveButton.disabled =
                true;


            const textoOriginal =
                saveButton.innerHTML;


            saveButton.innerHTML = `
                <span>
                    Guardando...
                </span>
            `;


            try {

                /*
                |--------------------------------------------------------------------------
                | 1. ELIMINAR SOLO LA DISPONIBILIDAD DE LA INSTITUCIÓN ACTUAL
                |--------------------------------------------------------------------------
                |
                | Si estamos modificando Colegio, no tocamos Academia.
                | Si estamos modificando Academia, no tocamos Colegio.
                |
                */

                await eliminarDisponibilidadesActuales(
                    profesorId,
                    institucion
                );


                /*
                |--------------------------------------------------------------------------
                | 2. CREAR NUEVA DISPONIBILIDAD
                |--------------------------------------------------------------------------
                */

                if (
                    todasLasFranjas.length >
                    0
                ) {

                    await crearDisponibilidades(
                        profesorId,
                        institucion,
                        todasLasFranjas
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | 3. RECARGAR DESDE BACKEND
                |--------------------------------------------------------------------------
                */

                delete disponibilidades[
                    claveActual()
                ];


                delete personalizados[
                    claveActual()
                ];


                await cargarDisponibilidadAPI();


                const institucionTexto =
                    institucion ===
                    'academia'

                        ? 'Academia'

                        : 'Colegio';


                if (
                    sinHorarios
                ) {

                    alert(
                        `Disponibilidad eliminada correctamente.\n\nProfesor: ${nombreProfesor}\nInstitución: ${institucionTexto}`
                    );

                } else {

                    alert(
                        `Disponibilidad guardada correctamente.\n\nProfesor: ${nombreProfesor}\nInstitución: ${institucionTexto}\nFranjas guardadas: ${todasLasFranjas.length}`
                    );
                }


            } catch (error) {

                console.error(
                    'Error guardando disponibilidad:',
                    error
                );


                alert(
                    mensajeError(
                        error,
                        'No se pudo guardar la disponibilidad.'
                    )
                );


                /*
                |--------------------------------------------------------------------------
                | RECARGAMOS EL ESTADO REAL
                |--------------------------------------------------------------------------
                |
                | Si una petición intermedia falló, mostramos lo que realmente quedó
                | registrado en la base de datos.
                |
                */

                try {

                    await cargarDisponibilidadAPI();

                } catch {
                    // Ya mostramos el error principal.
                }

            } finally {

                guardando =
                    false;


                saveButton.disabled =
                    false;


                saveButton.innerHTML =
                    textoOriginal;
            }
        }
    );


    // =========================================================
    // ESCAPE
    // =========================================================

    document.addEventListener(
        'keydown',
        event => {

            if (
                event.key ===
                'Escape'
            ) {

                cerrarTodosCustomSelects();
            }
        }
    );


    // =========================================================
    // INICIO
    // =========================================================

    async function iniciar() {

        limpiarGrid();

        renderizarPersonalizados();

        actualizarResumen();

        actualizarTodosCustomSelects();


        await cargarProfesores();


        actualizarTodosCustomSelects();
    }


    iniciar();

});
