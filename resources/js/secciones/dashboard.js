document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // VERIFICAR DASHBOARD
    // =========================================================

    const contenedorHorarios =
        document.getElementById(
            'dashboard-horarios-hoy'
        );

    if (!contenedorHorarios) {
        return;
    }


    // =========================================================
    // API
    // =========================================================

    const API = {
        profesores:
            '/api/profesores',

        cursos:
            '/api/cursos',

        aulas:
            '/api/aulas',

        horarios:
            '/api/horarios'
    };


    // =========================================================
    // ELEMENTOS
    // =========================================================

    const fechaEl =
        document.getElementById(
            'dashboard-fecha'
        );

    const headerProfesores =
        document.getElementById(
            'header-total-profesores'
        );

    const headerCursos =
        document.getElementById(
            'header-total-cursos'
        );

    const headerAulas =
        document.getElementById(
            'header-total-aulas'
        );

    const headerHorarios =
        document.getElementById(
            'header-total-horarios'
        );

    const totalProfesores =
        document.getElementById(
            'dashboard-total-profesores'
        );

    const totalCursos =
        document.getElementById(
            'dashboard-total-cursos'
        );

    const totalAulas =
        document.getElementById(
            'dashboard-total-aulas'
        );

    const totalHorarios =
        document.getElementById(
            'dashboard-total-horarios'
        );

    const totalHoy =
        document.getElementById(
            'dashboard-total-hoy'
        );


    // =========================================================
    // ESTADO
    // =========================================================

    let profesoresCache = [];
    let cursosCache = [];
    let aulasCache = [];
    let horariosCache = [];

    let cargandoDashboard =
        false;


    // =========================================================
    // UTILIDADES
    // =========================================================

    function esc(valor) {

        return String(valor ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    function normalizarTexto(valor) {

        return String(valor ?? '')
            .trim()
            .toLowerCase()
            .normalize('NFD')
            .replace(
                /[\u0300-\u036f]/g,
                ''
            );
    }


    function minutosHora(hora) {

        if (!hora) {
            return 0;
        }

        const partes =
            String(hora)
                .slice(0, 5)
                .split(':')
                .map(Number);

        return (
            (partes[0] || 0) *
            60
        ) +
        (
            partes[1] || 0
        );
    }


    function minutosActuales() {

        const ahora =
            new Date();

        return (
            ahora.getHours() *
            60
        ) +
        ahora.getMinutes();
    }


    function obtenerDiaActualTexto() {

        const dias = [
            'domingo',
            'lunes',
            'martes',
            'miercoles',
            'jueves',
            'viernes',
            'sabado'
        ];

        return dias[
            new Date().getDay()
        ];
    }


    function extraerLista(
        payload
    ) {

        if (
            Array.isArray(
                payload
            )
        ) {
            return payload;
        }

        if (
            Array.isArray(
                payload?.data
            )
        ) {
            return payload.data;
        }

        if (
            Array.isArray(
                payload?.data?.data
            )
        ) {
            return payload.data.data;
        }

        return [];
    }


    function extraerMeta(
        payload
    ) {

        if (
            payload?.meta &&
            typeof payload.meta ===
            'object'
        ) {
            return payload.meta;
        }

        if (
            payload?.data &&
            !Array.isArray(
                payload.data
            ) &&
            payload.data?.last_page
        ) {

            return {
                current_page:
                    payload.data.current_page,

                last_page:
                    payload.data.last_page,

                per_page:
                    payload.data.per_page,

                total:
                    payload.data.total
            };
        }

        return null;
    }


    async function cargarTodasLasPaginas(
        url,
        params = {}
    ) {

        const primera =
            await window.axios.get(
                url,
                {
                    params: {
                        ...params,
                        per_page: 100,
                        page: 1
                    }
                }
            );

        const lista = [
            ...extraerLista(
                primera.data
            )
        ];

        const meta =
            extraerMeta(
                primera.data
            );

        const ultimaPagina =
            Number(
                meta?.last_page ||
                1
            );

        for (
            let pagina = 2;
            pagina <= ultimaPagina;
            pagina++
        ) {

            const respuesta =
                await window.axios.get(
                    url,
                    {
                        params: {
                            ...params,
                            per_page: 100,
                            page: pagina
                        }
                    }
                );

            lista.push(
                ...extraerLista(
                    respuesta.data
                )
            );
        }

        return lista;
    }


    // =========================================================
    // FECHA
    // =========================================================

    function actualizarFecha() {

        if (!fechaEl) {
            return;
        }

        const ahora =
            new Date();

        const texto =
            ahora.toLocaleDateString(
                'es-PE',
                {
                    weekday:
                        'long',

                    day:
                        '2-digit',

                    month:
                        'long',

                    year:
                        'numeric'
                }
            );

        const textoFinal =
            texto.charAt(0)
                .toUpperCase() +
            texto.slice(1);

        fechaEl.innerHTML = `

            <svg
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
            >

                <rect
                    x="3"
                    y="4"
                    width="18"
                    height="18"
                    rx="2"
                ></rect>

                <path
                    d="M3 10h18M8 2v4M16 2v4"
                ></path>

            </svg>

            ${esc(
                textoFinal
            )}
        `;
    }


    // =========================================================
    // NOMBRES
    // =========================================================

    function nombreProfesor(
        profesor
    ) {

        if (!profesor) {
            return 'Profesor no disponible';
        }

        const nombre =
            profesor.nombre ||
            profesor.nombres ||
            '';

        const apellidos = [
            profesor.apellido_paterno,
            profesor.apellido_materno,
            profesor.apellido
        ]
            .filter(Boolean)
            .join(' ');

        return (
            `${nombre} ${apellidos}`
                .replace(
                    /\s+/g,
                    ' '
                )
                .trim() ||

            profesor.nombre_completo ||

            'Profesor'
        );
    }


    function nombreCurso(
        curso
    ) {

        return (
            curso?.nombre ||
            curso?.nombre_curso ||
            curso?.codigo ||
            'Curso no disponible'
        );
    }


    function nombreAula(
        aula
    ) {

        return (
            aula?.nombre ||
            aula?.nombre_aula ||
            aula?.codigo ||
            'Aula no disponible'
        );
    }


    function nombreGrado(
        grado
    ) {

        return (
            grado?.nombre_completo ||
            grado?.nombre ||
            `${grado?.grado || ''} ${
                grado?.seccion || ''
            }`.trim() ||
            ''
        );
    }


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    function actualizarEstadisticas() {

        const valores = {

            profesores:
                profesoresCache.length,

            cursos:
                cursosCache.length,

            aulas:
                aulasCache.length,

            horarios:
                horariosCache.length
        };


        if (
            headerProfesores
        ) {
            headerProfesores.textContent =
                valores.profesores;
        }


        if (
            headerCursos
        ) {
            headerCursos.textContent =
                valores.cursos;
        }


        if (
            headerAulas
        ) {
            headerAulas.textContent =
                valores.aulas;
        }


        if (
            headerHorarios
        ) {
            headerHorarios.textContent =
                valores.horarios;
        }


        if (
            totalProfesores
        ) {
            totalProfesores.textContent =
                valores.profesores;
        }


        if (
            totalCursos
        ) {
            totalCursos.textContent =
                valores.cursos;
        }


        if (
            totalAulas
        ) {
            totalAulas.textContent =
                valores.aulas;
        }


        if (
            totalHorarios
        ) {
            totalHorarios.textContent =
                valores.horarios;
        }
    }


    // =========================================================
    // ESTADO DE CLASE
    // =========================================================

    function obtenerEstadoClase(
        horario
    ) {

        const ahora =
            minutosActuales();

        const inicio =
            minutosHora(
                horario.hora_inicio
            );

        const fin =
            minutosHora(
                horario.hora_fin
            );


        if (
            ahora >= inicio &&
            ahora < fin
        ) {

            return {
                texto:
                    'EN CURSO',

                clase:
                    'bg-emerald-100 text-emerald-700',

                barra:
                    'linear-gradient(180deg,#10B981,#059669)'
            };
        }


        if (
            ahora < inicio
        ) {

            return {
                texto:
                    'PRÓXIMA',

                clase:
                    'bg-blue-50 text-blue-700',

                barra:
                    'linear-gradient(180deg,#1B3A6B,#0F2749)'
            };
        }


        return {
            texto:
                'FINALIZADA',

            clase:
                'bg-slate-100 text-slate-500',

            barra:
                'linear-gradient(180deg,#94A3B8,#64748B)'
        };
    }


    // =========================================================
    // HORARIOS DE HOY
    // =========================================================

    function renderHorariosHoy() {

        const diaActual =
            obtenerDiaActualTexto();


        if (
            diaActual ===
            'domingo'
        ) {

            if (
                totalHoy
            ) {
                totalHoy.textContent =
                    '0 clases programadas';
            }


            contenedorHorarios.innerHTML = `

                <div
                    class="
                        px-6
                        py-12
                        text-center
                    "
                >

                    <div
                        class="
                            mx-auto
                            flex
                            h-12
                            w-12
                            items-center
                            justify-center
                            rounded-2xl
                            bg-slate-100
                        "
                    >

                        <svg
                            class="h-6 w-6 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                d="M6 9h12v5a6 6 0 0 1-12 0z"
                            />

                            <path
                                d="M18 10h1a2 2 0 0 1 0 4h-1"
                            />

                            <path
                                d="M8 3v3M12 3v3M16 3v3"
                            />
                        </svg>

                    </div>


                    <p
                        class="
                            mt-4
                            font-bold
                        "
                        style="
                            color:#0F2749;
                        "
                    >
                        Hoy es domingo
                    </p>


                    <p
                        class="
                            mt-1
                            text-sm
                            text-slate-400
                        "
                    >
                        No hay clases programadas.
                    </p>

                </div>
            `;

            return;
        }


        const horariosHoy =
            horariosCache
                .filter(
                    horario => {

                        return (
                            normalizarTexto(
                                horario.dia_semana
                            ) ===
                            diaActual &&

                            normalizarTexto(
                                horario.estado
                            ) ===
                            'activo'
                        );
                    }
                )
                .sort(
                    (a, b) =>

                        minutosHora(
                            a.hora_inicio
                        ) -

                        minutosHora(
                            b.hora_inicio
                        )
                );


        if (
            totalHoy
        ) {

            totalHoy.textContent =
                `${horariosHoy.length} ${
                    horariosHoy.length ===
                    1

                        ? 'clase programada'

                        : 'clases programadas'
                }`;
        }


        if (
            !horariosHoy.length
        ) {

            contenedorHorarios.innerHTML = `

                <div
                    class="
                        px-6
                        py-12
                        text-center
                    "
                >

                    <div
                        class="
                            mx-auto
                            flex
                            h-12
                            w-12
                            items-center
                            justify-center
                            rounded-2xl
                            bg-slate-100
                        "
                    >

                        <svg
                            class="h-6 w-6 text-slate-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="16"
                                rx="2"
                            />

                            <path
                                d="M8 3v4M16 3v4M3 10h18"
                            />
                        </svg>

                    </div>


                    <p
                        class="
                            mt-4
                            font-bold
                        "
                        style="
                            color:#0F2749;
                        "
                    >
                        No hay clases para hoy
                    </p>


                    <p
                        class="
                            mt-1
                            text-sm
                            text-slate-400
                        "
                    >
                        No existen horarios activos para el día actual.
                    </p>

                </div>
            `;

            return;
        }


        contenedorHorarios.innerHTML =
            horariosHoy
                .map(
                    horario => {

                        const estado =
                            obtenerEstadoClase(
                                horario
                            );


                        const profesor =
                            horario.profesor;


                        const curso =
                            horario.curso;


                        const aula =
                            horario.aula;


                        const grado =
                            horario.grado;


                        return `

                            <div
                                class="
                                    flex
                                    items-center
                                    gap-4
                                    border-b
                                    border-slate-100
                                    px-5
                                    py-4
                                    transition
                                    last:border-b-0
                                    hover:bg-slate-50/70
                                    md:px-6
                                "
                            >

                                <div
                                    class="
                                        w-14
                                        shrink-0
                                        text-center
                                    "
                                >

                                    <p
                                        class="
                                            text-sm
                                            font-extrabold
                                        "
                                        style="
                                            color:#0F2749;
                                        "
                                    >
                                        ${esc(
                                            String(
                                                horario.hora_inicio ||
                                                ''
                                            ).slice(
                                                0,
                                                5
                                            )
                                        )}
                                    </p>


                                    <p
                                        class="
                                            mt-0.5
                                            text-[9px]
                                            font-semibold
                                            text-slate-300
                                        "
                                    >
                                        ${esc(
                                            String(
                                                horario.hora_fin ||
                                                ''
                                            ).slice(
                                                0,
                                                5
                                            )
                                        )}
                                    </p>

                                </div>


                                <div
                                    class="
                                        h-12
                                        w-1
                                        shrink-0
                                        rounded-full
                                    "
                                    style="
                                        background:
                                            ${estado.barra};
                                    "
                                ></div>


                                <div
                                    class="
                                        min-w-0
                                        flex-1
                                    "
                                >

                                    <div
                                        class="
                                            flex
                                            flex-wrap
                                            items-center
                                            gap-2
                                        "
                                    >

                                        <p
                                            class="
                                                truncate
                                                font-bold
                                            "
                                            style="
                                                color:#0F2749;
                                            "
                                        >
                                            ${esc(
                                                nombreCurso(
                                                    curso
                                                )
                                            )}
                                        </p>


                                        <span
                                            class="
                                                rounded-full
                                                px-2
                                                py-0.5
                                                text-[9px]
                                                font-extrabold
                                                tracking-wide
                                                ${estado.clase}
                                            "
                                        >
                                            ${estado.texto}
                                        </span>

                                    </div>


                                    <p
                                        class="
                                            mt-1
                                            truncate
                                            text-sm
                                            text-slate-400
                                        "
                                    >

                                        ${esc(
                                            nombreProfesor(
                                                profesor
                                            )
                                        )}

                                        <span
                                            class="
                                                px-1
                                                text-slate-300
                                            "
                                        >
                                            ·
                                        </span>

                                        ${esc(
                                            nombreAula(
                                                aula
                                            )
                                        )}

                                        ${
                                            grado
                                                ? `
                                                    <span
                                                        class="
                                                            px-1
                                                            text-slate-300
                                                        "
                                                    >
                                                        ·
                                                    </span>

                                                    ${esc(
                                                        nombreGrado(
                                                            grado
                                                        )
                                                    )}
                                                `
                                                : ''
                                        }

                                    </p>

                                </div>


                                <span
                                    class="
                                        hidden
                                        rounded-full
                                        bg-slate-100
                                        px-2.5
                                        py-1
                                        text-[10px]
                                        font-semibold
                                        text-slate-500
                                        sm:inline-flex
                                    "
                                >

                                    ${
                                        normalizarTexto(
                                            horario.institucion
                                        ) ===
                                        'academia'

                                            ? 'Academia'

                                            : 'Colegio'
                                    }

                                </span>

                            </div>
                        `;
                    }
                )
                .join('');
    }


    // =========================================================
    // CARGAR DATOS REALES
    // =========================================================

    async function cargarDashboard(
        silencioso = false
    ) {

        if (
            cargandoDashboard
        ) {
            return;
        }


        cargandoDashboard =
            true;


        try {

            const [
                profesores,
                cursos,
                aulas,
                horarios
            ] =
                await Promise.all([

                    cargarTodasLasPaginas(
                        API.profesores
                    ),

                    cargarTodasLasPaginas(
                        API.cursos
                    ),

                    cargarTodasLasPaginas(
                        API.aulas
                    ),

                    cargarTodasLasPaginas(
                        API.horarios
                    )

                ]);


            profesoresCache =
                profesores;


            cursosCache =
                cursos;


            aulasCache =
                aulas;


            horariosCache =
                horarios;


            actualizarEstadisticas();

            renderHorariosHoy();


        } catch (error) {

            console.error(
                'Error cargando dashboard:',
                error
            );


            if (
                !silencioso
            ) {

                contenedorHorarios.innerHTML = `

                    <div
                        class="
                            px-6
                            py-12
                            text-center
                        "
                    >

                        <div
                            class="
                                mx-auto
                                flex
                                h-12
                                w-12
                                items-center
                                justify-center
                                rounded-2xl
                                bg-red-50
                                text-red-500
                            "
                        >

                            <svg
                                class="h-6 w-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <circle
                                    cx="12"
                                    cy="12"
                                    r="9"
                                />

                                <path
                                    d="M12 7v6"
                                />

                                <path
                                    d="M12 17h.01"
                                />
                            </svg>

                        </div>


                        <p
                            class="
                                mt-4
                                font-bold
                                text-red-600
                            "
                        >
                            No se pudo cargar el dashboard
                        </p>


                        <p
                            class="
                                mt-1
                                text-sm
                                text-slate-400
                            "
                        >
                            Revisa la conexión con la API.
                        </p>

                    </div>
                `;
            }

        } finally {

            cargandoDashboard =
                false;
        }
    }


    // =========================================================
    // ACTUALIZAR AL VOLVER
    // =========================================================

    window.addEventListener(
        'focus',
        () => {

            cargarDashboard(
                true
            );
        }
    );


    // =========================================================
    // ACTUALIZAR ESTADO DE CLASES
    // =========================================================

    setInterval(
        () => {

            renderHorariosHoy();

        },
        60000
    );


    // =========================================================
    // INICIO
    // =========================================================

    actualizarFecha();

    cargarDashboard();

});