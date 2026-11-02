# Recesos institucionales fijos

## Fuente confirmada

Se reviso `HORARIO_ACTUAL__0002.xlsx`, unicamente los recesos con relleno
amarillo y horas completas. El usuario descarto expresamente 11:10-11:30;
Secundaria conserva 11:10-11:25. Las celdas vacias no definen descansos.

| Institucion / nivel | Primer receso | Segundo receso |
| --- | --- | --- |
| Colegio / Primaria | 08:30-08:50 | 10:20-10:40 |
| Colegio / Secundaria | 09:15-09:35 | 11:10-11:25 |
| Academia | 08:50-09:10 | 10:40-10:50 |

No hay recesos de tarde confirmados. El turno completo aplica los recesos
de la manana, sin inventar descansos o almuerzo.

## Comportamiento

- La fuente unica es `config/horarios.php`, clave `recesos_fijos`.
- Horarios muestra las reglas en una tabla de solo lectura. La API rechaza
  crear, editar o eliminar configuraciones desde los antiguos endpoints.
- Lectura, vista previa, generacion y validacion usan las mismas reglas,
  incluso si una base antigua conserva otros bloques de configuracion.
  Consultar no modifica la base.
- Una clase puede terminar justo al inicio del receso o empezar justo al
  terminarlo. Nunca puede invadirlo. El generador aprovecha los minutos
  anteriores al descanso y continua la carga pendiente despues, respetando
  disponibilidad y conflictos de profesor, grado y aula.
- Los recesos son por nivel: el de Primaria no bloquea Secundaria. La
  disponibilidad general de Colegio no elimina la union de ambos descansos,
  porque un profesor puede atender los dos niveles. Al asignar un grado se
  descuentan sus recesos concretos. Academia mantiene su disponibilidad propia.
- Las bandas de receso solo se dibujan en la vista por grado, para Primaria,
  Secundaria y Academia. Por profesor, por aula y aula completa se muestran
  solamente las clases, sin mezclar recesos. Ocultarlos no desactiva ninguna
  validacion de disponibilidad ni de conflictos.
- No se rellenan huecos con clases inexistentes ni se aumenta la carga
  asignada solo para completar visualmente la grilla.

## Adaptacion de la base local de demostracion

Se hizo respaldo SQL y se verifico su restauracion y las huellas de sus
21 tablas. Primero se probo el plan en una copia separada de MySQL y luego
se aplico a la base local con transaccion y auditoria.

- Conservados los 102 horarios y las 27 asignaciones.
- Conservados los identificadores, profesor, curso, grado, aula, dia,
  periodo y duracion de cada bloque.
- Detectados 16 bloques que invadian los nuevos recesos; se reubicaron
  46 bloques para acomodarlos sin cruces.
- La disponibilidad de Colegio y los catalogos no se cambiaron.
- La disponibilidad de Academia de cuatro profesores sigue siendo simulada,
  identificada como `PRUEBA SIMULADA`, y ajustada a las clases de prueba.
  No representa disponibilidad confirmada por los docentes.
- Verificador final: 102 horarios, 27 asignaciones, cero incidencias.

Los respaldos y planes de adaptacion estan fuera del repositorio. Estos
datos locales no viajan con Git. No ejecutar seeders, `migrate:fresh` ni
regeneraciones masivas para actualizar otra base con horarios existentes.

## Actualizacion y comprobacion

Este cambio no agrega migraciones ni dependencias. En otra instalacion:

```bash
npm run build
php artisan config:clear
php artisan horarios:verificar
```

Si se usa Vite en desarrollo, `npm run dev` carga las fuentes actuales.
Una base con incidencias necesita respaldo y un plan autorizado de ajuste;
no se deben mover automaticamente horarios reales al instalar codigo.
En produccion, volver a generar la cache de configuracion al terminar.

La suite PHP usa SQLite en memoria y las pruebas JS no escriben datos reales.
El navegador se prueba sobre una copia separada de MySQL, sin enviar correo.

Comprobacion de esta revision: 136 pruebas PHP (650 aserciones), 30 pruebas
JavaScript y compilacion Vite aprobadas. Se revisaron Horarios, Disponibilidad
y Asignaciones en navegador. La tabla fija se comprobo en escritorio y movil
de 390 x 844, sin desbordamiento horizontal. Los errores iniciales de conexion
se resolvieron al encender MySQL; despues no hubo nuevos errores de consola
en estos flujos. No se verifico un despliegue externo.
