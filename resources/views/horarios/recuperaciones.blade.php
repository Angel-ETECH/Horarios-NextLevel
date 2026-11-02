<section class="border-y border-slate-200 bg-white px-5 py-4" aria-label="Calendario de clases">
    <div class="flex flex-wrap items-end gap-3">
        <input id="horario-semana" type="hidden" value="{{ now()->format('Y-m-d') }}">
        <span id="semana-horario-label" class="text-sm font-semibold text-[#0F2749]" role="status">Semana actual</span>
        <button id="horario-habitual" type="button" class="ml-auto rounded-md border border-slate-300 px-3 py-2 text-sm">Editar horario base</button>
    </div>
    <p id="recuperaciones-status" class="mt-2 text-sm text-emerald-700" role="status"></p>
    <div id="recuperaciones-lista" class="mt-4 overflow-x-auto"></div>
</section>

<dialog id="recuperacion-dialog" class="m-auto max-h-[90vh] w-[calc(100%_-_2rem)] max-w-2xl overflow-y-auto rounded-lg border border-slate-200 p-0 text-[#0F2749] backdrop:bg-black/40">
    <form id="recuperacion-form" class="space-y-4 p-5">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold">Reprogramar clase</h2>
            <button type="button" id="recuperacion-cerrar" aria-label="Cerrar" class="h-8 w-8 rounded-md border border-slate-200 text-xl">&times;</button>
        </div>
        <label class="block text-sm font-semibold">Clase
            <select id="recuperacion-clase" required class="mt-1 w-full rounded-md border border-slate-300 p-2"></select>
        </label>
        <div class="grid gap-3 sm:grid-cols-2">
            <label class="text-sm font-semibold">Fecha original
                <input id="recuperacion-original" type="date" required class="mt-1 w-full rounded-md border border-slate-300 p-2">
            </label>
            <label class="text-sm font-semibold">Fecha de recuperaci&oacute;n
                <input id="recuperacion-fecha" type="date" required min="{{ now()->format('Y-m-d') }}" class="mt-1 w-full rounded-md border border-slate-300 p-2">
            </label>
        </div>
        <label class="block text-sm font-semibold">Aula
            <select id="recuperacion-aula" required class="mt-1 w-full rounded-md border border-slate-300 p-2"></select>
        </label>
        <div class="grid gap-3 sm:grid-cols-2">
            <label class="text-sm font-semibold">Buscar desde
                <input id="recuperacion-desde" type="time" value="14:00" min="07:00" max="20:00" required class="mt-1 w-full rounded-md border border-slate-300 p-2">
            </label>
            <label class="text-sm font-semibold">Buscar hasta
                <input id="recuperacion-hasta" type="time" value="18:00" min="07:00" max="20:00" required class="mt-1 w-full rounded-md border border-slate-300 p-2">
            </label>
        </div>
        <label class="flex items-start gap-2 text-sm"><input id="recuperacion-excepcional" type="checkbox" class="mt-1">Disponibilidad excepcional confirmada por el profesor para esta fecha y rango.</label>
        <label class="flex items-start gap-2 text-sm"><input id="recuperacion-jornada" type="checkbox" class="mt-1">Recuperaci&oacute;n autorizada fuera de la jornada habitual o en domingo; grupo y aula confirmados.</label>
        <label class="block text-sm font-semibold">Motivo
            <textarea id="recuperacion-motivo" required minlength="5" maxlength="255" rows="2" class="mt-1 w-full rounded-md border border-slate-300 p-2"></textarea>
        </label>
        <div id="recuperacion-error" role="alert" class="hidden rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></div>
        <button type="button" id="recuperacion-buscar" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold">Buscar horarios disponibles</button>
        <fieldset id="recuperacion-opciones" class="max-h-48 overflow-y-auto"></fieldset>
        <div class="flex justify-end gap-2 border-t border-slate-200 pt-4">
            <button type="button" id="recuperacion-volver" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Volver</button>
            <button id="recuperacion-guardar" type="submit" disabled class="rounded-md bg-[#0F2749] px-4 py-2 text-sm font-semibold text-white disabled:opacity-40">Confirmar recuperaci&oacute;n</button>
        </div>
    </form>
</dialog>

<dialog id="recuperacion-cancelar-dialog" class="m-auto max-h-[90vh] w-[calc(100%_-_2rem)] max-w-lg overflow-y-auto rounded-lg border border-slate-200 p-5 text-[#0F2749] backdrop:bg-black/40">
    <form id="recuperacion-cancelar-form" class="space-y-4">
        <h2 class="text-lg font-bold">Cancelar recuperaci&oacute;n</h2>
        <p id="recuperacion-cancelar-clase" class="text-sm text-slate-600"></p>
        <label class="block text-sm font-semibold">Motivo de cancelaci&oacute;n
            <textarea id="recuperacion-cancelar-motivo" required minlength="5" maxlength="255" rows="3" class="mt-1 w-full rounded-md border border-slate-300 p-2"></textarea>
        </label>
        <div id="recuperacion-cancelar-error" role="alert" class="hidden rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700"></div>
        <div class="flex flex-wrap justify-end gap-2 border-t border-slate-200 pt-4">
            <button type="button" id="recuperacion-cancelar-volver" class="rounded-md border border-slate-300 px-4 py-2 text-sm">Volver</button>
            <button id="recuperacion-cancelar-confirmar" type="submit" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white disabled:opacity-40">Confirmar cancelaci&oacute;n</button>
        </div>
    </form>
</dialog>
