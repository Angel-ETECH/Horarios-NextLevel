document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // GUARD
    // =========================================================

    const tabla =
        document.getElementById('profesores-body');

    const profesorModal =
        document.getElementById('profesor-modal');

    const profesorForm =
        document.getElementById('profesor-form');


    if (
        !tabla ||
        !profesorModal ||
        !profesorForm
    ) {
        return;
    }


    // =========================================================
    // STORAGE
    // =========================================================

    const PROFESORES_KEY =
        'nextlevel_profesores';


    // =========================================================
    // ELEMENTOS CREAR
    // =========================================================

    const openProfesorModal =
        document.getElementById('open-profesor-modal');

    const closeProfesorModal =
        document.getElementById('close-profesor-modal');

    const cancelProfesorModal =
        document.getElementById('cancel-profesor-modal');

    const profesorModalOverlay =
        document.getElementById('profesor-modal-overlay');


    const codigoInput =
        document.getElementById('codigo');

    const nombreInput =
        document.getElementById('nombre');

    const apellidoPaternoInput =
        document.getElementById('apellido_paterno');

    const apellidoMaternoInput =
        document.getElementById('apellido_materno');

    const dniInput =
        document.getElementById('dni');

    const emailInput =
        document.getElementById('email');

    const telefonoInput =
        document.getElementById('telefono');

    const sexoInput =
        document.getElementById('sexo');

    const fechaNacimientoInput =
        document.getElementById('fecha_nacimiento');

    const especialidadInput =
        document.getElementById('especialidad');

    const cargaHorariaInput =
        document.getElementById('carga_horaria_maxima');

    const estadoInput =
        document.getElementById('estado');

    const observacionesInput =
        document.getElementById('observaciones');


    // =========================================================
    // FILTROS
    // =========================================================

    const buscador =
        document.getElementById('buscar-profesor');

    const filtroEstado =
        document.getElementById('filtro-estado');


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    const totalProfesores =
        document.getElementById('total-profesores');

    const profesoresActivos =
        document.getElementById('profesores-activos');

    const profesoresInactivos =
        document.getElementById('profesores-inactivos');

    const resultadosProfesores =
        document.getElementById('resultados-profesores');

    const totalResultadosProfesores =
        document.getElementById('total-resultados-profesores');

    const paginacion =
        document.getElementById('profesores-paginacion');


    // =========================================================
    // EDITAR
    // =========================================================

    const editarModal =
        document.getElementById('editar-profesor-modal');

    const editarOverlay =
        document.getElementById('editar-modal-overlay');

    const closeEditar =
        document.getElementById('close-editar-modal');

    const cancelEditar =
        document.getElementById('cancel-editar-modal');

    const editarForm =
        document.getElementById('editar-profesor-form');


    const editId =
        document.getElementById('editar-id');

    const editCodigo =
        document.getElementById('editar-codigo');

    const editNombre =
        document.getElementById('editar-nombre');

    const editApellidoPaterno =
        document.getElementById('editar-apellido_paterno');

    const editApellidoMaterno =
        document.getElementById('editar-apellido_materno');

    const editDni =
        document.getElementById('editar-dni');

    const editEmail =
        document.getElementById('editar-email');

    const editTelefono =
        document.getElementById('editar-telefono');

    const editSexo =
        document.getElementById('editar-sexo');

    const editFechaNacimiento =
        document.getElementById('editar-fecha_nacimiento');

    const editEspecialidad =
        document.getElementById('editar-especialidad');

    const editCargaHoraria =
        document.getElementById('editar-carga_horaria_maxima');

    const editEstado =
        document.getElementById('editar-estado');

    const editObservaciones =
        document.getElementById('editar-observaciones');


    const editInstitucionColegio =
        document.getElementById('editar-institucion-colegio');

    const editInstitucionAcademia =
        document.getElementById('editar-institucion-academia');


    // =========================================================
    // ELIMINAR
    // =========================================================

    const eliminarModal =
        document.getElementById('eliminar-profesor-modal');

    const eliminarOverlay =
        document.getElementById('eliminar-profesor-modal-overlay');

    const closeEliminar =
        document.getElementById('close-eliminar-profesor-modal');

    const cancelEliminar =
        document.getElementById('cancel-eliminar-profesor-modal');

    const confirmEliminar =
        document.getElementById('confirm-eliminar-profesor');

    const eliminarNombre =
        document.getElementById('eliminar-profesor-nombre');


    // =========================================================
    // ESTADO
    // =========================================================

    let profesorEditandoId =
        null;

    let profesorAEliminarId =
        null;

    let paginaActual =
        1;


    const POR_PAGINA =
        6;


    // =========================================================
    // STORAGE
    // =========================================================

    function leerProfesores() {

        try {

            const datos =
                JSON.parse(
                    localStorage.getItem(
                        PROFESORES_KEY
                    ) || '[]'
                );


            return Array.isArray(
                datos
            )
                ? datos
                : [];

        } catch (error) {

            console.error(
                'Error leyendo profesores:',
                error
            );


            return [];
        }
    }


    function guardarProfesores(
        profesores
    ) {

        localStorage.setItem(
            PROFESORES_KEY,
            JSON.stringify(
                profesores
            )
        );
    }


    // =========================================================
    // HELPERS
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
    // ICONOS SELECT
    // =========================================================

    function iconoSelect(
        tipo
    ) {

        const iconos = {

            estado: `
                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 2.5 2.5L16 9"/>
                </svg>
            `,


            usuario: `
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


            especialidad: `
                <svg
                    width="17"
                    height="17"
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
            `
        };


        return (
            iconos[tipo] ||
            iconos.estado
        );
    }


    // =========================================================
    // CUSTOM SELECT
    // =========================================================

    function wrapperSelect(
        select
    ) {

        if (!select) {
            return null;
        }


        return document.querySelector(
            `[data-profesor-select="${select.id}"]`
        );
    }


    function construirCustomSelect(
        wrapper
    ) {

        if (
            !wrapper ||
            wrapper.dataset.ready === 'true'
        ) {
            return;
        }


        const select =
            document.getElementById(
                wrapper.dataset.profesorSelect
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
            'estado';


        wrapper.innerHTML = `

            <button
                type="button"
                class="profesor-select-trigger"
                aria-expanded="false"
            >

                <span class="profesor-select-icon">
                    ${iconoSelect(icono)}
                </span>


                <span class="profesor-select-content">

                    <span class="profesor-select-label">
                        ${esc(label)}
                    </span>


                    <span class="profesor-select-text">
                        ${esc(placeholder)}
                    </span>

                </span>


                <svg
                    class="profesor-select-arrow"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m6 9 6 6 6-6"/>
                </svg>

            </button>


            <div class="profesor-select-menu">

                <div class="profesor-select-options"></div>

            </div>
        `;


        wrapper.dataset.ready =
            'true';


        const trigger =
            wrapper.querySelector(
                '.profesor-select-trigger'
            );


        trigger.addEventListener(
            'click',
            () => {

                cerrarTodosSelects(
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

                    renderOpcionesSelect(
                        wrapper
                    );
                }
            }
        );


        actualizarCustomSelect(
            select
        );
    }


    function cerrarSelect(
        wrapper
    ) {

        wrapper?.classList.remove(
            'open'
        );


        wrapper
            ?.querySelector(
                '.profesor-select-trigger'
            )
            ?.setAttribute(
                'aria-expanded',
                'false'
            );
    }


    function cerrarTodosSelects(
        excepto = null
    ) {

        document
            .querySelectorAll(
                '[data-profesor-select]'
            )
            .forEach(
                wrapper => {

                    if (
                        wrapper !== excepto
                    ) {

                        cerrarSelect(
                            wrapper
                        );
                    }
                }
            );
    }


    function renderOpcionesSelect(
        wrapper
    ) {

        const select =
            document.getElementById(
                wrapper.dataset.profesorSelect
            );


        const container =
            wrapper.querySelector(
                '.profesor-select-options'
            );


        if (
            !select ||
            !container
        ) {
            return;
        }


        const opciones =
            Array.from(
                select.options
            );


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
                                    profesor-select-option
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

                                <span class="profesor-option-icon">

                                    ${iconoSelect(
                                        wrapper.dataset.icon
                                    )}

                                </span>


                                <span class="profesor-option-text">

                                    ${esc(
                                        option.textContent
                                    )}

                                </span>


                                <svg
                                    class="profesor-option-check"
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
                '.profesor-select-option'
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


                            actualizarCustomSelect(
                                select
                            );


                            cerrarSelect(
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
            wrapperSelect(
                select
            );


        if (!wrapper) {
            return;
        }


        const texto =
            wrapper.querySelector(
                '.profesor-select-text'
            );


        const option =
            select.options[
                select.selectedIndex
            ];


        if (texto) {

            texto.textContent =
                option?.textContent ||
                wrapper.dataset.placeholder ||
                'Seleccionar';
        }


        renderOpcionesSelect(
            wrapper
        );
    }


    function actualizarTodosCustomSelect() {

        [
            filtroEstado,
            sexoInput,
            especialidadInput,
            estadoInput,
            editSexo,
            editEspecialidad,
            editEstado
        ]
            .filter(Boolean)
            .forEach(
                actualizarCustomSelect
            );
    }


    document
        .querySelectorAll(
            '[data-profesor-select]'
        )
        .forEach(
            construirCustomSelect
        );


    document.addEventListener(
        'click',
        event => {

            if (
                !event.target.closest(
                    '[data-profesor-select]'
                )
            ) {

                cerrarTodosSelects();
            }
        }
    );


    // =========================================================
    // ESPECIALIDAD
    // =========================================================

    function nombreEspecialidad(
        valor
    ) {

        const nombres = {

            matematica:
                'Matemática',

            comunicacion:
                'Comunicación',

            ingles:
                'Inglés',

            fisica:
                'Física',

            quimica:
                'Química',

            historia:
                'Historia',

            arte:
                'Arte',

            musica:
                'Música',

            educacion_fisica:
                'Educación Física'
        };


        return (
            nombres[valor] ||
            valor ||
            '—'
        );
    }


    // =========================================================
    // INSTITUCIONES
    // =========================================================

    function institucionesProfesor(
        profesor
    ) {

        if (
            Array.isArray(
                profesor.instituciones
            ) &&
            profesor.instituciones.length
        ) {

            return profesor.instituciones;
        }


        /*
         * Compatibilidad con profesores antiguos
         * creados antes de guardar institución.
         */
        return [
            'colegio',
            'academia'
        ];
    }


    function htmlInstituciones(
        profesor
    ) {

        const instituciones =
            institucionesProfesor(
                profesor
            );


        return instituciones
            .map(
                institucion => {

                    if (
                        institucion ===
                        'academia'
                    ) {

                        return `
                            <span
                                class="
                                    inline-flex
                                    rounded-full
                                    bg-red-50
                                    px-2.5 py-1
                                    text-[11px]
                                    font-semibold
                                    text-red-700
                                "
                            >
                                Academia
                            </span>
                        `;
                    }


                    return `
                        <span
                            class="
                                inline-flex
                                rounded-full
                                bg-blue-50
                                px-2.5 py-1
                                text-[11px]
                                font-semibold
                                text-[#1B3A6B]
                            "
                        >
                            Colegio
                        </span>
                    `;
                }
            )
            .join('');
    }


    // =========================================================
    // MODAL NUEVO
    // =========================================================

    function prepararNuevoProfesor() {

        profesorForm.reset();


        cargaHorariaInput.value =
            '30';


        estadoInput.value =
            'activo';


        actualizarTodosCustomSelect();
    }


    function abrirProfesorModal() {

        prepararNuevoProfesor();


        profesorModal.classList.remove(
            'hidden'
        );


        profesorModal.setAttribute(
            'aria-hidden',
            'false'
        );


        document.body.classList.add(
            'overflow-hidden'
        );


        setTimeout(
            () => codigoInput.focus(),
            100
        );
    }


    function cerrarProfesorModal() {

        profesorModal.classList.add(
            'hidden'
        );


        profesorModal.setAttribute(
            'aria-hidden',
            'true'
        );


        document.body.classList.remove(
            'overflow-hidden'
        );


        cerrarTodosSelects();


        prepararNuevoProfesor();
    }


    openProfesorModal?.addEventListener(
        'click',
        abrirProfesorModal
    );


    closeProfesorModal?.addEventListener(
        'click',
        cerrarProfesorModal
    );


    cancelProfesorModal?.addEventListener(
        'click',
        cerrarProfesorModal
    );


    profesorModalOverlay?.addEventListener(
        'click',
        cerrarProfesorModal
    );


    // =========================================================
    // GUARDAR PROFESOR
    // =========================================================

    profesorForm.addEventListener(
        'submit',
        event => {

            event.preventDefault();


            const codigo =
                codigoInput.value
                    .trim()
                    .toUpperCase();


            const nombre =
                nombreInput.value
                    .trim();


            const apellidoPaterno =
                apellidoPaternoInput.value
                    .trim();


            const apellidoMaterno =
                apellidoMaternoInput.value
                    .trim();


            const dni =
                dniInput.value
                    .trim();


            const email =
                emailInput.value
                    .trim();


            const telefono =
                telefonoInput.value
                    .trim();


            const sexo =
                sexoInput.value;


            const fechaNacimiento =
                fechaNacimientoInput.value;


            const especialidad =
                especialidadInput.value;


            const carga =
                Number(
                    cargaHorariaInput.value ||
                    30
                );


            const estado =
                estadoInput.value;


            const observaciones =
                observacionesInput.value
                    .trim();


            const instituciones =
                Array.from(
                    profesorForm.querySelectorAll(
                        'input[name="instituciones[]"]:checked'
                    )
                )
                    .map(
                        input =>
                            input.value
                    );


            if (
                !codigo ||
                !nombre ||
                !apellidoPaterno ||
                !dni ||
                !email
            ) {

                alert(
                    'Completa todos los campos obligatorios.'
                );

                return;
            }


            if (
                dni.length !== 8 ||
                !/^\d{8}$/.test(
                    dni
                )
            ) {

                alert(
                    'El DNI debe contener 8 números.'
                );

                dniInput.focus();

                return;
            }


            if (
                !instituciones.length
            ) {

                alert(
                    'Selecciona al menos una institución.'
                );

                return;
            }


            const profesores =
                leerProfesores();


            const duplicadoCodigo =
                profesores.find(
                    profesor =>
                        normalizarTexto(
                            profesor.codigo
                        ) ===
                        normalizarTexto(
                            codigo
                        )
                );


            if (duplicadoCodigo) {

                alert(
                    'Ya existe un profesor con ese código.'
                );

                return;
            }


            const duplicadoDni =
                profesores.find(
                    profesor =>
                        String(
                            profesor.dni
                        ) ===
                        dni
                );


            if (duplicadoDni) {

                alert(
                    'Ya existe un profesor con ese DNI.'
                );

                return;
            }


            const duplicadoEmail =
                profesores.find(
                    profesor =>
                        normalizarTexto(
                            profesor.email
                        ) ===
                        normalizarTexto(
                            email
                        )
                );


            if (duplicadoEmail) {

                alert(
                    'Ya existe un profesor con ese correo electrónico.'
                );

                return;
            }


            profesores.push({

                id:
                    Date.now(),

                codigo,

                nombre,

                apellido_paterno:
                    apellidoPaterno,

                apellido_materno:
                    apellidoMaterno,

                dni,

                email,

                telefono,

                sexo,

                fecha_nacimiento:
                    fechaNacimiento,

                especialidad,

                carga_horaria_maxima:
                    carga,

                estado,

                observaciones,

                instituciones
            });


            guardarProfesores(
                profesores
            );


            cerrarProfesorModal();


            const filtrados =
                obtenerProfesoresFiltrados(
                    profesores
                );


            paginaActual =
                Math.max(
                    1,
                    Math.ceil(
                        filtrados.length /
                        POR_PAGINA
                    )
                );


            renderizarProfesores();
        }
    );


    // =========================================================
    // FILTRADO
    // =========================================================

    function obtenerProfesoresFiltrados(
        profesores
    ) {

        const texto =
            normalizarTexto(
                buscador?.value
            );


        const estado =
            filtroEstado?.value ||
            '';


        return profesores.filter(
            profesor => {

                const contenido =
                    normalizarTexto(
                        `
                            ${profesor.nombre || ''}
                            ${profesor.apellido_paterno || ''}
                            ${profesor.apellido_materno || ''}
                            ${profesor.dni || ''}
                            ${profesor.email || ''}
                            ${profesor.telefono || ''}
                            ${profesor.especialidad || ''}
                        `
                    );


                const coincideTexto =
                    !texto ||
                    contenido.includes(
                        texto
                    );


                const coincideEstado =
                    !estado ||
                    profesor.estado ===
                    estado;


                return (
                    coincideTexto &&
                    coincideEstado
                );
            }
        );
    }


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    function actualizarEstadisticas(
        profesores
    ) {

        const activos =
            profesores.filter(
                profesor =>
                    profesor.estado ===
                    'activo'
            ).length;


        const inactivos =
            profesores.filter(
                profesor =>
                    profesor.estado ===
                    'inactivo' ||
                    profesor.estado ===
                    'licencia'
            ).length;


        totalProfesores.textContent =
            profesores.length;


        profesoresActivos.textContent =
            activos;


        profesoresInactivos.textContent =
            inactivos;
    }


    // =========================================================
    // ESTADO HTML
    // =========================================================

    function htmlEstado(
        estado
    ) {

        if (
            estado ===
            'activo'
        ) {

            return `
                <span
                    class="
                        inline-flex
                        items-center
                        gap-1.5
                        rounded-full
                        bg-emerald-50
                        px-2.5 py-1
                        text-xs
                        font-semibold
                        text-emerald-700
                    "
                >
                    <span
                        class="
                            h-1.5 w-1.5
                            rounded-full
                            bg-emerald-500
                        "
                    ></span>

                    Activo
                </span>
            `;
        }


        if (
            estado ===
            'licencia'
        ) {

            return `
                <span
                    class="
                        inline-flex
                        items-center
                        gap-1.5
                        rounded-full
                        bg-amber-50
                        px-2.5 py-1
                        text-xs
                        font-semibold
                        text-amber-700
                    "
                >
                    <span
                        class="
                            h-1.5 w-1.5
                            rounded-full
                            bg-amber-500
                        "
                    ></span>

                    Licencia
                </span>
            `;
        }


        return `
            <span
                class="
                    inline-flex
                    items-center
                    gap-1.5
                    rounded-full
                    bg-slate-100
                    px-2.5 py-1
                    text-xs
                    font-semibold
                    text-slate-600
                "
            >
                <span
                    class="
                        h-1.5 w-1.5
                        rounded-full
                        bg-slate-400
                    "
                ></span>

                Inactivo
            </span>
        `;
    }


    // =========================================================
    // FILA
    // =========================================================

    function crearFila(
        profesor
    ) {

        const fila =
            document.createElement(
                'tr'
            );


        fila.className =
            'profesor-row';


        const nombreCompleto =
            `
                ${profesor.nombre || ''}
                ${profesor.apellido_paterno || ''}
                ${profesor.apellido_materno || ''}
            `
                .replace(
                    /\s+/g,
                    ' '
                )
                .trim();


        const iniciales =
            (
                String(
                    profesor.nombre ||
                    ''
                ).charAt(0) +

                String(
                    profesor.apellido_paterno ||
                    ''
                ).charAt(0)
            )
                .toUpperCase() ||
            'PR';


        fila.innerHTML = `

            <td class="px-5 py-4">

                <div class="flex items-center gap-3">

                    <div
                        class="
                            flex
                            h-11 w-11
                            shrink-0
                            items-center
                            justify-center
                            rounded-full
                            bg-blue-50
                            text-sm
                            font-extrabold
                            text-[#1B3A6B]
                        "
                    >
                        ${esc(
                            iniciales
                        )}
                    </div>


                    <div class="min-w-0">

                        <p
                            class="
                                truncate
                                text-sm
                                font-bold
                                text-slate-800
                            "
                        >
                            ${esc(
                                nombreCompleto
                            )}
                        </p>


                        <p
                            class="
                                mt-0.5
                                truncate
                                text-xs
                                text-slate-400
                            "
                        >
                            ${esc(
                                profesor.email ||
                                'Sin correo'
                            )}
                        </p>

                    </div>

                </div>

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                ${esc(
                    profesor.dni ||
                    '—'
                )}

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                ${esc(
                    nombreEspecialidad(
                        profesor.especialidad
                    )
                )}

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                ${esc(
                    profesor.telefono ||
                    '—'
                )}

            </td>


            <td class="px-5 py-4">

                <div class="flex flex-wrap gap-1.5">

                    ${htmlInstituciones(
                        profesor
                    )}

                </div>

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-600">

                ${esc(
                    profesor.carga_horaria_maxima ||
                    30
                )}

                h

            </td>


            <td class="whitespace-nowrap px-5 py-4">

                ${htmlEstado(
                    profesor.estado
                )}

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-right">

                <div class="inline-flex items-center gap-2">

                    <button
                        type="button"
                        class="
                            profesor-action-btn
                            profesor-action-edit
                            editar-profesor
                        "
                        data-id="${esc(
                            profesor.id
                        )}"
                        title="Editar profesor"
                        aria-label="Editar profesor"
                    >

                        <svg
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4z"/>
                        </svg>

                    </button>


                    <button
                        type="button"
                        class="
                            profesor-action-btn
                            profesor-action-delete
                            eliminar-profesor
                        "
                        data-id="${esc(
                            profesor.id
                        )}"
                        data-nombre="${esc(
                            nombreCompleto
                        )}"
                        title="Eliminar profesor"
                        aria-label="Eliminar profesor"
                    >

                        <svg
                            width="16"
                            height="16"
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


        return fila;
    }


    // =========================================================
    // PAGINACIÓN
    // =========================================================

    function renderizarPaginacion(
        totalFiltrados
    ) {

        if (!paginacion) {
            return;
        }


        const totalPaginas =
            Math.max(
                1,
                Math.ceil(
                    totalFiltrados /
                    POR_PAGINA
                )
            );


        if (
            paginaActual >
            totalPaginas
        ) {

            paginaActual =
                totalPaginas;
        }


        /*
         * Con 6 o menos profesores no mostramos
         * paginación innecesaria.
         */
        if (
            totalFiltrados <=
            POR_PAGINA
        ) {

            paginacion.innerHTML =
                '';

            return;
        }


        let html = `

            <button
                type="button"
                class="profesor-pagination-btn"
                data-pagina="${
                    paginaActual -
                    1
                }"
                ${
                    paginaActual === 1
                        ? 'disabled'
                        : ''
                }
            >
                Anterior
            </button>
        `;


        for (
            let pagina = 1;
            pagina <= totalPaginas;
            pagina++
        ) {

            html += `

                <button
                    type="button"
                    class="
                        profesor-pagination-btn
                        ${
                            pagina ===
                            paginaActual
                                ? 'active'
                                : ''
                        }
                    "
                    data-pagina="${pagina}"
                >
                    ${pagina}
                </button>
            `;
        }


        html += `

            <button
                type="button"
                class="profesor-pagination-btn"
                data-pagina="${
                    paginaActual +
                    1
                }"
                ${
                    paginaActual ===
                    totalPaginas
                        ? 'disabled'
                        : ''
                }
            >
                Siguiente
            </button>
        `;


        paginacion.innerHTML =
            html;
    }


    paginacion?.addEventListener(
        'click',
        event => {

            const button =
                event.target.closest(
                    '[data-pagina]'
                );


            if (
                !button ||
                button.disabled
            ) {
                return;
            }


            paginaActual =
                Number(
                    button.dataset.pagina
                );


            renderizarProfesores();


            tabla
                .closest(
                    '.profesor-card'
                )
                ?.scrollIntoView({
                    behavior:
                        'smooth',

                    block:
                        'start'
                });
        }
    );


    // =========================================================
    // RENDERIZAR
    // =========================================================

    function renderizarProfesores() {

        const profesores =
            leerProfesores();


        actualizarEstadisticas(
            profesores
        );


        const filtrados =
            obtenerProfesoresFiltrados(
                profesores
            );


        const totalPaginas =
            Math.max(
                1,
                Math.ceil(
                    filtrados.length /
                    POR_PAGINA
                )
            );


        if (
            paginaActual >
            totalPaginas
        ) {

            paginaActual =
                totalPaginas;
        }


        const inicio =
            (
                paginaActual -
                1
            ) *
            POR_PAGINA;


        const pagina =
            filtrados.slice(
                inicio,
                inicio + POR_PAGINA
            );


        tabla.innerHTML =
            '';


        if (!filtrados.length) {

            tabla.innerHTML = `

                <tr>

                    <td
                        colspan="8"
                        class="
                            px-6
                            py-16
                            text-center
                        "
                    >

                        <div class="flex flex-col items-center">

                            <div
                                class="
                                    flex
                                    h-16 w-16
                                    items-center justify-center
                                    rounded-2xl
                                    bg-slate-100
                                    text-slate-400
                                "
                            >

                                <svg
                                    class="h-8 w-8"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                >
                                    <circle cx="10" cy="8" r="4"/>
                                    <path d="M3 21a7 7 0 0 1 14 0"/>
                                    <path d="m17 17 4 4"/>
                                    <circle cx="18" cy="18" r="3"/>
                                </svg>

                            </div>


                            <h3
                                class="mt-4 font-bold"
                                style="color:#0F2749;"
                            >
                                No se encontraron profesores
                            </h3>


                            <p
                                class="
                                    mt-1
                                    text-sm
                                    text-slate-400
                                "
                            >
                                Cambia la búsqueda o el filtro seleccionado.
                            </p>

                        </div>

                    </td>

                </tr>
            `;

        } else {

            pagina.forEach(
                profesor => {

                    tabla.appendChild(
                        crearFila(
                            profesor
                        )
                    );
                }
            );
        }


        resultadosProfesores.textContent =
            pagina.length;


        totalResultadosProfesores.textContent =
            filtrados.length;


        renderizarPaginacion(
            filtrados.length
        );
    }


    // =========================================================
    // FILTROS
    // =========================================================

    function cambioFiltro() {

        paginaActual =
            1;


        renderizarProfesores();
    }


    buscador?.addEventListener(
        'input',
        cambioFiltro
    );


    filtroEstado?.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                filtroEstado
            );


            cambioFiltro();
        }
    );


    // =========================================================
    // CUSTOM SELECTS FORMULARIOS
    // =========================================================

    [
        sexoInput,
        especialidadInput,
        estadoInput,
        editSexo,
        editEspecialidad,
        editEstado
    ]
        .filter(Boolean)
        .forEach(
            select => {

                select.addEventListener(
                    'change',
                    () => {

                        actualizarCustomSelect(
                            select
                        );
                    }
                );
            }
        );


    // =========================================================
    // EDITAR
    // =========================================================

    function abrirEditarModal(
        profesor
    ) {

        profesorEditandoId =
            profesor.id;


        editId.value =
            profesor.id;


        editCodigo.value =
            profesor.codigo ||
            '';


        editNombre.value =
            profesor.nombre ||
            '';


        editApellidoPaterno.value =
            profesor.apellido_paterno ||
            '';


        editApellidoMaterno.value =
            profesor.apellido_materno ||
            '';


        editDni.value =
            profesor.dni ||
            '';


        editEmail.value =
            profesor.email ||
            '';


        editTelefono.value =
            profesor.telefono ||
            '';


        editSexo.value =
            profesor.sexo ||
            '';


        editFechaNacimiento.value =
            profesor.fecha_nacimiento ||
            '';


        editEspecialidad.value =
            profesor.especialidad ||
            '';


        editCargaHoraria.value =
            profesor.carga_horaria_maxima ||
            30;


        editEstado.value =
            profesor.estado ||
            'activo';


        editObservaciones.value =
            profesor.observaciones ||
            '';


        const instituciones =
            institucionesProfesor(
                profesor
            );


        editInstitucionColegio.checked =
            instituciones.includes(
                'colegio'
            );


        editInstitucionAcademia.checked =
            instituciones.includes(
                'academia'
            );


        actualizarTodosCustomSelect();


        editarModal.classList.remove(
            'hidden'
        );


        editarModal.setAttribute(
            'aria-hidden',
            'false'
        );


        document.body.classList.add(
            'overflow-hidden'
        );
    }


    function cerrarEditarModal() {

        editarModal.classList.add(
            'hidden'
        );


        editarModal.setAttribute(
            'aria-hidden',
            'true'
        );


        document.body.classList.remove(
            'overflow-hidden'
        );


        profesorEditandoId =
            null;


        editarForm.reset();


        cerrarTodosSelects();
    }


    closeEditar?.addEventListener(
        'click',
        cerrarEditarModal
    );


    cancelEditar?.addEventListener(
        'click',
        cerrarEditarModal
    );


    editarOverlay?.addEventListener(
        'click',
        cerrarEditarModal
    );


    // =========================================================
    // CLICK TABLA
    // =========================================================

    tabla.addEventListener(
        'click',
        event => {

            const editar =
                event.target.closest(
                    '.editar-profesor'
                );


            if (editar) {

                const profesor =
                    leerProfesores()
                        .find(
                            item =>
                                String(
                                    item.id
                                ) ===
                                String(
                                    editar.dataset.id
                                )
                        );


                if (profesor) {

                    abrirEditarModal(
                        profesor
                    );
                }


                return;
            }


            const eliminar =
                event.target.closest(
                    '.eliminar-profesor'
                );


            if (eliminar) {

                abrirEliminarModal(

                    eliminar.dataset
                        .nombre,

                    eliminar.dataset
                        .id
                );
            }
        }
    );


    // =========================================================
    // GUARDAR EDICIÓN
    // =========================================================

    editarForm?.addEventListener(
        'submit',
        event => {

            event.preventDefault();


            if (
                profesorEditandoId ===
                null
            ) {
                return;
            }


            const instituciones =
                [];


            if (
                editInstitucionColegio.checked
            ) {

                instituciones.push(
                    'colegio'
                );
            }


            if (
                editInstitucionAcademia.checked
            ) {

                instituciones.push(
                    'academia'
                );
            }


            if (
                !instituciones.length
            ) {

                alert(
                    'Selecciona al menos una institución.'
                );

                return;
            }


            const profesores =
                leerProfesores();


            const index =
                profesores.findIndex(
                    profesor =>
                        String(
                            profesor.id
                        ) ===
                        String(
                            profesorEditandoId
                        )
                );


            if (
                index ===
                -1
            ) {
                return;
            }


            const dni =
                editDni.value
                    .trim();


            const email =
                editEmail.value
                    .trim();


            const duplicadoDni =
                profesores.find(
                    profesor =>
                        String(
                            profesor.id
                        ) !==
                        String(
                            profesorEditandoId
                        ) &&
                        String(
                            profesor.dni
                        ) ===
                        dni
                );


            if (duplicadoDni) {

                alert(
                    'Ya existe otro profesor con ese DNI.'
                );

                return;
            }


            const duplicadoEmail =
                profesores.find(
                    profesor =>
                        String(
                            profesor.id
                        ) !==
                        String(
                            profesorEditandoId
                        ) &&
                        normalizarTexto(
                            profesor.email
                        ) ===
                        normalizarTexto(
                            email
                        )
                );


            if (duplicadoEmail) {

                alert(
                    'Ya existe otro profesor con ese correo.'
                );

                return;
            }


            profesores[index] = {

                ...profesores[index],

                nombre:
                    editNombre.value
                        .trim(),

                apellido_paterno:
                    editApellidoPaterno.value
                        .trim(),

                apellido_materno:
                    editApellidoMaterno.value
                        .trim(),

                dni,

                email,

                telefono:
                    editTelefono.value
                        .trim(),

                sexo:
                    editSexo.value,

                fecha_nacimiento:
                    editFechaNacimiento.value,

                especialidad:
                    editEspecialidad.value,

                carga_horaria_maxima:
                    Number(
                        editCargaHoraria.value ||
                        30
                    ),

                estado:
                    editEstado.value,

                observaciones:
                    editObservaciones.value
                        .trim(),

                instituciones
            };


            guardarProfesores(
                profesores
            );


            cerrarEditarModal();


            renderizarProfesores();
        }
    );


    // =========================================================
    // ELIMINAR
    // =========================================================

    function abrirEliminarModal(
        nombre,
        id
    ) {

        profesorAEliminarId =
            id;


        eliminarNombre.textContent =
            `"${nombre}"`;


        eliminarModal.classList.remove(
            'hidden'
        );


        eliminarModal.setAttribute(
            'aria-hidden',
            'false'
        );


        document.body.classList.add(
            'overflow-hidden'
        );
    }


    function cerrarEliminarModal() {

        profesorAEliminarId =
            null;


        eliminarModal.classList.add(
            'hidden'
        );


        eliminarModal.setAttribute(
            'aria-hidden',
            'true'
        );


        document.body.classList.remove(
            'overflow-hidden'
        );
    }


    eliminarOverlay?.addEventListener(
        'click',
        cerrarEliminarModal
    );


    closeEliminar?.addEventListener(
        'click',
        cerrarEliminarModal
    );


    cancelEliminar?.addEventListener(
        'click',
        cerrarEliminarModal
    );


    confirmEliminar?.addEventListener(
        'click',
        () => {

            if (
                profesorAEliminarId ===
                null
            ) {
                return;
            }


            const profesores =
                leerProfesores()
                    .filter(
                        profesor =>
                            String(
                                profesor.id
                            ) !==
                            String(
                                profesorAEliminarId
                            )
                    );


            guardarProfesores(
                profesores
            );


            cerrarEliminarModal();


            renderizarProfesores();
        }
    );


    // =========================================================
    // ESC
    // =========================================================

    document.addEventListener(
        'keydown',
        event => {

            if (
                event.key !==
                'Escape'
            ) {
                return;
            }


            cerrarTodosSelects();


            if (
                !profesorModal.classList.contains(
                    'hidden'
                )
            ) {

                cerrarProfesorModal();
            }


            if (
                editarModal &&
                !editarModal.classList.contains(
                    'hidden'
                )
            ) {

                cerrarEditarModal();
            }


            if (
                eliminarModal &&
                !eliminarModal.classList.contains(
                    'hidden'
                )
            ) {

                cerrarEliminarModal();
            }
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

                paginaActual =
                    1;


                renderizarProfesores();
            }
        }
    );


    window.addEventListener(
        'focus',
        () => {

            renderizarProfesores();

            actualizarTodosCustomSelect();
        }
    );


    // =========================================================
    // INICIO
    // =========================================================

    prepararNuevoProfesor();

    renderizarProfesores();

});