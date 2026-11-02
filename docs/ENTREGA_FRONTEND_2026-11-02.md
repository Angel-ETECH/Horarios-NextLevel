# Entrega de frontend — 02/11/2026

No se integró ni publicó nada en main. Su referencia revisada es 4c57a03
(BackEnd completado); su cambio propio afecta .env.example.

## Funciones entregadas

- Asignación automática o manual de bloques, con aula elegida explícitamente.
- Ampliación de disponibilidad y bloqueo de reducciones que afecten clases.
- Recesos institucionales fijos por nivel; visibles en consulta por grado.
- Recuperaciones por fecha sin reemplazar la plantilla semanal.
- Reorganización por arrastre con validación de profesor, aula, grado,
  disponibilidad, duración y recesos; espera segura al soltar rápidamente.
- Exportaciones y consulta pública conectadas a los filtros y calendario.
- Auditoría de coherencia con horarios:verificar y protección de errores API.

## Base de datos

Esta entrega añade recuperaciones_horario y las columnas modo_programacion
y bloques_manuales en profesor_curso. Desde main también existen migraciones
anteriores pendientes (aulas, historial y configuración de bloques).
migrate ejecuta únicamente las pendientes; algunas antiguas ajustan datos,
por lo que es indispensable respaldar antes y probar primero sobre una copia.
La limpieza y regularización de la base local no se transmiten con Git.
No importar automáticamente el respaldo SQL histórico del repositorio.

## Integración a cargo del compañero (Git Bash)

Con sus cambios guardados, crear una rama de integración desde main:

```bash
git fetch origin
git switch main
git pull --ff-only origin main
git switch -c integrar-frontend-20261102
git merge --no-ff origin/frontend
```

La simulación detectó conflicto en .env.example: combinar las variables
necesarias de ambas ramas y conservar valores de ejemplo, nunca credenciales.
Revisar cualquier otro conflicto señalado por Git; después:

```bash
git add .env.example
git commit
composer install
npm ci
php artisan optimize:clear
php artisan migrate:status
# Antes del siguiente comando: respaldo de su base y MySQL encendido.
php artisan migrate
npm run build
php artisan horarios:verificar
php artisan test
npm test
```

No ejecutar migrate:fresh ni db:seed sobre datos existentes. Si la auditoría
reporta incidencias, revisarlas antes de regenerar o aprobar la entrega.
Mantener el .env propio y APP_KEY existente. Reiniciar trabajadores de colas
si están ejecutándose. Probar login, asignación, disponibilidad, arrastre,
recuperaciones y exportaciones en navegador.

Una vez aprobada la rama de integración, el encargado puede hacer su PR a
main o integrar y publicar él mismo. No se ha ejecutado esa integración aquí.

## Evidencia local

Arrastre probado con sesión: traslado de lunes a miércoles y regreso al
lunes; rechazo de martes ocupado y receso. Tres bloques y dos asignaciones
con auditoría sin incidencias. JavaScript: 43 pruebas; pruebas específicas
de asignación: 25 con 144 aserciones; compilación correcta.
