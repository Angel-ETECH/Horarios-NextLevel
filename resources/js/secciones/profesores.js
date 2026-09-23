import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {

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
    // API
    // =========================================================

    const API_PROFESORES =
        '/api/profesores';

    const API_ESTADISTICAS =
        '/api/profesores/estadisticas';


    axios.defaults.headers.common['Accept'] =
        'application/json';

    axios.defaults.headers.common['X-Requested-With'] =
        'XMLHttpRequest';

    axios.defaults.withCredentials =
        true;


    // =========================================================
    // CREAR
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

    let profesoresPagina =
        [];

    let profesorEditandoId =
        null;

    let profesorAEliminarId =
        null;

    let paginaActual =
        1;

    let ultimaPagina =
        1;

    let totalRegistros =
        0;

    const POR_PAGINA =
        6;

    let timeoutBusqueda =
        null;


    // =========================================================
    // HELPERS
    // =========================================================

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


    function valorNullable(
        valor
    ) {

        const texto =
            String(
                valor ?? ''
            ).trim();


        return texto === ''
            ? null
            : texto;
    }


    function fechaParaInput(
        valor
    ) {

        if (!valor) {
            return '';
        }


        const texto =
            String(
                valor
            );


        const coincidencia =
            texto.match(
                /^(\d{4}-\d{2}-\d{2})/
            );


        return coincidencia
            ? coincidencia[1]
            : '';
    }


    function nombreEspecialidad(
        valor
    ) {

        return String(
            valor ?? ''
        ).trim() || '—';
    }


    function obtenerMensajeError(
        error
    ) {

        const data =
            error?.response?.data ??
            {};


        if (data.errors) {

            const mensajes =
                Object.values(
                    data.errors
                )
                    .flat()
                    .filter(Boolean);


            if (
                mensajes.length
            ) {

                return mensajes.join(
                    '\n'
                );
            }
        }


        return (
            data.message ||
            'Ocurrió un error.'
        );
    }


    // =========================================================
    // TOAST
    // =========================================================

    function mostrarToast(
        mensaje,
        tipo = 'info'
    ) {

        let toast =
            document.getElementById(
                'profesor-toast'
            );


        if (!toast) {

            toast =
                document.createElement(
                    'div'
                );


            toast.id =
                'profesor-toast';


            toast.className = `
                fixed
                bottom-5
                right-5
                z-[500]
                hidden
                max-w-sm
                rounded-xl
                px-4
                py-3
                text-sm
                font-semibold
                text-white
                shadow-xl
            `;


            document.body.appendChild(
                toast
            );
        }


        toast.textContent =
            mensaje;


        toast.classList.remove(
            'hidden',
            'bg-slate-900',
            'bg-emerald-600',
            'bg-red-600',
            'bg-amber-600'
        );


        if (
            tipo ===
            'success'
        ) {

            toast.classList.add(
                'bg-emerald-600'
            );

        } else if (
            tipo ===
            'error'
        ) {

            toast.classList.add(
                'bg-red-600'
            );

        } else if (
            tipo ===
            'warning'
        ) {

            toast.classList.add(
                'bg-amber-600'
            );

        } else {

            toast.classList.add(
                'bg-slate-900'
            );
        }


        clearTimeout(
            mostrarToast.timeout
        );


        mostrarToast.timeout =
            setTimeout(
                () => {

                    toast.classList.add(
                        'hidden'
                    );

                },
                3500
            );
    }


    // =========================================================
    // CUSTOM SELECTS
    // =========================================================

    function iconoSelect(
        tipo
    ) {

        if (
            tipo ===
            'usuario'
        ) {

            return `
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <circle cx="12" cy="7" r="4"/>
                    <path d="M20 21a8 8 0 0 0-16 0"/>
                </svg>
            `;
        }


        return `
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                <circle cx="12" cy="12" r="9"/>
                <path d="m8 12 2.5 2.5L16 9"/>
            </svg>
        `;
    }


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
            wrapper.dataset.ready ===
            'true'
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


        wrapper.innerHTML = `

            <button
                type="button"
                class="profesor-select-trigger"
            >

                <span class="profesor-select-icon">
                    ${iconoSelect(
                        wrapper.dataset.icon
                    )}
                </span>

                <span class="profesor-select-content">

                    <span class="profesor-select-label">
                        ${esc(
                            wrapper.dataset.label ||
                            'Seleccionar'
                        )}
                    </span>

                    <span class="profesor-select-text"></span>

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


        wrapper
            .querySelector(
                '.profesor-select-trigger'
            )
            .addEventListener(
                'click',
                () => {

                    cerrarTodosSelects(
                        wrapper
                    );


                    wrapper.classList.toggle(
                        'open'
                    );


                    renderOpcionesSelect(
                        wrapper
                    );
                }
            );


        actualizarCustomSelect(
            select
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
                        wrapper !==
                        excepto
                    ) {

                        wrapper.classList.remove(
                            'open'
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


        const contenedor =
            wrapper.querySelector(
                '.profesor-select-options'
            );


        if (
            !select ||
            !contenedor
        ) {
            return;
        }


        contenedor.innerHTML =
            Array.from(
                select.options
            )
                .map(
                    option => `

                        <button
                            type="button"
                            class="
                                profesor-select-option
                                ${
                                    String(
                                        option.value
                                    ) ===
                                    String(
                                        select.value
                                    )
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
                    `
                )
                .join('');


        contenedor
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
                                        bubbles:
                                            true
                                    }
                                )
                            );


                            actualizarCustomSelect(
                                select
                            );


                            wrapper.classList.remove(
                                'open'
                            );
                        }
                    );
                }
            );
    }


    function actualizarCustomSelect(
        select
    ) {

        const wrapper =
            wrapperSelect(
                select
            );


        if (!wrapper) {
            return;
        }


        const option =
            select.options[
                select.selectedIndex
            ];


        const texto =
            wrapper.querySelector(
                '.profesor-select-text'
            );


        if (texto) {

            texto.textContent =
                option?.textContent ||
                wrapper.dataset.placeholder ||
                'Seleccionar';
        }
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


    [
        filtroEstado,
        sexoInput,
        estadoInput,
        editSexo,
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
    // INSTITUCIÓN
    // =========================================================

    function institucionDesdeChecks(
        colegio,
        academia
    ) {

        if (
            colegio &&
            academia
        ) {
            return 'ambos';
        }


        if (colegio) {
            return 'colegio';
        }


        if (academia) {
            return 'academia';
        }


        return null;
    }


    function institucionesProfesor(
        profesor
    ) {

        if (
            profesor.institucion ===
            'ambos'
        ) {

            return [
                'colegio',
                'academia'
            ];
        }


        if (
            profesor.institucion ===
            'colegio'
        ) {

            return [
                'colegio'
            ];
        }


        if (
            profesor.institucion ===
            'academia'
        ) {

            return [
                'academia'
            ];
        }


        return [];
    }


    function htmlInstituciones(
        profesor
    ) {

        return institucionesProfesor(
            profesor
        )
            .map(
                institucion => {

                    if (
                        institucion ===
                        'academia'
                    ) {

                        return `
                            <span class="rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-semibold text-red-700">
                                Academia
                            </span>
                        `;
                    }


                    return `
                        <span class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-[#1B3A6B]">
                            Colegio
                        </span>
                    `;
                }
            )
            .join('');
    }


    // =========================================================
    // CREAR MODAL
    // =========================================================

    function abrirProfesorModal() {

        profesorForm.reset();

        cargaHorariaInput.value =
            '30';

        estadoInput.value =
            'activo';

        actualizarCustomSelect(
            sexoInput
        );

        actualizarCustomSelect(
            estadoInput
        );


        profesorModal.classList.remove(
            'hidden'
        );


        document.body.classList.add(
            'overflow-hidden'
        );
    }


    function cerrarProfesorModal() {

        profesorModal.classList.add(
            'hidden'
        );


        document.body.classList.remove(
            'overflow-hidden'
        );
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
    // VALIDAR
    // =========================================================

    function validarProfesor(
        datos
    ) {

        if (
            !datos.codigo ||
            !datos.nombre ||
            !datos.apellido_paterno ||
            !datos.email ||
            !datos.dni ||
            !datos.sexo ||
            !datos.estado
        ) {

            mostrarToast(
                'Completa los campos obligatorios.',
                'error'
            );

            return false;
        }


        if (
            !/^\d{8}$/.test(
                datos.dni
            )
        ) {

            mostrarToast(
                'El DNI debe tener 8 dígitos.',
                'error'
            );

            return false;
        }


        if (
            !datos.institucion
        ) {

            mostrarToast(
                'Selecciona una institución.',
                'error'
            );

            return false;
        }


        return true;
    }


    // =========================================================
    // CREAR
    // =========================================================

    profesorForm.addEventListener(
        'submit',
        async event => {

            event.preventDefault();


            const colegio =
                profesorForm.querySelector(
                    '[value="colegio"]'
                )?.checked;


            const academia =
                profesorForm.querySelector(
                    '[value="academia"]'
                )?.checked;


            const datos = {

                codigo:
                    codigoInput.value
                        .trim()
                        .toUpperCase(),

                nombre:
                    nombreInput.value
                        .trim(),

                apellido_paterno:
                    apellidoPaternoInput.value
                        .trim(),

                apellido_materno:
                    valorNullable(
                        apellidoMaternoInput.value
                    ),

                email:
                    emailInput.value
                        .trim(),

                telefono:
                    valorNullable(
                        telefonoInput.value
                    ),

                dni:
                    dniInput.value
                        .trim(),

                sexo:
                    sexoInput.value,

                fecha_nacimiento:
                    fechaNacimientoInput.value ||
                    null,

                especialidad:
                    valorNullable(
                        especialidadInput.value
                    ),

                carga_horaria_maxima:
                    Number(
                        cargaHorariaInput.value ||
                        30
                    ),

                estado:
                    estadoInput.value,

                institucion:
                    institucionDesdeChecks(
                        colegio,
                        academia
                    ),

                observaciones:
                    valorNullable(
                        observacionesInput.value
                    )
            };


            if (
                !validarProfesor(
                    datos
                )
            ) {
                return;
            }


            try {

                const respuesta =
                    await axios.post(
                        API_PROFESORES,
                        datos
                    );


                cerrarProfesorModal();


                mostrarToast(
                    respuesta.data?.message ||
                    'Profesor creado correctamente.',
                    'success'
                );


                paginaActual =
                    1;


                await Promise.all([
                    cargarProfesores(),
                    cargarEstadisticas()
                ]);


            } catch (error) {

                mostrarToast(
                    obtenerMensajeError(
                        error
                    ),
                    'error'
                );
            }
        }
    );


    // =========================================================
    // ESTADO
    // =========================================================

    function htmlEstado(
        estado
    ) {

        if (
            estado ===
            'activo'
        ) {

            return `
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                    Activo
                </span>
            `;
        }


        if (
            estado ===
            'licencia'
        ) {

            return `
                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                    Licencia
                </span>
            `;
        }


        return `
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
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


        fila.innerHTML = `

            <td class="px-5 py-4">
                <div>
                    <p class="font-bold text-slate-800">
                        ${esc(
                            nombreCompleto
                        )}
                    </p>

                    <p class="text-xs text-slate-400">
                        ${esc(
                            profesor.email
                        )}
                    </p>
                </div>
            </td>

            <td class="px-5 py-4 text-sm">
                ${esc(
                    profesor.dni
                )}
            </td>

            <td class="px-5 py-4 text-sm">
                ${esc(
                    nombreEspecialidad(
                        profesor.especialidad
                    )
                )}
            </td>

            <td class="px-5 py-4 text-sm">
                ${esc(
                    profesor.telefono ||
                    '—'
                )}
            </td>

            <td class="px-5 py-4">
                <div class="flex gap-1.5">
                    ${htmlInstituciones(
                        profesor
                    )}
                </div>
            </td>

            <td class="px-5 py-4 text-sm">

                ${esc(
                    profesor.carga_horaria_actual ??
                    0
                )}
                /
                ${esc(
                    profesor.carga_horaria_maxima ??
                    30
                )}
                h

            </td>

            <td class="px-5 py-4">
                ${htmlEstado(
                    profesor.estado
                )}
            </td>

            <td class="px-5 py-4 text-right">

                <button
                    type="button"
                    class="profesor-action-btn profesor-action-edit editar-profesor"
                    data-id="${esc(
                        profesor.id
                    )}"
                >
                    ✎
                </button>

                <button
                    type="button"
                    class="profesor-action-btn profesor-action-delete eliminar-profesor"
                    data-id="${esc(
                        profesor.id
                    )}"
                    data-nombre="${esc(
                        nombreCompleto
                    )}"
                >
                    ×
                </button>

            </td>
        `;


        return fila;
    }


    // =========================================================
    // CARGAR
    // =========================================================

    async function cargarProfesores() {

        tabla.innerHTML = `
            <tr>
                <td colspan="8" class="px-6 py-16 text-center text-slate-400">
                    Cargando profesores...
                </td>
            </tr>
        `;


        try {

            const params = {

                page:
                    paginaActual,

                per_page:
                    POR_PAGINA
            };


            if (
                buscador.value.trim()
            ) {

                params.search =
                    buscador.value.trim();
            }


            if (
                filtroEstado.value
            ) {

                params.estado =
                    filtroEstado.value;
            }


            const respuesta =
                await axios.get(
                    API_PROFESORES,
                    {
                        params
                    }
                );


            const paginador =
                respuesta.data?.data ??
                {};


            profesoresPagina =
                paginador.data ??
                [];


            paginaActual =
                paginador.current_page ??
                1;


            ultimaPagina =
                paginador.last_page ??
                1;


            totalRegistros =
                paginador.total ??
                0;


            renderizarProfesores();


        } catch (error) {

            if (
                error?.response?.status ===
                401
            ) {

                tabla.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">

                            <h3 class="font-bold text-[#0F2749]">
                                Autenticación requerida
                            </h3>

                            <p class="mt-2 text-sm text-slate-400">
                                La API está protegida con Sanctum.
                            </p>

                        </td>
                    </tr>
                `;

            } else {

                tabla.innerHTML = `
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-red-600">
                            Error cargando profesores.
                        </td>
                    </tr>
                `;
            }
        }
    }


    async function cargarEstadisticas() {

        try {

            const respuesta =
                await axios.get(
                    API_ESTADISTICAS
                );


            const data =
                respuesta.data?.data ??
                {};


            totalProfesores.textContent =
                data.total ??
                0;


            profesoresActivos.textContent =
                data.activos ??
                0;


            profesoresInactivos.textContent =
                Number(
                    data.inactivos ??
                    0
                ) +
                Number(
                    data.licencia ??
                    0
                );


        } catch {

            totalProfesores.textContent =
                '0';

            profesoresActivos.textContent =
                '0';

            profesoresInactivos.textContent =
                '0';
        }
    }


    function renderizarProfesores() {

        tabla.innerHTML =
            '';


        if (
            !profesoresPagina.length
        ) {

            tabla.innerHTML = `
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center text-slate-400">
                        No se encontraron profesores.
                    </td>
                </tr>
            `;

        } else {

            profesoresPagina.forEach(
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
            profesoresPagina.length;


        totalResultadosProfesores.textContent =
            totalRegistros;


        renderizarPaginacion();
    }


    // =========================================================
    // PAGINACIÓN
    // =========================================================

    function renderizarPaginacion() {

        if (
            ultimaPagina <=
            1
        ) {

            paginacion.innerHTML =
                '';

            return;
        }


        let html =
            '';


        for (
            let pagina = 1;
            pagina <= ultimaPagina;
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


        paginacion.innerHTML =
            html;
    }


    paginacion.addEventListener(
        'click',
        event => {

            const boton =
                event.target.closest(
                    '[data-pagina]'
                );


            if (!boton) {
                return;
            }


            paginaActual =
                Number(
                    boton.dataset.pagina
                );


            cargarProfesores();
        }
    );


    // =========================================================
    // FILTROS
    // =========================================================

    buscador.addEventListener(
        'input',
        () => {

            clearTimeout(
                timeoutBusqueda
            );


            timeoutBusqueda =
                setTimeout(
                    () => {

                        paginaActual =
                            1;

                        cargarProfesores();

                    },
                    350
                );
        }
    );


    filtroEstado.addEventListener(
        'change',
        () => {

            actualizarCustomSelect(
                filtroEstado
            );


            paginaActual =
                1;


            cargarProfesores();
        }
    );


    // =========================================================
    // EDITAR
    // =========================================================

    tabla.addEventListener(
        'click',
        async event => {

            const botonEditar =
                event.target.closest(
                    '.editar-profesor'
                );


            if (botonEditar) {

                try {

                    const respuesta =
                        await axios.get(
                            `${API_PROFESORES}/${botonEditar.dataset.id}`
                        );


                    abrirEditarModal(
                        respuesta.data.data
                    );


                } catch (error) {

                    mostrarToast(
                        obtenerMensajeError(
                            error
                        ),
                        'error'
                    );
                }


                return;
            }


            const botonEliminar =
                event.target.closest(
                    '.eliminar-profesor'
                );


            if (
                botonEliminar
            ) {

                profesorAEliminarId =
                    botonEliminar.dataset.id;


                eliminarNombre.textContent =
                    `"${botonEliminar.dataset.nombre}"`;


                eliminarModal.classList.remove(
                    'hidden'
                );
            }
        }
    );


    function abrirEditarModal(
        profesor
    ) {

        profesorEditandoId =
            profesor.id;


        editId.value =
            profesor.id;

        editCodigo.value =
            profesor.codigo ??
            '';

        editNombre.value =
            profesor.nombre ??
            '';

        editApellidoPaterno.value =
            profesor.apellido_paterno ??
            '';

        editApellidoMaterno.value =
            profesor.apellido_materno ??
            '';

        editDni.value =
            profesor.dni ??
            '';

        editEmail.value =
            profesor.email ??
            '';

        editTelefono.value =
            profesor.telefono ??
            '';

        editSexo.value =
            profesor.sexo ??
            '';

        editFechaNacimiento.value =
            fechaParaInput(
                profesor.fecha_nacimiento
            );

        editEspecialidad.value =
            profesor.especialidad ??
            '';

        editCargaHoraria.value =
            profesor.carga_horaria_maxima ??
            30;

        editEstado.value =
            profesor.estado ??
            'activo';

        editObservaciones.value =
            profesor.observaciones ??
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


        actualizarCustomSelect(
            editSexo
        );

        actualizarCustomSelect(
            editEstado
        );


        editarModal.classList.remove(
            'hidden'
        );
    }


    editarForm.addEventListener(
        'submit',
        async event => {

            event.preventDefault();


            const datos = {

                codigo:
                    editCodigo.value,

                nombre:
                    editNombre.value.trim(),

                apellido_paterno:
                    editApellidoPaterno.value.trim(),

                apellido_materno:
                    valorNullable(
                        editApellidoMaterno.value
                    ),

                email:
                    editEmail.value.trim(),

                telefono:
                    valorNullable(
                        editTelefono.value
                    ),

                dni:
                    editDni.value.trim(),

                sexo:
                    editSexo.value,

                fecha_nacimiento:
                    editFechaNacimiento.value ||
                    null,

                especialidad:
                    valorNullable(
                        editEspecialidad.value
                    ),

                carga_horaria_maxima:
                    Number(
                        editCargaHoraria.value
                    ),

                estado:
                    editEstado.value,

                institucion:
                    institucionDesdeChecks(
                        editInstitucionColegio.checked,
                        editInstitucionAcademia.checked
                    ),

                observaciones:
                    valorNullable(
                        editObservaciones.value
                    )
            };


            if (
                !validarProfesor(
                    datos
                )
            ) {
                return;
            }


            try {

                const respuesta =
                    await axios.put(
                        `${API_PROFESORES}/${profesorEditandoId}`,
                        datos
                    );


                editarModal.classList.add(
                    'hidden'
                );


                mostrarToast(
                    respuesta.data?.message ||
                    'Profesor actualizado.',
                    'success'
                );


                await Promise.all([
                    cargarProfesores(),
                    cargarEstadisticas()
                ]);


            } catch (error) {

                mostrarToast(
                    obtenerMensajeError(
                        error
                    ),
                    'error'
                );
            }
        }
    );


    closeEditar?.addEventListener(
        'click',
        () => editarModal.classList.add(
            'hidden'
        )
    );


    cancelEditar?.addEventListener(
        'click',
        () => editarModal.classList.add(
            'hidden'
        )
    );


    editarOverlay?.addEventListener(
        'click',
        () => editarModal.classList.add(
            'hidden'
        )
    );


    // =========================================================
    // ELIMINAR
    // =========================================================

    async function eliminarProfesor() {

        if (
            !profesorAEliminarId
        ) {
            return;
        }


        try {

            const respuesta =
                await axios.delete(
                    `${API_PROFESORES}/${profesorAEliminarId}`
                );


            eliminarModal.classList.add(
                'hidden'
            );


            mostrarToast(
                respuesta.data?.message ||
                'Profesor eliminado.',
                'success'
            );


            await Promise.all([
                cargarProfesores(),
                cargarEstadisticas()
            ]);


        } catch (error) {

            mostrarToast(
                obtenerMensajeError(
                    error
                ),
                'error'
            );
        }
    }


    confirmEliminar?.addEventListener(
        'click',
        eliminarProfesor
    );


    cancelEliminar?.addEventListener(
        'click',
        () => eliminarModal.classList.add(
            'hidden'
        )
    );


    eliminarOverlay?.addEventListener(
        'click',
        () => eliminarModal.classList.add(
            'hidden'
        )
    );


    closeEliminar?.addEventListener(
        'click',
        () => eliminarModal.classList.add(
            'hidden'
        )
    );


    // =========================================================
    // INICIO
    // =========================================================

    Promise.all([
        cargarProfesores(),
        cargarEstadisticas()
    ]);

});