import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // GUARD
    // =========================================================

    const modal =
        document.getElementById('curso-modal');

    const formulario =
        document.getElementById('curso-form');

    const tabla =
        document.getElementById('cursos-body');


    if (
        !modal ||
        !formulario ||
        !tabla
    ) {
        return;
    }


    // =========================================================
    // API
    // =========================================================

    const API_CURSOS =
        '/api/cursos';

    const API_ESTADISTICAS =
        '/api/cursos/estadisticas';


    axios.defaults.headers.common['Accept'] =
        'application/json';

    axios.defaults.headers.common['X-Requested-With'] =
        'XMLHttpRequest';

    axios.defaults.withCredentials =
        true;


    // =========================================================
    // MODAL
    // =========================================================

    const abrirModal =
        document.getElementById('open-curso-modal');

    const cerrarModalBtn =
        document.getElementById('close-curso-modal');

    const cancelarModal =
        document.getElementById('cancel-curso-modal');

    const overlay =
        document.getElementById('curso-modal-overlay');


    // =========================================================
    // CAMPOS
    // =========================================================

    const codigoInput =
        document.getElementById('curso-codigo');

    const nombreInput =
        document.getElementById('curso-nombre');

    const descripcionInput =
        document.getElementById('curso-descripcion');

    const nivelInput =
        document.getElementById('curso-nivel');

    const tipoInput =
        document.getElementById('curso-tipo');

    const horasInput =
        document.getElementById('curso-horas');

    const duracionInput =
        document.getElementById('curso-duracion');

    const colorInput =
        document.getElementById('curso-color');

    const estadoInput =
        document.getElementById('curso-estado');

    const modalTitle =
        document.getElementById('curso-modal-title');

    const submitButton =
        document.getElementById('curso-submit-button');

    const submitText =
        document.getElementById('curso-submit-text');

    const colorPreview =
        document.getElementById('curso-color-preview');


    // =========================================================
    // FILTROS
    // =========================================================

    const buscador =
        document.getElementById('buscar-curso');

    const filtroNivel =
        document.getElementById('filtro-curso-nivel');

    const filtroTipo =
        document.getElementById('filtro-curso-tipo');

    const filtroEstado =
        document.getElementById('filtro-curso-estado');


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    const totalCursos =
        document.getElementById('total-cursos');

    const cursosActivos =
        document.getElementById('cursos-activos');

    const cursosInactivos =
        document.getElementById('cursos-inactivos');

    const resultadosCursos =
        document.getElementById('resultados-cursos');

    const totalResultadosCursos =
        document.getElementById('total-resultados-cursos');

    const paginacion =
        document.getElementById('cursos-paginacion');


    // =========================================================
    // ELIMINAR
    // =========================================================

    const eliminarModal =
        document.getElementById('eliminar-curso-modal');

    const overlayEliminar =
        document.getElementById('eliminar-curso-modal-overlay');

    const closeEliminar =
        document.getElementById('close-eliminar-curso-modal');

    const cancelEliminar =
        document.getElementById('cancel-eliminar-curso-modal');

    const confirmEliminar =
        document.getElementById('confirm-eliminar-curso');

    const eliminarNombre =
        document.getElementById('eliminar-curso-nombre');


    // =========================================================
    // ESTADO
    // =========================================================

    let cursosPagina =
        [];

    let cursoEditandoId =
        null;

    let cursoAEliminarId =
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

    let cargandoCursos =
        false;


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


    function obtenerMensajeError(
        error
    ) {

        if (!error?.response) {
            return 'No se pudo conectar con el servidor.';
        }


        const data =
            error.response.data ??
            {};


        if (data.errors) {

            const mensajes =
                Object.values(
                    data.errors
                )
                    .flat()
                    .filter(Boolean);


            if (mensajes.length) {
                return mensajes.join('\n');
            }
        }


        return data.message ||
            'Ocurrió un error inesperado.';
    }


    function esActivo(
        valor
    ) {

        return (
            valor === true ||
            valor === 1 ||
            valor === '1'
        );
    }


    // =========================================================
    // TOAST
    // =========================================================

    function obtenerToast() {

        let toast =
            document.getElementById(
                'curso-toast'
            );


        if (toast) {
            return toast;
        }


        toast =
            document.createElement(
                'div'
            );


        toast.id =
            'curso-toast';


        toast.className = `
            fixed
            bottom-5
            right-5
            z-[500]
            hidden
            max-w-sm
            whitespace-pre-line
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


        return toast;
    }


    function mostrarToast(
        mensaje,
        tipo = 'info'
    ) {

        const toast =
            obtenerToast();


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
                4000
            );
    }


    // =========================================================
    // ICONOS CUSTOM SELECT
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

            nivel: `
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
            `,

            tipo: `
                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M7 8h10"/>
                    <path d="M7 12h6"/>
                </svg>
            `,

            tiempo: `
                <svg
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.9"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            `
        };


        return (
            iconos[tipo] ||
            iconos.tipo
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
            `[data-curso-select="${select.id}"]`
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
                wrapper.dataset.cursoSelect
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


        wrapper.innerHTML = `

            <button
                type="button"
                class="curso-select-trigger"
                aria-expanded="false"
            >

                <span class="curso-select-icon">

                    ${iconoSelect(
                        wrapper.dataset.icon
                    )}

                </span>


                <span class="curso-select-content">

                    <span class="curso-select-label">
                        ${esc(label)}
                    </span>

                    <span class="curso-select-text">
                        ${esc(placeholder)}
                    </span>

                </span>


                <svg
                    class="curso-select-arrow"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m6 9 6 6 6-6"/>
                </svg>

            </button>


            <div class="curso-select-menu">
                <div class="curso-select-options"></div>
            </div>
        `;


        wrapper.dataset.ready =
            'true';


        const trigger =
            wrapper.querySelector(
                '.curso-select-trigger'
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
                '.curso-select-trigger'
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
                '[data-curso-select]'
            )
            .forEach(
                wrapper => {

                    if (
                        wrapper !==
                        excepto
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
                wrapper.dataset.cursoSelect
            );


        const contenedor =
            wrapper.querySelector(
                '.curso-select-options'
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
                                    curso-select-option
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

                                <span class="curso-option-icon">

                                    ${iconoSelect(
                                        wrapper.dataset.icon
                                    )}

                                </span>


                                <span class="curso-option-text">

                                    ${esc(
                                        option.textContent
                                    )}

                                </span>


                                <svg
                                    class="curso-option-check"
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


        contenedor
            .querySelectorAll(
                '.curso-select-option'
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


        const option =
            select.options[
                select.selectedIndex
            ];


        const texto =
            wrapper.querySelector(
                '.curso-select-text'
            );


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
            filtroNivel,
            filtroTipo,
            filtroEstado,
            nivelInput,
            tipoInput,
            duracionInput,
            estadoInput
        ]
            .filter(Boolean)
            .forEach(
                actualizarCustomSelect
            );
    }


    document
        .querySelectorAll(
            '[data-curso-select]'
        )
        .forEach(
            construirCustomSelect
        );


    document.addEventListener(
        'click',
        event => {

            if (
                !event.target.closest(
                    '[data-curso-select]'
                )
            ) {

                cerrarTodosSelects();
            }
        }
    );


    [
        filtroNivel,
        filtroTipo,
        filtroEstado,
        nivelInput,
        tipoInput,
        duracionInput,
        estadoInput
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
    // COLOR
    // =========================================================

    function actualizarColorPreview() {

        if (
            !colorInput ||
            !colorPreview
        ) {
            return;
        }


        colorPreview.textContent =
            colorInput.value.toUpperCase();


        colorPreview.style.color =
            colorInput.value;
    }


    colorInput?.addEventListener(
        'input',
        actualizarColorPreview
    );


    // =========================================================
    // LABELS
    // =========================================================

    function labelNivel(
        nivel
    ) {

        const labels = {

            primaria:
                'Primaria',

            secundaria:
                'Secundaria',

            academia:
                'Academia',

            todos:
                'Todos'
        };


        return (
            labels[nivel] ||
            nivel ||
            '—'
        );
    }


    function labelTipo(
        tipo
    ) {

        const labels = {

            obligatorio:
                'Obligatorio',

            electivo:
                'Electivo',

            taller:
                'Taller'
        };


        return (
            labels[tipo] ||
            tipo ||
            '—'
        );
    }


    // =========================================================
    // MODAL
    // =========================================================

    function prepararNuevoCurso() {

        cursoEditandoId =
            null;


        formulario.reset();


        codigoInput.readOnly =
            false;


        codigoInput.classList.remove(
            'bg-slate-100',
            'cursor-not-allowed'
        );


        nivelInput.value =
            'todos';


        tipoInput.value =
            'obligatorio';


        horasInput.value =
            '4';


        duracionInput.value =
            '60';


        colorInput.value =
            '#1B3A6B';


        estadoInput.value =
            '1';


        modalTitle.textContent =
            'Agregar curso';


        submitText.textContent =
            'Guardar curso';


        actualizarColorPreview();


        actualizarTodosCustomSelect();
    }


    function abrirCursoModal(
        editar = false
    ) {

        if (!editar) {
            prepararNuevoCurso();
        }


        modal.classList.remove(
            'hidden'
        );


        modal.setAttribute(
            'aria-hidden',
            'false'
        );


        document.body.classList.add(
            'overflow-hidden'
        );


        setTimeout(
            () => {

                if (!editar) {
                    codigoInput.focus();
                } else {
                    nombreInput.focus();
                }
            },
            100
        );
    }


    function cerrarCursoModal() {

        modal.classList.add(
            'hidden'
        );


        modal.setAttribute(
            'aria-hidden',
            'true'
        );


        document.body.classList.remove(
            'overflow-hidden'
        );


        cerrarTodosSelects();


        prepararNuevoCurso();
    }


    abrirModal?.addEventListener(
        'click',
        () => {

            abrirCursoModal(
                false
            );
        }
    );


    cerrarModalBtn?.addEventListener(
        'click',
        cerrarCursoModal
    );


    cancelarModal?.addEventListener(
        'click',
        cerrarCursoModal
    );


    overlay?.addEventListener(
        'click',
        cerrarCursoModal
    );


    // =========================================================
    // VALIDACIÓN
    // =========================================================

    function validarCurso(
        datos
    ) {

        if (
            !datos.codigo ||
            !datos.nombre ||
            !datos.nivel ||
            !datos.tipo
        ) {

            mostrarToast(
                'Completa los campos obligatorios.',
                'error'
            );

            return false;
        }


        if (
            datos.horas_semanales < 1 ||
            datos.horas_semanales > 30
        ) {

            mostrarToast(
                'Las horas semanales deben estar entre 1 y 30.',
                'error'
            );

            return false;
        }


        const duraciones =
            [
                30,
                45,
                60,
                90,
                120
            ];


        if (
            !duraciones.includes(
                datos.duracion_minutos
            )
        ) {

            mostrarToast(
                'La duración debe ser 30, 45, 60, 90 o 120 minutos.',
                'error'
            );

            return false;
        }


        if (
            !/^#[A-Fa-f0-9]{6}$/.test(
                datos.color
            )
        ) {

            mostrarToast(
                'El color seleccionado no es válido.',
                'error'
            );

            return false;
        }


        return true;
    }


    function obtenerDatosFormulario() {

        return {

            codigo:
                codigoInput.value
                    .trim()
                    .toUpperCase(),

            nombre:
                nombreInput.value
                    .trim(),

            descripcion:
                valorNullable(
                    descripcionInput.value
                ),

            horas_semanales:
                Number(
                    horasInput.value
                ),

            duracion_minutos:
                Number(
                    duracionInput.value
                ),

            nivel:
                nivelInput.value,

            tipo:
                tipoInput.value,

            color:
                colorInput.value,

            activo:
                estadoInput.value ===
                '1'
        };
    }


    // =========================================================
    // GUARDAR
    // =========================================================

    formulario.addEventListener(
        'submit',
        async event => {

            event.preventDefault();


            const datos =
                obtenerDatosFormulario();


            if (
                !validarCurso(
                    datos
                )
            ) {
                return;
            }


            const textoOriginal =
                submitText.textContent;


            try {

                submitButton.disabled =
                    true;


                submitText.textContent =
                    cursoEditandoId
                        ? 'Guardando...'
                        : 'Registrando...';


                let respuesta;


                if (
                    cursoEditandoId !==
                    null
                ) {

                    respuesta =
                        await axios.put(
                            `${API_CURSOS}/${cursoEditandoId}`,
                            datos
                        );

                } else {

                    respuesta =
                        await axios.post(
                            API_CURSOS,
                            datos
                        );
                }


                cerrarCursoModal();


                mostrarToast(
                    respuesta.data?.message ||
                    (
                        cursoEditandoId
                            ? 'Curso actualizado correctamente.'
                            : 'Curso creado correctamente.'
                    ),
                    'success'
                );


                paginaActual =
                    1;


                await Promise.all([
                    cargarCursos(),
                    cargarEstadisticas()
                ]);


            } catch (error) {

                if (
                    error?.response?.status ===
                    401
                ) {

                    mostrarToast(
                        'La API requiere una sesión autenticada.',
                        'warning'
                    );

                } else {

                    mostrarToast(
                        obtenerMensajeError(
                            error
                        ),
                        'error'
                    );
                }


                console.error(
                    'Error guardando curso:',
                    error
                );


            } finally {

                submitButton.disabled =
                    false;


                submitText.textContent =
                    textoOriginal;
            }
        }
    );


    // =========================================================
    // ICONO CURSO
    // =========================================================

    function iconoCurso(
        nivel
    ) {

        if (
            nivel ===
            'academia'
        ) {

            return `
                <svg
                    width="20"
                    height="20"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="m2 10 10-5 10 5-10 5Z"/>
                    <path d="M6 12v5c3 2 9 2 12 0v-5"/>
                </svg>
            `;
        }


        return `
            <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
            >
                <path d="M5 4h13a2 2 0 0 1 2 2v14H7a3 3 0 0 1-3-3V5a1 1 0 0 1 1-1Z"/>
                <path d="M7 4v16"/>
                <path d="M10 8h6"/>
                <path d="M10 12h6"/>
            </svg>
        `;
    }


    // =========================================================
    // FILA
    // =========================================================

    function crearFila(
        curso
    ) {

        const fila =
            document.createElement(
                'tr'
            );


        fila.className =
            'curso-row';


        const color =
            /^#[A-Fa-f0-9]{6}$/.test(
                curso.color ?? ''
            )
                ? curso.color
                : '#1B3A6B';


        fila.innerHTML = `

            <td class="whitespace-nowrap px-5 py-4">

                <div class="flex items-center gap-3">

                    <div
                        class="
                            flex
                            h-11
                            w-11
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                        "
                        style="
                            background:${esc(color)}14;
                            color:${esc(color)};
                        "
                    >
                        ${iconoCurso(
                            curso.nivel
                        )}
                    </div>


                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            ${esc(
                                curso.nombre
                            )}
                        </p>

                        <p class="mt-0.5 max-w-[260px] truncate text-xs text-slate-400">
                            ${
                                curso.descripcion
                                    ? esc(
                                        curso.descripcion
                                    )
                                    : 'Sin descripción'
                            }
                        </p>

                    </div>

                </div>

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-600">

                ${esc(
                    curso.codigo
                )}

            </td>


            <td class="whitespace-nowrap px-5 py-4">

                <span
                    class="
                        inline-flex
                        rounded-full
                        bg-blue-50
                        px-2.5
                        py-1
                        text-xs
                        font-semibold
                        text-[#1B3A6B]
                    "
                >
                    ${esc(
                        labelNivel(
                            curso.nivel
                        )
                    )}
                </span>

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                ${esc(
                    labelTipo(
                        curso.tipo
                    )
                )}

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-slate-600">

                ${esc(
                    curso.horas_semanales
                )}
                h

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                ${esc(
                    curso.duracion_minutos
                )}
                min

            </td>


            <td class="whitespace-nowrap px-5 py-4">

                ${
                    esActivo(
                        curso.activo
                    )

                        ? `
                            <span
                                class="
                                    inline-flex
                                    items-center
                                    gap-1.5
                                    rounded-full
                                    bg-emerald-50
                                    px-2.5
                                    py-1
                                    text-xs
                                    font-semibold
                                    text-emerald-700
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Activo
                            </span>
                        `

                        : `
                            <span
                                class="
                                    inline-flex
                                    items-center
                                    gap-1.5
                                    rounded-full
                                    bg-slate-100
                                    px-2.5
                                    py-1
                                    text-xs
                                    font-semibold
                                    text-slate-600
                                "
                            >
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                Inactivo
                            </span>
                        `
                }

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-right">

                <div class="inline-flex items-center gap-2">

                    <button
                        type="button"
                        class="
                            curso-action-btn
                            curso-action-edit
                            editar-curso
                        "
                        data-id="${esc(
                            curso.id
                        )}"
                        title="Editar curso"
                        aria-label="Editar curso"
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
                            curso-action-btn
                            curso-action-delete
                            eliminar-curso
                        "
                        data-id="${esc(
                            curso.id
                        )}"
                        data-nombre="${esc(
                            curso.nombre
                        )}"
                        title="Eliminar curso"
                        aria-label="Eliminar curso"
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
    // ESTADOS DE TABLA
    // =========================================================

    function mostrarCargando() {

        tabla.innerHTML = `

            <tr>
                <td colspan="8" class="px-6 py-16 text-center">

                    <div class="flex flex-col items-center">

                        <div
                            class="
                                h-8
                                w-8
                                animate-spin
                                rounded-full
                                border-2
                                border-slate-200
                                border-t-[#1B3A6B]
                            "
                        ></div>

                        <p class="mt-4 text-sm font-semibold text-slate-500">
                            Cargando cursos...
                        </p>

                    </div>

                </td>
            </tr>
        `;
    }


    function mostrarSinResultados() {

        tabla.innerHTML = `

            <tr>

                <td colspan="8" class="px-6 py-16 text-center">

                    <div class="flex flex-col items-center">

                        <div
                            class="
                                flex
                                h-16
                                w-16
                                items-center
                                justify-center
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
                                <path d="M5 4h13a2 2 0 0 1 2 2v14H7a3 3 0 0 1-3-3V5a1 1 0 0 1 1-1Z"/>
                                <path d="M7 4v16"/>
                            </svg>

                        </div>

                        <h3
                            class="mt-4 font-bold"
                            style="color:#0F2749;"
                        >
                            No se encontraron cursos
                        </h3>

                        <p class="mt-1 text-sm text-slate-400">
                            Cambia la búsqueda o los filtros seleccionados.
                        </p>

                    </div>

                </td>

            </tr>
        `;
    }


    function mostrarAutenticacionRequerida() {

        cursosPagina =
            [];

        totalRegistros =
            0;

        paginaActual =
            1;

        ultimaPagina =
            1;


        tabla.innerHTML = `

            <tr>

                <td colspan="8" class="px-6 py-16 text-center">

                    <div class="mx-auto max-w-md">

                        <div
                            class="
                                mx-auto
                                flex
                                h-14
                                w-14
                                items-center
                                justify-center
                                rounded-2xl
                                bg-amber-50
                                text-amber-700
                            "
                        >
                            <svg
                                class="h-7 w-7"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect x="5" y="11" width="14" height="10" rx="2"/>
                                <path d="M8 11V7a4 4 0 0 1 8 0v4"/>
                            </svg>
                        </div>

                        <h3
                            class="mt-4 font-extrabold"
                            style="color:#0F2749;"
                        >
                            Autenticación requerida
                        </h3>

                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            La API de cursos está protegida con Sanctum.
                            Cuando el backend tenga una sesión autenticada,
                            aquí aparecerán los cursos reales.
                        </p>

                    </div>

                </td>

            </tr>
        `;


        resultadosCursos.textContent =
            '0';

        totalResultadosCursos.textContent =
            '0';

        paginacion.innerHTML =
            '';
    }


    function mostrarErrorListado() {

        tabla.innerHTML = `

            <tr>
                <td colspan="8" class="px-6 py-16 text-center">

                    <h3 class="font-bold text-red-600">
                        No se pudieron cargar los cursos
                    </h3>

                    <p class="mt-2 text-sm text-slate-400">
                        Revisa que Laravel esté ejecutándose y que la API responda correctamente.
                    </p>

                </td>
            </tr>
        `;


        resultadosCursos.textContent =
            '0';

        totalResultadosCursos.textContent =
            '0';

        paginacion.innerHTML =
            '';
    }


    // =========================================================
    // CARGAR CURSOS
    // =========================================================

    async function cargarCursos() {

        if (cargandoCursos) {
            return;
        }


        cargandoCursos =
            true;


        mostrarCargando();


        try {

            const params = {

                page:
                    paginaActual,

                per_page:
                    POR_PAGINA
            };


            const search =
                buscador?.value
                    ?.trim();


            if (search) {
                params.search =
                    search;
            }


            if (
                filtroNivel?.value
            ) {

                params.nivel =
                    filtroNivel.value;
            }


            if (
                filtroTipo?.value
            ) {

                params.tipo =
                    filtroTipo.value;
            }


            if (
                filtroEstado?.value !==
                ''
            ) {

                params.activo =
                    filtroEstado.value;
            }


            const respuesta =
                await axios.get(
                    API_CURSOS,
                    {
                        params
                    }
                );


            const paginador =
                respuesta.data?.data ??
                {};


            cursosPagina =
                Array.isArray(
                    paginador.data
                )
                    ? paginador.data
                    : [];


            paginaActual =
                Number(
                    paginador.current_page ??
                    1
                );


            ultimaPagina =
                Math.max(
                    1,
                    Number(
                        paginador.last_page ??
                        1
                    )
                );


            totalRegistros =
                Number(
                    paginador.total ??
                    0
                );


            renderizarCursos();


        } catch (error) {

            if (
                error?.response?.status ===
                401
            ) {

                mostrarAutenticacionRequerida();

            } else {

                mostrarErrorListado();
            }


            console.error(
                'Error cargando cursos:',
                error
            );


        } finally {

            cargandoCursos =
                false;
        }
    }


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    async function cargarEstadisticas() {

        try {

            const respuesta =
                await axios.get(
                    API_ESTADISTICAS
                );


            const data =
                respuesta.data?.data ??
                {};


            totalCursos.textContent =
                Number(
                    data.total ??
                    0
                );


            cursosActivos.textContent =
                Number(
                    data.activos ??
                    0
                );


            cursosInactivos.textContent =
                Math.max(
                    0,
                    Number(
                        data.total ??
                        0
                    ) -
                    Number(
                        data.activos ??
                        0
                    )
                );


        } catch (error) {

            totalCursos.textContent =
                '0';

            cursosActivos.textContent =
                '0';

            cursosInactivos.textContent =
                '0';


            if (
                error?.response?.status !==
                401
            ) {

                console.error(
                    'Error cargando estadísticas:',
                    error
                );
            }
        }
    }


    // =========================================================
    // RENDER
    // =========================================================

    function renderizarCursos() {

        tabla.innerHTML =
            '';


        if (
            !cursosPagina.length
        ) {

            mostrarSinResultados();

        } else {

            cursosPagina.forEach(
                curso => {

                    tabla.appendChild(
                        crearFila(
                            curso
                        )
                    );
                }
            );
        }


        resultadosCursos.textContent =
            cursosPagina.length;


        totalResultadosCursos.textContent =
            totalRegistros;


        renderizarPaginacion();
    }


    // =========================================================
    // PAGINACIÓN
    // =========================================================

    function obtenerPaginasVisibles(
        actual,
        total
    ) {

        if (
            total <=
            7
        ) {

            return Array.from(
                {
                    length:
                        total
                },
                (
                    _,
                    index
                ) =>
                    index + 1
            );
        }


        const paginas =
            [1];


        if (
            actual >
            4
        ) {

            paginas.push(
                '...'
            );
        }


        const inicio =
            Math.max(
                2,
                actual - 1
            );


        const fin =
            Math.min(
                total - 1,
                actual + 1
            );


        for (
            let i = inicio;
            i <= fin;
            i++
        ) {

            paginas.push(
                i
            );
        }


        if (
            actual <
            total - 3
        ) {

            paginas.push(
                '...'
            );
        }


        paginas.push(
            total
        );


        return paginas;
    }


    function renderizarPaginacion() {

        if (!paginacion) {
            return;
        }


        if (
            ultimaPagina <=
            1
        ) {

            paginacion.innerHTML =
                '';

            return;
        }


        let html = `

            <button
                type="button"
                class="curso-pagination-btn"
                data-pagina="${
                    paginaActual - 1
                }"
                ${
                    paginaActual <= 1
                        ? 'disabled'
                        : ''
                }
            >
                Anterior
            </button>
        `;


        const paginas =
            obtenerPaginasVisibles(
                paginaActual,
                ultimaPagina
            );


        paginas.forEach(
            pagina => {

                if (
                    pagina ===
                    '...'
                ) {

                    html += `
                        <span class="inline-flex h-[38px] items-center px-1 text-sm text-slate-400">
                            ...
                        </span>
                    `;

                    return;
                }


                html += `

                    <button
                        type="button"
                        class="
                            curso-pagination-btn
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
        );


        html += `

            <button
                type="button"
                class="curso-pagination-btn"
                data-pagina="${
                    paginaActual + 1
                }"
                ${
                    paginaActual >=
                    ultimaPagina
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
        async event => {

            const boton =
                event.target.closest(
                    '[data-pagina]'
                );


            if (
                !boton ||
                boton.disabled
            ) {
                return;
            }


            const nuevaPagina =
                Number(
                    boton.dataset.pagina
                );


            if (
                nuevaPagina < 1 ||
                nuevaPagina > ultimaPagina ||
                nuevaPagina === paginaActual
            ) {
                return;
            }


            paginaActual =
                nuevaPagina;


            await cargarCursos();


            tabla
                .closest(
                    '.curso-card'
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
    // FILTROS
    // =========================================================

    buscador?.addEventListener(
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

                        cargarCursos();

                    },
                    350
                );
        }
    );


    function cambioFiltro(
        select
    ) {

        actualizarCustomSelect(
            select
        );


        paginaActual =
            1;


        cargarCursos();
    }


    filtroNivel?.addEventListener(
        'change',
        () => cambioFiltro(
            filtroNivel
        )
    );


    filtroTipo?.addEventListener(
        'change',
        () => cambioFiltro(
            filtroTipo
        )
    );


    filtroEstado?.addEventListener(
        'change',
        () => cambioFiltro(
            filtroEstado
        )
    );


    // =========================================================
    // EDITAR / ELIMINAR
    // =========================================================

    tabla.addEventListener(
        'click',
        async event => {

            const editar =
                event.target.closest(
                    '.editar-curso'
                );


            if (editar) {

                try {

                    const respuesta =
                        await axios.get(
                            `${API_CURSOS}/${editar.dataset.id}`
                        );


                    const curso =
                        respuesta.data?.data;


                    if (!curso) {
                        return;
                    }


                    cursoEditandoId =
                        curso.id;


                    codigoInput.value =
                        curso.codigo ??
                        '';


                    codigoInput.readOnly =
                        true;


                    codigoInput.classList.add(
                        'bg-slate-100',
                        'cursor-not-allowed'
                    );


                    nombreInput.value =
                        curso.nombre ??
                        '';


                    descripcionInput.value =
                        curso.descripcion ??
                        '';


                    nivelInput.value =
                        curso.nivel ??
                        'todos';


                    tipoInput.value =
                        curso.tipo ??
                        'obligatorio';


                    horasInput.value =
                        curso.horas_semanales ??
                        4;


                    duracionInput.value =
                        String(
                            curso.duracion_minutos ??
                            60
                        );


                    colorInput.value =
                        curso.color ??
                        '#1B3A6B';


                    estadoInput.value =
                        esActivo(
                            curso.activo
                        )
                            ? '1'
                            : '0';


                    modalTitle.textContent =
                        'Editar curso';


                    submitText.textContent =
                        'Guardar cambios';


                    actualizarColorPreview();


                    actualizarTodosCustomSelect();


                    abrirCursoModal(
                        true
                    );


                } catch (error) {

                    mostrarToast(
                        obtenerMensajeError(
                            error
                        ),
                        'error'
                    );


                    console.error(
                        'Error obteniendo curso:',
                        error
                    );
                }


                return;
            }


            const eliminar =
                event.target.closest(
                    '.eliminar-curso'
                );


            if (eliminar) {

                abrirEliminarModal(
                    eliminar.dataset.nombre,
                    eliminar.dataset.id
                );
            }
        }
    );


    // =========================================================
    // ELIMINAR
    // =========================================================

    function abrirEliminarModal(
        nombre,
        id
    ) {

        cursoAEliminarId =
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

        cursoAEliminarId =
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


    overlayEliminar?.addEventListener(
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
        async () => {

            if (
                cursoAEliminarId ===
                null
            ) {
                return;
            }


            const id =
                cursoAEliminarId;


            const textoOriginal =
                confirmEliminar.innerHTML;


            try {

                confirmEliminar.disabled =
                    true;


                confirmEliminar.innerHTML =
                    'Eliminando...';


                const respuesta =
                    await axios.delete(
                        `${API_CURSOS}/${id}`
                    );


                cerrarEliminarModal();


                mostrarToast(
                    respuesta.data?.message ||
                    'Curso eliminado correctamente.',
                    'success'
                );


                if (
                    cursosPagina.length ===
                    1 &&
                    paginaActual >
                    1
                ) {

                    paginaActual--;
                }


                await Promise.all([
                    cargarCursos(),
                    cargarEstadisticas()
                ]);


            } catch (error) {

                if (
                    error?.response?.status ===
                    401
                ) {

                    mostrarToast(
                        'La API requiere una sesión autenticada.',
                        'warning'
                    );

                } else {

                    mostrarToast(
                        obtenerMensajeError(
                            error
                        ),
                        'error'
                    );
                }


                console.error(
                    'Error eliminando curso:',
                    error
                );


            } finally {

                confirmEliminar.disabled =
                    false;


                confirmEliminar.innerHTML =
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
                event.key !==
                'Escape'
            ) {
                return;
            }


            cerrarTodosSelects();


            if (
                !modal.classList.contains(
                    'hidden'
                )
            ) {

                cerrarCursoModal();
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
    // INICIO
    // =========================================================

    prepararNuevoCurso();


    Promise.all([
        cargarCursos(),
        cargarEstadisticas()
    ]);

});