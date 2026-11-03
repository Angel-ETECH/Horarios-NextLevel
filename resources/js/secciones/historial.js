import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {

    const pagina = document.getElementById('historial-page');

    if (!pagina) {
        return;
    }


    // =========================================================
    // ELEMENTOS
    // =========================================================

    const body = document.getElementById('historial-body');
    const resumen = document.getElementById('historial-resumen');

    const totalCambios = document.getElementById('historial-total-cambios');
    const totalCreaciones = document.getElementById('historial-total-creaciones');
    const totalActualizaciones = document.getElementById('historial-total-actualizaciones');
    const totalEliminaciones = document.getElementById('historial-total-eliminaciones');

    const filtroAccion = document.getElementById('historial-filtro-accion');
    const filtroInstitucion = document.getElementById('historial-filtro-institucion');
    const fechaInicio = document.getElementById('historial-fecha-inicio');
    const fechaFin = document.getElementById('historial-fecha-fin');

    const btnFiltrar = document.getElementById('historial-btn-filtrar');
    const btnLimpiar = document.getElementById('historial-btn-limpiar');
    const btnRecargar = document.getElementById('historial-btn-recargar');

    const modalDetalle = document.getElementById('historial-modal-detalle');
    const modalTitulo = document.getElementById('historial-modal-titulo');
    const modalSubtitulo = document.getElementById('historial-modal-subtitulo');
    const modalContenido = document.getElementById('historial-modal-contenido');
    const modalCerrar = document.getElementById('historial-modal-cerrar');
    const modalCerrarFooter = document.getElementById('historial-modal-cerrar-footer');
    const modalRevertir = document.getElementById('historial-modal-revertir');
    const modalAviso = document.getElementById('historial-modal-aviso');

    const modalConfirmacion = document.getElementById('historial-modal-confirmacion');
    const motivoReversion = document.getElementById('historial-motivo-reversion');
    const confirmarCancelar = document.getElementById('historial-confirmar-cancelar');
    const confirmarRevertir = document.getElementById('historial-confirmar-revertir');

    const toast = document.getElementById('historial-toast');


    // =========================================================
    // ESTADO
    // =========================================================

    let registros = [];
    let registroSeleccionado = null;


    // =========================================================
    // CONFIGURACIÓN AXIOS
    // =========================================================

    axios.defaults.headers.common['Accept'] = 'application/json';
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';


    // =========================================================
    // UTILIDADES
    // =========================================================

    function escaparHtml(valor) {

        if (valor === null || valor === undefined) {
            return '';
        }

        return String(valor)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }


    function capitalizar(valor) {

        if (!valor) {
            return '—';
        }

        const texto = String(valor);

        return texto.charAt(0).toUpperCase() + texto.slice(1);
    }


    function formatearFecha(valor) {

        if (!valor) {
            return '—';
        }

        const fecha = new Date(valor);

        if (Number.isNaN(fecha.getTime())) {
            return valor;
        }

        return new Intl.DateTimeFormat('es-PE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(fecha);
    }


    function obtenerNombreUsuario(registro) {

        return registro?.usuario?.name
            ?? registro?.usuario_nombre
            ?? 'Sistema';
    }


    function obtenerProfesor(registro) {

        const profesor = registro?.horario?.profesor;
        const snapshot =
            registro?.datos_nuevos ??
            registro?.datos_anteriores ??
            {};

        if (profesor) {
            return formatearValor(profesor, 'profesor');
        }

        if (snapshot.profesor) {
            return formatearValor(snapshot.profesor, 'profesor');
        }

        return snapshot.profesor_nombre ??
            (
                snapshot.profesor_id
                    ? `Profesor #${snapshot.profesor_id}`
                    : '—'
            );
    }


    function obtenerCurso(registro) {

        const curso = registro?.horario?.curso;
        const snapshot =
            registro?.datos_nuevos ??
            registro?.datos_anteriores ??
            {};

        if (curso) {
            return formatearValor(curso, 'curso');
        }

        if (snapshot.curso) {
            return formatearValor(snapshot.curso, 'curso');
        }

        return snapshot.curso_nombre
            ?? (
                snapshot.curso_id
                    ? `Curso #${snapshot.curso_id}`
                    : null
            )
            ?? '—';
    }


    function obtenerGrado(registro) {

        const grado = registro?.horario?.grado;
        const snapshot =
            registro?.datos_nuevos ??
            registro?.datos_anteriores ??
            {};

        if (grado) {
            return formatearValor(grado, 'grado');
        }

        if (snapshot.grado) {
            return formatearValor(snapshot.grado, 'grado');
        }

        return snapshot.grado_nombre ??
            (
                snapshot.grado_id
                    ? `Grado #${snapshot.grado_id}`
                    : '—'
            );
    }


    function obtenerAula(registro) {

        const aula = registro?.horario?.aula;
        const snapshot =
            registro?.datos_nuevos ??
            registro?.datos_anteriores ??
            {};

        if (aula) {
            return formatearValor(aula, 'aula');
        }

        if (snapshot.aula) {
            return formatearValor(snapshot.aula, 'aula');
        }

        return snapshot.aula_nombre
            ?? (
                snapshot.aula_id
                    ? `Aula #${snapshot.aula_id}`
                    : null
            )
            ?? '—';
    }


    function obtenerDatosHorario(registro) {

        return registro?.horario ??
            registro?.datos_nuevos ??
            registro?.datos_anteriores ??
            {};
    }


    function obtenerHorarioId(registro) {

        return registro?.horario_id ??
            registro?.datos_nuevos?.id ??
            registro?.datos_anteriores?.id ??
            '—';
    }


    function obtenerModulo(registro) {

        return registro?.datos_nuevos?.modulo
            ?? registro?.datos_anteriores?.modulo
            ?? registro?.horario?.modulo
            ?? (registro?.horario_id ? 'horarios' : null);
    }


    function etiquetaRegistro(registro) {

        const etiquetas = {
            horarios: 'Horario',
            profesores: 'Profesor',
            cursos: 'Curso',
            grados: 'Grado',
            aulas: 'Aula',
            asignaciones: 'Asignación',
            disponibilidad: 'Disponibilidad',
            configuraciones: 'Configuración de recesos',
            recuperaciones: 'Recuperación',
        };

        const etiqueta =
            etiquetas[obtenerModulo(registro)] ?? 'Registro';

        return `${etiqueta} #${obtenerHorarioId(registro)}`;
    }


    function obtenerAvisoNoRevertible(registro) {

        if (registro?.accion === 'crear') {

            return obtenerModulo(registro) === 'horarios'
                ? 'El horario de este registro ya no existe: no hay nada que revertir.'
                : 'Una creación de este módulo se deshace desde su propia pantalla.';
        }

        return 'Este registro no se puede revertir desde el historial.';
    }


    function nombreDia(valor) {

        const dias = {
            1: 'Lunes',
            2: 'Martes',
            3: 'Miércoles',
            4: 'Jueves',
            5: 'Viernes',
            6: 'Sábado',
            7: 'Domingo',

            lunes: 'Lunes',
            martes: 'Martes',
            miercoles: 'Miércoles',
            miércoles: 'Miércoles',
            jueves: 'Jueves',
            viernes: 'Viernes',
            sabado: 'Sábado',
            sábado: 'Sábado',
            domingo: 'Domingo',
        };

        return dias[valor]
            ?? dias[String(valor).toLowerCase()]
            ?? valor
            ?? '—';
    }


    function formatearHora(valor) {

        if (!valor) {
            return '—';
        }

        const texto = String(valor);

        const coincidencia = texto.match(/(\d{2}:\d{2})/);

        return coincidencia
            ? coincidencia[1]
            : texto;
    }


    function badgeAccion(accion) {

        const configuracion = {
            crear: {
                texto: 'Creación',
                clases: 'bg-emerald-50 text-emerald-700 border-emerald-200',
            },

            actualizar: {
                texto: 'Actualización',
                clases: 'bg-blue-50 text-blue-700 border-blue-200',
            },

            eliminar: {
                texto: 'Eliminación',
                clases: 'bg-red-50 text-red-700 border-red-200',
            },

            restaurar: {
                texto: 'Restauración',
                clases: 'bg-amber-50 text-amber-700 border-amber-200',
            },

            revertir: {
                texto: 'Reversión',
                clases: 'bg-violet-50 text-violet-700 border-violet-200',
            },
        };

        const item = configuracion[accion] ?? {
            texto: capitalizar(accion),
            clases: 'bg-slate-50 text-slate-700 border-slate-200',
        };

        return `
            <span class="inline-flex items-center rounded-full border px-2.5 py-1 text-[11px] font-bold ${item.clases}">
                ${escaparHtml(item.texto)}
            </span>
        `;
    }


    function mostrarToast(mensaje, tipo = 'info') {

        if (!toast) {
            return;
        }

        toast.textContent = mensaje;

        toast.classList.remove(
            'hidden',
            'bg-slate-900',
            'bg-emerald-600',
            'bg-red-600'
        );

        if (tipo === 'success') {
            toast.classList.add('bg-emerald-600');
        } else if (tipo === 'error') {
            toast.classList.add('bg-red-600');
        } else {
            toast.classList.add('bg-slate-900');
        }

        clearTimeout(mostrarToast.timeout);

        mostrarToast.timeout = setTimeout(() => {
            toast.classList.add('hidden');
        }, 3500);
    }


    // =========================================================
    // SELECTS VISUALES
    // =========================================================

    function cerrarSelectsVisuales(excepto = null) {

        document
            .querySelectorAll('.historial-filter-select.open')
            .forEach(wrapper => {

                if (wrapper === excepto) {
                    return;
                }

                wrapper.classList.remove('open');

                wrapper
                    .querySelector('.historial-filter-trigger')
                    ?.setAttribute('aria-expanded', 'false');
            });
    }


    function sincronizarSelectVisual(wrapper) {

        const select = document.getElementById(wrapper.dataset.historialSelect);
        const texto = wrapper.querySelector('.historial-filter-value');
        const opciones = wrapper.querySelectorAll('.historial-filter-option');

        if (!select || !texto) {
            return;
        }

        let opcionActiva = null;

        opciones.forEach(opcion => {

            const seleccionada = opcion.dataset.value === select.value;

            opcion.classList.toggle('selected', seleccionada);

            if (seleccionada) {
                opcionActiva = opcion;
            }
        });

        texto.textContent =
            opcionActiva?.querySelector('span:nth-child(2)')?.textContent?.trim()
            || select.selectedOptions?.[0]?.textContent?.trim()
            || 'Todas';
    }


    function sincronizarSelectsVisuales() {

        document
            .querySelectorAll('.historial-filter-select')
            .forEach(sincronizarSelectVisual);
    }


    document
        .querySelectorAll('.historial-filter-select')
        .forEach(wrapper => {

            const select = document.getElementById(wrapper.dataset.historialSelect);
            const trigger = wrapper.querySelector('.historial-filter-trigger');
            const opciones = wrapper.querySelectorAll('.historial-filter-option');

            if (!select || !trigger) {
                return;
            }

            trigger.addEventListener('click', () => {

                const abrir = !wrapper.classList.contains('open');

                cerrarSelectsVisuales(wrapper);
                wrapper.classList.toggle('open', abrir);
                trigger.setAttribute('aria-expanded', abrir ? 'true' : 'false');
            });

            opciones.forEach(opcion => {

                opcion.addEventListener('click', () => {

                    select.value = opcion.dataset.value ?? '';
                    sincronizarSelectVisual(wrapper);
                    cerrarSelectsVisuales();

                    select.dispatchEvent(new Event('change', {
                        bubbles: true,
                    }));
                });
            });

            select.addEventListener('change', () => sincronizarSelectVisual(wrapper));

            sincronizarSelectVisual(wrapper);
        });


    document.addEventListener('click', evento => {

        if (evento.target.closest('.historial-filter-select')) {
            return;
        }

        cerrarSelectsVisuales();
    });


    // =========================================================
    // CARGA
    // =========================================================

    async function cargarHistorial() {

        mostrarCargando();

        try {

            const params = {
                limit: 100,
            };

            if (filtroAccion.value) {
                params.accion = filtroAccion.value;
            }

            if (filtroInstitucion.value) {
                params.institucion = filtroInstitucion.value;
            }

            if (fechaInicio.value && fechaFin.value) {
                params.fecha_inicio = fechaInicio.value;
                params.fecha_fin = `${fechaFin.value} 23:59:59`;
            }

            const respuesta = await axios.get('/api/historial', {
                params,
            });

            registros = Array.isArray(respuesta.data?.data)
                ? respuesta.data.data
                : [];

            renderizarHistorial();

            await cargarEstadisticas();

        } catch (error) {

            manejarErrorCarga(error);
        }
    }


    async function cargarEstadisticas() {

        try {

            const respuesta = await axios.get('/api/historial/estadisticas');

            const data = respuesta.data?.data ?? {};

            totalCambios.textContent =
                Number(data.total_cambios ?? registros.length);

            totalCreaciones.textContent =
                Number(data.cambios_por_accion?.crear ?? 0);

            totalActualizaciones.textContent =
                Number(data.cambios_por_accion?.actualizar ?? 0);

            totalEliminaciones.textContent =
                Number(data.cambios_por_accion?.eliminar ?? 0);

        } catch (error) {

            // Si falla únicamente estadísticas,
            // calculamos lo que podamos con los registros cargados.

            totalCambios.textContent = registros.length;

            totalCreaciones.textContent =
                registros.filter(item => item.accion === 'crear').length;

            totalActualizaciones.textContent =
                registros.filter(item => item.accion === 'actualizar').length;

            totalEliminaciones.textContent =
                registros.filter(item => item.accion === 'eliminar').length;
        }
    }


    function mostrarCargando() {

        resumen.textContent = 'Cargando historial...';

        body.innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-12 text-center text-sm text-slate-400">
                    Cargando historial...
                </td>
            </tr>
        `;
    }


    function manejarErrorCarga(error) {

        registros = [];

        const status = error?.response?.status;

        if (status === 401) {

            resumen.textContent = 'La API requiere una sesión autenticada.';

            body.innerHTML = `
                <tr>
                    <td colspan="9" class="px-6 py-14 text-center">
                        <div class="mx-auto max-w-md">

                            <div
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-full"
                                style="background:#FEF3C7;"
                            >
                                <svg
                                    class="h-6 w-6"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="#92400E"
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
                                El backend protege el historial con Sanctum.
                                Cuando la autenticación del backend esté disponible,
                                esta pantalla comenzará a consumir los registros reales.
                            </p>

                        </div>
                    </td>
                </tr>
            `;

            return;
        }

        resumen.textContent = 'No fue posible cargar el historial.';

        body.innerHTML = `
            <tr>
                <td colspan="9" class="px-6 py-14 text-center">
                    <p class="font-bold text-red-600">
                        No se pudo conectar con el historial.
                    </p>

                    <p class="mt-2 text-sm text-slate-400">
                        Revisa que el servidor Laravel esté ejecutándose.
                    </p>
                </td>
            </tr>
        `;

        console.error('Error cargando historial:', error);
    }


    // =========================================================
    // RENDER
    // =========================================================

    function renderizarHistorial() {

        resumen.textContent =
            registros.length === 1
                ? '1 registro encontrado'
                : `${registros.length} registros encontrados`;

        if (!registros.length) {

            body.innerHTML = `
                <tr>
                    <td colspan="9" class="px-6 py-14 text-center">
                        <p class="font-bold text-slate-600">
                            No hay registros
                        </p>

                        <p class="mt-2 text-sm text-slate-400">
                            No se encontraron cambios con los filtros seleccionados.
                        </p>
                    </td>
                </tr>
            `;

            return;
        }

        body.innerHTML = registros.map(registro => {

            const horario = obtenerDatosHorario(registro);

            return `
                <tr class="transition hover:bg-slate-50/80">

                    <td class="px-5 py-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-extrabold text-white"
                                style="background:#1B3A6B;"
                            >
                                ${escaparHtml(
                                    obtenerNombreUsuario(registro)
                                        .charAt(0)
                                        .toUpperCase()
                                )}
                            </div>

                            <div class="min-w-0">

                                <p
                                    class="max-w-[150px] truncate text-sm font-bold"
                                    style="color:#0F2749;"
                                >
                                    ${escaparHtml(obtenerNombreUsuario(registro))}
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    ID ${escaparHtml(registro.usuario_id ?? '—')}
                                </p>

                            </div>

                        </div>

                    </td>


                    <td class="px-5 py-4">
                        ${badgeAccion(registro.accion)}
                    </td>


                    <td class="px-5 py-4">

                        <p class="text-sm font-bold text-slate-700">
                            ${escaparHtml(etiquetaRegistro(registro))}
                        </p>

                        <p class="mt-1 text-xs text-slate-400">
                            ${escaparHtml(
                                horario?.institucion
                                    ? capitalizar(horario.institucion)
                                    : 'Sin institución'
                            )}
                        </p>

                    </td>


                    <td class="px-5 py-4 text-sm text-slate-600">
                        ${escaparHtml(obtenerProfesor(registro))}
                    </td>


                    <td class="px-5 py-4 text-sm text-slate-600">
                        ${escaparHtml(obtenerCurso(registro))}
                    </td>


                    <td class="px-5 py-4 text-sm text-slate-600">
                        ${escaparHtml(obtenerGrado(registro))}
                    </td>


                    <td class="px-5 py-4 text-sm text-slate-600">
                        ${escaparHtml(obtenerAula(registro))}
                    </td>


                    <td class="px-5 py-4">

                        <p class="whitespace-nowrap text-sm text-slate-600">
                            ${escaparHtml(formatearFecha(registro.created_at))}
                        </p>

                    </td>


                    <td class="px-5 py-4 text-right">

                        <button
                            type="button"
                            class="historial-ver-detalle inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700"
                            data-id="${escaparHtml(registro.id)}"
                            title="Ver detalle"
                        >
                            <svg
                                class="h-4 w-4"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>

                    </td>

                </tr>
            `;

        }).join('');

        document
            .querySelectorAll('.historial-ver-detalle')
            .forEach(boton => {

                boton.addEventListener('click', () => {

                    const id = Number(boton.dataset.id);

                    abrirDetalle(id);
                });
            });
    }


    // =========================================================
    // DETALLE
    // =========================================================

    async function abrirDetalle(id) {

        try {

            modalContenido.innerHTML = `
                <div class="py-10 text-center text-sm text-slate-400">
                    Cargando detalle...
                </div>
            `;

            abrirModalDetalle();

            const respuesta = await axios.get(`/api/historial/${id}`);

            registroSeleccionado = respuesta.data?.data ?? null;

            if (!registroSeleccionado) {
                throw new Error('Registro no encontrado');
            }

            renderizarDetalle(registroSeleccionado);

        } catch (error) {

            modalContenido.innerHTML = `
                <div class="py-10 text-center text-sm text-red-600">
                    No se pudo cargar el detalle.
                </div>
            `;

            console.error(error);
        }
    }


    function renderizarDetalle(registro) {

        const horario = obtenerDatosHorario(registro);

        modalTitulo.textContent =
            etiquetaRegistro(registro);

        modalSubtitulo.textContent =
            `${capitalizar(registro.accion)} · ${formatearFecha(registro.created_at)}`;

        const datosAnteriores = registro.datos_anteriores ?? {};
        const datosNuevos = registro.datos_nuevos ?? {};

        const diferencias = obtenerDiferencias(
            datosAnteriores,
            datosNuevos
        );

        modalContenido.innerHTML = `

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                ${tarjetaDetalle(
                    'Usuario',
                    obtenerNombreUsuario(registro)
                )}

                ${tarjetaDetalle(
                    'Acción',
                    capitalizar(registro.accion)
                )}

                ${tarjetaDetalle(
                    'Profesor',
                    obtenerProfesor(registro)
                )}

                ${tarjetaDetalle(
                    'Curso',
                    obtenerCurso(registro)
                )}

                ${tarjetaDetalle(
                    'Grado',
                    obtenerGrado(registro)
                )}

                ${tarjetaDetalle(
                    'Aula',
                    obtenerAula(registro)
                )}

                ${tarjetaDetalle(
                    'Institución',
                    capitalizar(horario.institucion)
                )}

                ${tarjetaDetalle(
                    'Día',
                    nombreDia(horario.dia_semana)
                )}

                ${tarjetaDetalle(
                    'Horario',
                    `${formatearHora(horario.hora_inicio)} - ${formatearHora(horario.hora_fin)}`
                )}

                ${tarjetaDetalle(
                    'Turno',
                    capitalizar(horario.turno)
                )}

                ${tarjetaDetalle(
                    'Periodo académico',
                    horario.periodo_academico ?? '—'
                )}

                ${tarjetaDetalle(
                    'Estado',
                    capitalizar(horario.estado)
                )}

            </div>


            <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50 p-4">

                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Motivo
                </p>

                <p class="mt-2 text-sm leading-6 text-slate-600">
                    ${escaparHtml(registro.motivo ?? 'Sin motivo registrado')}
                </p>

            </div>


            <div class="mt-6">

                <div class="mb-3">

                    <h4
                        class="font-extrabold"
                        style="color:#0F2749;"
                    >
                        Cambios realizados
                    </h4>

                    <p class="mt-1 text-xs text-slate-400">
                        Comparación entre el estado anterior y el nuevo.
                    </p>

                </div>

                ${renderizarDiferencias(diferencias)}

            </div>

        `;

        const revertible =
            registro.puede_revertirse === true;

        if (revertible) {
            modalRevertir.classList.remove('hidden');
            modalRevertir.classList.add('inline-flex');
        } else {
            modalRevertir.classList.add('hidden');
            modalRevertir.classList.remove('inline-flex');
        }

        if (modalAviso) {

            modalAviso.textContent =
                revertible
                    ? ''
                    : obtenerAvisoNoRevertible(registro);

            modalAviso.classList.toggle('hidden', revertible);
        }
    }


    function tarjetaDetalle(titulo, valor) {

        return `
            <div class="rounded-xl border border-slate-200 p-4">

                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    ${escaparHtml(titulo)}
                </p>

                <p
                    class="mt-1.5 text-sm font-bold"
                    style="color:#0F2749;"
                >
                    ${escaparHtml(valor ?? '—')}
                </p>

            </div>
        `;
    }


    function obtenerDiferencias(anteriores, nuevos) {

        const campos = new Set([
            ...Object.keys(anteriores ?? {}),
            ...Object.keys(nuevos ?? {}),
        ]);

        const diferencias = [];

        campos.forEach(campo => {

            if ([
                'id',
                'created_at',
                'updated_at',
                'deleted_at',
            ].includes(campo)) {
                return;
            }

            const anterior = anteriores?.[campo] ?? null;
            const nuevo = nuevos?.[campo] ?? null;

            if (JSON.stringify(anterior) !== JSON.stringify(nuevo)) {

                diferencias.push({
                    campo,
                    anterior,
                    nuevo,
                });
            }
        });

        return diferencias;
    }


    function renderizarDiferencias(diferencias) {

        if (!diferencias.length) {

            return `
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-6 text-center text-sm text-slate-400">
                    No hay diferencias comparables para este registro.
                </div>
            `;
        }

        return `
            <div class="space-y-3">

                ${diferencias.map(item => `

                    <div class="overflow-hidden rounded-xl border border-slate-200">

                        <div class="border-b border-slate-200 bg-slate-50 px-4 py-2.5">

                            <p class="text-xs font-extrabold text-slate-600">
                                ${escaparHtml(formatearNombreCampo(item.campo))}
                            </p>

                        </div>

                        <div class="grid grid-cols-1 divide-y divide-slate-200 sm:grid-cols-2 sm:divide-x sm:divide-y-0">

                            <div class="p-4">

                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Antes
                                </p>

                                <p class="mt-1 break-words text-sm text-slate-600">
                                    ${escaparHtml(formatearValor(item.anterior, item.campo))}
                                </p>

                            </div>

                            <div class="p-4">

                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Después
                                </p>

                                <p class="mt-1 break-words text-sm font-semibold text-slate-700">
                                    ${escaparHtml(formatearValor(item.nuevo, item.campo))}
                                </p>

                            </div>

                        </div>

                    </div>

                `).join('')}

            </div>
        `;
    }


    function formatearNombreCampo(campo) {

        const nombres = {
            profesor: 'Profesor',
            profesor_id: 'Profesor',
            curso: 'Curso',
            curso_id: 'Curso',
            aula: 'Aula',
            aula_id: 'Aula',
            grado: 'Grado',
            grado_id: 'Grado',
            dia_semana: 'Día',
            hora_inicio: 'Hora de inicio',
            hora_fin: 'Hora de fin',
            turno: 'Turno',
            institucion: 'Institución',
            tipo: 'Tipo',
            semana: 'Semana',
            periodo_academico: 'Periodo académico',
            estado: 'Estado',
            version: 'Versión',
            creado_por: 'Creado por',
            modificado_por: 'Modificado por',
            observacion: 'Observación',
            profesor_nombre: 'Nombre de profesor',
            curso_nombre: 'Nombre de curso',
            aula_nombre: 'Nombre de aula',
            grado_nombre: 'Nombre de grado',
            original: 'Horario original',
        };

        return nombres[campo]
            ?? campo
                .replaceAll('_', ' ')
                .replace(/\b\w/g, letra => letra.toUpperCase());
    }


    const CLAVES_IGNORADAS_FALLBACK = new Set([
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'deleted',
        'password',
        'remember_token',
        'token',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'modulo',
        'tipo_registro',
        'solo_auditoria',
    ]);


    function normalizarEstructura(valor) {

        if (typeof valor === 'string') {
            const recortado = valor.trim();
            if (
                (recortado.startsWith('{') && recortado.endsWith('}')) ||
                (recortado.startsWith('[') && recortado.endsWith(']'))
            ) {
                try {
                    return JSON.parse(recortado);
                } catch (e) {
                    return valor;
                }
            }
        }

        return valor;
    }


    function esProfesorObjeto(obj, campo = null) {

        if (!obj || typeof obj !== 'object' || Array.isArray(obj)) {
            return false;
        }

        const c = String(campo || '').toLowerCase();
        if (c === 'profesor' || c === 'profesor_id' || c.includes('profesor')) {
            return true;
        }

        if ('apellido_paterno' in obj || 'apellido_materno' in obj) {
            return true;
        }

        if ('carga_horaria_maxima' in obj || 'carga_horaria_actual' in obj) {
            return true;
        }

        if ('especialidad' in obj && ('dni' in obj || 'codigo' in obj)) {
            return true;
        }

        if (typeof obj.codigo === 'string' && obj.codigo.startsWith('DOC-')) {
            return true;
        }

        return false;
    }


    function formatearProfesorObjeto(obj) {

        const partesNombre = [
            obj.nombre,
            obj.apellido_paterno,
            obj.apellido_materno,
        ].filter(p => p !== null && p !== undefined && String(p).trim() !== '')
         .map(p => String(p).trim());

        let nombre = partesNombre.join(' ');

        if (!nombre) {
            nombre = (obj.nombre_completo ?? obj.name ?? '').toString().trim();
        }

        if (!nombre && obj.id) {
            nombre = `Profesor #${obj.id}`;
        }

        const codigo = (obj.codigo ?? '').toString().trim();

        if (nombre && codigo) {
            return nombre.includes(`(${codigo})`) ? nombre : `${nombre} (${codigo})`;
        }

        if (nombre) {
            return nombre;
        }

        if (codigo) {
            return `Profesor (${codigo})`;
        }

        return '(vacío)';
    }


    function esCursoObjeto(obj, campo = null) {

        if (!obj || typeof obj !== 'object' || Array.isArray(obj)) {
            return false;
        }

        const c = String(campo || '').toLowerCase();
        if (c === 'curso' || c === 'curso_id' || c.includes('curso')) {
            return true;
        }

        return 'duracion_minutos' in obj || 'horas_semanales' in obj;
    }


    function formatearCursoObjeto(obj) {

        const nombre = (obj.nombre ?? obj.nombre_completo ?? obj.name ?? '').toString().trim();
        const codigo = (obj.codigo ?? '').toString().trim();

        if (nombre && codigo && !nombre.includes(codigo)) {
            return `${nombre} (${codigo})`;
        }

        return nombre || codigo || (obj.id ? `Curso #${obj.id}` : 'Curso');
    }


    function esAulaObjeto(obj, campo = null) {

        if (!obj || typeof obj !== 'object' || Array.isArray(obj)) {
            return false;
        }

        const c = String(campo || '').toLowerCase();
        if (c === 'aula' || c === 'aula_id' || c.includes('aula')) {
            return true;
        }

        return 'equipamiento' in obj || ('capacidad' in obj && ('piso' in obj || 'edificio' in obj));
    }


    function formatearAulaObjeto(obj) {

        const nombre = (obj.nombre ?? obj.name ?? '').toString().trim();
        const codigo = (obj.codigo ?? '').toString().trim();

        if (nombre && codigo && nombre !== codigo && !nombre.includes(codigo)) {
            return `${nombre} (${codigo})`;
        }

        return nombre || codigo || (obj.id ? `Aula #${obj.id}` : 'Aula');
    }


    function esGradoObjeto(obj, campo = null) {

        if (!obj || typeof obj !== 'object' || Array.isArray(obj)) {
            return false;
        }

        const c = String(campo || '').toLowerCase();
        if (c === 'grado' || c === 'grado_id' || c.includes('grado')) {
            return true;
        }

        return 'seccion' in obj || 'numero_estudiantes' in obj || 'año_academico' in obj;
    }


    function formatearGradoObjeto(obj) {

        const codigo = (obj.codigo ?? '').toString().trim();

        if (obj.nombre_completo) {
            const base = String(obj.nombre_completo).trim();
            if (codigo && !base.includes(codigo)) {
                return `${base} (${codigo})`;
            }
            return base;
        }

        const partes = [obj.nivel, obj.grado, obj.seccion ? `Sección ${obj.seccion}` : null]
            .filter(p => p !== null && p !== undefined && String(p).trim() !== '')
            .map(p => String(p).trim());

        if (partes.length > 0) {
            const base = partes.join(' ');
            return (codigo && !base.includes(codigo)) ? `${base} (${codigo})` : base;
        }

        const nombre = (obj.nombre ?? '').toString().trim();
        if (nombre && codigo && !nombre.includes(codigo)) {
            return `${nombre} (${codigo})`;
        }

        return nombre || codigo || (obj.id ? `Grado #${obj.id}` : 'Grado');
    }


    function formatearEntidadConocida(obj, campo = null) {

        if (!obj || typeof obj !== 'object' || Array.isArray(obj)) {
            return null;
        }

        if (esProfesorObjeto(obj, campo)) {
            return formatearProfesorObjeto(obj);
        }

        if (esCursoObjeto(obj, campo)) {
            return formatearCursoObjeto(obj);
        }

        if (esAulaObjeto(obj, campo)) {
            return formatearAulaObjeto(obj);
        }

        if (esGradoObjeto(obj, campo)) {
            return formatearGradoObjeto(obj);
        }

        const titulo = (obj.titulo ?? obj.title ?? '').toString().trim();
        const nombre = (obj.nombre_completo ?? obj.nombre ?? obj.name ?? '').toString().trim();
        const codigo = (obj.codigo ?? obj.code ?? '').toString().trim();

        const textoPrincipal = titulo || nombre;
        if (textoPrincipal && codigo && !textoPrincipal.includes(codigo)) {
            return `${textoPrincipal} (${codigo})`;
        }

        if (textoPrincipal || codigo) {
            return textoPrincipal || codigo;
        }

        return null;
    }


    function formatearFallbackArrayUObjeto(dato, nivel = 0) {

        if (dato === null || dato === undefined || dato === '') {
            return '(vacío)';
        }

        if (nivel > 3) {
            return '...';
        }

        if (Array.isArray(dato)) {
            if (dato.length === 0) {
                return '(vacío)';
            }

            const items = dato.map(item => {
                if (item === null || item === undefined || item === '') {
                    return '(vacío)';
                }
                if (typeof item === 'object') {
                    const entidad = formatearEntidadConocida(item);
                    return entidad ?? formatearFallbackArrayUObjeto(item, nivel + 1);
                }
                return String(item);
            }).filter(t => t && t !== '(vacío)');

            return items.length > 0 ? items.join(', ') : '(vacío)';
        }

        if (typeof dato === 'object') {
            const entidad = formatearEntidadConocida(dato);
            if (entidad) {
                return entidad;
            }

            const pares = [];

            for (const [clave, val] of Object.entries(dato)) {
                if (CLAVES_IGNORADAS_FALLBACK.has(clave)) {
                    continue;
                }
                if (val === null || val === undefined || val === '') {
                    continue;
                }

                const etiqueta = formatearNombreCampo(clave);
                let valTexto;

                if (typeof val === 'boolean') {
                    valTexto = val ? 'Sí' : 'No';
                } else if (typeof val === 'object') {
                    const entVal = formatearEntidadConocida(val, clave);
                    valTexto = entVal ?? formatearFallbackArrayUObjeto(val, nivel + 1);
                } else {
                    valTexto = String(val);
                }

                if (valTexto && valTexto !== '(vacío)') {
                    pares.push(`${etiqueta}: ${valTexto}`);
                }
            }

            if (pares.length > 0) {
                return pares.join(', ');
            }

            if (dato.id) {
                return `#${dato.id}`;
            }

            return '(vacío)';
        }

        return String(dato);
    }


    function formatearValor(valor, campo = null) {

        if (valor === null || valor === undefined || valor === '') {
            return '(vacío)';
        }

        if (typeof valor === 'boolean') {
            return valor ? 'Sí' : 'No';
        }

        const valorNormalizado = normalizarEstructura(valor);

        if (typeof valorNormalizado === 'object') {
            if (valorNormalizado === null) {
                return '(vacío)';
            }

            if (Array.isArray(valorNormalizado)) {
                if (valorNormalizado.length === 0) {
                    return '(vacío)';
                }
                return formatearFallbackArrayUObjeto(valorNormalizado);
            }

            const entidad = formatearEntidadConocida(valorNormalizado, campo);
            if (entidad) {
                return entidad;
            }

            return formatearFallbackArrayUObjeto(valorNormalizado);
        }

        if (campo === 'dia_semana' || campo === 'dia') {
            return nombreDia(valorNormalizado);
        }

        if (campo === 'hora_inicio' || campo === 'hora_fin') {
            return formatearHora(valorNormalizado);
        }

        return String(valorNormalizado);
    }


    // =========================================================
    // MODALES
    // =========================================================

    function abrirModalDetalle() {

        modalDetalle.classList.remove('hidden');
        modalDetalle.classList.add('flex');

        document.body.classList.add('overflow-hidden');
    }


    function cerrarModalDetalle() {

        modalDetalle.classList.add('hidden');
        modalDetalle.classList.remove('flex');

        document.body.classList.remove('overflow-hidden');

        registroSeleccionado = null;
    }


    function abrirConfirmacion() {

        if (!registroSeleccionado) {
            return;
        }

        motivoReversion.value = '';

        modalConfirmacion.classList.remove('hidden');
        modalConfirmacion.classList.add('flex');
    }


    function cerrarConfirmacion() {

        modalConfirmacion.classList.add('hidden');
        modalConfirmacion.classList.remove('flex');
    }


    // =========================================================
    // REVERTIR
    // =========================================================

    async function revertirCambio() {

        if (!registroSeleccionado) {
            return;
        }

        const id = registroSeleccionado.id;

        const motivo =
            motivoReversion.value.trim()
            || 'Revertido desde historial';

        confirmarRevertir.disabled = true;
        confirmarRevertir.textContent = 'Revirtiendo...';

        try {

            const respuesta = await axios.post(
                `/api/historial/revertir/${id}`,
                {
                    motivo,
                }
            );

            cerrarConfirmacion();
            cerrarModalDetalle();

            mostrarToast(
                respuesta.data?.message
                    ?? 'Cambio revertido correctamente.',
                'success'
            );

            await cargarHistorial();

        } catch (error) {

            const mensaje =
                error?.response?.data?.message
                ?? 'No se pudo revertir el cambio.';

            mostrarToast(mensaje, 'error');

            console.error(
                'Error al revertir historial:',
                error
            );

        } finally {

            confirmarRevertir.disabled = false;
            confirmarRevertir.textContent = 'Revertir';
        }
    }


    // =========================================================
    // EVENTOS
    // =========================================================

    btnFiltrar.addEventListener('click', () => {

        if (
            (fechaInicio.value && !fechaFin.value)
            || (!fechaInicio.value && fechaFin.value)
        ) {

            mostrarToast(
                'Selecciona una fecha inicial y una fecha final.',
                'error'
            );

            return;
        }

        cargarHistorial();
    });


    btnLimpiar.addEventListener('click', () => {

        filtroAccion.value = '';
        filtroInstitucion.value = '';
        fechaInicio.value = '';
        fechaFin.value = '';

        sincronizarSelectsVisuales();

        cargarHistorial();
    });


    btnRecargar.addEventListener('click', cargarHistorial);

    modalCerrar.addEventListener('click', cerrarModalDetalle);
    modalCerrarFooter.addEventListener('click', cerrarModalDetalle);

    modalRevertir.addEventListener('click', abrirConfirmacion);

    confirmarCancelar.addEventListener('click', cerrarConfirmacion);
    confirmarRevertir.addEventListener('click', revertirCambio);


    modalDetalle.addEventListener('click', evento => {

        if (evento.target === modalDetalle) {
            cerrarModalDetalle();
        }
    });


    modalConfirmacion.addEventListener('click', evento => {

        if (evento.target === modalConfirmacion) {
            cerrarConfirmacion();
        }
    });


    document.addEventListener('keydown', evento => {

        if (evento.key !== 'Escape') {
            return;
        }

        if (!modalConfirmacion.classList.contains('hidden')) {
            cerrarConfirmacion();
            return;
        }

        if (!modalDetalle.classList.contains('hidden')) {
            cerrarModalDetalle();
        }
    });


    // =========================================================
    // INICIO
    // =========================================================

    cargarHistorial();

});
