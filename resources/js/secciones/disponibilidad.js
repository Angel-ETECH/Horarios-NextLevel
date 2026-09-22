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
    // PERSONALIZADOS
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
    // STORAGE
    // =========================================================

    const PROFESORES_KEY =
        'nextlevel_profesores';

    const DISPONIBILIDAD_KEY =
        'nextlevel_disponibilidades';


    const DURACION_BLOQUE_GRID_MIN =
        60;


    const diasCortos = {

        1: 'Lun',
        2: 'Mar',
        3: 'Mié',
        4: 'Jue',
        5: 'Vie',
        6: 'Sáb'

    };


    // =========================================================
    // ESTADO
    // =========================================================

    const disponibilidades =
        {};

    const personalizados =
        {};


    // =========================================================
    // STORAGE HELPERS
    // =========================================================

    function leerStorage(
        clave
    ) {

        try {

            const datos =
                JSON.parse(
                    localStorage.getItem(
                        clave
                    ) || '[]'
                );


            return Array.isArray(
                datos
            )
                ? datos
                : [];

        } catch (error) {

            console.error(
                `Error leyendo ${clave}:`,
                error
            );


            return [];
        }
    }


    function guardarStorage(
        clave,
        datos
    ) {

        localStorage.setItem(
            clave,
            JSON.stringify(
                datos
            )
        );
    }


    function cargarTodasLasDisponibilidades() {

        return leerStorage(
            DISPONIBILIDAD_KEY
        );
    }


    function guardarTodasLasDisponibilidades(
        lista
    ) {

        guardarStorage(
            DISPONIBILIDAD_KEY,
            lista
        );
    }


    // =========================================================
    // UTILIDADES
    // =========================================================

    function normalizarTexto(
        valor
    ) {

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


    function esc(
        valor
    ) {

        return String(
            valor ?? ''
        )
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
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


    // =========================================================
    // CUSTOM SELECT
    // =========================================================

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

                        search.value =
                            '';
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
    // CARGAR PROFESORES
    // =========================================================

    function cargarProfesores() {

        const profesores =
            leerStorage(
                PROFESORES_KEY
            );


        const profesorSeleccionado =
            profesorSelect.value;


        const institucionActual =
            institucionSelect.value ||
            'colegio';


        profesorSelect.innerHTML = `

            <option value="">
                Seleccionar profesor
            </option>
        `;


        const disponibles =
            profesores
                .filter(
                    profesor => {

                        /*
                         * Solo profesores activos.
                         */
                        if (
                            profesor.estado &&
                            profesor.estado !==
                            'activo'
                        ) {

                            return false;
                        }


                        /*
                         * Compatibilidad con profesores antiguos.
                         */
                        if (
                            !Array.isArray(
                                profesor.instituciones
                            ) ||
                            !profesor.instituciones.length
                        ) {

                            return true;
                        }


                        return profesor.instituciones.includes(
                            institucionActual
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


        disponibles.forEach(
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


        const seleccionExiste =
            Array.from(
                profesorSelect.options
            )
                .some(
                    option =>
                        option.value ===
                        profesorSeleccionado
                );


        profesorSelect.value =
            seleccionExiste
                ? profesorSeleccionado
                : '';


        if (
            disponibles.length ===
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


        actualizarCustomSelect(
            profesorSelect
        );
    }


    // =========================================================
    // CLAVE
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
    // OBTENER SELECCIONADOS
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

                    ids.add(
                        `${slot.dataset.dia}-${slot.dataset.hora}`
                    );
                }
            );


        return Array.from(
            ids
        );
    }


    // =========================================================
    // PERSONALIZADOS ACTUALES
    // =========================================================

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
    // MINUTOS
    // =========================================================

    function minutosEntre(
        horaInicio,
        horaFin
    ) {

        const [
            hInicio,
            mInicio
        ] =
            horaInicio
                .split(':')
                .map(Number);


        const [
            hFin,
            mFin
        ] =
            horaFin
                .split(':')
                .map(Number);


        return Math.max(
            0,

            (
                hFin * 60 +
                mFin
            ) -

            (
                hInicio * 60 +
                mInicio
            )
        );
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


        countElement.textContent =
            seleccionados.length +
            personalizadosData.length;


        daysElement.textContent =
            dias.size;


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


        /*
         * En móvil mantenemos texto legible.
         */
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


    // =========================================================
    // LIMPIAR GRID
    // =========================================================

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
    // CARGAR VISUAL
    // =========================================================

    function cargarDisponibilidad() {

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
    // HIDRATAR STORAGE
    // =========================================================

    function hidratarDesdeStorage(
        clave
    ) {

        if (
            disponibilidades[clave] !==
            undefined ||
            personalizados[clave] !==
            undefined
        ) {

            return;
        }


        const separador =
            clave.lastIndexOf(
                '-'
            );


        const profesorId =
            clave.substring(
                0,
                separador
            );


        const institucion =
            clave.substring(
                separador + 1
            );


        const guardadas =
            cargarTodasLasDisponibilidades()
                .filter(
                    item => {

                        const id =
                            item.profesor_id ??
                            item.profesorId;


                        return (

                            String(id) ===
                            String(profesorId) &&

                            item.institucion ===
                            institucion
                        );
                    }
                );


        const idsGrid =
            [];

        const custom =
            [];


        guardadas.forEach(
            item => {

                const dia =
                    Number(
                        item.dia_semana ??
                        item.dia
                    );


                const horaInicio =
                    item.hora_inicio ??
                    item.horaInicio;


                const horaFin =
                    item.hora_fin ??
                    item.horaFin;


                if (
                    !dia ||
                    !horaInicio ||
                    !horaFin
                ) {

                    return;
                }


                const [
                    hi,
                    mi
                ] =
                    horaInicio
                        .split(':')
                        .map(Number);


                const [
                    hf,
                    mf
                ] =
                    horaFin
                        .split(':')
                        .map(Number);


                const inicioMin =
                    hi * 60 +
                    mi;


                const finMin =
                    hf * 60 +
                    mf;


                const duracion =
                    finMin -
                    inicioMin;


                const alineado =

                    mi === 0 &&

                    mf === 0 &&

                    duracion > 0 &&

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

                            `${dia}-${String(hora).padStart(
                                2,
                                '0'
                            )}:${String(minuto).padStart(
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
                            `${Date.now()}-${Math.random()
                                .toString(36)
                                .slice(2,8)}`,

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


    // =========================================================
    // CAMBIO PROFESOR
    // =========================================================

    function alCambiarSeleccion() {

        actualizarCustomSelect(
            profesorSelect
        );


        if (
            !profesorSelect.value
        ) {

            limpiarGrid();

            renderizarPersonalizados();

            actualizarResumen();

            return;
        }


        const clave =
            claveActual();


        hidratarDesdeStorage(
            clave
        );


        cargarDisponibilidad();

        renderizarPersonalizados();

        actualizarResumen();
    }


    profesorSelect.addEventListener(
        'change',
        alCambiarSeleccion
    );


    // =========================================================
    // CAMBIO INSTITUCIÓN
    // =========================================================

    institucionSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                institucionSelect
            );


            cargarProfesores();


            limpiarGrid();

            renderizarPersonalizados();

            actualizarResumen();


            if (
                profesorSelect.value
            ) {

                alCambiarSeleccion();
            }
        }
    );


    // =========================================================
    // CAMBIO DÍA PERSONALIZADO
    // =========================================================

    diaPersonalizadoSelect?.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                diaPersonalizadoSelect
            );
        }
    );


    // =========================================================
    // SLOT
    // =========================================================

    slots.forEach(
        slot => {

            slot.addEventListener(
                'click',
                () => {

                    if (
                        !profesorSelect.value
                    ) {

                        alert(
                            'Primero selecciona un profesor.'
                        );


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
    // TODO
    // =========================================================

    selectAllButton?.addEventListener(
        'click',
        () => {

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


                            const [
                                hAnterior,
                                mAnterior
                            ] =
                                anterior
                                    .split(':')
                                    .map(Number);


                            const esperado =
                                hAnterior * 60 +
                                mAnterior +
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
                                    hAnterior * 60 +
                                    mAnterior +
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
                        inicio !== null &&
                        anterior !== null
                    ) {

                        const [
                            hAnterior,
                            mAnterior
                        ] =
                            anterior
                                .split(':')
                                .map(Number);


                        const fin =
                            hAnterior * 60 +
                            mAnterior +
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
                (a,b) =>
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


    // =========================================================
    // AGREGAR PERSONALIZADO
    // =========================================================

    agregarPersonalizadoButton?.addEventListener(
        'click',
        () => {

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
                    diaPersonalizadoSelect.value
                );


            const horaInicio =
                inicioPersonalizadoInput.value;


            const horaFin =
                finPersonalizadoInput.value;


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


            const traslape =
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
                traslape
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
                        `${Date.now()}-${Math.random()
                            .toString(36)
                            .slice(2,8)}`,

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


    // =========================================================
    // QUITAR PERSONALIZADO
    // =========================================================

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
                            item.id !==
                            button.dataset.id
                    );


            renderizarPersonalizados();
        }
    );


    // =========================================================
    // GUARDAR
    // =========================================================

    saveButton?.addEventListener(
        'click',
        () => {

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


            const todasLasFranjas =
                [

                    ...franjasGrid,

                    ...personalizadosData.map(
                        item => ({

                            dia_semana:
                                item.dia,

                            hora_inicio:
                                item.horaInicio,

                            hora_fin:
                                item.horaFin
                        })
                    )
                ];


            const todas =
                cargarTodasLasDisponibilidades();


            const restantes =
                todas.filter(
                    item => {

                        const id =
                            item.profesor_id ??
                            item.profesorId;


                        return !(

                            String(
                                id
                            ) ===
                            String(
                                profesorId
                            ) &&

                            item.institucion ===
                            institucion
                        );
                    }
                );


            todasLasFranjas.forEach(
                franja => {

                    restantes.push({

                        profesor_id:
                            String(
                                profesorId
                            ),

                        institucion,

                        dia_semana:
                            Number(
                                franja.dia_semana
                            ),

                        hora_inicio:
                            franja.hora_inicio,

                        hora_fin:
                            franja.hora_fin
                    });
                }
            );


            guardarTodasLasDisponibilidades(
                restantes
            );


            const nombreProfesor =
                profesorSelect.options[
                    profesorSelect.selectedIndex
                ]?.text ||
                'Profesor';


            const institucionTexto =
                institucion ===
                'academia'
                    ? 'Academia'
                    : 'Colegio';


            alert(

                `✅ Disponibilidad guardada correctamente.\n\n` +

                `Profesor: ${nombreProfesor}\n` +

                `Institución: ${institucionTexto}\n\n` +

                `Esta información solamente indica cuándo puede trabajar el profesor.\n` +

                `Todavía no se ha creado ninguna clase.`

            );


            actualizarResumen();
        }
    );


    // =========================================================
    // STORAGE
    // =========================================================

    window.addEventListener(
        'storage',
        event => {

            if (
                event.key ===
                PROFESORES_KEY
            ) {

                cargarProfesores();
            }


            if (
                event.key ===
                DISPONIBILIDAD_KEY &&
                profesorSelect.value
            ) {

                const clave =
                    claveActual();


                delete disponibilidades[
                    clave
                ];


                delete personalizados[
                    clave
                ];


                alCambiarSeleccion();
            }
        }
    );


    // =========================================================
    // FOCUS
    // =========================================================

    window.addEventListener(
        'focus',
        () => {

            const seleccionado =
                profesorSelect.value;


            cargarProfesores();


            const existe =
                Array.from(
                    profesorSelect.options
                )
                    .some(
                        option =>
                            option.value ===
                            seleccionado
                    );


            if (
                seleccionado &&
                existe
            ) {

                profesorSelect.value =
                    seleccionado;
            }


            actualizarCustomSelect(
                profesorSelect
            );
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

    cargarProfesores();

    limpiarGrid();

    renderizarPersonalizados();

    actualizarResumen();

    actualizarTodosCustomSelects();

});