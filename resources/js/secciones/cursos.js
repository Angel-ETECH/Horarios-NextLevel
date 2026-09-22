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
    // STORAGE
    // =========================================================

    const CURSOS_KEY =
        'nextlevel_cursos';


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

    const observacionesInput =
        document.getElementById('curso-observaciones');

    const modalTitle =
        document.getElementById('curso-modal-title');

    const submitText =
        document.getElementById('curso-submit-text');

    const colorPreview =
        document.getElementById('curso-color-preview');


    // =========================================================
    // FILTROS
    // =========================================================

    const buscador =
        document.getElementById('buscar-curso');

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
    // MODAL ELIMINAR
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

    let cursoEditandoId =
        null;

    let cursoAEliminarId =
        null;

    let paginaActual =
        1;

    const POR_PAGINA =
        6;


    // =========================================================
    // STORAGE HELPERS
    // =========================================================

    function leerCursos() {

        try {

            const datos =
                JSON.parse(
                    localStorage.getItem(
                        CURSOS_KEY
                    ) || '[]'
                );


            return Array.isArray(
                datos
            )
                ? datos
                : [];

        } catch (error) {

            console.error(
                'Error leyendo cursos:',
                error
            );


            return [];
        }
    }


    function guardarCursos(
        cursos
    ) {

        localStorage.setItem(
            CURSOS_KEY,
            JSON.stringify(
                cursos
            )
        );
    }


    // =========================================================
    // UTILIDADES
    // =========================================================

    function normalizarTexto(
        valor
    ) {

        return String(
            valor ??
            ''
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
            valor ??
            ''
        )
            .replace(
                /&/g,
                '&amp;'
            )
            .replace(
                /</g,
                '&lt;'
            )
            .replace(
                />/g,
                '&gt;'
            )
            .replace(
                /"/g,
                '&quot;'
            )
            .replace(
                /'/g,
                '&#039;'
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
                    <rect
                        x="3"
                        y="4"
                        width="18"
                        height="16"
                        rx="2"
                    />

                    <path d="M7 8h10"/>
                    <path d="M7 12h6"/>
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


        const tipoIcono =
            wrapper.dataset.icon ||
            'tipo';


        const usaBusqueda =
            wrapper.dataset.search !==
            'false';


        wrapper.innerHTML = `

            <button
                type="button"
                class="curso-select-trigger"
                aria-expanded="false"
            >

                <span class="curso-select-icon">

                    ${iconoSelect(
                        tipoIcono
                    )}

                </span>


                <span class="curso-select-content">

                    <span class="curso-select-label">

                        ${esc(
                            label
                        )}

                    </span>


                    <span class="curso-select-text">

                        ${esc(
                            placeholder
                        )}

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

                ${
                    usaBusqueda

                        ? `
                            <div class="curso-select-search-wrap">

                                <svg
                                    class="curso-select-search-icon"
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
                                    class="curso-select-search"
                                    placeholder="Buscar..."
                                    autocomplete="off"
                                >

                            </div>
                        `

                        : ''
                }


                <div
                    class="curso-select-options"
                ></div>

            </div>
        `;


        wrapper.dataset.ready =
            'true';


        const trigger =
            wrapper.querySelector(
                '.curso-select-trigger'
            );


        const search =
            wrapper.querySelector(
                '.curso-select-search'
            );


        trigger.addEventListener(
            'click',
            () => {

                if (
                    trigger.disabled
                ) {
                    return;
                }


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

                    if (search) {

                        search.value =
                            '';
                    }


                    renderOpcionesSelect(
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

                renderOpcionesSelect(
                    wrapper,
                    search.value
                );
            }
        );


        search?.addEventListener(
            'keydown',
            event => {

                if (
                    event.key ===
                    'Escape'
                ) {

                    cerrarSelect(
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
        wrapper,
        busqueda = ''
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


        if (!opciones.length) {

            contenedor.innerHTML = `
                <div class="curso-select-empty">
                    No hay opciones disponibles
                </div>
            `;


            return;
        }


        contenedor.innerHTML =
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


        const trigger =
            wrapper.querySelector(
                '.curso-select-trigger'
            );


        const texto =
            wrapper.querySelector(
                '.curso-select-text'
            );


        if (
            !trigger ||
            !texto
        ) {
            return;
        }


        trigger.disabled =
            select.disabled;


        const option =
            select.options[
                select.selectedIndex
            ];


        texto.textContent =
            option?.textContent ||
            wrapper.dataset.placeholder ||
            'Seleccionar';


        renderOpcionesSelect(
            wrapper
        );
    }


    function actualizarTodosCustomSelect() {

        [
            filtroEstado,
            nivelInput,
            tipoInput,
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
            colorInput.value;


        colorPreview.style.color =
            colorInput.value;
    }


    colorInput?.addEventListener(
        'input',
        actualizarColorPreview
    );


    // =========================================================
    // MODAL
    // =========================================================

    function prepararNuevoCurso() {

        cursoEditandoId =
            null;


        formulario.reset();


        nivelInput.value =
            'todos';


        tipoInput.value =
            'obligatorio';


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
            () =>
                codigoInput.focus(),
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


        formulario.reset();


        cursoEditandoId =
            null;


        cerrarTodosSelects();


        prepararNuevoCurso();
    }


    abrirModal?.addEventListener(
        'click',
        () =>
            abrirCursoModal(
                false
            )
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
    // GUARDAR
    // =========================================================

    formulario.addEventListener(
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


            const descripcion =
                descripcionInput.value
                    .trim();


            const nivel =
                nivelInput.value;


            const tipo =
                tipoInput.value;


            const horas =
                Number(
                    horasInput.value
                );


            const duracion =
                Number(
                    duracionInput.value ||
                    60
                );


            const color =
                colorInput.value ||
                '#1B3A6B';


            const activo =
                estadoInput.value ===
                '1';


            const observaciones =
                observacionesInput.value
                    .trim();


            if (
                !codigo ||
                !nombre ||
                !nivel ||
                !tipo ||
                !horas
            ) {

                alert(
                    'Completa todos los campos obligatorios.'
                );

                return;
            }


            if (
                horas < 1 ||
                horas > 20
            ) {

                alert(
                    'Las horas semanales deben estar entre 1 y 20.'
                );

                return;
            }


            if (
                duracion < 30 ||
                duracion > 180
            ) {

                alert(
                    'La duración por clase debe estar entre 30 y 180 minutos.'
                );

                return;
            }


            let cursos =
                leerCursos();


            const duplicado =
                cursos.find(
                    curso =>

                        normalizarTexto(
                            curso.codigo
                        ) ===
                        normalizarTexto(
                            codigo
                        ) &&

                        String(
                            curso.id
                        ) !==
                        String(
                            cursoEditandoId
                        )
                );


            if (duplicado) {

                alert(
                    'Ya existe un curso con ese código.'
                );


                codigoInput.focus();


                return;
            }


            const cursoData = {

                codigo,

                nombre,

                descripcion,

                nivel,

                tipo,

                horas_semanales:
                    horas,

                duracion_minutos:
                    duracion,

                color,

                activo,

                observaciones
            };


            // =================================================
            // EDITAR
            // =================================================

            if (
                cursoEditandoId !==
                null
            ) {

                const index =
                    cursos.findIndex(
                        curso =>
                            String(
                                curso.id
                            ) ===
                            String(
                                cursoEditandoId
                            )
                    );


                if (
                    index !==
                    -1
                ) {

                    cursos[index] = {

                        ...cursos[index],

                        ...cursoData
                    };
                }


                guardarCursos(
                    cursos
                );


                cerrarCursoModal();


                paginaActual =
                    1;


                renderizarCursos();


                return;
            }


            // =================================================
            // NUEVO
            // =================================================

            cursoData.id =
                Date.now();


            cursos.push(
                cursoData
            );


            guardarCursos(
                cursos
            );


            cerrarCursoModal();


            const filtrados =
                obtenerCursosFiltrados(
                    cursos
                );


            paginaActual =
                Math.max(
                    1,

                    Math.ceil(
                        filtrados.length /
                        POR_PAGINA
                    )
                );


            renderizarCursos();
        }
    );


    // =========================================================
    // FILTRAR
    // =========================================================

    function obtenerCursosFiltrados(
        cursos
    ) {

        const texto =
            normalizarTexto(
                buscador?.value
            );


        const estado =
            filtroEstado?.value ??
            '';


        return cursos.filter(
            curso => {

                const textoCurso =
                    normalizarTexto(
                        `
                            ${
                                curso.nombre ??
                                ''
                            }

                            ${
                                curso.codigo ??
                                ''
                            }

                            ${
                                curso.descripcion ??
                                ''
                            }
                        `
                    );


                const coincideTexto =
                    !texto ||
                    textoCurso.includes(
                        texto
                    );


                const coincideEstado =
                    estado ===
                    '' ||

                    (
                        estado ===
                        '1' &&
                        curso.activo ===
                        true
                    ) ||

                    (
                        estado ===
                        '0' &&
                        curso.activo ===
                        false
                    );


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
        cursos
    ) {

        const activos =
            cursos.filter(
                curso =>
                    curso.activo ===
                    true
            ).length;


        const inactivos =
            cursos.filter(
                curso =>
                    curso.activo !==
                    true
            ).length;


        if (totalCursos) {

            totalCursos.textContent =
                cursos.length;
        }


        if (cursosActivos) {

            cursosActivos.textContent =
                activos;
        }


        if (cursosInactivos) {

            cursosInactivos.textContent =
                inactivos;
        }
    }


    // =========================================================
    // LABEL NIVEL
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
            'Sin nivel'
        );
    }


    // =========================================================
    // LABEL TIPO
    // =========================================================

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
            'Sin tipo'
        );
    }


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
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <path d="m2 10 10-5 10 5-10 5Z"/>
                    <path d="M6 12v5c3 2 9 2 12 0v-5"/>
                </svg>
            `;
        }


        if (
            nivel ===
            'primaria'
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
                    <path d="M5 4h13a2 2 0 0 1 2 2v14H7a3 3 0 0 1-3-3V5a1 1 0 0 1 1-1Z"/>
                    <path d="M7 4v16"/>
                    <path d="M10 8h6"/>
                </svg>
            `;
        }


        if (
            nivel ===
            'secundaria'
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
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>
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
            curso.color ||
            '#1B3A6B';


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
                            background:
                                ${esc(
                                    color
                                )}14;

                            color:
                                ${esc(
                                    color
                                )};
                        "
                    >

                        ${iconoCurso(
                            curso.nivel
                        )}

                    </div>


                    <div>

                        <p
                            class="
                                text-sm
                                font-bold
                                text-slate-800
                            "
                        >
                            ${esc(
                                curso.nombre
                            )}
                        </p>


                        <p
                            class="
                                mt-0.5
                                text-xs
                                text-slate-400
                            "
                        >
                            ${esc(
                                curso.codigo
                            )}
                        </p>

                    </div>

                </div>

            </td>


            <td
                class="
                    whitespace-nowrap
                    px-5
                    py-4
                    text-sm
                    text-slate-600
                "
            >
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


            <td
                class="
                    whitespace-nowrap
                    px-5
                    py-4
                    text-sm
                    text-slate-600
                "
            >
                ${esc(
                    labelTipo(
                        curso.tipo
                    )
                )}
            </td>


            <td
                class="
                    whitespace-nowrap
                    px-5
                    py-4
                    text-sm
                    font-medium
                    text-slate-600
                "
            >
                ${esc(
                    curso.horas_semanales
                )}
                h
            </td>


            <td class="whitespace-nowrap px-5 py-4">

                ${
                    curso.activo

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
                                <span
                                    class="
                                        h-1.5
                                        w-1.5
                                        rounded-full
                                        bg-emerald-500
                                    "
                                ></span>

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
                                <span
                                    class="
                                        h-1.5
                                        w-1.5
                                        rounded-full
                                        bg-slate-400
                                    "
                                ></span>

                                Inactivo
                            </span>
                        `
                }

            </td>


            <td
                class="
                    whitespace-nowrap
                    px-5
                    py-4
                    text-right
                "
            >

                <div
                    class="
                        inline-flex
                        items-center
                        gap-2
                    "
                >

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
                class="curso-pagination-btn"
                data-pagina="${
                    paginaActual -
                    1
                }"
                ${
                    paginaActual ===
                    1

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


        html += `

            <button
                type="button"
                class="curso-pagination-btn"
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


            paginaActual =
                Number(
                    boton.dataset.pagina
                );


            renderizarCursos();


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
    // RENDER
    // =========================================================

    function renderizarCursos() {

        const cursos =
            leerCursos();


        actualizarEstadisticas(
            cursos
        );


        const filtrados =
            obtenerCursosFiltrados(
                cursos
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


        const fin =
            inicio +
            POR_PAGINA;


        const pagina =
            filtrados.slice(
                inicio,
                fin
            );


        tabla.innerHTML =
            '';


        // =====================================================
        // VACÍO
        // =====================================================

        if (!filtrados.length) {

            tabla.innerHTML = `

                <tr>

                    <td
                        colspan="7"
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
                                class="
                                    mt-4
                                    font-bold
                                "
                                style="
                                    color:
                                        #0F2749;
                                "
                            >
                                No se encontraron cursos
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
                curso => {

                    tabla.appendChild(
                        crearFila(
                            curso
                        )
                    );
                }
            );
        }


        // =====================================================
        // CONTADORES
        // =====================================================

        if (resultadosCursos) {

            resultadosCursos.textContent =
                pagina.length;
        }


        if (totalResultadosCursos) {

            totalResultadosCursos.textContent =
                filtrados.length;
        }


        renderizarPaginacion(
            filtrados.length
        );
    }


    // =========================================================
    // EDITAR / ELIMINAR
    // =========================================================

    tabla.addEventListener(
        'click',
        event => {

            // =================================================
            // EDITAR
            // =================================================

            const editar =
                event.target.closest(
                    '.editar-curso'
                );


            if (editar) {

                const curso =
                    leerCursos()
                        .find(
                            item =>
                                String(
                                    item.id
                                ) ===
                                String(
                                    editar.dataset.id
                                )
                        );


                if (!curso) {
                    return;
                }


                cursoEditandoId =
                    curso.id;


                codigoInput.value =
                    curso.codigo ??
                    '';


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
                    '';


                duracionInput.value =
                    curso.duracion_minutos ??
                    60;


                colorInput.value =
                    curso.color ??
                    '#1B3A6B';


                estadoInput.value =
                    curso.activo
                        ? '1'
                        : '0';


                observacionesInput.value =
                    curso.observaciones ??
                    '';


                modalTitle.textContent =
                    'Editar curso';


                submitText.textContent =
                    'Guardar cambios';


                actualizarColorPreview();


                actualizarTodosCustomSelect();


                abrirCursoModal(
                    true
                );


                return;
            }


            // =================================================
            // ELIMINAR
            // =================================================

            const eliminar =
                event.target.closest(
                    '.eliminar-curso'
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
        () => {

            if (
                cursoAEliminarId ===
                null
            ) {
                return;
            }


            const cursos =
                leerCursos()
                    .filter(
                        curso =>
                            String(
                                curso.id
                            ) !==
                            String(
                                cursoAEliminarId
                            )
                    );


            guardarCursos(
                cursos
            );


            cerrarEliminarModal();


            renderizarCursos();
        }
    );


    // =========================================================
    // FILTROS
    // =========================================================

    function cambioFiltro() {

        paginaActual =
            1;


        renderizarCursos();
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
    // SELECTS DEL MODAL
    // =========================================================

    [
        nivelInput,
        tipoInput,
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
    // STORAGE
    // =========================================================

    window.addEventListener(
        'storage',
        event => {

            if (
                event.key ===
                CURSOS_KEY
            ) {

                paginaActual =
                    1;


                renderizarCursos();
            }
        }
    );


    window.addEventListener(
        'focus',
        () => {

            renderizarCursos();

            actualizarTodosCustomSelect();
        }
    );


    // =========================================================
    // INICIO
    // =========================================================

    prepararNuevoCurso();

    renderizarCursos();

});