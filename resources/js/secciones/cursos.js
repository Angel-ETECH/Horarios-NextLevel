document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // MODAL AGREGAR CURSO
    // ==========================================
    const modal = document.getElementById('curso-modal');
    const abrirModal = document.getElementById('open-curso-modal');
    const cerrarModalBtn = document.getElementById('close-curso-modal');
    const cancelarModal = document.getElementById('cancel-curso-modal');
    const overlay = document.getElementById('curso-modal-overlay');
    const formulario = document.getElementById('curso-form');

    const codigoInput = document.getElementById('curso-codigo');
    const nombreInput = document.getElementById('curso-nombre');
    const descripcionInput = document.getElementById('curso-descripcion');
    const nivelInput = document.getElementById('curso-nivel');
    const tipoInput = document.getElementById('curso-tipo');
    const horasInput = document.getElementById('curso-horas');
    const duracionInput = document.getElementById('curso-duracion');
    const colorInput = document.getElementById('curso-color');
    const estadoInput = document.getElementById('curso-estado');
    const observacionesInput = document.getElementById('curso-observaciones');
    const modalTitle = document.getElementById('curso-modal-title');
    const submitButton = document.getElementById('curso-submit-button');

    let cursoEditandoId = null;

    if (!modal || !formulario) return;

    function abrirCursoModal() {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => codigoInput.focus(), 100);
    }

    function cerrarCursoModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        formulario.reset();
        cursoEditandoId = null;
        modalTitle.textContent = 'Agregar curso';
        submitButton.textContent = 'Guardar curso';
        if (colorInput) colorInput.value = '#3490dc';
        const preview = document.getElementById('curso-color-preview');
        if (preview) preview.textContent = '#3490dc';
    }

    if (abrirModal) abrirModal.addEventListener('click', abrirCursoModal);
    if (cerrarModalBtn) cerrarModalBtn.addEventListener('click', cerrarCursoModal);
    if (cancelarModal) cancelarModal.addEventListener('click', cerrarCursoModal);
    if (overlay) overlay.addEventListener('click', cerrarCursoModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarCursoModal();
        }
    });

    // ==========================================
    // GUARDAR CURSO EN LOCALSTORAGE
    // ==========================================
    formulario.addEventListener('submit', (event) => {
        event.preventDefault();

        const codigo = codigoInput.value.trim().toUpperCase();
        const nombre = nombreInput.value.trim();
        const descripcion = descripcionInput.value.trim();
        const nivel = nivelInput.value;
        const tipo = tipoInput.value;
        const horas = horasInput.value;
        const duracion = duracionInput.value || 60;
        const color = colorInput.value || '#3490dc';
        const activo = estadoInput.value === '1';
        const observaciones = observacionesInput.value.trim();

        if (!codigo || !nombre || !nivel || !tipo || !horas) {
            alert('Completa todos los campos obligatorios.');
            return;
        }

        let cursos = JSON.parse(localStorage.getItem('nextlevel_cursos') || '[]');

        const duplicado = cursos.find(c => c.codigo === codigo && c.id !== cursoEditandoId);
        if (duplicado) {
            alert('Ya existe un curso con este código.');
            codigoInput.focus();
            return;
        }

        const cursoData = {
            codigo,
            nombre,
            descripcion,
            nivel,
            tipo,
            horas_semanales: parseInt(horas),
            duracion_minutos: parseInt(duracion),
            color,
            activo,
            observaciones
        };

        if (cursoEditandoId) {
            const index = cursos.findIndex(c => c.id === cursoEditandoId);
            if (index !== -1) {
                cursos[index] = { ...cursos[index], ...cursoData };
                localStorage.setItem('nextlevel_cursos', JSON.stringify(cursos));
                alert('✅ Curso actualizado correctamente.');
                cerrarCursoModal();
                renderizarCursos();
            }
            return;
        }

        cursoData.id = Date.now();
        cursos.push(cursoData);
        localStorage.setItem('nextlevel_cursos', JSON.stringify(cursos));

        alert('✅ Curso guardado correctamente.');
        cerrarCursoModal();
        renderizarCursos();
    });

    // ==========================================
    // RENDERIZAR CURSOS
    // ==========================================
    function renderizarCursos() {
        const cursos = JSON.parse(localStorage.getItem('nextlevel_cursos') || '[]');
        const tbody = document.querySelector('table tbody');
        const emptyState = document.getElementById('cursos-empty');

        if (!tbody) return;

        const filas = tbody.querySelectorAll('.curso-row');
        filas.forEach(fila => fila.remove());

        if (cursos.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            actualizarEstadisticas();
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');

        const iconos = ['📐', '📝', '🌎', '⚛️', '🧪', '📖', '🎨', '💻', '🎵'];
        const colores = ['indigo', 'purple', 'blue', 'orange', 'pink', 'yellow', 'teal', 'rose', 'cyan'];

        const nivelLabels = {
            'primaria': '👶 Primaria',
            'secundaria': '🧑‍🎓 Secundaria',
            'academia': '🎓 Academia',
            'todos': '📚 Todos'
        };

        const tipoLabels = {
            'obligatorio': 'Obligatorio',
            'electivo': 'Electivo',
            'taller': 'Taller'
        };

        cursos.forEach((curso, index) => {
            const icono = iconos[index % iconos.length];
            const color = colores[index % colores.length];

            const estadoHTML = curso.activo
                ? `<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">Activo</span>`
                : `<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactivo</span>`;

            const fila = document.createElement('tr');
            fila.className = 'curso-row transition hover:bg-slate-50';
            fila.dataset.nombre = curso.nombre;
            fila.dataset.estado = curso.activo ? '1' : '0';
            fila.dataset.id = curso.id;

            fila.innerHTML = `
                <td class="whitespace-nowrap px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-${color}-100 text-lg">${icono}</div>
                        <div>
                            <p class="font-medium text-slate-900">${curso.nombre}</p>
                            <p class="text-xs text-slate-500">${curso.codigo}</p>
                        </div>
                    </div>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${curso.codigo}</td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${nivelLabels[curso.nivel] || curso.nivel}</td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${tipoLabels[curso.tipo] || curso.tipo}</td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${curso.horas_semanales} h</td>
                <td class="whitespace-nowrap px-6 py-4">${estadoHTML}</td>
                <td class="whitespace-nowrap px-6 py-4 text-right">
                    <button type="button" class="editar-curso rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                        data-id="${curso.id}"
                        data-codigo="${curso.codigo}"
                        data-nombre="${curso.nombre}"
                        data-descripcion="${curso.descripcion || ''}"
                        data-nivel="${curso.nivel}"
                        data-tipo="${curso.tipo}"
                        data-horas="${curso.horas_semanales}"
                        data-duracion="${curso.duracion_minutos || 60}"
                        data-color="${curso.color || '#3490dc'}"
                        data-activo="${curso.activo ? '1' : '0'}"
                        data-observaciones="${curso.observaciones || ''}"
                        title="Editar">✏️</button>
                    <button type="button" class="eliminar-curso rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                        data-id="${curso.id}"
                        data-nombre="${curso.nombre}"
                        title="Eliminar">🗑️</button>
                </td>
            `;

            tbody.insertBefore(fila, emptyState);
        });

        actualizarEstadisticas();
        filtrarCursos();
    }

    // ==========================================
    // ACTUALIZAR ESTADÍSTICAS
    // ==========================================
    function actualizarEstadisticas() {
        const cursos = JSON.parse(localStorage.getItem('nextlevel_cursos') || '[]');
        const total = document.getElementById('total-cursos');
        const activos = document.getElementById('cursos-activos');
        const inactivos = document.getElementById('cursos-inactivos');
        const totalResultados = document.getElementById('total-resultados-cursos');

        const activosCount = cursos.filter(c => c.activo).length;
        const inactivosCount = cursos.filter(c => !c.activo).length;

        if (total) total.textContent = cursos.length;
        if (activos) activos.textContent = activosCount;
        if (inactivos) inactivos.textContent = inactivosCount;
        if (totalResultados) totalResultados.textContent = cursos.length;
    }

    // ==========================================
    // EDITAR CURSO (delegación)
    // ==========================================
    document.addEventListener('click', (event) => {
        const boton = event.target.closest('.editar-curso');
        if (!boton) return;

        cursoEditandoId = parseInt(boton.dataset.id);

        codigoInput.value = boton.dataset.codigo;
        nombreInput.value = boton.dataset.nombre;
        descripcionInput.value = boton.dataset.descripcion || '';
        nivelInput.value = boton.dataset.nivel;
        tipoInput.value = boton.dataset.tipo;
        horasInput.value = boton.dataset.horas;
        duracionInput.value = boton.dataset.duracion || 60;
        colorInput.value = boton.dataset.color || '#3490dc';
        estadoInput.value = boton.dataset.activo;
        observacionesInput.value = boton.dataset.observaciones || '';

        const preview = document.getElementById('curso-color-preview');
        if (preview) preview.textContent = colorInput.value;

        modalTitle.textContent = 'Editar curso';
        submitButton.textContent = 'Guardar cambios';
        abrirCursoModal();
    });

    // ==========================================
    // ELIMINAR CURSO
    // ==========================================
    const eliminarModal = document.getElementById('eliminar-curso-modal');
    if (eliminarModal) {
        const overlayEliminar = document.getElementById('eliminar-curso-modal-overlay');
        const closeEliminar = document.getElementById('close-eliminar-curso-modal');
        const cancelEliminar = document.getElementById('cancel-eliminar-curso-modal');
        const confirmEliminar = document.getElementById('confirm-eliminar-curso');
        const eliminarNombre = document.getElementById('eliminar-curso-nombre');

        let cursoAEliminarId = null;

        function abrirEliminarModal(nombre, id) {
            eliminarNombre.textContent = `"${nombre}"`;
            cursoAEliminarId = id;
            eliminarModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function cerrarEliminarModal() {
            eliminarModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            cursoAEliminarId = null;
        }

        if (closeEliminar) closeEliminar.addEventListener('click', cerrarEliminarModal);
        if (cancelEliminar) cancelEliminar.addEventListener('click', cerrarEliminarModal);
        if (overlayEliminar) overlayEliminar.addEventListener('click', cerrarEliminarModal);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !eliminarModal.classList.contains('hidden')) {
                cerrarEliminarModal();
            }
        });

        document.addEventListener('click', (event) => {
            const boton = event.target.closest('.eliminar-curso');
            if (!boton) return;
            abrirEliminarModal(boton.dataset.nombre, parseInt(boton.dataset.id));
        });

        if (confirmEliminar) {
            confirmEliminar.addEventListener('click', () => {
                if (!cursoAEliminarId) return;
                let cursos = JSON.parse(localStorage.getItem('nextlevel_cursos') || '[]');
                cursos = cursos.filter(c => c.id !== cursoAEliminarId);
                localStorage.setItem('nextlevel_cursos', JSON.stringify(cursos));
                alert('🗑️ Curso eliminado.');
                cerrarEliminarModal();
                renderizarCursos();
            });
        }
    }

    // ==========================================
    // BÚSQUEDA Y FILTRO
    // ==========================================
    const buscador = document.getElementById('buscar-curso');
    const filtro = document.getElementById('filtro-curso-estado');

    function filtrarCursos() {
        const texto = buscador ? buscador.value.toLowerCase().trim() : '';
        const estadoSeleccionado = filtro ? filtro.value : '';
        const filas = document.querySelectorAll('.curso-row');
        let encontrados = 0;

        filas.forEach((fila) => {
            const nombre = fila.dataset.nombre.toLowerCase();
            const estado = fila.dataset.estado;
            const coincideNombre = nombre.includes(texto);
            const coincideEstado = estadoSeleccionado === '' || estado === estadoSeleccionado;

            if (coincideNombre && coincideEstado) {
                fila.classList.remove('hidden');
                encontrados++;
            } else {
                fila.classList.add('hidden');
            }
        });

        const resultados = document.getElementById('resultados-cursos');
        const emptyState = document.getElementById('cursos-empty');

        if (resultados) resultados.textContent = encontrados;

        if (emptyState) {
            if (encontrados === 0 && document.querySelectorAll('.curso-row').length > 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    if (buscador) buscador.addEventListener('input', filtrarCursos);
    if (filtro) filtro.addEventListener('change', filtrarCursos);

    // ==========================================
    // INICIALIZAR
    // ==========================================
    renderizarCursos();

    window.addEventListener('storage', (event) => {
        if (event.key === 'nextlevel_cursos') {
            renderizarCursos();
        }
    });
});