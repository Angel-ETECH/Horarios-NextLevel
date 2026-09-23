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

        if (!profesor) {
            return '—';
        }

        if (profesor.nombre_completo) {
            return profesor.nombre_completo;
        }

        if (profesor.nombre && profesor.apellido) {
            return `${profesor.nombre} ${profesor.apellido}`;
        }

        return profesor.nombre
            ?? profesor.name
            ?? `Profesor #${profesor.id ?? ''}`;
    }


    function obtenerCurso(registro) {

        const curso = registro?.horario?.curso;

        return curso?.nombre
            ?? curso?.name
            ?? '—';
    }


    function obtenerGrado(registro) {

        const grado = registro?.horario?.grado;

        if (!grado) {
            return '—';
        }

        if (grado.nombre) {
            return grado.nombre;
        }

        const partes = [];

        if (grado.grado) {
            partes.push(grado.grado);
        }

        if (grado.seccion) {
            partes.push(grado.seccion);
        }

        return partes.length
            ? partes.join(' ')
            : `Grado #${grado.id ?? ''}`;
    }


    function obtenerAula(registro) {

        const aula = registro?.horario?.aula;

        return aula?.nombre
            ?? aula?.codigo
            ?? aula?.name
            ?? '—';
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

            const horario = registro.horario;

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
                            #${escaparHtml(registro.horario_id ?? '—')}
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

        const horario = registro.horario ?? {};

        modalTitulo.textContent =
            `Horario #${registro.horario_id ?? '—'}`;

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

        const accionesRevertibles = [
            'crear',
            'actualizar',
            'eliminar',
        ];

        if (accionesRevertibles.includes(registro.accion)) {
            modalRevertir.classList.remove('hidden');
            modalRevertir.classList.add('inline-flex');
        } else {
            modalRevertir.classList.add('hidden');
            modalRevertir.classList.remove('inline-flex');
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
                                    ${escaparHtml(formatearValor(item.anterior))}
                                </p>

                            </div>

                            <div class="p-4">

                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    Después
                                </p>

                                <p class="mt-1 break-words text-sm font-semibold text-slate-700">
                                    ${escaparHtml(formatearValor(item.nuevo))}
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
            profesor_id: 'Profesor',
            curso_id: 'Curso',
            aula_id: 'Aula',
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
        };

        return nombres[campo]
            ?? campo
                .replaceAll('_', ' ')
                .replace(/\b\w/g, letra => letra.toUpperCase());
    }


    function formatearValor(valor) {

        if (valor === null || valor === undefined || valor === '') {
            return '(vacío)';
        }

        if (typeof valor === 'boolean') {
            return valor ? 'Sí' : 'No';
        }

        if (typeof valor === 'object') {
            return JSON.stringify(valor);
        }

        return String(valor);
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