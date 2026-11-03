import { fechaActualHorario } from '../calendario-actual';

document.addEventListener('DOMContentLoaded', () => {
    const $ = id => document.getElementById(id);
    const dialog = $('recuperacion-dialog');
    if (!dialog) return;
    const form = $('recuperacion-form');
    const semana = $('horario-semana');
    semana.value = fechaActualHorario();
    const lista = $('recuperaciones-lista');
    const clase = $('recuperacion-clase');
    const opciones = $('recuperacion-opciones');
    const guardar = $('recuperacion-guardar');
    const buscar = $('recuperacion-buscar');
    const error = $('recuperacion-error');
    let horarios = [];
    let aulasDisponibles = [];
    let version = 0;
    let listadoVersion = 0;
    let busqueda = null;
    let guardando = false;
    const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const mensaje = e => Object.values(e?.response?.data?.errors || {}).flat().join(' ') || e?.response?.data?.message || e.message || 'No se pudo completar la operacion.';
    const fecha = date => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    const mostrarFecha = value => new Intl.DateTimeFormat('es-PE').format(new Date(`${String(value).slice(0, 10)}T12:00:00`));
    const mostrarHora = value => String(value).includes('T') ? String(value).slice(11, 16) : String(value).slice(0, 5);
    const nombreProfesor = r => r.original.profesor?.nombre_completo || [r.original.profesor?.nombre, r.original.profesor?.apellido_paterno, r.original.profesor?.apellido_materno].filter(Boolean).join(' ');
    const aviso = text => { error.textContent = text; error.classList.toggle('hidden', !text); };

    function invalidar() {
        version++;
        busqueda = null;
        opciones.replaceChildren();
        guardar.disabled = true;
    }

    function datos() {
        return {
            horario_id: Number(clase.value), fecha_original: $('recuperacion-original').value,
            fecha_recuperacion: $('recuperacion-fecha').value, aula_id: Number($('recuperacion-aula').value),
            buscar_desde: $('recuperacion-desde').value, buscar_hasta: $('recuperacion-hasta').value,
            disponibilidad_excepcional: $('recuperacion-excepcional').checked,
            disponible_desde: $('recuperacion-desde').value, disponible_hasta: $('recuperacion-hasta').value,
            jornada_autorizada: $('recuperacion-jornada').checked, motivo: $('recuperacion-motivo').value.trim(),
        };
    }

    function seleccionarClase() {
        invalidar();
        const h = horarios.find(item => String(item.id) === clase.value);
        if (!h) return;
        const proxima = new Date(`${semana.value || fechaActualHorario()}T12:00:00`);
        const dias = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        const dia = String(h.dia_semana).normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        proxima.setDate(proxima.getDate() + (dias.indexOf(dia) - proxima.getDay() + 7) % 7);
        $('recuperacion-original').value = h.fecha_clase || fecha(proxima);
        $('recuperacion-fecha').min = fechaActualHorario();
        $('recuperacion-fecha').value = [h.fecha_clase || fecha(proxima), fechaActualHorario()].sort().at(-1);
        const aulas = aulasDisponibles.filter(a => a.activo && [h.institucion, 'ambos'].includes(a.institucion)
            && Number(a.capacidad) >= Number(h.grado?.numero_estudiantes || 0));
        $('recuperacion-aula').replaceChildren(new Option('Seleccionar aula', ''), ...aulas.map(a => new Option(`${a.nombre} · ${a.capacidad} plazas`, a.id)));
        if (aulas.some(a => String(a.id) === String(h.aula_id))) $('recuperacion-aula').value = String(h.aula_id);
    }

    window.addEventListener('abrir-recuperacion', async event => {
        invalidar();
        aviso('');
        form.reset();
        horarios = event.detail.horarios;
        clase.replaceChildren(...horarios.map(h => new Option(`${h.curso?.nombre || 'Curso'} · ${h.grado?.nombre_completo || 'Grado'} · ${h.dia_semana} ${h.hora_inicio}-${h.hora_fin}`, h.id)));
        if (!horarios.length) return;
        dialog.showModal();
        buscar.disabled = true;
        const solicitud = ++version;
        try {
            const aulas = [];
            for (let page = 1, last = 1; page <= last; page++) {
                const response = await window.axios.get('/api/aulas', { params: { page, per_page: 100 } });
                const data = response.data;
                aulas.push(...(Array.isArray(data.data) ? data.data : data.data?.data || []));
                last = data.meta?.last_page || data.data?.last_page || 1;
            }
            if (solicitud !== version || !dialog.open) return;
            aulasDisponibles = aulas;
            seleccionarClase();
        } catch (e) { if (dialog.open) aviso(mensaje(e)); }
        finally { buscar.disabled = false; }
    });

    clase.addEventListener('change', seleccionarClase);
    form.addEventListener('input', event => {
        if (event.target.name === 'opcion-recuperacion') {
            guardar.disabled = !busqueda;
        } else invalidar();
    });
    buscar.addEventListener('click', async () => {
        invalidar();
        aviso('');
        const solicitud = version;
        const payload = datos();
        delete payload.motivo;
        buscar.disabled = true;
        try {
            const response = await window.axios.post('/api/recuperaciones/opciones', payload);
            if (solicitud !== version || !dialog.open) return;
            const result = response.data.data;
            if (!result.opciones.length) {
                aviso(Object.values(result.motivos).flat().join(' ') || 'No hay horarios disponibles en ese rango.');
                return;
            }
            busqueda = JSON.stringify(datos());
            opciones.innerHTML = `<legend class="mb-2 text-sm font-semibold">Horarios disponibles</legend>${result.opciones.map(o => `<label class="flex items-center gap-2 border-b border-slate-100 py-2 text-sm"><input type="radio" name="opcion-recuperacion" value="${esc(o.hora_inicio)}" required> ${esc(o.hora_inicio)} - ${esc(o.hora_fin)}</label>`).join('')}`;
        } catch (e) { if (solicitud === version) aviso(mensaje(e)); }
        finally { buscar.disabled = false; }
    });

    form.addEventListener('submit', async event => {
        event.preventDefault();
        const opcion = opciones.querySelector('input:checked');
        if (!opcion || !busqueda || busqueda !== JSON.stringify(datos()) || guardando) return;
        guardando = true;
        guardar.disabled = true;
        buscar.disabled = true;
        try {
            const payload = { ...datos(), hora_inicio: opcion.value };
            const response = await window.axios.post('/api/recuperaciones', payload);
            semana.value = fechaActualHorario();
            $('recuperaciones-status').textContent = `Recuperacion programada para el ${mostrarFecha(payload.fecha_recuperacion)}, ${payload.hora_inicio} - ${mostrarHora(response.data.data.hora_fin)}.`;
            dialog.close();
            semana.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (e) { aviso(mensaje(e)); guardar.disabled = false; }
        finally { guardando = false; buscar.disabled = false; }
    });
    const cerrar = () => { if (!guardando) { invalidar(); dialog.close(); } };
    $('recuperacion-cerrar').addEventListener('click', cerrar);
    $('recuperacion-volver').addEventListener('click', cerrar);
    dialog.addEventListener('cancel', event => { if (guardando) event.preventDefault(); else invalidar(); });

    async function listar() {
        const solicitud = ++listadoVersion;
        const actual = semana.value;
        $('semana-horario-label').textContent = actual ? 'Semana actual' : 'Edicion del horario base';
        $('horario-habitual').textContent = actual ? 'Editar horario base' : 'Volver a consulta';
        $('horario-edicion-habitual')?.classList.toggle('hidden', Boolean(actual));
        if (!actual) { lista.replaceChildren(); return; }
        try {
            const response = await window.axios.get('/api/recuperaciones', { params: { vigentes: 1 } });
            if (solicitud !== listadoVersion || actual !== semana.value) return;
            const rows = response.data.data;
            lista.innerHTML = rows.length ? `<h3 class="mb-2 text-sm font-semibold">Recuperaciones pendientes</h3><table class="w-full text-left text-sm"><thead><tr><th class="p-2">Clase</th><th class="p-2">Original</th><th class="p-2">Recuperaci&oacute;n</th><th class="p-2">Acciones</th></tr></thead><tbody>${rows.map(r => `<tr class="border-t border-slate-100"><td class="p-2">${esc(nombreProfesor(r))}<br>${esc(r.original.curso?.nombre)} · ${esc(r.original.grado?.nombre_completo)}</td><td class="p-2">${esc(mostrarFecha(r.fecha_original))}<br>${esc(mostrarHora(r.original.hora_inicio))}</td><td class="p-2">${esc(mostrarFecha(r.fecha_recuperacion))} ${esc(mostrarHora(r.hora_inicio))} - ${esc(mostrarHora(r.hora_fin))}<br>${esc(r.aula?.nombre)}</td><td class="p-2"><button type="button" data-cancelar-recuperacion="${Number(r.id)}" class="rounded-md border border-slate-300 px-3 py-1">Cancelar recuperaci&oacute;n</button></td></tr>`).join('')}</tbody></table>` : '';
        } catch (e) { if (solicitud === listadoVersion) lista.textContent = mensaje(e); }
    }
    const cancelarDialog = $('recuperacion-cancelar-dialog');
    const cancelarForm = $('recuperacion-cancelar-form');
    const cancelarError = $('recuperacion-cancelar-error');
    const cancelarConfirmar = $('recuperacion-cancelar-confirmar');
    let cancelarId = null;
    let cancelando = false;
    lista.addEventListener('click', event => {
        const boton = event.target.closest('[data-cancelar-recuperacion]');
        if (!boton) return;
        cancelarId = boton.dataset.cancelarRecuperacion;
        cancelarForm.reset();
        cancelarError.classList.add('hidden');
        $('recuperacion-cancelar-clase').textContent = boton.closest('tr').querySelector('td').innerText;
        cancelarDialog.showModal();
    });
    cancelarForm.addEventListener('submit', async event => {
        event.preventDefault();
        if (!cancelarId || cancelando) return;
        cancelando = true;
        cancelarConfirmar.disabled = true;
        cancelarError.classList.add('hidden');
        try {
            await window.axios.post(`/api/recuperaciones/${cancelarId}/cancelar`, { motivo: $('recuperacion-cancelar-motivo').value.trim() });
            cancelarDialog.close();
            $('recuperaciones-status').textContent = 'Recuperacion cancelada. Clase original restablecida.';
            semana.dispatchEvent(new Event('change', { bubbles: true }));
        } catch (e) { cancelarError.textContent = mensaje(e); cancelarError.classList.remove('hidden'); }
        finally { cancelando = false; cancelarConfirmar.disabled = false; }
    });
    $('recuperacion-cancelar-volver').addEventListener('click', () => { if (!cancelando) cancelarDialog.close(); });
    cancelarDialog.addEventListener('cancel', event => { if (cancelando) event.preventDefault(); });
    semana.addEventListener('change', () => { invalidar(); listar(); window.dispatchEvent(new Event('agenda-cambiada')); });
    $('horario-habitual').addEventListener('click', () => { semana.value = semana.value ? '' : fechaActualHorario(); semana.dispatchEvent(new Event('change')); });
    function sincronizarConsultaActual() {
        if (semana.value && semana.value !== fechaActualHorario()) {
            semana.value = fechaActualHorario();
            semana.dispatchEvent(new Event('change'));
        }
    }
    window.addEventListener('focus', sincronizarConsultaActual);
    window.setInterval(sincronizarConsultaActual, 60000);
    listar();
});
