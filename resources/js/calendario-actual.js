export function fechaActualHorario(ahora = new Date()) {
    return ahora.toLocaleDateString('sv-SE', { timeZone: 'America/Lima' });
}
