document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // MODAL AGREGAR AULA
    // ==========================================
    const modal = document.getElementById('aula-modal');
    const abrirModal = document.getElementById('open-aula-modal');
    const cerrarModalBtn = document.getElementById('close-aula-modal');
    const cancelarModal = document.getElementById('cancel-aula-modal');
    const overlay = document.getElementById('aula-modal-overlay');
    const formulario = document.getElementById('aula-form');

    const codigoInput = document.getElementById('aula-codigo');
    const nombreInput = document.getElementById('aula-nombre');
    const capacidadInput = document.getElementById('aula-capacidad');
    const tipoInput = document.getElementById('aula-tipo');
    const nivelInput = document.getElementById('aula-nivel');
    const equipamientoInput = document.getElementById('aula-equipamiento');
    const estadoInput = document.getElementById('aula-estado');
    const observacionesInput = document.getElementById('aula-observaciones');
    const modalTitle = document.getElementById('aula-modal-title');
    const submitButton = document.getElementById('aula-submit-button');

    let aulaEditandoId = null;

    if (!modal || !formulario) return;

    function abrirAulaModal() {
        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
        setTimeout(() => codigoInput.focus(), 100);
    }

    function cerrarAulaModal() {
        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        formulario.reset();
        aulaEditandoId = null;
        modalTitle.textContent = 'Agregar aula';
        submitButton.textContent = 'Guardar aula';
    }

    if (abrirModal) abrirModal.addEventListener('click', abrirAulaModal);
    if (cerrarModalBtn) cerrarModalBtn.addEventListener('click', cerrarAulaModal);
    if (cancelarModal) cancelarModal.addEventListener('click', cerrarAulaModal);
    if (overlay) overlay.addEventListener('click', cerrarAulaModal);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarAulaModal();
        }
    });

    // ==========================================
    // GUARDAR AULA EN LOCALSTORAGE
    // ==========================================
    formulario.addEventListener('submit', (event) => {
        event.preventDefault();

        const codigo = codigoInput.value.trim().toUpperCase();
        const nombre = nombreInput.value.trim();
        const capacidad = capacidadInput.value.trim();
        const tipo = tipoInput.value;
        const nivel = nivelInput.value || 'todos';
        const equipamiento = equipamientoInput.value.trim();
        const activo = estadoInput.value === '1';
        const observaciones = observacionesInput.value.trim();

        if (!codigo || !nombre || !capacidad || !tipo) {
            alert('Completa todos los campos obligatorios.');
            return;
        }

        let aulas = JSON.parse(localStorage.getItem('nextlevel_aulas') || '[]');

        // Verificar código duplicado
        const duplicado = aulas.find(a => a.codigo === codigo && a.id !== aulaEditandoId);
        if (duplicado) {
            alert('Ya existe un aula con este código.');
            codigoInput.focus();
            return;
        }

        const aulaData = {
            codigo,
            nombre,
            capacidad: parseInt(capacidad),
            tipo,
            nivel,
            equipamiento,
            activo,
            observaciones
        };

        if (aulaEditandoId) {
            // MODO EDICIÓN
            const index = aulas.findIndex(a => a.id === aulaEditandoId);
            if (index !== -1) {
                aulas[index] = { ...aulas[index], ...aulaData };
                localStorage.setItem('nextlevel_aulas', JSON.stringify(aulas));
                alert('✅ Aula actualizada correctamente.');
                cerrarAulaModal();
                renderizarAulas();
            }
            return;
        }

        // MODO AGREGAR
        aulaData.id = Date.now();
        aulas.push(aulaData);
        localStorage.setItem('nextlevel_aulas', JSON.stringify(aulas));

        alert('✅ Aula guardada correctamente.');
        cerrarAulaModal();
        renderizarAulas();
    });

    // ==========================================
    // RENDERIZAR AULAS
    // ==========================================
    function renderizarAulas() {
        const aulas = JSON.parse(localStorage.getItem('nextlevel_aulas') || '[]');
        const tbody = document.querySelector('table tbody');
        const emptyState = document.getElementById('aulas-empty');

        if (!tbody) return;

        const filas = tbody.querySelectorAll('.aula-row');
        filas.forEach(fila => fila.remove());

        if (aulas.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            actualizarEstadisticas();
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');

        const iconos = {
            'aula_normal': '🏫',
            'taller': '🛠️',
            'auditorio': '🎭',
            'virtual': '💻'
        };

        const colores = {
            'aula_normal': 'indigo',
            'taller': 'amber',
            'auditorio': 'purple',
            'virtual': 'cyan'
        };

        const nivelLabels = {
            'primaria': '👶 Primaria',
            'secundaria': '🧑‍🎓 Secundaria',
            'academia': '🎓 Academia',
            'todos': '📚 Todos'
        };

        const tipoLabels = {
            'aula_normal': 'Aula normal',
            'taller': 'Taller',
            'auditorio': 'Auditorio',
            'virtual': 'Virtual'
        };

        aulas.forEach((aula) => {
            const icono = iconos[aula.tipo] || '🏫';
            const color = colores[aula.tipo] || 'indigo';

            const estadoHTML = aula.activo
                ? `<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">Activo</span>`
                : `<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">Inactivo</span>`;

            const fila = document.createElement('tr');
            fila.className = 'aula-row transition hover:bg-slate-50';
            fila.dataset.nombre = aula.nombre;
            fila.dataset.estado = aula.activo ? '1' : '0';
            fila.dataset.id = aula.id;

            fila.innerHTML = `
                <td class="whitespace-nowrap px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-${color}-100 text-lg">${icono}</div>
                        <div>
                            <p class="font-medium text-slate-900">${aula.nombre}</p>
                            <p class="text-xs text-slate-500">${aula.codigo}</p>
                        </div>
                    </div>
                </td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${aula.codigo}</td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${aula.capacidad} alumnos</td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${tipoLabels[aula.tipo] || aula.tipo}</td>
                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">${nivelLabels[aula.nivel] || aula.nivel}</td>
                <td class="whitespace-nowrap px-6 py-4">${estadoHTML}</td>
                <td class="whitespace-nowrap px-6 py-4 text-right">
                    <button type="button" class="editar-aula rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                        data-id="${aula.id}"
                        data-codigo="${aula.codigo}"
                        data-nombre="${aula.nombre}"
                        data-capacidad="${aula.capacidad}"
                        data-tipo="${aula.tipo}"
                        data-nivel="${aula.nivel || 'todos'}"
                        data-equipamiento="${aula.equipamiento || ''}"
                        data-activo="${aula.activo ? '1' : '0'}"
                        data-observaciones="${aula.observaciones || ''}"
                        title="Editar">✏️</button>
                    <button type="button" class="eliminar-aula rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                        data-id="${aula.id}"
                        data-nombre="${aula.nombre}"
                        title="Eliminar">🗑️</button>
                </td>
            `;

            tbody.insertBefore(fila, emptyState);
        });

        actualizarEstadisticas();
        filtrarAulas();
    }

    // ==========================================
    // ACTUALIZAR ESTADÍSTICAS
    // ==========================================
    function actualizarEstadisticas() {
        const aulas = JSON.parse(localStorage.getItem('nextlevel_aulas') || '[]');
        const total = document.getElementById('total-aulas');
        const activas = document.getElementById('aulas-activas');
        const inactivas = document.getElementById('aulas-inactivas');
        const resultados = document.getElementById('resultados-aulas');

        const activasCount = aulas.filter(a => a.activo).length;
        const inactivasCount = aulas.filter(a => !a.activo).length;

        if (total) total.textContent = aulas.length;
        if (activas) activas.textContent = activasCount;
        if (inactivas) inactivas.textContent = inactivasCount;
        if (resultados) resultados.textContent = aulas.length;
    }

    // ==========================================
    // EDITAR AULA (delegación)
    // ==========================================
    document.addEventListener('click', (event) => {
        const boton = event.target.closest('.editar-aula');
        if (!boton) return;

        aulaEditandoId = parseInt(boton.dataset.id);

        codigoInput.value = boton.dataset.codigo;
        nombreInput.value = boton.dataset.nombre;
        capacidadInput.value = boton.dataset.capacidad;
        tipoInput.value = boton.dataset.tipo;
        nivelInput.value = boton.dataset.nivel || 'todos';
        equipamientoInput.value = boton.dataset.equipamiento || '';
        estadoInput.value = boton.dataset.activo;
        observacionesInput.value = boton.dataset.observaciones || '';

        modalTitle.textContent = 'Editar aula';
        submitButton.textContent = 'Guardar cambios';
        abrirAulaModal();
    });

    // ==========================================
    // ELIMINAR AULA
    // ==========================================
    const eliminarModal = document.getElementById('delete-aula-modal');
    if (eliminarModal) {
        const overlayEliminar = document.getElementById('delete-aula-overlay');
        const cancelEliminar = document.getElementById('cancel-delete-aula');
        const confirmEliminar = document.getElementById('confirm-delete-aula');
        const eliminarNombre = document.getElementById('delete-aula-name');

        let aulaAEliminarId = null;

        function abrirEliminarModal(nombre, id) {
            eliminarNombre.textContent = `"${nombre}"`;
            aulaAEliminarId = id;
            eliminarModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function cerrarEliminarModal() {
            eliminarModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            aulaAEliminarId = null;
        }

        if (cancelEliminar) cancelEliminar.addEventListener('click', cerrarEliminarModal);
        if (overlayEliminar) overlayEliminar.addEventListener('click', cerrarEliminarModal);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !eliminarModal.classList.contains('hidden')) {
                cerrarEliminarModal();
            }
        });

        document.addEventListener('click', (event) => {
            const boton = event.target.closest('.eliminar-aula');
            if (!boton) return;
            abrirEliminarModal(boton.dataset.nombre, parseInt(boton.dataset.id));
        });

        if (confirmEliminar) {
            confirmEliminar.addEventListener('click', () => {
                if (!aulaAEliminarId) return;
                let aulas = JSON.parse(localStorage.getItem('nextlevel_aulas') || '[]');
                aulas = aulas.filter(a => a.id !== aulaAEliminarId);
                localStorage.setItem('nextlevel_aulas', JSON.stringify(aulas));
                alert('🗑️ Aula eliminada.');
                cerrarEliminarModal();
                renderizarAulas();
            });
        }
    }

    // ==========================================
    // BÚSQUEDA Y FILTROS
    // ==========================================
    const buscador = document.getElementById('buscar-aula');
    const filtroEstado = document.getElementById('filtro-aula-estado');
    const filtroTipo = document.getElementById('filtro-aula-tipo');
    const filtroNivel = document.getElementById('filtro-aula-nivel');

    function filtrarAulas() {
        const texto = buscador ? buscador.value.toLowerCase().trim() : '';
        const estadoSeleccionado = filtroEstado ? filtroEstado.value : '';
        const tipoSeleccionado = filtroTipo ? filtroTipo.value : '';
        const nivelSeleccionado = filtroNivel ? filtroNivel.value : '';
        const filas = document.querySelectorAll('.aula-row');
        let encontrados = 0;

        filas.forEach((fila) => {
            const nombre = fila.dataset.nombre.toLowerCase();
            const estado = fila.dataset.estado;
            // Obtener datos de los botones
            const botonEditar = fila.querySelector('.editar-aula');
            const tipo = botonEditar ? botonEditar.dataset.tipo : '';
            const nivel = botonEditar ? botonEditar.dataset.nivel : '';

            const coincideNombre = nombre.includes(texto);
            const coincideEstado = estadoSeleccionado === '' || estado === estadoSeleccionado;
            const coincideTipo = tipoSeleccionado === '' || tipo === tipoSeleccionado;
            const coincideNivel = nivelSeleccionado === '' || nivel === nivelSeleccionado;

            if (coincideNombre && coincideEstado && coincideTipo && coincideNivel) {
                fila.classList.remove('hidden');
                encontrados++;
            } else {
                fila.classList.add('hidden');
            }
        });

        const resultados = document.getElementById('resultados-aulas');
        const emptyState = document.getElementById('aulas-empty');

        if (resultados) resultados.textContent = encontrados;

        if (emptyState) {
            if (encontrados === 0 && document.querySelectorAll('.aula-row').length > 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    if (buscador) buscador.addEventListener('input', filtrarAulas);
    if (filtroEstado) filtroEstado.addEventListener('change', filtrarAulas);
    if (filtroTipo) filtroTipo.addEventListener('change', filtrarAulas);
    if (filtroNivel) filtroNivel.addEventListener('change', filtrarAulas);

    // ==========================================
    // INICIALIZAR
    // ==========================================
    renderizarAulas();

    window.addEventListener('storage', (event) => {
        if (event.key === 'nextlevel_aulas') {
            renderizarAulas();
        }
    });
});