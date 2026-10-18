# Instalacion y entrega

## Requisitos

Windows con Git Bash, XAMPP/MySQL, PHP 8.2 o superior, Composer y Node.js
20.19.x o 22.12 o superior. PHP debe tener las extensiones requeridas por
Composer, ademas de `pdo_mysql` y `pdo_sqlite` para las pruebas.

## Preparar una copia local

En Git Bash, con MySQL iniciado desde XAMPP:

```bash
cd /c/xampp/htdocs/Horarios-NextLevel
export PATH="/c/xampp/php:$PATH"
php -v
composer install
npm ci
test -f .env || cp .env.example .env
```

Editar el `.env` de esa copia con su usuario y clave de MySQL. La plantilla
deja las credenciales vacias. Crear una base local exclusiva para el proyecto
con el nombre de `DB_DATABASE` (por defecto, `horarios_next_level`). No apuntar
a una base compartida para probar la instalacion.

Mantener `APP_URL=http://127.0.0.1:8000`. `FRONTEND_URL="${APP_URL}"` hereda esa
direccion; tambien puede omitirse o quedar vacio para usar `APP_URL`.
`SESSION_DOMAIN=null` permite cookies en el host usado para abrir la app.
Definir un `ADMIN_INVITATION_CODE` propio para registrar administradores en
`/registro`; compartirlo por un canal privado. No hay una clave incluida.

Solo en una copia nueva sin `APP_KEY`, generar la clave:

```bash
php artisan key:generate
```

No regenerarla en una instalacion con datos cifrados. Tras revisar la conexion:

```bash
php artisan config:clear
php artisan migrate
php artisan storage:link
npm run build
php artisan serve --host=127.0.0.1 --port=8000
```

Abrir <http://127.0.0.1:8000>. Si `public/hot` quedo de una sesion anterior,
detener Vite y quitar solo ese archivo para usar el build. Para trabajar con
recarga automatica, ejecutar `npm run dev` en otra terminal y mantener Laravel
encendido. El puerto de Vite no es la direccion de acceso a la aplicacion.

En una copia con datos existentes, conservar `.env` y `APP_KEY`, respaldar la
base antes de migrar y revisar `php artisan migrate:status`. No ejecutar
`migrate:fresh`, `db:wipe` ni los seeders sobre los datos de trabajo. Los
seeders incluyen cuentas de demostracion y no son necesarios para este arranque.

## Correo y colas

Con `MAIL_MAILER=log`, la recuperacion escribe el correo y su enlace en
`storage/logs/laravel.log`; no entrega mensajes a una bandeja real. El mensaje
de exito de la pantalla no confirma una entrega SMTP. Usar una cuenta local
registrada y abrir `/forgot-password` para revisar el flujo. No compartir logs
con enlaces de recuperacion activos.

Para correo real, configurar `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`,
`MAIL_SCHEME`, `MAIL_USERNAME`, `MAIL_PASSWORD` y `MAIL_FROM_ADDRESS` segun el
proveedor, solo en el `.env` privado. Esta configuracion usa `MAIL_SCHEME`:
`smtp` para SMTP con STARTTLS cuando lo ofrece el servidor (habitualmente 587),
o `smtps` para TLS implicito (habitualmente 465). `MAIL_ENCRYPTION` no se lee
en `config/mail.php`. Aplicar los cambios con `php artisan config:clear`.

`ResetPasswordNotification` usa `Queueable`, pero no implementa `ShouldQueue`:
el correo se procesa durante la peticion y no necesita un worker. Para otros
trabajos que si se encolen, `QUEUE_CONNECTION=database` necesita las tablas de
colas creadas por las migraciones y otra terminal con:

```bash
php artisan queue:work --tries=3
```

No iniciar un worker de prueba conectado a la base de otra persona.

## Pruebas sin tocar la base de trabajo

La suite actual usa una base exclusiva SQLite en memoria (`:memory:`),
descartada al terminar. Algunas pruebas crean su propio esquema; no sustituirla
por MySQL ni migrarla manualmente. Este bloque de Git Bash fija la conexion y
evita cargar la cache de configuracion normal, sin editar `.env` o `phpunit.xml`:

```bash
(
    export APP_ENV=testing
    export APP_CONFIG_CACHE=bootstrap/cache/config.testing.php
    export DB_CONNECTION=sqlite DB_DATABASE=:memory: DB_URL=
    export CACHE_STORE=array SESSION_DRIVER=array
    export MAIL_MAILER=array QUEUE_CONNECTION=sync
    if [ -e "$APP_CONFIG_CACHE" ]; then
        echo "Hay una cache de pruebas previa: revisarla antes de continuar."
        exit 1
    fi
    php vendor/bin/phpunit --no-progress --do-not-cache-result
)
npm test
npm run build
```

El bloque PHP corre en una subshell: sus variables no alteran la terminal de
desarrollo. Se espera un resultado `OK` de PHPUnit, pruebas JS sin fallos y un
build sin errores. Esto no comprueba SMTP ni el aspecto visual de las pantallas.

## Entrega a otra persona

La evidencia y los pendientes de esta revision estan en
`docs/REVISION_ENTREGA_2026-10-18.md`. Los cambios de aulas realizados en la
base local no viajan con Git: otra instalacion necesita sus datos autorizados
o completar las aulas manualmente. No ejecutar una reparacion automaticamente
como parte de `migrate`.

- Entregar el codigo y los archivos de bloqueo; instalar con `composer install`
  y `npm ci`. Mantener credenciales, `.env`, logs y tokens fuera de la entrega.
- Indicar si se entrega una base vacia o un respaldo autorizado; registrar
  migraciones pendientes y datos que requieren revision, como `Aula pendiente`.
- Confirmar acceso, login/logout, consulta publica y recuperacion con una cuenta
  de prueba. Revisar que el enlace tenga `/reset-password?token=...&email=...`
  en el host correcto y permita volver a iniciar sesion tras cambiar la clave.
- Registrar los resultados y pendientes de pruebas, revision visual, SMTP y
  despliegue. Una compilacion correcta no confirma esos otros puntos.

En produccion, usar `APP_ENV=production`, `APP_DEBUG=false` y `APP_URL` con el
dominio HTTPS real. Omitir o vaciar `FRONTEND_URL` para heredar ese dominio,
o definir su direccion publica si es distinta. Revisar dominio/cookies y
`SANCTUM_STATEFUL_DOMAINS`, servir desde `public/`, ejecutar `npm run build`
y despues `php artisan config:cache`. Si hay workers activos, reiniciarlos con
`php artisan queue:restart` tras cambiar la configuracion.
