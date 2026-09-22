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

    const aulaSelect =
        document.getElementById('asignacion-aula');

    const diasContainer =
        document.getElementById('asignacion-dias');

    const horaInicioInput =
        document.getElementById('asignacion-hora-inicio');

    const horaFinInput =
        document.getElementById('asignacion-hora-fin');

    const estadoSelect =
        document.getElementById('asignacion-estado');

    const guardarButton =
        document.getElementById('guardar-asignacion');

    const limpiarButton =
        document.getElementById('limpiar-asignacion');

    const buscarInput =
        document.getElementById('buscar-asignacion');

    const tabla =
        document.getElementById('asignaciones-body');

    const resumenDisponibilidad =
        document.getElementById(
            'resumen-disponibilidad-profesor'
        );

    const estadoDisponibilidad =
        document.getElementById(
            'estado-disponibilidad-asignacion'
        );


    if (
        !profesorSelect ||
        !institucionSelect ||
        !cursoSelect ||
        !gradoSelect ||
        !aulaSelect ||
        !diasContainer ||
        !horaInicioInput ||
        !horaFinInput ||
        !estadoSelect ||
        !guardarButton ||
        !tabla
    ) {
        return;
    }


    // =========================================================
    // STORAGE
    // =========================================================

    const DISPONIBILIDAD_KEY =
        'nextlevel_disponibilidades';

    const HORARIOS_KEY =
        'nextlevel_horarios';


    function leerStorage(clave) {

        try {

            const datos =
                JSON.parse(
                    localStorage.getItem(clave) || '[]'
                );

            return Array.isArray(datos)
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
            JSON.stringify(datos)
        );
    }


    function cargarDisponibilidades() {

        return leerStorage(
            DISPONIBILIDAD_KEY
        );
    }


    function cargarHorarios() {

        return leerStorage(
            HORARIOS_KEY
        );
    }


    function guardarHorarios(
        horarios
    ) {

        guardarStorage(
            HORARIOS_KEY,
            horarios
        );
    }


    // =========================================================
    // CATÁLOGOS
    // =========================================================

    function obtenerProfesores() {

        return leerStorage(
            'nextlevel_profesores'
        );
    }


    function obtenerCursos() {

        return leerStorage(
            'nextlevel_cursos'
        );
    }


    function obtenerGrados() {

        return leerStorage(
            'nextlevel_grados'
        );
    }


    function obtenerAulas() {

        return leerStorage(
            'nextlevel_aulas'
        );
    }


    // =========================================================
    // UTILIDADES
    // =========================================================

    const dias = {
        1: 'Lunes',
        2: 'Martes',
        3: 'Miércoles',
        4: 'Jueves',
        5: 'Viernes',
        6: 'Sábado'
    };


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

            aula: `
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
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M7 8h3"/>
                    <path d="M14 8h3"/>
                    <path d="M7 12h3"/>
                    <path d="M14 12h3"/>
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
    // CUSTOM SELECTS
    // =========================================================

    function obtenerWrapperSelect(
        select
    ) {

        return document.querySelector(
            `[data-custom-select="${select.id}"]`
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
            wrapper.dataset.search !==
            'false';

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


        wrapper.dataset.ready =
            'true';


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
                    event.key ===
                    'Escape'
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


    function cerrarCustomSelect(
        wrapper
    ) {

        wrapper.classList.remove(
            'open'
        );


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


    function renderizarOpcionesCustom(
        wrapper,
        busqueda = ''
    ) {

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
            normalizarTexto(
                busqueda
            );


        const opciones =
            Array.from(
                select.options
            )
                .filter(
                    option => {

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
                    }
                );


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
                .map(
                    option => {

                        const seleccionada =
                            String(select.value) ===
                            String(option.value);


                        return `

                            <button
                                type="button"
                                class="
                                    nl-option
                                    ${seleccionada
                                        ? 'selected'
                                        : ''
                                    }
                                "
                                data-value="${escaparHTML(option.value)}"
                            >

                                <span class="nl-option-icon">
                                    ${iconoSelector(
                                        wrapper.dataset.icon
                                    )}
                                </span>

                                <span class="nl-option-text">
                                    ${escaparHTML(
                                        option.textContent
                                    )}
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
                    }
                )
                .join('');


        contenedor
            .querySelectorAll(
                '.nl-option'
            )
            .forEach(
                boton => {

                    boton.addEventListener(
                        'click',
                        () => {

                            select.value =
                                boton.dataset.value;


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


    function actualizarTodosCustomSelect() {

        [
            profesorSelect,
            institucionSelect,
            cursoSelect,
            gradoSelect,
            aulaSelect,
            estadoSelect
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
    // DÍAS SELECCIONADOS
    // =========================================================

    function obtenerDiasSeleccionados() {

        return Array.from(
            diasContainer.querySelectorAll(
                '.dia-asignacion:checked'
            )
        )
            .map(
                checkbox =>
                    Number(
                        checkbox.value
                    )
            )
            .filter(Boolean)
            .sort(
                (a, b) =>
                    a - b
            );
    }


    function actualizarEstiloDias() {

        diasContainer
            .querySelectorAll(
                '.dia-asignacion'
            )
            .forEach(
                checkbox => {

                    const label =
                        checkbox.closest(
                            '.dia-asignacion-label'
                        );


                    label?.classList.toggle(
                        'activo',
                        checkbox.checked
                    );
                }
            );
    }


    // =========================================================
    // PROFESORES
    // =========================================================

    function cargarProfesores() {

        const profesores =
            obtenerProfesores();

        const valorAnterior =
            profesorSelect.value;


        profesorSelect.innerHTML =
            '<option value="">Seleccionar profesor</option>';


        profesores
            .slice()
            .sort(
                (a, b) => {

                    const nombreA =
                        `${a.nombre ?? ''} ${a.apellido_paterno ?? ''}`;

                    const nombreB =
                        `${b.nombre ?? ''} ${b.apellido_paterno ?? ''}`;


                    return nombreA.localeCompare(
                        nombreB,
                        'es'
                    );
                }
            )
            .forEach(
                profesor => {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        profesor.id;


                    option.textContent =
                        `${profesor.nombre ?? ''} ${profesor.apellido_paterno ?? ''}`
                            .trim();


                    profesorSelect.appendChild(
                        option
                    );
                }
            );


        const existeAnterior =
            Array.from(
                profesorSelect.options
            ).some(
                option =>
                    option.value ===
                    valorAnterior
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

    function cargarCursos() {

        const cursos =
            obtenerCursos();

        const institucion =
            normalizarTexto(
                institucionSelect.value
            );

        const valorAnterior =
            cursoSelect.value;


        cursoSelect.innerHTML =
            '<option value="">Seleccionar curso</option>';


        let filtrados = [];


        if (
            institucion ===
            'colegio'
        ) {

            filtrados =
                cursos.filter(
                    curso => {

                        const nivel =
                            normalizarTexto(
                                curso.nivel
                            );


                        return [
                            'primaria',
                            'secundaria',
                            'todos'
                        ].includes(
                            nivel
                        );
                    }
                );
        }


        if (
            institucion ===
            'academia'
        ) {

            filtrados =
                cursos.filter(
                    curso =>
                        normalizarTexto(
                            curso.nivel
                        ) ===
                        'academia'
                );
        }


        filtrados =
            filtrados
                .filter(
                    curso =>
                        curso.activo !==
                        false
                )
                .sort(
                    (a, b) =>
                        String(
                            a.nombre || ''
                        ).localeCompare(
                            String(
                                b.nombre || ''
                            ),
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
                    curso.nombre;


                cursoSelect.appendChild(
                    option
                );
            }
        );


        const existeAnterior =
            Array.from(
                cursoSelect.options
            ).some(
                option =>
                    option.value ===
                    valorAnterior
            );


        cursoSelect.value =
            existeAnterior
                ? valorAnterior
                : '';


        cursoSelect.disabled =
            !institucion ||
            filtrados.length === 0;


        actualizarCustomSelect(
            cursoSelect
        );
    }


    // =========================================================
    // GRADOS
    // =========================================================

    function cargarGrados() {

        const grados =
            obtenerGrados();

        const institucion =
            normalizarTexto(
                institucionSelect.value
            );

        const valorAnterior =
            gradoSelect.value;

        const gradoContainer =
            document.getElementById(
                'grado-container'
            );


        gradoSelect.innerHTML =
            '<option value="">Seleccionar grado</option>';


        if (
            institucion !==
            'colegio'
        ) {

            gradoSelect.disabled =
                true;

            gradoSelect.value =
                '';


            gradoContainer?.classList.add(
                'opacity-50'
            );


            actualizarCustomSelect(
                gradoSelect
            );


            return;
        }


        gradoContainer?.classList.remove(
            'opacity-50'
        );


        const filtrados =
            grados
                .filter(
                    grado => {

                        const nivel =
                            normalizarTexto(
                                grado.nivel
                            );


                        return [
                            'primaria',
                            'secundaria'
                        ].includes(
                            nivel
                        );
                    }
                )
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
                            nivelA !==
                            nivelB
                        ) {

                            return nivelA.localeCompare(
                                nivelB,
                                'es'
                            );
                        }


                        return String(
                            a.nombre_completo ??
                            a.nombre ??
                            ''
                        ).localeCompare(
                            String(
                                b.nombre_completo ??
                                b.nombre ??
                                ''
                            ),
                            'es',
                            {
                                numeric:
                                    true
                            }
                        );
                    }
                );


        filtrados.forEach(
            grado => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    grado.id;


                const nombre =
                    grado.nombre_completo ??
                    grado.nombre ??
                    'Grado';


                const nivel =
                    normalizarTexto(
                        grado.nivel
                    ) ===
                    'primaria'
                        ? 'Primaria'
                        : 'Secundaria';


                option.textContent =
                    `${nombre} · ${nivel}`;


                gradoSelect.appendChild(
                    option
                );
            }
        );


        gradoSelect.disabled =
            filtrados.length === 0;


        const existeAnterior =
            Array.from(
                gradoSelect.options
            ).some(
                option =>
                    option.value ===
                    valorAnterior
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
    // AULAS
    // =========================================================

    function cargarAulas() {

        const aulas =
            obtenerAulas();

        const institucion =
            normalizarTexto(
                institucionSelect.value
            );

        const valorAnterior =
            aulaSelect.value;


        aulaSelect.innerHTML =
            '<option value="">Seleccionar aula</option>';


        let filtradas = [];


        if (
            institucion ===
            'academia'
        ) {

            filtradas =
                aulas.filter(
                    aula =>
                        normalizarTexto(
                            aula.nivel
                        ) ===
                        'academia'
                );
        }


        if (
            institucion ===
            'colegio'
        ) {

            filtradas =
                aulas.filter(
                    aula => {

                        const nivel =
                            normalizarTexto(
                                aula.nivel
                            );


                        return [
                            'primaria',
                            'secundaria',
                            'todos'
                        ].includes(
                            nivel
                        );
                    }
                );
        }


        filtradas
            .sort(
                (a, b) =>
                    String(
                        a.nombre || ''
                    ).localeCompare(
                        String(
                            b.nombre || ''
                        ),
                        'es'
                    )
            )
            .forEach(
                aula => {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        aula.id;


                    option.textContent =
                        aula.capacidad

                            ? `${aula.nombre} · ${aula.capacidad} alumnos`

                            : aula.nombre;


                    aulaSelect.appendChild(
                        option
                    );
                }
            );


        aulaSelect.disabled =
            !institucion ||
            filtradas.length === 0;


        const existeAnterior =
            Array.from(
                aulaSelect.options
            ).some(
                option =>
                    option.value ===
                    valorAnterior
            );


        aulaSelect.value =
            existeAnterior
                ? valorAnterior
                : '';


        actualizarCustomSelect(
            aulaSelect
        );
    }


    // =========================================================
    // DISPONIBILIDAD
    // =========================================================

    function disponibilidadProfesorActual() {

        const profesorId =
            profesorSelect.value;

        const institucion =
            institucionSelect.value;


        if (
            !profesorId ||
            !institucion
        ) {

            return [];
        }


        return cargarDisponibilidades()
            .filter(
                item => {

                    const id =
                        item.profesor_id ??
                        item.profesorId;


                    return (
                        String(id) ===
                        String(profesorId) &&

                        normalizarTexto(
                            item.institucion
                        ) ===
                        normalizarTexto(
                            institucion
                        )
                    );
                }
            );
    }


    function disponibilidadDelDia(
        dia
    ) {

        return disponibilidadProfesorActual()
            .filter(
                item =>
                    Number(
                        item.dia_semana ??
                        item.dia
                    ) ===
                    Number(dia)
            )
            .map(
                item => ({

                    inicio:
                        item.hora_inicio ??
                        item.horaInicio,

                    fin:
                        item.hora_fin ??
                        item.horaFin
                })
            )
            .filter(
                rango =>
                    rango.inicio &&
                    rango.fin
            )
            .sort(
                (a, b) =>
                    a.inicio.localeCompare(
                        b.inicio
                    )
            );
    }


    function obtenerRangoComun(
        diasSeleccionados
    ) {

        if (
            !diasSeleccionados.length
        ) {
            return null;
        }


        let intersecciones =
            disponibilidadDelDia(
                diasSeleccionados[0]
            );


        if (!intersecciones.length) {
            return null;
        }


        for (
            let i = 1;
            i < diasSeleccionados.length;
            i++
        ) {

            const rangosDia =
                disponibilidadDelDia(
                    diasSeleccionados[i]
                );

            const nuevas =
                [];


            intersecciones.forEach(
                actual => {

                    rangosDia.forEach(
                        rango => {

                            const inicio =
                                actual.inicio >
                                rango.inicio

                                    ? actual.inicio
                                    : rango.inicio;


                            const fin =
                                actual.fin <
                                rango.fin

                                    ? actual.fin
                                    : rango.fin;


                            if (
                                inicio <
                                fin
                            ) {

                                nuevas.push({
                                    inicio,
                                    fin
                                });
                            }
                        }
                    );
                }
            );


            intersecciones =
                nuevas;


            if (!intersecciones.length) {
                return null;
            }
        }


        intersecciones.sort(
            (a, b) =>
                a.inicio.localeCompare(
                    b.inicio
                )
        );


        return (
            intersecciones[0] ||
            null
        );
    }


    function actualizarDisponibilidadProfesor() {

        const disponibilidad =
            disponibilidadProfesorActual();


        diasContainer.innerHTML =
            '';


        horaInicioInput.value =
            '';

        horaFinInput.value =
            '';

        horaInicioInput.disabled =
            true;

        horaFinInput.disabled =
            true;


        ocultarEstado();


        if (
            !profesorSelect.value ||
            !institucionSelect.value
        ) {

            diasContainer.innerHTML = `
                <span class="px-2 text-xs text-slate-400">
                    Selecciona profesor e institución.
                </span>
            `;


            resumenDisponibilidad.innerHTML = `
                <span class="text-xs text-slate-400">
                    Selecciona profesor e institución para consultar su disponibilidad.
                </span>
            `;


            return;
        }


        if (!disponibilidad.length) {

            diasContainer.innerHTML = `
                <span class="px-2 text-xs text-red-500">
                    No existen días disponibles.
                </span>
            `;


            resumenDisponibilidad.innerHTML = `
                <span
                    class="
                        rounded-full
                        border border-red-200
                        bg-red-50
                        px-3 py-1.5
                        text-xs
                        font-medium
                        text-red-600
                    "
                >
                    Este profesor no tiene disponibilidad configurada.
                </span>
            `;


            return;
        }


        const diasDisponibles =
            Array.from(
                new Set(
                    disponibilidad.map(
                        item =>
                            Number(
                                item.dia_semana ??
                                item.dia
                            )
                    )
                )
            )
                .filter(Boolean)
                .sort(
                    (a, b) =>
                        a - b
                );


        diasContainer.innerHTML = `

            <div
                class="
                    grid
                    grid-cols-2
                    gap-2
                    sm:grid-cols-3
                "
            >

                ${
                    diasDisponibles
                        .map(
                            dia => `

                                <label
                                    class="
                                        dia-asignacion-label
                                        flex
                                        cursor-pointer
                                        items-center
                                        gap-2
                                        rounded-lg
                                        border
                                        border-slate-200
                                        bg-slate-50
                                        px-3
                                        py-2.5
                                        text-xs
                                        font-semibold
                                        text-slate-600
                                    "
                                >

                                    <input
                                        type="checkbox"
                                        value="${dia}"
                                        class="
                                            dia-asignacion
                                            h-4 w-4
                                            rounded
                                            border-slate-300
                                        "
                                        style="
                                            accent-color:#1B3A6B;
                                        "
                                    >

                                    <span>
                                        ${dias[dia]}
                                    </span>

                                </label>
                            `
                        )
                        .join('')
                }

            </div>
        `;


        diasContainer
            .querySelectorAll(
                '.dia-asignacion'
            )
            .forEach(
                checkbox => {

                    checkbox.addEventListener(
                        'change',
                        () => {

                            actualizarEstiloDias();

                            actualizarHorasPorDias();
                        }
                    );
                }
            );


        resumenDisponibilidad.innerHTML =
            disponibilidad
                .slice()
                .sort(
                    (a, b) => {

                        const diaA =
                            Number(
                                a.dia_semana ??
                                a.dia
                            );

                        const diaB =
                            Number(
                                b.dia_semana ??
                                b.dia
                            );


                        return (
                            diaA -
                            diaB
                        );
                    }
                )
                .map(
                    item => {

                        const dia =
                            Number(
                                item.dia_semana ??
                                item.dia
                            );

                        const inicio =
                            item.hora_inicio ??
                            item.horaInicio;

                        const fin =
                            item.hora_fin ??
                            item.horaFin;


                        return `

                            <span
                                class="
                                    rounded-full
                                    border border-blue-200
                                    bg-blue-50
                                    px-3 py-1.5
                                    text-xs
                                    font-medium
                                    text-blue-700
                                "
                            >
                                ${dias[dia]}
                                ·
                                ${inicio}
                                -
                                ${fin}
                            </span>
                        `;
                    }
                )
                .join('');
    }


    function actualizarHorasPorDias() {

        const diasSeleccionados =
            obtenerDiasSeleccionados();


        horaInicioInput.value =
            '';

        horaFinInput.value =
            '';


        ocultarEstado();


        if (!diasSeleccionados.length) {

            horaInicioInput.disabled =
                true;

            horaFinInput.disabled =
                true;

            return;
        }


        const rangoComun =
            obtenerRangoComun(
                diasSeleccionados
            );


        if (!rangoComun) {

            horaInicioInput.disabled =
                true;

            horaFinInput.disabled =
                true;


            mostrarEstado(
                false,
                'Los días seleccionados no tienen un rango disponible en común.'
            );


            return;
        }


        horaInicioInput.disabled =
            false;

        horaFinInput.disabled =
            false;


        horaInicioInput.value =
            rangoComun.inicio;

        horaFinInput.value =
            rangoComun.fin;


        validarHorarioVisual();
    }


    // =========================================================
    // CONFLICTOS
    // =========================================================

    function existeTraslape(
        inicioA,
        finA,
        inicioB,
        finB
    ) {

        return (
            inicioA < finB &&
            inicioB < finA
        );
    }


    function validarDentroDisponibilidad(
        profesorId,
        institucion,
        dia,
        horaInicio,
        horaFin
    ) {

        const disponibilidades =
            cargarDisponibilidades()
                .filter(
                    item => {

                        const id =
                            item.profesor_id ??
                            item.profesorId;


                        return (
                            String(id) ===
                            String(profesorId) &&

                            normalizarTexto(
                                item.institucion
                            ) ===
                            normalizarTexto(
                                institucion
                            ) &&

                            Number(
                                item.dia_semana ??
                                item.dia
                            ) ===
                            Number(dia)
                        );
                    }
                );


        return disponibilidades.some(
            item => {

                const inicio =
                    item.hora_inicio ??
                    item.horaInicio;

                const fin =
                    item.hora_fin ??
                    item.horaFin;


                return (
                    horaInicio >=
                    inicio &&
                    horaFin <=
                    fin
                );
            }
        );
    }


    function validarConflictos({
        horarioExistente,
        profesorId,
        aulaId,
        gradoId,
        dia,
        horaInicio,
        horaFin
    }) {

        const conflictoProfesor =
            horarioExistente.find(
                horario =>

                    String(
                        horario.profesor_id
                    ) ===
                    String(profesorId) &&

                    Number(
                        horario.dia_semana
                    ) ===
                    Number(dia) &&

                    existeTraslape(
                        horaInicio,
                        horaFin,
                        horario.hora_inicio,
                        horario.hora_fin
                    )
            );


        if (conflictoProfesor) {

            return {
                valido: false,
                mensaje:
                    'El profesor ya tiene otra clase asignada en ese horario.'
            };
        }


        const conflictoAula =
            horarioExistente.find(
                horario =>

                    String(
                        horario.aula_id
                    ) ===
                    String(aulaId) &&

                    Number(
                        horario.dia_semana
                    ) ===
                    Number(dia) &&

                    existeTraslape(
                        horaInicio,
                        horaFin,
                        horario.hora_inicio,
                        horario.hora_fin
                    )
            );


        if (conflictoAula) {

            return {
                valido: false,
                mensaje:
                    'El aula ya está ocupada en ese horario.'
            };
        }


        if (gradoId) {

            const conflictoGrado =
                horarioExistente.find(
                    horario =>

                        horario.grado_id &&

                        String(
                            horario.grado_id
                        ) ===
                        String(gradoId) &&

                        Number(
                            horario.dia_semana
                        ) ===
                        Number(dia) &&

                        existeTraslape(
                            horaInicio,
                            horaFin,
                            horario.hora_inicio,
                            horario.hora_fin
                        )
                );


            if (conflictoGrado) {

                return {
                    valido: false,
                    mensaje:
                        'El grado ya tiene otra clase en ese horario.'
                };
            }
        }


        return {
            valido: true,
            mensaje:
                'Horario disponible.'
        };
    }


    // =========================================================
    // ESTADO VALIDACIÓN
    // =========================================================

    function mostrarEstado(
        valido,
        texto
    ) {

        if (!estadoDisponibilidad) {
            return;
        }


        estadoDisponibilidad.className =
            'mt-4 rounded-xl border p-3 text-sm';


        if (valido) {

            estadoDisponibilidad.classList.add(
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700'
            );


            estadoDisponibilidad.textContent =
                `✓ ${texto}`;

        } else {

            estadoDisponibilidad.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );


            estadoDisponibilidad.textContent =
                `✕ ${texto}`;
        }
    }


    function ocultarEstado() {

        estadoDisponibilidad?.classList.add(
            'hidden'
        );
    }


    function validarHorarioVisual() {

        const profesorId =
            profesorSelect.value;

        const institucion =
            institucionSelect.value;

        const aulaId =
            aulaSelect.value;

        const gradoId =
            gradoSelect.value;

        const seleccionados =
            obtenerDiasSeleccionados();

        const horaInicio =
            horaInicioInput.value;

        const horaFin =
            horaFinInput.value;


        if (
            !profesorId ||
            !institucion ||
            !seleccionados.length ||
            !horaInicio ||
            !horaFin
        ) {

            ocultarEstado();

            return false;
        }


        if (
            horaInicio >=
            horaFin
        ) {

            mostrarEstado(
                false,
                'La hora de fin debe ser posterior a la hora de inicio.'
            );


            return false;
        }


        for (
            const dia of
            seleccionados
        ) {

            if (
                !validarDentroDisponibilidad(
                    profesorId,
                    institucion,
                    dia,
                    horaInicio,
                    horaFin
                )
            ) {

                mostrarEstado(
                    false,
                    `El profesor no está disponible el ${dias[dia]}.`
                );


                return false;
            }


            if (aulaId) {

                const validacion =
                    validarConflictos({

                        horarioExistente:
                            cargarHorarios(),

                        profesorId,

                        aulaId,

                        gradoId,

                        dia,

                        horaInicio,

                        horaFin
                    });


                if (!validacion.valido) {

                    mostrarEstado(
                        false,
                        `${dias[dia]}: ${validacion.mensaje}`
                    );


                    return false;
                }
            }
        }


        mostrarEstado(
            true,
            aulaId
                ? 'Horario disponible.'
                : 'Profesor disponible. Selecciona un aula.'
        );


        return true;
    }


    // =========================================================
    // INSTITUCIÓN
    // =========================================================

    function actualizarInstitucion() {

        cargarCursos();

        cargarGrados();

        cargarAulas();

        actualizarDisponibilidadProfesor();

        validarHorarioVisual();

        actualizarTodosCustomSelect();
    }


    // =========================================================
    // LIMPIAR
    // =========================================================

    function limpiarFormulario() {

        profesorSelect.value =
            '';

        institucionSelect.value =
            '';

        cursoSelect.innerHTML =
            '<option value="">Seleccionar curso</option>';

        gradoSelect.innerHTML =
            '<option value="">Seleccionar grado</option>';

        aulaSelect.innerHTML =
            '<option value="">Seleccionar aula</option>';


        cursoSelect.disabled =
            true;

        gradoSelect.disabled =
            true;

        aulaSelect.disabled =
            true;


        diasContainer.innerHTML = `
            <span class="px-2 text-xs text-slate-400">
                Selecciona profesor e institución.
            </span>
        `;


        horaInicioInput.value =
            '';

        horaFinInput.value =
            '';

        horaInicioInput.disabled =
            true;

        horaFinInput.disabled =
            true;


        estadoSelect.value =
            'activo';


        resumenDisponibilidad.innerHTML = `
            <span class="text-xs text-slate-400">
                Selecciona profesor e institución.
            </span>
        `;


        ocultarEstado();

        actualizarTodosCustomSelect();
    }


    // =========================================================
    // GUARDAR
    // =========================================================

    guardarButton.addEventListener(
        'click',
        () => {

            const profesorId =
                profesorSelect.value;

            const institucion =
                institucionSelect.value;

            const cursoId =
                cursoSelect.value;

            const gradoId =
                gradoSelect.value ||
                null;

            const aulaId =
                aulaSelect.value;

            const seleccionados =
                obtenerDiasSeleccionados();

            const horaInicio =
                horaInicioInput.value;

            const horaFin =
                horaFinInput.value;

            const estado =
                estadoSelect.value;


            if (!profesorId) {

                alert(
                    'Selecciona un profesor.'
                );

                return;
            }


            if (!institucion) {

                alert(
                    'Selecciona una institución.'
                );

                return;
            }


            if (!cursoId) {

                alert(
                    'Selecciona un curso.'
                );

                return;
            }


            if (
                institucion ===
                'colegio' &&
                !gradoId
            ) {

                alert(
                    'Selecciona un grado y sección.'
                );

                return;
            }


            if (!aulaId) {

                alert(
                    'Selecciona un aula.'
                );

                return;
            }


            if (!seleccionados.length) {

                alert(
                    'Selecciona al menos un día.'
                );

                return;
            }


            if (
                !horaInicio ||
                !horaFin
            ) {

                alert(
                    'Selecciona hora de inicio y fin.'
                );

                return;
            }


            const horarios =
                cargarHorarios();


            for (
                const dia of
                seleccionados
            ) {

                if (
                    !validarDentroDisponibilidad(
                        profesorId,
                        institucion,
                        dia,
                        horaInicio,
                        horaFin
                    )
                ) {

                    alert(
                        `El profesor no está disponible el ${dias[dia]}.`
                    );

                    return;
                }


                const validacion =
                    validarConflictos({

                        horarioExistente:
                            horarios,

                        profesorId,

                        aulaId,

                        gradoId,

                        dia,

                        horaInicio,

                        horaFin
                    });


                if (!validacion.valido) {

                    alert(
                        `${dias[dia]}: ${validacion.mensaje}`
                    );

                    return;
                }
            }


            const baseId =
                Date.now();


            seleccionados.forEach(
                (dia, indice) => {

                    horarios.push({

                        id:
                            baseId +
                            indice,

                        profesor_id:
                            Number(
                                profesorId
                            ),

                        curso_id:
                            Number(
                                cursoId
                            ),

                        grado_id:
                            gradoId

                                ? Number(
                                    gradoId
                                )

                                : null,

                        aula_id:
                            Number(
                                aulaId
                            ),

                        institucion,

                        dia_semana:
                            dia,

                        hora_inicio:
                            horaInicio,

                        hora_fin:
                            horaFin,

                        estado
                    });
                }
            );


            guardarHorarios(
                horarios
            );


            renderizarAsignaciones();


            const nombresDias =
                seleccionados
                    .map(
                        dia =>
                            dias[dia]
                    )
                    .join(', ');


            alert(
                `Asignación creada correctamente.\n\n` +
                `Días: ${nombresDias}\n` +
                `Horario: ${horaInicio} - ${horaFin}`
            );


            cursoSelect.value =
                '';

            aulaSelect.value =
                '';


            diasContainer
                .querySelectorAll(
                    '.dia-asignacion'
                )
                .forEach(
                    checkbox => {

                        checkbox.checked =
                            false;
                    }
                );


            actualizarEstiloDias();


            horaInicioInput.value =
                '';

            horaFinInput.value =
                '';

            horaInicioInput.disabled =
                true;

            horaFinInput.disabled =
                true;


            actualizarTodosCustomSelect();
        }
    );


    // =========================================================
    // TABLA
    // =========================================================

    function renderizarAsignaciones() {

        const horarios =
            cargarHorarios();

        const profesores =
            obtenerProfesores();

        const cursos =
            obtenerCursos();

        const grados =
            obtenerGrados();

        const aulas =
            obtenerAulas();


        tabla.innerHTML =
            '';


        if (!horarios.length) {

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

                        <div class="flex flex-col items-center">

                            <div
                                class="
                                    mb-3
                                    flex h-12 w-12
                                    items-center
                                    justify-center
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
                                No hay asignaciones creadas
                            </p>

                            <p class="mt-1 text-sm text-slate-400">
                                Crea tu primera clase usando el formulario.
                            </p>

                        </div>

                    </td>

                </tr>
            `;


            return;
        }


        horarios
            .slice()
            .sort(
                (a, b) =>

                    Number(
                        a.dia_semana
                    ) -
                    Number(
                        b.dia_semana
                    ) ||

                    String(
                        a.hora_inicio
                    ).localeCompare(
                        String(
                            b.hora_inicio
                        )
                    )
            )
            .forEach(
                horario => {

                    const profesor =
                        profesores.find(
                            item =>
                                String(item.id) ===
                                String(
                                    horario.profesor_id
                                )
                        );


                    const curso =
                        cursos.find(
                            item =>
                                String(item.id) ===
                                String(
                                    horario.curso_id
                                )
                        );


                    const grado =
                        grados.find(
                            item =>
                                String(item.id) ===
                                String(
                                    horario.grado_id
                                )
                        );


                    const aula =
                        aulas.find(
                            item =>
                                String(item.id) ===
                                String(
                                    horario.aula_id
                                )
                        );


                    const nombreProfesor =
                        profesor

                            ? `${profesor.nombre ?? ''} ${profesor.apellido_paterno ?? ''}`
                                .trim()

                            : 'Profesor no disponible';


                    const nombreCurso =
                        curso?.nombre ??
                        'Curso no disponible';


                    const nombreGrado =
                        horario.institucion ===
                        'academia'

                            ? '—'

                            : grado?.nombre_completo ??
                              grado?.nombre ??
                              '—';


                    const nombreAula =
                        aula?.nombre ??
                        'Aula no disponible';


                    const iniciales =
                        nombreProfesor
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


                    const estadoHTML =
                        horario.estado ===
                        'inactivo'

                            ? `
                                <span
                                    class="
                                        inline-flex
                                        rounded-full
                                        bg-slate-100
                                        px-2.5 py-1
                                        text-xs font-medium
                                        text-slate-600
                                    "
                                >
                                    Inactivo
                                </span>
                            `

                            : `
                                <span
                                    class="
                                        inline-flex
                                        rounded-full
                                        bg-emerald-100
                                        px-2.5 py-1
                                        text-xs font-medium
                                        text-emerald-700
                                    "
                                >
                                    Activo
                                </span>
                            `;


                    const fila =
                        document.createElement(
                            'tr'
                        );


                    fila.className =
                        'asignacion-row asignaciones-table-row';


                    fila.dataset.id =
                        horario.id;


                    fila.innerHTML = `

                        <td class="px-5 py-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="
                                        flex h-9 w-9
                                        shrink-0
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-blue-50
                                        text-xs
                                        font-bold
                                        text-[#1B3A6B]
                                    "
                                >
                                    ${escaparHTML(
                                        iniciales
                                    )}
                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-slate-800">
                                        ${escaparHTML(
                                            nombreProfesor
                                        )}
                                    </p>

                                    <p class="mt-0.5 text-xs text-slate-400">
                                        ${
                                            horario.institucion ===
                                            'academia'

                                                ? 'Academia'

                                                : 'Colegio'
                                        }
                                    </p>

                                </div>

                            </div>

                        </td>


                        <td class="px-5 py-4 text-sm text-slate-600">
                            ${escaparHTML(
                                nombreCurso
                            )}
                        </td>


                        <td class="px-5 py-4 text-sm text-slate-600">
                            ${escaparHTML(
                                nombreGrado
                            )}
                        </td>


                        <td class="px-5 py-4 text-sm text-slate-600">
                            ${escaparHTML(
                                nombreAula
                            )}
                        </td>


                        <td class="px-5 py-4 text-sm font-medium text-slate-700">

                            ${
                                dias[
                                    horario.dia_semana
                                ] ??
                                horario.dia_semana
                            }

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
                                ${horario.hora_inicio}
                                -
                                ${horario.hora_fin}
                            </span>

                        </td>


                        <td class="px-5 py-4">
                            ${estadoHTML}
                        </td>


                        <td class="px-5 py-4 text-right">

                            <button
                                type="button"
                                class="
                                    eliminar-asignacion
                                    inline-flex
                                    h-9 w-9
                                    items-center
                                    justify-center
                                    rounded-lg
                                    text-red-500
                                    transition
                                    hover:bg-red-50
                                    hover:text-red-700
                                "
                                data-id="${horario.id}"
                                title="Eliminar"
                            >

                                <svg
                                    class="h-4 w-4"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M3 6h18"/>
                                    <path d="M8 6V4h8v2"/>
                                    <path d="M19 6l-1 14H6L5 6"/>
                                </svg>

                            </button>

                        </td>
                    `;


                    tabla.appendChild(
                        fila
                    );
                }
            );
    }


    // =========================================================
    // ELIMINAR
    // =========================================================

    tabla.addEventListener(
        'click',
        event => {

            const boton =
                event.target.closest(
                    '.eliminar-asignacion'
                );


            if (!boton) {
                return;
            }


            if (
                !confirm(
                    '¿Deseas eliminar esta asignación?'
                )
            ) {
                return;
            }


            const id =
                boton.dataset.id;


            const horarios =
                cargarHorarios()
                    .filter(
                        horario =>
                            String(
                                horario.id
                            ) !==
                            String(id)
                    );


            guardarHorarios(
                horarios
            );


            renderizarAsignaciones();
        }
    );


    // =========================================================
    // BUSCADOR TABLA
    // =========================================================

    buscarInput?.addEventListener(
        'input',
        () => {

            const texto =
                normalizarTexto(
                    buscarInput.value
                );


            tabla
                .querySelectorAll(
                    '.asignacion-row'
                )
                .forEach(
                    fila => {

                        fila.classList.toggle(
                            'hidden',
                            !normalizarTexto(
                                fila.textContent
                            ).includes(
                                texto
                            )
                        );
                    }
                );
        }
    );


    // =========================================================
    // EVENTOS
    // =========================================================

    institucionSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                institucionSelect
            );


            actualizarInstitucion();
        }
    );


    profesorSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                profesorSelect
            );


            actualizarDisponibilidadProfesor();

            validarHorarioVisual();
        }
    );


    cursoSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                cursoSelect
            );
        }
    );


    gradoSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                gradoSelect
            );


            validarHorarioVisual();
        }
    );


    aulaSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                aulaSelect
            );


            validarHorarioVisual();
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


    horaInicioInput.addEventListener(
        'change',
        validarHorarioVisual
    );


    horaFinInput.addEventListener(
        'change',
        validarHorarioVisual
    );


    limpiarButton?.addEventListener(
        'click',
        limpiarFormulario
    );


    // =========================================================
    // STORAGE
    // =========================================================

    window.addEventListener(
        'storage',
        event => {

            if (
                event.key ===
                DISPONIBILIDAD_KEY
            ) {

                actualizarDisponibilidadProfesor();
            }


            if (
                event.key ===
                HORARIOS_KEY
            ) {

                renderizarAsignaciones();
            }


            if (
                [
                    'nextlevel_profesores',
                    'nextlevel_cursos',
                    'nextlevel_grados',
                    'nextlevel_aulas'
                ].includes(
                    event.key
                )
            ) {

                cargarProfesores();

                actualizarInstitucion();
            }
        }
    );


    window.addEventListener(
        'focus',
        () => {

            cargarProfesores();

            cargarCursos();

            cargarGrados();

            cargarAulas();

            actualizarDisponibilidadProfesor();

            renderizarAsignaciones();

            actualizarTodosCustomSelect();
        }
    );


    // =========================================================
    // INICIAL
    // =========================================================

    cursoSelect.disabled =
        true;

    gradoSelect.disabled =
        true;

    aulaSelect.disabled =
        true;


    cargarProfesores();

    cargarCursos();

    cargarGrados();

    cargarAulas();

    actualizarDisponibilidadProfesor();

    renderizarAsignaciones();

    actualizarTodosCustomSelect();

});