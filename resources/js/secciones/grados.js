document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // GUARD
    // =========================================================

    const tabla =
        document.getElementById('grados-body');

    const formulario =
        document.getElementById('grado-form');

    const modal =
        document.getElementById('grado-modal');


    if (
        !tabla ||
        !formulario ||
        !modal
    ) {
        return;
    }


    // =========================================================
    // ELEMENTOS
    // =========================================================

    const abrirModalBtn =
        document.getElementById('open-grado-modal');

    const cerrarModalBtn =
        document.getElementById('close-grado-modal');

    const cancelarModalBtn =
        document.getElementById('cancel-grado-modal');

    const modalOverlay =
        document.getElementById('grado-modal-overlay');


    const modalTitle =
        document.getElementById('grado-modal-title');

    const submitButton =
        document.getElementById('grado-submit');

    const submitText =
        document.getElementById('grado-submit-text');


    const codigoInput =
        document.getElementById('grado-codigo');

    const nivelInput =
        document.getElementById('grado-nivel');

    const gradoInput =
        document.getElementById('grado-grado');

    const seccionInput =
        document.getElementById('grado-seccion');

    const capacidadInput =
        document.getElementById('grado-capacidad');

    const estudiantesInput =
        document.getElementById('grado-estudiantes');

    const anioInput =
        document.getElementById('grado-anio');

    const turnoInput =
        document.getElementById('grado-turno');

    const estadoInput =
        document.getElementById('grado-estado');


    const formErrors =
        document.getElementById('grado-form-errors');


    // =========================================================
    // FILTROS
    // =========================================================

    const buscarInput =
        document.getElementById('buscar-grado');

    const filtroNivel =
        document.getElementById('filtro-grado-nivel');

    const filtroTurno =
        document.getElementById('filtro-grado-turno');

    const filtroEstado =
        document.getElementById('filtro-grado-estado');

    const filtroAnio =
        document.getElementById('filtro-grado-anio');

    const limpiarFiltrosBtn =
        document.getElementById('limpiar-filtros-grado');


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    const totalGrados =
        document.getElementById('total-grados');

    const gradosActivos =
        document.getElementById('grados-activos');

    const totalEstudiantes =
        document.getElementById('total-estudiantes-grados');

    const capacidadTotal =
        document.getElementById('capacidad-total-grados');


    // =========================================================
    // RESULTADOS / PAGINACIÓN
    // =========================================================

    const resultadosGrados =
        document.getElementById('resultados-grados');

    const resultadosFooter =
        document.getElementById('resultados-grados-footer');

    const paginacion =
        document.getElementById('grados-paginacion');


    // =========================================================
    // MENSAJE
    // =========================================================

    const mensaje =
        document.getElementById('grado-mensaje');


    // =========================================================
    // MODAL ELIMINAR
    // =========================================================

    const deleteModal =
        document.getElementById('delete-grado-modal');

    const deleteOverlay =
        document.getElementById('delete-grado-overlay');

    const cancelDelete =
        document.getElementById('cancel-delete-grado');

    const confirmDelete =
        document.getElementById('confirm-delete-grado');

    const deleteNombre =
        document.getElementById('delete-grado-name');


    // =========================================================
    // ESTADO
    // =========================================================

    let gradoEditandoId = null;

    let gradoAEliminarId = null;

    let paginaActual = 1;

    let debounceTimer = null;

    let peticionActual = 0;


    // =========================================================
    // HELPERS
    // =========================================================

    function escaparHTML(valor) {

        return String(valor ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    function capitalizar(valor) {

        const texto =
            String(valor ?? '')
                .trim();

        if (!texto) {
            return '';
        }

        return (
            texto.charAt(0).toUpperCase() +
            texto.slice(1)
        );
    }


    function nombreGrado(grado) {

        if (!grado) {
            return 'Grado';
        }

        return (
            grado.nombre_completo ||
            `${grado.grado || ''} ${grado.seccion || ''}`.trim() ||
            grado.codigo ||
            `Grado ${grado.id}`
        );
    }


    function normalizarActivo(valor) {

        return (
            valor === true ||
            valor === 1 ||
            valor === '1' ||
            valor === 'true'
        );
    }


    // =========================================================
    // MENSAJES
    // =========================================================

    function mostrarMensaje(
        texto,
        tipo = 'success'
    ) {

        if (!mensaje) {
            return;
        }


        mensaje.className =
            'rounded-xl border px-4 py-3 text-sm';


        if (tipo === 'success') {

            mensaje.classList.add(
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700'
            );

        } else if (tipo === 'info') {

            mensaje.classList.add(
                'border-blue-200',
                'bg-blue-50',
                'text-blue-700'
            );

        } else {

            mensaje.classList.add(
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );
        }


        mensaje.textContent =
            texto;


        mensaje.classList.remove(
            'hidden'
        );


        clearTimeout(
            mostrarMensaje.timer
        );


        mostrarMensaje.timer =
            setTimeout(
                () => {

                    mensaje.classList.add(
                        'hidden'
                    );

                },
                4500
            );
    }


    // =========================================================
    // ERRORES FORMULARIO
    // =========================================================

    function limpiarErroresFormulario() {

        if (!formErrors) {
            return;
        }

        formErrors.innerHTML = '';

        formErrors.classList.add(
            'hidden'
        );
    }


    function mostrarErroresFormulario(
        errores
    ) {

        if (!formErrors) {
            return;
        }


        const mensajes = [];


        Object.values(
            errores || {}
        ).forEach(
            lista => {

                if (
                    Array.isArray(lista)
                ) {

                    lista.forEach(
                        texto => {
                            mensajes.push(
                                texto
                            );
                        }
                    );

                } else if (lista) {

                    mensajes.push(
                        String(lista)
                    );
                }
            }
        );


        if (!mensajes.length) {

            formErrors.textContent =
                'Revisa los datos ingresados.';

        } else {

            formErrors.innerHTML =
                mensajes
                    .map(
                        texto => `
                            <p>
                                ${escaparHTML(texto)}
                            </p>
                        `
                    )
                    .join('');
        }


        formErrors.classList.remove(
            'hidden'
        );
    }


    // =========================================================
    // MODAL
    // =========================================================

    function abrirModal() {

        modal.classList.remove(
            'hidden'
        );

        modal.classList.add(
            'flex'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }


    function cerrarModal() {

        modal.classList.add(
            'hidden'
        );

        modal.classList.remove(
            'flex'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );

        limpiarErroresFormulario();

        gradoEditandoId = null;
    }


    function prepararNuevoGrado() {

        gradoEditandoId = null;

        formulario.reset();


        capacidadInput.value =
            '30';

        estudiantesInput.value =
            '0';

        estadoInput.value =
            '1';


        if (
            anioInput &&
            !anioInput.value
        ) {

            anioInput.value =
                new Date()
                    .getFullYear();
        }


        modalTitle.textContent =
            'Nuevo grado';

        submitText.textContent =
            'Guardar grado';

        submitButton.disabled =
            false;


        limpiarErroresFormulario();

        abrirModal();


        setTimeout(
            () => {
                codigoInput?.focus();
            },
            80
        );
    }


    function prepararEditarGrado(
        grado
    ) {

        if (!grado) {
            return;
        }


        gradoEditandoId =
            grado.id;


        codigoInput.value =
            grado.codigo ?? '';

        nivelInput.value =
            grado.nivel ?? '';

        gradoInput.value =
            grado.grado ?? '';

        seccionInput.value =
            grado.seccion ?? '';

        capacidadInput.value =
            grado.capacidad_maxima ?? 30;

        estudiantesInput.value =
            grado.numero_estudiantes ?? 0;

        anioInput.value =
            grado.año_academico ?? '';

        turnoInput.value =
            grado.turno ?? '';

        estadoInput.value =
            normalizarActivo(
                grado.activo
            )
                ? '1'
                : '0';


        modalTitle.textContent =
            'Editar grado';

        submitText.textContent =
            'Guardar cambios';


        limpiarErroresFormulario();

        abrirModal();
    }


    // =========================================================
    // DELETE MODAL
    // =========================================================

    function abrirDeleteModal(
        grado
    ) {

        if (
            !deleteModal ||
            !grado
        ) {
            return;
        }


        gradoAEliminarId =
            grado.id;


        if (deleteNombre) {

            deleteNombre.textContent =
                `"${nombreGrado(grado)}"`;
        }


        deleteModal.classList.remove(
            'hidden'
        );

        deleteModal.classList.add(
            'flex'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }


    function cerrarDeleteModal() {

        if (!deleteModal) {
            return;
        }


        deleteModal.classList.add(
            'hidden'
        );

        deleteModal.classList.remove(
            'flex'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );


        gradoAEliminarId = null;
    }


    // =========================================================
    // REQUEST PARAMS
    // =========================================================

    function construirParametros() {

        const params = {
            page: paginaActual,
            per_page: 10
        };


        const search =
            buscarInput?.value
                .trim();


        if (search) {
            params.search = search;
        }


        if (
            filtroNivel?.value
        ) {
            params.nivel =
                filtroNivel.value;
        }


        if (
            filtroTurno?.value
        ) {
            params.turno =
                filtroTurno.value;
        }


        if (
            filtroAnio?.value
        ) {
            params['año_academico'] =
                filtroAnio.value;
        }


        if (
            filtroEstado?.value !== ''
        ) {
            params.activo =
                filtroEstado.value;
        }


        return params;
    }


    // =========================================================
    // API ERROR
    // =========================================================

    function manejarErrorApi(
        error,
        mensajeGeneral =
            'Ocurrió un error al procesar la solicitud.'
    ) {

        console.error(
            error
        );


        const status =
            error?.response?.status;


        if (status === 401) {

            mostrarMensaje(
                'Autenticación requerida. La API está protegida con Sanctum.',
                'error'
            );

            return;
        }


        if (status === 403) {

            mostrarMensaje(
                'No tienes permisos para realizar esta acción.',
                'error'
            );

            return;
        }


        if (status === 422) {

            const mensajeBackend =
                error?.response?.data?.message;


            mostrarMensaje(
                mensajeBackend ||
                'Los datos enviados no son válidos.',
                'error'
            );

            return;
        }


        const mensajeBackend =
            error?.response?.data?.message;


        mostrarMensaje(
            mensajeBackend ||
            mensajeGeneral,
            'error'
        );
    }


    // =========================================================
    // CARGAR ESTADÍSTICAS
    // =========================================================

    async function cargarEstadisticas() {

        try {

            const response =
                await window.axios.get(
                    '/api/grados/estadisticas'
                );


            const data =
                response?.data?.data ||
                {};


            if (totalGrados) {

                totalGrados.textContent =
                    data.total ?? 0;
            }


            if (gradosActivos) {

                gradosActivos.textContent =
                    data.activos ?? 0;
            }


            if (totalEstudiantes) {

                totalEstudiantes.textContent =
                    data.total_estudiantes ?? 0;
            }


            if (capacidadTotal) {

                capacidadTotal.textContent =
                    data.capacidad_total ?? 0;
            }

        } catch (error) {

            console.error(
                'Error cargando estadísticas de grados:',
                error
            );
        }
    }


    // =========================================================
    // RENDER FILA
    // =========================================================

    function crearFila(
        grado
    ) {

        const tr =
            document.createElement(
                'tr'
            );


        tr.className =
            'transition hover:bg-slate-50';


        const activo =
            normalizarActivo(
                grado.activo
            );


        const porcentaje =
            grado.capacidad_maxima
                ? Math.min(
                    100,
                    Math.round(
                        (
                            Number(
                                grado.numero_estudiantes || 0
                            ) /
                            Number(
                                grado.capacidad_maxima || 1
                            )
                        ) * 100
                    )
                )
                : 0;


        const estadoHtml =
            activo
                ? `
                    <span class="grado-badge bg-emerald-50 text-emerald-700">
                        Activo
                    </span>
                `
                : `
                    <span class="grado-badge bg-slate-100 text-slate-600">
                        Inactivo
                    </span>
                `;


        const nivelHtml = `

            <span
                class="grado-badge"
                style="
                    background:rgba(27,58,107,.07);
                    color:#1B3A6B;
                "
            >
                ${escaparHTML(
                    capitalizar(
                        grado.nivel
                    )
                )}
            </span>
        `;


        tr.innerHTML = `

            <td class="px-6 py-4">

                <span
                    class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-xs font-semibold text-slate-700"
                >
                    ${escaparHTML(
                        grado.codigo || '-'
                    )}
                </span>

            </td>


            <td class="px-6 py-4">

                <div>

                    <p class="font-semibold text-slate-900">
                        ${escaparHTML(
                            nombreGrado(grado)
                        )}
                    </p>

                    <p class="mt-1 text-xs text-slate-400">
                        ${escaparHTML(
                            grado.grado || ''
                        )}
                        ${escaparHTML(
                            grado.seccion
                                ? `· Sección ${grado.seccion}`
                                : ''
                        )}
                    </p>

                </div>

            </td>


            <td class="px-6 py-4">
                ${nivelHtml}
            </td>


            <td class="px-6 py-4">

                <div class="min-w-[120px]">

                    <div class="flex items-center justify-between gap-2">

                        <span class="text-sm font-semibold text-slate-700">
                            ${escaparHTML(
                                grado.numero_estudiantes ?? 0
                            )}
                            /
                            ${escaparHTML(
                                grado.capacidad_maxima ?? 0
                            )}
                        </span>

                        <span class="text-[10px] font-semibold text-slate-400">
                            ${porcentaje}%
                        </span>

                    </div>


                    <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100">

                        <div
                            class="h-full rounded-full"
                            style="
                                width:${porcentaje}%;
                                background:#1B3A6B;
                            "
                        ></div>

                    </div>

                </div>

            </td>


            <td class="px-6 py-4 text-sm text-slate-600">

                ${escaparHTML(
                    grado.año_academico ?? '-'
                )}

            </td>


            <td class="px-6 py-4">

                <span class="text-sm font-medium text-slate-700">

                    ${escaparHTML(
                        capitalizar(
                            grado.turno
                        ) || '-'
                    )}

                </span>

            </td>


            <td class="px-6 py-4">
                ${estadoHtml}
            </td>


            <td class="px-6 py-4">

                <div class="flex items-center justify-end gap-2">

                    <button
                        type="button"
                        class="editar-grado flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                        data-id="${escaparHTML(
                            grado.id
                        )}"
                        title="Editar"
                    >

                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 20h9"/>
                            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                        </svg>

                    </button>


                    <button
                        type="button"
                        class="eliminar-grado flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                        data-id="${escaparHTML(
                            grado.id
                        )}"
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

                </div>

            </td>
        `;


        tr._grado =
            grado;


        return tr;
    }


    // =========================================================
    // RENDER PAGINACIÓN
    // =========================================================

    function renderizarPaginacion(
        meta
    ) {

        if (!paginacion) {
            return;
        }


        paginacion.innerHTML = '';


        const pagina =
            Number(
                meta.current_page || 1
            );

        const ultima =
            Number(
                meta.last_page || 1
            );


        if (ultima <= 1) {
            return;
        }


        const crearBoton = (
            texto,
            paginaDestino,
            activo = false,
            deshabilitado = false
        ) => {

            const button =
                document.createElement(
                    'button'
                );


            button.type =
                'button';


            button.textContent =
                texto;


            button.className =
                'min-w-[36px] rounded-lg border px-3 py-1.5 text-xs font-semibold transition';


            if (activo) {

                button.style.background =
                    '#0F2749';

                button.style.borderColor =
                    '#0F2749';

                button.style.color =
                    'white';

            } else {

                button.classList.add(
                    'border-slate-200',
                    'bg-white',
                    'text-slate-600',
                    'hover:bg-slate-50'
                );
            }


            if (deshabilitado) {

                button.disabled =
                    true;

                button.classList.add(
                    'cursor-not-allowed',
                    'opacity-40'
                );

            } else {

                button.addEventListener(
                    'click',
                    () => {

                        paginaActual =
                            paginaDestino;

                        cargarGrados();

                        window.scrollTo({
                            top: 0,
                            behavior: 'smooth'
                        });
                    }
                );
            }


            return button;
        };


        paginacion.appendChild(
            crearBoton(
                'Anterior',
                pagina - 1,
                false,
                pagina <= 1
            )
        );


        const inicio =
            Math.max(
                1,
                pagina - 2
            );

        const fin =
            Math.min(
                ultima,
                pagina + 2
            );


        for (
            let i = inicio;
            i <= fin;
            i++
        ) {

            paginacion.appendChild(
                crearBoton(
                    String(i),
                    i,
                    i === pagina
                )
            );
        }


        paginacion.appendChild(
            crearBoton(
                'Siguiente',
                pagina + 1,
                false,
                pagina >= ultima
            )
        );
    }


    // =========================================================
    // CARGAR GRADOS
    // =========================================================

    async function cargarGrados() {

        const idPeticion =
            ++peticionActual;


        tabla.innerHTML = `

            <tr>
                <td
                    colspan="8"
                    class="px-6 py-12 text-center text-sm text-slate-400"
                >
                    Cargando grados...
                </td>
            </tr>
        `;


        try {

            const response =
                await window.axios.get(
                    '/api/grados',
                    {
                        params:
                            construirParametros()
                    }
                );


            if (
                idPeticion !==
                peticionActual
            ) {
                return;
            }


            const paginator =
                response?.data?.data ||
                {};


            const grados =
                Array.isArray(
                    paginator.data
                )
                    ? paginator.data
                    : [];


            tabla.innerHTML =
                '';


            if (!grados.length) {

                tabla.innerHTML = `

                    <tr>
                        <td
                            colspan="8"
                            class="px-6 py-14 text-center"
                        >

                            <div class="flex flex-col items-center">

                                <svg
                                    class="mb-3 h-10 w-10 text-slate-300"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                >
                                    <path d="M4 19.5V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14.5"/>
                                    <path d="M8 7h8M8 11h8M8 15h5"/>
                                </svg>

                                <p class="font-medium text-slate-600">
                                    No se encontraron grados
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    Prueba cambiando los filtros.
                                </p>

                            </div>

                        </td>
                    </tr>
                `;

            } else {

                grados.forEach(
                    grado => {

                        tabla.appendChild(
                            crearFila(
                                grado
                            )
                        );
                    }
                );
            }


            const total =
                paginator.total ?? 0;


            if (resultadosGrados) {

                resultadosGrados.textContent =
                    total;
            }


            if (resultadosFooter) {

                resultadosFooter.textContent =
                    total;
            }


            renderizarPaginacion(
                paginator
            );


        } catch (error) {

            tabla.innerHTML = `

                <tr>
                    <td
                        colspan="8"
                        class="px-6 py-14 text-center text-sm text-red-500"
                    >
                        No se pudieron cargar los grados.
                    </td>
                </tr>
            `;


            manejarErrorApi(
                error,
                'No se pudieron cargar los grados.'
            );
        }
    }


    // =========================================================
    // OBTENER GRADO POR ID
    // =========================================================

    async function obtenerGrado(
        id
    ) {

        const response =
            await window.axios.get(
                `/api/grados/${id}`
            );


        return (
            response?.data?.data ||
            null
        );
    }


    // =========================================================
    // VALIDACIÓN FRONTEND
    // =========================================================

    function validarFormulario() {

        limpiarErroresFormulario();


        const capacidad =
            Number(
                capacidadInput.value
            );

        const estudiantes =
            Number(
                estudiantesInput.value
            );


        if (
            !codigoInput.value.trim() ||
            !nivelInput.value ||
            !gradoInput.value.trim() ||
            !seccionInput.value.trim() ||
            !turnoInput.value ||
            !anioInput.value
        ) {

            mostrarErroresFormulario({
                general: [
                    'Completa todos los campos obligatorios.'
                ]
            });

            return false;
        }


        if (
            capacidad < 1 ||
            capacidad > 30
        ) {

            mostrarErroresFormulario({
                capacidad: [
                    'La capacidad máxima debe estar entre 1 y 30.'
                ]
            });

            return false;
        }


        if (
            estudiantes < 0 ||
            estudiantes > 30
        ) {

            mostrarErroresFormulario({
                estudiantes: [
                    'El número de estudiantes debe estar entre 0 y 30.'
                ]
            });

            return false;
        }


        if (
            estudiantes >
            capacidad
        ) {

            mostrarErroresFormulario({
                estudiantes: [
                    'El número de estudiantes no puede exceder la capacidad máxima.'
                ]
            });

            return false;
        }


        return true;
    }


    // =========================================================
    // FORM SUBMIT
    // =========================================================

    formulario.addEventListener(
        'submit',
        async event => {

            event.preventDefault();


            if (
                !validarFormulario()
            ) {
                return;
            }


            const payload = {

                codigo:
                    codigoInput.value
                        .trim(),

                nivel:
                    nivelInput.value,

                grado:
                    gradoInput.value
                        .trim(),

                seccion:
                    seccionInput.value
                        .trim(),

                capacidad_maxima:
                    Number(
                        capacidadInput.value
                    ),

                numero_estudiantes:
                    Number(
                        estudiantesInput.value
                    ),

                año_academico:
                    Number(
                        anioInput.value
                    ),

                turno:
                    turnoInput.value,

                activo:
                    estadoInput.value ===
                    '1'
            };


            submitButton.disabled =
                true;


            submitText.textContent =
                gradoEditandoId
                    ? 'Guardando...'
                    : 'Creando...';


            try {

                if (gradoEditandoId) {

                    await window.axios.put(
                        `/api/grados/${gradoEditandoId}`,
                        payload
                    );


                    mostrarMensaje(
                        'Grado actualizado correctamente.',
                        'success'
                    );

                } else {

                    await window.axios.post(
                        '/api/grados',
                        payload
                    );


                    mostrarMensaje(
                        'Grado creado correctamente.',
                        'success'
                    );
                }


                cerrarModal();


                await Promise.all([
                    cargarGrados(),
                    cargarEstadisticas()
                ]);


            } catch (error) {

                if (
                    error?.response?.status ===
                    422
                ) {

                    const errors =
                        error?.response?.data?.errors;


                    if (errors) {

                        mostrarErroresFormulario(
                            errors
                        );

                    } else {

                        mostrarErroresFormulario({
                            general: [
                                error?.response?.data?.message ||
                                'Los datos enviados no son válidos.'
                            ]
                        });
                    }

                } else {

                    manejarErrorApi(
                        error,
                        'No se pudo guardar el grado.'
                    );
                }

            } finally {

                submitButton.disabled =
                    false;


                submitText.textContent =
                    gradoEditandoId
                        ? 'Guardar cambios'
                        : 'Guardar grado';
            }
        }
    );


    // =========================================================
    // EDITAR
    // =========================================================

    document.addEventListener(
        'click',
        async event => {

            const boton =
                event.target.closest(
                    '.editar-grado'
                );


            if (!boton) {
                return;
            }


            const id =
                boton.dataset.id;


            try {

                boton.disabled =
                    true;


                const grado =
                    await obtenerGrado(
                        id
                    );


                prepararEditarGrado(
                    grado
                );


            } catch (error) {

                manejarErrorApi(
                    error,
                    'No se pudo cargar el grado.'
                );

            } finally {

                boton.disabled =
                    false;
            }
        }
    );


    // =========================================================
    // ABRIR DELETE
    // =========================================================

    document.addEventListener(
        'click',
        event => {

            const boton =
                event.target.closest(
                    '.eliminar-grado'
                );


            if (!boton) {
                return;
            }


            const fila =
                boton.closest('tr');


            const grado =
                fila?._grado;


            if (!grado) {
                return;
            }


            abrirDeleteModal(
                grado
            );
        }
    );


    // =========================================================
    // CONFIRMAR ELIMINAR
    // =========================================================

    confirmDelete?.addEventListener(
        'click',
        async () => {

            if (!gradoAEliminarId) {
                return;
            }


            confirmDelete.disabled =
                true;


            const textoOriginal =
                confirmDelete.textContent;


            confirmDelete.textContent =
                'Eliminando...';


            try {

                await window.axios.delete(
                    `/api/grados/${gradoAEliminarId}`
                );


                cerrarDeleteModal();


                mostrarMensaje(
                    'Grado eliminado correctamente.',
                    'success'
                );


                if (
                    paginaActual > 1
                ) {

                    paginaActual = 1;
                }


                await Promise.all([
                    cargarGrados(),
                    cargarEstadisticas()
                ]);


            } catch (error) {

                manejarErrorApi(
                    error,
                    'No se pudo eliminar el grado.'
                );

            } finally {

                confirmDelete.disabled =
                    false;

                confirmDelete.textContent =
                    textoOriginal;
            }
        }
    );


    // =========================================================
    // FILTROS
    // =========================================================

    function cerrarFiltrosVisuales(
        excepto = null
    ) {

        document
            .querySelectorAll('.grado-filter-select.open')
            .forEach(
                wrapper => {

                    if (
                        wrapper ===
                        excepto
                    ) {
                        return;
                    }

                    wrapper.classList.remove(
                        'open'
                    );

                    wrapper
                        .querySelector('.grado-filter-trigger')
                        ?.setAttribute(
                            'aria-expanded',
                            'false'
                        );
                }
            );
    }


    function sincronizarFiltroVisual(
        wrapper
    ) {

        const select =
            document.getElementById(
                wrapper.dataset.filterSelect
            );

        const valorTexto =
            wrapper.querySelector(
                '.grado-filter-value'
            );

        const opciones =
            wrapper.querySelectorAll(
                '.grado-filter-option'
            );

        if (
            !select ||
            !valorTexto
        ) {
            return;
        }

        let opcionActiva =
            null;

        opciones.forEach(
            opcion => {

                const seleccionada =
                    opcion.dataset.value ===
                    select.value;

                opcion.classList.toggle(
                    'selected',
                    seleccionada
                );

                if (seleccionada) {
                    opcionActiva =
                        opcion;
                }
            }
        );

        valorTexto.textContent =
            opcionActiva?.querySelector('span:nth-child(2)')?.textContent?.trim() ||
            select.selectedOptions?.[0]?.textContent?.trim() ||
            'Todos';
    }


    function sincronizarFiltrosVisuales() {

        document
            .querySelectorAll('.grado-filter-select')
            .forEach(
                sincronizarFiltroVisual
            );
    }


    document
        .querySelectorAll('.grado-filter-select')
        .forEach(
            wrapper => {

                const select =
                    document.getElementById(
                        wrapper.dataset.filterSelect
                    );

                const trigger =
                    wrapper.querySelector(
                        '.grado-filter-trigger'
                    );

                const opciones =
                    wrapper.querySelectorAll(
                        '.grado-filter-option'
                    );

                if (
                    !select ||
                    !trigger
                ) {
                    return;
                }

                trigger.addEventListener(
                    'click',
                    () => {

                        const abrir =
                            !wrapper.classList.contains(
                                'open'
                            );

                        cerrarFiltrosVisuales(
                            wrapper
                        );

                        wrapper.classList.toggle(
                            'open',
                            abrir
                        );

                        trigger.setAttribute(
                            'aria-expanded',
                            abrir ? 'true' : 'false'
                        );
                    }
                );

                opciones.forEach(
                    opcion => {

                        opcion.addEventListener(
                            'click',
                            () => {

                                select.value =
                                    opcion.dataset.value ?? '';

                                sincronizarFiltroVisual(
                                    wrapper
                                );

                                cerrarFiltrosVisuales();

                                select.dispatchEvent(
                                    new Event(
                                        'change',
                                        {
                                            bubbles: true
                                        }
                                    )
                                );
                            }
                        );
                    }
                );

                select.addEventListener(
                    'change',
                    () => sincronizarFiltroVisual(
                        wrapper
                    )
                );

                sincronizarFiltroVisual(
                    wrapper
                );
            }
        );


    document.addEventListener(
        'click',
        event => {

            if (
                event.target.closest(
                    '.grado-filter-select'
                )
            ) {
                return;
            }

            cerrarFiltrosVisuales();
        }
    );


    function actualizarFiltros() {

        paginaActual =
            1;

        cargarGrados();
    }


    buscarInput?.addEventListener(
        'input',
        () => {

            clearTimeout(
                debounceTimer
            );


            debounceTimer =
                setTimeout(
                    () => {
                        actualizarFiltros();
                    },
                    350
                );
        }
    );


    filtroNivel?.addEventListener(
        'change',
        actualizarFiltros
    );


    filtroTurno?.addEventListener(
        'change',
        actualizarFiltros
    );


    filtroEstado?.addEventListener(
        'change',
        actualizarFiltros
    );


    filtroAnio?.addEventListener(
        'input',
        () => {

            clearTimeout(
                debounceTimer
            );


            debounceTimer =
                setTimeout(
                    () => {
                        actualizarFiltros();
                    },
                    350
                );
        }
    );


    limpiarFiltrosBtn?.addEventListener(
        'click',
        () => {

            if (buscarInput) {
                buscarInput.value = '';
            }

            if (filtroNivel) {
                filtroNivel.value = '';
            }

            if (filtroTurno) {
                filtroTurno.value = '';
            }

            if (filtroEstado) {
                filtroEstado.value = '';
            }

            if (filtroAnio) {
                filtroAnio.value = '';
            }

            sincronizarFiltrosVisuales();


            paginaActual =
                1;


            cargarGrados();
        }
    );


    // =========================================================
    // BOTONES MODAL
    // =========================================================

    abrirModalBtn?.addEventListener(
        'click',
        prepararNuevoGrado
    );


    cerrarModalBtn?.addEventListener(
        'click',
        cerrarModal
    );


    cancelarModalBtn?.addEventListener(
        'click',
        cerrarModal
    );


    modalOverlay?.addEventListener(
        'click',
        cerrarModal
    );


    // =========================================================
    // BOTONES DELETE
    // =========================================================

    cancelDelete?.addEventListener(
        'click',
        cerrarDeleteModal
    );


    deleteOverlay?.addEventListener(
        'click',
        cerrarDeleteModal
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


            if (
                !modal.classList.contains(
                    'hidden'
                )
            ) {

                cerrarModal();
            }


            if (
                deleteModal &&
                !deleteModal.classList.contains(
                    'hidden'
                )
            ) {

                cerrarDeleteModal();
            }
        }
    );


    // =========================================================
    // INICIO
    // =========================================================

    Promise.all([
        cargarGrados(),
        cargarEstadisticas()
    ]);

});
