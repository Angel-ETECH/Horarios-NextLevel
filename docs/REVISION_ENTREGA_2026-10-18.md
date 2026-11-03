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

## Verificaciones iniciales

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

- La revision visual inicial quedo pendiente por una limitacion de la herramienta.
  Posteriormente se completo el recorrido descrito al final de este documento,
  usando el navegador integrado y una base MySQL separada.
- No se comprobo el desplazamiento en la aplicacion nativa de Microsoft Excel:
  se verificaron el contenido del XLSX descargado, sus paneles y vistas renderizadas.
- Las verificaciones posteriores de correo, cola e instalacion se describen al
  final. Sigue pendiente el despliegue externo: el equipo aun no tiene hosting.
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
No se importo el SQL que ya estaba versionado en la rama remota. Las revisiones
posteriores de navegador, correo e instalacion se detallan a continuacion.

## Revision posterior en navegador real

Pruebas sobre `horarios_entrega_test_20261018`, con usuario y registros de
prueba independientes de `nextlevel_horarios`. No se borraron ni regeneraron
las asignaciones reales en esta ronda. Tras las pruebas, el usuario solicito
organizar y publicar los cambios con commits en `frontend`, sin integrar con `main`.

### Fallos corregidos

- La disponibilidad por horas descartaba una hora completa si el receso solo
  ocupaba una parte. Ahora conserva los minutos utilizables, los vuelve a cargar
  al seleccionar el profesor y calcula el total real sin incluir recesos.
- Los controles de inicio y minutos de receso estaban bloqueados y la vista
  usaba intervalos fijos. Ahora actualizan los bloques enviados al backend;
  se mantienen dos recesos por turno, separados dos horas entre sus inicios.
  La vista previa muestra el resultado antes de guardarlo. El backend sigue
  validando continuidad y revierte la operacion si no puede reprogramar.
- La semana quedaba recortada en escritorio. Se ajustaron las columnas y los
  bloques cortos; lunes a sabado se ven en el panel probado a 1280 px.
- La captura PNG desplazaba los textos de encabezados y total. Ahora usa el
  renderizado nativo del navegador y la dependencia local de html2canvas,
  sin depender de un script CDN.
- La imagen publica avisaba exito sin producir una descarga. Ahora utiliza
  un Blob, dimensiones completas y liberacion posterior del recurso temporal.
- PDF usa pesos compatibles con DejaVu Sans para evitar sustituciones de fuente.
  Los documentos identifican el profesor, grado o aula realmente consultado;
  solo incluyen al docente dentro del bloque cuando la consulta no es por profesor.
- Se unificaron los colores por nivel y se elimino el prefijo duplicado de aula
  en Excel. El formulario sin profesor seleccionado ya no afirma que no haya
  disponibilidad; los modales de profesores se exponen al abrirse a accesibilidad.
- Se restauro la carga de recursos de produccion archivando un `public/hot`
  obsoleto local, que apuntaba a un servidor Vite apagado. No se versiono ese archivo.
- Actualizaciones compatibles del lock de npm: auditoria sin vulnerabilidades.

### Recorrido comprobado

- Alta y edicion de profesor, curso, grado y aula compartida; disponibilidad
  guardada y recuperada en la primera seleccion; asignacion con aula fija.
- Generacion de cuatro bloques: 07:00-07:45, 07:45-08:30, 08:30-09:00 y
  09:30-10:15. No queda un hueco antes del receso de las 09:00.
- Desactivacion y reactivacion de asignacion; desactivacion del profesor con
  sincronizacion de carga, asignacion y horarios. Reactivacion de asignacion
  rechazada mientras el profesor seguia inactivo; posterior reactivacion correcta.
- Eliminacion de un bloque, registro en historial y restauracion desde la interfaz.
- Cambio de receso de 30 a 45 minutos: el cuarto bloque se reprogramo a
  09:45-10:30. Al volver a 30 minutos, regreso a 09:30-10:15, sin duplicados.
- Consulta publica por profesor, grado y aula: las mismas cuatro clases.
  Vista publica movil de 390 x 844 px, con bloques legibles y navegacion por dia.
- Descargas reales de XLSX, PDF y PNG desde el panel; PDF y PNG desde consulta
  publica por profesor, incluida descarga PNG con ancho movil. Archivos PNG
  inspeccionados visualmente y PDF descargado renderizado para revision.
- XLSX descargado: cuatro bloques y cuatro registros de detalle, sin repetir
  profesor en cada bloque al exportar por profesor; ambas hojas sin panel congelado.

### Resultado final de las comprobaciones

- PHPUnit: **73 pruebas, 361 aserciones**, correctas, SQLite aislado.
- JavaScript: **20 pruebas**, correctas.
- Compilacion Vite correcta; `npm audit` sin vulnerabilidades; diff sin errores.
- Auditoria de solo lectura sobre la base real: **102 horarios activos**,
  **0 cruces**, **0 cruces con recesos**, **0 asignaciones activas sin aula** y
  **0 asignaciones activas de profesores inactivos**.

La prueba en un navegador no certifica todos los navegadores, impresoras ni
datos posibles. El cierre siguiente amplia las verificaciones de esta ronda.

## Cierre de correo, instalacion y seguridad

### Correo y cola

- Se enviaron recuperaciones de contrasena por el transporte SMTP real de Laravel
  a un receptor local limitado a la direccion sintetica de pruebas. Se comprobo
  el mensaje MIME recibido, destinatario, enlace y token guardado.
- Un trabajo de correo se guardo en la cola MySQL aislada y fue procesado por
  `queue:work --once`: el receptor SMTP recibio el correo, el trabajo desaparecio
  de `jobs` y no hubo entradas en `failed_jobs`. Se completaron **9 comprobaciones**,
  repetidas despues de actualizar las dependencias; los cambios de prueba se revirtieron.
- La recuperacion normal sigue siendo sincrona: no necesita worker. Esta prueba
  de un trabajo en cola no certifica un supervisor permanente en un hosting.
- Con autorizacion del usuario se envio un unico mensaje inocuo mediante el
  proveedor SMTP configurado en el `.env` privado. El servidor lo acepto y el
  usuario confirmo su llegada a **Recibidos**. No se publicaron destinatario,
  credenciales, enlaces activos ni mensajes capturados.
- Se agregaron pruebas permanentes para el MIME de recuperacion, el procesamiento
  de cola y la respuesta ante fallo de correo sin exponer datos del transporte.

### Instalacion local con configuracion de produccion

Se extrajo una copia del codigo versionado, sin copiar `vendor`, `node_modules`,
credenciales, datos reales ni `public/hot`. Se instalaron las dependencias con
los archivos de bloqueo y se aplicaron las actualizaciones finales verificadas.

- `composer install --no-dev --prefer-dist --optimize-autoloader`: correcto.
- `npm ci` y `npm run build`: correctos en la copia limpia.
- **27 migraciones** sobre una base MySQL nueva exclusiva de esta comprobacion;
  una segunda ejecucion no tuvo migraciones pendientes.
- `APP_ENV=production`, `APP_DEBUG=false`, clave exclusiva de prueba, cache de
  configuracion, rutas y vistas y enlace de almacenamiento correctos.
- **49 comprobaciones HTTP y de arranque correctas**: acceso y recursos compilados
  de login, registro, recuperacion y consulta publica; cuatro catalogos publicos;
  API privada sin acceso anonimo; registro mediante invitacion; ocho consultas
  autenticadas; logout y rechazo posterior del token; errores sin trazas internas.

El usuario confirmo que no hay hosting todavia. Esto verifica una instalacion
local de produccion, no DNS, HTTPS, permisos, backups, supervisor ni rendimiento
del futuro servidor. Las bases y archivos privados de QA no forman parte de Git.

### Correcciones adicionales

- Se detecto y corrigio un registro no atomico: ahora crear usuario y token se
  realiza en una transaccion. Si falla el token, no queda una cuenta incompleta
  y se puede reintentar el registro. Se agrego una prueba de regresion.
- Los errores inesperados de registro, renovacion y cierre de sesion ya no
  incluyen detalles internos cuando `APP_DEBUG=false`, igual que recuperacion.
- Composer detecto **7 avisos en 5 paquetes**. Se actualizaron solo esos paquetes
  dentro de las versiones compatibles: Laravel **12.70.0**, CommonMark **2.10.4**,
  Flysystem **3.36.0**, HttpFoundation y YAML **7.4.21**. `composer.json` no cambio.
- Las auditorias finales de Composer y npm no reportaron avisos de seguridad.
  Se revisaron los avisos oficiales de
  [Laravel](https://github.com/laravel/framework/security/advisories/GHSA-jh5r-qr3c-85q8)
  y [Symfony YAML](https://symfony.com/cve-2026-109413).

Resultado final repetido con las dependencias actualizadas: **77 pruebas PHP,
390 aserciones**, **20 pruebas JavaScript**, compilacion correcta y sin errores
de espacios en el diff. Los cambios se prepararon en commits separados de
horarios, exportaciones, correo/colas, autenticacion, seguridad y documentacion.
No se modifico ni se publico `main`.

Pendientes ajenos a una prueba automatica local: confirmar academicamente las
cuatro aulas propuestas, revisar Excel en su aplicacion nativa y validar el
despliegue externo cuando exista un hosting. No equivalen a afirmar que el
sistema esta libre de cualquier fallo en cualquier entorno.
