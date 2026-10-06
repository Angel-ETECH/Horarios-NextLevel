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

    const disponibilidadResumen =
        document.getElementById(
            'asignacion-disponibilidad-resumen'
        );

    const recesosResumen =
        document.getElementById(
            'asignacion-recesos-resumen'
        );


    if (
        !profesorSelect ||
        !institucionSelect ||
        !cursoSelect ||
        !gradoSelect ||
        !aulaSelect ||
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
    let aulas = [];
    let asignaciones = [];
    let disponibilidadesProfesor = {};

    let asignacionEditandoId = null;
    let cargando = false;
    let horasDisponiblesActuales = null;
    let configuracionRecesosLista = false;


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


    function nombreAula(aula) {

        if (!aula) {
            return 'Aula no definida';
        }


        const nombre =
            aula.nombre ||
            aula.nombre_aula ||
            aula.codigo ||
            `Aula ${aula.id}`;


        return nombre;
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
    // DISPONIBILIDAD DEL PROFESOR
    // =========================================================

    function horaCorta(valor) {

        return String(
            valor ?? ''
        ).substring(
            0,
            5
        );
    }


    function minutosHora(valor) {

        const [
            horas,
            minutos
        ] =
            horaCorta(
                valor
            )
                .split(':')
                .map(Number);


        if (
            Number.isNaN(horas) ||
            Number.isNaN(minutos)
        ) {
            return 0;
        }


        return (
            horas * 60 +
            minutos
        );
    }


    function nombreDiaDisponible(valor) {

        const dia =
            normalizarTexto(
                valor
            );


        const nombres = {
            lunes: 'Lun',
            martes: 'Mar',
            miercoles: 'Mié',
            jueves: 'Jue',
            viernes: 'Vie',
            sabado: 'Sáb',
            domingo: 'Dom'
        };


        return nombres[dia] || valor || 'Día';
    }


    function formatearHorasDisponibles(horas) {

        if (
            horas === null ||
            horas === undefined
        ) {
            return '0';
        }


        return Number.isInteger(
            horas
        )
            ? String(horas)
            : horas.toFixed(
                1
            );
    }

    function obtenerGradoSeleccionado() {
        return grados.find(
            grado =>
                String(
                    grado.id
                ) ===
                String(
                    gradoSelect.value
                )
        ) || null;
    }

    function turnoConfigurable(valor) {
        return [
            'mañana',
            'tarde',
            'completo'
        ].includes(
            valor
        )
            ? valor
            : 'completo';
    }

    function pintarResumenRecesos(
        texto,
        estado = 'normal'
    ) {
        if (!recesosResumen) {
            return;
        }

        recesosResumen.classList.remove(
            'border-red-200',
            'bg-red-50',
            'text-red-700',
            'border-emerald-200',
            'bg-emerald-50',
            'text-emerald-800',
            'border-slate-200',
            'bg-slate-50',
            'text-slate-600'
        );

        if (estado === 'ok') {
            recesosResumen.classList.add(
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-800'
            );
        } else if (estado === 'error') {
            recesosResumen.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );
        } else {
            recesosResumen.classList.add(
                'border-slate-200',
                'bg-slate-50',
                'text-slate-600'
            );
        }

        recesosResumen.textContent =
            texto;
    }

    async function actualizarResumenRecesos() {
        configuracionRecesosLista =
            false;

        const institucion =
            institucionSelect.value;

        const grado =
            obtenerGradoSeleccionado();

        if (
            !institucion ||
            !grado
        ) {
            pintarResumenRecesos(
                'Selecciona grado e institución para validar recesos.'
            );

            return;
        }

        pintarResumenRecesos(
            'Validando recesos configurados...'
        );

        try {
            const response =
                await window.axios.get(
                    '/api/configuraciones-horario/recesos',
                    {
                        params: {
                            institucion,
                            nivel:
                                grado.nivel,
                            turno:
                                turnoConfigurable(
                                    grado.turno
                                )
                        }
                    }
                );

            const recesos =
                response?.data?.data ??
                [];

            const mañana =
                recesos.filter(
                    receso =>
                        minutosHora(
                            receso.hora_inicio
                        ) < 12 * 60
                ).length;

            const tarde =
                recesos.filter(
                    receso =>
                        minutosHora(
                            receso.hora_inicio
                        ) >= 12 * 60
                ).length;

            const turno =
                turnoConfigurable(
                    grado.turno
                );

            const esValido =
                turno === 'mañana'
                    ? mañana >= 2
                    : turno === 'tarde'
                        ? tarde >= 2
                        : mañana >= 2 && tarde >= 2;

            configuracionRecesosLista =
                esValido;

            if (esValido) {
                pintarResumenRecesos(
                    `Recesos listos: ${recesos.map(
                        receso =>
                            `${horaCorta(receso.hora_inicio)}-${horaCorta(receso.hora_fin)}`
                    ).join(' · ')}`,
                    'ok'
                );

                return;
            }

            pintarResumenRecesos(
                'Faltan recesos configurados para este grado/turno. Guárdalos primero en Horarios.',
                'error'
            );
        } catch (error) {
            console.error(
                'Error validando recesos:',
                error
            );

            pintarResumenRecesos(
                'No se pudo validar la configuración de recesos.',
                'error'
            );
        }
    }


    async function obtenerDisponibilidadProfesor(profesorId) {

        if (!profesorId) {
            return [];
        }


        if (
            disponibilidadesProfesor[
                profesorId
            ]
        ) {
            return disponibilidadesProfesor[
                profesorId
            ];
        }


        const response =
            await window.axios.get(
                `/api/disponibilidades/profesor/${profesorId}`
            );


        const registros =
            extraerColeccion(
                response
            );


        disponibilidadesProfesor[
            profesorId
        ] =
            registros;


        return registros;
    }


    function resumirDisponibilidad(
        registros,
        institucion
    ) {

        const filtrados =
            registros.filter(
                item =>
                    normalizarTexto(
                        item.institucion ?? 'colegio'
                    ) ===
                        normalizarTexto(
                            institucion
                        ) &&
                    normalizarTexto(
                        item.tipo ?? 'disponible'
                    ) === 'disponible'
            );


        let minutosTotales =
            0;


        const rangos =
            filtrados.map(
                item => {

                    const inicio =
                        horaCorta(
                            item.hora_inicio
                        );


                    const fin =
                        horaCorta(
                            item.hora_fin
                        );


                    minutosTotales +=
                        Math.max(
                            0,
                            minutosHora(fin) -
                                minutosHora(inicio)
                        );


                    return `${nombreDiaDisponible(
                        item.dia_semana
                    )} ${inicio}-${fin}`;
                }
            );


        return {
            horas:
                minutosTotales /
                60,

            horasEnteras:
                Math.floor(
                    minutosTotales /
                    60
                ),

            rangos
        };
    }


    function pintarResumenDisponibilidad(
        resumen,
        estado = 'normal'
    ) {

        if (!disponibilidadResumen) {
            return;
        }


        disponibilidadResumen.classList.remove(
            'border-red-200',
            'bg-red-50',
            'text-red-700',
            'border-emerald-200',
            'bg-emerald-50',
            'text-emerald-800',
            'border-slate-200',
            'bg-slate-50',
            'text-slate-600'
        );


        if (estado === 'error') {

            disponibilidadResumen.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );

            disponibilidadResumen.textContent =
                'No se pudo cargar la disponibilidad del profesor.';

            return;
        }


        if (
            !resumen ||
            resumen.horas <= 0
        ) {

            disponibilidadResumen.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );

            disponibilidadResumen.textContent =
                'Este profesor no tiene disponibilidad guardada para esta institución.';

            return;
        }


        disponibilidadResumen.classList.add(
            'border-emerald-200',
            'bg-emerald-50',
            'text-emerald-800'
        );


        disponibilidadResumen.textContent =
            `${formatearHorasDisponibles(
                resumen.horas
            )} hora(s) disponibles: ${
                resumen.rangos.join(' · ')
            }`;
    }


    async function actualizarResumenDisponibilidad() {

        const profesorId =
            profesorSelect.value;


        const institucion =
            institucionSelect.value;


        horasDisponiblesActuales =
            null;


        if (
            !profesorId ||
            !institucion
        ) {

            pintarResumenDisponibilidad(
                null
            );

            return;
        }


        if (disponibilidadResumen) {

            disponibilidadResumen.className =
                'mt-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-xs leading-5 text-slate-600';

            disponibilidadResumen.textContent =
                'Consultando disponibilidad guardada...';
        }


        try {

            const registros =
                await obtenerDisponibilidadProfesor(
                    profesorId
                );


            const resumen =
                resumirDisponibilidad(
                    registros,
                    institucion
                );


            horasDisponiblesActuales =
                resumen.horasEnteras;


            pintarResumenDisponibilidad(
                resumen
            );


            if (
                resumen.horasEnteras > 0
            ) {

                horasInput.max =
                    String(
                        Math.min(
                            40,
                            resumen.horasEnteras
                        )
                    );


                if (
                    !asignacionEditandoId &&
                    !horasInput.value
                ) {

                    horasInput.value =
                        String(
                            Math.min(
                                40,
                                resumen.horasEnteras
                            )
                        );
                }
            }

        } catch (error) {

            console.error(
                'Error cargando disponibilidad del profesor:',
                error
            );

            pintarResumenDisponibilidad(
                null,
                'error'
            );
        }
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
                    <path d="M4 21V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v16"/>
                    <path d="M9 21v-6h6v6"/>
                    <path d="M8 7h.01M12 7h.01M16 7h.01"/>
                    <path d="M8 11h.01M12 11h.01M16 11h.01"/>
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
        mensaje,
        tipo = null
    ) {

        if (!estadoAsignacion) {
            return;
        }


        estadoAsignacion.className =
            'border-t border-slate-200 px-5 py-4';


        const estilo =
            tipo === 'warning'
                ? `
                    border-amber-200
                    bg-amber-50
                    text-amber-800
                `
                : (
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
                );


        estadoAsignacion.innerHTML = `

            <div
                class="
                    rounded-xl
                    border
                    px-4 py-3
                    text-sm font-medium
                    ${estilo}
                "
            >
                ${escaparHTML(mensaje)}
            </div>
        `;
    }


    function obtenerAvisoGeneracion(response) {

        const generacion =
            response?.data?.generacion_horarios;


        if (
            !generacion ||
            generacion.success === false
        ) {
            return null;
        }


        const incompletas =
            Array.isArray(
                generacion.asignaciones_incompletas
            )
                ? generacion.asignaciones_incompletas
                : [];


        if (incompletas.length === 0) {
            return null;
        }


        const actual =
            incompletas.find(
                item =>
                    String(item.asignacion_id) ===
                    String(
                        response?.data?.data?.id
                    )
            ) ||
            incompletas[0];


        const pendientes =
            Number(
                actual?.horas_pendientes
            ) || 0;


        const motivo =
            actual?.motivo ||
            'No se encontraron bloques disponibles.';


        return pendientes > 0
            ? `Asignación guardada, pero faltan ${pendientes} hora(s) por programar. ${motivo} Revisa la disponibilidad del profesor, conflictos de aula/grado o aumenta su rango horario.`
            : null;
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
    // AULAS
    // =========================================================

    function cargarSelectAulas() {

        const valorAnterior =
            aulaSelect.value;


        aulaSelect.innerHTML = `
            <option value="">
                Seleccionar aula
            </option>
        `;


        const filtradas =
            aulas
                .filter(aula => {

                    if (
                        aula.activo === false ||
                        Number(aula.activo) === 0
                    ) {
                        return false;
                    }


                    return true;
                })
                .sort(
                    (a, b) =>
                        nombreAula(a)
                            .localeCompare(
                                nombreAula(b),
                                'es',
                                {
                                    numeric: true
                                }
                            )
                );


        filtradas.forEach(aula => {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                aula.id;


            option.textContent =
                nombreAula(aula);


            aulaSelect.appendChild(
                option
            );
        });


        aulaSelect.disabled =
            filtradas.length === 0;


        const existeAnterior =
            Array.from(
                aulaSelect.options
            ).some(
                option =>
                    String(option.value) ===
                    String(valorAnterior)
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
                gradosAPI,
                aulasAPI
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
                    ),

                    cargarTodasLasPaginas(
                        '/api/aulas'
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


            aulas =
                Array.isArray(aulasAPI)
                    ? aulasAPI
                    : [];


            cargarSelectProfesores();

            cargarSelectCursos();

            cargarSelectGrados();

            cargarSelectAulas();


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
                    colspan="9"
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
                        colspan="9"
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

                    const aula =
                        asignacion.aula;


                    const contenido =
                        normalizarTexto(
                            [
                                nombreProfesor(profesor),
                                nombreCurso(curso),
                                nombreGrado(grado),
                                nombreAula(aula),
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
                        colspan="9"
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

                const aula =
                    asignacion.aula;


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


                    <td
                        class="
                            px-5 py-4
                            text-sm
                            text-slate-600
                        "
                    >
                        ${escaparHTML(
                            nombreAula(aula)
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

                            <button
                                type="button"
                                class="
                                    eliminar-asignacion-definitiva
                                    inline-flex
                                    h-9 w-9
                                    items-center justify-center
                                    rounded-lg
                                    text-slate-400
                                    transition
                                    hover:bg-red-50
                                    hover:text-red-700
                                "
                                data-id="${asignacion.id}"
                                title="Eliminar definitivamente"
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
                                    <path d="M3 6h18"/>
                                    <path d="M8 6V4h8v2"/>
                                    <path d="M19 6l-1 14H6L5 6"/>
                                    <path d="M10 11v5"/>
                                    <path d="M14 11v5"/>
                                </svg>

                            </button>

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

            aula_id:
                Number(
                    aulaSelect.value
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


        if (!aulaSelect.value) {

            alert(
                'Selecciona un aula.'
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


        if (
            horasDisponiblesActuales !== null &&
            horasDisponiblesActuales <= 0
        ) {

            alert(
                'Este profesor no tiene disponibilidad guardada para la institución seleccionada.'
            );

            return false;
        }


        if (
            horasDisponiblesActuales !== null &&
            horas >
                horasDisponiblesActuales
        ) {

            alert(
                `Solo hay ${horasDisponiblesActuales} hora(s) disponibles para este profesor en la institución seleccionada.`
            );

            return false;
        }

        if (
            !configuracionRecesosLista
        ) {
            alert(
                'Antes de guardar, configura los recesos del grado en Horarios.'
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

        aulaSelect.innerHTML = `
            <option value="">
                Seleccionar aula
            </option>
        `;


        cursoSelect.disabled =
            true;


        gradoSelect.disabled =
            true;

        aulaSelect.disabled =
            true;


        horasInput.value =
            '';

        horasInput.max =
            '40';

        horasDisponiblesActuales =
            null;

        configuracionRecesosLista =
            false;

        pintarResumenDisponibilidad(
            null
        );

        pintarResumenRecesos(
            'Selecciona grado e institución para validar recesos.'
        );


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


            const avisoGeneracion =
                obtenerAvisoGeneracion(
                    response
                );


            mostrarEstado(
                !avisoGeneracion,
                avisoGeneracion || mensaje,
                avisoGeneracion
                    ? 'warning'
                    : null
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
                !avisoGeneracion,
                avisoGeneracion || mensaje,
                avisoGeneracion
                    ? 'warning'
                    : null
            );


            await cargarAsignaciones();


            alert(
                avisoGeneracion ||
                (
                    eraEdicion
                        ? 'Asignación actualizada correctamente.'
                        : 'Asignación creada correctamente.'
                )
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

        cargarSelectAulas();


        aulaSelect.value =
            asignacion.aula_id
                ? String(
                    asignacion.aula_id
                )
                : '';


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


        void actualizarResumenDisponibilidad();
        void actualizarResumenRecesos();


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

    async function eliminarAsignacionDefinitiva(
        id
    ) {

        const asignacion =
            asignaciones.find(
                item =>
                    String(item.id) ===
                    String(id)
            );

        const profesor =
            asignacion?.profesor?.nombre_completo ||
            asignacion?.profesor?.nombre ||
            'este profesor';

        const curso =
            asignacion?.curso?.nombre ||
            'este curso';

        const primeraConfirmacion =
            confirm(
                `¿Eliminar definitivamente la asignación de ${profesor} en ${curso}? También se quitarán sus clases del horario.`
            );

        if (!primeraConfirmacion) {
            return;
        }

        const segundaConfirmacion =
            confirm(
                'Esta acción no solo desactiva: borra la asignación de la lista. ¿Confirmas la eliminación definitiva?'
            );

        if (!segundaConfirmacion) {
            return;
        }

        try {

            const response =
                await window.axios.delete(
                    `/api/asignaciones/${id}/permanente`
                );

            alert(
                response?.data?.message ||
                'Asignación eliminada definitivamente.'
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
                'Error eliminando definitivamente la asignación:',
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

                return;
            }


            const botonEliminarDefinitivo =
                event.target.closest(
                    '.eliminar-asignacion-definitiva'
                );


            if (botonEliminarDefinitivo) {

                eliminarAsignacionDefinitiva(
                    botonEliminarDefinitivo.dataset.id
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


            aulaSelect.value =
                '';


            cargarSelectProfesores();

            cargarSelectCursos();

            cargarSelectGrados();

            cargarSelectAulas();


            horasInput.value =
                '';


            horasInput.max =
                '40';


            actualizarTodosCustomSelect();


            void actualizarResumenDisponibilidad();

            void actualizarResumenRecesos();
        }
    );


    cursoSelect.addEventListener(
        'change',
        () => {

            gradoSelect.value =
                '';

            aulaSelect.value =
                '';


            cargarSelectGrados();

            cargarSelectAulas();


            actualizarCustomSelect(
                cursoSelect
            );

            void actualizarResumenRecesos();
        }
    );


    profesorSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                profesorSelect
            );


            horasInput.value =
                '';


            horasInput.max =
                '40';


            void actualizarResumenDisponibilidad();
        }
    );


    gradoSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                gradoSelect
            );

            cargarSelectAulas();

            void actualizarResumenRecesos();
        }
    );


    aulaSelect.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                aulaSelect
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

        aulaSelect.disabled =
            true;


        actualizarContadorObservaciones();


        actualizarTodosCustomSelect();


        await cargarCatalogos();


        await cargarAsignaciones();
    }


    iniciar();

});
