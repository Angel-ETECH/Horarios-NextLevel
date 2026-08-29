// =========================================================
// DISPONIBILIDAD DE PROFESORES
// =========================================================
//
// Esta sección SOLO registra cuándo puede trabajar
// un profesor.
//
// NO crea clases.
// NO genera horarios.
// NO asigna cursos.
// NO ocupa aulas.
//
// Los profesores se leen desde:
// nextlevel_profesores
//
// La disponibilidad se guarda en:
// nextlevel_disponibilidades
//
// =========================================================

document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // ELEMENTOS PRINCIPALES
    // =========================================================

    const profesorSelect =
        document.getElementById('profesor-select');

    const institucionSelect =
        document.getElementById('institucion-select');

    const slots =
        document.querySelectorAll('.availability-slot');

    const selectAllButton =
        document.getElementById('select-all-availability');

    const clearButton =
        document.getElementById('clear-availability');

    const saveButton =
        document.getElementById('save-availability');

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


    // =========================================================
    // HORARIOS PERSONALIZADOS
    // =========================================================

    const diaPersonalizadoSelect =
        document.getElementById('personalizado-dia');

    const inicioPersonalizadoInput =
        document.getElementById('personalizado-inicio');

    const finPersonalizadoInput =
        document.getElementById('personalizado-fin');

    const agregarPersonalizadoButton =
        document.getElementById('agregar-personalizado');

    const listaPersonalizadosEl =
        document.getElementById('personalizados-lista');

    const personalizadosVacioEl =
        document.getElementById('personalizados-vacio');


    // =========================================================
    // VERIFICAR PÁGINA
    // =========================================================

    if (!profesorSelect || slots.length === 0) {
        return;
    }


    // =========================================================
    // CONFIGURACIÓN
    // =========================================================

    const PROFESORES_KEY =
        'nextlevel_profesores';

    const DISPONIBILIDAD_KEY =
        'nextlevel_disponibilidades';

    const DURACION_BLOQUE_GRID_MIN =
        60;


    const diasCortos = {

        1: 'Lun',
        2: 'Mar',
        3: 'Mié',
        4: 'Jue',
        5: 'Vie',
        6: 'Sáb'

    };


    // =========================================================
    // ESTADO EN MEMORIA
    // =========================================================

    const disponibilidades = {};

    const personalizados = {};


    // =========================================================
    // LOCAL STORAGE
    // =========================================================

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


    function cargarTodasLasDisponibilidades() {

        return leerStorage(
            DISPONIBILIDAD_KEY
        );

    }


    function guardarTodasLasDisponibilidades(
        lista
    ) {

        guardarStorage(
            DISPONIBILIDAD_KEY,
            lista
        );

    }


    // =========================================================
    // CARGAR PROFESORES
    // =========================================================

    function cargarProfesores() {

        const profesores =
            leerStorage(
                PROFESORES_KEY
            );


        const profesorSeleccionado =
            profesorSelect.value;


        const institucionActual =
            institucionSelect
                ? institucionSelect.value
                : 'colegio';


        // Limpiamos el select.

        profesorSelect.innerHTML = `

            <option value="">
                Seleccionar profesor
            </option>

        `;


        // =====================================================
        // FILTRAR PROFESORES
        // =====================================================

        const profesoresDisponibles =
            profesores.filter(
                (profesor) => {

                    /*
                    |--------------------------------------------------------------------------
                    | ESTADO
                    |--------------------------------------------------------------------------
                    |
                    | Solo mostramos profesores activos.
                    |
                    */

                    if (
                        profesor.estado &&
                        profesor.estado !== 'activo'
                    ) {

                        return false;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PROFESORES ANTIGUOS
                    |--------------------------------------------------------------------------
                    |
                    | Algunos profesores pueden haber sido creados antes
                    | de agregar el campo "instituciones".
                    |
                    | Para no perderlos, si no tienen ese campo los
                    | mostramos tanto en Colegio como Academia.
                    |
                    */

                    if (
                        !Array.isArray(
                            profesor.instituciones
                        ) ||
                        profesor.instituciones.length === 0
                    ) {

                        return true;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | FILTRO POR INSTITUCIÓN
                    |--------------------------------------------------------------------------
                    */

                    return profesor.instituciones.includes(
                        institucionActual
                    );

                }
            );


        // =====================================================
        // ORDENAR POR NOMBRE
        // =====================================================

        profesoresDisponibles.sort(
            (a, b) => {

                const nombreA = [

                    a.nombre,
                    a.apellido_paterno,
                    a.apellido_materno

                ]
                    .filter(Boolean)
                    .join(' ');


                const nombreB = [

                    b.nombre,
                    b.apellido_paterno,
                    b.apellido_materno

                ]
                    .filter(Boolean)
                    .join(' ');


                return nombreA.localeCompare(
                    nombreB,
                    'es'
                );

            }
        );


        // =====================================================
        // CREAR OPCIONES
        // =====================================================

        profesoresDisponibles.forEach(
            (profesor) => {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    String(
                        profesor.id
                    );


                const nombreCompleto = [

                    profesor.nombre,
                    profesor.apellido_paterno,
                    profesor.apellido_materno

                ]
                    .filter(Boolean)
                    .join(' ');


                if (profesor.codigo) {

                    option.textContent =
                        `${nombreCompleto} · ${profesor.codigo}`;

                } else {

                    option.textContent =
                        nombreCompleto;

                }


                profesorSelect.appendChild(
                    option
                );

            }
        );


        // =====================================================
        // RESTAURAR SELECCIÓN
        // =====================================================

        const seleccionExiste =
            Array.from(
                profesorSelect.options
            ).some(
                (option) =>
                    option.value ===
                    profesorSeleccionado
            );


        if (seleccionExiste) {

            profesorSelect.value =
                profesorSeleccionado;

        } else {

            profesorSelect.value =
                '';

        }


        // =====================================================
        // SIN PROFESORES
        // =====================================================

        if (
            profesoresDisponibles.length === 0
        ) {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                '';

            option.disabled =
                true;


            option.textContent =
                institucionActual === 'academia'
                    ? 'No hay profesores activos para Academia'
                    : 'No hay profesores activos para Colegio';


            profesorSelect.appendChild(
                option
            );

        }

    }


    // =========================================================
    // CLAVE PROFESOR + INSTITUCIÓN
    // =========================================================

    function claveActual() {

        const profesorId =
            profesorSelect.value;


        const institucion =
            institucionSelect
                ? institucionSelect.value
                : 'colegio';


        return `${profesorId}-${institucion}`;

    }


    // =========================================================
    // OBTENER BLOQUES SELECCIONADOS
    // =========================================================

    function obtenerSeleccionados() {

        const identificadores =
            new Set();


        document.querySelectorAll(
            '.availability-slot.selected'
        ).forEach(
            (slot) => {

                identificadores.add(
                    `${slot.dataset.dia}-${slot.dataset.hora}`
                );

            }
        );


        return Array.from(
            identificadores
        );

    }


    // =========================================================
    // HORARIOS PERSONALIZADOS ACTUALES
    // =========================================================

    function personalizadosActuales() {

        if (!profesorSelect.value) {

            return [];

        }


        return personalizados[
            claveActual()
        ] || [];

    }


    // =========================================================
    // CALCULAR MINUTOS
    // =========================================================

    function minutosEntre(
        horaInicio,
        horaFin
    ) {

        const [
            horaInicioNumero,
            minutoInicio
        ] =
            horaInicio
                .split(':')
                .map(Number);


        const [
            horaFinNumero,
            minutoFin
        ] =
            horaFin
                .split(':')
                .map(Number);


        const inicio =
            horaInicioNumero * 60 +
            minutoInicio;


        const fin =
            horaFinNumero * 60 +
            minutoFin;


        return Math.max(
            0,
            fin - inicio
        );

    }


    // =========================================================
    // ACTUALIZAR RESUMEN
    // =========================================================

    function actualizarResumen() {

        const seleccionados =
            obtenerSeleccionados();


        const dias =
            new Set();


        // =====================================================
        // BLOQUES DEL GRID
        // =====================================================

        seleccionados.forEach(
            (identificador) => {

                const [
                    dia
                ] =
                    identificador.split('-');


                dias.add(
                    String(dia)
                );

            }
        );


        let minutosTotales =
            seleccionados.length *
            DURACION_BLOQUE_GRID_MIN;


        // =====================================================
        // HORARIOS PERSONALIZADOS
        // =====================================================

        const personalizadosData =
            personalizadosActuales();


        personalizadosData.forEach(
            (item) => {

                dias.add(
                    String(
                        item.dia
                    )
                );


                minutosTotales +=
                    minutosEntre(
                        item.horaInicio,
                        item.horaFin
                    );

            }
        );


        // =====================================================
        // TOTAL BLOQUES
        // =====================================================

        if (countElement) {

            countElement.textContent =
                seleccionados.length +
                personalizadosData.length;

        }


        // =====================================================
        // TOTAL DÍAS
        // =====================================================

        if (daysElement) {

            daysElement.textContent =
                dias.size;

        }


        // =====================================================
        // TOTAL HORAS
        // =====================================================

        if (hoursElement) {

            const horas =
                minutosTotales / 60;


            hoursElement.textContent =
                Number.isInteger(horas)
                    ? horas
                    : horas.toFixed(1);

        }

    }


    // =========================================================
    // MARCAR / DESMARCAR SLOT
    // =========================================================

    function marcarSlot(
        slot,
        seleccionado
    ) {

        if (seleccionado) {

            slot.classList.remove(
                'bg-white',
                'hover:bg-indigo-50'
            );


            slot.classList.add(
                'bg-indigo-600',
                'selected'
            );

        } else {

            slot.classList.remove(
                'bg-indigo-600',
                'selected'
            );


            slot.classList.add(
                'bg-white',
                'hover:bg-indigo-50'
            );

        }

    }


    // =========================================================
    // LIMPIAR GRID
    // =========================================================

    function limpiarGrid() {

        slots.forEach(
            (slot) => {

                marcarSlot(
                    slot,
                    false
                );

            }
        );

    }


    // =========================================================
    // CARGAR DISPONIBILIDAD VISUAL
    // =========================================================

    function cargarDisponibilidad() {

        limpiarGrid();


        if (!profesorSelect.value) {

            actualizarResumen();

            return;

        }


        const datos =
            disponibilidades[
                claveActual()
            ] || [];


        slots.forEach(
            (slot) => {

                const identificador =
                    `${slot.dataset.dia}-${slot.dataset.hora}`;


                if (
                    datos.includes(
                        identificador
                    )
                ) {

                    marcarSlot(
                        slot,
                        true
                    );

                }

            }
        );


        actualizarResumen();

    }


    // =========================================================
    // HIDRATAR DISPONIBILIDAD DESDE STORAGE
    // =========================================================

    function hidratarDesdeStorage(
        clave
    ) {

        /*
        |--------------------------------------------------------------------------
        | YA CARGADO
        |--------------------------------------------------------------------------
        */

        if (
            disponibilidades[clave] !== undefined ||
            personalizados[clave] !== undefined
        ) {

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | SEPARAR PROFESOR E INSTITUCIÓN
        |--------------------------------------------------------------------------
        */

        const separador =
            clave.lastIndexOf('-');


        const profesorId =
            clave.substring(
                0,
                separador
            );


        const institucion =
            clave.substring(
                separador + 1
            );


        /*
        |--------------------------------------------------------------------------
        | BUSCAR DISPONIBILIDAD
        |--------------------------------------------------------------------------
        */

        const guardadas =
            cargarTodasLasDisponibilidades()
                .filter(
                    (item) => {

                        const idGuardado =
                            item.profesor_id ??
                            item.profesorId;


                        return (

                            String(
                                idGuardado
                            ) ===
                                String(
                                    profesorId
                                ) &&

                            item.institucion ===
                                institucion

                        );

                    }
                );


        const idsGrid =
            [];


        const custom =
            [];


        guardadas.forEach(
            (item) => {

                const dia =
                    Number(
                        item.dia_semana ??
                        item.dia
                    );


                const horaInicio =
                    item.hora_inicio ??
                    item.horaInicio;


                const horaFin =
                    item.hora_fin ??
                    item.horaFin;


                if (
                    !dia ||
                    !horaInicio ||
                    !horaFin
                ) {

                    return;

                }


                const [
                    horaInicial,
                    minutoInicial
                ] =
                    horaInicio
                        .split(':')
                        .map(Number);


                const [
                    horaFinal,
                    minutoFinal
                ] =
                    horaFin
                        .split(':')
                        .map(Number);


                const inicioMinutos =
                    horaInicial * 60 +
                    minutoInicial;


                const finMinutos =
                    horaFinal * 60 +
                    minutoFinal;


                const duracion =
                    finMinutos -
                    inicioMinutos;


                /*
                |--------------------------------------------------------------------------
                | ¿SE PUEDE REPRESENTAR EN EL GRID?
                |--------------------------------------------------------------------------
                */

                const alineadoGrid =

                    minutoInicial === 0 &&

                    minutoFinal === 0 &&

                    duracion > 0 &&

                    duracion %
                        DURACION_BLOQUE_GRID_MIN ===
                        0;


                if (alineadoGrid) {

                    let cursor =
                        inicioMinutos;


                    while (
                        cursor <
                        finMinutos
                    ) {

                        const hora =
                            Math.floor(
                                cursor / 60
                            );


                        const minuto =
                            cursor % 60;


                        idsGrid.push(

                            `${dia}-${String(hora).padStart(2, '0')}:${String(minuto).padStart(2, '0')}`

                        );


                        cursor +=
                            DURACION_BLOQUE_GRID_MIN;

                    }

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | HORARIO PERSONALIZADO
                    |--------------------------------------------------------------------------
                    */

                    custom.push({

                        id:
                            `${Date.now()}-${Math.random()
                                .toString(36)
                                .slice(2, 8)}`,

                        dia,

                        horaInicio,

                        horaFin

                    });

                }

            }
        );


        disponibilidades[
            clave
        ] =
            Array.from(
                new Set(
                    idsGrid
                )
            );


        personalizados[
            clave
        ] =
            custom;

    }


    // =========================================================
    // CAMBIO DE PROFESOR
    // =========================================================

    function alCambiarSeleccion() {

        if (!profesorSelect.value) {

            limpiarGrid();

            renderizarPersonalizados();

            actualizarResumen();

            return;

        }


        const clave =
            claveActual();


        hidratarDesdeStorage(
            clave
        );


        cargarDisponibilidad();

        renderizarPersonalizados();

        actualizarResumen();

    }


    profesorSelect.addEventListener(
        'change',
        alCambiarSeleccion
    );


    // =========================================================
    // CAMBIO DE INSTITUCIÓN
    // =========================================================

    if (institucionSelect) {

        institucionSelect.addEventListener(
            'change',
            () => {

                /*
                |--------------------------------------------------------------------------
                | RECARGAR PROFESORES
                |--------------------------------------------------------------------------
                |
                | Si un profesor fue registrado solo para Colegio,
                | no aparecerá al seleccionar Academia.
                |
                */

                cargarProfesores();


                /*
                |--------------------------------------------------------------------------
                | LIMPIAR VISUAL
                |--------------------------------------------------------------------------
                */

                limpiarGrid();

                renderizarPersonalizados();

                actualizarResumen();


                /*
                |--------------------------------------------------------------------------
                | SI CONSERVÓ PROFESOR SELECCIONADO
                |--------------------------------------------------------------------------
                */

                if (
                    profesorSelect.value
                ) {

                    alCambiarSeleccion();

                }

            }
        );

    }


    // =========================================================
    // CLICK EN BLOQUE
    // =========================================================

    slots.forEach(
        (slot) => {

            slot.addEventListener(
                'click',
                () => {

                    if (
                        !profesorSelect.value
                    ) {

                        alert(
                            'Primero selecciona un profesor.'
                        );


                        profesorSelect.focus();

                        return;

                    }


                    const dia =
                        slot.dataset.dia;


                    const hora =
                        slot.dataset.hora;


                    const seleccionar =
                        !slot.classList.contains(
                            'selected'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | SINCRONIZAR ESCRITORIO Y MÓVIL
                    |--------------------------------------------------------------------------
                    */

                    const copias =
                        document.querySelectorAll(

                            `.availability-slot[data-dia="${dia}"][data-hora="${hora}"]`

                        );


                    copias.forEach(
                        (copia) => {

                            marcarSlot(
                                copia,
                                seleccionar
                            );

                        }
                    );


                    disponibilidades[
                        claveActual()
                    ] =
                        obtenerSeleccionados();


                    actualizarResumen();

                }
            );

        }
    );


    // =========================================================
    // SELECCIONAR DÍA COMPLETO
    // =========================================================

    dayButtons.forEach(
        (button) => {

            button.addEventListener(
                'click',
                () => {

                    if (
                        !profesorSelect.value
                    ) {

                        alert(
                            'Primero selecciona un profesor.'
                        );


                        profesorSelect.focus();

                        return;

                    }


                    const dia =
                        button.dataset.dia;


                    const bloques =
                        document.querySelectorAll(

                            `.availability-slot[data-dia="${dia}"]`

                        );


                    const bloquesAgrupados =
                        new Map();


                    bloques.forEach(
                        (slot) => {

                            const identificador =
                                `${slot.dataset.dia}-${slot.dataset.hora}`;


                            if (
                                !bloquesAgrupados.has(
                                    identificador
                                )
                            ) {

                                bloquesAgrupados.set(
                                    identificador,
                                    []
                                );

                            }


                            bloquesAgrupados
                                .get(
                                    identificador
                                )
                                .push(
                                    slot
                                );

                        }
                    );


                    const todosSeleccionados =
                        Array.from(
                            bloquesAgrupados.values()
                        ).every(
                            (copias) =>

                                copias.some(
                                    (slot) =>
                                        slot.classList.contains(
                                            'selected'
                                        )
                                )

                        );


                    bloquesAgrupados.forEach(
                        (copias) => {

                            copias.forEach(
                                (slot) => {

                                    marcarSlot(
                                        slot,
                                        !todosSeleccionados
                                    );

                                }
                            );

                        }
                    );


                    disponibilidades[
                        claveActual()
                    ] =
                        obtenerSeleccionados();


                    actualizarResumen();

                }
            );

        }
    );


    // =========================================================
    // SELECCIONAR TODO
    // =========================================================

    if (selectAllButton) {

        selectAllButton.addEventListener(
            'click',
            () => {

                if (
                    !profesorSelect.value
                ) {

                    alert(
                        'Primero selecciona un profesor.'
                    );


                    profesorSelect.focus();

                    return;

                }


                slots.forEach(
                    (slot) => {

                        marcarSlot(
                            slot,
                            true
                        );

                    }
                );


                disponibilidades[
                    claveActual()
                ] =
                    obtenerSeleccionados();


                actualizarResumen();

            }
        );

    }


    // =========================================================
    // LIMPIAR DISPONIBILIDAD
    // =========================================================

    if (clearButton) {

        clearButton.addEventListener(
            'click',
            () => {

                if (
                    !profesorSelect.value
                ) {

                    alert(
                        'Primero selecciona un profesor.'
                    );


                    profesorSelect.focus();

                    return;

                }


                limpiarGrid();


                disponibilidades[
                    claveActual()
                ] =
                    [];


                actualizarResumen();

            }
        );

    }


    // =========================================================
    // PESTAÑAS MÓVILES
    // =========================================================

    mobileDayTabs.forEach(
        (tab) => {

            tab.addEventListener(
                'click',
                () => {

                    const dia =
                        tab.dataset.dia;


                    mobileDayPanels.forEach(
                        (panel) => {

                            panel.classList.toggle(

                                'hidden',

                                panel.dataset.diaPanel !==
                                    dia

                            );

                        }
                    );


                    mobileDayTabs.forEach(
                        (otraTab) => {

                            const activa =
                                otraTab ===
                                tab;


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

                        }
                    );

                }
            );

        }
    );


    // =========================================================
    // CONVERTIR GRID A FRANJAS
    // =========================================================

    function bloquesGridAFranjas(
        identificadores
    ) {

        const porDia =
            {};


        identificadores.forEach(
            (identificador) => {

                const separador =
                    identificador.indexOf('-');


                const dia =
                    identificador.substring(
                        0,
                        separador
                    );


                const hora =
                    identificador.substring(
                        separador + 1
                    );


                if (!porDia[dia]) {

                    porDia[dia] =
                        [];

                }


                porDia[dia].push(
                    hora
                );

            }
        );


        const franjas =
            [];


        Object.keys(
            porDia
        ).forEach(
            (dia) => {

                const horas =
                    Array.from(
                        new Set(
                            porDia[dia]
                        )
                    ).sort();


                let inicio =
                    null;


                let anterior =
                    null;


                horas.forEach(
                    (hora) => {

                        if (
                            inicio ===
                            null
                        ) {

                            inicio =
                                hora;

                            anterior =
                                hora;

                            return;

                        }


                        const [
                            horaAnterior,
                            minutoAnterior
                        ] =
                            anterior
                                .split(':')
                                .map(Number);


                        const anteriorMinutos =

                            horaAnterior * 60 +
                            minutoAnterior;


                        const esperadoMinutos =

                            anteriorMinutos +
                            DURACION_BLOQUE_GRID_MIN;


                        const horaEsperada =

                            `${String(
                                Math.floor(
                                    esperadoMinutos /
                                    60
                                )
                            ).padStart(
                                2,
                                '0'
                            )}:${String(
                                esperadoMinutos %
                                60
                            ).padStart(
                                2,
                                '0'
                            )}`;


                        /*
                        |--------------------------------------------------------------------------
                        | CORTE
                        |--------------------------------------------------------------------------
                        */

                        if (
                            hora !==
                            horaEsperada
                        ) {

                            const [
                                horaFinAnterior,
                                minutoFinAnterior
                            ] =
                                anterior
                                    .split(':')
                                    .map(Number);


                            const finMinutos =

                                horaFinAnterior *
                                    60 +

                                minutoFinAnterior +

                                DURACION_BLOQUE_GRID_MIN;


                            franjas.push({

                                dia_semana:
                                    Number(
                                        dia
                                    ),

                                hora_inicio:
                                    inicio,

                                hora_fin:

                                    `${String(
                                        Math.floor(
                                            finMinutos /
                                            60
                                        )
                                    ).padStart(
                                        2,
                                        '0'
                                    )}:${String(
                                        finMinutos %
                                        60
                                    ).padStart(
                                        2,
                                        '0'
                                    )}`

                            });


                            inicio =
                                hora;

                        }


                        anterior =
                            hora;

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | CERRAR ÚLTIMA FRANJA
                |--------------------------------------------------------------------------
                */

                if (
                    inicio !== null &&
                    anterior !== null
                ) {

                    const [
                        horaAnterior,
                        minutoAnterior
                    ] =
                        anterior
                            .split(':')
                            .map(Number);


                    const finMinutos =

                        horaAnterior *
                            60 +

                        minutoAnterior +

                        DURACION_BLOQUE_GRID_MIN;


                    franjas.push({

                        dia_semana:
                            Number(
                                dia
                            ),

                        hora_inicio:
                            inicio,

                        hora_fin:

                            `${String(
                                Math.floor(
                                    finMinutos /
                                    60
                                )
                            ).padStart(
                                2,
                                '0'
                            )}:${String(
                                finMinutos %
                                60
                            ).padStart(
                                2,
                                '0'
                            )}`

                    });

                }

            }
        );


        return franjas;

    }


    // =========================================================
    // RENDER HORARIOS PERSONALIZADOS
    // =========================================================

    function renderizarPersonalizados() {

        if (
            !listaPersonalizadosEl
        ) {

            return;

        }


        listaPersonalizadosEl
            .querySelectorAll(
                '.chip-personalizado'
            )
            .forEach(
                (elemento) => {

                    elemento.remove();

                }
            );


        const items =
            personalizadosActuales();


        if (
            personalizadosVacioEl
        ) {

            personalizadosVacioEl.classList.toggle(

                'hidden',

                items.length > 0

            );

        }


        items
            .slice()
            .sort(
                (a, b) =>

                    a.dia -
                        b.dia ||

                    a.horaInicio.localeCompare(
                        b.horaInicio
                    )
            )
            .forEach(
                (item) => {

                    const chip =
                        document.createElement(
                            'span'
                        );


                    chip.className =

                        'chip-personalizado inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-medium text-indigo-700';


                    chip.innerHTML = `

                        ${diasCortos[item.dia] ?? 'Día'}
                        ·
                        ${item.horaInicio}
                        –
                        ${item.horaFin}

                        <button
                            type="button"
                            class="quitar-personalizado text-indigo-400 hover:text-indigo-700"
                            data-id="${item.id}"
                            title="Eliminar horario"
                        >
                            ✕
                        </button>

                    `;


                    listaPersonalizadosEl.appendChild(
                        chip
                    );

                }
            );


        actualizarResumen();

    }


    // =========================================================
    // AGREGAR HORARIO PERSONALIZADO
    // =========================================================

    if (
        agregarPersonalizadoButton
    ) {

        agregarPersonalizadoButton.addEventListener(
            'click',
            () => {

                if (
                    !profesorSelect.value
                ) {

                    alert(
                        'Primero selecciona un profesor.'
                    );


                    profesorSelect.focus();

                    return;

                }


                if (
                    !diaPersonalizadoSelect ||
                    !inicioPersonalizadoInput ||
                    !finPersonalizadoInput
                ) {

                    return;

                }


                const dia =
                    Number(
                        diaPersonalizadoSelect.value
                    );


                const horaInicio =
                    inicioPersonalizadoInput.value;


                const horaFin =
                    finPersonalizadoInput.value;


                if (
                    !dia ||
                    !horaInicio ||
                    !horaFin
                ) {

                    alert(
                        'Completa el día, hora de inicio y hora de fin.'
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


                const clave =
                    claveActual();


                if (
                    !personalizados[
                        clave
                    ]
                ) {

                    personalizados[
                        clave
                    ] =
                        [];

                }


                // =================================================
                // VALIDAR TRASLAPE
                // =================================================

                const traslape =
                    personalizados[
                        clave
                    ].some(
                        (item) =>

                            item.dia ===
                                dia &&

                            horaInicio <
                                item.horaFin &&

                            item.horaInicio <
                                horaFin

                    );


                if (traslape) {

                    alert(
                        'Ese horario se traslapa con otro horario personalizado del mismo día.'
                    );

                    return;

                }


                personalizados[
                    clave
                ].push({

                    id:
                        `${Date.now()}-${Math.random()
                            .toString(36)
                            .slice(2, 8)}`,

                    dia,

                    horaInicio,

                    horaFin

                });


                inicioPersonalizadoInput.value =
                    '';


                finPersonalizadoInput.value =
                    '';


                renderizarPersonalizados();

            }
        );

    }


    // =========================================================
    // QUITAR HORARIO PERSONALIZADO
    // =========================================================

    if (
        listaPersonalizadosEl
    ) {

        listaPersonalizadosEl.addEventListener(
            'click',
            (event) => {

                const boton =
                    event.target.closest(
                        '.quitar-personalizado'
                    );


                if (!boton) {

                    return;

                }


                if (
                    !profesorSelect.value
                ) {

                    return;

                }


                const clave =
                    claveActual();


                personalizados[
                    clave
                ] =
                    (
                        personalizados[
                            clave
                        ] || []
                    ).filter(
                        (item) =>

                            item.id !==
                            boton.dataset.id

                    );


                renderizarPersonalizados();

            }
        );

    }


    // =========================================================
    // GUARDAR DISPONIBILIDAD
    // =========================================================

    if (saveButton) {

        saveButton.addEventListener(
            'click',
            () => {

                const profesorId =
                    profesorSelect.value;


                const institucion =
                    institucionSelect
                        ? institucionSelect.value
                        : 'colegio';


                // =================================================
                // VALIDACIONES
                // =================================================

                if (!profesorId) {

                    alert(
                        'Primero selecciona un profesor.'
                    );


                    profesorSelect.focus();

                    return;

                }


                if (!institucion) {

                    alert(
                        'Selecciona una institución.'
                    );

                    return;

                }


                // =================================================
                // GUARDAR ESTADO DEL GRID
                // =================================================

                disponibilidades[
                    claveActual()
                ] =
                    obtenerSeleccionados();


                const seleccionados =
                    disponibilidades[
                        claveActual()
                    ] || [];


                const franjasGrid =
                    bloquesGridAFranjas(
                        seleccionados
                    );


                const personalizadosData =
                    personalizadosActuales();


                // =================================================
                // UNIR FRANJAS
                // =================================================

                const todasLasFranjas = [

                    ...franjasGrid,

                    ...personalizadosData.map(
                        (item) => ({

                            dia_semana:
                                item.dia,

                            hora_inicio:
                                item.horaInicio,

                            hora_fin:
                                item.horaFin

                        })
                    )

                ];


                // =================================================
                // DATOS ACTUALES
                // =================================================

                const todas =
                    cargarTodasLasDisponibilidades();


                // =================================================
                // ELIMINAR DISPONIBILIDAD ANTERIOR
                // =================================================

                const restantes =
                    todas.filter(
                        (item) => {

                            const idGuardado =
                                item.profesor_id ??
                                item.profesorId;


                            return !(

                                String(
                                    idGuardado
                                ) ===
                                    String(
                                        profesorId
                                    ) &&

                                item.institucion ===
                                    institucion

                            );

                        }
                    );


                // =================================================
                // AGREGAR NUEVAS FRANJAS
                // =================================================

                todasLasFranjas.forEach(
                    (franja) => {

                        restantes.push({

                            profesor_id:
                                String(
                                    profesorId
                                ),

                            institucion,

                            dia_semana:
                                Number(
                                    franja.dia_semana
                                ),

                            hora_inicio:
                                franja.hora_inicio,

                            hora_fin:
                                franja.hora_fin

                        });

                    }
                );


                // =================================================
                // GUARDAR
                // =================================================

                guardarTodasLasDisponibilidades(
                    restantes
                );


                // =================================================
                // MENSAJE
                // =================================================

                const nombreProfesor =
                    profesorSelect.options[
                        profesorSelect.selectedIndex
                    ]?.text ||
                    'Profesor';


                const institucionTexto =
                    institucion ===
                    'academia'
                        ? 'Academia'
                        : 'Colegio';


                alert(

                    `✅ Disponibilidad guardada correctamente.\n\n` +

                    `Profesor: ${nombreProfesor}\n` +

                    `Institución: ${institucionTexto}\n\n` +

                    `Esta información solamente indica cuándo puede trabajar el profesor.\n` +

                    `Todavía no se ha creado ninguna clase.`

                );


                actualizarResumen();

            }
        );

    }


    // =========================================================
    // ACTUALIZAR PROFESORES SI CAMBIAN
    // =========================================================

    window.addEventListener(
        'storage',
        (event) => {

            if (
                event.key ===
                PROFESORES_KEY
            ) {

                cargarProfesores();

            }


            if (
                event.key ===
                DISPONIBILIDAD_KEY
            ) {

                if (
                    profesorSelect.value
                ) {

                    const clave =
                        claveActual();


                    delete disponibilidades[
                        clave
                    ];


                    delete personalizados[
                        clave
                    ];


                    alCambiarSeleccion();

                }

            }

        }
    );


    // =========================================================
    // ACTUALIZAR AL REGRESAR A LA PESTAÑA
    // =========================================================

    window.addEventListener(
        'focus',
        () => {

            const seleccionado =
                profesorSelect.value;


            cargarProfesores();


            if (
                seleccionado &&
                Array.from(
                    profesorSelect.options
                ).some(
                    (option) =>
                        option.value ===
                        seleccionado
                )
            ) {

                profesorSelect.value =
                    seleccionado;

            }

        }
    );


    // =========================================================
    // ESTADO INICIAL
    // =========================================================

    cargarProfesores();

    limpiarGrid();

    renderizarPersonalizados();

    actualizarResumen();

});