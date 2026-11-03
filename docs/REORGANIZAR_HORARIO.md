# Reorganizar el horario habitual

En Horarios, consultar **Por grado** y pulsar **Reorganizar horario**. Se abre la plantilla habitual, separada de las recuperaciones por fecha. Arrastrar dentro del mismo grado y confirmar el día, las horas y el aula. **Terminar reorganización** desactiva el arrastre.

Mientras se consultan destinos al servidor no se permite guardar. La previsualización muestra verde para un bloque lectivo disponible y rojo para un destino no permitido. Se conserva la duración: un bloque de 45 minutos no cabe en un espacio de 30. Se respetan disponibilidad, recesos y ocupación de profesor, aula y grado, incluyendo aulas y profesores compartidos entre instituciones.

Al confirmar se vuelve a validar con el bloqueo de agenda y una transacción. Si el bloque original cambió, o el destino fue ocupado después de consultarlo, se rechaza sin mover la clase. El cambio se registra en Historial.

La asignación automática pasa a manual conservando su plan completo, y se modifica solamente el bloque elegido. Las asignaciones manuales actualizan su plan. Esto permite conservar la elección al regenerar. Una asignación incompleta debe completarse antes de reorganizarla. No se cambia el aula por arrastre.

Para una recuperación excepcional, usar **Reprogramar clase**; reorganizar modifica la plantilla semanal. El domingo no forma parte de esta plantilla.

Verificación: pruebas aisladas de conservación y regeneración, origen desactualizado, duración incorrecta, receso, falta de disponibilidad, bloques hermanos ocupados y ocupación posterior a la previsualización. Las pruebas JavaScript cubren soltar antes de recibir opciones, rechazo de destino ocupado, respuesta tardía después de cambiar el contexto, fallo de red y cancelación sin enviar cambios.

Prueba visual autenticada del 02/11/2026: se trasladó Comunicación Primaria del lunes 07:00–07:45 al miércoles y se devolvió al lunes; se rechazaron el martes ocupado y un destino dentro del primer receso. Se conservaron los tres bloques, sus aulas y duraciones. La auditoría final no detectó incidencias. Los movimientos de ida y vuelta quedan registrados en Historial. La cancelación se comprobó con prueba automatizada.

Al soltar rápidamente, se espera la consulta pendiente antes de validar y solicitar confirmación; nunca se guarda mientras el destino no esté validado. Cambiar de contexto invalida la respuesta pendiente.
