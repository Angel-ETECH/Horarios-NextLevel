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

    const botonesEliminar = document.querySelectorAll('.eliminar-profesor');


    botonesEliminar.forEach((boton) => {

        boton.addEventListener('click', () => {

            const nombre = boton.dataset.nombre;

            const confirmar = confirm(
                `¿Estás seguro de eliminar a ${nombre}?`
            );


            if (confirmar) {

                alert(`${nombre} listo para eliminar.`);

            }

        });

    });

});