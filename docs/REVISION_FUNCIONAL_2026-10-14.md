# Revision funcional del 14 de octubre de 2026

## Resultado

Se corrigieron errores confirmados de backend y frontend y se ampliaron las pruebas.
Los flujos cubiertos pasan. Esto no equivale a garantizar ausencia de cualquier bug.
Las operaciones de prueba se hicieron en SQLite en memoria; la base real solo se consulto.

## Correcciones

- Profesores: desactivar sincroniza asignaciones, horarios, carga horaria e historial.
- Asignaciones: crear, editar y activar se revierten si falla la generacion del horario.
- Reactivacion: valida profesor, institucion, curso, grado, aula y configuracion.
- Generador: excluye entidades inactivas, respeta el periodo solicitado y evita usar
  configuraciones duplicadas antiguas para un mismo turno.
- Horarios: validan el aula seleccionada y los cruces dentro del mismo periodo.
- Disponibilidad: el reemplazo completo se guarda mediante una sola operacion atomica.
  Si una franja falla o deja clases sin cobertura, se conserva el estado anterior.
- Rangos contiguos de disponibilidad pueden cubrir una clase que atraviesa la hora.
- Recesos: la actualizacion y reprogramacion son atomicas; una regeneracion fallida
  o incompleta conserva la configuracion y los horarios anteriores.
- Configuracion: valida continuidad y cobertura de la jornada y bloquea su eliminacion
  mientras tenga asignaciones activas. No decide aulas por frecuencia de uso.
- Historial: restaura horarios eliminados desde sus datos guardados, validando
  disponibilidad, aula, asignacion, recesos y conflictos actuales.
- Cursos, grados y aulas: se bloquean cambios incompatibles con asignaciones activas.
- Frontend: se descartan respuestas desactualizadas y se informa la generacion parcial.
- Exportaciones: conservan los filtros visibles; el PDF almacenado usa el disco publico.
- Asignaciones antiguas sin aula: se muestran como Aula pendiente, sin elegir una al azar.

## Verificacion ejecutada

- PHPUnit: 34 pruebas, 145 aserciones, todas correctas.
- JavaScript: 15 pruebas de estado y respuestas asincronas, todas correctas.
- Vite: compilacion de produccion correcta.
- Sintaxis: 84 archivos PHP y 14 archivos JavaScript comprobados.
- Se renderizaron las 14 rutas principales de vistas mediante pruebas HTTP.
- Se probaron inicio de sesion con token, acceso protegido y cierre de sesion.
- Se generaron archivos Excel y PDF de prueba y HTML para imagen.
- Excel: contiene clases, omite el profesor repetido en su propio horario y no congela paneles.
- Se probaron aulas compartidas sin doble reserva, limites de recesos y generacion repetida.

Comandos para repetir:

```bash
vendor/bin/phpunit --no-progress
npm test
npm run build
```

## Datos reales consultados

- Conexion MySQL operativa y todas las migraciones aplicadas.
- 102 horarios activos.
- 0 cruces entre horarios activos del mismo periodo que comparten profesor, aula o grado.
- 0 asignaciones activas de profesores inactivos.
- 24 asignaciones activas antiguas sin aula seleccionada.

Es necesario abrir las asignaciones marcadas Aula pendiente y seleccionar su aula real.
No se modificaron esas elecciones ni se eliminaron asignaciones reales durante la revision.

## Limites

- No hubo un navegador conectado para comprobar visualmente todas las pantallas ni
  la imagen PNG final. La verificacion de imagen cubre su HTML, no los pixeles de captura.
- No se enviaron correos reales: la entrega SMTP y las colas en produccion quedan pendientes.
- La prueba automatizada usa un esquema SQLite aislado; la instalacion completa mediante
  migraciones MySQL nuevas y el despliegue del servidor no se reprodujeron.
- Los cambios siguen locales, sin commit ni push realizados por esta revision.
