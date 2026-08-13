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
// ==========================================
// AULAS
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const buscador = document.getElementById('buscar-aula');
    const filtro = document.getElementById('filtro-aula-estado');

    const emptyState = document.getElementById('aulas-empty');
    const resultados = document.getElementById('resultados-aulas');

    const totalAulas = document.getElementById('total-aulas');
    const aulasDisponibles = document.getElementById('aulas-disponibles');
    const aulasNoDisponibles = document.getElementById('aulas-no-disponibles');

    const modal = document.getElementById('aula-modal');
    const abrirModalBtn = document.getElementById('open-aula-modal');
    const cerrarModalBtn = document.getElementById('close-aula-modal');
    const cancelarModalBtn = document.getElementById('cancel-aula-modal');
    const overlay = document.getElementById('aula-modal-overlay');

    const formulario = document.getElementById('aula-form');

    const nombreInput = document.getElementById('aula-nombre');
    const capacidadInput = document.getElementById('aula-capacidad');
    const tipoInput = document.getElementById('aula-tipo');
    const estadoInput = document.getElementById('aula-estado');

    const modalTitle = document.getElementById('aula-modal-title');
    const submitButton = document.getElementById('aula-submit-button');

    let aulaEditando = null;


    const eliminarModal = document.getElementById('delete-aula-modal');
    const eliminarModalOverlay = document.getElementById('delete-aula-overlay');
    const eliminarAulaNombre = document.getElementById('delete-aula-name');
    const cancelEliminarModal = document.getElementById('cancel-delete-aula');
    const confirmEliminarBoton = document.getElementById('confirm-delete-aula');

    let aulaAEliminar = null;


    // Si no estamos en la página de Aulas, no hacemos nada.

    if (!buscador || !filtro || !modal || !formulario) {
        return;
    }


    // ==========================================
    // MODAL AGREGAR / EDITAR
    // ==========================================

    function abrirAulaModal() {

        modal.classList.remove('hidden');

        modal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            nombreInput.focus();
        }, 100);

    }


    function cerrarAulaModal() {

        modal.classList.add('hidden');

        modal.setAttribute('aria-hidden', 'true');

        document.body.classList.remove('overflow-hidden');

        formulario.reset();

        aulaEditando = null;

        modalTitle.textContent = 'Agregar aula';

        submitButton.textContent = 'Guardar aula';

    }


    abrirModalBtn.addEventListener('click', abrirAulaModal);

    cerrarModalBtn.addEventListener('click', cerrarAulaModal);

    cancelarModalBtn.addEventListener('click', cerrarAulaModal);

    overlay.addEventListener('click', cerrarAulaModal);


    document.addEventListener('keydown', (event) => {

        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            cerrarAulaModal();
        }

    });


    // ==========================================
    // ESTADÍSTICAS
    // ==========================================

    function actualizarEstadisticas() {

        const filas = document.querySelectorAll('.aula-row');

        let disponibles = 0;
        let noDisponibles = 0;

        filas.forEach((aula) => {

            if (aula.dataset.estado === 'disponible') {
                disponibles++;
            } else {
                noDisponibles++;
            }

        });

        totalAulas.textContent = filas.length;

        aulasDisponibles.textContent = disponibles;

        aulasNoDisponibles.textContent = noDisponibles;

    }


    // ==========================================
    // FILTRAR
    // ==========================================

    function filtrarAulas() {

        const texto = buscador.value
            .toLowerCase()
            .trim();

        const estadoSeleccionado = filtro.value;

        const filas = document.querySelectorAll('.aula-row');

        let encontrados = 0;


        filas.forEach((aula) => {

            const nombre = aula.dataset.nombre.toLowerCase();

            const estado = aula.dataset.estado;


            const coincideNombre =
                nombre.includes(texto);

            const coincideEstado =
                estadoSeleccionado === '' ||
                estado === estadoSeleccionado;


            if (coincideNombre && coincideEstado) {

                aula.classList.remove('hidden');

                encontrados++;

            } else {

                aula.classList.add('hidden');

            }

        });


        resultados.textContent = encontrados;


        if (encontrados === 0) {

            emptyState.classList.remove('hidden');

        } else {

            emptyState.classList.add('hidden');

        }

    }


    buscador.addEventListener('input', filtrarAulas);

    filtro.addEventListener('change', filtrarAulas);


    // ==========================================
    // AGREGAR / EDITAR AULA
    // ==========================================

    formulario.addEventListener('submit', (event) => {

        event.preventDefault();


        const nombre = nombreInput.value.trim();

        const capacidad = capacidadInput.value.trim();

        const tipo = tipoInput.value;

        const estado = estadoInput.value;


        // Validación básica

        if (!nombre || !capacidad || !tipo) {

            alert('Completa todos los campos.');

            return;

        }


        // ==========================================
        // MODO EDICIÓN
        // ==========================================

        if (aulaEditando) {

            const fila = aulaEditando;


            fila.dataset.nombre = nombre;

            fila.dataset.estado = estado;


            const nombreElemento =
                fila.querySelector('td:first-child p');


            const celdas =
                fila.querySelectorAll('td');


            nombreElemento.textContent = nombre;


            celdas[1].textContent = `${capacidad} alumnos`;

            celdas[2].textContent = tipo;


            // Actualizar estado

            celdas[3].innerHTML =
                estado === 'disponible'

                    ? `
                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                            Disponible
                        </span>
                      `

                    : `
                        <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">
                            No disponible
                        </span>
                      `;


            // Actualizar botones

            const botonEditar =
                fila.querySelector('.editar-aula');


            botonEditar.dataset.nombre = nombre;

            botonEditar.dataset.capacidad = capacidad;

            botonEditar.dataset.tipo = tipo;

            botonEditar.dataset.estado = estado;


            const botonEliminar =
                fila.querySelector('.eliminar-aula');


            botonEliminar.dataset.nombre = nombre;


            cerrarAulaModal();

            actualizarEstadisticas();

            filtrarAulas();


            return;

        }


        // ==========================================
        // MODO AGREGAR
        // ==========================================

        const tbody = document.querySelector('table tbody');

        const fila = document.createElement('tr');

        fila.className =
            'aula-row transition hover:bg-slate-50';

        fila.dataset.nombre = nombre;

        fila.dataset.estado = estado;


        const estadoHTML = estado === 'disponible'

            ? `
                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                    Disponible
                </span>
              `

            : `
                <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">
                    No disponible
                </span>
              `;


        const icono = tipo === 'Laboratorio'
            ? '💻'
            : tipo === 'Sala'
                ? '🪑'
                : '🏫';


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
                            ${tipo}
                        </p>

                    </div>

                </div>

            </td>


            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                ${capacidad} alumnos
            </td>


            <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600">
                ${tipo}
            </td>


            <td class="whitespace-nowrap px-6 py-4">

                ${estadoHTML}

            </td>


            <td class="whitespace-nowrap px-6 py-4 text-right">

                <button
                    type="button"
                    class="editar-aula rounded-lg p-2 text-slate-500 transition hover:bg-indigo-50 hover:text-indigo-600"
                    data-nombre="${nombre}"
                    data-capacidad="${capacidad}"
                    data-tipo="${tipo}"
                    data-estado="${estado}"
                    title="Editar"
                >
                    ✏️
                </button>


                <button
                    type="button"
                    class="eliminar-aula rounded-lg p-2 text-slate-500 transition hover:bg-red-50 hover:text-red-600"
                    data-nombre="${nombre}"
                    title="Eliminar"
                >
                    🗑️
                </button>

            </td>

        `;


        tbody.insertBefore(fila, emptyState);


        cerrarAulaModal();

        actualizarEstadisticas();

        filtrarAulas();

    });


    // ==========================================
    // EDITAR AULA
    // ==========================================

    document.addEventListener('click', (event) => {

        const botonEditar =
            event.target.closest('.editar-aula');


        if (!botonEditar) {
            return;
        }


        aulaEditando =
            botonEditar.closest('.aula-row');


        const nombre = botonEditar.dataset.nombre;
        const capacidad = botonEditar.dataset.capacidad;
        const tipo = botonEditar.dataset.tipo;
        const estado = botonEditar.dataset.estado;


        nombreInput.value = nombre;

        capacidadInput.value = capacidad;

        tipoInput.value = tipo;

        estadoInput.value = estado;


        modalTitle.textContent = 'Editar aula';

        submitButton.textContent = 'Guardar cambios';


        abrirAulaModal();

    });


    // ==========================================
    // ELIMINAR AULA
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

        aulaAEliminar = null;

    }


    document.addEventListener('click', (event) => {

        const botonEliminar =
            event.target.closest('.eliminar-aula');


        if (!botonEliminar) {
            return;
        }


        aulaAEliminar =
            botonEliminar.closest('.aula-row');


        const nombre = botonEliminar.dataset.nombre;


        eliminarAulaNombre.textContent = `"${nombre}"`;


        abrirEliminarModal();

    });


    cancelEliminarModal.addEventListener(
        'click',
        cerrarEliminarModal
    );

    eliminarModalOverlay.addEventListener(
        'click',
        cerrarEliminarModal
    );


    document.addEventListener('keydown', (event) => {

        if (
            event.key === 'Escape' &&
            !eliminarModal.classList.contains('hidden')
        ) {
            cerrarEliminarModal();
        }

    });


    confirmEliminarBoton.addEventListener('click', () => {

        if (!aulaAEliminar) {
            return;
        }

        aulaAEliminar.remove();

        cerrarEliminarModal();

        actualizarEstadisticas();

        filtrarAulas();

    });


    // ==========================================
    // ESTADO INICIAL
    // ==========================================

    actualizarEstadisticas();

    filtrarAulas();

});

// ==========================================
// DISPONIBILIDAD
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const profesorSelect = document.getElementById('profesor-select');
    const slots = document.querySelectorAll('.availability-slot');

    const selectAllButton = document.getElementById('select-all-availability');
    const clearButton = document.getElementById('clear-availability');
    const saveButton = document.getElementById('save-availability');

    const countElement =
        document.getElementById('availability-count');

    const daysElement =
        document.getElementById('availability-days');

    const hoursElement =
        document.getElementById('availability-hours');

    const dayButtons =
        document.querySelectorAll('.select-day');

    const mobileDayTabs =
        document.querySelectorAll('.mobile-day-tab');

    const mobileDayPanels =
        document.querySelectorAll('.mobile-day-panel');

    if (!profesorSelect || !slots.length) {
        return;
    }


    // ==========================================
    // DATOS TEMPORALES DE LOS PROFESORES
    // ==========================================

    const disponibilidades = {};


    // ==========================================
    // OBTENER BLOQUES SELECCIONADOS (SIN DUPLICADOS)
    // ==========================================
    // Nota: cada bloque existe hasta 2 veces en el DOM
    // (versión escritorio + versión móvil), por eso
    // usamos un Set para no contar el mismo horario dos veces.

    function obtenerSeleccionados() {

        const identificadoresUnicos = new Set();

        document.querySelectorAll(
            '.availability-slot.selected'
        ).forEach((slot) => {

            identificadoresUnicos.add(
                `${slot.dataset.dia}-${slot.dataset.hora}`
            );

        });

        return Array.from(identificadoresUnicos);

    }


    // ==========================================
    // ACTUALIZAR RESUMEN
    // ==========================================

    function actualizarResumen() {

        const identificadoresUnicos =
            obtenerSeleccionados();


        const cantidad =
            identificadoresUnicos.length;


        const dias =
            new Set(
                identificadoresUnicos.map(
                    (id) => id.split('-')[0]
                )
            );


        countElement.textContent =
            cantidad;


        daysElement.textContent =
            dias.size;


        hoursElement.textContent =
            cantidad;

    }


    // ==========================================
    // LIMPIAR VISUALMENTE LA CUADRÍCULA
    // ==========================================

    function limpiarGrid() {

        slots.forEach((slot) => {

            slot.classList.remove(
                'bg-indigo-600',
                'selected'
            );

            slot.classList.add(
                'bg-white',
                'hover:bg-indigo-50'
            );

        });

    }


    // ==========================================
    // CARGAR DISPONIBILIDAD DEL PROFESOR
    // ==========================================

    function cargarDisponibilidad(profesorId) {

        limpiarGrid();


        const datos =
            disponibilidades[profesorId] || [];


        slots.forEach((slot) => {

            const identificador =
                `${slot.dataset.dia}-${slot.dataset.hora}`;


            if (datos.includes(identificador)) {

                slot.classList.remove(
                    'bg-white',
                    'hover:bg-indigo-50'
                );

                slot.classList.add(
                    'bg-indigo-600',
                    'selected'
                );

            }

        });

        actualizarResumen();

    }


    // ==========================================
    // CLICK EN BLOQUE
    // ==========================================
    // Como cada horario puede existir 2 veces (grid de
    // escritorio + lista móvil), al hacer clic buscamos
    // TODAS las copias con el mismo día y hora, y las
    // sincronizamos juntas.

    slots.forEach((slot) => {

        slot.addEventListener('click', () => {

            if (!profesorSelect.value) {

                alert(
                    'Primero selecciona un profesor.'
                );

                profesorSelect.focus();

                return;

            }


            const dia = slot.dataset.dia;
            const hora = slot.dataset.hora;

            const seleccionando =
                !slot.classList.contains('selected');


            const copiasDelBloque =
                document.querySelectorAll(
                    `.availability-slot[data-dia="${dia}"][data-hora="${hora}"]`
                );


            copiasDelBloque.forEach((copia) => {

                if (seleccionando) {

                    copia.classList.remove(
                        'bg-white',
                        'hover:bg-indigo-50'
                    );

                    copia.classList.add(
                        'bg-indigo-600',
                        'selected'
                    );

                } else {

                    copia.classList.remove(
                        'bg-indigo-600',
                        'selected'
                    );

                    copia.classList.add(
                        'bg-white',
                        'hover:bg-indigo-50'
                    );

                }

            });


            disponibilidades[
                profesorSelect.value
            ] = obtenerSeleccionados();


            actualizarResumen();

        });

    });


    // ==========================================
    // SELECCIONAR DÍA COMPLETO
    // ==========================================

    dayButtons.forEach((button) => {

        button.addEventListener('click', () => {

            if (!profesorSelect.value) {

                alert(
                    'Primero selecciona un profesor.'
                );

                profesorSelect.focus();

                return;
            }


            const dia =
                button.dataset.dia;


            const bloquesDelDia =
                document.querySelectorAll(
                    `.availability-slot[data-dia="${dia}"]`
                );


            const todosSeleccionados =
                Array.from(bloquesDelDia).every(
                    (slot) =>
                        slot.classList.contains(
                            'selected'
                        )
                );


            bloquesDelDia.forEach((slot) => {

                if (todosSeleccionados) {

                    slot.classList.remove(
                        'bg-indigo-600',
                        'selected'
                    );

                    slot.classList.add(
                        'bg-white',
                        'hover:bg-indigo-50'
                    );

                } else {

                    slot.classList.remove(
                        'bg-white',
                        'hover:bg-indigo-50'
                    );

                    slot.classList.add(
                        'bg-indigo-600',
                        'selected'
                    );

                }

            });


            disponibilidades[
                profesorSelect.value
            ] = obtenerSeleccionados();


            actualizarResumen();

        });

    });


    // ==========================================
    // PESTAÑAS DE DÍA (VISTA MÓVIL)
    // ==========================================

    mobileDayTabs.forEach((tab) => {

        tab.addEventListener('click', () => {

            const dia =
                tab.dataset.dia;


            mobileDayPanels.forEach((panel) => {

                panel.classList.toggle(
                    'hidden',
                    panel.dataset.diaPanel !== dia
                );

            });


            mobileDayTabs.forEach((otraTab) => {

                const activa =
                    otraTab === tab;


                otraTab.classList.toggle(
                    'bg-indigo-600',
                    activa
                );

                otraTab.classList.toggle(
                    'text-white',
                    activa
                );

                otraTab.classList.toggle(
                    'bg-slate-100',
                    !activa
                );

                otraTab.classList.toggle(
                    'text-slate-600',
                    !activa
                );

            });

        });

    });


    // ==========================================
    // CAMBIAR PROFESOR
    // ==========================================

    profesorSelect.addEventListener(
        'change',
        () => {

            const profesorId =
                profesorSelect.value;


            if (!profesorId) {

                limpiarGrid();

                actualizarResumen();

                return;

            }


            cargarDisponibilidad(
                profesorId
            );

        }
    );


    // ==========================================
    // SELECCIONAR TODO
    // ==========================================

    selectAllButton.addEventListener(
        'click',
        () => {

            if (!profesorSelect.value) {

                alert(
                    'Primero selecciona un profesor.'
                );

                profesorSelect.focus();

                return;

            }


            slots.forEach((slot) => {

                slot.classList.remove(
                    'bg-white',
                    'hover:bg-indigo-50'
                );

                slot.classList.add(
                    'bg-indigo-600',
                    'selected'
                );

            });


            disponibilidades[
                profesorSelect.value
            ] = obtenerSeleccionados();


            actualizarResumen();

        }
    );


    // ==========================================
    // LIMPIAR
    // ==========================================

    clearButton.addEventListener(
        'click',
        () => {

            if (!profesorSelect.value) {

                alert(
                    'Primero selecciona un profesor.'
                );

                profesorSelect.focus();

                return;

            }


            limpiarGrid();


            disponibilidades[
                profesorSelect.value
            ] = [];


            actualizarResumen();

        }
    );


    // ==========================================
    // GUARDAR
    // ==========================================

    saveButton.addEventListener(
        'click',
        () => {

            const profesorId =
                profesorSelect.value;


            if (!profesorId) {

                alert(
                    'Primero selecciona un profesor.'
                );

                profesorSelect.focus();

                return;

            }


            const disponibilidad =
                disponibilidades[profesorId] || [];


            console.log({
                profesor_id: profesorId,
                disponibilidad: disponibilidad
            });


            alert(
                `Disponibilidad guardada correctamente.\n\nBloques seleccionados: ${disponibilidad.length}`
            );

        }
    );


    // ==========================================
    // ESTADO INICIAL
    // ==========================================

    actualizarResumen();

});
// ==========================================
// ASIGNACIONES
// ==========================================

document.addEventListener('DOMContentLoaded', () => {

    const profesorSelect =
        document.getElementById('asignacion-profesor');

    const cursoSelect =
        document.getElementById('asignacion-curso');

    const gradoSelect =
        document.getElementById('asignacion-grado');

    const aulaSelect =
        document.getElementById('asignacion-aula');

    const horasInput =
        document.getElementById('horas-semanales');

    const bloqueSelect =
        document.getElementById('horas-bloque');

    const estadoSelect =
        document.getElementById('asignacion-estado');

    const guardarButton =
        document.getElementById('guardar-asignacion');

    const limpiarButton =
        document.getElementById('limpiar-asignacion');

    const buscarInput =
        document.getElementById('buscar-asignacion');

    const tabla =
        document.getElementById('asignaciones-body');


    // ==========================================
    // VERIFICAR PÁGINA
    // ==========================================

    if (
        !profesorSelect ||
        !cursoSelect ||
        !gradoSelect ||
        !aulaSelect ||
        !horasInput ||
        !bloqueSelect ||
        !estadoSelect ||
        !guardarButton ||
        !limpiarButton ||
        !tabla
    ) {
        return;
    }


    // ==========================================
    // LIMPIAR FORMULARIO
    // ==========================================

    function limpiarFormulario() {

        profesorSelect.value = '';
        cursoSelect.value = '';
        gradoSelect.value = '';
        aulaSelect.value = '';
        horasInput.value = '';
        bloqueSelect.value = '1';
        estadoSelect.value = 'activo';

    }


    // ==========================================
    // CREAR ASIGNACIÓN
    // ==========================================

    guardarButton.addEventListener('click', () => {

        const profesorId =
            profesorSelect.value;

        const cursoId =
            cursoSelect.value;

        const gradoId =
            gradoSelect.value;

        const aulaId =
            aulaSelect.value;

        const horas =
            horasInput.value;

        const bloque =
            bloqueSelect.value;

        const estado =
            estadoSelect.value;


        // ==========================================
        // VALIDACIÓN
        // ==========================================

        if (!profesorId) {

            alert('Selecciona un profesor.');

            profesorSelect.focus();

            return;
        }


        if (!cursoId) {

            alert('Selecciona un curso.');

            cursoSelect.focus();

            return;
        }


        if (!gradoId) {

            alert('Selecciona un grado.');

            gradoSelect.focus();

            return;
        }


        if (!aulaId) {

            alert('Selecciona un aula.');

            aulaSelect.focus();

            return;
        }


        if (!horas || horas < 1) {

            alert(
                'Ingresa una cantidad válida de horas semanales.'
            );

            horasInput.focus();

            return;
        }


        // ==========================================
        // OBTENER TEXTOS
        // ==========================================

        const profesor =
            profesorSelect.options[
                profesorSelect.selectedIndex
            ].text;

        const curso =
            cursoSelect.options[
                cursoSelect.selectedIndex
            ].text;

        const grado =
            gradoSelect.options[
                gradoSelect.selectedIndex
            ].text;

        const aula =
            aulaSelect.options[
                aulaSelect.selectedIndex
            ].text;


        // ==========================================
        // INICIALES
        // ==========================================

        const partesNombre =
            profesor.split(' ');

        let iniciales = '';

        if (partesNombre.length >= 2) {

            iniciales =
                partesNombre[0].charAt(0) +
                partesNombre[1].charAt(0);

        } else {

            iniciales =
                profesor.substring(0, 2);

        }

        iniciales =
            iniciales.toUpperCase();


        // ==========================================
        // ESTADO VISUAL
        // ==========================================

        let estadoHTML = '';

        if (estado === 'activo') {

            estadoHTML = `
                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                    Activo
                </span>
            `;

        } else {

            estadoHTML = `
                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                    Inactivo
                </span>
            `;

        }


        // ==========================================
        // CREAR FILA
        // ==========================================

        const fila =
            document.createElement('tr');

        fila.className =
            'asignacion-row hover:bg-slate-50';


        fila.innerHTML = `

            <td class="px-5 py-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                        ${iniciales}
                    </div>

                    <span class="text-sm font-medium text-slate-800">
                        ${profesor}
                    </span>

                </div>

            </td>


            <td class="px-5 py-4 text-sm text-slate-600">
                ${curso}
            </td>


            <td class="px-5 py-4 text-sm text-slate-600">
                ${grado}
            </td>


            <td class="px-5 py-4 text-sm text-slate-600">
                ${aula}
            </td>


            <td class="px-5 py-4 text-sm text-slate-600">
                ${horas} h
            </td>


            <td class="px-5 py-4">

                ${estadoHTML}

            </td>


            <td class="px-5 py-4 text-right">

                <button
                    type="button"
                    class="eliminar-asignacion text-sm font-medium text-red-600 hover:text-red-800"
                >
                    Eliminar
                </button>

            </td>

        `;


        tabla.appendChild(fila);


        // ==========================================
        // LIMPIAR FORMULARIO
        // ==========================================

        limpiarFormulario();


        alert(
            'Asignación creada correctamente.'
        );

    });


    // ==========================================
    // BOTÓN LIMPIAR
    // ==========================================

    limpiarButton.addEventListener('click', () => {

        limpiarFormulario();

    });


    // ==========================================
    // ELIMINAR ASIGNACIÓN
    // ==========================================

    tabla.addEventListener('click', (event) => {

        const boton =
            event.target.closest(
                '.eliminar-asignacion'
            );


        if (!boton) {
            return;
        }


        const confirmar =
            confirm(
                '¿Deseas eliminar esta asignación?'
            );


        if (!confirmar) {
            return;
        }


        const fila =
            boton.closest('tr');


        fila.remove();

    });


    // ==========================================
    // BUSCADOR
    // ==========================================

    if (buscarInput) {

        buscarInput.addEventListener(
            'input',
            () => {

                const texto =
                    buscarInput.value
                        .toLowerCase()
                        .trim();


                const filas =
                    tabla.querySelectorAll(
                        '.asignacion-row'
                    );


                filas.forEach((fila) => {

                    const contenido =
                        fila.textContent
                            .toLowerCase();


                    if (
                        contenido.includes(texto)
                    ) {

                        fila.classList.remove(
                            'hidden'
                        );

                    } else {

                        fila.classList.add(
                            'hidden'
                        );

                    }

                });

            }
        );

    }

});