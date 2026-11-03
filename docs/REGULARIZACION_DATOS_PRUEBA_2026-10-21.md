# Regularizacion de datos de prueba del 21/10/2026

## Alcance autorizado

Continuacion de `REVISION_FUNCIONAL_2026-10-20.md`. El usuario confirmo que
los profesores y horarios actuales son datos de demostracion y autorizo crear
disponibilidad simulada identificada como prueba. No confirmo que Colegio y
Academia tengan los mismos rangos; no se copio disponibilidad entre instituciones.
Tambien confirmo que Aula 301 es compartida por ambas instituciones.

Estas franjas no son una declaracion de disponibilidad real de los docentes.

## Cambios aplicados en la base local

- Antes de cambiar datos se exporto la base y se verifico su restauracion,
  comparando las filas de sus 21 tablas. Los SQL y sus manifiestos estan en
  la carpeta privada `respaldos` del chat, fuera del repositorio.
- Se recalculo el contador del profesor #1: 24 -> 28, igual a la suma de sus
  asignaciones activas y por debajo de su limite de 30. Se registro en historial
  como correccion calculada no reversible, para no restaurar un contador erroneo.
- Aula #10, codigo A-301: institucion `academia` -> `ambos`. No se reasigno aula
  a ninguna clase. Se conserva su uso por Colegio y Academia.
- Se crearon 16 franjas de Academia para los cuatro profesores que tenian 27
  bloques sin disponibilidad. Se unieron solo intervalos contiguos ocupados
  por esos bloques: 1215 minutos en total. No se rellenaron huecos ni recesos.
- Cada franja incluye la observacion `PRUEBA SIMULADA`, indicando que no
  representa disponibilidad confirmada. Disponibilidad y Asignaciones muestran
  tambien el aviso visible de datos de prueba al consultar esos registros.
- Se conservaron los 102 horarios, las 27 asignaciones activas y toda la
  disponibilidad de Colegio. Se comprobaron las huellas de las tablas, no solo
  el numero de filas. No se regeneraron ni eliminaron clases.
- Las dos correcciones y las 16 altas tienen historial: eventos #163 a #180
  de esta base local. Esos IDs no son una dependencia del codigo.

## Verificacion

- Prueba previa en una copia restaurada de la base, despues aplicada a la local.
- Repetir la regularizacion en la copia no agrega registros ni eventos duplicados.
- `php artisan horarios:verificar --json`: 102 horarios, 27 asignaciones activas,
  cero incidencias de las reglas verificadas.
- Auditoria independiente: cero cruces, cruces con receso, bloques fuera de
  disponibilidad, asignaciones incompatibles y contadores incoherentes.
- PHPUnit: 125 pruebas, 598 aserciones, sin fallos.
- JavaScript: 27 pruebas, sin fallos; incluyen aislamiento de la etiqueta de prueba
  por profesor e institucion y el texto visible de ambos formularios.
- `npm run build` y `git diff --check`: correctos.
- Navegador con MySQL aislado: Academia carga los rangos desde la primera
  seleccion y muestra el aviso; cambiar a Colegio muestra sus rangos sin
  etiquetarlos como simulados. No se guardaron cambios desde esos formularios.

## Entrega

Esta regularizacion no agrega migraciones ni paquetes. Los cambios de datos
locales no viajan mediante Git: antes de llevarlos a otra base, respaldarla y
revisar sus propios datos y autorizaciones. No ejecutar `migrate:fresh`, seeders
ni la regularizacion de demostracion sobre una base real de produccion.

Para uso real, sustituir estos rangos por disponibilidad confirmada por cada
docente, revisar las aulas y volver a ejecutar el verificador. Cero incidencias
en esta prueba no certifica la disponibilidad real ni un despliegue en hosting.

Los cambios de codigo siguen locales en `frontend`. Esta intervencion no crea
commits, no hace push y no modifica `main`.
