# Revision funcional del 20/10/2026

Actualizacion del 21/10: el usuario confirmo que los datos son de demostracion
y autorizo su regularizacion. El resultado posterior esta en
`REGULARIZACION_DATOS_PRUEBA_2026-10-21.md`. Este informe conserva el estado
anterior a esa autorizacion.

## Estado de esta revision

Se corrigieron los fallos reproducidos y se verificaron los flujos indicados abajo.
Los cambios estan locales en `frontend`: esta revision no crea commits, no hace
push y no integra nada en `main`.

No se declara una entrega definitiva de los datos reales. El usuario eligio
revisar disponibilidad y aula antes de cambiar datos, por lo que no se altero
la base de trabajo `nextlevel_horarios`.

## Correcciones

- Editar y activar una asignacion valida profesor, curso, grado, institucion,
  aula y carga maxima igual que la accion Activar. No permite dejar sin aula
  una asignacion activa; una asignacion antigua sin aula puede desactivarse.
- Cambiar las referencias y desactivar a la vez elimina tambien los bloques
  anteriores. Reducir horas conserva los primeros dias de la semana, no un
  orden alfabetico.
- Crear, editar, eliminar y restaurar profesores, cursos y grados conserva
  cambios e historial en la misma transaccion. Lo mismo aplica al CRUD de
  aulas. Si falla el historial, se revierten los cambios.
- No se puede eliminar un profesor con asignaciones activas, reducir su carga
  maxima por debajo de lo asignado ni cambiar la institucion de un aula si
  deja incompatibles sus asignaciones. Se mantienen aulas compartidas entre
  niveles y aulas marcadas para ambas instituciones.
- Los filtros de aulas se combinan y permiten consultar aulas inactivas.
  La consulta de disponibilidad no ofrece aulas inactivas o de otra institucion.
- Los cambios de configuracion y recesos tienen historial propio. Se impide
  desactivar o reutilizar una configuracion en uso para otro nivel, turno,
  institucion o periodo. Los recesos siguen siendo editables y reprograman las
  clases dentro de una transaccion.
- Los recesos vigentes no mezclan configuraciones antiguas con la actual y
  prefieren el turno especifico a la configuracion completa de respaldo.
- El resumen de disponibilidad en Asignaciones descuenta los recesos vigentes,
  se actualiza al cambiar grado y evita respuestas tardias de otra seleccion.
  El selector visual de aula muestra el valor guardado al editar.
- El historial ordena de forma estable los eventos creados en el mismo segundo.
- Con `APP_DEBUG=false`, las respuestas API de error de servidor no exponen
  SQL, rutas internas, trazas ni detalles SMTP. Las validaciones 422 se conservan.
- La auditoria npm completa detecto dos avisos en dependencias de desarrollo.
  Se actualizaron Vite 7.3.6 -> 7.3.7 y follow-redirects 1.16.0 -> 1.16.1 sin
  cambiar versiones mayores. La auditoria posterior y el build son correctos.

## Comprobaciones actuales

- PHPUnit: **124 pruebas, 592 aserciones**, sin fallos.
- JavaScript: **24 pruebas**, sin fallos.
- `npm run build`, `composer validate` y `git diff --check`: correctos.
- Auditorias de dependencias PHP y npm: sin vulnerabilidades notificadas.
- Navegador contra MySQL de prueba aislado: carga de disponibilidad a la primera
  seleccion, aula visible al editar, desactivar y reactivar desde el formulario,
  desaparicion y regeneracion de bloques, recesos 30 -> 45 -> 30 minutos e historial.
- Las clases de prueba terminan a las 09:00; la siguiente comienza a las 09:30
  o 09:45, segun el receso guardado. No se inventan clases si no hay carga,
  disponibilidad o recursos suficientes.
- Consulta publica por profesor, grado y aula: las mismas cuatro clases.
  Revision movil a 390x844 sin desbordamiento horizontal ni errores de consola.
- Descargas reales desde la interfaz: PNG, PDF y XLSX. Imagen inspeccionada,
  PDF renderizado e inspeccionado (una pagina A4), XLSX leido con PhpSpreadsheet:
  cuatro clases en Horario y cuatro filas en Detalle, filtros correctos y sin
  paneles divididos. El nombre del profesor no se repite en cada bloque de su
  propio horario. No se abrio esta descarga en Excel nativo durante esta revision.
- El nuevo verificador en la base de prueba devuelve cero incidencias.

## Datos reales pendientes de revision

La verificacion en solo lectura encontro **102 horarios y 27 asignaciones activas**:

- 0 cruces entre profesores, aulas o grados; 0 cruces con recesos.
- 0 horarios sin asignacion activa; 0 aulas de horario distintas de su asignacion.
- 27 bloques sin disponibilidad registrada que cubra su rango (Academia).
- Asignacion #11 de Colegio con un aula marcada solo para Academia.
- Profesor #1: contador de carga 24, suma de asignaciones activas 28, limite 30.

No se agrego disponibilidad, cambio aula, regenero ni actualizo el contador en
la base real. Primero deben confirmarse los datos academicos autorizados.
Las correcciones de codigo previenen nuevos casos, pero no cambian los datos
antiguos automaticamente.

## Comprobacion repetible

Desde la carpeta del proyecto:

```bash
php artisan horarios:verificar
php artisan horarios:verificar --json
php artisan horarios:verificar --periodo=2026-2027
```

El comando es de solo lectura. Devuelve codigo 0 sin incidencias y 1 cuando
encuentra datos que revisar. Indica los IDs afectados sin cambiarlos. El filtro
de periodo limita los bloques; la carga y las referencias activas se revisan
globalmente.

Esta revision no requiere nuevas migraciones ni paquetes adicionales. El lock
de npm si cambia por las actualizaciones de seguridad: en otra copia ejecutar
`npm ci` y `npm run build`. Para probar el codigo local, usar MySQL de XAMPP,
`php artisan serve` y `npm run dev`, o el build ya generado.
Los datos de prueba, credenciales, respaldos y capturas no
forman parte de Git. Las eliminaciones previas de `horario.pdf` y `horario.xlsx`
se respetaron.

No se repitio el envio de correo real, confirmado por el usuario en la revision
del 18/10. Las pruebas automaticas de autenticacion y correo si se ejecutaron.
Continua pendiente un despliegue en hosting real; no se ha suministrado servidor.
