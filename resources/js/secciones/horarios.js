document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // ELEMENTOS PRINCIPALES
    // =========================================================

    const contenedor =
        document.getElementById('horarios-render-container');

    if (!contenedor) {
        return;
    }


    // =========================================================
    // STORAGE
    // =========================================================

    const KEYS = {

        horarios:
            'nextlevel_horarios',

        disponibilidades:
            'nextlevel_disponibilidades',

        profesores:
            'nextlevel_profesores',

        aulas:
            'nextlevel_aulas',

        grados:
            'nextlevel_grados',

        cursos:
            'nextlevel_cursos'

    };


    // =========================================================
    // DÍAS
    // =========================================================

    const DIAS_NOMBRES = [
        '',
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes',
        'Sábado'
    ];


    const DIAS_CORTOS = [
        '',
        'Lun',
        'Mar',
        'Mié',
        'Jue',
        'Vie',
        'Sáb'
    ];


    const DIAS_SEMANA = [
        1,
        2,
        3,
        4,
        5,
        6
    ];


    // =========================================================
    // CONTROLES
    // =========================================================

    const tabsInstitucion =
        document.querySelectorAll('.institucion-tab');

    const vistaBotones =
        document.querySelectorAll('.vista-btn');

    const chkCompleto =
        document.getElementById('chk-semana-completa');


    const filtroProfesor =
        document.getElementById('horario-profesor');

    const filtroAula =
        document.getElementById('horario-aula');

    const filtroGrado =
        document.getElementById('horario-grado');

    const filtroCurso =
        document.getElementById('horario-curso');


    const btnReset =
        document.getElementById('btn-reset');

    const btnPdf =
        document.getElementById('btn-pdf');

    const btnExcel =
        document.getElementById('btn-excel');


    const tituloEl =
        document.getElementById('titulo-horario');

    const subtituloEl =
        document.getElementById('subtitulo-horario');


    const statProfesores =
        document.getElementById('total-profesores');

    const statAulas =
        document.getElementById('total-aulas');

    const statClases =
        document.getElementById('total-clases');

    const statEstado =
        document.getElementById('estado-horario');

    const statEstadoPunto =
        document.getElementById('estado-punto');


    const mensajeEl =
        document.getElementById('horario-mensaje');

    const modoEdicionEl =
        document.getElementById('modo-edicion-horario');


    // =========================================================
    // ESTADO
    // =========================================================

    let institucionActiva =
        'colegio';

    let vistaActual =
        'profesor';

    let bloqueArrastradoId =
        null;


    // =========================================================
    // PERMISOS
    // =========================================================
    //
    // Todavía estamos trabajando frontend.
    //
    // Si más adelante el backend proporciona el rol,
    // puede guardarse temporalmente como:
    //
    // localStorage.setItem(
    //     'nextlevel_rol',
    //     'director'
    // );
    //
    // Si no existe, asumimos administrador.
    // =========================================================

    function puedeEditarHorario() {

        const rol =
            (
                localStorage.getItem(
                    'nextlevel_rol'
                ) ||
                'administrador'
            )
                .toLowerCase()
                .trim();


        return [
            'administrador',
            'director'
        ].includes(rol);

    }


    if (
        modoEdicionEl &&
        !puedeEditarHorario()
    ) {

        modoEdicionEl.innerHTML =
            `
                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                Solo lectura
            `;


        modoEdicionEl.className =
            'inline-flex shrink-0 items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600';

    }


    // =========================================================
    // STORAGE
    // =========================================================

    function leer(clave) {

        try {

            const data =
                localStorage.getItem(
                    clave
                );


            return data
                ? JSON.parse(data)
                : [];

        } catch (error) {

            console.error(
                `Error leyendo ${clave}:`,
                error
            );


            return [];

        }

    }


    function guardar(
        clave,
        datos
    ) {

        localStorage.setItem(
            clave,
            JSON.stringify(datos)
        );

    }


    // =========================================================
    // HORARIOS
    // =========================================================

    function cargarHorarios() {

        return leer(
            KEYS.horarios
        ).filter(
            (horario) =>

                horario &&
                horario.hora_inicio &&
                horario.hora_fin &&
                horario.dia_semana

        );

    }


    function guardarHorarios(
        horarios
    ) {

        guardar(
            KEYS.horarios,
            horarios
        );

    }


    // =========================================================
    // UTILIDADES DE TIEMPO
    // =========================================================

    function aMinutos(hora) {

        if (!hora) {
            return 0;
        }


        const [
            horas,
            minutos
        ] =
            String(hora)
                .split(':')
                .map(Number);


        return (
            (horas * 60) +
            minutos
        );

    }


    function aTexto(minutos) {

        const horas =
            String(
                Math.floor(
                    minutos / 60
                )
            ).padStart(
                2,
                '0'
            );


        const minutosRestantes =
            String(
                minutos % 60
            ).padStart(
                2,
                '0'
            );


        return `${horas}:${minutosRestantes}`;

    }


    function existeTraslape(
        inicioA,
        finA,
        inicioB,
        finB
    ) {

        return (
            aMinutos(inicioA) <
                aMinutos(finB) &&

            aMinutos(inicioB) <
                aMinutos(finA)
        );

    }


    // =========================================================
    // ESCAPAR TEXTO
    // =========================================================

    function esc(texto) {

        if (!texto) {
            return '';
        }


        return String(texto)

            .replace(
                /&/g,
                '&amp;'
            )

            .replace(
                /</g,
                '&lt;'
            )

            .replace(
                />/g,
                '&gt;'
            )

            .replace(
                /"/g,
                '&quot;'
            );

    }


    // =========================================================
    // CATÁLOGOS
    // =========================================================

    function getCatalogos() {

        const profesores =
            leer(KEYS.profesores);

        const aulas =
            leer(KEYS.aulas);

        const grados =
            leer(KEYS.grados);

        const cursos =
            leer(KEYS.cursos);


        return {

            profesores,

            aulas,

            grados,

            cursos,


            mapProf:
                new Map(
                    profesores.map(
                        (item) => [
                            String(item.id),
                            item
                        ]
                    )
                ),


            mapAula:
                new Map(
                    aulas.map(
                        (item) => [
                            String(item.id),
                            item
                        ]
                    )
                ),


            mapGrado:
                new Map(
                    grados.map(
                        (item) => [
                            String(item.id),
                            item
                        ]
                    )
                ),


            mapCurso:
                new Map(
                    cursos.map(
                        (item) => [
                            String(item.id),
                            item
                        ]
                    )
                )

        };

    }


    function nombreProfesor(
        profesor,
        id
    ) {

        if (!profesor) {

            return `Profesor ${id ?? '?'}`;

        }


        return `${profesor.nombre ?? ''} ${profesor.apellido_paterno ?? ''}`
            .trim();

    }


    function nombreAula(
        aula,
        id
    ) {

        return aula
            ? aula.nombre
            : `Aula ${id ?? '?'}`;

    }


    function nombreCurso(
        curso,
        id
    ) {

        return curso
            ? curso.nombre
            : `Curso ${id ?? '?'}`;

    }


    function nombreGrado(
        grado,
        id
    ) {

        if (!grado) {

            return id
                ? `Grado ${id}`
                : 'Sin grado';

        }


        return (
            grado.nombre_completo ||
            `${grado.grado ?? ''} ${grado.seccion ?? ''}`.trim()
        );

    }


    // =========================================================
    // VISTA
    // =========================================================

    function getAgrupaPor() {

        if (
            vistaActual ===
            'aula-completa'
        ) {

            return 'aula';

        }


        return vistaActual;

    }


    function isVistaCompleta() {

        return (
            vistaActual ===
                'aula-completa' ||

            (
                chkCompleto &&
                chkCompleto.checked
            )
        );

    }


    // =========================================================
    // COLORES
    // =========================================================

    const colores = [

        {
            bg: '#eef2ff',
            text: '#312e81',
            sub: '#4338ca'
        },

        {
            bg: '#ecfdf5',
            text: '#064e3b',
            sub: '#047857'
        },

        {
            bg: '#eff6ff',
            text: '#1e3a8a',
            sub: '#1d4ed8'
        },

        {
            bg: '#fffbeb',
            text: '#78350f',
            sub: '#b45309'
        },

        {
            bg: '#faf5ff',
            text: '#4c1d95',
            sub: '#7e22ce'
        },

        {
            bg: '#f0f9ff',
            text: '#0c4a6e',
            sub: '#0369a1'
        },

        {
            bg: '#fff1f2',
            text: '#881337',
            sub: '#be123c'
        },

        {
            bg: '#f0fdfa',
            text: '#134e4a',
            sub: '#0f766e'
        }

    ];


    const colorCache = {};


    function getColorCurso(
        nombre
    ) {

        if (
            !colorCache[nombre]
        ) {

            const indice =
                Object.keys(
                    colorCache
                ).length %
                colores.length;


            colorCache[nombre] =
                colores[indice];

        }


        return colorCache[nombre];

    }


    // =========================================================
    // FILTROS
    // =========================================================

    function poblarSelect(
        select,
        opciones,
        textoDefault
    ) {

        if (!select) {
            return;
        }


        const seleccionado =
            select.value ||
            'todos';


        select.innerHTML =
            '';


        const defecto =
            document.createElement(
                'option'
            );


        defecto.value =
            'todos';

        defecto.textContent =
            textoDefault;


        select.appendChild(
            defecto
        );


        opciones.sort(
            (a, b) =>
                a.texto.localeCompare(
                    b.texto,
                    'es',
                    {
                        numeric: true
                    }
                )
        );


        opciones.forEach(
            (opcion) => {

                const element =
                    document.createElement(
                        'option'
                    );


                element.value =
                    String(
                        opcion.id
                    );


                element.textContent =
                    opcion.texto;


                select.appendChild(
                    element
                );

            }
        );


        if (
            Array.from(
                select.options
            ).some(
                (option) =>
                    option.value ===
                    seleccionado
            )
        ) {

            select.value =
                seleccionado;

        }

    }


    function cargarFiltros(
        bloques
    ) {

        const {
            mapProf,
            mapAula,
            mapGrado,
            mapCurso
        } =
            getCatalogos();


        function unicos(
            campo,
            resolver
        ) {

            const vistos =
                new Map();


            bloques.forEach(
                (bloque) => {

                    const id =
                        bloque[campo];


                    if (
                        id === null ||
                        id === undefined
                    ) {

                        return;

                    }


                    const key =
                        String(id);


                    if (
                        !vistos.has(
                            key
                        )
                    ) {

                        vistos.set(
                            key,
                            {
                                id,

                                texto:
                                    resolver(
                                        key,
                                        id
                                    )
                            }
                        );

                    }

                }
            );


            return Array.from(
                vistos.values()
            );

        }


        poblarSelect(

            filtroProfesor,

            unicos(
                'profesor_id',
                (key, id) =>
                    nombreProfesor(
                        mapProf.get(key),
                        id
                    )
            ),

            'Todos los profesores'

        );


        poblarSelect(

            filtroAula,

            unicos(
                'aula_id',
                (key, id) =>
                    nombreAula(
                        mapAula.get(key),
                        id
                    )
            ),

            'Todas las aulas'

        );


        poblarSelect(

            filtroGrado,

            unicos(
                'grado_id',
                (key, id) =>
                    nombreGrado(
                        mapGrado.get(key),
                        id
                    )
            ),

            'Todos'

        );


        poblarSelect(

            filtroCurso,

            unicos(
                'curso_id',
                (key, id) =>
                    nombreCurso(
                        mapCurso.get(key),
                        id
                    )
            ),

            'Todos los cursos'

        );

    }


    // =========================================================
    // DÍAS DEL TABLERO
    // =========================================================

    function obtenerDias(
        bloques,
        completo
    ) {

        if (completo) {

            return DIAS_SEMANA;

        }


        const dias =
            Array.from(
                new Set(
                    bloques.map(
                        (bloque) =>
                            Number(
                                bloque.dia_semana
                            )
                    )
                )
            )
                .sort(
                    (a, b) =>
                        a - b
                );


        return dias.length
            ? dias
            : [1];

    }


    // =========================================================
    // FILAS HORARIAS
    // =========================================================

    function construirSlots(
        bloques,
        completo
    ) {

        /*
        |--------------------------------------------------------------------------
        | SEMANA COMPLETA
        |--------------------------------------------------------------------------
        |
        | Mostramos 07:00 - 20:00 incluso si todavía existen horas libres.
        | Esto permite que el administrador tenga celdas donde arrastrar
        | una clase.
        |
        */

        if (completo) {

            const slots = [];


            for (
                let minutos = 420;
                minutos < 1200;
                minutos += 60
            ) {

                slots.push({

                    ini:
                        minutos,

                    fin:
                        minutos + 60

                });

            }


            return slots;

        }


        const limites =
            new Set();


        bloques.forEach(
            (bloque) => {

                limites.add(
                    aMinutos(
                        bloque.hora_inicio
                    )
                );


                limites.add(
                    aMinutos(
                        bloque.hora_fin
                    )
                );

            }
        );


        if (
            limites.size <
            2
        ) {

            limites.add(
                420
            );

            limites.add(
                480
            );

        }


        const ordenados =
            Array.from(
                limites
            ).sort(
                (a, b) =>
                    a - b
            );


        const slots = [];


        for (
            let i = 0;
            i <
            ordenados.length - 1;
            i++
        ) {

            slots.push({

                ini:
                    ordenados[i],

                fin:
                    ordenados[i + 1]

            });

        }


        return slots;

    }


    // =========================================================
    // MATRIZ
    // =========================================================

    function construirMatriz(
        bloques,
        dias,
        slots
    ) {

        const matriz = {};


        dias.forEach(
            (dia) => {

                matriz[dia] =
                    new Array(
                        slots.length
                    ).fill(null);

            }
        );


        dias.forEach(
            (dia) => {

                const bloquesDia =
                    bloques

                        .filter(
                            (bloque) =>
                                Number(
                                    bloque.dia_semana
                                ) ===
                                Number(dia)
                        )

                        .sort(
                            (a, b) =>
                                aMinutos(
                                    a.hora_inicio
                                ) -
                                aMinutos(
                                    b.hora_inicio
                                )
                        );


                bloquesDia.forEach(
                    (bloque) => {

                        const inicioMin =
                            aMinutos(
                                bloque.hora_inicio
                            );


                        const finMin =
                            aMinutos(
                                bloque.hora_fin
                            );


                        let inicio =
                            -1;


                        for (
                            let i = 0;
                            i < slots.length;
                            i++
                        ) {

                            if (
                                slots[i].ini <=
                                    inicioMin &&

                                inicioMin <
                                    slots[i].fin
                            ) {

                                inicio = i;

                                break;

                            }

                        }


                        if (
                            inicio ===
                            -1
                        ) {

                            return;

                        }


                        let span = 0;


                        for (
                            let i = inicio;
                            i <
                                slots.length &&
                            slots[i].ini <
                                finMin;
                            i++
                        ) {

                            span++;

                        }


                        span =
                            Math.max(
                                1,
                                span
                            );


                        const actual =
                            matriz[dia][inicio];


                        if (
                            actual &&
                            actual.tipo ===
                                'bloque'
                        ) {

                            actual.bloques.push(
                                bloque
                            );

                            actual.conflicto =
                                true;

                            return;

                        }


                        if (
                            actual &&
                            actual.tipo ===
                                'cont'
                        ) {

                            const propietario =
                                matriz[dia][
                                    actual.dueno
                                ];


                            propietario.bloques.push(
                                bloque
                            );

                            propietario.conflicto =
                                true;

                            return;

                        }


                        matriz[dia][inicio] = {

                            tipo:
                                'bloque',

                            bloques:
                                [bloque],

                            span,

                            conflicto:
                                false

                        };


                        for (
                            let i =
                                inicio + 1;
                            i <
                                inicio +
                                span;
                            i++
                        ) {

                            matriz[dia][i] = {

                                tipo:
                                    'cont',

                                dueno:
                                    inicio

                            };

                        }

                    }
                );

            }
        );


        return matriz;

    }


    // =========================================================
    // TEXTO DE UNA CLASE
    // =========================================================

    function getLineasBloque(
        bloque
    ) {

        const {
            mapProf,
            mapAula,
            mapGrado,
            mapCurso
        } =
            getCatalogos();


        const curso =
            nombreCurso(
                mapCurso.get(
                    String(
                        bloque.curso_id
                    )
                ),
                bloque.curso_id
            );


        const profesor =
            nombreProfesor(
                mapProf.get(
                    String(
                        bloque.profesor_id
                    )
                ),
                bloque.profesor_id
            );


        const aula =
            nombreAula(
                mapAula.get(
                    String(
                        bloque.aula_id
                    )
                ),
                bloque.aula_id
            );


        const grado =
            nombreGrado(
                mapGrado.get(
                    String(
                        bloque.grado_id
                    )
                ),
                bloque.grado_id
            );


        const agrupa =
            getAgrupaPor();


        if (
            agrupa ===
            'profesor'
        ) {

            return {

                curso,

                sec:
                    aula,

                ter:
                    grado

            };

        }


        if (
            agrupa ===
            'aula'
        ) {

            return {

                curso,

                sec:
                    profesor,

                ter:
                    grado

            };

        }


        return {

            curso,

            sec:
                profesor,

            ter:
                aula

        };

    }


    // =========================================================
    // AGRUPAR
    // =========================================================

    function agruparBloques(
        bloques
    ) {

        const {
            mapProf,
            mapAula,
            mapGrado
        } =
            getCatalogos();


        const grupos =
            new Map();


        const agrupa =
            getAgrupaPor();


        bloques.forEach(
            (bloque) => {

                let clave;

                let nombre;


                if (
                    agrupa ===
                    'profesor'
                ) {

                    clave =
                        `prof-${bloque.profesor_id}`;


                    nombre =
                        nombreProfesor(
                            mapProf.get(
                                String(
                                    bloque.profesor_id
                                )
                            ),
                            bloque.profesor_id
                        );

                } else if (
                    agrupa ===
                    'aula'
                ) {

                    clave =
                        `aula-${bloque.aula_id}`;


                    nombre =
                        nombreAula(
                            mapAula.get(
                                String(
                                    bloque.aula_id
                                )
                            ),
                            bloque.aula_id
                        );

                } else {

                    clave =
                        `grado-${bloque.grado_id}`;


                    nombre =
                        nombreGrado(
                            mapGrado.get(
                                String(
                                    bloque.grado_id
                                )
                            ),
                            bloque.grado_id
                        );

                }


                if (
                    !grupos.has(
                        clave
                    )
                ) {

                    grupos.set(
                        clave,
                        {

                            clave,

                            nombre,

                            bloques:
                                []

                        }
                    );

                }


                grupos
                    .get(clave)
                    .bloques
                    .push(bloque);

            }
        );


        return Array.from(
            grupos.values()
        ).sort(
            (a, b) =>
                a.nombre.localeCompare(
                    b.nombre,
                    'es',
                    {
                        numeric: true
                    }
                )
        );

    }


    // =========================================================
    // CONFLICTOS GENERALES
    // =========================================================

    function detectarConflictos(
        bloques
    ) {

        const conflictos = [];


        [
            'profesor_id',
            'aula_id',
            'grado_id'
        ].forEach(
            (campo) => {

                const agrupados =
                    new Map();


                bloques.forEach(
                    (bloque) => {

                        const id =
                            bloque[campo];


                        if (
                            id === null ||
                            id === undefined
                        ) {

                            return;

                        }


                        const key =
                            `${id}-${bloque.dia_semana}`;


                        if (
                            !agrupados.has(
                                key
                            )
                        ) {

                            agrupados.set(
                                key,
                                []
                            );

                        }


                        agrupados
                            .get(key)
                            .push(bloque);

                    }
                );


                agrupados.forEach(
                    (lista) => {

                        lista.sort(
                            (a, b) =>
                                aMinutos(
                                    a.hora_inicio
                                ) -
                                aMinutos(
                                    b.hora_inicio
                                )
                        );


                        for (
                            let i = 0;
                            i <
                            lista.length - 1;
                            i++
                        ) {

                            if (
                                existeTraslape(
                                    lista[i].hora_inicio,
                                    lista[i].hora_fin,
                                    lista[i + 1].hora_inicio,
                                    lista[i + 1].hora_fin
                                )
                            ) {

                                conflictos.push({

                                    campo,

                                    a:
                                        lista[i],

                                    b:
                                        lista[i + 1]

                                });

                            }

                        }

                    }
                );

            }
        );


        return conflictos;

    }


    // =========================================================
    // DISPONIBILIDAD PARA DRAG & DROP
    // =========================================================

    function profesorDisponible(
        bloque,
        nuevoDia,
        nuevaHoraInicio,
        nuevaHoraFin
    ) {

        const disponibilidades =
            leer(
                KEYS.disponibilidades
            );


        return disponibilidades.some(
            (item) => {

                const profesorId =
                    item.profesor_id ??
                    item.profesorId;


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


                return (

                    String(
                        profesorId
                    ) ===
                        String(
                            bloque.profesor_id
                        ) &&

                    item.institucion ===
                        bloque.institucion &&

                    dia ===
                        Number(
                            nuevoDia
                        ) &&

                    nuevaHoraInicio >=
                        inicio &&

                    nuevaHoraFin <=
                        fin

                );

            }
        );

    }


    // =========================================================
    // VALIDAR MOVIMIENTO
    // =========================================================

    function validarMovimiento(
        bloque,
        nuevoDia,
        nuevaHoraInicio,
        nuevaHoraFin
    ) {

        if (
            !profesorDisponible(
                bloque,
                nuevoDia,
                nuevaHoraInicio,
                nuevaHoraFin
            )
        ) {

            return {

                valido:
                    false,

                mensaje:
                    `El profesor no está disponible el ${DIAS_NOMBRES[nuevoDia]} de ${nuevaHoraInicio} a ${nuevaHoraFin}.`

            };

        }


        const horarios =
            cargarHorarios();


        const restantes =
            horarios.filter(
                (item) =>
                    String(item.id) !==
                    String(bloque.id)
            );


        // =====================================================
        // PROFESOR
        // =====================================================

        const conflictoProfesor =
            restantes.find(
                (item) =>

                    String(
                        item.profesor_id
                    ) ===
                        String(
                            bloque.profesor_id
                        ) &&

                    Number(
                        item.dia_semana
                    ) ===
                        Number(
                            nuevoDia
                        ) &&

                    existeTraslape(
                        nuevaHoraInicio,
                        nuevaHoraFin,
                        item.hora_inicio,
                        item.hora_fin
                    )

            );


        if (conflictoProfesor) {

            return {

                valido:
                    false,

                mensaje:
                    'El profesor ya tiene otra clase en ese horario.'

            };

        }


        // =====================================================
        // AULA
        // =====================================================

        const conflictoAula =
            restantes.find(
                (item) =>

                    String(
                        item.aula_id
                    ) ===
                        String(
                            bloque.aula_id
                        ) &&

                    Number(
                        item.dia_semana
                    ) ===
                        Number(
                            nuevoDia
                        ) &&

                    existeTraslape(
                        nuevaHoraInicio,
                        nuevaHoraFin,
                        item.hora_inicio,
                        item.hora_fin
                    )

            );


        if (conflictoAula) {

            return {

                valido:
                    false,

                mensaje:
                    'El aula ya está ocupada en ese horario.'

            };

        }


        // =====================================================
        // GRADO
        // =====================================================

        if (
            bloque.grado_id !==
                null &&
            bloque.grado_id !==
                undefined
        ) {

            const conflictoGrado =
                restantes.find(
                    (item) =>

                        item.grado_id !==
                            null &&

                        String(
                            item.grado_id
                        ) ===
                            String(
                                bloque.grado_id
                            ) &&

                        Number(
                            item.dia_semana
                        ) ===
                            Number(
                                nuevoDia
                            ) &&

                        existeTraslape(
                            nuevaHoraInicio,
                            nuevaHoraFin,
                            item.hora_inicio,
                            item.hora_fin
                        )

                );


            if (conflictoGrado) {

                return {

                    valido:
                        false,

                    mensaje:
                        'El grado o grupo ya tiene otra clase en ese horario.'

                };

            }

        }


        return {

            valido:
                true,

            mensaje:
                'Movimiento permitido.'

        };

    }


    // =========================================================
    // MENSAJES
    // =========================================================

    let timerMensaje =
        null;


    function mostrarMensaje(
        tipo,
        titulo,
        texto
    ) {

        if (!mensajeEl) {

            if (
                tipo ===
                'error'
            ) {

                alert(texto);

            }

            return;

        }


        clearTimeout(
            timerMensaje
        );


        mensajeEl.classList.remove(
            'hidden',
            'border-emerald-200',
            'border-red-200'
        );


        if (
            tipo ===
            'error'
        ) {

            mensajeEl.classList.add(
                'border-red-200'
            );


            mensajeEl.innerHTML =
                `
                    <div class="flex gap-3">
                        <div class="text-lg">❌</div>
                        <div>
                            <p class="text-sm font-bold text-red-700">
                                ${esc(titulo)}
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-600">
                                ${esc(texto)}
                            </p>
                        </div>
                    </div>
                `;

        } else {

            mensajeEl.classList.add(
                'border-emerald-200'
            );


            mensajeEl.innerHTML =
                `
                    <div class="flex gap-3">
                        <div class="text-lg">✅</div>
                        <div>
                            <p class="text-sm font-bold text-emerald-700">
                                ${esc(titulo)}
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-600">
                                ${esc(texto)}
                            </p>
                        </div>
                    </div>
                `;

        }


        timerMensaje =
            setTimeout(
                () => {

                    mensajeEl
                        .classList
                        .add(
                            'hidden'
                        );

                },
                4500
            );

    }


    // =========================================================
    // MOVER CLASE
    // =========================================================

    function moverClase(
        id,
        nuevoDia,
        nuevaHoraInicio
    ) {

        const horarios =
            cargarHorarios();


        const indice =
            horarios.findIndex(
                (item) =>
                    String(item.id) ===
                    String(id)
            );


        if (
            indice ===
            -1
        ) {

            mostrarMensaje(
                'error',
                'No se encontró la clase',
                'Actualiza la página e inténtalo nuevamente.'
            );

            return;

        }


        const bloque =
            horarios[indice];


        const duracion =
            aMinutos(
                bloque.hora_fin
            ) -
            aMinutos(
                bloque.hora_inicio
            );


        const nuevoInicioMin =
            aMinutos(
                nuevaHoraInicio
            );


        const nuevoFinMin =
            nuevoInicioMin +
            duracion;


        const nuevaHoraFin =
            aTexto(
                nuevoFinMin
            );


        const validacion =
            validarMovimiento(
                bloque,
                Number(
                    nuevoDia
                ),
                nuevaHoraInicio,
                nuevaHoraFin
            );


        if (
            !validacion.valido
        ) {

            mostrarMensaje(
                'error',
                'No se puede mover la clase',
                validacion.mensaje
            );


            renderizar();

            return;

        }


        horarios[indice] = {

            ...bloque,

            dia_semana:
                Number(
                    nuevoDia
                ),

            hora_inicio:
                nuevaHoraInicio,

            hora_fin:
                nuevaHoraFin

        };


        guardarHorarios(
            horarios
        );


        mostrarMensaje(
            'ok',
            'Clase movida',
            `${DIAS_NOMBRES[nuevoDia]} · ${nuevaHoraInicio} - ${nuevaHoraFin}`
        );


        renderizar();

    }


    // =========================================================
    // MISMO TABLERO
    // =========================================================
    //
    // Arrastrar una clase en "Por profesor" NO cambia profesor.
    // Arrastrarla en "Por aula" NO cambia aula.
    //
    // Para cambiar profesor/aula/grado se debe hacer desde
    // Asignaciones.
    // =========================================================

    function claveGrupoDelBloque(
        bloque
    ) {

        const agrupa =
            getAgrupaPor();


        if (
            agrupa ===
            'profesor'
        ) {

            return `prof-${bloque.profesor_id}`;

        }


        if (
            agrupa ===
            'aula'
        ) {

            return `aula-${bloque.aula_id}`;

        }


        return `grado-${bloque.grado_id}`;

    }


    // =========================================================
    // ACTIVAR DRAG & DROP
    // =========================================================

    function activarDragDrop() {

        if (
            !puedeEditarHorario()
        ) {

            return;

        }


        const clases =
            contenedor.querySelectorAll(
                '.horario-clase'
            );


        const zonas =
            contenedor.querySelectorAll(
                '.horario-dropzone'
            );


        clases.forEach(
            (clase) => {

                clase.addEventListener(
                    'dragstart',
                    (event) => {

                        bloqueArrastradoId =
                            clase.dataset.id;


                        clase.classList.add(
                            'dragging'
                        );


                        event.dataTransfer.effectAllowed =
                            'move';


                        event.dataTransfer.setData(
                            'text/plain',
                            bloqueArrastradoId
                        );

                    }
                );


                clase.addEventListener(
                    'dragend',
                    () => {

                        bloqueArrastradoId =
                            null;


                        clase.classList.remove(
                            'dragging'
                        );


                        zonas.forEach(
                            (zona) =>
                                zona.classList.remove(
                                    'drag-over'
                                )
                        );

                    }
                );

            }
        );


        zonas.forEach(
            (zona) => {

                zona.addEventListener(
                    'dragover',
                    (event) => {

                        event.preventDefault();


                        event.dataTransfer.dropEffect =
                            'move';


                        zona.classList.add(
                            'drag-over'
                        );

                    }
                );


                zona.addEventListener(
                    'dragleave',
                    () => {

                        zona.classList.remove(
                            'drag-over'
                        );

                    }
                );


                zona.addEventListener(
                    'drop',
                    (event) => {

                        event.preventDefault();


                        zona.classList.remove(
                            'drag-over'
                        );


                        const id =
                            event.dataTransfer.getData(
                                'text/plain'
                            ) ||
                            bloqueArrastradoId;


                        if (!id) {

                            return;

                        }


                        const horarios =
                            cargarHorarios();


                        const bloque =
                            horarios.find(
                                (item) =>
                                    String(
                                        item.id
                                    ) ===
                                    String(id)
                            );


                        if (!bloque) {

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | NO CAMBIAMOS EL PROPIETARIO DEL TABLERO
                        |--------------------------------------------------------------------------
                        */

                        const grupoEsperado =
                            claveGrupoDelBloque(
                                bloque
                            );


                        if (
                            zona.dataset.grupo !==
                            grupoEsperado
                        ) {

                            mostrarMensaje(
                                'error',
                                'Movimiento no permitido',
                                'Para cambiar profesor, aula o grado debes hacerlo desde Asignaciones.'
                            );

                            return;

                        }


                        moverClase(

                            id,

                            Number(
                                zona.dataset.dia
                            ),

                            zona.dataset.hora

                        );

                    }
                );

            }
        );

    }


    // =========================================================
    // CONSTRUIR TABLERO
    // =========================================================

    function construirTablero(
        grupo
    ) {

        const completo =
            isVistaCompleta();


        const dias =
            obtenerDias(
                grupo.bloques,
                completo
            );


        const slots =
            construirSlots(
                grupo.bloques,
                completo
            );


        const matriz =
            construirMatriz(
                grupo.bloques,
                dias,
                slots
            );


        const icono =
            getAgrupaPor() ===
                'profesor'

                ? '👨‍🏫'

                : getAgrupaPor() ===
                    'aula'

                    ? '🏫'

                    : '🎓';


        const tarjeta =
            document.createElement(
                'div'
            );


        tarjeta.className =
            'horario-card overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm';


        // =====================================================
        // CABECERA DÍAS
        // =====================================================

        const thead =
            dias
                .map(
                    (dia) =>

                        `
                            <th
                                class="border-r border-slate-700 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider"
                            >
                                ${DIAS_CORTOS[dia] ?? dia}
                            </th>
                        `

                )
                .join('');


        // =====================================================
        // FILAS
        // =====================================================

        const filasHTML =
            slots
                .map(
                    (slot, indice) => {

                        const celdas =
                            dias
                                .map(
                                    (dia) => {

                                        const celda =
                                            matriz[dia][indice];


                                        /*
                                        |--------------------------------------------------------------------------
                                        | CONTINUACIÓN DE UNA CLASE LARGA
                                        |--------------------------------------------------------------------------
                                        */

                                        if (
                                            celda &&
                                            celda.tipo ===
                                                'cont'
                                        ) {

                                            return '';

                                        }


                                        const dataDrop =
                                            `
                                                data-dia="${dia}"
                                                data-hora="${aTexto(slot.ini)}"
                                                data-grupo="${esc(grupo.clave)}"
                                            `;


                                        /*
                                        |--------------------------------------------------------------------------
                                        | LIBRE
                                        |--------------------------------------------------------------------------
                                        */

                                        if (!celda) {

                                            return `
                                                <td
                                                    class="horario-dropzone border-r border-slate-200 p-2 align-top"
                                                    ${dataDrop}
                                                >
                                                    <div class="flex min-h-[72px] items-center justify-center rounded-lg border border-dashed border-slate-200">
                                                        <span class="text-xs text-slate-400">
                                                            Libre
                                                        </span>
                                                    </div>
                                                </td>
                                            `;

                                        }


                                        // =================================================
                                        // CLASE
                                        // =================================================

                                        const cuerpo =
                                            celda.bloques
                                                .map(
                                                    (bloque) => {

                                                        const lineas =
                                                            getLineasBloque(
                                                                bloque
                                                            );


                                                        const color =
                                                            getColorCurso(
                                                                lineas.curso
                                                            );


                                                        const draggable =
                                                            puedeEditarHorario()
                                                                ? 'true'
                                                                : 'false';


                                                        return `

                                                            <div
                                                                class="horario-clase rounded-lg p-3"
                                                                draggable="${draggable}"
                                                                data-id="${bloque.id}"
                                                                style="background:${color.bg};"
                                                            >

                                                                <div class="flex items-start justify-between gap-2">

                                                                    <p
                                                                        class="text-sm font-semibold"
                                                                        style="color:${color.text};"
                                                                    >
                                                                        ${esc(lineas.curso)}
                                                                    </p>

                                                                    ${
                                                                        puedeEditarHorario()
                                                                            ? `
                                                                                <span
                                                                                    class="shrink-0 text-xs opacity-40"
                                                                                    title="Arrastrar clase"
                                                                                >
                                                                                    ⠿
                                                                                </span>
                                                                              `
                                                                            : ''
                                                                    }

                                                                </div>


                                                                <p
                                                                    class="mt-1 text-xs"
                                                                    style="color:${color.sub};"
                                                                >
                                                                    ${esc(lineas.sec)}
                                                                </p>


                                                                <p class="mt-1 text-xs text-slate-500">
                                                                    ${esc(lineas.ter)}
                                                                </p>


                                                                <p class="mt-1 text-[11px] text-slate-400">
                                                                    ${bloque.hora_inicio}
                                                                    –
                                                                    ${bloque.hora_fin}
                                                                </p>

                                                            </div>

                                                        `;

                                                    }
                                                )
                                                .join('');


                                        const aviso =
                                            celda.conflicto

                                                ? `
                                                    <p class="mb-1 text-[11px] font-semibold text-rose-600">
                                                        ⚠ Cruce de horario
                                                    </p>
                                                  `

                                                : '';


                                        return `

                                            <td
                                                class="horario-dropzone border-r border-slate-200 p-2 align-top"
                                                rowspan="${celda.span}"
                                                ${dataDrop}
                                            >

                                                ${aviso}

                                                <div class="space-y-1">
                                                    ${cuerpo}
                                                </div>

                                            </td>

                                        `;

                                    }
                                )
                                .join('');


                        return `

                            <tr class="border-b border-slate-200">

                                <td class="bg-slate-50 px-4 py-3 text-center align-top">

                                    <span class="text-sm font-semibold text-slate-700">
                                        ${aTexto(slot.ini)}
                                    </span>

                                    <span class="block text-xs text-slate-400">
                                        ${aTexto(slot.fin)}
                                    </span>

                                </td>

                                ${celdas}

                            </tr>

                        `;

                    }
                )
                .join('');


        // =====================================================
        // TARJETA
        // =====================================================

        tarjeta.innerHTML =
            `

                <div class="border-b border-slate-200 px-5 py-4">

                    <div class="flex flex-wrap items-center justify-between gap-2">

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                ${icono}
                                ${esc(grupo.nombre)}
                            </h2>

                            <p class="mt-1 text-xs text-slate-500">
                                ${grupo.bloques.length}
                                clase(s) asignadas
                            </p>

                        </div>


                        <div class="flex items-center gap-2">

                            ${
                                puedeEditarHorario()

                                    ? `
                                        <span class="hidden text-[11px] text-slate-400 sm:inline">
                                            Arrastra una clase para moverla
                                        </span>
                                      `

                                    : ''
                            }


                            <span class="inline-flex rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700">
                                ${
                                    institucionActiva ===
                                    'colegio'
                                        ? '🏫 Colegio'
                                        : '🎓 Academia'
                                }
                            </span>

                        </div>

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="w-full min-w-[900px] border-collapse">

                        <thead>

                            <tr class="bg-slate-900 text-white">

                                <th class="w-24 border-r border-slate-700 px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">
                                    Hora
                                </th>

                                ${thead}

                            </tr>

                        </thead>


                        <tbody>
                            ${filasHTML}
                        </tbody>

                    </table>

                </div>

            `;


        return tarjeta;

    }


    // =========================================================
    // TÍTULOS
    // =========================================================

    function actualizarTitulos() {

        const textos = {

            profesor: [
                'Horario por profesor',
                'Consulta las clases asignadas a cada profesor.'
            ],

            aula: [
                'Horario por aula',
                'Consulta qué clase se dicta en cada aula, sin choques.'
            ],

            grado: [
                'Horario por grado',
                'Consulta las clases de un grado o sección.'
            ],

            'aula-completa': [
                'Horario completo del aula',
                'Semana completa con horas libres incluidas.'
            ]

        };


        const [
            titulo,
            subtitulo
        ] =
            textos[vistaActual] ||
            textos.profesor;


        if (tituloEl) {

            tituloEl.textContent =
                titulo;

        }


        if (subtituloEl) {

            subtituloEl.textContent =
                subtitulo;

        }

    }


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    function actualizarEstadisticas(
        bloques
    ) {

        if (statProfesores) {

            statProfesores.textContent =
                new Set(
                    bloques.map(
                        (item) =>
                            item.profesor_id
                    )
                ).size;

        }


        if (statAulas) {

            statAulas.textContent =
                new Set(
                    bloques.map(
                        (item) =>
                            item.aula_id
                    )
                ).size;

        }


        if (statClases) {

            statClases.textContent =
                bloques.length;

        }


        const conflictos =
            detectarConflictos(
                bloques
            );


        if (statEstado) {

            statEstado.textContent =
                conflictos.length

                    ? `${conflictos.length} conflicto(s)`

                    : 'Sin conflictos';

        }


        if (statEstadoPunto) {

            statEstadoPunto.style.background =
                conflictos.length
                    ? '#db0808'
                    : '#10b981';

        }

    }


    // =========================================================
    // RENDER PRINCIPAL
    // =========================================================

    let gruposVisibles =
        [];


    function renderizar() {

        actualizarTitulos();


        const todos =
            cargarHorarios();


        const bloquesInstitucion =
            todos.filter(
                (bloque) =>
                    bloque.institucion ===
                    institucionActiva
            );


        actualizarEstadisticas(
            bloquesInstitucion
        );


        cargarFiltros(
            bloquesInstitucion
        );


        if (
            bloquesInstitucion.length ===
            0
        ) {

            gruposVisibles =
                [];


            contenedor.innerHTML =
                `

                    <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center">

                        <span class="mb-3 text-3xl">
                            📭
                        </span>

                        <p class="text-sm font-medium text-slate-700">
                            No hay horarios para ${
                                institucionActiva ===
                                'colegio'
                                    ? 'Colegio'
                                    : 'Academia'
                            }
                        </p>

                        <p class="mt-1 max-w-sm text-xs text-slate-500">
                            Ve a Asignaciones y crea una clase indicando profesor,
                            curso, aula, día y horario.
                        </p>

                    </div>

                `;


            return;

        }


        let bloques =
            bloquesInstitucion;


        if (
            filtroProfesor &&
            filtroProfesor.value !==
                'todos'
        ) {

            bloques =
                bloques.filter(
                    (item) =>
                        String(
                            item.profesor_id
                        ) ===
                        String(
                            filtroProfesor.value
                        )
                );

        }


        if (
            filtroAula &&
            filtroAula.value !==
                'todos'
        ) {

            bloques =
                bloques.filter(
                    (item) =>
                        String(
                            item.aula_id
                        ) ===
                        String(
                            filtroAula.value
                        )
                );

        }


        if (
            filtroGrado &&
            filtroGrado.value !==
                'todos'
        ) {

            bloques =
                bloques.filter(
                    (item) =>
                        String(
                            item.grado_id
                        ) ===
                        String(
                            filtroGrado.value
                        )
                );

        }


        if (
            filtroCurso &&
            filtroCurso.value !==
                'todos'
        ) {

            bloques =
                bloques.filter(
                    (item) =>
                        String(
                            item.curso_id
                        ) ===
                        String(
                            filtroCurso.value
                        )
                );

        }


        if (
            bloques.length ===
            0
        ) {

            gruposVisibles =
                [];


            contenedor.innerHTML =
                `

                    <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white p-12 text-center">

                        <span class="mb-3 text-3xl">
                            🔍
                        </span>

                        <p class="text-sm font-medium text-slate-700">
                            No hay horarios que coincidan con este filtro.
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Ajusta los filtros para continuar.
                        </p>

                    </div>

                `;


            return;

        }


        gruposVisibles =
            agruparBloques(
                bloques
            );


        contenedor.innerHTML =
            '';


        gruposVisibles.forEach(
            (grupo) => {

                contenedor.appendChild(
                    construirTablero(
                        grupo
                    )
                );

            }
        );


        // Después de crear el HTML,
        // activamos drag & drop.

        activarDragDrop();

    }


    // =========================================================
    // EXCEL
    // =========================================================

    function nombreArchivo(
        extension
    ) {

        const fecha =
            new Date()
                .toISOString()
                .slice(
                    0,
                    10
                );


        return (
            `horarios_${institucionActiva}_${vistaActual}_${fecha}.${extension}`
        );

    }


    function tablaExport(
        grupo
    ) {

        const completo =
            isVistaCompleta();


        const dias =
            obtenerDias(
                grupo.bloques,
                completo
            );


        const slots =
            construirSlots(
                grupo.bloques,
                dias,
                completo
            );


        const matriz =
            construirMatriz(
                grupo.bloques,
                dias,
                slots
            );


        return {
            dias,
            slots,
            matriz
        };

    }


    function lineasBloqueExcel(
        bloque
    ) {

        const lineas =
            getLineasBloque(
                bloque
            );


        return (

            `${lineas.curso} | ` +

            `${lineas.sec} | ` +

            `${lineas.ter} | ` +

            `${bloque.hora_inicio}-${bloque.hora_fin}`

        );

    }


    function exportarExcel() {

        if (
            !gruposVisibles.length
        ) {

            alert(
                'No hay horarios para exportar.'
            );

            return;

        }


        if (!window.XLSX) {

            alert(
                'No se pudo cargar la librería para exportar Excel.'
            );

            return;

        }


        const workbook =
            XLSX.utils.book_new();


        const nombresUsados =
            new Set();


        gruposVisibles.forEach(
            (grupo) => {

                const data =
                    tablaExport(
                        grupo
                    );


                const filas = [

                    [
                        'Hora',
                        ...data.dias.map(
                            (dia) =>
                                DIAS_NOMBRES[dia] ??
                                dia
                        )
                    ]

                ];


                data.slots.forEach(
                    (slot, indice) => {

                        const fila = [

                            `${aTexto(slot.ini)} - ${aTexto(slot.fin)}`

                        ];


                        data.dias.forEach(
                            (dia) => {

                                const celda =
                                    data.matriz[dia][indice];


                                if (!celda) {

                                    fila.push(
                                        'Libre'
                                    );

                                    return;

                                }


                                const propietario =
                                    celda.tipo ===
                                        'cont'

                                        ? data.matriz[dia][
                                            celda.dueno
                                        ]

                                        : celda;


                                fila.push(

                                    propietario.bloques
                                        .map(
                                            lineasBloqueExcel
                                        )
                                        .join(
                                            ' /// '
                                        )

                                );

                            }
                        );


                        filas.push(
                            fila
                        );

                    }
                );


                let nombre =
                    (
                        grupo.nombre ||
                        'Horario'
                    )
                        .replace(
                            /[\\/?*[\]:]/g,
                            ' '
                        )
                        .slice(
                            0,
                            28
                        );


                let contador =
                    2;


                const nombreBase =
                    nombre;


                while (
                    nombresUsados.has(
                        nombre
                    )
                ) {

                    nombre =
                        `${nombreBase.slice(0, 24)} ${contador}`;

                    contador++;

                }


                nombresUsados.add(
                    nombre
                );


                const worksheet =
                    XLSX.utils.aoa_to_sheet(
                        filas
                    );


                worksheet['!cols'] =
                    filas[0].map(
                        (_, indice) => ({

                            wch:
                                indice === 0
                                    ? 16
                                    : 34

                        })
                    );


                XLSX.utils.book_append_sheet(

                    workbook,

                    worksheet,

                    nombre

                );

            }
        );


        XLSX.writeFile(
            workbook,
            nombreArchivo(
                'xlsx'
            )
        );

    }


    // =========================================================
    // PDF
    // =========================================================

    function exportarPdf() {

        if (
            !gruposVisibles.length
        ) {

            alert(
                'No hay horarios para exportar.'
            );

            return;

        }


        if (
            !window.html2pdf
        ) {

            window.print();

            return;

        }


        const wrapper =
            document.createElement(
                'div'
            );


        wrapper.style.padding =
            '20px';


        wrapper.innerHTML =
            `

                <h1
                    style="
                        font-family:Arial;
                        font-size:20px;
                        margin-bottom:16px;
                    "
                >
                    Horarios académicos —
                    Next Level School
                </h1>

                ${contenedor.innerHTML}

            `;


        wrapper
            .querySelectorAll(
                '.horario-clase'
            )
            .forEach(
                (clase) => {

                    clase.removeAttribute(
                        'draggable'
                    );

                }
            );


        html2pdf()
            .set({

                margin:
                    8,

                filename:
                    nombreArchivo(
                        'pdf'
                    ),

                html2canvas: {

                    scale:
                        2,

                    useCORS:
                        true

                },

                jsPDF: {

                    unit:
                        'mm',

                    format:
                        'a4',

                    orientation:
                        'landscape'

                }

            })
            .from(
                wrapper
            )
            .save();

    }


    // =========================================================
    // INSTITUCIÓN
    // =========================================================

    tabsInstitucion.forEach(
        (tab) => {

            tab.addEventListener(
                'click',
                () => {

                    institucionActiva =
                        tab.dataset.institucion;


                    tabsInstitucion.forEach(
                        (otra) => {

                            const activa =
                                otra === tab;


                            if (activa) {

                                otra.style.background =
                                    'linear-gradient(135deg,#1B3A6B,#0F2749)';


                                otra.classList.add(
                                    'text-white'
                                );


                                otra.classList.remove(
                                    'text-slate-600'
                                );

                            } else {

                                otra.style.background =
                                    '';


                                otra.classList.remove(
                                    'text-white'
                                );


                                otra.classList.add(
                                    'text-slate-600'
                                );

                            }

                        }
                    );


                    renderizar();

                }
            );

        }
    );


    // =========================================================
    // VISTAS
    // =========================================================

    vistaBotones.forEach(
        (boton) => {

            boton.addEventListener(
                'click',
                () => {

                    vistaActual =
                        boton.dataset.vista;


                    vistaBotones.forEach(
                        (otro) => {

                            const activo =
                                otro === boton;


                            if (activo) {

                                otro.style.borderColor =
                                    '#1B3A6B';


                                otro.style.background =
                                    'rgba(27,58,107,.045)';

                            } else {

                                otro.style.borderColor =
                                    '';


                                otro.style.background =
                                    '';

                            }

                        }
                    );


                    if (
                        chkCompleto
                    ) {

                        chkCompleto.disabled =
                            vistaActual ===
                            'aula-completa';

                    }


                    renderizar();

                }
            );

        }
    );


    // =========================================================
    // SEMANA COMPLETA
    // =========================================================

    if (chkCompleto) {

        chkCompleto.addEventListener(
            'change',
            renderizar
        );

    }


    // =========================================================
    // FILTROS
    // =========================================================

    [
        filtroProfesor,
        filtroAula,
        filtroGrado,
        filtroCurso
    ].forEach(
        (elemento) => {

            if (elemento) {

                elemento.addEventListener(
                    'change',
                    renderizar
                );

            }

        }
    );


    // =========================================================
    // RESET
    // =========================================================

    if (btnReset) {

        btnReset.addEventListener(
            'click',
            () => {

                [
                    filtroProfesor,
                    filtroAula,
                    filtroGrado,
                    filtroCurso
                ].forEach(
                    (elemento) => {

                        if (elemento) {

                            elemento.value =
                                'todos';

                        }

                    }
                );


                if (chkCompleto) {

                    chkCompleto.checked =
                        false;

                }


                renderizar();

            }
        );

    }


    // =========================================================
    // EXPORTAR
    // =========================================================

    if (btnPdf) {

        btnPdf.addEventListener(
            'click',
            exportarPdf
        );

    }


    if (btnExcel) {

        btnExcel.addEventListener(
            'click',
            exportarExcel
        );

    }


    // =========================================================
    // STORAGE
    // =========================================================

    window.addEventListener(
        'storage',
        (event) => {

            if (
                Object.values(
                    KEYS
                ).includes(
                    event.key
                )
            ) {

                renderizar();

            }

        }
    );


    window.addEventListener(
        'focus',
        renderizar
    );


    // =========================================================
    // INICIAR
    // =========================================================

    renderizar();

});