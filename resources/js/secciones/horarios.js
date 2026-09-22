document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // ELEMENTOS
    // =========================================================

    const contenedor =
        document.getElementById('horarios-render-container');

    if (!contenedor) {
        return;
    }


    const tabsInstitucion =
        document.querySelectorAll('.institucion-tab');

    const vistaBotones =
        document.querySelectorAll('.vista-btn');

    const chkCompleto =
        document.getElementById('chk-semana-completa');


    const filtroProfesor =
        document.getElementById('horario-profesor');

    const filtroAula =
        document.getElementById('horario-aula');

    const filtroGrado =
        document.getElementById('horario-grado');

    const filtroCurso =
        document.getElementById('horario-curso');


    const btnReset =
        document.getElementById('btn-reset');

    const btnPdf =
        document.getElementById('btn-pdf');

    const btnExcel =
        document.getElementById('btn-excel');


    const tituloEl =
        document.getElementById('titulo-horario');

    const subtituloEl =
        document.getElementById('subtitulo-horario');


    const statProfesores =
        document.getElementById('total-profesores');

    const statAulas =
        document.getElementById('total-aulas');

    const statClases =
        document.getElementById('total-clases');

    const statEstado =
        document.getElementById('estado-horario');

    const statEstadoPunto =
        document.getElementById('estado-punto');


    const mensajeEl =
        document.getElementById('horario-mensaje');

    const modoEdicionEl =
        document.getElementById('modo-edicion-horario');


    // =========================================================
    // MODAL
    // =========================================================

    const eliminarHorarioModal =
        document.getElementById('eliminar-horario-modal');

    const eliminarHorarioOverlay =
        document.getElementById('eliminar-horario-overlay');

    const closeEliminarHorarioModal =
        document.getElementById('close-eliminar-horario-modal');

    const cancelEliminarHorarioModal =
        document.getElementById('cancel-eliminar-horario-modal');

    const confirmEliminarHorario =
        document.getElementById('confirm-eliminar-horario');


    const eliminarHorarioProfesor =
        document.getElementById('eliminar-horario-profesor');

    const eliminarHorarioCurso =
        document.getElementById('eliminar-horario-curso');

    const eliminarHorarioDia =
        document.getElementById('eliminar-horario-dia');

    const eliminarHorarioHora =
        document.getElementById('eliminar-horario-hora');

    const eliminarHorarioAula =
        document.getElementById('eliminar-horario-aula');


    // =========================================================
    // STORAGE
    // =========================================================

    const KEYS = {

        horarios:
            'nextlevel_horarios',

        disponibilidades:
            'nextlevel_disponibilidades',

        profesores:
            'nextlevel_profesores',

        aulas:
            'nextlevel_aulas',

        grados:
            'nextlevel_grados',

        cursos:
            'nextlevel_cursos'
    };


    // =========================================================
    // DÍAS
    // =========================================================

    const DIAS_NOMBRES = [
        '',
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes',
        'Sábado'
    ];


    const DIAS_CORTOS = [
        '',
        'LUN',
        'MAR',
        'MIÉ',
        'JUE',
        'VIE',
        'SÁB'
    ];


    const DIAS_SEMANA = [
        1,
        2,
        3,
        4,
        5,
        6
    ];


    // =========================================================
    // ESTADO
    // =========================================================

    let institucionActiva =
        'colegio';

    let vistaActual =
        'profesor';

    let bloqueArrastradoId =
        null;

    let gruposVisibles =
        [];

    let horarioAEliminarId =
        null;


    // =========================================================
    // STORAGE
    // =========================================================

    function leer(clave) {

        try {

            const datos =
                JSON.parse(
                    localStorage.getItem(clave) ||
                    '[]'
                );


            return Array.isArray(datos)
                ? datos
                : [];

        } catch (error) {

            console.error(
                `Error leyendo ${clave}`,
                error
            );


            return [];
        }
    }


    function guardar(
        clave,
        datos
    ) {

        localStorage.setItem(
            clave,
            JSON.stringify(datos)
        );
    }


    // =========================================================
    // UTILIDADES
    // =========================================================

    function esc(valor) {

        return String(valor ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    function normalizarTexto(valor) {

        return String(valor ?? '')
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(
                /[\u0300-\u036f]/g,
                ''
            )
            .replace(
                /\s+/g,
                ' '
            );
    }


    // =========================================================
    // CUSTOM SELECT ICONOS
    // =========================================================

    function iconoFiltro(tipo) {

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
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M20 21a8 8 0 0 0-16 0"/>
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
                >
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M7 8h3"/>
                    <path d="M14 8h3"/>
                    <path d="M7 12h3"/>
                    <path d="M14 12h3"/>
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

    function wrapperFiltro(select) {

        if (!select) {
            return null;
        }


        return document.querySelector(
            `[data-horario-select="${select.id}"]`
        );
    }


    function construirCustomFiltro(wrapper) {

        if (
            !wrapper ||
            wrapper.dataset.ready === 'true'
        ) {
            return;
        }


        const select =
            document.getElementById(
                wrapper.dataset.horarioSelect
            );


        if (!select) {
            return;
        }


        const label =
            wrapper.dataset.label ||
            'Filtro';


        const placeholder =
            wrapper.dataset.placeholder ||
            'Seleccionar';


        const icono =
            wrapper.dataset.icon ||
            'curso';


        wrapper.innerHTML = `

            <button
                type="button"
                class="horario-custom-trigger"
                aria-expanded="false"
            >

                <span class="horario-custom-icon">
                    ${iconoFiltro(icono)}
                </span>

                <span class="horario-custom-content">

                    <span class="horario-custom-small">
                        ${esc(label)}
                    </span>

                    <span class="horario-custom-text">
                        ${esc(placeholder)}
                    </span>

                </span>

                <svg
                    class="horario-custom-arrow"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m6 9 6 6 6-6"/>
                </svg>

            </button>


            <div class="horario-custom-menu">

                <div class="horario-custom-search-wrap">

                    <svg
                        class="horario-custom-search-icon"
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
                        class="horario-custom-search"
                        autocomplete="off"
                        placeholder="Buscar..."
                    >

                </div>

                <div class="horario-custom-options"></div>

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

                if (trigger.disabled) {
                    return;
                }


                cerrarTodosFiltros(
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

                    buscador.value =
                        '';


                    renderOpcionesFiltro(
                        wrapper
                    );


                    setTimeout(
                        () =>
                            buscador.focus(),
                        40
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
            event => {

                if (
                    event.key === 'Escape'
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


    function cerrarFiltro(wrapper) {

        wrapper?.classList.remove(
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
                wrapper => {

                    if (
                        wrapper !== excepto
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
            document.getElementById(
                wrapper.dataset.horarioSelect
            );


        const container =
            wrapper.querySelector(
                '.horario-custom-options'
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
                    option => {

                        return normalizarTexto(
                            option.textContent
                        ).includes(
                            query
                        );
                    }
                );


        if (!opciones.length) {

            container.innerHTML = `
                <div class="horario-custom-empty">
                    No se encontraron resultados
                </div>
            `;

            return;
        }


        container.innerHTML =
            opciones
                .map(
                    option => {

                        const activo =
                            String(select.value) ===
                            String(option.value);


                        return `

                            <button
                                type="button"

                                class="
                                    horario-custom-option
                                    ${
                                        activo
                                            ? 'selected'
                                            : ''
                                    }
                                "

                                data-value="${esc(
                                    option.value
                                )}"
                            >

                                <span class="horario-option-icon">

                                    ${iconoFiltro(
                                        wrapper.dataset.icon
                                    )}

                                </span>


                                <span class="horario-option-text">

                                    ${esc(
                                        option.textContent
                                    )}

                                </span>


                                <svg
                                    class="horario-option-check"
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
                '.horario-custom-option'
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
                                        bubbles: true
                                    }
                                )
                            );


                            actualizarCustomFiltro(
                                select
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


        renderOpcionesFiltro(
            wrapper
        );
    }


    function actualizarTodosCustomFiltros() {

        [
            filtroProfesor,
            filtroAula,
            filtroGrado,
            filtroCurso
        ]
            .filter(Boolean)
            .forEach(
                actualizarCustomFiltro
            );
    }


    document
        .querySelectorAll(
            '[data-horario-select]'
        )
        .forEach(
            construirCustomFiltro
        );


    document.addEventListener(
        'click',
        event => {

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
    // PERMISOS
    // =========================================================

    function puedeEditarHorario() {

        const rol =
            String(
                localStorage.getItem(
                    'nextlevel_rol'
                ) ||
                'administrador'
            )
                .trim()
                .toLowerCase();


        return [
            'administrador',
            'director'
        ].includes(
            rol
        );
    }


    if (
        modoEdicionEl &&
        !puedeEditarHorario()
    ) {

        modoEdicionEl.innerHTML = `
            <span class="h-2 w-2 rounded-full bg-slate-400"></span>
            Solo lectura
        `;


        modoEdicionEl.className =
            'inline-flex shrink-0 items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600';
    }


    // =========================================================
    // HORAS
    // =========================================================

    function aMinutos(hora) {

        if (!hora) {
            return 0;
        }


        const partes =
            String(hora).split(':');


        return (
            (Number(partes[0]) || 0) *
            60
        ) +
        (
            Number(partes[1]) ||
            0
        );
    }


    function aTexto(minutos) {

        const horas =
            String(
                Math.floor(
                    minutos /
                    60
                )
            )
                .padStart(
                    2,
                    '0'
                );


        const restantes =
            String(
                minutos %
                60
            )
                .padStart(
                    2,
                    '0'
                );


        return `${horas}:${restantes}`;
    }


    function duracionMinutos(
        bloque
    ) {

        return Math.max(
            0,

            aMinutos(
                bloque.hora_fin
            ) -

            aMinutos(
                bloque.hora_inicio
            )
        );
    }


    function textoDuracion(
        bloque
    ) {

        const minutos =
            duracionMinutos(
                bloque
            );


        const horas =
            Math.floor(
                minutos /
                60
            );


        const restantes =
            minutos %
            60;


        if (
            horas > 0 &&
            restantes === 0
        ) {

            return `${horas} h`;
        }


        if (
            horas > 0
        ) {

            return `${horas} h ${restantes} min`;
        }


        return `${restantes} min`;
    }


    function existeTraslape(
        inicioA,
        finA,
        inicioB,
        finB
    ) {

        return (
            aMinutos(inicioA) <
            aMinutos(finB) &&

            aMinutos(inicioB) <
            aMinutos(finA)
        );
    }


    // =========================================================
    // INSTITUCIÓN
    // =========================================================

    function normalizarInstitucion(
        valor
    ) {

        return String(
            valor ||
            'colegio'
        )
            .trim()
            .toLowerCase();
    }


    function textoInstitucion() {

        return (
            institucionActiva ===
            'colegio'
        )
            ? 'Colegio'
            : 'Academia';
    }


    // =========================================================
    // CATÁLOGOS
    // =========================================================

    function getCatalogos() {

        const profesores =
            leer(
                KEYS.profesores
            );

        const aulas =
            leer(
                KEYS.aulas
            );

        const grados =
            leer(
                KEYS.grados
            );

        const cursos =
            leer(
                KEYS.cursos
            );


        return {

            profesores,
            aulas,
            grados,
            cursos,


            mapProf:
                new Map(
                    profesores.map(
                        item => [
                            String(item.id),
                            item
                        ]
                    )
                ),


            mapAula:
                new Map(
                    aulas.map(
                        item => [
                            String(item.id),
                            item
                        ]
                    )
                ),


            mapGrado:
                new Map(
                    grados.map(
                        item => [
                            String(item.id),
                            item
                        ]
                    )
                ),


            mapCurso:
                new Map(
                    cursos.map(
                        item => [
                            String(item.id),
                            item
                        ]
                    )
                )
        };
    }


    // =========================================================
    // NOMBRES
    // =========================================================

    function nombreProfesor(
        profesor
    ) {

        if (!profesor) {

            return 'Profesor no disponible';
        }


        const nombre =
            profesor.nombre ||
            profesor.nombres ||
            '';


        const apellidos = [

            profesor.apellido_paterno,

            profesor.apellidoPaterno,

            profesor.apellido,

            profesor.apellido_materno

        ]
            .filter(Boolean)
            .join(' ');


        return (
            `${nombre} ${apellidos}`
                .replace(
                    /\s+/g,
                    ' '
                )
                .trim() ||

            profesor.nombre_completo ||

            profesor.nombreCompleto ||

            'Profesor'
        );
    }


    function nombreAula(
        aula
    ) {

        if (!aula) {

            return 'Aula no disponible';
        }


        return (
            aula.nombre ||
            aula.nombre_aula ||
            aula.codigo ||
            'Aula'
        );
    }


    function nombreCurso(
        curso
    ) {

        if (!curso) {

            return 'Curso no disponible';
        }


        return (
            curso.nombre ||
            curso.nombre_curso ||
            curso.codigo ||
            'Curso'
        );
    }


    function nombreGrado(
        grado
    ) {

        if (!grado) {

            return 'Sin grado';
        }


        let nombre =
            grado.nombre_completo ||
            grado.nombreCompleto ||
            grado.nombre ||
            `${grado.grado || ''} ${grado.seccion || ''}`
                .trim() ||
            'Grado';


        const nivel =
            normalizarTexto(
                grado.nivel
            );


        if (
            nivel === 'primaria'
        ) {

            if (
                !normalizarTexto(
                    nombre
                ).includes(
                    'primaria'
                )
            ) {

                nombre +=
                    ' · Primaria';
            }
        }


        if (
            nivel === 'secundaria'
        ) {

            if (
                !normalizarTexto(
                    nombre
                ).includes(
                    'secundaria'
                )
            ) {

                nombre +=
                    ' · Secundaria';
            }
        }


        return nombre;
    }


    // =========================================================
    // IDENTIDAD REAL DEL GRADO
    // =========================================================
    //
    // No dependemos solamente de grado_id.
    //
    // Si por datos antiguos existen dos IDs distintos que
    // representan "1° B · Primaria", también los consideramos
    // el mismo grado.
    // =========================================================

    function claveRealGrado(
        gradoId
    ) {

        if (
            gradoId === null ||
            gradoId === undefined ||
            gradoId === ''
        ) {

            return '';
        }


        const {
            mapGrado
        } =
            getCatalogos();


        const grado =
            mapGrado.get(
                String(
                    gradoId
                )
            );


        if (!grado) {

            return `id:${String(
                gradoId
            )}`;
        }


        const nivel =
            normalizarTexto(
                grado.nivel ??
                grado.nivel_educativo ??
                grado.tipo_nivel ??
                grado.nivelEducativo ??
                ''
            );


        const nombre =
            normalizarTexto(
                grado.nombre_completo ??
                grado.nombreCompleto ??
                grado.nombre ??
                grado.grado ??
                ''
            );


        const seccion =
            normalizarTexto(
                grado.seccion ??
                ''
            );


        return [
            nivel,
            nombre,
            seccion
        ]
            .filter(Boolean)
            .join('|');
    }


    function esMismoGrado(
        gradoA,
        gradoB
    ) {

        if (
            gradoA === null ||
            gradoA === undefined ||
            gradoB === null ||
            gradoB === undefined
        ) {

            return false;
        }


        if (
            String(gradoA) ===
            String(gradoB)
        ) {

            return true;
        }


        const claveA =
            claveRealGrado(
                gradoA
            );


        const claveB =
            claveRealGrado(
                gradoB
            );


        return (
            claveA !== '' &&
            claveB !== '' &&
            claveA === claveB
        );
    }


    // =========================================================
    // HORARIOS
    // =========================================================

    function cargarHorarios() {

        return leer(
            KEYS.horarios
        )
            .filter(
                horario =>
                    horario &&
                    horario.hora_inicio &&
                    horario.hora_fin &&
                    horario.dia_semana
            );
    }


    function guardarHorarios(
        horarios
    ) {

        guardar(
            KEYS.horarios,
            horarios
        );
    }


    function datosClase(
        bloque
    ) {

        const {
            mapProf,
            mapAula,
            mapGrado,
            mapCurso
        } =
            getCatalogos();


        return {

            profesor:
                nombreProfesor(
                    mapProf.get(
                        String(
                            bloque.profesor_id
                        )
                    )
                ),


            aula:
                nombreAula(
                    mapAula.get(
                        String(
                            bloque.aula_id
                        )
                    )
                ),


            grado:
                nombreGrado(
                    mapGrado.get(
                        String(
                            bloque.grado_id
                        )
                    )
                ),


            curso:
                nombreCurso(
                    mapCurso.get(
                        String(
                            bloque.curso_id
                        )
                    )
                )
        };
    }


    // =========================================================
    // DISPONIBILIDAD DEL PROFESOR
    // =========================================================

    function profesorDisponible(
        bloque,
        dia,
        horaInicio,
        horaFin
    ) {

        const disponibilidades =
            leer(
                KEYS.disponibilidades
            );


        const profesorId =
            bloque.profesor_id;


        const institucion =
            normalizarInstitucion(
                bloque.institucion
            );


        const rangos =
            disponibilidades
                .filter(
                    item => {

                        const id =
                            item.profesor_id ??
                            item.profesorId;


                        const diaItem =
                            item.dia_semana ??
                            item.dia;


                        return (

                            String(id) ===
                            String(profesorId) &&

                            normalizarInstitucion(
                                item.institucion
                            ) ===
                            institucion &&

                            Number(
                                diaItem
                            ) ===
                            Number(dia)
                        );
                    }
                );


        if (!rangos.length) {

            return false;
        }


        return rangos.some(
            rango => {

                const inicio =
                    rango.hora_inicio ??
                    rango.horaInicio;


                const fin =
                    rango.hora_fin ??
                    rango.horaFin;


                if (
                    !inicio ||
                    !fin
                ) {

                    return false;
                }


                return (

                    aMinutos(
                        horaInicio
                    ) >=
                    aMinutos(
                        inicio
                    ) &&

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


    // =========================================================
    // VALIDAR CONFLICTOS
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


        // -----------------------------------------------------
        // RANGO VÁLIDO
        // -----------------------------------------------------

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
                    'La hora de fin debe ser posterior a la hora de inicio.'
            };
        }


        // -----------------------------------------------------
        // DENTRO DE LA TABLA
        // -----------------------------------------------------

        if (
            aMinutos(
                horaInicio
            ) <
            420 ||
            aMinutos(
                horaFin
            ) >
            1200
        ) {

            return {

                valido:
                    false,

                mensaje:
                    'La clase debe mantenerse entre las 07:00 y las 20:00.'
            };
        }


        // -----------------------------------------------------
        // DISPONIBILIDAD PROFESOR
        // -----------------------------------------------------

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
                    `${datos.profesor} no está disponible el ${DIAS_NOMBRES[dia]} de ${horaInicio} a ${horaFin}.`
            };
        }


        const horarios =
            cargarHorarios();


        // -----------------------------------------------------
        // BUSCAR CUALQUIER CLASE QUE SE TRASLAPE
        // -----------------------------------------------------

        for (
            const otro of
            horarios
        ) {

            // La misma clase que estamos moviendo no cuenta.
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


            // Diferente institución.
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


            // Diferente día.
            if (
                Number(
                    otro.dia_semana
                ) !==
                Number(
                    dia
                )
            ) {

                continue;
            }


            // No se cruzan.
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


            // -------------------------------------------------
            // PROFESOR OCUPADO
            // -------------------------------------------------

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
                        `${datos.profesor} ya tiene ${datosOtro.curso} el ${DIAS_NOMBRES[dia]} de ${otro.hora_inicio} a ${otro.hora_fin}.`
                };
            }


            // -------------------------------------------------
            // AULA OCUPADA
            // -------------------------------------------------

            if (
                bloque.aula_id !==
                null &&

                bloque.aula_id !==
                undefined &&

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
                        `${datos.aula} ya está ocupada por ${datosOtro.curso} el ${DIAS_NOMBRES[dia]} de ${otro.hora_inicio} a ${otro.hora_fin}.`
                };
            }


            // -------------------------------------------------
            // GRADO OCUPADO
            // -------------------------------------------------

            if (
                bloque.grado_id &&
                otro.grado_id &&
                esMismoGrado(
                    otro.grado_id,
                    bloque.grado_id
                )
            ) {

                return {

                    valido:
                        false,

                    mensaje:
                        `${datos.grado} ya tiene ${datosOtro.curso} el ${DIAS_NOMBRES[dia]} de ${otro.hora_inicio} a ${otro.hora_fin}.`
                };
            }
        }


        return {

            valido:
                true,

            mensaje:
                'Horario disponible.'
        };
    }


    // =========================================================
    // COLORES
    // =========================================================

    const COLORES = [

        {
            bg:'#ECFDF5',
            border:'#A7F3D0',
            titulo:'#065F46',
            acento:'#10B981'
        },

        {
            bg:'#EEF2FF',
            border:'#C7D2FE',
            titulo:'#312E81',
            acento:'#4F46E5'
        },

        {
            bg:'#EFF6FF',
            border:'#BFDBFE',
            titulo:'#1E3A8A',
            acento:'#2563EB'
        },

        {
            bg:'#FFFBEB',
            border:'#FDE68A',
            titulo:'#78350F',
            acento:'#D97706'
        }
    ];


    const cacheColor =
        new Map();


    function colorCurso(
        nombre
    ) {

        const key =
            nombre ||
            'Curso';


        if (
            !cacheColor.has(
                key
            )
        ) {

            cacheColor.set(

                key,

                COLORES[
                    cacheColor.size %
                    COLORES.length
                ]
            );
        }


        return cacheColor.get(
            key
        );
    }


    // =========================================================
    // INSTITUCIÓN
    // =========================================================

    function horarioEsDeInstitucion(
        horario
    ) {

        return (
            normalizarInstitucion(
                horario.institucion
            ) ===
            institucionActiva
        );
    }


    // =========================================================
    // AGRUPACIÓN
    // =========================================================

    function getAgrupaPor() {

        return (
            vistaActual ===
            'aula-completa'
        )
            ? 'aula'
            : vistaActual;
    }


    function agruparBloques(
        bloques
    ) {

        const {
            mapProf,
            mapAula,
            mapGrado
        } =
            getCatalogos();


        const grupos =
            new Map();


        const tipo =
            getAgrupaPor();


        bloques.forEach(
            bloque => {

                let id;
                let nombre;
                let clave;


                if (
                    tipo ===
                    'profesor'
                ) {

                    id =
                        bloque.profesor_id;


                    const profesor =
                        mapProf.get(
                            String(id)
                        );


                    if (!profesor) {
                        return;
                    }


                    nombre =
                        nombreProfesor(
                            profesor
                        );


                    clave =
                        `prof-${id}`;

                } else if (
                    tipo ===
                    'aula'
                ) {

                    id =
                        bloque.aula_id;


                    const aula =
                        mapAula.get(
                            String(id)
                        );


                    if (!aula) {
                        return;
                    }


                    nombre =
                        nombreAula(
                            aula
                        );


                    clave =
                        `aula-${id}`;

                } else {

                    id =
                        bloque.grado_id;


                    const grado =
                        mapGrado.get(
                            String(id)
                        );


                    if (!grado) {
                        return;
                    }


                    nombre =
                        nombreGrado(
                            grado
                        );


                    clave =
                        `grado-${id}`;
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
                            nombre,
                            bloques:[]
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


        return Array.from(
            grupos.values()
        );
    }


    // =========================================================
    // POBLAR FILTROS
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
            select.value ||
            'todos';


        select.innerHTML =
            `<option value="todos">${textoDefault}</option>`;


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
                                true
                        }
                    )
            )
            .forEach(
                opcion => {

                    const element =
                        document.createElement(
                            'option'
                        );


                    element.value =
                        String(
                            opcion.id
                        );


                    element.textContent =
                        opcion.texto;


                    select.appendChild(
                        element
                    );
                }
            );


        const existe =
            Array.from(
                select.options
            )
                .some(
                    option =>
                        option.value ===
                        actual
                );


        select.value =
            existe
                ? actual
                : 'todos';


        actualizarCustomFiltro(
            select
        );
    }


    function cargarFiltros(
        bloques
    ) {

        const {
            mapProf,
            mapAula,
            mapGrado,
            mapCurso
        } =
            getCatalogos();


        const profesorMap =
            new Map();

        const aulaMap =
            new Map();

        const gradoMap =
            new Map();

        const cursoMap =
            new Map();


        bloques.forEach(
            bloque => {

                const p =
                    mapProf.get(
                        String(
                            bloque.profesor_id
                        )
                    );


                if (p) {

                    profesorMap.set(
                        String(
                            bloque.profesor_id
                        ),

                        {
                            id:
                                bloque.profesor_id,

                            texto:
                                nombreProfesor(
                                    p
                                )
                        }
                    );
                }


                const a =
                    mapAula.get(
                        String(
                            bloque.aula_id
                        )
                    );


                if (a) {

                    aulaMap.set(
                        String(
                            bloque.aula_id
                        ),

                        {
                            id:
                                bloque.aula_id,

                            texto:
                                nombreAula(
                                    a
                                )
                        }
                    );
                }


                const g =
                    mapGrado.get(
                        String(
                            bloque.grado_id
                        )
                    );


                if (g) {

                    gradoMap.set(
                        String(
                            bloque.grado_id
                        ),

                        {
                            id:
                                bloque.grado_id,

                            texto:
                                nombreGrado(
                                    g
                                )
                        }
                    );
                }


                const c =
                    mapCurso.get(
                        String(
                            bloque.curso_id
                        )
                    );


                if (c) {

                    cursoMap.set(
                        String(
                            bloque.curso_id
                        ),

                        {
                            id:
                                bloque.curso_id,

                            texto:
                                nombreCurso(
                                    c
                                )
                        }
                    );
                }
            }
        );


        poblarSelect(
            filtroProfesor,
            Array.from(
                profesorMap.values()
            ),
            'Todos los profesores'
        );


        poblarSelect(
            filtroAula,
            Array.from(
                aulaMap.values()
            ),
            'Todas las aulas'
        );


        poblarSelect(
            filtroGrado,
            Array.from(
                gradoMap.values()
            ),
            'Todos los grados'
        );


        poblarSelect(
            filtroCurso,
            Array.from(
                cursoMap.values()
            ),
            'Todos los cursos'
        );


        if (
            institucionActiva ===
            'academia'
        ) {

            filtroGrado.value =
                'todos';


            filtroGrado.disabled =
                true;

        } else {

            filtroGrado.disabled =
                false;
        }


        actualizarTodosCustomFiltros();
    }


    function aplicarFiltros(
        bloques
    ) {

        return bloques.filter(
            bloque => {

                if (
                    filtroProfesor.value !==
                    'todos' &&

                    String(
                        bloque.profesor_id
                    ) !==
                    filtroProfesor.value
                ) {

                    return false;
                }


                if (
                    filtroAula.value !==
                    'todos' &&

                    String(
                        bloque.aula_id
                    ) !==
                    filtroAula.value
                ) {

                    return false;
                }


                if (
                    filtroGrado.value !==
                    'todos' &&

                    String(
                        bloque.grado_id
                    ) !==
                    filtroGrado.value
                ) {

                    return false;
                }


                if (
                    filtroCurso.value !==
                    'todos' &&

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
            filtroCurso
        ]
            .filter(Boolean)
            .forEach(
                filtro => {

                    filtro.value =
                        'todos';
                }
            );


        actualizarTodosCustomFiltros();
    }


    // =========================================================
    // DETECTAR CONFLICTOS ACTUALES
    // =========================================================

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

                const a =
                    bloques[i];

                const b =
                    bloques[j];


                if (
                    normalizarInstitucion(
                        a.institucion
                    ) !==
                    normalizarInstitucion(
                        b.institucion
                    )
                ) {

                    continue;
                }


                if (
                    Number(
                        a.dia_semana
                    ) !==
                    Number(
                        b.dia_semana
                    )
                ) {

                    continue;
                }


                if (
                    !existeTraslape(
                        a.hora_inicio,
                        a.hora_fin,
                        b.hora_inicio,
                        b.hora_fin
                    )
                ) {

                    continue;
                }


                const mismoProfesor =
                    String(
                        a.profesor_id
                    ) ===
                    String(
                        b.profesor_id
                    );


                const mismaAula =
                    a.aula_id &&
                    b.aula_id &&
                    String(
                        a.aula_id
                    ) ===
                    String(
                        b.aula_id
                    );


                const mismoGrado =
                    a.grado_id &&
                    b.grado_id &&
                    esMismoGrado(
                        a.grado_id,
                        b.grado_id
                    );


                if (
                    mismoProfesor ||
                    mismaAula ||
                    mismoGrado
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

        statProfesores.textContent =
            new Set(
                bloques.map(
                    item =>
                        item.profesor_id
                )
            ).size;


        statAulas.textContent =
            new Set(
                bloques.map(
                    item =>
                        item.aula_id
                )
            ).size;


        statClases.textContent =
            bloques.length;


        const tieneConflictos =
            detectarConflictosExistentes(
                bloques
            );


        if (
            tieneConflictos
        ) {

            statEstado.textContent =
                'Revisar conflictos';


            statEstadoPunto.style.background =
                '#DB0808';

        } else {

            statEstado.textContent =
                'Sin conflictos';


            statEstadoPunto.style.background =
                '#10B981';
        }
    }


    // =========================================================
    // DATOS SECUNDARIOS
    // =========================================================

    function datosSecundarios(
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

                segundo:
                    datos.aula,

                tercero:
                    datos.grado
            };
        }


        if (
            getAgrupaPor() ===
            'aula'
        ) {

            return {

                principal:
                    datos.curso,

                segundo:
                    datos.profesor,

                tercero:
                    datos.grado
            };
        }


        return {

            principal:
                datos.curso,

            segundo:
                datos.profesor,

            tercero:
                datos.aula
        };
    }


    // =========================================================
    // SLOTS
    // =========================================================

    function construirSlots() {

        const slots =
            [];


        for (
            let minutos = 420;
            minutos < 1200;
            minutos += 60
        ) {

            slots.push({

                ini:
                    minutos,

                fin:
                    minutos +
                    60
            });
        }


        return slots;
    }


    function construirMatriz(
        bloques,
        dias,
        slots
    ) {

        const matriz =
            {};


        dias.forEach(
            dia => {

                matriz[dia] =
                    new Array(
                        slots.length
                    )
                        .fill(
                            null
                        );
            }
        );


        dias.forEach(
            dia => {

                const clases =
                    bloques
                        .filter(
                            bloque =>
                                Number(
                                    bloque.dia_semana
                                ) ===
                                Number(dia)
                        )
                        .sort(
                            (a, b) =>
                                aMinutos(
                                    a.hora_inicio
                                ) -
                                aMinutos(
                                    b.hora_inicio
                                )
                        );


                clases.forEach(
                    bloque => {

                        const inicio =
                            aMinutos(
                                bloque.hora_inicio
                            );


                        const fin =
                            aMinutos(
                                bloque.hora_fin
                            );


                        const indiceInicio =
                            slots.findIndex(
                                slot =>
                                    slot.ini <=
                                    inicio &&

                                    inicio <
                                    slot.fin
                            );


                        if (
                            indiceInicio === -1
                        ) {

                            return;
                        }


                        let span =
                            0;


                        for (
                            let i =
                                indiceInicio;

                            i <
                            slots.length &&
                            slots[i].ini <
                            fin;

                            i++
                        ) {

                            span++;
                        }


                        span =
                            Math.max(
                                1,
                                span
                            );


                        /*
                         * Si por datos antiguos existen dos clases
                         * en la misma celda del mismo grupo,
                         * no pisamos silenciosamente la primera.
                         */
                        if (
                            matriz[dia][
                                indiceInicio
                            ] !==
                            null
                        ) {

                            console.warn(
                                'Conflicto visual detectado:',
                                bloque
                            );

                            return;
                        }


                        matriz[dia][
                            indiceInicio
                        ] = {

                            tipo:
                                'bloque',

                            bloque,

                            span
                        };


                        for (
                            let i =
                                indiceInicio +
                                1;

                            i <
                            indiceInicio +
                            span &&
                            i <
                            slots.length;

                            i++
                        ) {

                            if (
                                matriz[dia][i] ===
                                null
                            ) {

                                matriz[dia][i] = {
                                    tipo:
                                        'cont'
                                };
                            }
                        }
                    }
                );
            }
        );


        return matriz;
    }


    // =========================================================
    // TARJETA
    // =========================================================

    function tarjetaDetallada(
        bloque
    ) {

        const datos =
            datosSecundarios(
                bloque
            );


        const color =
            colorCurso(
                datos.principal
            );


        const btnEliminar =
            puedeEditarHorario()

                ? `
                    <button
                        type="button"
                        class="btn-eliminar-horario"
                        data-eliminar-horario-id="${esc(
                            bloque.id
                        )}"
                        draggable="false"
                        title="Eliminar clase"
                    >
                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M3 6h18"></path>
                            <path d="M8 6V4h8v2"></path>
                            <path d="M19 6l-1 14H6L5 6"></path>
                            <path d="M10 11v5"></path>
                            <path d="M14 11v5"></path>
                        </svg>
                    </button>
                `

                : '';


        return `
            <article
                class="horario-clase relative flex h-full flex-col overflow-hidden rounded-xl border p-3 shadow-sm"
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
                "
            >

                <div
                    class="absolute inset-y-0 left-0 w-1"
                    style="background:${color.acento};"
                ></div>


                ${btnEliminar}


                <div class="flex h-full flex-col justify-between pl-2 pr-7">

                    <div>

                        <p
                            class="text-[12px] font-extrabold leading-4"
                            style="color:${color.titulo};"
                        >
                            ${esc(
                                datos.principal
                            )}
                        </p>


                        <p
                            class="mt-2 text-[11px] font-semibold"
                            style="color:${color.acento};"
                        >
                            ${esc(
                                datos.segundo
                            )}
                        </p>


                        <p class="mt-1 text-[10px] text-slate-500">
                            ${esc(
                                datos.tercero
                            )}
                        </p>

                    </div>


                    <div class="mt-4 flex flex-wrap gap-1.5">

                        <span class="rounded-md bg-white px-2 py-1 text-[9px] font-semibold text-slate-500 shadow-sm">

                            ${esc(
                                bloque.hora_inicio
                            )}

                            -

                            ${esc(
                                bloque.hora_fin
                            )}

                        </span>


                        <span
                            class="rounded-md bg-white px-2 py-1 text-[9px] font-bold shadow-sm"
                            style="color:${color.acento};"
                        >

                            ${esc(
                                textoDuracion(
                                    bloque
                                )
                            )}

                        </span>

                    </div>

                </div>

            </article>
        `;
    }


    // =========================================================
    // TABLA
    // =========================================================

    function renderTablaGrupo(
        grupo
    ) {

        const slots =
            construirSlots();


        const matriz =
            construirMatriz(
                grupo.bloques,
                DIAS_SEMANA,
                slots
            );


        let headers =
            '';


        DIAS_SEMANA.forEach(
            dia => {

                headers += `
                    <th class="border-b border-r border-slate-200 bg-slate-50 px-3 py-3 text-center">

                        <span class="block text-xs font-extrabold" style="color:#0F2749;">
                            ${DIAS_NOMBRES[dia]}
                        </span>

                        <span class="block text-[9px] font-semibold text-slate-400">
                            ${DIAS_CORTOS[dia]}
                        </span>

                    </th>
                `;
            }
        );


        let filas =
            '';


        slots.forEach(
            (slot,index) => {

                filas += `
                    <tr style="height:88px;">

                        <th class="sticky left-0 z-10 w-[110px] border-b border-r border-slate-200 bg-white px-2 py-2 text-center">

                            <span class="block text-[10px] font-extrabold" style="color:#1B3A6B;">
                                ${index + 1}H
                            </span>

                            <span class="mt-1 block text-[9px] text-slate-400">
                                ${aTexto(slot.ini)}
                                -
                                ${aTexto(slot.fin)}
                            </span>

                        </th>
                `;


                DIAS_SEMANA.forEach(
                    dia => {

                        const celda =
                            matriz[dia][
                                index
                            ];


                        if (
                            celda?.tipo ===
                            'cont'
                        ) {

                            return;
                        }


                        if (
                            celda?.tipo ===
                            'bloque'
                        ) {

                            const altura =
                                celda.span *
                                88;


                            filas += `
                                <td
                                    rowspan="${celda.span}"
                                    class="horario-dropzone border-b border-r border-slate-200 p-1 align-top"
                                    data-grupo="${esc(
                                        grupo.clave
                                    )}"
                                    data-dia="${dia}"
                                    data-hora-inicio="${aTexto(
                                        slot.ini
                                    )}"
                                    style="height:${altura}px;"
                                >

                                    <div
                                        style="
                                            height:${
                                                altura -
                                                8
                                            }px;
                                        "
                                    >
                                        ${tarjetaDetallada(
                                            celda.bloque
                                        )}
                                    </div>

                                </td>
                            `;


                            return;
                        }


                        filas += `
                            <td
                                class="horario-dropzone border-b border-r border-slate-200 bg-white p-1"
                                data-grupo="${esc(
                                    grupo.clave
                                )}"
                                data-dia="${dia}"
                                data-hora-inicio="${aTexto(
                                    slot.ini
                                )}"
                            >

                                <div
                                    class="flex h-[80px] items-center justify-center rounded-lg border border-dashed border-slate-200 bg-slate-50/40 text-[8px] font-semibold uppercase tracking-wider text-slate-300"
                                >
                                    Libre
                                </div>

                            </td>
                        `;
                    }
                );


                filas += `
                    </tr>
                `;
            }
        );


        return `
            <section
                class="horario-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                data-grupo-card="${esc(
                    grupo.clave
                )}"
            >

                <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h3
                            class="text-base font-extrabold"
                            style="color:#0F2749;"
                        >
                            ${esc(
                                grupo.nombre
                            )}
                        </h3>

                        <p class="mt-1 text-xs text-slate-500">

                            ${grupo.bloques.length}

                            ${
                                grupo.bloques.length ===
                                1

                                    ? 'clase programada'

                                    : 'clases programadas'
                            }

                        </p>

                    </div>


                    <div class="flex flex-wrap items-center gap-2">

                        <span
                            class="rounded-full px-3 py-1.5 text-[11px] font-semibold"
                            style="
                                background:#F1F5F9;
                                color:#1B3A6B;
                            "
                        >
                            ${textoInstitucion()}
                        </span>


                        <button
                            type="button"
                            class="horario-export-btn btn-exportar-grupo"
                            data-exportar-grupo="${esc(
                                grupo.clave
                            )}"
                            data-formato="imagen"
                        >
                            Imagen
                        </button>


                        <button
                            type="button"
                            class="horario-export-btn horario-export-btn-pdf btn-exportar-grupo"
                            data-exportar-grupo="${esc(
                                grupo.clave
                            )}"
                            data-formato="pdf"
                        >
                            PDF
                        </button>

                    </div>

                </div>


                <div class="horario-scroll overflow-x-auto">

                    <table
                        class="horario-tabla w-full min-w-[1080px] table-fixed border-collapse"
                    >

                        <thead>
                            <tr>

                                <th
                                    class="sticky left-0 z-30 w-[110px] border-b border-r border-slate-200 bg-slate-50 px-2 py-3 text-center text-[10px] font-bold uppercase text-slate-500"
                                >
                                    Hora
                                </th>

                                ${headers}

                            </tr>
                        </thead>

                        <tbody>
                            ${filas}
                        </tbody>

                    </table>

                </div>

            </section>
        `;
    }


    // =========================================================
    // MENSAJES
    // =========================================================

    function mostrarMensaje(
        texto,
        tipo='ok'
    ) {

        const estilos = {

            ok:
                'border-emerald-200 bg-emerald-50 text-emerald-700',

            error:
                'border-red-200 bg-red-50 text-red-700',

            info:
                'border-blue-200 bg-blue-50 text-blue-700'
        };


        mensajeEl.className =
            `fixed bottom-5 right-5 z-[100] max-w-md rounded-2xl border p-4 text-sm font-medium shadow-2xl ${
                estilos[tipo] ||
                estilos.info
            }`;


        mensajeEl.textContent =
            texto;


        mensajeEl.classList.remove(
            'hidden'
        );


        clearTimeout(
            mostrarMensaje.timer
        );


        mostrarMensaje.timer =
            setTimeout(
                () => {

                    mensajeEl.classList.add(
                        'hidden'
                    );
                },
                5500
            );
    }


    // =========================================================
    // ELIMINAR
    // =========================================================

    function abrirModalEliminarHorario(
        id
    ) {

        const bloque =
            cargarHorarios()
                .find(
                    horario =>
                        String(
                            horario.id
                        ) ===
                        String(id)
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


        eliminarHorarioProfesor.textContent =
            datos.profesor;


        eliminarHorarioCurso.textContent =
            datos.curso;


        eliminarHorarioDia.textContent =
            DIAS_NOMBRES[
                Number(
                    bloque.dia_semana
                )
            ];


        eliminarHorarioHora.textContent =
            `${bloque.hora_inicio} - ${bloque.hora_fin}`;


        eliminarHorarioAula.textContent =
            datos.aula;


        eliminarHorarioModal.classList.remove(
            'hidden'
        );


        document.body.classList.add(
            'overflow-hidden'
        );
    }


    function cerrarModalEliminarHorario() {

        horarioAEliminarId =
            null;


        eliminarHorarioModal.classList.add(
            'hidden'
        );


        document.body.classList.remove(
            'overflow-hidden'
        );
    }


    function eliminarHorarioSeleccionado() {

        if (
            horarioAEliminarId === null
        ) {
            return;
        }


        const nuevos =
            leer(
                KEYS.horarios
            )
                .filter(
                    horario =>
                        String(
                            horario.id
                        ) !==
                        String(
                            horarioAEliminarId
                        )
                );


        guardarHorarios(
            nuevos
        );


        cerrarModalEliminarHorario();


        render();


        mostrarMensaje(
            'Horario eliminado correctamente.'
        );
    }


    // =========================================================
    // DRAG - CLAVE DE GRUPO
    // =========================================================

    function claveGrupoBloque(
        bloque
    ) {

        if (
            getAgrupaPor() ===
            'profesor'
        ) {

            return `prof-${bloque.profesor_id}`;
        }


        if (
            getAgrupaPor() ===
            'aula'
        ) {

            return `aula-${bloque.aula_id}`;
        }


        return `grado-${bloque.grado_id}`;
    }


    // =========================================================
    // PREVISUALIZAR MOVIMIENTO
    // =========================================================

    function obtenerValidacionZona(
        bloque,
        zona
    ) {

        const dia =
            Number(
                zona.dataset.dia
            );


        const horaInicio =
            zona.dataset.horaInicio;


        const duracion =
            duracionMinutos(
                bloque
            );


        if (
            !duracion
        ) {

            return {

                valido:
                    false,

                mensaje:
                    'La clase tiene una duración inválida.'
            };
        }


        const horaFin =
            aTexto(
                aMinutos(
                    horaInicio
                ) +
                duracion
            );


        return validarMovimiento(
            bloque,
            dia,
            horaInicio,
            horaFin
        );
    }


    // =========================================================
    // MOVER CLASE
    // =========================================================

    function moverClase(
        id,
        dia,
        horaInicio
    ) {

        const horarios =
            cargarHorarios();


        const index =
            horarios.findIndex(
                horario =>
                    String(
                        horario.id
                    ) ===
                    String(id)
            );


        if (
            index === -1
        ) {

            mostrarMensaje(
                'No se encontró la clase que deseas mover.',
                'error'
            );

            return false;
        }


        const bloque =
            horarios[index];


        const duracion =
            duracionMinutos(
                bloque
            );


        if (
            duracion <= 0
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
                ) +
                duracion
            );


        // -----------------------------------------------------
        // VALIDACIÓN GLOBAL
        // -----------------------------------------------------

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


        // -----------------------------------------------------
        // GUARDAR SOLO SI TODO ESTÁ CORRECTO
        // -----------------------------------------------------

        horarios[index] = {

            ...bloque,

            dia_semana:
                Number(
                    dia
                ),

            hora_inicio:
                horaInicio,

            hora_fin:
                horaFin
        };


        guardarHorarios(
            horarios
        );


        render();


        mostrarMensaje(
            `Clase movida al ${DIAS_NOMBRES[dia]} de ${horaInicio} a ${horaFin}.`
        );


        return true;
    }


    // =========================================================
    // LIMPIAR PREVIEW DRAG
    // =========================================================

    function limpiarEstadoDrag(
        zona
    ) {

        zona.classList.remove(
            'drag-over'
        );


        zona.style.background =
            '';


        zona.style.outline =
            '';


        zona.style.outlineOffset =
            '';
    }


    // =========================================================
    // ACTIVAR DRAG & DROP
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
                clase => {

                    clase.addEventListener(
                        'dragstart',
                        event => {

                            if (
                                event.target.closest(
                                    '.btn-eliminar-horario'
                                )
                            ) {

                                event.preventDefault();

                                return;
                            }


                            bloqueArrastradoId =
                                clase.dataset.horarioId;


                            clase.classList.add(
                                'dragging'
                            );


                            event.dataTransfer.effectAllowed =
                                'move';


                            event.dataTransfer.setData(
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


                            clase.classList.remove(
                                'dragging'
                            );


                            contenedor
                                .querySelectorAll(
                                    '.horario-dropzone'
                                )
                                .forEach(
                                    limpiarEstadoDrag
                                );
                        }
                    );
                }
            );


        contenedor
            .querySelectorAll(
                '.horario-dropzone'
            )
            .forEach(
                zona => {

                    zona.addEventListener(
                        'dragover',
                        event => {

                            event.preventDefault();


                            const id =
                                bloqueArrastradoId ||
                                event.dataTransfer.getData(
                                    'text/plain'
                                );


                            if (!id) {
                                return;
                            }


                            const bloque =
                                cargarHorarios()
                                    .find(
                                        horario =>
                                            String(
                                                horario.id
                                            ) ===
                                            String(id)
                                    );


                            if (!bloque) {
                                return;
                            }


                            /*
                             * No permitimos cambiar de profesor,
                             * aula o grado arrastrando entre grupos.
                             */
                            if (
                                zona.dataset.grupo !==
                                claveGrupoBloque(
                                    bloque
                                )
                            ) {

                                zona.classList.remove(
                                    'drag-over'
                                );


                                zona.style.background =
                                    'rgba(219,8,8,.08)';


                                zona.style.outline =
                                    '2px dashed #DB0808';


                                zona.style.outlineOffset =
                                    '-4px';


                                event.dataTransfer.dropEffect =
                                    'none';


                                return;
                            }


                            const validacion =
                                obtenerValidacionZona(
                                    bloque,
                                    zona
                                );


                            if (
                                validacion.valido
                            ) {

                                zona.style.background =
                                    'rgba(16,185,129,.08)';


                                zona.style.outline =
                                    '2px dashed #10B981';


                                zona.style.outlineOffset =
                                    '-4px';


                                event.dataTransfer.dropEffect =
                                    'move';

                            } else {

                                zona.style.background =
                                    'rgba(219,8,8,.08)';


                                zona.style.outline =
                                    '2px dashed #DB0808';


                                zona.style.outlineOffset =
                                    '-4px';


                                event.dataTransfer.dropEffect =
                                    'none';
                            }
                        }
                    );


                    zona.addEventListener(
                        'dragleave',
                        () => {

                            limpiarEstadoDrag(
                                zona
                            );
                        }
                    );


                    zona.addEventListener(
                        'drop',
                        event => {

                            event.preventDefault();


                            limpiarEstadoDrag(
                                zona
                            );


                            const id =
                                event.dataTransfer.getData(
                                    'text/plain'
                                ) ||
                                bloqueArrastradoId;


                            const bloque =
                                cargarHorarios()
                                    .find(
                                        horario =>
                                            String(
                                                horario.id
                                            ) ===
                                            String(id)
                                    );


                            if (!bloque) {

                                mostrarMensaje(
                                    'No se encontró la clase arrastrada.',
                                    'error'
                                );

                                return;
                            }


                            // -------------------------------------
                            // MISMO GRUPO
                            // -------------------------------------

                            if (
                                zona.dataset.grupo !==
                                claveGrupoBloque(
                                    bloque
                                )
                            ) {

                                mostrarMensaje(
                                    'Solo puedes cambiar el día y la hora. No puedes cambiar de profesor, aula o grado arrastrando.',
                                    'error'
                                );

                                return;
                            }


                            const dia =
                                Number(
                                    zona.dataset.dia
                                );


                            const horaInicio =
                                zona.dataset.horaInicio;


                            const duracion =
                                duracionMinutos(
                                    bloque
                                );


                            const horaFin =
                                aTexto(
                                    aMinutos(
                                        horaInicio
                                    ) +
                                    duracion
                                );


                            // -------------------------------------
                            // VALIDACIÓN ANTES DE MOVER
                            // -------------------------------------

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

                                return;
                            }


                            moverClase(
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
    // EXPORT
    // =========================================================

    function limpiarNombreArchivo(
        texto
    ) {

        return String(texto)
            .normalize('NFD')
            .replace(
                /[\u0300-\u036f]/g,
                ''
            )
            .replace(
                /[^a-zA-Z0-9_-]+/g,
                '_'
            )
            .replace(
                /_+/g,
                '_'
            )
            .replace(
                /^_|_$/g,
                ''
            )
            .toLowerCase();
    }


    function ordenarBloquesExportacion(
        bloques
    ) {

        return [
            ...bloques
        ]
            .sort(
                (a,b) => {

                    if (
                        Number(
                            a.dia_semana
                        ) !==
                        Number(
                            b.dia_semana
                        )
                    ) {

                        return (
                            Number(
                                a.dia_semana
                            ) -
                            Number(
                                b.dia_semana
                            )
                        );
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
    }


    function exportarExcel(
        bloques
    ) {

        if (!bloques.length) {

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
                'No se cargó Excel.',
                'error'
            );

            return;
        }


        const filas = [[

            'Institución',
            'Día',
            'Hora inicio',
            'Hora fin',
            'Duración',
            'Profesor',
            'Curso',
            'Grado',
            'Aula'

        ]];


        ordenarBloquesExportacion(
            bloques
        )
            .forEach(
                bloque => {

                    const datos =
                        datosClase(
                            bloque
                        );


                    filas.push([

                        textoInstitucion(),

                        DIAS_NOMBRES[
                            Number(
                                bloque.dia_semana
                            )
                        ],

                        bloque.hora_inicio,

                        bloque.hora_fin,

                        textoDuracion(
                            bloque
                        ),

                        datos.profesor,

                        datos.curso,

                        datos.grado,

                        datos.aula

                    ]);
                }
            );


        const hoja =
            XLSX.utils.aoa_to_sheet(
                filas
            );


        const libro =
            XLSX.utils.book_new();


        XLSX.utils.book_append_sheet(
            libro,
            hoja,
            'Horarios'
        );


        XLSX.writeFile(
            libro,
            `horarios_${institucionActiva}.xlsx`
        );


        mostrarMensaje(
            'Excel descargado correctamente.'
        );
    }


    function exportarPdf(
        bloques,
        nombre
    ) {

        if (!bloques.length) {

            mostrarMensaje(
                'No hay datos para exportar.',
                'info'
            );

            return;
        }


        const ventana =
            window.open(
                '',
                '_blank'
            );


        if (!ventana) {

            mostrarMensaje(
                'Permite ventanas emergentes.',
                'error'
            );

            return;
        }


        const filas =
            ordenarBloquesExportacion(
                bloques
            )
                .map(
                    bloque => {

                        const datos =
                            datosClase(
                                bloque
                            );


                        return `
                            <tr>
                                <td>${esc(
                                    DIAS_NOMBRES[
                                        Number(
                                            bloque.dia_semana
                                        )
                                    ]
                                )}</td>

                                <td>
                                    ${esc(
                                        bloque.hora_inicio
                                    )}
                                    -
                                    ${esc(
                                        bloque.hora_fin
                                    )}
                                </td>

                                <td>${esc(
                                    datos.profesor
                                )}</td>

                                <td>${esc(
                                    datos.curso
                                )}</td>

                                <td>${esc(
                                    datos.grado
                                )}</td>

                                <td>${esc(
                                    datos.aula
                                )}</td>
                            </tr>
                        `;
                    }
                )
                .join('');


        ventana.document.write(`
            <!DOCTYPE html>
            <html>
            <head>

                <title>${esc(
                    nombre
                )}</title>

                <style>

                    body{
                        font-family:Arial;
                        padding:30px;
                    }

                    h1{
                        color:#0F2749;
                    }

                    table{
                        width:100%;
                        border-collapse:collapse;
                    }

                    th{
                        background:#0F2749;
                        color:white;
                    }

                    th,
                    td{
                        border:1px solid #ddd;
                        padding:8px;
                        font-size:11px;
                    }

                </style>

            </head>

            <body>

                <h1>
                    ${esc(
                        tituloEl.textContent
                    )}
                </h1>

                <p>
                    ${esc(
                        textoInstitucion()
                    )}
                </p>

                <table>

                    <thead>

                        <tr>

                            <th>Día</th>
                            <th>Horario</th>
                            <th>Profesor</th>
                            <th>Curso</th>
                            <th>Grado</th>
                            <th>Aula</th>

                        </tr>

                    </thead>

                    <tbody>
                        ${filas}
                    </tbody>

                </table>

                <script>
                    setTimeout(
                        () => window.print(),
                        300
                    );
                <\/script>

            </body>
            </html>
        `);


        ventana.document.close();
    }


    function exportarImagen(
        bloques,
        nombre
    ) {

        if (!bloques.length) {

            mostrarMensaje(
                'No hay datos para exportar.',
                'info'
            );

            return;
        }


        mostrarMensaje(
            'Usa el botón PDF para exportar el horario completo.',
            'info'
        );
    }


    // =========================================================
    // BLOQUES VISIBLES
    // =========================================================

    function obtenerBloquesVisibles() {

        const institucionales =
            cargarHorarios()
                .filter(
                    horario =>
                        horarioEsDeInstitucion(
                            horario
                        )
                );


        return aplicarFiltros(
            institucionales
        );
    }


    // =========================================================
    // CLICK CONTENEDOR
    // =========================================================

    contenedor.addEventListener(
        'click',
        event => {

            const botonEliminar =
                event.target.closest(
                    '.btn-eliminar-horario'
                );


            if (botonEliminar) {

                event.preventDefault();

                event.stopPropagation();


                abrirModalEliminarHorario(
                    botonEliminar.dataset
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


            const grupo =
                gruposVisibles.find(
                    grupo =>
                        grupo.clave ===
                        botonExportar.dataset
                            .exportarGrupo
                );


            if (!grupo) {
                return;
            }


            const nombre =
                `horario_${grupo.nombre}_${textoInstitucion()}`;


            if (
                botonExportar.dataset
                    .formato ===
                'pdf'
            ) {

                exportarPdf(
                    grupo.bloques,
                    nombre
                );

            } else {

                exportarImagen(
                    grupo.bloques,
                    nombre
                );
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
    // TÍTULOS
    // =========================================================

    function actualizarTituloVista() {

        const configuracion = {

            profesor:[
                'Horario por profesor',
                'Consulta las clases asignadas a cada profesor.'
            ],

            aula:[
                'Horario por aula',
                'Consulta las clases programadas en cada aula.'
            ],

            'aula-completa':[
                'Horario general por aulas',
                'Vista completa por días y bloques horarios.'
            ],

            grado:[
                'Horario por grado',
                'Consulta las clases correspondientes a cada grado o sección.'
            ]
        };


        const actual =
            configuracion[
                vistaActual
            ];


        tituloEl.textContent =
            actual[0];


        subtituloEl.textContent =
            actual[1];
    }


    // =========================================================
    // RENDER
    // =========================================================

    function render() {

        const todos =
            cargarHorarios();


        const institucionales =
            todos.filter(
                horario =>
                    horarioEsDeInstitucion(
                        horario
                    )
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


        if (!filtrados.length) {

            gruposVisibles =
                [];


            contenedor.innerHTML = `
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

                    <h3 class="font-bold text-slate-800">
                        Sin horarios
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        No existen clases que coincidan con los filtros seleccionados.
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
                    grupo =>
                        renderTablaGrupo(
                            grupo
                        )
                )
                .join('');


        activarDragDrop();
    }


    // =========================================================
    // INSTITUCIÓN
    // =========================================================

    tabsInstitucion.forEach(
        tab => {

            tab.addEventListener(
                'click',
                () => {

                    institucionActiva =
                        tab.dataset
                            .institucion;


                    tabsInstitucion.forEach(
                        boton => {

                            if (
                                boton === tab
                            ) {

                                boton.style.background =
                                    'linear-gradient(135deg,#1B3A6B,#0F2749)';

                                boton.style.color =
                                    '#FFFFFF';

                            } else {

                                boton.style.background =
                                    '#FFFFFF';

                                boton.style.color =
                                    '#475569';
                            }
                        }
                    );


                    limpiarFiltros();

                    cerrarTodosFiltros();


                    render();
                }
            );
        }
    );


    // =========================================================
    // VISTAS
    // =========================================================

    vistaBotones.forEach(
        boton => {

            boton.addEventListener(
                'click',
                () => {

                    vistaActual =
                        boton.dataset
                            .vista;


                    vistaBotones.forEach(
                        item => {

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


                    if (chkCompleto) {

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
            );
        }
    );


    // =========================================================
    // FILTROS
    // =========================================================

    [
        filtroProfesor,
        filtroAula,
        filtroGrado,
        filtroCurso
    ]
        .filter(Boolean)
        .forEach(
            filtro => {

                filtro.addEventListener(
                    'change',
                    () => {

                        actualizarCustomFiltro(
                            filtro
                        );


                        render();
                    }
                );
            }
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
    // EXPORT
    // =========================================================

    btnPdf
        ?.addEventListener(
            'click',
            () => {

                exportarPdf(

                    obtenerBloquesVisibles(),

                    `horarios_${institucionActiva}`
                );
            }
        );


    btnExcel
        ?.addEventListener(
            'click',
            () => {

                exportarExcel(
                    obtenerBloquesVisibles()
                );
            }
        );


    // =========================================================
    // ACTUALIZACIONES
    // =========================================================

    window.addEventListener(
        'storage',
        event => {

            if (
                Object.values(
                    KEYS
                ).includes(
                    event.key
                )
            ) {

                render();
            }
        }
    );


    window.addEventListener(
        'focus',
        () => {

            render();
        }
    );


    // =========================================================
    // INICIO
    // =========================================================

    actualizarTodosCustomFiltros();

    render();

});
