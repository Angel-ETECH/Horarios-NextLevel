document.addEventListener('DOMContentLoaded', () => {

    // =============================================================
    // STORAGE
    // =============================================================

    const KEYS = {
        profesores: 'nextlevel_profesores',
        horarios: 'nextlevel_horarios',
        cursos: 'nextlevel_cursos',
        aulas: 'nextlevel_aulas',
        grados: 'nextlevel_grados'
    };


    // =============================================================
    // ELEMENTOS
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

    const customMenu =
        document.getElementById('custom-select-menu');

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
    // GUARD
    // =============================================================

    if (
        !selector ||
        !btnConsultar ||
        !horarioRender
    ) {
        return;
    }


    // =============================================================
    // ESTADO
    // =============================================================

    let institucionSeleccionada = '';

    let tipoSeleccionado = '';

    let diaMovilActual = 0;

    let registroSeleccionadoActual = null;

    let horariosSeleccionadosActuales = [];

    let opcionesSelectorActuales = [];


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
    // STORAGE
    // =============================================================

    function leerStorage(clave) {

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
                `Error leyendo ${clave}:`,
                error
            );


            return [];
        }
    }


    function obtenerProfesores() {
        return leerStorage(KEYS.profesores);
    }


    function obtenerHorarios() {
        return leerStorage(KEYS.horarios);
    }


    function obtenerCursos() {
        return leerStorage(KEYS.cursos);
    }


    function obtenerAulas() {
        return leerStorage(KEYS.aulas);
    }


    function obtenerGrados() {
        return leerStorage(KEYS.grados);
    }


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


    function buscarPorId(lista, id) {

        return lista.find(
            item =>
                String(item.id) ===
                String(id)
        ) || null;
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


    // =============================================================
    // INSTITUCIÓN
    // =============================================================

    function obtenerInstitucion(objeto) {

        if (!objeto) {
            return '';
        }


        const valor =
            objeto.institucion ??
            objeto.tipo_institucion ??
            objeto.institucion_nombre ??
            objeto.tipoInstitucion ??
            '';


        return normalizarTexto(valor);
    }


    function perteneceInstitucion(
        objeto,
        institucion
    ) {

        const encontrada =
            obtenerInstitucion(objeto);


        /*
         * Compatibilidad temporal con registros antiguos.
         */
        if (!encontrada) {

            /*
             * Para profesores sí podemos mirar instituciones.
             */
            if (
                Array.isArray(
                    objeto?.instituciones
                ) &&
                objeto.instituciones.length
            ) {

                return objeto.instituciones
                    .map(normalizarTexto)
                    .includes(
                        normalizarTexto(
                            institucion
                        )
                    );
            }


            return true;
        }


        return encontrada ===
            normalizarTexto(
                institucion
            );
    }


    // =============================================================
    // DÍA
    // =============================================================

    function normalizarDia(dia) {

        const valor =
            normalizarTexto(dia);


        const mapa = {

            '1': 'Lunes',
            '2': 'Martes',
            '3': 'Miércoles',
            '4': 'Jueves',
            '5': 'Viernes',
            '6': 'Sábado',

            lunes: 'Lunes',
            martes: 'Martes',
            miercoles: 'Miércoles',
            jueves: 'Jueves',
            viernes: 'Viernes',
            sabado: 'Sábado'
        };


        return mapa[valor] || '';
    }


    function aMinutos(hora) {

        if (!hora) {
            return 0;
        }


        const partes =
            String(hora)
                .split(':');


        return (
            (Number(partes[0]) || 0) *
            60
        ) +
        (
            Number(partes[1]) || 0
        );
    }


    // =============================================================
    // NOMBRES
    // =============================================================

    function nombreProfesor(profesor) {

        if (!profesor) {
            return 'Profesor';
        }


        const completo =
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
                .trim();


        return completo ||
            profesor.nombre_completo ||
            profesor.nombreCompleto ||
            `Profesor ${profesor.id}`;
    }


    function nombreCurso(curso) {

        if (!curso) {
            return 'Curso';
        }


        return curso.nombre ||
            curso.nombre_curso ||
            curso.descripcion ||
            `Curso ${curso.id}`;
    }


    function nombreAula(aula) {

        if (!aula) {
            return 'Aula';
        }


        return aula.nombre ||
            aula.nombre_aula ||
            aula.codigo ||
            `Aula ${aula.id}`;
    }


    // =============================================================
    // NIVEL DE GRADO
    // =============================================================

    function obtenerNivelGrado(grado) {

        if (!grado) {
            return '';
        }


        const valor =
            grado.nivel ??
            grado.nivel_educativo ??
            grado.tipo_nivel ??
            grado.nivelEducativo ??
            '';


        const normalizado =
            normalizarTexto(valor);


        if (
            normalizado.includes(
                'primaria'
            )
        ) {
            return 'Primaria';
        }


        if (
            normalizado.includes(
                'secundaria'
            )
        ) {
            return 'Secundaria';
        }


        return '';
    }


    function nombreBaseGrado(grado) {

        if (!grado) {
            return 'Grado';
        }


        const nombre =
            grado.nombre ||
            grado.nombre_grado ||
            grado.grado ||
            '';


        const seccion =
            grado.seccion ||
            '';


        if (
            nombre &&
            seccion &&
            !normalizarTexto(nombre)
                .includes(
                    normalizarTexto(
                        seccion
                    )
                )
        ) {

            return `${nombre} ${seccion}`;
        }


        return nombre ||
            `Grado ${grado.id}`;
    }


    function nombreGrado(grado) {

        const base =
            nombreBaseGrado(
                grado
            );


        const nivel =
            obtenerNivelGrado(
                grado
            );


        return nivel
            ? `${base} · ${nivel}`
            : base;
    }


    // =============================================================
    // NOMBRE SEGÚN TIPO
    // =============================================================

    function obtenerNombreRegistro(
        tipo,
        registro
    ) {

        if (
            tipo ===
            'profesor'
        ) {

            return nombreProfesor(
                registro
            );
        }


        if (
            tipo ===
            'grado'
        ) {

            return nombreGrado(
                registro
            );
        }


        if (
            tipo ===
            'aula'
        ) {

            return nombreAula(
                registro
            );
        }


        if (
            tipo ===
            'curso'
        ) {

            return nombreCurso(
                registro
            );
        }


        return '';
    }


    // =============================================================
    // LISTAS
    // =============================================================

    function obtenerListaTipo(tipo) {

        if (
            tipo ===
            'profesor'
        ) {
            return obtenerProfesores();
        }


        if (
            tipo ===
            'grado'
        ) {
            return obtenerGrados();
        }


        if (
            tipo ===
            'aula'
        ) {
            return obtenerAulas();
        }


        if (
            tipo ===
            'curso'
        ) {
            return obtenerCursos();
        }


        return [];
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
    // BOTONES INSTITUCIÓN
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
    // DISPONIBILIDAD DE TIPO
    // =============================================================

    function actualizarDisponibilidadTipos() {

        if (!botonTipoGrado) {
            return;
        }


        if (
            institucionSeleccionada ===
            'academia'
        ) {

            botonTipoGrado.classList.add(
                'hidden'
            );


            if (
                tipoSeleccionado ===
                'grado'
            ) {

                tipoSeleccionado =
                    '';
            }

        } else {

            botonTipoGrado.classList.remove(
                'hidden'
            );
        }
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
    // LIMPIAR RESULTADO
    // =============================================================

    function limpiarResultado() {

        mensaje.classList.add(
            'hidden'
        );


        consultaInfo.classList.add(
            'hidden'
        );


        horarioContainer.classList.add(
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
            customButton.disabled
        ) {
            return;
        }


        customSelect.classList.add(
            'open'
        );


        customButton.setAttribute(
            'aria-expanded',
            'true'
        );


        customSearch.value =
            '';


        renderOpcionesCustom();


        setTimeout(
            () =>
                customSearch.focus(),
            30
        );
    }


    function actualizarCustomSelectVisual() {

        customButton.disabled =
            selector.disabled;


        customSmall.textContent =
            selectorLabel.textContent ||
            'Seleccionar';


        customIcon.innerHTML =
            iconoTipo(
                tipoSeleccionado ||
                'curso'
            );


        const opcion =
            selector.options[
                selector.selectedIndex
            ];


        customText.textContent =
            opcion?.textContent ||
            'Seleccionar';
    }


    function renderOpcionesCustom(
        busqueda = ''
    ) {

        const query =
            normalizarTexto(
                busqueda
            );


        if (
            !opcionesSelectorActuales.length
        ) {

            customOptions.innerHTML = `
                <div class="custom-select-empty">
                    No hay opciones disponibles
                </div>
            `;


            return;
        }


        const filtradas =
            opcionesSelectorActuales.filter(
                item =>
                    normalizarTexto(
                        item.texto
                    ).includes(
                        query
                    ) ||
                    normalizarTexto(
                        item.nivel
                    ).includes(
                        query
                    )
            );


        if (!filtradas.length) {

            customOptions.innerHTML = `
                <div class="custom-select-empty">
                    No se encontraron resultados
                </div>
            `;


            return;
        }


        let html =
            '';


        let ultimoNivel =
            null;


        filtradas.forEach(
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


                        <span class="custom-option-text">

                            ${escaparHTML(
                                item.texto
                            )}

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


    customButton?.addEventListener(
        'click',
        () => {

            if (
                customSelect.classList.contains(
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

            renderOpcionesCustom(
                customSearch.value
            );
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
    // ACTUALIZAR SELECTOR
    // =============================================================

    function actualizarSelector() {

        limpiarResultado();


        selector.innerHTML =
            '';


        selector.disabled =
            true;


        opcionesSelectorActuales =
            [];


        if (
            !institucionSeleccionada
        ) {

            selectorLabel.textContent =
                'Seleccionar';


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

            selectorLabel.textContent =
                'Seleccionar';


            selector.innerHTML = `
                <option value="">
                    Selecciona el tipo de consulta
                </option>
            `;


            actualizarCustomSelectVisual();


            return;
        }


        if (
            institucionSeleccionada ===
            'academia' &&
            tipoSeleccionado ===
            'grado'
        ) {

            tipoSeleccionado =
                '';


            actualizarBotonesTipo();


            actualizarSelector();


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


        selectorLabel.textContent =
            etiquetas[
                tipoSeleccionado
            ];


        let lista =
            obtenerListaTipo(
                tipoSeleccionado
            );


        /*
         * Filtrado por institución.
         */
        lista =
            lista.filter(
                item =>
                    perteneceInstitucion(
                        item,
                        institucionSeleccionada
                    )
            );


        /*
         * En Colegio se muestran Primaria y Secundaria.
         */
        if (
            tipoSeleccionado ===
            'grado'
        ) {

            lista =
                lista.filter(
                    grado => {

                        const nivel =
                            obtenerNivelGrado(
                                grado
                            );


                        return (
                            nivel ===
                            'Primaria' ||
                            nivel ===
                            'Secundaria' ||
                            nivel ===
                            ''
                        );
                    }
                );
        }


        lista.sort(
            (a,b) => {

                const nivelA =
                    obtenerNivelGrado(
                        a
                    );


                const nivelB =
                    obtenerNivelGrado(
                        b
                    );


                if (
                    tipoSeleccionado ===
                    'grado' &&
                    nivelA !==
                    nivelB
                ) {

                    const orden = {
                        Primaria: 1,
                        Secundaria: 2,
                        '': 3
                    };


                    return (
                        (orden[nivelA] || 9) -
                        (orden[nivelB] || 9)
                    );
                }


                return obtenerNombreRegistro(
                    tipoSeleccionado,
                    a
                )
                    .localeCompare(
                        obtenerNombreRegistro(
                            tipoSeleccionado,
                            b
                        ),
                        'es',
                        {
                            numeric: true
                        }
                    );
            }
        );


        selector.innerHTML = `
            <option value="">
                Seleccione ${
                    etiquetas[
                        tipoSeleccionado
                    ].toLowerCase()
                }
            </option>
        `;


        lista.forEach(
            registro => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    registro.id;


                option.textContent =
                    obtenerNombreRegistro(
                        tipoSeleccionado,
                        registro
                    );


                selector.appendChild(
                    option
                );


                opcionesSelectorActuales.push({

                    id:
                        String(
                            registro.id
                        ),

                    registro,

                    texto:
                        obtenerNombreRegistro(
                            tipoSeleccionado,
                            registro
                        ),

                    nivel:
                        tipoSeleccionado ===
                        'grado'

                            ? (
                                obtenerNivelGrado(
                                    registro
                                ) ||
                                'Sin nivel'
                            )

                            : ''
                });
            }
        );


        selector.disabled =
            false;


        actualizarCustomSelectVisual();


        renderOpcionesCustom();
    }


    // =============================================================
    // MENSAJE
    // =============================================================

    function mostrarMensaje(
        texto,
        tipo = 'error'
    ) {

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


    // =============================================================
    // HORARIOS POR INSTITUCIÓN
    // =============================================================

    function horariosPorInstitucion() {

        return obtenerHorarios()
            .filter(
                horario =>
                    perteneceInstitucion(
                        horario,
                        institucionSeleccionada
                    )
            );
    }


    // =============================================================
    // FILTRAR
    // =============================================================

    function filtrarHorarios(
        tipo,
        id
    ) {

        const horarios =
            horariosPorInstitucion();


        if (
            tipo ===
            'profesor'
        ) {

            return horarios.filter(
                horario =>
                    String(
                        horario.profesor_id
                    ) ===
                    String(id)
            );
        }


        if (
            tipo ===
            'grado'
        ) {

            return horarios.filter(
                horario =>
                    String(
                        horario.grado_id
                    ) ===
                    String(id)
            );
        }


        if (
            tipo ===
            'aula'
        ) {

            return horarios.filter(
                horario =>
                    String(
                        horario.aula_id
                    ) ===
                    String(id)
            );
        }


        if (
            tipo ===
            'curso'
        ) {

            return horarios.filter(
                horario =>
                    String(
                        horario.curso_id
                    ) ===
                    String(id)
            );
        }


        return [];
    }


    // =============================================================
    // INFORMACIÓN
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


        consultaTipoInfo.textContent =
            etiquetas[
                tipoSeleccionado
            ];


        consultaNombre.textContent =
            obtenerNombreRegistro(
                tipoSeleccionado,
                registro
            );


        consultaDetalle.textContent =
            `${cantidadClases} ${
                cantidadClases === 1
                    ? 'clase programada'
                    : 'clases programadas'
            }`;


        const institucionTexto =
            institucionSeleccionada ===
            'colegio'

                ? 'Colegio'
                : 'Academia';


        consultaInstitucion.textContent =
            institucionTexto;


        consultaInstitucion.className =

            institucionSeleccionada ===
            'colegio'

                ? 'inline-flex w-fit items-center rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-[#1B3A6B]'

                : 'inline-flex w-fit items-center rounded-full bg-red-50 px-3 py-1.5 text-xs font-bold text-[#DB0808]';


        /*
         * Descarga exclusivamente por profesor.
         */
        accionesDescarga?.classList.toggle(
            'visible',
            tipoSeleccionado ===
            'profesor' &&
            cantidadClases >
            0
        );


        consultaInfo.classList.remove(
            'hidden'
        );
    }


    // =============================================================
    // CONTENIDO CLASE
    // =============================================================

    function crearContenidoClase(
        horario
    ) {

        const profesores =
            obtenerProfesores();

        const cursos =
            obtenerCursos();

        const aulas =
            obtenerAulas();

        const grados =
            obtenerGrados();


        const profesor =
            buscarPorId(
                profesores,
                horario.profesor_id
            );


        const curso =
            buscarPorId(
                cursos,
                horario.curso_id
            );


        const aula =
            buscarPorId(
                aulas,
                horario.aula_id
            );


        const grado =
            buscarPorId(
                grados,
                horario.grado_id
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
                        institucionSeleccionada ===
                        'colegio'

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
                            horario.hora_inicio ||
                            ''
                        )}

                        -

                        ${escaparHTML(
                            horario.hora_fin ||
                            ''
                        )}

                    </p>

                </div>

            </article>
        `;
    }


    // =============================================================
    // RENDER HORARIO
    // =============================================================

    function renderizarHorario(
        horarios
    ) {

        horarioRender.innerHTML =
            '';


        const ordenados =
            [...horarios]
                .sort(
                    (a,b) => {

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
            (dia,index) => {

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


        diaMovilActual =
            0;


        actualizarDiaMovil();
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
            (columna,index) => {

                columna.classList.toggle(
                    'dia-activo',
                    index ===
                    diaMovilActual
                );
            }
        );


        if (horarioDiaActual) {

            horarioDiaActual.childNodes[0]
                .textContent =
                    DIAS[
                        diaMovilActual
                    ] +
                    ' ';
        }


        if (horarioDiaContador) {

            horarioDiaContador.textContent =
                `${diaMovilActual + 1} de ${DIAS.length}`;
        }


        if (btnDiaAnterior) {

            btnDiaAnterior.disabled =
                diaMovilActual ===
                0;
        }


        if (btnDiaSiguiente) {

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
    // CONSULTAR
    // =============================================================

    function consultarHorario() {

        mensaje.classList.add(
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


        const lista =
            obtenerListaTipo(
                tipoSeleccionado
            );


        const registro =
            buscarPorId(
                lista,
                id
            );


        if (!registro) {

            mostrarMensaje(
                'No se encontró el registro seleccionado.'
            );


            return;
        }


        const horarios =
            filtrarHorarios(
                tipoSeleccionado,
                id
            );


        registroSeleccionadoActual =
            registro;


        horariosSeleccionadosActuales =
            horarios;


        mostrarInformacion(
            registro,
            horarios.length
        );


        if (!horarios.length) {

            mostrarMensaje(
                'No existen clases programadas para esta consulta.',
                'info'
            );


            horarioContainer.classList.add(
                'hidden'
            );


            return;
        }


        renderizarHorario(
            horarios
        );
    }


    // =============================================================
    // DESCARGAR PDF
    // =============================================================

    btnDescargarPdf?.addEventListener(
        'click',
        () => {

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


            /*
             * Por ahora usa el diálogo de impresión del navegador.
             * Desde ahí se puede elegir "Guardar como PDF".
             *
             * Cuando el backend entregue el endpoint PDF,
             * este evento se cambia por una llamada a esa ruta.
             */

            window.print();
        }
    );


    // =============================================================
    // DESCARGAR IMAGEN
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


            const nombre =
                nombreProfesor(
                    registroSeleccionadoActual
                );


            const textoOriginal =
                btnDescargarImagen.innerHTML;


            btnDescargarImagen.disabled =
                true;


            btnDescargarImagen.innerHTML = `
                Generando imagen...
            `;


            try {

                /*
                 * Hacemos visibles todos los días
                 * aunque estemos en móvil.
                 */
                horarioContainer.classList.add(
                    'exportando-horario'
                );


                const canvas =
                    await html2canvas(
                        horarioContainer,
                        {
                            scale:
                                2,

                            backgroundColor:
                                '#FFFFFF',

                            useCORS:
                                true,

                            logging:
                                false
                        }
                    );


                const enlace =
                    document.createElement(
                        'a'
                    );


                enlace.download =
                    `Horario_${limpiarNombreArchivo(
                        nombre
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

            } catch (error) {

                console.error(
                    'Error generando imagen:',
                    error
                );


                mostrarMensaje(
                    'No se pudo generar la imagen del horario.'
                );

            } finally {

                horarioContainer.classList.remove(
                    'exportando-horario'
                );


                btnDescargarImagen.disabled =
                    false;


                btnDescargarImagen.innerHTML =
                    textoOriginal;


                actualizarDiaMovil();
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
                () => {

                    institucionSeleccionada =
                        boton.dataset.institucion;


                    actualizarBotonesInstitucion();


                    actualizarDisponibilidadTipos();


                    actualizarBotonesTipo();


                    actualizarSelector();
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
                () => {

                    if (
                        institucionSeleccionada ===
                        'academia' &&
                        boton.dataset.tipo ===
                        'grado'
                    ) {

                        return;
                    }


                    tipoSeleccionado =
                        boton.dataset.tipo;


                    actualizarBotonesTipo();


                    actualizarSelector();
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
    // CONSULTAR
    // =============================================================

    btnConsultar.addEventListener(
        'click',
        consultarHorario
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
                    customOptions.querySelector(
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


    // =============================================================
    // SWIPE MÓVIL
    // =============================================================

    let touchInicioX =
        null;


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