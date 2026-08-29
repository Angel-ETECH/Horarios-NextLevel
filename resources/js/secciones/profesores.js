document.addEventListener('DOMContentLoaded', () => {
    // ==========================================
    // MODAL AGREGAR PROFESOR
    // ==========================================
    const profesorModal = document.getElementById('profesor-modal');
    if (profesorModal) {
        const openProfesorModal = document.getElementById('open-profesor-modal');
        const closeProfesorModal = document.getElementById('close-profesor-modal');
        const cancelProfesorModal = document.getElementById('cancel-profesor-modal');
        const profesorModalOverlay = document.getElementById('profesor-modal-overlay');
        const profesorForm = document.getElementById('profesor-form');

        function abrirProfesorModal() {
            profesorModal.classList.remove('hidden');
            profesorModal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('overflow-hidden');
            setTimeout(() => {
                const codigo = document.getElementById('codigo');
                if (codigo) codigo.focus();
            }, 100);
        }

        function cerrarProfesorModal() {
            profesorModal.classList.add('hidden');
            profesorModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('overflow-hidden');
            if (profesorForm) profesorForm.reset();
        }

        if (openProfesorModal) openProfesorModal.addEventListener('click', abrirProfesorModal);
        if (closeProfesorModal) closeProfesorModal.addEventListener('click', cerrarProfesorModal);
        if (cancelProfesorModal) cancelProfesorModal.addEventListener('click', cerrarProfesorModal);
        if (profesorModalOverlay) profesorModalOverlay.addEventListener('click', cerrarProfesorModal);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !profesorModal.classList.contains('hidden')) {
                cerrarProfesorModal();
            }
        });

        // ==========================================
        // GUARDAR EN LOCALSTORAGE
        // ==========================================
        if (profesorForm) {
            profesorForm.addEventListener('submit', (event) => {
                event.preventDefault();

                const codigo = document.getElementById('codigo').value.trim().toUpperCase();
                const nombre = document.getElementById('nombre').value.trim();
                const apellido_paterno = document.getElementById('apellido_paterno').value.trim();
                const apellido_materno = document.getElementById('apellido_materno').value.trim();
                const dni = document.getElementById('dni').value.trim();
                const email = document.getElementById('email').value.trim();
                const telefono = document.getElementById('telefono').value.trim();
                const sexo = document.getElementById('sexo').value;
                const fecha_nacimiento = document.getElementById('fecha_nacimiento').value;
                const especialidad = document.getElementById('especialidad').value;
                const carga_horaria_maxima = document.getElementById('carga_horaria_maxima').value || 30;
                const estado = document.getElementById('estado').value;
                const observaciones = document.getElementById('observaciones').value.trim();

                const instituciones = Array.from(
                    document.querySelectorAll('input[name="instituciones[]"]:checked')
                ).map(el => el.value);

                if (!codigo || !nombre || !apellido_paterno || !dni || !email) {
                    alert('Completa todos los campos obligatorios.');
                    return;
                }

                if (instituciones.length === 0) {
                    alert('Selecciona al menos una institución.');
                    return;
                }

                const profesor = {
                    id: Date.now(),
                    codigo,
                    nombre,
                    apellido_paterno,
                    apellido_materno,
                    dni,
                    email,
                    telefono,
                    sexo,
                    fecha_nacimiento,
                    especialidad,
                    carga_horaria_maxima: parseInt(carga_horaria_maxima),
                    estado,
                    observaciones,
                    instituciones
                };

                const profesores = JSON.parse(localStorage.getItem('nextlevel_profesores') || '[]');
                profesores.push(profesor);
                localStorage.setItem('nextlevel_profesores', JSON.stringify(profesores));

                alert('✅ Profesor guardado correctamente.');
                cerrarProfesorModal();
                renderizarProfesores();
            });
        }
    }

    // ==========================================
    // RENDERIZAR PROFESORES
    // ==========================================
    function renderizarProfesores() {
        const profesores = JSON.parse(localStorage.getItem('nextlevel_profesores') || '[]');
        const tbody = document.querySelector('table tbody');
        const emptyState = document.getElementById('profesores-empty');

        if (!tbody) return;

        const filas = tbody.querySelectorAll('.profesor-row');
        filas.forEach(fila => fila.remove());

        if (profesores.length === 0) {
            if (emptyState) emptyState.classList.remove('hidden');
            actualizarEstadisticas();
            return;
        }

        if (emptyState) emptyState.classList.add('hidden');

        const colores = ['indigo', 'emerald', 'amber', 'violet', 'blue', 'rose', 'cyan'];

        profesores.forEach((prof, index) => {
            const iniciales = (prof.nombre.charAt(0) + prof.apellido_paterno.charAt(0)).toUpperCase();
            const color = colores[index % colores.length];

            const estadoLabels = {
                'activo': 'Activo',
                'inactivo': 'Inactivo',
                'licencia': 'Licencia'
            };

            const estadoColors = {
                'activo': 'bg-emerald-100 text-emerald-700',
                'inactivo': 'bg-slate-100 text-slate-600',
                'licencia': 'bg-amber-100 text-amber-700'
            };

            const estadoHTML = `
                <span class="inline-flex rounded-full ${estadoColors[prof.estado] || 'bg-slate-100 text-slate-600'} px-2.5 py-1 text-xs font-medium">
                    ${estadoLabels[prof.estado] || prof.estado}
                </span>
            `;

            const fila = document.createElement('tr');
            fila.className = 'profesor-row transition hover:bg-slate-50';
            fila.dataset.nombre = `${prof.nombre} ${prof.apellido_paterno}`;
            fila.dataset.estado = prof.estado;
            fila.dataset.id = prof.id;

            fila.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-${color}-100 font-semibold text-${color}-700">
                            ${iniciales}
                        </div>
                        <div>
                            <p class="font-medium text-slate-900">${prof.nombre} ${prof.apellido_paterno}</p>
                            <p class="text-sm text-slate-500">${prof.email}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600">${prof.dni}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${prof.especialidad || '—'}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${prof.telefono || '—'}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${prof.carga_horaria_maxima || '30'} h</td>
                <td class="px-6 py-4">${estadoHTML}</td>
                <td class="px-6 py-4">
                    <div class="flex justify-end gap-2">
                        <button type="button" class="editar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                            data-id="${prof.id}"
                            data-codigo="${prof.codigo}"
                            data-nombre="${prof.nombre}"
                            data-apellido_paterno="${prof.apellido_paterno}"
                            data-apellido_materno="${prof.apellido_materno || ''}"
                            data-dni="${prof.dni}"
                            data-email="${prof.email}"
                            data-telefono="${prof.telefono || ''}"
                            data-sexo="${prof.sexo || ''}"
                            data-fecha_nacimiento="${prof.fecha_nacimiento || ''}"
                            data-especialidad="${prof.especialidad || ''}"
                            data-carga_horaria_maxima="${prof.carga_horaria_maxima || 30}"
                            data-estado="${prof.estado}"
                            data-observaciones="${prof.observaciones || ''}"
                            title="Editar">✏️</button>
                        <button type="button" class="eliminar-profesor rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                            data-id="${prof.id}"
                            data-nombre="${prof.nombre} ${prof.apellido_paterno}"
                            title="Eliminar">🗑️</button>
                    </div>
                </td>
            `;

            tbody.insertBefore(fila, emptyState);
        });

        actualizarEstadisticas();
        actualizarContadorResultados();
    }

    // ==========================================
    // ACTUALIZAR ESTADÍSTICAS
    // ==========================================
    function actualizarEstadisticas() {
        const profesores = JSON.parse(localStorage.getItem('nextlevel_profesores') || '[]');
        const total = document.getElementById('total-profesores');
        const activos = document.getElementById('profesores-activos');
        const inactivos = document.getElementById('profesores-inactivos');

        const activosCount = profesores.filter(p => p.estado === 'activo').length;
        const inactivosCount = profesores.filter(p => p.estado === 'inactivo' || p.estado === 'licencia').length;

        if (total) total.textContent = profesores.length;
        if (activos) activos.textContent = activosCount;
        if (inactivos) inactivos.textContent = inactivosCount;
    }

    function actualizarContadorResultados() {
        const profesores = JSON.parse(localStorage.getItem('nextlevel_profesores') || '[]');
        const resultados = document.getElementById('resultados-profesores');
        const totalResultados = document.getElementById('total-resultados-profesores');
        if (resultados) resultados.textContent = profesores.length;
        if (totalResultados) totalResultados.textContent = profesores.length;
    }

    // ==========================================
    // EDITAR PROFESOR
    // ==========================================
    const editarModal = document.getElementById('editar-profesor-modal');
    if (editarModal) {
        const closeEditar = document.getElementById('close-editar-modal');
        const cancelEditar = document.getElementById('cancel-editar-modal');
        const overlayEditar = document.getElementById('editar-modal-overlay');
        const editarForm = document.getElementById('editar-profesor-form');

        const editId = document.getElementById('editar-id');
        const editCodigo = document.getElementById('editar-codigo');
        const editNombre = document.getElementById('editar-nombre');
        const editApellidoPaterno = document.getElementById('editar-apellido_paterno');
        const editApellidoMaterno = document.getElementById('editar-apellido_materno');
        const editDni = document.getElementById('editar-dni');
        const editEmail = document.getElementById('editar-email');
        const editTelefono = document.getElementById('editar-telefono');
        const editSexo = document.getElementById('editar-sexo');
        const editFechaNacimiento = document.getElementById('editar-fecha_nacimiento');
        const editEspecialidad = document.getElementById('editar-especialidad');
        const editCargaHoraria = document.getElementById('editar-carga_horaria_maxima');
        const editEstado = document.getElementById('editar-estado');
        const editObservaciones = document.getElementById('editar-observaciones');

        let profesorEditandoId = null;

        function abrirEditarModal(datos) {
            editId.value = datos.id;
            editCodigo.value = datos.codigo;
            editNombre.value = datos.nombre;
            editApellidoPaterno.value = datos.apellido_paterno;
            editApellidoMaterno.value = datos.apellido_materno || '';
            editDni.value = datos.dni;
            editEmail.value = datos.email;
            editTelefono.value = datos.telefono || '';
            editSexo.value = datos.sexo || '';
            editFechaNacimiento.value = datos.fecha_nacimiento || '';
            editEspecialidad.value = datos.especialidad || '';
            editCargaHoraria.value = datos.carga_horaria_maxima || 30;
            editEstado.value = datos.estado;
            editObservaciones.value = datos.observaciones || '';
            profesorEditandoId = datos.id;
            editarModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function cerrarEditarModal() {
            editarModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            profesorEditandoId = null;
            if (editarForm) editarForm.reset();
        }

        if (closeEditar) closeEditar.addEventListener('click', cerrarEditarModal);
        if (cancelEditar) cancelEditar.addEventListener('click', cerrarEditarModal);
        if (overlayEditar) overlayEditar.addEventListener('click', cerrarEditarModal);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !editarModal.classList.contains('hidden')) {
                cerrarEditarModal();
            }
        });

        document.addEventListener('click', (event) => {
            const boton = event.target.closest('.editar-profesor');
            if (!boton) return;

            abrirEditarModal({
                id: parseInt(boton.dataset.id),
                codigo: boton.dataset.codigo,
                nombre: boton.dataset.nombre,
                apellido_paterno: boton.dataset.apellido_paterno,
                apellido_materno: boton.dataset.apellido_materno || '',
                dni: boton.dataset.dni,
                email: boton.dataset.email,
                telefono: boton.dataset.telefono || '',
                sexo: boton.dataset.sexo || '',
                fecha_nacimiento: boton.dataset.fecha_nacimiento || '',
                especialidad: boton.dataset.especialidad || '',
                carga_horaria_maxima: parseInt(boton.dataset.carga_horaria_maxima) || 30,
                estado: boton.dataset.estado,
                observaciones: boton.dataset.observaciones || ''
            });
        });

        if (editarForm) {
            editarForm.addEventListener('submit', (event) => {
                event.preventDefault();

                const profesores = JSON.parse(localStorage.getItem('nextlevel_profesores') || '[]');
                const index = profesores.findIndex(p => p.id === profesorEditandoId);

                if (index !== -1) {
                    profesores[index] = {
                        ...profesores[index],
                        nombre: editNombre.value.trim(),
                        apellido_paterno: editApellidoPaterno.value.trim(),
                        apellido_materno: editApellidoMaterno.value.trim(),
                        dni: editDni.value.trim(),
                        email: editEmail.value.trim(),
                        telefono: editTelefono.value.trim(),
                        sexo: editSexo.value,
                        fecha_nacimiento: editFechaNacimiento.value,
                        especialidad: editEspecialidad.value,
                        carga_horaria_maxima: parseInt(editCargaHoraria.value) || 30,
                        estado: editEstado.value,
                        observaciones: editObservaciones.value.trim()
                    };

                    localStorage.setItem('nextlevel_profesores', JSON.stringify(profesores));
                    alert('✅ Profesor actualizado correctamente.');
                    cerrarEditarModal();
                    renderizarProfesores();
                }
            });
        }
    }

    // ==========================================
    // ELIMINAR PROFESOR
    // ==========================================
    const eliminarModal = document.getElementById('eliminar-profesor-modal');
    if (eliminarModal) {
        const overlayEliminar = document.getElementById('eliminar-profesor-modal-overlay');
        const closeEliminar = document.getElementById('close-eliminar-profesor-modal');
        const cancelEliminar = document.getElementById('cancel-eliminar-profesor-modal');
        const confirmEliminar = document.getElementById('confirm-eliminar-profesor');
        const eliminarNombre = document.getElementById('eliminar-profesor-nombre');

        let profesorAEliminarId = null;

        function abrirEliminarModal(nombre, id) {
            eliminarNombre.textContent = `"${nombre}"`;
            profesorAEliminarId = id;
            eliminarModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function cerrarEliminarModal() {
            eliminarModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            profesorAEliminarId = null;
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
            const boton = event.target.closest('.eliminar-profesor');
            if (!boton) return;
            abrirEliminarModal(boton.dataset.nombre, parseInt(boton.dataset.id));
        });

        if (confirmEliminar) {
            confirmEliminar.addEventListener('click', () => {
                if (!profesorAEliminarId) return;
                let profesores = JSON.parse(localStorage.getItem('nextlevel_profesores') || '[]');
                profesores = profesores.filter(p => p.id !== profesorAEliminarId);
                localStorage.setItem('nextlevel_profesores', JSON.stringify(profesores));
                alert('🗑️ Profesor eliminado.');
                cerrarEliminarModal();
                renderizarProfesores();
            });
        }
    }

    // ==========================================
    // BÚSQUEDA Y FILTRO
    // ==========================================
    const buscador = document.getElementById('buscar-profesor');
    const filtroEstado = document.getElementById('filtro-estado');

    function filtrarProfesores() {
        const texto = buscador ? buscador.value.toLowerCase().trim() : '';
        const estadoSeleccionado = filtroEstado ? filtroEstado.value : '';
        const filas = document.querySelectorAll('.profesor-row');
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

        const resultados = document.getElementById('resultados-profesores');
        const emptyState = document.getElementById('profesores-empty');

        if (resultados) resultados.textContent = encontrados;

        if (emptyState) {
            if (encontrados === 0 && document.querySelectorAll('.profesor-row').length > 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }
    }

    if (buscador) buscador.addEventListener('input', filtrarProfesores);
    if (filtroEstado) filtroEstado.addEventListener('change', filtrarProfesores);

    // ==========================================
    // INICIALIZAR
    // ==========================================
    renderizarProfesores();

    window.addEventListener('storage', (event) => {
        if (event.key === 'nextlevel_profesores') {
            renderizarProfesores();
        }
    });
});