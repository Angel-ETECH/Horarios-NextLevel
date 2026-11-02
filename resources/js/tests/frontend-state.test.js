import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';
import { fechaActualHorario } from '../calendario-actual.js';

const asignacionesSource = readFileSync(
    new URL('../secciones/asignaciones.js', import.meta.url), 'utf8'
).replaceAll('\r\n', '\n');
const horariosSource = readFileSync(
    new URL('../secciones/horarios.js', import.meta.url), 'utf8'
).replaceAll('\r\n', '\n');
const consultaSource = readFileSync(
    new URL('../consulta-horarios.js', import.meta.url), 'utf8'
).replaceAll('\r\n', '\n');
const disponibilidadSource = readFileSync(
    new URL('../secciones/disponibilidad.js', import.meta.url), 'utf8'
).replaceAll('\r\n', '\n');
const recuperacionesSource = readFileSync(
    new URL('../secciones/recuperaciones.js', import.meta.url), 'utf8'
).replaceAll('\r\n', '\n');

test('drag destinations fail closed until the server answers for the current class', () => {
    const c = loadFunctions(horariosSource, ['validarMovimiento'], {
        reorganizando: true, opcionesMovimiento: null, diaANumero: () => 2,
    });
    assert.equal(c.validarMovimiento({ id: 1 }, 2, '07:00', '07:45').valido, false);
    c.opcionesMovimiento = { id: 2, items: [{ dia_semana: 'martes', hora_inicio: '07:00', hora_fin: '07:45', disponible: true }] };
    assert.equal(c.validarMovimiento({ id: 1 }, 2, '07:00', '07:45').valido, false);
    c.opcionesMovimiento.id = 1;
    assert.equal(c.validarMovimiento({ id: 1 }, 2, '07:00', '07:45').valido, true);
    assert.equal(c.validarMovimiento({ id: 1 }, 2, '07:00', '07:30').valido, false);
    c.opcionesMovimiento.items[0].disponible = false;
    c.opcionesMovimiento.items[0].motivo = 'Aula ocupada';
    assert.equal(c.validarMovimiento({ id: 1 }, 2, '07:00', '07:45').mensaje, 'Aula ocupada');
});

function dragFixture() {
    const request = deferred();
    const handlers = {}, zoneHandlers = {}, moves = [], messages = [];
    const card = { draggable: true, dataset: { horarioId: '1' },
        classList: { add() {}, remove() {} }, addEventListener: (name, fn) => handlers[name] = fn };
    const zone = { dataset: { grupo: 'grado-1', dia: '3' },
        addEventListener: (name, fn) => zoneHandlers[name] = fn };
    const transfer = { value: '', setData(_, value) { this.value = value; }, getData() { return this.value; } };
    const event = { preventDefault() {}, target: { closest: () => null }, dataTransfer: transfer, clientY: 450 };
    const context = loadFunctions(horariosSource, ['activarDragDrop', 'validarMovimiento'], {
        contenedor: { querySelectorAll: selector => selector === '.horario-clase' ? [card] : [zone] },
        puedeArrastrar: () => true, reorganizando: true, bloqueArrastradoId: null,
        opcionesMovimiento: null, solicitudMovimiento: 0, consultaMovimientoPendiente: null,
        window: { axios: { get: () => request.promise } }, API: { horarios: '/horarios' },
        buscarHorario: () => ({ id: 1 }), claveGrupoBloque: () => 'grado-1',
        minutoDesdeEvento: () => 420, aTexto: m => m === 420 ? '07:00' : '07:45',
        aMinutos: () => 420, duracionMinutos: () => 45, diaANumero: () => 3,
        mostrarPreviewDrop() {}, limpiarTodosPreviewDrop() {},
        mostrarMensaje: text => messages.push(text), moverClase: (...args) => moves.push(args),
    });
    context.activarDragDrop();
    return { context, request, handlers, zoneHandlers, event, moves, messages };
}

for (const available of [true, false]) {
    test(`quick drop waits for server and ${available ? 'accepts free' : 'rejects occupied'} destination`, async () => {
        const f = dragFixture();
        f.handlers.dragstart(f.event);
        f.zoneHandlers.dragover(f.event);
        assert.equal(f.event.dataTransfer.dropEffect, 'move');
        const pending = f.zoneHandlers.drop(f.event);
        f.handlers.dragend();
        assert.equal(f.moves.length, 0);
        f.request.resolve({ data: { data: [{ dia_semana: 'miércoles', hora_inicio: '07:00', hora_fin: '07:45', disponible: available, motivo: 'Aula ocupada' }] } });
        await pending;
        assert.equal(f.moves.length, available ? 1 : 0);
        if (!available) assert.equal(f.messages.at(-1), 'Aula ocupada');
    });
}

test('late drop cannot move a class after editing context changes', async () => {
    const f = dragFixture();
    f.handlers.dragstart(f.event);
    const pending = f.zoneHandlers.drop(f.event);
    f.context.solicitudMovimiento++;
    f.request.resolve({ data: { data: [] } });
    await pending;
    assert.equal(f.moves.length, 0);
});

test('failed destination request never moves a dropped class', async () => {
    const f = dragFixture();
    f.handlers.dragstart(f.event);
    const pending = f.zoneHandlers.drop(f.event);
    f.request.reject(new Error('Network unavailable'));
    await pending;
    assert.equal(f.moves.length, 0);
});

test('cancelling a valid move never sends a mutation request', async () => {
    let requests = 0;
    const context = loadFunctions(horariosSource, ['moverClase'], {
        puedeArrastrar: () => true,
        buscarHorario: () => ({ id: 1, dia_semana: 'lunes', hora_inicio: '07:00', hora_fin: '07:45' }),
        duracionMinutos: () => 45, aMinutos: () => 420, aTexto: () => '07:45',
        validarMovimiento: () => ({ valido: true }), diaANumero: () => 1,
        diaATexto: dia => dia === 1 ? 'Lunes' : 'Miércoles',
        datosClase: () => ({ curso: 'Comunicación', aula: '101' }),
        window: { confirm: () => false, axios: { post: () => { requests++; } } },
    });
    assert.equal(await context.moverClase(1, 3, '07:00'), false);
    assert.equal(requests, 0);
});

function gridAvailabilityFixture(recesos) {
    return loadFunctions(disponibilidadSource, [
        'minutosHora', 'aTextoHora', 'rangosSeCruzan', 'rangosDisponiblesGrid',
        'bloquesGridAFranjas', 'disponibilidadAEstadoVisual',
    ], {
        recesosInstitucion: recesos, DURACION_BLOQUE_GRID_MIN: 60,
        nombreDiaANumero: () => 1, horaCorta: value => value.substring(0, 5),
        disponibilidades: {}, personalizados: {}, disponibilidadesSimuladas: new Set(),
        generarIdTemporal: () => 'custom',
    });
}

test('availability form sends canonical weekday names for every grid day', () => {
    const mapping = disponibilidadSource.match(/const diasBackend\s*=\s*(\{[\s\S]*?\});/);
    assert.ok(mapping, 'The form must define its backend weekday mapping');
    const days = vm.runInNewContext(`(${mapping[1]})`);
    const context = gridAvailabilityFixture([]);
    const ranges = context.bloquesGridAFranjas(['1-14:00', '2-14:00', '3-14:00', '4-14:00', '5-14:00', '6-14:00']);
    assert.deepEqual(Array.from(ranges, range => days[Number(range.dia_semana)]),
        ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado']);
});

test('hourly availability retains the usable minutes after each recess', () => {
    const context = gridAvailabilityFixture([
        { hora_inicio: '09:00', hora_fin: '09:30' },
        { hora_inicio: '11:00', hora_fin: '11:30' },
    ]);
    const ranges = context.bloquesGridAFranjas(['1-07:00', '1-08:00', '1-09:00', '1-10:00', '1-11:00']);
    assert.deepEqual(JSON.parse(JSON.stringify(ranges)), [
        { dia_semana: 1, hora_inicio: '07:00', hora_fin: '09:00' },
        { dia_semana: 1, hora_inicio: '09:30', hora_fin: '11:00' },
        { dia_semana: 1, hora_inicio: '11:30', hora_fin: '12:00' },
    ]);
    context.disponibilidadAEstadoVisual(ranges, 'test');
    assert.deepEqual(Array.from(context.disponibilidades.test), ['1-07:00', '1-08:00', '1-09:00', '1-10:00', '1-11:00']);
    assert.equal(context.personalizados.test.length, 0);
});

test('recesses inside an hour preserve both available edges without expanding custom ranges', () => {
    const context = gridAvailabilityFixture([{ hora_inicio: '09:10', hora_fin: '09:25' }]);
    const ranges = context.bloquesGridAFranjas(['1-09:00']);
    assert.deepEqual(JSON.parse(JSON.stringify(ranges)), [
        { dia_semana: 1, hora_inicio: '09:00', hora_fin: '09:10' },
        { dia_semana: 1, hora_inicio: '09:25', hora_fin: '10:00' },
    ]);
    context.disponibilidadAEstadoVisual([ranges[1]], 'test');
    assert.equal(context.disponibilidades.test.length, 0);
    assert.equal(context.personalizados.test[0].horaInicio, '09:25');
});

test('overlapping recesses are subtracted once and fully blocked slots return no availability', () => {
    const context = gridAvailabilityFixture([
        { hora_inicio: '09:00', hora_fin: '09:40' },
        { hora_inicio: '09:30', hora_fin: '10:00' },
    ]);
    assert.equal(context.bloquesGridAFranjas(['1-09:00']).length, 0);
});

test('simulated availability stays identified only for its professor and institution', () => {
    const context = gridAvailabilityFixture([]);
    const range = { dia_semana: 1, hora_inicio: '07:00', hora_fin: '08:00' };
    context.disponibilidadAEstadoVisual([
        { ...range, observacion: 'PRUEBA SIMULADA: demostracion.' },
    ], '1-academia');
    context.disponibilidadAEstadoVisual([range], '1-colegio');
    assert.equal(context.disponibilidadesSimuladas.has('1-academia'), true);
    assert.equal(context.disponibilidadesSimuladas.has('1-colegio'), false);
    context.disponibilidadAEstadoVisual([], '1-academia');
    assert.equal(context.disponibilidadesSimuladas.has('1-academia'), false);
});

test('availability view labels demonstration data without changing saved times', () => {
    const detailElement = {};
    const context = loadFunctions(disponibilidadSource, ['actualizarResumen'], {
        obtenerSeleccionados: () => [], personalizadosActuales: () => [],
        bloquesGridAFranjas: () => [{ dia_semana: 1, hora_inicio: '07:00', hora_fin: '08:30' }],
        profesorSelect: { value: '1' }, claveActual: () => '1-academia',
        disponibilidadesSimuladas: new Set(['1-academia']), diasCortos: { 1: 'Lun' },
        minutosEntre: () => 90, countElement: null, daysElement: null, hoursElement: null,
        detailElement,
    });
    context.actualizarResumen();
    assert.match(detailElement.textContent, /^Disponibilidad simulada \(datos de prueba\): Lun 07:00-08:30$/);
    context.disponibilidadesSimuladas.clear();
    context.actualizarResumen();
    assert.match(detailElement.textContent, /^Disponibilidad guardada\/seleccionada:/);
});

function publicScheduleFixture() {
    const request = deferred();
    const rendered = [];
    const messages = [];
    const context = loadFunctions(consultaSource, ['consultarHorario'], {
        console: { error() {} },
        mensaje: { classList: { add() {} } },
        institucionSeleccionada: 'colegio', tipoSeleccionado: 'profesor',
        selector: { value: '1' }, opcionesSelectorActuales: [{ id: 1, registro: { id: 1 } }],
        btnConsultar: {}, activarCargaBoton() {}, desactivarCargaBoton() {},
        endpointHorario: () => '/test', apiGet: () => request.promise,
        aListaHorarios: items => items, filtrarInstitucionHorario: () => true,
        registroSeleccionadoActual: null, horariosSeleccionadosActuales: [],
        mostrarInformacion: data => rendered.push(data), renderizarHorario: data => rendered.push(data),
        renderizarHorarioVacio() {}, mostrarMensaje: text => messages.push(text),
        obtenerMensajeError: () => 'Error',
    });
    return { context, request, rendered, messages };
}

test('public consultation ignores a response for a previously selected teacher', async () => {
    const { context, request, rendered } = publicScheduleFixture();
    const pending = context.consultarHorario();
    context.selector.value = '2';
    request.resolve({ data: { info: { id: 1 }, horarios: [{ id: 1 }] } });
    await pending;
    assert.equal(rendered.length, 0);
    assert.equal(context.registroSeleccionadoActual, null);
});

test('public consultation ignores an error from a previously selected institution', async () => {
    const { context, request, messages } = publicScheduleFixture();
    const pending = context.consultarHorario();
    context.institucionSeleccionada = 'academia';
    request.reject(new Error('Delayed response'));
    await pending;
    assert.equal(messages.length, 0);
});

// Exercise the shipped functions with controlled network timing and DOM state.
function loadFunctions(source, names, globals) {
    const context = vm.createContext({ semanaInput: null, fechaActualHorario: () => '2026-10-21', document: { getElementById: () => null }, ...globals });
    const functions = names.map(name => {
        const start = source.search(new RegExp(`    (?:async )?function ${name}\\(`));
        assert.ok(start >= 0, `Missing function: ${name}`);
        const end = source.indexOf('\n    }\n', start);
        assert.ok(end >= 0, `Missing closing brace: ${name}`);
        return source.slice(start, end + 6);
    });
    vm.runInContext(functions.join('\n'), context);
    return context;
}

function deferred() {
    let resolve;
    let reject;
    const promise = new Promise((yes, no) => {
        resolve = yes;
        reject = no;
    });
    return { promise, resolve, reject };
}

test('automatic schedule date follows institutional timezone at the week boundary', () => {
    assert.equal(fechaActualHorario(new Date('2026-10-26T04:59:00Z')), '2026-10-25');
    assert.equal(fechaActualHorario(new Date('2026-10-26T05:00:00Z')), '2026-10-26');
});

test('normal consultation has no past-date picker and dates stay in the rescheduling form', () => {
    const publico = readFileSync(new URL('../../views/horarios/consulta.blade.php', import.meta.url), 'utf8');
    const recuperaciones = readFileSync(new URL('../../views/horarios/recuperaciones.blade.php', import.meta.url), 'utf8');
    assert.doesNotMatch(publico, /consulta-semana|consulta-habitual/);
    assert.match(recuperaciones, /id="horario-semana" type="hidden"/);
    assert.match(recuperaciones, /id="recuperacion-fecha" type="date"/);
    assert.doesNotMatch(recuperacionesSource, /semana\.value = payload\.fecha_recuperacion/);
    assert.match(recuperacionesSource, /params: \{ vigentes: 1 \}/);
});

test('public consultation always requests the current week without a date field', async () => {
    const { context, request } = publicScheduleFixture();
    let params;
    context.apiGet = (_, value) => { params = value; return request.promise; };
    const pending = context.consultarHorario();
    request.resolve({ data: { info: {}, horarios: [] } });
    await pending;
    assert.equal(params.semana_inicio, '2026-10-21');
});

test('public consultation updates automatically when the institutional date changes', () => {
    let today = '2026-10-25';
    let reloads = 0;
    const context = loadFunctions(consultaSource, ['sincronizarConsultaActual'], {
        fechaActualHorario: () => today, fechaConsultaActual: today, selector: { value: '1' },
        consultarHorario: () => reloads++,
    });
    context.sincronizarConsultaActual();
    assert.equal(reloads, 0);
    today = '2026-10-26';
    context.sincronizarConsultaActual();
    context.sincronizarConsultaActual();
    assert.equal(reloads, 1);
});

test('admin consultation follows the current week but preserves explicit base editing', () => {
    const changes = [];
    const context = loadFunctions(recuperacionesSource, ['sincronizarConsultaActual'], {
        semana: { value: '2026-10-25', dispatchEvent: e => changes.push(e.type) },
        fechaActualHorario: () => '2026-10-26', Event: class { constructor(type) { this.type = type; } },
    });
    context.sincronizarConsultaActual();
    assert.equal(context.semana.value, '2026-10-26');
    assert.deepEqual(changes, ['change']);
    context.semana.value = '';
    context.sincronizarConsultaActual();
    assert.equal(context.semana.value, '');
    assert.equal(changes.length, 1);
});

test('timetable has no editable recess controls or separate client-side recess rules', () => {
    const view = readFileSync(new URL('../../views/horarios/index.blade.php', import.meta.url), 'utf8');
    assert.match(view, /recesos_fijos/);
    assert.doesNotMatch(view, /id="config-receso-|id="btn-guardar-config-horario"/);
    assert.doesNotMatch(horariosSource, /recesosBaseConfig|guardarConfigHorario|configRecesoInicio/);
});

test('recess bands only appear in grade views, including Academia', () => {
    const recesses = [
        { nivel: 'primaria', hora_inicio: '08:30', hora_fin: '08:50' },
        { nivel: 'secundaria', hora_inicio: '09:15', hora_fin: '09:35' },
    ];
    const context = loadFunctions(horariosSource, ['recesosVisiblesGrupo'], {
        recesosParaGrupo: grupo => recesses.filter(receso => receso.nivel === grupo.nivel),
    });
    for (const tipo of ['profesor', 'aula', 'aula-completa']) {
        assert.equal(context.recesosVisiblesGrupo({ tipo, nivel: 'primaria' }).length, 0);
    }
    for (const nivel of ['primaria', 'secundaria', 'academia']) {
        if (nivel === 'academia') recesses.push({ nivel, hora_inicio: '08:50', hora_fin: '09:10' });
        const visible = context.recesosVisiblesGrupo({ tipo: 'grado', nivel });
        assert.equal(visible.length, 1);
        assert.equal(visible[0].nivel, nivel);
    }
});

test('hiding recess bands does not disable collision validation in any view', () => {
    const recess = { hora_inicio: '08:30', hora_fin: '08:50' };
    const context = loadFunctions(horariosSource, ['horarioChocaConReceso'], {
        recesosParaBloque: () => [recess],
        existeTraslape: (inicio, fin, desde, hasta) => inicio < hasta && fin > desde,
    });
    assert.equal(context.horarioChocaConReceso({}, '08:15', '08:45'), recess);
    assert.equal(context.horarioChocaConReceso({}, '08:00', '08:30'), undefined);
    assert.equal(context.horarioChocaConReceso({}, '08:50', '09:30'), undefined);
});

test('fixed afternoon with no recesses permits assignments without requesting invented breaks', async () => {
    const { context, recesses } = assignmentFixture(async () => ({ data: { data: [], recesos_fijos: true } }));
    context.turnoConfigurable = () => 'tarde';
    await context.actualizarResumenRecesos();
    assert.equal(context.configuracionRecesosLista, true);
    assert.equal(recesses.at(-1).state, 'ok');
    assert.match(recesses.at(-1).message, /Sin recesos/);
});
test('an unselected professor is not reported as having no availability', async () => {
    const { context, summaries } = assignmentFixture();
    context.profesorSelect.value = '';
    await context.actualizarResumenDisponibilidad();
    assert.equal(summaries[0].state, 'sin-seleccion');
    assert.equal(context.horasDisponiblesActuales, null);
});

function availability(hours, institucion = 'colegio') {
    return {
        data: {
            data: [{
                institucion, tipo: 'disponible', dia_semana: 'lunes',
                hora_inicio: '07:00', hora_fin: `${String(7 + hours).padStart(2, '0')}:00`,
            }],
        },
    };
}

function generation(items = [], id = 1) {
    return {
        data: {
            data: { id }, message: 'Operacion completada.',
            generacion_horarios: { success: true, asignaciones_incompletas: items },
        },
    };
}

function assignmentFixture(get = async () => availability(4)) {
    const alerts = [];
    const statuses = [];
    const summaries = [];
    const recesses = [];
    const element = value => ({ value, max: '40' });
    const context = loadFunctions(asignacionesSource, [
        'normalizarTexto', 'extraerPaginador', 'extraerColeccion', 'horaCorta', 'minutosHora',
        'nombreDiaDisponible', 'resumirDisponibilidad', 'obtenerDisponibilidadProfesor',
        'franjasSinRecesos', 'horaDesdeMinutos',
        'actualizarResumenDisponibilidad', 'actualizarResumenRecesos',
        'obtenerAvisoGeneracion', 'validarFormulario', 'cambiarEstadoAsignacion',
        'obtenerMensajeError', 'limpiarFormulario',
    ], {
        console: { error() {} }, window: { axios: { get } },
        profesorSelect: element('1'), institucionSelect: element('colegio'),
        cursoSelect: element('1'), gradoSelect: element('1'), aulaSelect: element('1'),
        horasInput: element('2'), rolSelect: element('titular'), estadoSelect: element('1'),
        observacionesInput: element(''), guardarTexto: {}, disponibilidadResumen: {},
        consultaDisponibilidad: 0, consultaRecesos: 0,
        horasDisponiblesActuales: null, configuracionRecesosLista: true,
        asignacionEditandoId: null,
        programacionManual: { validar: () => true, limpiar() {}, cargar() {} },
        asignaciones: [{ id: 1, aula_id: 1, profesor: { estado: 'activo' } }],
        alert: message => alerts.push(message), confirm: () => true,
        pintarResumenDisponibilidad: (summary, state) => summaries.push({ summary, state }),
        pintarResumenRecesos: (message, state) => recesses.push({ message, state }),
        mostrarEstado: (...args) => statuses.push(args), cargarAsignaciones: async () => {},
        obtenerGradoSeleccionado: () => ({ nivel: 'primaria', turno: 'manana' }),
        turnoConfigurable: () => 'mañana', editarAsignacion() {},
        obtenerRecesosDisponibilidad: async () => [],
        cargarSelectProfesores() {}, actualizarContadorObservaciones() {},
        ocultarEstado() {}, actualizarTodosCustomSelect() {},
    });
    return { context, alerts, statuses, summaries, recesses };
}

test('availability is fetched again after it changes on the server', async () => {
    let calls = 0;
    const { context, summaries } = assignmentFixture(async () => availability(++calls === 1 ? 4 : 1));
    await context.actualizarResumenDisponibilidad();
    assert.equal(summaries.at(-1).summary.horas, 4);
    await context.actualizarResumenDisponibilidad();
    assert.equal(summaries.at(-1).summary.horas, 1);
    assert.equal(calls, 2);
    assert.equal(context.validarFormulario(), true);
});

test('availability summary excludes new recesses without extending saved ranges', () => {
    const { context } = assignmentFixture();
    const resumen = context.resumirDisponibilidad(availability(4).data.data, 'colegio', [
        { hora_inicio: '09:00', hora_fin: '09:45' },
        { hora_inicio: '09:30', hora_fin: '10:00' },
    ]);
    assert.equal(resumen.horas, 3);
    assert.deepEqual(Array.from(resumen.rangos), ['Lun 07:00-09:00', 'Lun 10:00-11:00']);
    assert.equal(context.franjasSinRecesos('09:00', '09:30', [{ hora_inicio: '09:00', hora_fin: '10:00' }]).length, 0);
});

test('assignment summary labels simulated ranges only in the selected institution', () => {
    const { context } = assignmentFixture();
    const records = [
        ...availability(4).data.data,
        { ...availability(2, 'academia').data.data[0], observacion: 'PRUEBA SIMULADA: demostracion.' },
    ];
    const real = context.resumirDisponibilidad(records, 'colegio');
    const simulated = context.resumirDisponibilidad(records, 'academia');
    assert.equal(real.simulada, false);
    assert.equal(simulated.simulada, true);
    assert.equal(simulated.horas, 2);
    const disponibilidadResumen = { classList: { add() {}, remove() {} } };
    const painter = loadFunctions(asignacionesSource, ['formatearHorasDisponibles', 'pintarResumenDisponibilidad'], {
        disponibilidadResumen,
    });
    painter.pintarResumenDisponibilidad(simulated);
    assert.match(disponibilidadResumen.textContent, /^2 hora\(s\) disponibles \(datos de prueba\):/);
    painter.pintarResumenDisponibilidad(real);
    assert.doesNotMatch(disponibilidadResumen.textContent, /datos de prueba/);
});

test('late recess response cannot overwrite availability for a newer selection', async () => {
    const { context } = assignmentFixture();
    const oldRecess = deferred();
    context.obtenerRecesosDisponibilidad = () => oldRecess.promise;
    const pending = context.actualizarResumenDisponibilidad();
    context.profesorSelect.value = '2';
    context.obtenerRecesosDisponibilidad = async () => [];
    await context.actualizarResumenDisponibilidad();
    oldRecess.resolve([{ hora_inicio: '07:00', hora_fin: '11:00' }]);
    await pending;
    assert.equal(context.horasDisponiblesActuales, 40);
});

test('custom selectors also refresh the classroom label after editing or clearing', () => {
    const fields = ['profesorSelect', 'institucionSelect', 'cursoSelect', 'gradoSelect',
        'aulaSelect', 'rolSelect', 'estadoSelect', 'filtroInstitucion'];
    const refreshed = [];
    const context = loadFunctions(asignacionesSource, ['actualizarTodosCustomSelect'], {
        ...Object.fromEntries(fields.map(field => [field, { field }])),
        actualizarCustomSelect: select => refreshed.push(select.field),
    });
    context.actualizarTodosCustomSelect();
    assert.deepEqual(refreshed, fields);
});

test('classroom choices respect institution and preserve rooms shared across levels', () => {
    const aulaSelect = { value: '', options: [], appendChild(option) { this.options.push(option); } };
    const context = loadFunctions(asignacionesSource, ['cargarSelectAulas', 'normalizarTexto'], {
        aulaSelect, institucionSelect: { value: 'colegio' },
        aulas: [
            { id: 1, nombre: 'Aula 101', institucion: 'colegio', nivel: 'primaria', activo: true },
            { id: 2, nombre: 'Aula 102', institucion: 'ambos', nivel: 'secundaria', activo: true },
            { id: 3, nombre: 'Aula 301', institucion: 'academia', activo: true },
            { id: 4, nombre: 'Aula 103', institucion: 'ambos', activo: false },
        ],
        document: { createElement: () => ({}) }, actualizarCustomSelect() {}, nombreAula: aula => aula.nombre,
    });
    context.cargarSelectAulas();
    assert.deepEqual(aulaSelect.options.map(option => option.value), [1, 2]);
});

test('late availability responses cannot replace the newly selected professor', async () => {
    const first = deferred();
    const { context, summaries } = assignmentFixture(url =>
        url.endsWith('/1') ? first.promise : Promise.resolve(availability(1))
    );
    const oldRequest = context.actualizarResumenDisponibilidad();
    context.profesorSelect.value = '2';
    await context.actualizarResumenDisponibilidad();
    first.resolve(availability(8));
    await oldRequest;
    assert.equal(summaries.at(-1).summary.horas, 1);
    assert.equal(context.horasInput.max, '40');
    assert.equal(summaries.length, 1);
});

test('late availability errors do not overwrite the latest successful summary', async () => {
    const first = deferred();
    const { context, summaries } = assignmentFixture(url =>
        url.endsWith('/1') ? first.promise : Promise.resolve(availability(3))
    );
    const oldRequest = context.actualizarResumenDisponibilidad();
    context.profesorSelect.value = '2';
    await context.actualizarResumenDisponibilidad();
    first.reject(new Error('Old request failed'));
    await oldRequest;
    assert.equal(summaries.at(-1).summary.horas, 3);
    assert.equal(summaries.length, 1);
});

test('saving is blocked while availability is pending or failed', async () => {
    const pending = deferred();
    const { context } = assignmentFixture(() => pending.promise);
    const request = context.actualizarResumenDisponibilidad();
    assert.equal(context.validarFormulario(), false);
    pending.reject(new Error('Unavailable'));
    await request;
    assert.equal(context.validarFormulario(), false);
    context.window.axios.get = async () => availability(3);
    await context.actualizarResumenDisponibilidad();
    assert.equal(context.validarFormulario(), true);
});

test('clearing the form invalidates an outstanding availability request', async () => {
    const pending = deferred();
    const { context, summaries } = assignmentFixture(() => pending.promise);
    const request = context.actualizarResumenDisponibilidad();
    context.limpiarFormulario();
    pending.resolve(availability(8));
    await request;
    assert.equal(context.horasDisponiblesActuales, null);
    assert.equal(context.horasInput.value, '');
    assert.equal(summaries.length, 1);
    assert.equal(summaries[0].summary, null);
});

test('late recess validation cannot validate the newly selected grade', async () => {
    const first = deferred();
    let calls = 0;
    const { context, recesses } = assignmentFixture(() =>
        ++calls === 1 ? first.promise : Promise.resolve({ data: { data: [] } })
    );
    const oldRequest = context.actualizarResumenRecesos();
    context.gradoSelect.value = '2';
    await context.actualizarResumenRecesos();
    first.resolve({ data: { data: [
        { hora_inicio: '09:00', hora_fin: '09:15' },
        { hora_inicio: '10:00', hora_fin: '10:15' },
    ] } });
    await oldRequest;
    assert.equal(context.configuracionRecesosLista, false);
    assert.equal(recesses.at(-1).state, 'error');
});

test('generation warnings account for current and other incomplete assignments', () => {
    const { context } = assignmentFixture();
    const warning = context.obtenerAvisoGeneracion(generation([
        { asignacion_id: 1, horas_pendientes: 2, motivo: 'Aula ocupada.' },
        { asignacion_id: 2, horas_pendientes: 3 },
    ]));
    assert.match(warning, /faltan 2 hora/);
    assert.match(warning, /Aula ocupada/);
    assert.match(warning, /3 hora\(s\) pendientes en 1 otra/);
    const otherOnly = context.obtenerAvisoGeneracion(generation([
        { asignacion_id: 2, horas_pendientes: 3 },
    ]));
    assert.doesNotMatch(otherOnly, /pero faltan/);
    assert.match(otherOnly, /3 hora\(s\) pendientes/);
    assert.equal(context.obtenerAvisoGeneracion(generation()), null);
    assert.equal(context.obtenerAvisoGeneracion({ data: {} }), null);
});

test('activation shows a warning for partial generation and keeps existing guards', async () => {
    const { context, alerts, statuses } = assignmentFixture();
    let posts = 0;
    context.window.axios.post = async () => {
        ++posts;
        return generation([{ asignacion_id: 1, horas_pendientes: 2 }]);
    };
    await context.cambiarEstadoAsignacion(1, true);
    assert.match(alerts.at(-1), /activada, pero faltan 2/);
    assert.equal(statuses.at(-1)[2], 'warning');
    context.asignaciones[0].profesor.estado = 'inactivo';
    await context.cambiarEstadoAsignacion(1, true);
    context.asignaciones[0].profesor.estado = 'activo';
    context.asignaciones[0].aula_id = null;
    await context.cambiarEstadoAsignacion(1, true);
    assert.equal(posts, 1);
    assert.match(context.obtenerMensajeError({ response: { data: {
        message: 'Generic', errors: { aula_id: ['Selecciona un aula.'] },
    } } }), /Selecciona un aula/);
});

test('returning to the tab refreshes assignments, availability, and recesses', () => {
    const handler = asignacionesSource.match(/    window\.addEventListener\('focus', \(\) => \{[\s\S]*?\n    \}\);/);
    assert.ok(handler);
    let callback;
    const calls = [];
    const context = vm.createContext({
        cargando: false, window: { addEventListener: (_, listener) => { callback = listener; } },
        cargarAsignaciones: () => calls.push('assignments'),
        actualizarResumenDisponibilidad: () => calls.push('availability'),
        actualizarResumenRecesos: () => calls.push('recesses'),
    });
    vm.runInContext(handler[0], context);
    callback();
    assert.deepEqual(calls, ['assignments', 'availability', 'recesses']);
    context.cargando = true;
    callback();
    assert.equal(calls.length, 3);
});

test('group exports preserve all visible filters', () => {
    const context = loadFunctions(horariosSource, ['obtenerFiltrosExportacion'], {
        institucionActiva: 'colegio', filtroProfesor: { value: '1' },
        filtroAula: { value: '2' }, filtroGrado: { value: '3' },
        filtroCurso: { value: '4' },
    });
    const expected = { institucion: 'colegio', profesor_id: '1', aula_id: '2', grado_id: '3', curso_id: '4' };
    for (const [tipo, field] of [['profesor', 'profesor_id'], ['aula', 'aula_id'], ['grado', 'grado_id']]) {
        assert.deepEqual(JSON.parse(JSON.stringify(context.obtenerFiltrosExportacion({ tipo, id: 9 }))),
            { ...expected, [field]: 9 });
    }
    context.filtroCurso.value = 'todos';
    assert.equal(context.obtenerFiltrosExportacion().curso_id, undefined);
    context.semanaInput = { value: '2026-10-26' };
    assert.equal(context.obtenerFiltrosExportacion().semana_inicio, '2026-10-26');
});

test('changing weeks while loading cannot render the previous week', async () => {
    const first = deferred();
    const requests = [];
    const rendered = [];
    let context;
    context = loadFunctions(horariosSource, ['cargarDatos'], {
        cargando: false, recargaDatosPendiente: false, institucionActiva: 'colegio', semanaInput: { value: '2026-10-19' },
        API: { horarios: 'horarios', grados: 'grados', disponibilidades: 'disponibilidades', configuraciones: 'configuraciones' },
        horariosCache: [], gradosCache: [], disponibilidadesCache: [], configuracionesHorarioCache: [],
        cargarTodasLasPaginas: (url, params) => {
            if (url !== 'horarios') return Promise.resolve([]);
            requests.push(params.semana_inicio);
            return params.semana_inicio === '2026-10-19' ? first.promise : Promise.resolve([{ id: 2 }]);
        },
        normalizarHorario: item => item, render: () => rendered.push(context.horariosCache.map(item => item.id)),
        console: { error() {} }, mostrarMensaje() {}, obtenerMensajeError: () => 'Failed',
    });
    const pending = context.cargarDatos();
    context.semanaInput.value = '2026-10-26';
    await context.cargarDatos();
    first.resolve([{ id: 1 }]);
    await pending;
    assert.deepEqual(requests, ['2026-10-19', '2026-10-26']);
    assert.deepEqual(rendered, [[2]]);
});

test('changing institutions during loading fetches the latest institution before rendering', async () => {
    const first = deferred();
    const requests = [];
    const rendered = [];
    let context;
    context = loadFunctions(horariosSource, ['cargarDatos'], {
        cargando: false, recargaDatosPendiente: false, institucionActiva: 'colegio',
        API: { horarios: 'horarios', grados: 'grados', disponibilidades: 'disponibilidades', configuraciones: 'configuraciones' },
        horariosCache: [], gradosCache: [], disponibilidadesCache: [], configuracionesHorarioCache: [],
        cargarTodasLasPaginas: (url, params) => {
            if (url !== 'horarios') return Promise.resolve([]);
            requests.push(params.institucion);
            return params.institucion === 'colegio' ? first.promise : Promise.resolve([{ id: 2 }]);
        },
        normalizarHorario: item => item,
        render: () => rendered.push(context.horariosCache.map(item => item.id)),
        console: { error() {} }, mostrarMensaje() {}, obtenerMensajeError: () => 'Failed',
    });
    const request = context.cargarDatos();
    context.institucionActiva = 'academia';
    await context.cargarDatos();
    first.resolve([{ id: 1 }]);
    await request;
    assert.deepEqual(requests, ['colegio', 'academia']);
    assert.deepEqual(rendered, [[2]]);
    assert.equal(context.cargando, false);
});

test('legacy active assignments show Aula pendiente in their row', () => {
    const rows = [];
    const context = loadFunctions(asignacionesSource, [
        'renderizarAsignaciones', 'normalizarTexto', 'nombreProfesor', 'nombreCurso',
        'nombreGrado', 'nombreAula', 'escaparHTML', 'capitalizar',
    ], {
        asignaciones: [{ id: 1, activo: true, aula_id: null, institucion: 'colegio',
            profesor: { nombre: 'Ana' }, curso: { nombre: 'Matematica' },
            grado: { nombre: 'Primero' }, rol: 'titular', horas_asignadas: 2 }],
        buscarInput: { value: '' }, filtroInstitucion: { value: '' },
        tabla: { appendChild: row => rows.push(row) },
        document: { createElement: () => ({ dataset: {} }) },
    });
    context.renderizarAsignaciones();
    assert.equal(rows.length, 1);
    assert.match(rows[0].innerHTML, /Aula pendiente/);
    assert.doesNotMatch(rows[0].innerHTML, />\s*Activo\s*</);
});

test('editing retains unavailable references and leaves a missing aula unselected', () => {
    function select() {
        let options = [];
        let selected = '';
        return {
            get options() { return options; },
            set innerHTML(_) { options = [{ value: '', dataset: {} }]; selected = ''; },
            get value() { return selected; },
            set value(value) { selected = options.some(item => String(item.value) === String(value)) ? String(value) : ''; },
            appendChild(option) { options.push(option); },
            get selectedOptions() { return options.filter(item => String(item.value) === selected); },
        };
    }
    const context = loadFunctions(asignacionesSource, [
        'editarAsignacion', 'seleccionarReferenciaAsignada', 'cargarSelectProfesores',
        'cargarSelectCursos', 'cargarSelectGrados', 'cargarSelectAulas',
        'obtenerGradoSeleccionado', 'normalizarTexto', 'nombreProfesor', 'nombreCurso',
        'nombreGrado', 'nombreAula', 'capitalizar', 'profesorCompatibleInstitucion',
        'obtenerInstitucionProfesor',
    ], {
        asignaciones: [{ id: 1, profesor_id: 7, curso_id: 8, grado_id: 9, aula_id: null,
            activo: true, institucion: 'colegio', rol: 'titular', horas_asignadas: 2,
            profesor: { id: 7, nombre: 'Ana', estado: 'inactivo' },
            curso: { id: 8, nombre: 'Historia', nivel: 'secundaria', activo: false },
            grado: { id: 9, nombre: 'Primero', nivel: 'secundaria', activo: false } }],
        profesores: [], cursos: [], grados: [{ id: 10, nombre: 'Primaria', nivel: 'primaria', activo: true }], aulas: [],
        profesorSelect: select(), cursoSelect: select(), gradoSelect: select(), aulaSelect: select(),
        institucionSelect: { value: '' }, horasInput: {}, rolSelect: {}, estadoSelect: {},
        observacionesInput: {}, guardarTexto: {}, asignacionEditandoId: null,
        programacionManual: { cargar() {} },
        document: { createElement: () => ({ dataset: {} }) },
        window: { scrollTo() {} }, alert() {}, actualizarCustomSelect() {},
        actualizarContadorObservaciones() {}, actualizarTodosCustomSelect() {},
        actualizarResumenDisponibilidad() {}, actualizarResumenRecesos() {}, ocultarEstado() {},
    });
    context.editarAsignacion(1);
    for (const [field, id] of [['profesorSelect', '7'], ['cursoSelect', '8'], ['gradoSelect', '9']]) {
        assert.equal(context[field].value, id);
        assert.equal(context[field].selectedOptions[0].dataset.noDisponible, '1');
        assert.match(context[field].selectedOptions[0].textContent, /No disponible/);
    }
    assert.equal(context.gradoSelect.options.some(item => item.value === 10), false);
    assert.equal(context.obtenerGradoSeleccionado().id, 9);
    assert.equal(context.aulaSelect.value, '');
    assert.equal(context.asignaciones[0].aula_id, null);
});
