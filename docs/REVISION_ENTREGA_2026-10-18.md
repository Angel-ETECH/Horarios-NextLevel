# Revision de entrega - 18 de octubre de 2026

## Codigo preparado

- Rama de trabajo: `frontend`. No se ha fusionado con `main`.
- Commit de respaldo del trabajo previo: `cada705`.
- Autenticacion: `/api/auth/user` apunta a `me`; se habilitaron `/api/auth/me`
  y `/api/auth/refresh`, conservando los endpoints de acceso existentes.
- Los alias `/api/auth/login`, `/register`, `/forgot-password` y
  `/reset-password` mantienen el contrato de las rutas sin el prefijo `auth`.
- Cierre de sesion revoca solo el token actual. Renovar consume el token una
  sola vez y conserva nombre, permisos y vencimiento. Si falla la renovacion,
  la transaccion mantiene el token anterior.
- Recuperacion usa `APP_URL` cuando no hay `FRONTEND_URL`; se normaliza la
  barra final. Las credenciales de la plantilla estan vacias y el correo local
  usa logs. El `.env` privado existente no se reemplazo.
- Instrucciones de instalacion y entrega: `docs/INSTALACION_Y_ENTREGA.md`.

## Aulas de las asignaciones existentes

Antes de modificar datos se genero un respaldo SQL completo fuera del repo:

`C:\Users\LENOVO\Documents\Codex\2026-09-26\estoy-en-c-xampp-htdocs-horarios-3\respaldos\horarios-antes-entrega-20261018-174222.sql`

El archivo tiene 368079 bytes y 21 definiciones de tablas. Contiene datos
privados: no subirlo a GitHub ni compartirlo sin autorizacion.

Se completaron 24 asignaciones activas sin `aula_id`: 20 conservaron su unica
aula historica. Para las cuatro ambiguas se aplico una propuesta automatica
que conserva ubicaciones cuando es posible y comprueba cruces:

| Asignacion | Profesor / curso / grado | Aula aplicada |
| --- | --- | --- |
| 1 | Carlos / Matematicas Primaria / 1ro A | Aula 202 |
| 17 | Ana / Matematicas Academia / Pre A | Aula Virtual 1 |
| 18 | Luis / Personal Social / 3ro A | Aula 102 |
| 34 | Maria / Comunicacion / 3ro A | Aula 102 |

Estas cuatro decisiones requieren conformidad del responsable academico:
la ausencia de cruces no acredita por si sola la conveniencia fisica del aula.
Se cambiaron solo las aulas de 10 bloques; no se alteraron dias, horas ni
periodos de los 102 horarios. Se registraron los cambios en historial.

La auditoria final de `nextlevel_horarios` informa:

- 102 horarios activos.
- 0 asignaciones activas sin aula.
- 0 cruces activos por profesor, aula o grado dentro del mismo dia y periodo.

El comando `horarios:completar-aulas` permite revisar otros datos antiguos.
Sin opciones solo simula. `--aplicar --respaldo=/ruta/al/respaldo.sql` guarda
con historial, bajo transaccion; exige un archivo previo no vacio, pero no
certifica que ese respaldo pueda restaurarse. No aplicarlo sin revisar el plan.
Si faltan horarios historicos o no hay aula fija libre, no modifica nada.

## Verificaciones ejecutadas

- PHPUnit: **66 pruebas, 321 aserciones**, sin fallos, en SQLite aislado.
- JavaScript: **15 pruebas**, sin fallos.
- `npm run build`: compilacion de produccion correcta.
- `git diff --check`: sin errores de espacios.
- Instalacion MySQL nueva: **26 migraciones aplicadas correctamente** en
  `horarios_entrega_test_20261018`, separada de la base de trabajo.
- Recorrido API mediante el kernel HTTP de Laravel sobre esa base MySQL:
  **48 comprobaciones correctas**, incluida la reversion de los datos de prueba.
  El guion local esta fuera del repositorio, en la carpeta `qa` de este chat.
- Ese recorrido creo y edito un profesor; guardo y consulto disponibilidad;
  creo configuracion y asignacion con aula; verifico generacion hasta el receso
  y continuacion posterior; consulto profesor/grado/aula; genero XLSX, PDF y
  HTML de imagen; desactivo/reactivo asignacion y profesor; rechazo disponibilidad
  incompatible; cambio recesos y comprobo reprogramacion; elimino/restauro
  bloques; elimino una asignacion; verifico historial, renovacion y logout.
- La consulta local `/consulta-horarios` respondio HTTP 200 en el puerto 8000.

## Pendientes para autorizar la entrega

- **Prueba visual en navegador pendiente.** La herramienta de control de Chrome
  se detuvo al no poder verificar la URL con seguridad. No se continuaron las
  acciones de navegador ni se sustituyeron por otra automatizacion.
- La respuesta de HTML, XLSX o PDF no certifica alineacion visual ni descarga
  real desde la interfaz. Revisar imagen y PDF exportados, Excel al desplazar,
  vista movil, filtros y transiciones de las pantallas.
- SMTP real, entrega de correos, workers persistentes y despliegue no probados.
- Validar las cuatro aulas propuestas y revisar esta rama antes de fusionarla.

Las pruebas cubren los escenarios descritos; no constituyen una garantia de
ausencia total de errores en cualquier dato o entorno.

## Integracion con la rama remota

Tras el rechazo de push por divergencia, se incorporaron los commits remotos
`ef5ddf0`, `17ed325` y `dad90b1` mediante merge, conservando `cada705` y
`52e6365`. No se uso rebase ni push forzado.

Se combinaron los cuatro archivos con conflictos, se evito duplicar el evento
de actualizacion de profesor y se conservaron las validaciones de horarios.
La reversion ampliada de historial utiliza servicios para asignaciones,
disponibilidad y aulas, valida cambios academicos con uso activo y sincroniza
las asignaciones al desactivar un profesor. Tambien se conserva la opcion de
recuperar horarios eliminados fisicamente desde sus snapshots.

Verificacion de la combinacion: **72 pruebas PHP / 342 aserciones**, **15
pruebas JS**, compilacion correcta y **48 comprobaciones MySQL**. La nueva
migracion `2026_10_12_000001_add_institucion_to_aulas_table` se verifico en la
base MySQL aislada: agrega `institucion` a aulas. En otras instalaciones,
respaldar y ejecutar `php artisan migrate` antes de usar el codigo actualizado.
No se importo el SQL que ya estaba versionado en la rama remota. La revision
visual y SMTP siguen pendientes como se indica arriba.
