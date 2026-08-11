import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

document.addEventListener('DOMContentLoaded', () => {

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    const openButton = document.getElementById('open-sidebar');
    const closeButton = document.getElementById('close-sidebar');

    const sidebarLinks = document.querySelectorAll('.sidebar-link');


    function openSidebar() {

        sidebar.classList.remove('-translate-x-full');

        overlay.classList.remove('hidden');

    }


    function closeSidebar() {

        sidebar.classList.add('-translate-x-full');

        overlay.classList.add('hidden');

    }


    if (openButton) {

        openButton.addEventListener('click', openSidebar);

    }


    if (closeButton) {

        closeButton.addEventListener('click', closeSidebar);

    }


    if (overlay) {

        overlay.addEventListener('click', closeSidebar);

    }


    sidebarLinks.forEach((link) => {

        link.addEventListener('click', () => {

            if (window.innerWidth < 1024) {

                closeSidebar();

            }

        });

    });


    window.addEventListener('resize', () => {

        if (window.innerWidth >= 1024) {

            sidebar.classList.remove('-translate-x-full');

            overlay.classList.add('hidden');

        } else {

            sidebar.classList.add('-translate-x-full');

        }

    });

});

// ==========================================
// MODAL DE PROFESORES
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const profesorModal = document.getElementById('profesor-modal');

    const openProfesorModal = document.getElementById('open-profesor-modal');

    const closeProfesorModal = document.getElementById('close-profesor-modal');

    const cancelProfesorModal = document.getElementById('cancel-profesor-modal');

    const profesorModalOverlay = document.getElementById('profesor-modal-overlay');

    const profesorForm = document.getElementById('profesor-form');


    // Si no estamos en la página de profesores,
    // no hacemos nada.

    if (!profesorModal) {
        return;
    }


    // ABRIR MODAL

    function abrirProfesorModal() {

        profesorModal.classList.remove('hidden');

        profesorModal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('overflow-hidden');

    }


    // CERRAR MODAL

    function cerrarProfesorModal() {

        profesorModal.classList.add('hidden');

        profesorModal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('overflow-hidden');

    }


    // BOTÓN AGREGAR

    if (openProfesorModal) {

        openProfesorModal.addEventListener('click', abrirProfesorModal);

    }


    // BOTÓN X

    if (closeProfesorModal) {

        closeProfesorModal.addEventListener('click', cerrarProfesorModal);

    }


    // BOTÓN CANCELAR

    if (cancelProfesorModal) {

        cancelProfesorModal.addEventListener('click', cerrarProfesorModal);

    }


    // CLIC EN EL FONDO

    if (profesorModalOverlay) {

        profesorModalOverlay.addEventListener('click', cerrarProfesorModal);

    }


    // ESC PARA CERRAR

    document.addEventListener('keydown', (event) => {

        if (event.key === 'Escape' && !profesorModal.classList.contains('hidden')) {

            cerrarProfesorModal();

        }

    });


    // FORMULARIO

    if (profesorForm) {

        profesorForm.addEventListener('submit', (event) => {

            event.preventDefault();

            alert('Profesor listo para guardar.');

        });

    }

});

// ==========================================
// BÚSQUEDA, FILTRO Y ESTADÍSTICAS
// PROFESORES
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const buscador = document.getElementById('buscar-profesor');

    const filtroEstado = document.getElementById('filtro-estado');

    const profesores = document.querySelectorAll('.profesor-row');

    const emptyState = document.getElementById('profesores-empty');

    const resultados = document.getElementById('resultados-profesores');

    const totalResultados = document.getElementById(
        'total-resultados-profesores'
    );

    const totalProfesores = document.getElementById(
        'total-profesores'
    );

    const profesoresActivos = document.getElementById(
        'profesores-activos'
    );

    const profesoresInactivos = document.getElementById(
        'profesores-inactivos'
    );


    if (!buscador || !filtroEstado || profesores.length === 0) {
        return;
    }


    // ==========================================
    // ESTADÍSTICAS INICIALES
    // ==========================================

    let activos = 0;

    let inactivos = 0;


    profesores.forEach((profesor) => {

        if (profesor.dataset.estado === 'activo') {

            activos++;

        } else {

            inactivos++;

        }

    });


    if (totalProfesores) {
        totalProfesores.textContent = profesores.length;
    }

    if (profesoresActivos) {
        profesoresActivos.textContent = activos;
    }

    if (profesoresInactivos) {
        profesoresInactivos.textContent = inactivos;
    }

    if (totalResultados) {
        totalResultados.textContent = profesores.length;
    }


    // ==========================================
    // FILTRAR
    // ==========================================

    function filtrarProfesores() {

        const texto = buscador.value
            .toLowerCase()
            .trim();

        const estadoSeleccionado = filtroEstado.value;

        let encontrados = 0;


        profesores.forEach((profesor) => {

            const nombre = profesor.dataset.nombre
                .toLowerCase();

            const estado = profesor.dataset.estado;


            const coincideNombre =
                nombre.includes(texto);


            const coincideEstado =
                estadoSeleccionado === '' ||
                estado === estadoSeleccionado;


            if (coincideNombre && coincideEstado) {

                profesor.classList.remove('hidden');

                encontrados++;

            } else {

                profesor.classList.add('hidden');

            }

        });


        // ==========================================
        // ACTUALIZAR CONTADOR
        // ==========================================

        if (resultados) {

            resultados.textContent = encontrados;

        }


        // ==========================================
        // MOSTRAR ESTADO VACÍO
        // ==========================================

        if (emptyState) {

            if (encontrados === 0) {

                emptyState.classList.remove('hidden');

            } else {

                emptyState.classList.add('hidden');

            }

        }

    }


    buscador.addEventListener(
        'input',
        filtrarProfesores
    );


    filtroEstado.addEventListener(
        'change',
        filtrarProfesores
    );

});

// ==========================================
// EDITAR PROFESOR
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('editar-profesor-modal');

    if (!modal) {
        return;
    }

    const botonesEditar = document.querySelectorAll('.editar-profesor');

    const cerrar = document.getElementById('close-editar-modal');

    const cancelar = document.getElementById('cancel-editar-modal');

    const overlay = document.getElementById('editar-modal-overlay');

    const form = document.getElementById('editar-profesor-form');


    const nombre = document.getElementById('editar-nombre');
    const email = document.getElementById('editar-email');
    const telefono = document.getElementById('editar-telefono');
    const especialidad = document.getElementById('editar-especialidad');
    const estado = document.getElementById('editar-estado');


    function abrirModal(datos) {

        nombre.value = datos.nombre;
        email.value = datos.email;
        telefono.value = datos.telefono;
        especialidad.value = datos.especialidad;
        estado.value = datos.estado;

        modal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');

    }


    function cerrarModal() {

        modal.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');

    }


    botonesEditar.forEach((boton) => {

        boton.addEventListener('click', () => {

            abrirModal({

                nombre: boton.dataset.nombre,

                email: boton.dataset.email,

                telefono: boton.dataset.telefono,

                especialidad: boton.dataset.especialidad,

                estado: boton.dataset.estado

            });

        });

    });


    cerrar.addEventListener('click', cerrarModal);

    cancelar.addEventListener('click', cerrarModal);

    overlay.addEventListener('click', cerrarModal);


    form.addEventListener('submit', (event) => {

        event.preventDefault();

        alert('Cambios listos para guardar.');

        cerrarModal();

    });

});

// ==========================================
// ELIMINAR PROFESOR
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const eliminarModal = document.getElementById('eliminar-profesor-modal');

    if (!eliminarModal) {
        return;
    }

    const eliminarModalOverlay = document.getElementById(
        'eliminar-profesor-modal-overlay'
    );

    const closeEliminarModal = document.getElementById(
        'close-eliminar-profesor-modal'
    );

    const cancelEliminarModal = document.getElementById(
        'cancel-eliminar-profesor-modal'
    );

    const confirmEliminarBoton = document.getElementById(
        'confirm-eliminar-profesor'
    );

    const eliminarProfesorNombre = document.getElementById(
        'eliminar-profesor-nombre'
    );

    const botonesEliminar = document.querySelectorAll('.eliminar-profesor');

    let profesorAEliminar = null;


    function abrirEliminarModal() {

        eliminarModal.classList.remove('hidden');

        eliminarModal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('overflow-hidden');

    }


    function cerrarEliminarModal() {

        eliminarModal.classList.add('hidden');

        eliminarModal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('overflow-hidden');

        profesorAEliminar = null;

    }


    botonesEliminar.forEach((boton) => {

        boton.addEventListener('click', () => {

            profesorAEliminar = boton.dataset.nombre;

            eliminarProfesorNombre.textContent = `"${profesorAEliminar}"`;

            abrirEliminarModal();

        });

    });


    closeEliminarModal.addEventListener(
        'click',
        cerrarEliminarModal
    );

    cancelEliminarModal.addEventListener(
        'click',
        cerrarEliminarModal
    );

    eliminarModalOverlay.addEventListener(
        'click',
        cerrarEliminarModal
    );


    // ESC PARA CERRAR

    document.addEventListener('keydown', (event) => {

        if (
            event.key === 'Escape' &&
            !eliminarModal.classList.contains('hidden')
        ) {
            cerrarEliminarModal();
        }

    });


    confirmEliminarBoton.addEventListener('click', () => {

        if (!profesorAEliminar) {
            return;
        }

        alert(`${profesorAEliminar} listo para eliminar.`);

        cerrarEliminarModal();

    });

});

// ==========================================
// CURSOS
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const buscador = document.getElementById('buscar-curso');
    const filtro = document.getElementById('filtro-curso-estado');

    const cursos = document.querySelectorAll('.curso-row');

    const emptyState = document.getElementById('cursos-empty');

    const resultados = document.getElementById('resultados-cursos');
    const totalResultados = document.getElementById('total-resultados-cursos');

    const totalCursos = document.getElementById('total-cursos');
    const activos = document.getElementById('cursos-activos');
    const inactivos = document.getElementById('cursos-inactivos');

    const modal = document.getElementById('curso-modal');
    const abrirModal = document.getElementById('open-curso-modal');
    const cerrarModal = document.getElementById('close-curso-modal');
    const cancelarModal = document.getElementById('cancel-curso-modal');
    const overlay = document.getElementById('curso-modal-overlay');

    const formulario = document.getElementById('curso-form');

    const nombreInput = document.getElementById('curso-nombre');
    const codigoInput = document.getElementById('curso-codigo');
    const areaInput = document.getElementById('curso-area');
    const horasInput = document.getElementById('curso-horas');
    const estadoInput = document.getElementById('curso-estado');

    const modalTitle = document.getElementById('curso-modal-title');

    const submitButton = document.getElementById(
        'curso-submit-button'
    );

    let cursoEditando = null;


    const eliminarModal = document.getElementById('eliminar-curso-modal');

    const eliminarModalOverlay = document.getElementById(
        'eliminar-curso-modal-overlay'
    );

    const closeEliminarModal = document.getElementById(
        'close-eliminar-curso-modal'
    );

    const cancelEliminarModal = document.getElementById(
        'cancel-eliminar-curso-modal'
    );

    const confirmEliminarBoton = document.getElementById(
        'confirm-eliminar-curso'
    );

    const eliminarCursoNombre = document.getElementById(
        'eliminar-curso-nombre'
    );

    let cursoAEliminar = null;


    // Si no estamos en la página de Cursos, no hacemos nada.

    if (!buscador || !filtro || !modal || !formulario) {
        return;
    }


    // ==========================================
    // MODAL
    // ==========================================

    function abrirCursoModal() {

        modal.classList.remove('hidden');

        modal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            nombreInput.focus();
        }, 100);

    }


    function cerrarCursoModal() {

        modal.classList.add('hidden');

        modal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('overflow-hidden');

        formulario.reset();

        cursoEditando = null;

        modalTitle.textContent = 'Agregar curso';

        submitButton.textContent = 'Guardar curso';

    }


    abrirModal.addEventListener('click', abrirCursoModal);

    cerrarModal.addEventListener('click', cerrarCursoModal);

    cancelarModal.addEventListener('click', cerrarCursoModal);

    overlay.addEventListener('click', cerrarCursoModal);


    // Cerrar con ESC

    document.addEventListener('keydown', (event) => {

        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarCursoModal();
        }

    });


    // ==========================================
    // ESTADÍSTICAS
    // ==========================================

    function actualizarEstadisticas() {

        const filas = document.querySelectorAll('.curso-row');

        let cantidadActivos = 0;
        let cantidadInactivos = 0;

        filas.forEach((curso) => {

            if (curso.dataset.estado === 'activo') {
                cantidadActivos++;
            }

            if (curso.dataset.estado === 'inactivo') {
                cantidadInactivos++;
            }

        });

        totalCursos.textContent = filas.length;

        activos.textContent = cantidadActivos;

        inactivos.textContent = cantidadInactivos;

        totalResultados.textContent = filas.length;

    }


    // ==========================================
    // FILTRAR
    // ==========================================

    function filtrarCursos() {

        const texto = buscador.value
            .toLowerCase()
            .trim();

        const estadoSeleccionado = filtro.value;

        const filas = document.querySelectorAll('.curso-row');

        let encontrados = 0;


        filas.forEach((curso) => {

            const nombre = curso.dataset.nombre.toLowerCase();

            const estado = curso.dataset.estado;


            const coincideNombre =
                nombre.includes(texto);

            const coincideEstado =
                estadoSeleccionado === '' ||
                estado === estadoSeleccionado;


            if (coincideNombre && coincideEstado) {

                curso.classList.remove('hidden');

                encontrados++;

            } else {

                curso.classList.add('hidden');

            }

        });


        resultados.textContent = encontrados;


        if (encontrados === 0) {

            emptyState.classList.remove('hidden');

        } else {

            emptyState.classList.add('hidden');

        }

    }


    buscador.addEventListener('input', filtrarCursos);

    filtro.addEventListener('change', filtrarCursos);


    // ==========================================
    // AGREGAR CURSO
    // ==========================================

    formulario.addEventListener('submit', (event) => {

        event.preventDefault();


        const nombre = nombreInput.value.trim();

        const codigo = codigoInput.value.trim().toUpperCase();

        const area = areaInput.value;

        const horas = horasInput.value;

        const estado = estadoInput.value;


        // Validación básica

        if (!nombre || !codigo || !area || !horas) {

            alert('Completa todos los campos.');

            return;

        }


        // ==========================================
        // MODO EDICIÓN
        // ==========================================

        if (cursoEditando) {

            const fila = cursoEditando;


            fila.dataset.nombre = nombre;

            fila.dataset.estado = estado;


            const nombreElemento =
                fila.querySelector('td:first-child p');


            const descripcionElemento =
                fila.querySelector('td:first-child p:last-child');


            const celdas =
                fila.querySelectorAll('td');


            nombreElemento.textContent = nombre;

            descripcionElemento.textContent = area;


            celdas[1].textContent = codigo;

            celdas[2].textContent = area;

            celdas[3].textContent = `${horas} horas`;


            // Actualizar estado

            celdas[4].innerHTML =
                estado === 'activo'

                    ? `
                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                            Activo
                        </span>
                      `

                    : `
                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                            Inactivo
                        </span>
                      `;


            // Actualizar botones

            const botonEditar =
                fila.querySelector('.editar-curso');


            botonEditar.dataset.nombre = nombre;

            botonEditar.dataset.codigo = codigo;

            botonEditar.dataset.area = area;

            botonEditar.dataset.horas = horas;

            botonEditar.dataset.estado = estado;


            const botonEliminar =
                fila.querySelector('.eliminar-curso');


            botonEliminar.dataset.nombre = nombre;


            cerrarCursoModal();

            actualizarEstadisticas();

            filtrarCursos();


            return;

        }


        // Verificar código duplicado

        const codigosExistentes = document.querySelectorAll(
            '.curso-row [data-codigo]'
        );


        // Crear fila

        const tbody = document.querySelector(
            'table tbody'
        );


        const fila = document.createElement('tr');

        fila.className =
            'curso-row transition hover:bg-slate-50';

        fila.dataset.nombre = nombre;

        fila.dataset.estado = estado;


        // Estado visual

        const estadoHTML = estado === 'activo'

            ? `
                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                    Activo
                </span>
              `

            : `
                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                    Inactivo
                </span>
              `;


        // Color del icono

        const icono = '📚';


        fila.innerHTML = `

            <td class="whitespace-nowrap px-6 py-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100 text-lg">
                        ${icono}
                    </div>

                    <div>

                        <p class="font-medium text-slate-900">
                            ${nombre}
                        </p>

                        <p class="text-xs text-slate-500">
                            ${area}
                        </p>

                    </div>

                </div>

            </td>


            <td
                class="whitespace-nowrap px-6 py-4 text-sm text-slate-600"
                data-codigo="${codigo}"
            >
                ${codigo}
            </td>


            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                ${area}
            </td>


            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                ${horas} horas
            </td>


            <td class="whitespace-nowrap px-6 py-4">

                ${estadoHTML}

            </td>


            <td class="whitespace-nowrap px-6 py-4 text-right">

                <button
                    type="button"
                    class="editar-curso rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                    data-nombre="${nombre}"
                    data-codigo="${codigo}"
                    data-area="${area}"
                    data-horas="${horas}"
                    data-estado="${estado}"
                    title="Editar"
                >
                    ✏️
                </button>


                <button
                    type="button"
                    class="eliminar-curso rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                    data-nombre="${nombre}"
                    title="Eliminar"
                >
                    🗑️
                </button>

            </td>

        `;


        // Insertar antes del estado vacío

        tbody.insertBefore(
            fila,
            emptyState
        );


        // Cerrar modal

        cerrarCursoModal();


        // Actualizar estadísticas

        actualizarEstadisticas();


        // Actualizar filtros

        filtrarCursos();

    });


    // ==========================================
    // EDITAR CURSO
    // ==========================================

    document.addEventListener('click', (event) => {

        const botonEditar =
            event.target.closest('.editar-curso');


        if (!botonEditar) {
            return;
        }


        cursoEditando =
            botonEditar.closest('.curso-row');


        const nombre =
            botonEditar.dataset.nombre;

        const codigo =
            botonEditar.dataset.codigo;

        const area =
            botonEditar.dataset.area;

        const horas =
            botonEditar.dataset.horas;

        const estado =
            botonEditar.dataset.estado;


        // Rellenar formulario

        nombreInput.value = nombre;

        codigoInput.value = codigo;

        areaInput.value = area;

        horasInput.value = horas;

        estadoInput.value = estado;


        // Cambiar título

        modalTitle.textContent = 'Editar curso';

        submitButton.textContent = 'Guardar cambios';


        // Abrir modal

        abrirCursoModal();

    });


    // ==========================================
    // ELIMINAR CURSO
    // ==========================================

    function abrirEliminarModal() {

        eliminarModal.classList.remove('hidden');

        eliminarModal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('overflow-hidden');

    }


    function cerrarEliminarModal() {

        eliminarModal.classList.add('hidden');

        eliminarModal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('overflow-hidden');

        cursoAEliminar = null;

    }


    document.addEventListener('click', (event) => {

        const botonEliminar =
            event.target.closest('.eliminar-curso');


        if (!botonEliminar) {
            return;
        }


        cursoAEliminar =
            botonEliminar.closest('.curso-row');


        const nombre =
            botonEliminar.dataset.nombre;


        eliminarCursoNombre.textContent = `"${nombre}"`;


        abrirEliminarModal();

    });


    closeEliminarModal.addEventListener(
        'click',
        cerrarEliminarModal
    );

    cancelEliminarModal.addEventListener(
        'click',
        cerrarEliminarModal
    );

    eliminarModalOverlay.addEventListener(
        'click',
        cerrarEliminarModal
    );


    // Cerrar con ESC

    document.addEventListener('keydown', (event) => {

        if (
            event.key === 'Escape' &&
            !eliminarModal.classList.contains('hidden')
        ) {
            cerrarEliminarModal();
        }

    });


    confirmEliminarBoton.addEventListener('click', () => {

        if (!cursoAEliminar) {
            return;
        }

        cursoAEliminar.remove();

        cerrarEliminarModal();

        actualizarEstadisticas();

        filtrarCursos();

    });


    // ==========================================
    // ESTADO INICIAL
    // ==========================================

    actualizarEstadisticas();

    filtrarCursos();

});