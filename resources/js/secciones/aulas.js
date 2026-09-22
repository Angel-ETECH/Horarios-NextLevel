document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // GUARD
    // =========================================================

    const modal =
        document.getElementById('aula-modal');

    const formulario =
        document.getElementById('aula-form');

    const tabla =
        document.getElementById('aulas-body');


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

    const AULAS_KEY =
        'nextlevel_aulas';


    // =========================================================
    // ELEMENTOS MODAL
    // =========================================================

    const abrirModal =
        document.getElementById('open-aula-modal');

    const cerrarModalBtn =
        document.getElementById('close-aula-modal');

    const cancelarModal =
        document.getElementById('cancel-aula-modal');

    const overlay =
        document.getElementById('aula-modal-overlay');


    const codigoInput =
        document.getElementById('aula-codigo');

    const nombreInput =
        document.getElementById('aula-nombre');

    const capacidadInput =
        document.getElementById('aula-capacidad');

    const tipoInput =
        document.getElementById('aula-tipo');

    const nivelInput =
        document.getElementById('aula-nivel');

    const equipamientoInput =
        document.getElementById('aula-equipamiento');

    const estadoInput =
        document.getElementById('aula-estado');

    const observacionesInput =
        document.getElementById('aula-observaciones');

    const modalTitle =
        document.getElementById('aula-modal-title');

    const submitText =
        document.getElementById('aula-submit-text');


    // =========================================================
    // FILTROS
    // =========================================================

    const buscador =
        document.getElementById('buscar-aula');

    const filtroEstado =
        document.getElementById('filtro-aula-estado');

    const filtroTipo =
        document.getElementById('filtro-aula-tipo');

    const filtroNivel =
        document.getElementById('filtro-aula-nivel');


    // =========================================================
    // CONTADORES
    // =========================================================

    const totalAulas =
        document.getElementById('total-aulas');

    const aulasActivas =
        document.getElementById('aulas-activas');

    const aulasInactivas =
        document.getElementById('aulas-inactivas');

    const resultadosAulas =
        document.getElementById('resultados-aulas');

    const resultadosFooter =
        document.getElementById('resultados-aulas-footer');

    const totalResultadosFooter =
        document.getElementById('total-resultados-aulas');

    const paginacion =
        document.getElementById('aulas-paginacion');


    // =========================================================
    // MODAL ELIMINAR
    // =========================================================

    const eliminarModal =
        document.getElementById('delete-aula-modal');

    const overlayEliminar =
        document.getElementById('delete-aula-overlay');

    const closeEliminar =
        document.getElementById('close-delete-aula-modal');

    const cancelEliminar =
        document.getElementById('cancel-delete-aula');

    const confirmEliminar =
        document.getElementById('confirm-delete-aula');

    const eliminarNombre =
        document.getElementById('delete-aula-name');


    // =========================================================
    // ESTADO
    // =========================================================

    let aulaEditandoId =
        null;

    let aulaAEliminarId =
        null;

    let paginaActual =
        1;

    const POR_PAGINA =
        6;


    // =========================================================
    // STORAGE HELPERS
    // =========================================================

    function leerAulas() {

        try {

            const datos =
                JSON.parse(
                    localStorage.getItem(
                        AULAS_KEY
                    ) || '[]'
                );


            return Array.isArray(
                datos
            )
                ? datos
                : [];

        } catch (error) {

            console.error(
                'Error leyendo aulas:',
                error
            );


            return [];
        }
    }


    function guardarAulas(
        aulas
    ) {

        localStorage.setItem(
            AULAS_KEY,
            JSON.stringify(
                aulas
            )
        );
    }


    // =========================================================
    // UTILS
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
    // ICONOS
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
                    <path d="M7 8h3"/>
                    <path d="M14 8h3"/>
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
            `
        };


        return (
            iconos[tipo] ||
            iconos.tipo
        );
    }


    function iconoTipoAula(
        tipo
    ) {

        if (
            tipo ===
            'taller'
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
                    <path d="M14.7 6.3a4 4 0 0 0-5 5L4 17l3 3 5.7-5.7a4 4 0 0 0 5-5l-2.4 2.4-3-3z"/>
                </svg>
            `;
        }


        if (
            tipo ===
            'auditorio'
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
                    <path d="M4 20V8h16v12"/>
                    <path d="M2 20h20"/>
                    <path d="M8 12h8"/>
                    <path d="M8 16h8"/>
                </svg>
            `;
        }


        if (
            tipo ===
            'virtual'
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
                    <rect x="3" y="4" width="18" height="13" rx="2"/>
                    <path d="M8 21h8"/>
                    <path d="M12 17v4"/>
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
                <path d="M4 21V5h16v16"/>
                <path d="M2 21h20"/>
                <path d="M8 9h2"/>
                <path d="M14 9h2"/>
                <path d="M9 21v-4h6v4"/>
            </svg>
        `;
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
            `[data-aula-select="${select.id}"]`
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
                wrapper.dataset.aulaSelect
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

        const usarBusqueda =
            wrapper.dataset.search !==
            'false';


        wrapper.innerHTML = `

            <button
                type="button"
                class="aula-select-trigger"
                aria-expanded="false"
            >

                <span class="aula-select-icon">
                    ${iconoSelect(
                        tipoIcono
                    )}
                </span>

                <span class="aula-select-content">

                    <span class="aula-select-label">
                        ${esc(
                            label
                        )}
                    </span>

                    <span class="aula-select-text">
                        ${esc(
                            placeholder
                        )}
                    </span>

                </span>

                <svg
                    class="aula-select-arrow"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path d="m6 9 6 6 6-6"/>
                </svg>

            </button>


            <div class="aula-select-menu">

                ${
                    usarBusqueda

                        ? `
                            <div class="aula-select-search-wrap">

                                <svg
                                    class="aula-select-search-icon"
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
                                    class="aula-select-search"
                                    placeholder="Buscar..."
                                    autocomplete="off"
                                >

                            </div>
                        `

                        : ''
                }

                <div class="aula-select-options"></div>

            </div>
        `;


        wrapper.dataset.ready =
            'true';


        const trigger =
            wrapper.querySelector(
                '.aula-select-trigger'
            );

        const search =
            wrapper.querySelector(
                '.aula-select-search'
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
                '.aula-select-trigger'
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
                '[data-aula-select]'
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
                wrapper.dataset.aulaSelect
            );


        const optionsContainer =
            wrapper.querySelector(
                '.aula-select-options'
            );


        if (
            !select ||
            !optionsContainer
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

            optionsContainer.innerHTML = `
                <div class="aula-select-empty">
                    No hay opciones disponibles
                </div>
            `;


            return;
        }


        optionsContainer.innerHTML =
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
                                    aula-select-option
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

                                <span class="aula-option-icon">
                                    ${iconoSelect(
                                        wrapper.dataset.icon
                                    )}
                                </span>

                                <span class="aula-option-text">
                                    ${esc(
                                        option.textContent
                                    )}
                                </span>

                                <svg
                                    class="aula-option-check"
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


        optionsContainer
            .querySelectorAll(
                '.aula-select-option'
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
                '.aula-select-trigger'
            );

        const text =
            wrapper.querySelector(
                '.aula-select-text'
            );


        if (
            !trigger ||
            !text
        ) {
            return;
        }


        trigger.disabled =
            select.disabled;


        const option =
            select.options[
                select.selectedIndex
            ];


        text.textContent =
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
            filtroTipo,
            filtroNivel,
            tipoInput,
            nivelInput,
            estadoInput
        ]
            .filter(Boolean)
            .forEach(
                actualizarCustomSelect
            );
    }


    document
        .querySelectorAll(
            '[data-aula-select]'
        )
        .forEach(
            construirCustomSelect
        );


    document.addEventListener(
        'click',
        event => {

            if (
                !event.target.closest(
                    '[data-aula-select]'
                )
            ) {

                cerrarTodosSelects();
            }
        }
    );


    // =========================================================
    // MODAL AULA
    // =========================================================

    function prepararNuevo() {

        aulaEditandoId =
            null;


        formulario.reset();


        nivelInput.value =
            'todos';

        estadoInput.value =
            '1';


        modalTitle.textContent =
            'Agregar aula';


        submitText.textContent =
            'Guardar aula';


        actualizarTodosCustomSelect();
    }


    function abrirAulaModal(
        editar = false
    ) {

        if (!editar) {

            prepararNuevo();
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


    function cerrarAulaModal() {

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


        aulaEditandoId =
            null;


        cerrarTodosSelects();


        actualizarTodosCustomSelect();
    }


    abrirModal?.addEventListener(
        'click',
        () =>
            abrirAulaModal(
                false
            )
    );


    cerrarModalBtn?.addEventListener(
        'click',
        cerrarAulaModal
    );


    cancelarModal?.addEventListener(
        'click',
        cerrarAulaModal
    );


    overlay?.addEventListener(
        'click',
        cerrarAulaModal
    );


    // =========================================================
    // GUARDAR / EDITAR
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


            const capacidad =
                Number(
                    capacidadInput.value
                );


            const tipo =
                tipoInput.value;


            const nivel =
                nivelInput.value ||
                'todos';


            const equipamiento =
                equipamientoInput.value
                    .trim();


            const activo =
                estadoInput.value ===
                '1';


            const observaciones =
                observacionesInput.value
                    .trim();


            if (
                !codigo ||
                !nombre ||
                !capacidad ||
                !tipo
            ) {

                alert(
                    'Completa todos los campos obligatorios.'
                );

                return;
            }


            if (
                capacidad <=
                0
            ) {

                alert(
                    'La capacidad debe ser mayor a 0.'
                );

                return;
            }


            let aulas =
                leerAulas();


            const duplicado =
                aulas.find(
                    aula =>

                        normalizarTexto(
                            aula.codigo
                        ) ===
                        normalizarTexto(
                            codigo
                        ) &&

                        String(
                            aula.id
                        ) !==
                        String(
                            aulaEditandoId
                        )
                );


            if (duplicado) {

                alert(
                    'Ya existe un aula con ese código.'
                );

                codigoInput.focus();

                return;
            }


            const aulaData = {

                codigo,
                nombre,
                capacidad,
                tipo,
                nivel,
                equipamiento,
                activo,
                observaciones
            };


            if (
                aulaEditandoId !==
                null
            ) {

                const index =
                    aulas.findIndex(
                        aula =>
                            String(
                                aula.id
                            ) ===
                            String(
                                aulaEditandoId
                            )
                    );


                if (
                    index !==
                    -1
                ) {

                    aulas[index] = {

                        ...aulas[index],

                        ...aulaData
                    };
                }


                guardarAulas(
                    aulas
                );


                cerrarAulaModal();


                paginaActual =
                    1;


                renderizarAulas();


                return;
            }


            aulaData.id =
                Date.now();


            aulas.push(
                aulaData
            );


            guardarAulas(
                aulas
            );


            cerrarAulaModal();


            /*
             * Mostramos la última página para que
             * el aula recién creada sea visible.
             */
            const filtradas =
                obtenerAulasFiltradas(
                    aulas
                );


            paginaActual =
                Math.max(
                    1,

                    Math.ceil(
                        filtradas.length /
                        POR_PAGINA
                    )
                );


            renderizarAulas();
        }
    );


    // =========================================================
    // FILTRAR
    // =========================================================

    function obtenerAulasFiltradas(
        aulas
    ) {

        const texto =
            normalizarTexto(
                buscador?.value
            );


        const estado =
            filtroEstado?.value ??
            '';


        const tipo =
            filtroTipo?.value ??
            '';


        const nivel =
            filtroNivel?.value ??
            '';


        return aulas.filter(
            aula => {

                const textoAula =
                    normalizarTexto(
                        `${
                            aula.nombre ??
                            ''
                        } ${
                            aula.codigo ??
                            ''
                        }`
                    );


                const coincideTexto =
                    !texto ||
                    textoAula.includes(
                        texto
                    );


                const coincideEstado =
                    estado ===
                    '' ||

                    (
                        estado ===
                        '1' &&
                        aula.activo ===
                        true
                    ) ||

                    (
                        estado ===
                        '0' &&
                        aula.activo ===
                        false
                    );


                const coincideTipo =
                    tipo ===
                    '' ||
                    String(
                        aula.tipo
                    ) ===
                    String(
                        tipo
                    );


                const coincideNivel =
                    nivel ===
                    '' ||
                    String(
                        aula.nivel ??
                        'todos'
                    ) ===
                    String(
                        nivel
                    );


                return (
                    coincideTexto &&
                    coincideEstado &&
                    coincideTipo &&
                    coincideNivel
                );
            }
        );
    }


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    function actualizarEstadisticas(
        aulas
    ) {

        const activas =
            aulas.filter(
                aula =>
                    aula.activo ===
                    true
            ).length;


        const inactivas =
            aulas.filter(
                aula =>
                    aula.activo !==
                    true
            ).length;


        if (totalAulas) {

            totalAulas.textContent =
                aulas.length;
        }


        if (aulasActivas) {

            aulasActivas.textContent =
                activas;
        }


        if (aulasInactivas) {

            aulasInactivas.textContent =
                inactivas;
        }
    }


    // =========================================================
    // LABELS
    // =========================================================

    function labelTipo(
        tipo
    ) {

        const labels = {

            aula_normal:
                'Aula normal',

            taller:
                'Taller',

            auditorio:
                'Auditorio',

            virtual:
                'Virtual'
        };


        return (
            labels[tipo] ||
            tipo ||
            'Sin tipo'
        );
    }


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
            'Todos'
        );
    }


    // =========================================================
    // FILA
    // =========================================================

    function crearFila(
        aula
    ) {

        const fila =
            document.createElement(
                'tr'
            );


        fila.className =
            'aula-row transition hover:bg-slate-50/80';


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
                                rgba(27,58,107,.07);
                            color:#1B3A6B;
                        "
                    >

                        ${iconoTipoAula(
                            aula.tipo
                        )}

                    </div>


                    <div>

                        <p class="text-sm font-bold text-slate-800">

                            ${esc(
                                aula.nombre
                            )}

                        </p>


                        <p class="mt-0.5 text-xs text-slate-400">

                            ${esc(
                                aula.codigo
                            )}

                        </p>

                    </div>

                </div>

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                ${esc(
                    aula.codigo
                )}

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                ${esc(
                    aula.capacidad
                )}
                alumnos

            </td>


            <td class="whitespace-nowrap px-5 py-4 text-sm text-slate-600">

                ${esc(
                    labelTipo(
                        aula.tipo
                    )
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
                            aula.nivel
                        )
                    )}

                </span>

            </td>


            <td class="whitespace-nowrap px-5 py-4">

                ${
                    aula.activo

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
                        class="aula-action-btn aula-action-edit editar-aula"
                        data-id="${esc(
                            aula.id
                        )}"
                        title="Editar aula"
                        aria-label="Editar aula"
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
                        class="aula-action-btn aula-action-delete eliminar-aula"
                        data-id="${esc(
                            aula.id
                        )}"
                        data-nombre="${esc(
                            aula.nombre
                        )}"
                        title="Eliminar aula"
                        aria-label="Eliminar aula"
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
        totalFiltradas
    ) {

        if (!paginacion) {
            return;
        }


        const totalPaginas =
            Math.max(
                1,

                Math.ceil(
                    totalFiltradas /
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
            totalFiltradas <=
            POR_PAGINA
        ) {

            paginacion.innerHTML =
                '';

            return;
        }


        let html = `

            <button
                type="button"
                class="aulas-pagination-btn"
                data-pagina="${paginaActual - 1}"
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
                        aulas-pagination-btn
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
                class="aulas-pagination-btn"
                data-pagina="${paginaActual + 1}"
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


            renderizarAulas();


            document
                .querySelector(
                    '#aulas-body'
                )
                ?.closest(
                    '.aulas-card'
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

    function renderizarAulas() {

        const aulas =
            leerAulas();


        actualizarEstadisticas(
            aulas
        );


        const filtradas =
            obtenerAulasFiltradas(
                aulas
            );


        const totalPaginas =
            Math.max(
                1,

                Math.ceil(
                    filtradas.length /
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
            filtradas.slice(
                inicio,
                fin
            );


        tabla.innerHTML =
            '';


        if (!filtradas.length) {

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
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m20 20-3.5-3.5"/>
                                </svg>

                            </div>


                            <h3
                                class="mt-4 font-bold"
                                style="color:#0F2749;"
                            >
                                No se encontraron aulas
                            </h3>


                            <p class="mt-1 text-sm text-slate-400">
                                Cambia la búsqueda o los filtros seleccionados.
                            </p>

                        </div>

                    </td>

                </tr>
            `;

        } else {

            pagina.forEach(
                aula => {

                    tabla.appendChild(
                        crearFila(
                            aula
                        )
                    );
                }
            );
        }


        if (resultadosAulas) {

            resultadosAulas.textContent =
                filtradas.length;
        }


        if (resultadosFooter) {

            resultadosFooter.textContent =
                pagina.length;
        }


        if (totalResultadosFooter) {

            totalResultadosFooter.textContent =
                filtradas.length;
        }


        renderizarPaginacion(
            filtradas.length
        );
    }


    // =========================================================
    // EDITAR
    // =========================================================

    tabla.addEventListener(
        'click',
        event => {

            const botonEditar =
                event.target.closest(
                    '.editar-aula'
                );


            if (botonEditar) {

                const id =
                    botonEditar.dataset.id;


                const aula =
                    leerAulas()
                        .find(
                            item =>
                                String(
                                    item.id
                                ) ===
                                String(id)
                        );


                if (!aula) {
                    return;
                }


                aulaEditandoId =
                    aula.id;


                codigoInput.value =
                    aula.codigo ??
                    '';


                nombreInput.value =
                    aula.nombre ??
                    '';


                capacidadInput.value =
                    aula.capacidad ??
                    '';


                tipoInput.value =
                    aula.tipo ??
                    '';


                nivelInput.value =
                    aula.nivel ??
                    'todos';


                equipamientoInput.value =
                    aula.equipamiento ??
                    '';


                estadoInput.value =
                    aula.activo
                        ? '1'
                        : '0';


                observacionesInput.value =
                    aula.observaciones ??
                    '';


                modalTitle.textContent =
                    'Editar aula';


                submitText.textContent =
                    'Guardar cambios';


                actualizarTodosCustomSelect();


                abrirAulaModal(
                    true
                );


                return;
            }


            const botonEliminar =
                event.target.closest(
                    '.eliminar-aula'
                );


            if (botonEliminar) {

                abrirEliminarModal(

                    botonEliminar.dataset
                        .nombre,

                    botonEliminar.dataset
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

        aulaAEliminarId =
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

        aulaAEliminarId =
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
                aulaAEliminarId ===
                null
            ) {
                return;
            }


            const aulas =
                leerAulas()
                    .filter(
                        aula =>
                            String(
                                aula.id
                            ) !==
                            String(
                                aulaAEliminarId
                            )
                    );


            guardarAulas(
                aulas
            );


            cerrarEliminarModal();


            renderizarAulas();
        }
    );


    // =========================================================
    // FILTROS
    // =========================================================

    function cambioFiltro() {

        paginaActual =
            1;


        renderizarAulas();
    }


    buscador?.addEventListener(
        'input',
        cambioFiltro
    );


    [
        filtroEstado,
        filtroTipo,
        filtroNivel
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


                        cambioFiltro();
                    }
                );
            }
        );


    // =========================================================
    // CAMBIOS EN MODAL SELECTS
    // =========================================================

    [
        tipoInput,
        nivelInput,
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
                !modal.classList.contains(
                    'hidden'
                )
            ) {

                cerrarAulaModal();
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
                AULAS_KEY
            ) {

                paginaActual =
                    1;


                renderizarAulas();
            }
        }
    );


    window.addEventListener(
        'focus',
        () => {

            renderizarAulas();

            actualizarTodosCustomSelect();
        }
    );


    // =========================================================
    // INICIO
    // =========================================================

    actualizarTodosCustomSelect();

    renderizarAulas();

});