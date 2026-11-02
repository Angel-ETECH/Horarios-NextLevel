# Asignación manual y disponibilidad

## Uso

En **Asignaciones**, después de elegir profesor, institución, curso, grado y aula,
se puede mantener **Distribución automática** o elegir **Elegir día y horario**.
La selección manual muestra los bloques lectivos cubiertos por la disponibilidad
guardada. Se pueden elegir varios seguidos en un mismo día o repartirlos entre
los días disponibles; no existe una preferencia fija por martes o jueves.

Las horas asignadas son horas lectivas: cada una corresponde a un bloque cuya
hora de inicio y fin se muestra en pantalla. Los recesos del nivel quedan fuera
de la selección. Un bloque ocupado informa si el conflicto es del profesor,
aula, grado o una recuperación. Guardar vuelve a validar los recursos en el
servidor; una consulta previa no garantiza que el espacio siga libre.

Editar o reactivar una asignación manual conserva su selección. La generación
automática respeta las clases existentes; al generar varias asignaciones se
reservan primero las selecciones manuales. Guardar una asignación solo genera
sus propios bloques. Si se cambia un bloque manual desde Horarios, se actualiza
también la selección guardada en la asignación; los cambios de profesor, curso,
grado o aula deben hacerse desde Asignaciones.

## Disponibilidad

Se permite añadir días, ampliar rangos y quitar horas libres con clases ya
asignadas. Solo se rechaza la eliminación de minutos que una clase o recuperación
programada utiliza. El mensaje identifica el curso y su horario afectado.
El rechazo revierte toda la edición; no guarda una disponibilidad parcial ni
mueve las clases para hacer encajar el cambio.

Una ampliación no queda bloqueada por una inconsistencia antigua fuera del
rango editado. Esas inconsistencias deben revisarse por separado con
`php artisan horarios:verificar`.

## Instalación y compatibilidad

Ejecutar `php artisan migrate` y `npm run build` al incorporar estos cambios.
La migración `2026_10_23_000001_add_programacion_manual_to_profesor_curso`
añade `modo_programacion` y `bloques_manuales` a `profesor_curso`. Las
asignaciones anteriores quedan como automáticas y sus horarios no se modifican.
No se ejecuta ninguna limpieza de datos desde una migración ni desde la interfaz.

## Verificación local del 24/10/2026

- 182 pruebas PHP, 921 aserciones; 36 pruebas JavaScript; compilación Vite correcta.
- Selección manual comprobada en los seis días admitidos de la semana habitual,
  bloques consecutivos, edición, reactivación y regeneración sin duplicados.
- Rechazo de recesos, horas fuera de disponibilidad, selección incompleta,
  duplicados y cruces de profesor, aula o grado, con reversión de los cambios.
- Ampliación y reducción de horas libres, rechazo de minutos ocupados y
  sincronización entre la edición de un bloque y su asignación manual.
- Prueba HTTP autenticada sobre una copia MySQL: ampliación, eliminación de
  horas libres, rechazo 422 de reducción ocupada con detalle, y generación
  automática en el único espacio libre sin alterar los bloques manuales.
- En navegador se comprobó la creación manual y que al editar reaparecen los
  dos bloques elegidos. La comprobación visual adicional de disponibilidad
  quedó limitada al deshabilitarse el control del navegador; su guardado y sus
  validaciones sí se comprobaron mediante HTTP y pruebas automatizadas.

La limpieza solicitada se realizó exclusivamente en `nextlevel_horarios` local,
con respaldo SQL restaurado y comparado en 22 tablas antes de borrar. Se
conservó `admin@nextlevel.com`, con su contraseña intacta, y los catálogos de
cursos, grados, alumnos y configuración de recesos. Se eliminaron los otros
tres usuarios de prueba y los datos de profesores, aulas, disponibilidad,
asignaciones, horarios e historial. Se invalidaron las sesiones anteriores.
No volver a ejecutar seeders de demostración para iniciar la prueba limpia.

Después de la limpieza, los siete listados API comprobados y la página de
acceso respondieron correctamente. La auditoría informó cero incidencias en
la base sin asignaciones. Esto verifica el estado vacío, no sustituye la
validación de los nuevos datos que ingrese la institución.
