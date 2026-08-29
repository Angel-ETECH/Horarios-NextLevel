@extends('layouts.app')

@section('title', 'Asignaciones - Next Level School')
@section('page-title', 'Asignaciones')

@section('content')

<style>
    :root{
        --rojo-principal:#db0808;
        --rojo-oscuro:#8d0707;
        --azul-noche:#1B3A6B;
        --azul-oscuro:#0F2749;
        --blanco:#FFFFFF;
    }

    @keyframes asignaciones-slide-up{
        from{opacity:0;transform:translateY(14px)}
        to{opacity:1;transform:translateY(0)}
    }

    .asignaciones-page{
        animation:asignaciones-slide-up .45s ease-out both;
    }

    .asignaciones-card{
        border:1px solid rgba(27,58,107,.10);
        box-shadow:0 8px 28px rgba(15,39,73,.06);
    }

    .asignaciones-input{
        transition:border-color .2s ease,box-shadow .2s ease,background .2s ease;
    }

    .asignaciones-input:focus{
        border-color:var(--azul-noche)!important;
        box-shadow:0 0 0 4px rgba(27,58,107,.09)!important;
    }

    .asignaciones-icon{
        box-shadow:inset 0 0 0 1px rgba(27,58,107,.08);
    }

    .asignaciones-primary{
        transition:transform .2s ease,box-shadow .2s ease;
    }

    .asignaciones-primary:hover{
        transform:translateY(-2px);
        box-shadow:0 12px 28px rgba(219,8,8,.22);
    }

    .asignaciones-secondary{
        transition:transform .2s ease,border-color .2s ease,background .2s ease;
    }

    .asignaciones-secondary:hover{
        transform:translateY(-1px);
        border-color:rgba(27,58,107,.25);
        background:#f8fafc;
    }

    .asignaciones-table-row{
        transition:background .18s ease;
    }

    .asignaciones-table-row:hover{
        background:linear-gradient(90deg,rgba(27,58,107,.025),rgba(219,8,8,.015));
    }
</style>

<div class="space-y-6 asignaciones-page">

    {{-- HEADER --}}
    <div class="flex items-start gap-3">
        <div class="asignaciones-icon flex h-12 w-12 items-center justify-center rounded-2xl"
             style="background:linear-gradient(145deg,rgba(27,58,107,.12),rgba(219,8,8,.06));">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M9 6h11M9 12h11M9 18h11"/>
                <circle cx="4.5" cy="6" r="1.5"/>
                <circle cx="4.5" cy="12" r="1.5"/>
                <circle cx="4.5" cy="18" r="1.5"/>
            </svg>
        </div>

        <div>
            <h1 class="text-2xl font-extrabold tracking-tight" style="color:#0F2749;">
                Asignaciones
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Asigna manualmente una clase respetando la disponibilidad del profesor.
            </p>
        </div>
    </div>


    {{-- INFO --}}
    <div class="rounded-2xl border border-blue-200 bg-blue-50 p-4">

        <div class="flex gap-3">

            <div class="text-xl">
                ℹ️
            </div>

            <div>

                <p class="text-sm font-semibold text-slate-800">
                    Asignación manual validada
                </p>

                <p class="mt-1 text-xs leading-5 text-slate-600">
                    Aquí decides el día y la hora real de la clase.
                    El sistema solamente permite guardar horarios que estén dentro
                    de la disponibilidad registrada del profesor y que no generen conflictos.
                </p>

            </div>

        </div>

    </div>


    {{-- FORMULARIO --}}
    <div class="asignaciones-card overflow-hidden rounded-2xl bg-white">

        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4"
             style="background:linear-gradient(90deg,rgba(27,58,107,.035),#fff);">

            <div class="flex h-10 w-10 items-center justify-center rounded-xl"
                 style="background:rgba(27,58,107,.08);">

                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 8v8M8 12h8"/>
                </svg>

            </div>

            <div>

                <h2 class="font-bold" style="color:#0F2749;">
                    Nueva asignación
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Elige profesor, curso, aula, día y horario.
                </p>

            </div>

        </div>


        <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2 lg:grid-cols-4">

            {{-- PROFESOR --}}
            <div>

                <label
                    for="asignacion-profesor"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Profesor
                    <span style="color:#db0808;">*</span>
                </label>

                <select
                    id="asignacion-profesor"
                    class="asignaciones-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none"
                >
                    <option value="">Seleccionar profesor</option>
                </select>

            </div>


            {{-- INSTITUCIÓN --}}
            <div>

                <label
                    for="asignacion-institucion"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Institución
                    <span style="color:#db0808;">*</span>
                </label>

                <select
                    id="asignacion-institucion"
                    class="asignaciones-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none"
                >
                    <option value="">Seleccionar institución</option>
                    <option value="colegio">Colegio</option>
                    <option value="academia">Academia</option>
                </select>

            </div>


            {{-- CURSO --}}
            <div>

                <label
                    for="asignacion-curso"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Curso / Carrera
                    <span style="color:#db0808;">*</span>
                </label>

                <select
                    id="asignacion-curso"
                    class="asignaciones-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none"
                >
                    <option value="">Seleccionar curso</option>
                </select>

            </div>


            {{-- GRADO --}}
            <div id="grado-container">

                <label
                    for="asignacion-grado"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Grado / Sección
                </label>

                <select
                    id="asignacion-grado"
                    class="asignaciones-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none"
                >
                    <option value="">Seleccionar grado</option>
                </select>

                <p
                    id="grado-help"
                    class="mt-1 text-xs text-slate-500"
                >
                    Selecciona el grado y sección del colegio.
                </p>

            </div>


            {{-- AULA --}}
            <div>

                <label
                    for="asignacion-aula"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Aula
                    <span style="color:#db0808;">*</span>
                </label>

                <select
                    id="asignacion-aula"
                    class="asignaciones-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none"
                >
                    <option value="">Seleccionar aula</option>
                </select>

            </div>


            {{-- DÍA --}}
            <div>

                <label
                    for="asignacion-dia"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Día
                    <span style="color:#db0808;">*</span>
                </label>

                <select
                    id="asignacion-dia"
                    class="asignaciones-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none"
                    disabled
                >
                    <option value="">Selecciona primero un profesor</option>
                </select>

                <p
                    id="asignacion-dia-help"
                    class="mt-1 text-xs text-slate-500"
                >
                    Solo aparecerán los días disponibles del profesor.
                </p>

            </div>


            {{-- HORA INICIO --}}
            <div>

                <label
                    for="asignacion-hora-inicio"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Hora inicio
                    <span style="color:#db0808;">*</span>
                </label>

                <input
                    id="asignacion-hora-inicio"
                    type="time"
                    step="60"
                    disabled
                    class="asignaciones-input w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none disabled:bg-slate-100 disabled:text-slate-400"
                >

            </div>


            {{-- HORA FIN --}}
            <div>

                <label
                    for="asignacion-hora-fin"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Hora fin
                    <span style="color:#db0808;">*</span>
                </label>

                <input
                    id="asignacion-hora-fin"
                    type="time"
                    step="60"
                    disabled
                    class="asignaciones-input w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none disabled:bg-slate-100 disabled:text-slate-400"
                >

            </div>

        </div>


        {{-- DISPONIBILIDAD DEL PROFESOR --}}
        <div class="border-t border-slate-200 px-5 py-5">

            <div class="mb-3">

                <h3 class="text-sm font-bold" style="color:#0F2749;">
                    Disponibilidad del profesor
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Estos son los rangos registrados en Disponibilidad.
                </p>

            </div>


            <div
                id="resumen-disponibilidad-profesor"
                class="flex flex-wrap gap-2"
            >

                <span class="text-xs text-slate-400">
                    Selecciona profesor e institución para consultar su disponibilidad.
                </span>

            </div>


            <div
                id="estado-disponibilidad-asignacion"
                class="mt-4 hidden rounded-xl border p-3 text-sm"
            >
            </div>

        </div>


        {{-- ESTADO --}}
        <div
            class="border-t border-slate-200 px-5 py-5"
            style="background:rgba(248,250,252,.55);"
        >

            <div class="max-w-sm">

                <label
                    for="asignacion-estado"
                    class="mb-2 block text-sm font-semibold"
                    style="color:#0F2749;"
                >
                    Estado
                </label>

                <select
                    id="asignacion-estado"
                    class="asignaciones-input w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none"
                >
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>

            </div>

        </div>


        {{-- BOTONES --}}
        <div
            class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:justify-end"
            style="background:linear-gradient(0deg,#fff,#fafbfc);"
        >

            <button
                id="limpiar-asignacion"
                type="button"
                class="asignaciones-secondary inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600"
            >
                Limpiar
            </button>


            <button
                id="guardar-asignacion"
                type="button"
                class="asignaciones-primary inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg"
                style="background:linear-gradient(135deg,#db0808,#8d0707); box-shadow:0 8px 22px rgba(219,8,8,.18);"
            >
                Crear asignación
            </button>

        </div>

    </div>


    {{-- LISTADO --}}
    <div class="asignaciones-card overflow-hidden rounded-2xl bg-white">

        <div
            class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 md:flex-row md:items-center md:justify-between"
            style="background:linear-gradient(90deg,rgba(27,58,107,.03),#fff);"
        >

            <div>

                <h2 class="font-bold" style="color:#0F2749;">
                    Asignaciones actuales
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Clases ya ubicadas en el horario.
                </p>

            </div>


            <input
                id="buscar-asignacion"
                type="text"
                placeholder="Buscar..."
                class="asignaciones-input w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none md:w-64"
            >

        </div>


        <div class="overflow-x-auto">

            <table class="w-full min-w-[1100px] text-left">

                <thead>

                    <tr
                        class="border-b border-slate-200"
                        style="background:linear-gradient(90deg,#f8fafc,#f3f6fa);"
                    >

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#1B3A6B;">
                            Profesor
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#1B3A6B;">
                            Curso
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#1B3A6B;">
                            Grado
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#1B3A6B;">
                            Aula
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#1B3A6B;">
                            Día
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#1B3A6B;">
                            Horario
                        </th>

                        <th class="px-5 py-3 text-xs font-bold uppercase tracking-wider" style="color:#1B3A6B;">
                            Estado
                        </th>

                        <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wider" style="color:#1B3A6B;">
                            Acciones
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="asignaciones-body"
                    class="divide-y divide-slate-100"
                >
                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection