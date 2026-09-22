document.addEventListener('DOMContentLoaded', () => {

    // =========================================================
    // VERIFICAR QUE ESTAMOS EN DASHBOARD
    // =========================================================

    const contenedorHorarios =
        document.getElementById(
            'dashboard-horarios-hoy'
        );

    if (!contenedorHorarios) {
        return;
    }


    // =========================================================
    // LOCALSTORAGE
    // =========================================================

    const KEYS = {

        profesores:
            'nextlevel_profesores',

        cursos:
            'nextlevel_cursos',

        aulas:
            'nextlevel_aulas',

        horarios:
            'nextlevel_horarios'
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
    // LEER STORAGE
    // =========================================================

    function leer(clave) {

        try {

            const datos =
                JSON.parse(
                    localStorage.getItem(clave) ||
                    '[]'
                );

            return Array.isArray(datos)
                ? datos
                : [];

        } catch (error) {

            console.error(
                `Error leyendo ${clave}:`,
                error
            );

            return [];
        }
    }


    // =========================================================
    // ESCAPAR HTML
    // =========================================================

    function esc(valor) {

        return String(valor ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    // =========================================================
    // FECHA ACTUAL
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

            ${
                texto.charAt(0)
                    .toUpperCase() +
                texto.slice(1)
            }
        `;
    }


    // =========================================================
    // ESTADÍSTICAS
    // =========================================================

    function actualizarEstadisticas() {

        const profesores =
            leer(
                KEYS.profesores
            );

        const cursos =
            leer(
                KEYS.cursos
            );

        const aulas =
            leer(
                KEYS.aulas
            );

        const horarios =
            leer(
                KEYS.horarios
            );


        const valores = {

            profesores:
                profesores.length,

            cursos:
                cursos.length,

            aulas:
                aulas.length,

            horarios:
                horarios.length
        };


        if (headerProfesores) {

            headerProfesores.textContent =
                valores.profesores;
        }


        if (headerCursos) {

            headerCursos.textContent =
                valores.cursos;
        }


        if (headerAulas) {

            headerAulas.textContent =
                valores.aulas;
        }


        if (headerHorarios) {

            headerHorarios.textContent =
                valores.horarios;
        }


        if (totalProfesores) {

            totalProfesores.textContent =
                valores.profesores;
        }


        if (totalCursos) {

            totalCursos.textContent =
                valores.cursos;
        }


        if (totalAulas) {

            totalAulas.textContent =
                valores.aulas;
        }


        if (totalHorarios) {

            totalHorarios.textContent =
                valores.horarios;
        }
    }


    // =========================================================
    // DÍA ACTUAL
    // =========================================================
    //
    // JavaScript:
    //
    // Domingo = 0
    // Lunes   = 1
    // ...
    // Sábado  = 6
    //
    // Coincide con dia_semana de tu horario.
    //
    // =========================================================

    function obtenerDiaActual() {

        return new Date().getDay();
    }


    // =========================================================
    // CONVERTIR HORA A MINUTOS
    // =========================================================

    function minutosHora(hora) {

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
            (horas || 0) * 60 +
            (minutos || 0)
        );
    }


    // =========================================================
    // MINUTOS ACTUALES
    // =========================================================

    function minutosActuales() {

        const ahora =
            new Date();


        return (
            ahora.getHours() *
            60 +
            ahora.getMinutes()
        );
    }


    // =========================================================
    // NOMBRE DEL PROFESOR
    // =========================================================

    function nombreProfesor(profesor) {

        if (!profesor) {
            return 'Profesor no disponible';
        }


        const nombre =
            profesor.nombre ||
            profesor.nombres ||
            '';


        const apellidos = [

            profesor.apellido_paterno,

            profesor.apellidoPaterno,

            profesor.apellido,

            profesor.apellido_materno

        ]
            .filter(Boolean)
            .join(' ');


        return (
            `${nombre} ${apellidos}`
                .replace(/\s+/g, ' ')
                .trim() ||
            'Profesor'
        );
    }


    // =========================================================
    // NOMBRE CURSO
    // =========================================================

    function nombreCurso(curso) {

        return (
            curso?.nombre ||
            curso?.nombre_curso ||
            'Curso no disponible'
        );
    }


    // =========================================================
    // NOMBRE AULA
    // =========================================================

    function nombreAula(aula) {

        return (
            aula?.nombre ||
            aula?.nombre_aula ||
            aula?.codigo ||
            'Aula no disponible'
        );
    }


    // =========================================================
    // ESTADO DE LA CLASE
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
    // RENDER HORARIOS DE HOY
    // =========================================================

    function renderHorariosHoy() {

        const horarios =
            leer(
                KEYS.horarios
            );


        const profesores =
            leer(
                KEYS.profesores
            );


        const cursos =
            leer(
                KEYS.cursos
            );


        const aulas =
            leer(
                KEYS.aulas
            );


        const diaActual =
            obtenerDiaActual();


        const horariosHoy =
            horarios
                .filter(
                    horario =>

                        Number(
                            horario.dia_semana
                        ) ===
                        Number(
                            diaActual
                        ) &&

                        horario.estado !==
                        'inactivo'
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


        if (totalHoy) {

            totalHoy.textContent =
                `${horariosHoy.length} ${
                    horariosHoy.length === 1
                        ? 'clase programada'
                        : 'clases programadas'
                }`;
        }


        // =====================================================
        // DOMINGO
        // =====================================================

        if (
            diaActual === 0
        ) {

            contenedorHorarios.innerHTML = `

                <div
                    class="px-6 py-12 text-center"
                >

                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xl"
                    >
                        ☕
                    </div>


                    <p
                        class="mt-4 font-bold"
                        style="color:#0F2749;"
                    >
                        Hoy es domingo
                    </p>


                    <p
                        class="mt-1 text-sm text-slate-400"
                    >
                        No hay clases programadas.
                    </p>

                </div>
            `;

            return;
        }


        // =====================================================
        // SIN CLASES
        // =====================================================

        if (
            horariosHoy.length ===
            0
        ) {

            contenedorHorarios.innerHTML = `

                <div
                    class="px-6 py-12 text-center"
                >

                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-xl"
                    >
                        🗓️
                    </div>


                    <p
                        class="mt-4 font-bold"
                        style="color:#0F2749;"
                    >
                        No hay clases para hoy
                    </p>


                    <p
                        class="mt-1 text-sm text-slate-400"
                    >
                        Las clases creadas desde Asignaciones aparecerán aquí automáticamente.
                    </p>

                </div>
            `;

            return;
        }


        // =====================================================
        // MOSTRAR CLASES
        // =====================================================

        contenedorHorarios.innerHTML =
            horariosHoy
                .map(
                    horario => {

                        const profesor =
                            profesores.find(
                                item =>
                                    String(
                                        item.id
                                    ) ===
                                    String(
                                        horario.profesor_id
                                    )
                            );


                        const curso =
                            cursos.find(
                                item =>
                                    String(
                                        item.id
                                    ) ===
                                    String(
                                        horario.curso_id
                                    )
                            );


                        const aula =
                            aulas.find(
                                item =>
                                    String(
                                        item.id
                                    ) ===
                                    String(
                                        horario.aula_id
                                    )
                            );


                        const estado =
                            obtenerEstadoClase(
                                horario
                            );


                        return `

                            <div
                                class="
                                    flex items-center
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

                                <!-- HORA -->
                                <div
                                    class="w-14 shrink-0 text-center"
                                >

                                    <p
                                        class="text-sm font-extrabold"
                                        style="color:#0F2749;"
                                    >
                                        ${esc(
                                            horario.hora_inicio
                                        )}
                                    </p>


                                    <p
                                        class="mt-0.5 text-[9px] font-semibold text-slate-300"
                                    >
                                        ${esc(
                                            horario.hora_fin
                                        )}
                                    </p>

                                </div>


                                <!-- BARRA -->
                                <div
                                    class="h-12 w-1 shrink-0 rounded-full"
                                    style="
                                        background:
                                            ${estado.barra};
                                    "
                                ></div>


                                <!-- INFO -->
                                <div
                                    class="min-w-0 flex-1"
                                >

                                    <div
                                        class="flex flex-wrap items-center gap-2"
                                    >

                                        <p
                                            class="truncate font-bold"
                                            style="color:#0F2749;"
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
                                        class="mt-1 truncate text-sm text-slate-400"
                                    >
                                        ${esc(
                                            nombreProfesor(
                                                profesor
                                            )
                                        )}

                                        <span
                                            class="px-1 text-slate-300"
                                        >
                                            ·
                                        </span>

                                        ${esc(
                                            nombreAula(
                                                aula
                                            )
                                        )}
                                    </p>

                                </div>


                                <!-- INSTITUCIÓN -->
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
                                        String(
                                            horario.institucion
                                        )
                                        .toLowerCase() ===
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
    // ACTUALIZAR TODO
    // =========================================================

    function actualizarDashboard() {

        actualizarFecha();

        actualizarEstadisticas();

        renderHorariosHoy();
    }


    // =========================================================
    // CAMBIOS EN LOCALSTORAGE
    // =========================================================

    window.addEventListener(
        'storage',
        event => {

            if (
                Object.values(
                    KEYS
                ).includes(
                    event.key
                )
            ) {

                actualizarDashboard();
            }
        }
    );


    // =========================================================
    // ACTUALIZAR AL REGRESAR A LA PESTAÑA
    // =========================================================

    window.addEventListener(
        'focus',
        actualizarDashboard
    );


    // =========================================================
    // ACTUALIZAR ESTADO DE CLASES CADA MINUTO
    // =========================================================

    setInterval(
        renderHorariosHoy,
        60000
    );


    // =========================================================
    // INICIO
    // =========================================================

    actualizarDashboard();

});