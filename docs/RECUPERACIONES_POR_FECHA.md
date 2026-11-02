# Recuperaciones por fecha

## Uso

1. Entrar a Horarios: se muestra automaticamente la semana actual.
2. Filtrar al profesor, grado o aula y pulsar **Reprogramar clase**.
3. Elegir el bloque y su fecha original. Indicar la fecha de recuperacion,
   un aula individual y el rango en que se puede recuperar.
4. Si el profesor confirma una disponibilidad excepcional, marcarla para
   esa fecha y rango. Sin esa confirmacion se exige su disponibilidad regular.
5. Para domingos o fuera de la jornada del grado, confirmar tambien la
   autorizacion institucional, del grupo y del aula. Esta casilla registra una
   confirmacion del administrador; no envia solicitudes ni notificaciones.
6. Escribir el motivo, buscar horarios disponibles, seleccionar uno y confirmar.

La duracion se calcula desde la clase original; no puede acortarse ni ampliarse.
La recuperacion se busca entre 07:00 y 20:00, en intervalos de inicio de 15 minutos.
Se conservan profesor, curso y grado; puede cambiarse el aula por otra compatible
y con capacidad suficiente. Se comprueban choques de profesor, grado y aula
entre ambas instituciones, ademas de los recesos del nivel correspondiente.
No se promete encontrar un hueco si ninguno satisface todas estas condiciones.

## Calendario y horario habitual

La consulta muestra automaticamente la semana actual, de lunes a domingo,
sin selector de fechas ni busqueda de horarios pasados. Las fechas solo se
eligen al reprogramar una clase. El calendario sigue la fecha de America/Lima
y se actualiza al volver a la pantalla o al cambiar el dia.
La clase desaparece solo de su fecha original y aparece en la fecha de
recuperacion, identificada como tal. Puede recuperarse en otra semana.
La plantilla semanal, las horas asignadas y la disponibilidad regular no cambian.
**Editar horario base** abre la edicion administrativa de la plantilla sin
excepciones por fecha. **Volver a consulta** restablece la semana actual.
La edicion de bloques habituales solo se permite en esa vista administrativa.
Guardar una recuperacion futura no adelanta la consulta a otra semana:
aparece en **Recuperaciones pendientes** hasta su fecha y se incorpora
automaticamente al horario cuando corresponda.

La consulta publica y las exportaciones Excel, PDF e imagen reciben
`semana_inicio=YYYY-MM-DD` y utilizan la misma proyeccion del calendario.
Sin ese parametro consultan la plantilla habitual. Excel incluye domingo,
fecha en el detalle y la identificacion de las recuperaciones. Los recesos
visuales siguen reservados a la consulta por grado; ocultarlos por profesor
o aula no desactiva su validacion.

## Cancelacion y proteccion

En Recuperaciones pendientes, **Cancelar recuperacion** pide un motivo.
Antes de restaurar la fecha original se comprueba que no haya sido ocupada por
otra clase. Si existe un conflicto se rechaza la cancelacion y no cambia nada.
Las creaciones y cancelaciones quedan registradas en Historial.
Mientras una clase tenga una recuperacion programada para hoy o una fecha futura, debe cancelarse antes
de editar, eliminar, desactivar o regenerar su bloque habitual. Esto tambien
protege operaciones indirectas sobre profesores y asignaciones; no borrar
registros directamente en la base para eludir la validacion.
Las limpiezas masivas comprueban todos los bloques antes de eliminar y no
guardan cambios parciales si existe una recuperacion pendiente. Un aula de
recuperacion tampoco puede eliminarse, desactivarse, cambiar a una institucion
incompatible o reducir su capacidad por debajo del grupo reservado.
Este modulo no registra asistencia ni afirma que la recuperacion se realizo.
Al pasar su fecha deja de figurar como pendiente y de bloquear la plantilla
o el aula. Se conserva el registro y su copia original en Historial; no se
eliminan datos antiguos ni se marca automaticamente la asistencia.
El verificador de recuperaciones comprueba reservas de hoy en adelante.

## Instalacion y comprobacion

En cada copia del proyecto, respaldar la base y ejecutar:

```bash
php artisan migrate
npm run build
php artisan horarios:verificar
```

La migracion `2026_10_21_000001_create_recuperaciones_horario_table` crea una
tabla nueva; no regenera horarios ni altera asignaciones existentes. Sin esa
tabla, las lecturas habituales siguen funcionando, pero crear una recuperacion
se rechaza con una indicacion de ejecutar la migracion.
Las rutas de creacion, busqueda y cancelacion requieren autenticacion.
El verificador comprueba tambien las recuperaciones sin modificar datos.

Las pruebas de navegador se realizan en una copia MySQL y con una cuenta QA;
no trasladar esa cuenta ni las recuperaciones simuladas a la base de trabajo.

## Verificacion local del 21/10/2026

- Suite PHP: 164 pruebas, 825 aserciones, sin fallos.
- Suite JavaScript: 36 pruebas sin fallos; compilacion Vite completada.
- Navegador: creacion del lunes al domingo 14:00-14:45, busqueda de huecos,
  consulta publica, descargas Excel/PDF/imagen y cancelacion con restitucion.
- Exportaciones descargadas inspeccionadas, incluido domingo en la matriz y
  fecha e identificacion de recuperacion en el detalle de Excel.
- Consulta normal sin selector de fechas, recuperacion futura visible como
  pendiente sin cambiar la semana actual, retorno desde edicion del horario
  base y PDF publico de la semana actual comprobados en navegador.
- Migracion local aplicada tras verificar el respaldo por restauracion. Se
  conservaron las huellas de horarios, asignaciones, disponibilidad y catalogos.
- Al instalar la tabla se conservaron los 102 bloques y las 27 asignaciones.
  La ultima lectura encontro 100 bloques y 27 asignaciones: Historial registra
  dos eliminaciones manuales posteriores desde Horarios, que se conservaron.
  Cero recuperaciones simuladas y cero incidencias en la base de trabajo.
  La recuperacion de prueba permanece solo en la copia QA.

Estos resultados describen la instalacion local; no certifican un despliegue
en hosting ni confirman disponibilidades reales del personal.
