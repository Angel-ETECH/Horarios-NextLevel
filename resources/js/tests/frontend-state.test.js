import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import test from 'node:test';
import vm from 'node:vm';

const asignacionesSource = readFileSync(
    new URL('../secciones/asignaciones.js', import.meta.url), 'utf8'
).replaceAll('\r\n', '\n');
const horariosSource = readFileSync(
    new URL('../secciones/horarios.js', import.meta.url), 'utf8'
).replaceAll('\r\n', '\n');
const consultaSource = readFileSync(
    new URL('../consulta-horarios.js', import.meta.url), 'utf8'
).replaceAll('\r\n', '\n');

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
    const context = vm.createContext(globals);
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
        asignaciones: [{ id: 1, aula_id: 1, profesor: { estado: 'activo' } }],
        alert: message => alerts.push(message), confirm: () => true,
        pintarResumenDisponibilidad: (summary, state) => summaries.push({ summary, state }),
        pintarResumenRecesos: (message, state) => recesses.push({ message, state }),
        mostrarEstado: (...args) => statuses.push(args), cargarAsignaciones: async () => {},
        obtenerGradoSeleccionado: () => ({ nivel: 'primaria', turno: 'manana' }),
        turnoConfigurable: () => 'mañana', editarAsignacion() {},
        cargarSelectProfesores() {}, actualizarContadorObservaciones() {},
        ocultarEstado() {}, actualizarTodosCustomSelect() {},
    });
    return { context, alerts, statuses, summaries, recesses };
}

test('availability is fetched again after it changes on the server', async () => {
    let calls = 0;
    const { context } = assignmentFixture(async () => availability(++calls === 1 ? 4 : 1));
    await context.actualizarResumenDisponibilidad();
    assert.equal(context.horasDisponiblesActuales, 4);
    await context.actualizarResumenDisponibilidad();
    assert.equal(context.horasDisponiblesActuales, 1);
    assert.equal(calls, 2);
    assert.equal(context.validarFormulario(), false);
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
    assert.equal(context.horasDisponiblesActuales, 1);
    assert.equal(context.horasInput.max, '1');
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
    assert.equal(context.horasDisponiblesActuales, 3);
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
