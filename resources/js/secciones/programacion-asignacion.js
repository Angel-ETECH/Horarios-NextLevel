const dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
export const claveBloque = b => `${b.dia_semana}|${b.hora_inicio}|${b.hora_fin}`;

export function iniciarProgramacionAsignacion({ contexto, controles, horas }) {
    const contenedor = document.getElementById('asignacion-programacion');
    if (!contenedor) return { payload: () => ({}), validar: () => true, cargar: () => {}, limpiar: () => {} };
    const panel = contenedor.querySelector('[data-panel-manual]');
    const lista = contenedor.querySelector('[data-bloques-manuales]');
    const resumen = contenedor.querySelector('[data-resumen-manual]');
    const radios = [...contenedor.querySelectorAll('[name="modo-programacion"]')];
    let seleccion = new Map();
    let opciones = new Map();
    let version = 0;
    let listo = false;
    const modo = () => radios.find(r => r.checked)?.value || 'automatico';
    const actualizarResumen = () => {
        resumen.textContent = `${seleccion.size} de ${Number(horas.value) || 0} bloques seleccionados. Puedes elegir varios seguidos en el mismo día.`;
    };
    function dibujar() {
        lista.replaceChildren();
        for (const dia of dias) {
            const bloques = [...opciones.values()].filter(b => b.dia_semana === dia);
            if (!bloques.length) continue;
            const grupo = document.createElement('fieldset');
            grupo.className = 'rounded-xl border border-slate-200 p-3';
            const titulo = document.createElement('legend');
            titulo.className = 'px-2 text-sm font-semibold capitalize';
            titulo.textContent = dia;
            grupo.append(titulo);
            for (const b of bloques) {
                const clave = claveBloque(b);
                const label = document.createElement('label');
                label.className = 'flex items-center gap-2 py-2 text-sm';
                const check = document.createElement('input');
                check.type = 'checkbox';
                check.checked = seleccion.has(clave);
                check.disabled = !b.disponible && !check.checked;
                check.addEventListener('change', () => {
                    if (check.checked) seleccion.set(clave, b);
                    else seleccion.delete(clave);
                    actualizarResumen();
                });
                const texto = document.createElement('span');
                texto.textContent = `${b.hora_inicio} – ${b.hora_fin}${b.disponible ? '' : ' · '+(b.motivo || 'Ya no disponible')}`;
                if (!b.disponible) texto.className = 'text-slate-500';
                label.append(check, texto);
                grupo.append(label);
            }
            lista.append(grupo);
        }
        if (!opciones.size) lista.textContent = 'No hay bloques disponibles. Revisa la disponibilidad del profesor y el turno del grado.';
        actualizarResumen();
    }
    async function consultar() {
        const consulta = ++version;
        panel.hidden = modo() !== 'manual';
        listo = false;
        if (panel.hidden) return;
        const params = contexto();
        if (!params.profesor_id || !params.curso_id || !params.grado_id || !params.aula_id || !params.institucion) {
            lista.textContent = 'Selecciona profesor, institución, curso, grado y aula para ver los horarios.';
            actualizarResumen();
            return;
        }
        lista.textContent = 'Comprobando horarios disponibles…';
        try {
            const { data } = await window.axios.get('/api/asignaciones/opciones-horario', { params });
            if (consulta !== version) return;
            opciones = new Map(data.data.map(b => [claveBloque(b), b]));
            for (const [clave, bloque] of seleccion) {
                if (!opciones.has(clave)) opciones.set(clave, { ...bloque, disponible: false });
            }
            listo = true;
            dibujar();
        } catch (error) {
            if (consulta !== version) return;
            lista.textContent = error.response?.data?.message || 'No se pudieron comprobar los horarios. Vuelve a consultar.';
        }
    }
    radios.forEach(r => r.addEventListener('change', consultar));
    controles.forEach(c => c.addEventListener('change', () => { seleccion.clear(); void consultar(); }));
    horas.addEventListener('input', actualizarResumen);
    contenedor.querySelector('[data-actualizar-bloques]').addEventListener('click', consultar);
    return {
        payload: () => ({ modo_programacion: modo(), bloques_manuales: modo() === 'manual'
            ? [...seleccion.values()].map(({ dia_semana, hora_inicio, hora_fin }) => ({ dia_semana, hora_inicio, hora_fin })) : null }),
        validar: () => {
            if (modo() !== 'manual') return true;
            if (!listo || seleccion.size !== Number(horas.value) || [...seleccion.keys()].some(k => !opciones.get(k)?.disponible)) {
                alert('Selecciona un bloque disponible por cada hora lectiva. Revisa el contador y los horarios marcados.');
                return false;
            }
            return true;
        },
        cargar: asignacion => {
            seleccion = new Map((asignacion.bloques_manuales || []).map(b => [claveBloque(b), b]));
            radios.forEach(r => { r.checked = r.value === (asignacion.modo_programacion || 'automatico'); });
            void consultar();
        },
        limpiar: () => {
            ++version;
            seleccion.clear(); opciones.clear(); listo = false;
            radios.forEach(r => { r.checked = r.value === 'automatico'; });
            panel.hidden = true; lista.replaceChildren(); actualizarResumen();
        },
    };
}
