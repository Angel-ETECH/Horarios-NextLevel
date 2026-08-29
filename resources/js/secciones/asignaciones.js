document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // ELEMENTOS
    // =========================================================

    const profesorSelect =
        document.getElementById('asignacion-profesor');

    const institucionSelect =
        document.getElementById('asignacion-institucion');

    const cursoSelect =
        document.getElementById('asignacion-curso');

    const gradoSelect =
        document.getElementById('asignacion-grado');

    const aulaSelect =
        document.getElementById('asignacion-aula');

    const diaSelect =
        document.getElementById('asignacion-dia');

    const horaInicioInput =
        document.getElementById('asignacion-hora-inicio');

    const horaFinInput =
        document.getElementById('asignacion-hora-fin');

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

    const resumenDisponibilidad =
        document.getElementById('resumen-disponibilidad-profesor');

    const estadoDisponibilidad =
        document.getElementById('estado-disponibilidad-asignacion');


    if (
        !profesorSelect ||
        !institucionSelect ||
        !cursoSelect ||
        !gradoSelect ||
        !aulaSelect ||
        !diaSelect ||
        !horaInicioInput ||
        !horaFinInput ||
        !estadoSelect ||
        !guardarButton ||
        !tabla
    ) {
        return;
    }


    // =========================================================
    // STORAGE
    // =========================================================

    const DISPONIBILIDAD_KEY =
        'nextlevel_disponibilidades';

    const HORARIOS_KEY =
        'nextlevel_horarios';


    function leerStorage(clave) {

        try {

            return JSON.parse(
                localStorage.getItem(clave)
            ) || [];

        } catch (error) {

            console.error(
                `Error leyendo ${clave}:`,
                error
            );

            return [];

        }

    }


    function guardarStorage(
        clave,
        datos
    ) {

        localStorage.setItem(
            clave,
            JSON.stringify(datos)
        );

    }


    function cargarDisponibilidades() {

        return leerStorage(
            DISPONIBILIDAD_KEY
        );

    }


    function cargarHorarios() {

        return leerStorage(
            HORARIOS_KEY
        );

    }


    function guardarHorarios(
        horarios
    ) {

        guardarStorage(
            HORARIOS_KEY,
            horarios
        );

    }


    // =========================================================
    // DATOS AUXILIARES
    // =========================================================

    const dias = {
        1: 'Lunes',
        2: 'Martes',
        3: 'Miércoles',
        4: 'Jueves',
        5: 'Viernes',
        6: 'Sábado'
    };


    function obtenerProfesores() {

        return leerStorage(
            'nextlevel_profesores'
        );

    }


    function obtenerCursos() {

        return leerStorage(
            'nextlevel_cursos'
        );

    }


    function obtenerGrados() {

        return leerStorage(
            'nextlevel_grados'
        );

    }


    function obtenerAulas() {

        return leerStorage(
            'nextlevel_aulas'
        );

    }


    // =========================================================
    // CARGAR PROFESORES
    // =========================================================

    function cargarProfesores() {

        const profesores =
            obtenerProfesores();


        profesorSelect.innerHTML =
            '<option value="">Seleccionar profesor</option>';


        profesores.forEach(
            (profesor) => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    profesor.id;


                option.textContent =
                    `${profesor.nombre ?? ''} ${profesor.apellido_paterno ?? ''}`
                        .trim();


                profesorSelect
                    .appendChild(option);

            }
        );

    }


    // =========================================================
    // CARGAR CURSOS
    // =========================================================

    function cargarCursos() {

        const cursos =
            obtenerCursos();


        const institucion =
            institucionSelect.value;


        cursoSelect.innerHTML =
            '<option value="">Seleccionar curso</option>';


        let filtrados =
            cursos;


        if (
            institucion ===
            'academia'
        ) {

            filtrados =
                cursos.filter(
                    (curso) =>

                        curso.nivel ===
                            'academia' ||

                        curso.nivel ===
                            'todos'

                );

        }


        if (
            institucion ===
            'colegio'
        ) {

            filtrados =
                cursos.filter(
                    (curso) =>

                        curso.nivel ===
                            'primaria' ||

                        curso.nivel ===
                            'secundaria' ||

                        curso.nivel ===
                            'todos'

                );

        }


        filtrados.forEach(
            (curso) => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    curso.id;


                option.textContent =
                    curso.nombre;


                cursoSelect
                    .appendChild(option);

            }
        );

    }


    // =========================================================
    // CARGAR GRADOS
    // =========================================================

    function cargarGrados() {

        const grados =
            obtenerGrados();


        const institucion =
            institucionSelect.value;


        gradoSelect.innerHTML =
            '<option value="">Seleccionar grado</option>';


        const gradoContainer =
            document.getElementById(
                'grado-container'
            );


        if (
            institucion ===
            'academia'
        ) {

            gradoSelect.disabled =
                true;

            gradoSelect.value =
                '';

            if (gradoContainer) {

                gradoContainer.classList.add(
                    'opacity-50'
                );

            }

            return;

        }


        gradoSelect.disabled =
            institucion !==
            'colegio';


        if (gradoContainer) {

            gradoContainer.classList.remove(
                'opacity-50'
            );

        }


        if (
            institucion !==
            'colegio'
        ) {

            return;

        }


        grados
            .filter(
                (grado) =>

                    grado.nivel ===
                        'primaria' ||

                    grado.nivel ===
                        'secundaria'

            )
            .forEach(
                (grado) => {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        grado.id;


                    option.textContent =
                        grado.nombre_completo ??
                        grado.nombre;


                    gradoSelect
                        .appendChild(option);

                }
            );

    }


    // =========================================================
    // CARGAR AULAS
    // =========================================================

    function cargarAulas() {

        const aulas =
            obtenerAulas();


        const institucion =
            institucionSelect.value;


        aulaSelect.innerHTML =
            '<option value="">Seleccionar aula</option>';


        let filtradas =
            aulas;


        if (
            institucion ===
            'academia'
        ) {

            filtradas =
                aulas.filter(
                    (aula) =>

                        aula.nivel ===
                            'academia' ||

                        aula.nivel ===
                            'todos'

                );

        }


        if (
            institucion ===
            'colegio'
        ) {

            filtradas =
                aulas.filter(
                    (aula) =>

                        aula.nivel ===
                            'primaria' ||

                        aula.nivel ===
                            'secundaria' ||

                        aula.nivel ===
                            'todos'

                );

        }


        filtradas.forEach(
            (aula) => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    aula.id;


                option.textContent =
                    aula.capacidad
                        ? `${aula.nombre} (${aula.capacidad} alumnos)`
                        : aula.nombre;


                aulaSelect
                    .appendChild(option);

            }
        );

    }


    // =========================================================
    // DISPONIBILIDAD DEL PROFESOR
    // =========================================================

    function disponibilidadProfesorActual() {

        const profesorId =
            profesorSelect.value;


        const institucion =
            institucionSelect.value;


        if (
            !profesorId ||
            !institucion
        ) {

            return [];

        }


        return cargarDisponibilidades()
            .filter(
                (item) => {

                    const id =
                        item.profesor_id ??
                        item.profesorId;


                    return (
                        String(id) ===
                            String(profesorId) &&

                        item.institucion ===
                            institucion
                    );

                }
            );

    }


    // =========================================================
    // MOSTRAR DISPONIBILIDAD
    // =========================================================

    function actualizarDisponibilidadProfesor() {

        const disponibilidad =
            disponibilidadProfesorActual();


        diaSelect.innerHTML =
            '<option value="">Seleccionar día</option>';


        horaInicioInput.value =
            '';

        horaFinInput.value =
            '';


        if (
            !profesorSelect.value ||
            !institucionSelect.value
        ) {

            diaSelect.disabled =
                true;

            horaInicioInput.disabled =
                true;

            horaFinInput.disabled =
                true;


            resumenDisponibilidad.innerHTML =
                `
                    <span class="text-xs text-slate-400">
                        Selecciona profesor e institución.
                    </span>
                `;


            return;

        }


        if (
            disponibilidad.length ===
            0
        ) {

            diaSelect.disabled =
                true;

            horaInicioInput.disabled =
                true;

            horaFinInput.disabled =
                true;


            resumenDisponibilidad.innerHTML =
                `
                    <span class="rounded-full border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600">
                        Este profesor no tiene disponibilidad configurada.
                    </span>
                `;


            return;

        }


        const diasDisponibles =
            Array.from(
                new Set(
                    disponibilidad.map(
                        (item) =>
                            Number(
                                item.dia_semana ??
                                item.dia
                            )
                    )
                )
            ).sort(
                (a, b) =>
                    a - b
            );


        diasDisponibles.forEach(
            (dia) => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    dia;


                option.textContent =
                    dias[dia];


                diaSelect
                    .appendChild(option);

            }
        );


        diaSelect.disabled =
            false;


        resumenDisponibilidad.innerHTML =
            disponibilidad
                .slice()
                .sort(
                    (a, b) => {

                        const diaA =
                            Number(
                                a.dia_semana ??
                                a.dia
                            );

                        const diaB =
                            Number(
                                b.dia_semana ??
                                b.dia
                            );


                        return (
                            diaA - diaB ||
                            (
                                a.hora_inicio ??
                                a.horaInicio
                            ).localeCompare(
                                b.hora_inicio ??
                                b.horaInicio
                            )
                        );

                    }
                )
                .map(
                    (item) => {

                        const dia =
                            Number(
                                item.dia_semana ??
                                item.dia
                            );


                        const inicio =
                            item.hora_inicio ??
                            item.horaInicio;


                        const fin =
                            item.hora_fin ??
                            item.horaFin;


                        return `
                            <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700">
                                ${dias[dia]} · ${inicio} - ${fin}
                            </span>
                        `;

                    }
                )
                .join('');

    }


    // =========================================================
    // AL SELECCIONAR DÍA
    // =========================================================

    function actualizarHorasPorDia() {

        const dia =
            Number(
                diaSelect.value
            );


        horaInicioInput.value =
            '';

        horaFinInput.value =
            '';


        if (!dia) {

            horaInicioInput.disabled =
                true;

            horaFinInput.disabled =
                true;

            return;

        }


        const disponibilidad =
            disponibilidadProfesorActual()
                .filter(
                    (item) =>

                        Number(
                            item.dia_semana ??
                            item.dia
                        ) === dia

                );


        horaInicioInput.disabled =
            false;

        horaFinInput.disabled =
            false;


        if (
            disponibilidad.length >
            0
        ) {

            const primera =
                disponibilidad[0];


            horaInicioInput.value =
                primera.hora_inicio ??
                primera.horaInicio;


            horaFinInput.value =
                primera.hora_fin ??
                primera.horaFin;

        }


        validarHorarioVisual();

    }


    // =========================================================
    // TRASLAPE
    // =========================================================

    function existeTraslape(
        inicioA,
        finA,
        inicioB,
        finB
    ) {

        return (
            inicioA < finB &&
            inicioB < finA
        );

    }


    // =========================================================
    // VALIDAR DISPONIBILIDAD
    // =========================================================

    function validarDentroDisponibilidad(
        profesorId,
        institucion,
        dia,
        horaInicio,
        horaFin
    ) {

        const disponibilidades =
            cargarDisponibilidades()
                .filter(
                    (item) => {

                        const id =
                            item.profesor_id ??
                            item.profesorId;


                        const diaItem =
                            Number(
                                item.dia_semana ??
                                item.dia
                            );


                        return (
                            String(id) ===
                                String(profesorId) &&

                            item.institucion ===
                                institucion &&

                            diaItem ===
                                Number(dia)
                        );

                    }
                );


        return disponibilidades
            .some(
                (item) => {

                    const inicio =
                        item.hora_inicio ??
                        item.horaInicio;


                    const fin =
                        item.hora_fin ??
                        item.horaFin;


                    return (
                        horaInicio >= inicio &&
                        horaFin <= fin
                    );

                }
            );

    }


    // =========================================================
    // VALIDAR CONFLICTOS
    // =========================================================

    function validarConflictos({
        horarioExistente,
        profesorId,
        aulaId,
        gradoId,
        dia,
        horaInicio,
        horaFin
    }) {

        const conflictoProfesor =
            horarioExistente.find(
                (horario) =>

                    String(
                        horario.profesor_id
                    ) ===
                        String(profesorId) &&

                    Number(
                        horario.dia_semana
                    ) ===
                        Number(dia) &&

                    existeTraslape(
                        horaInicio,
                        horaFin,
                        horario.hora_inicio,
                        horario.hora_fin
                    )

            );


        if (conflictoProfesor) {

            return {
                valido: false,
                mensaje:
                    'El profesor ya tiene otra clase asignada en ese horario.'
            };

        }


        const conflictoAula =
            horarioExistente.find(
                (horario) =>

                    String(
                        horario.aula_id
                    ) ===
                        String(aulaId) &&

                    Number(
                        horario.dia_semana
                    ) ===
                        Number(dia) &&

                    existeTraslape(
                        horaInicio,
                        horaFin,
                        horario.hora_inicio,
                        horario.hora_fin
                    )

            );


        if (conflictoAula) {

            return {
                valido: false,
                mensaje:
                    'El aula ya está ocupada en ese horario.'
            };

        }


        if (gradoId) {

            const conflictoGrado =
                horarioExistente.find(
                    (horario) =>

                        horario.grado_id &&

                        String(
                            horario.grado_id
                        ) ===
                            String(gradoId) &&

                        Number(
                            horario.dia_semana
                        ) ===
                            Number(dia) &&

                        existeTraslape(
                            horaInicio,
                            horaFin,
                            horario.hora_inicio,
                            horario.hora_fin
                        )

                );


            if (conflictoGrado) {

                return {
                    valido: false,
                    mensaje:
                        'El grado o grupo ya tiene otra clase en ese horario.'
                };

            }

        }


        return {
            valido: true,
            mensaje:
                'Horario disponible.'
        };

    }


    // =========================================================
    // ESTADO VISUAL DE LA VALIDACIÓN
    // =========================================================

    function mostrarEstado(
        valido,
        mensaje
    ) {

        if (
            !estadoDisponibilidad
        ) {

            return;

        }


        estadoDisponibilidad
            .classList.remove(
                'hidden',
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700',
                'border-red-200',
                'bg-red-50',
                'text-red-700'
            );


        if (valido) {

            estadoDisponibilidad
                .classList.add(
                    'border-emerald-200',
                    'bg-emerald-50',
                    'text-emerald-700'
                );


            estadoDisponibilidad
                .textContent =
                    `✓ ${mensaje}`;

        } else {

            estadoDisponibilidad
                .classList.add(
                    'border-red-200',
                    'bg-red-50',
                    'text-red-700'
                );


            estadoDisponibilidad
                .textContent =
                    `✕ ${mensaje}`;

        }

    }


    function ocultarEstado() {

        if (
            estadoDisponibilidad
        ) {

            estadoDisponibilidad
                .classList.add(
                    'hidden'
                );

        }

    }


    // =========================================================
    // VALIDACIÓN EN TIEMPO REAL
    // =========================================================

    function validarHorarioVisual() {

        const profesorId =
            profesorSelect.value;

        const institucion =
            institucionSelect.value;

        const aulaId =
            aulaSelect.value;

        const gradoId =
            gradoSelect.value;

        const dia =
            diaSelect.value;

        const horaInicio =
            horaInicioInput.value;

        const horaFin =
            horaFinInput.value;


        if (
            !profesorId ||
            !institucion ||
            !dia ||
            !horaInicio ||
            !horaFin
        ) {

            ocultarEstado();

            return false;

        }


        if (
            horaInicio >=
            horaFin
        ) {

            mostrarEstado(
                false,
                'La hora de fin debe ser posterior a la hora de inicio.'
            );

            return false;

        }


        const disponible =
            validarDentroDisponibilidad(
                profesorId,
                institucion,
                dia,
                horaInicio,
                horaFin
            );


        if (!disponible) {

            mostrarEstado(
                false,
                `El profesor no está disponible el ${dias[dia]} de ${horaInicio} a ${horaFin}.`
            );

            return false;

        }


        if (!aulaId) {

            mostrarEstado(
                true,
                'El profesor está disponible. Selecciona un aula para completar la validación.'
            );

            return true;

        }


        const validacion =
            validarConflictos({

                horarioExistente:
                    cargarHorarios(),

                profesorId,

                aulaId,

                gradoId,

                dia,

                horaInicio,

                horaFin

            });


        mostrarEstado(
            validacion.valido,
            validacion.mensaje
        );


        return validacion.valido;

    }


    // =========================================================
    // INSTITUCIÓN
    // =========================================================

    function actualizarInstitucion() {

        cargarCursos();

        cargarGrados();

        cargarAulas();

        actualizarDisponibilidadProfesor();

        validarHorarioVisual();

    }


    // =========================================================
    // LIMPIAR
    // =========================================================

    function limpiarFormulario() {

        profesorSelect.value =
            '';

        institucionSelect.value =
            '';

        cursoSelect.innerHTML =
            '<option value="">Seleccionar curso</option>';

        gradoSelect.innerHTML =
            '<option value="">Seleccionar grado</option>';

        aulaSelect.innerHTML =
            '<option value="">Seleccionar aula</option>';

        diaSelect.innerHTML =
            '<option value="">Selecciona primero un profesor</option>';


        diaSelect.disabled =
            true;

        horaInicioInput.value =
            '';

        horaFinInput.value =
            '';

        horaInicioInput.disabled =
            true;

        horaFinInput.disabled =
            true;

        estadoSelect.value =
            'activo';


        resumenDisponibilidad.innerHTML =
            `
                <span class="text-xs text-slate-400">
                    Selecciona profesor e institución para consultar su disponibilidad.
                </span>
            `;


        ocultarEstado();

        cargarGrados();

    }


    // =========================================================
    // GUARDAR ASIGNACIÓN
    // =========================================================

    guardarButton.addEventListener(
        'click',
        () => {

            const profesorId =
                profesorSelect.value;

            const institucion =
                institucionSelect.value;

            const cursoId =
                cursoSelect.value;

            const gradoId =
                gradoSelect.value ||
                null;

            const aulaId =
                aulaSelect.value;

            const dia =
                Number(
                    diaSelect.value
                );

            const horaInicio =
                horaInicioInput.value;

            const horaFin =
                horaFinInput.value;

            const estado =
                estadoSelect.value;


            // =====================================
            // CAMPOS
            // =====================================

            if (!profesorId) {

                alert(
                    'Selecciona un profesor.'
                );

                profesorSelect.focus();

                return;

            }


            if (!institucion) {

                alert(
                    'Selecciona una institución.'
                );

                institucionSelect.focus();

                return;

            }


            if (!cursoId) {

                alert(
                    'Selecciona un curso.'
                );

                cursoSelect.focus();

                return;

            }


            if (
                institucion ===
                    'colegio' &&
                !gradoId
            ) {

                alert(
                    'Selecciona un grado y sección.'
                );

                gradoSelect.focus();

                return;

            }


            if (!aulaId) {

                alert(
                    'Selecciona un aula.'
                );

                aulaSelect.focus();

                return;

            }


            if (!dia) {

                alert(
                    'Selecciona un día disponible.'
                );

                diaSelect.focus();

                return;

            }


            if (
                !horaInicio ||
                !horaFin
            ) {

                alert(
                    'Selecciona la hora de inicio y la hora de fin.'
                );

                return;

            }


            if (
                horaInicio >=
                horaFin
            ) {

                alert(
                    'La hora de fin debe ser posterior a la hora de inicio.'
                );

                return;

            }


            // =====================================
            // DISPONIBILIDAD
            // =====================================

            const disponible =
                validarDentroDisponibilidad(
                    profesorId,
                    institucion,
                    dia,
                    horaInicio,
                    horaFin
                );


            if (!disponible) {

                alert(
                    `El profesor no está disponible el ${dias[dia]} de ${horaInicio} a ${horaFin}.`
                );

                mostrarEstado(
                    false,
                    `El profesor no está disponible el ${dias[dia]} de ${horaInicio} a ${horaFin}.`
                );

                return;

            }


            // =====================================
            // CONFLICTOS
            // =====================================

            const horarios =
                cargarHorarios();


            const validacion =
                validarConflictos({

                    horarioExistente:
                        horarios,

                    profesorId,

                    aulaId,

                    gradoId,

                    dia,

                    horaInicio,

                    horaFin

                });


            if (
                !validacion.valido
            ) {

                alert(
                    validacion.mensaje
                );

                mostrarEstado(
                    false,
                    validacion.mensaje
                );

                return;

            }


            // =====================================
            // GUARDAR CLASE
            // =====================================

            const nuevaClase = {

                id:
                    Date.now(),

                profesor_id:
                    Number(
                        profesorId
                    ),

                curso_id:
                    Number(
                        cursoId
                    ),

                grado_id:
                    gradoId
                        ? Number(
                            gradoId
                        )
                        : null,

                aula_id:
                    Number(
                        aulaId
                    ),

                institucion,

                dia_semana:
                    dia,

                hora_inicio:
                    horaInicio,

                hora_fin:
                    horaFin,

                estado

            };


            horarios.push(
                nuevaClase
            );


            guardarHorarios(
                horarios
            );


            // =====================================
            // RESULTADO
            // =====================================

            renderizarAsignaciones();


            mostrarEstado(
                true,
                'Asignación creada correctamente.'
            );


            alert(
                `Asignación creada correctamente.\n\n` +
                `${dias[dia]} · ${horaInicio} - ${horaFin}`
            );


            // Dejamos profesor/institución para
            // facilitar agregar otra clase.

            cursoSelect.value =
                '';

            aulaSelect.value =
                '';

            diaSelect.value =
                '';

            horaInicioInput.value =
                '';

            horaFinInput.value =
                '';

            horaInicioInput.disabled =
                true;

            horaFinInput.disabled =
                true;

        }
    );


    // =========================================================
    // RENDER TABLA
    // =========================================================

    function renderizarAsignaciones() {

        const horarios =
            cargarHorarios();


        const profesores =
            obtenerProfesores();

        const cursos =
            obtenerCursos();

        const grados =
            obtenerGrados();

        const aulas =
            obtenerAulas();


        tabla.innerHTML =
            '';


        if (
            horarios.length ===
            0
        ) {

            tabla.innerHTML =
                `
                    <tr>
                        <td
                            colspan="8"
                            class="px-5 py-12 text-center text-slate-500"
                        >
                            <div class="flex flex-col items-center">
                                <span class="mb-3 text-3xl">📋</span>

                                <p class="font-medium">
                                    No hay asignaciones creadas
                                </p>

                                <p class="text-sm">
                                    Crea tu primera clase usando el formulario.
                                </p>
                            </div>
                        </td>
                    </tr>
                `;


            return;

        }


        horarios
            .slice()
            .sort(
                (a, b) =>

                    Number(
                        a.dia_semana
                    ) -
                    Number(
                        b.dia_semana
                    ) ||

                    a.hora_inicio.localeCompare(
                        b.hora_inicio
                    )
            )
            .forEach(
                (horario) => {

                    const profesor =
                        profesores.find(
                            (item) =>
                                String(
                                    item.id
                                ) ===
                                String(
                                    horario.profesor_id
                                )
                        );


                    const curso =
                        cursos.find(
                            (item) =>
                                String(
                                    item.id
                                ) ===
                                String(
                                    horario.curso_id
                                )
                        );


                    const grado =
                        grados.find(
                            (item) =>
                                String(
                                    item.id
                                ) ===
                                String(
                                    horario.grado_id
                                )
                        );


                    const aula =
                        aulas.find(
                            (item) =>
                                String(
                                    item.id
                                ) ===
                                String(
                                    horario.aula_id
                                )
                        );


                    const nombreProfesor =
                        profesor
                            ? `${profesor.nombre ?? ''} ${profesor.apellido_paterno ?? ''}`.trim()
                            : 'Profesor desconocido';


                    const nombreCurso =
                        curso?.nombre ??
                        'Curso desconocido';


                    const nombreGrado =
                        grado?.nombre_completo ??
                        grado?.nombre ??
                        '—';


                    const nombreAula =
                        aula?.nombre ??
                        'Aula desconocida';


                    const iniciales =
                        nombreProfesor
                            .split(' ')
                            .filter(Boolean)
                            .map(
                                (parte) =>
                                    parte.charAt(
                                        0
                                    )
                            )
                            .join('')
                            .substring(
                                0,
                                2
                            )
                            .toUpperCase();


                    const estadoHTML =
                        horario.estado ===
                        'inactivo'

                            ? `
                                <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                    Inactivo
                                </span>
                            `

                            : `
                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700">
                                    Activo
                                </span>
                            `;


                    const fila =
                        document.createElement(
                            'tr'
                        );


                    fila.className =
                        'asignacion-row asignaciones-table-row';


                    fila.dataset.id =
                        horario.id;


                    fila.innerHTML =
                        `

                            <td class="px-5 py-4">

                                <div class="flex items-center gap-3">

                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 font-semibold text-indigo-700">
                                        ${iniciales}
                                    </div>

                                    <div>

                                        <span class="block text-sm font-medium text-slate-800">
                                            ${nombreProfesor}
                                        </span>

                                        <span class="block text-xs text-slate-500">
                                            ${horario.institucion === 'academia' ? '🎓 Academia' : '🏫 Colegio'}
                                        </span>

                                    </div>

                                </div>

                            </td>


                            <td class="px-5 py-4 text-sm text-slate-600">
                                ${nombreCurso}
                            </td>


                            <td class="px-5 py-4 text-sm text-slate-600">
                                ${nombreGrado}
                            </td>


                            <td class="px-5 py-4 text-sm text-slate-600">
                                ${nombreAula}
                            </td>


                            <td class="px-5 py-4 text-sm font-medium text-slate-700">
                                ${dias[horario.dia_semana] ?? horario.dia_semana}
                            </td>


                            <td class="px-5 py-4">

                                <span class="inline-flex rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                    ${horario.hora_inicio} - ${horario.hora_fin}
                                </span>

                            </td>


                            <td class="px-5 py-4">
                                ${estadoHTML}
                            </td>


                            <td class="px-5 py-4 text-right">

                                <button
                                    type="button"
                                    class="eliminar-asignacion text-sm font-medium text-red-600 hover:text-red-800"
                                    data-id="${horario.id}"
                                >
                                    Eliminar
                                </button>

                            </td>

                        `;


                    tabla.appendChild(
                        fila
                    );

                }
            );

    }


    // =========================================================
    // ELIMINAR
    // =========================================================

    tabla.addEventListener(
        'click',
        (event) => {

            const boton =
                event.target.closest(
                    '.eliminar-asignacion'
                );


            if (!boton) {
                return;
            }


            const id =
                boton.dataset.id;


            if (
                !confirm(
                    '¿Deseas eliminar esta asignación?'
                )
            ) {

                return;

            }


            const horarios =
                cargarHorarios()
                    .filter(
                        (horario) =>
                            String(
                                horario.id
                            ) !==
                            String(id)
                    );


            guardarHorarios(
                horarios
            );


            renderizarAsignaciones();

        }
    );


    // =========================================================
    // BUSCADOR
    // =========================================================

    if (buscarInput) {

        buscarInput.addEventListener(
            'input',
            () => {

                const texto =
                    buscarInput.value
                        .toLowerCase()
                        .trim();


                tabla.querySelectorAll(
                    '.asignacion-row'
                ).forEach(
                    (fila) => {

                        fila.classList.toggle(
                            'hidden',
                            !fila.textContent
                                .toLowerCase()
                                .includes(
                                    texto
                                )
                        );

                    }
                );

            }
        );

    }


    // =========================================================
    // EVENTOS
    // =========================================================

    institucionSelect.addEventListener(
        'change',
        actualizarInstitucion
    );


    profesorSelect.addEventListener(
        'change',
        () => {

            actualizarDisponibilidadProfesor();

            validarHorarioVisual();

        }
    );


    diaSelect.addEventListener(
        'change',
        actualizarHorasPorDia
    );


    aulaSelect.addEventListener(
        'change',
        validarHorarioVisual
    );


    gradoSelect.addEventListener(
        'change',
        validarHorarioVisual
    );


    horaInicioInput.addEventListener(
        'change',
        validarHorarioVisual
    );


    horaFinInput.addEventListener(
        'change',
        validarHorarioVisual
    );


    if (limpiarButton) {

        limpiarButton.addEventListener(
            'click',
            limpiarFormulario
        );

    }


    // =========================================================
    // STORAGE ENTRE PESTAÑAS
    // =========================================================

    window.addEventListener(
        'storage',
        (event) => {

            if (
                event.key ===
                DISPONIBILIDAD_KEY
            ) {

                actualizarDisponibilidadProfesor();

            }


            if (
                event.key ===
                HORARIOS_KEY
            ) {

                renderizarAsignaciones();

            }


            if (
                [
                    'nextlevel_profesores',
                    'nextlevel_cursos',
                    'nextlevel_grados',
                    'nextlevel_aulas'
                ].includes(
                    event.key
                )
            ) {

                cargarProfesores();

                actualizarInstitucion();

            }

        }
    );


    // =========================================================
    // INICIAL
    // =========================================================

    cargarProfesores();

    cargarCursos();

    cargarGrados();

    cargarAulas();

    actualizarDisponibilidadProfesor();

    renderizarAsignaciones();

});