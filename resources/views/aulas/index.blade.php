@extends('layouts.app')

@section('title', 'Aulas - Next Level School')
@section('page-title', 'Aulas')

@section('content')

<style>
    /* ============================================ */
    /* ANIMACIONES Y ESTILOS PERSONALIZADOS        */
    /* ============================================ */
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(219, 8, 8, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(219, 8, 8, 0); }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-6px); }
    }

    .animate-slide-up {
        animation: slide-up 0.5s ease-out forwards;
    }

    .animate-float {
        animation: float 4s ease-in-out infinite;
    }

    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 45px rgba(15, 39, 73, 0.14);
    }

    .input-focus-ring {
        transition: all 0.2s ease;
    }
    .input-focus-ring:focus {
        border-color: #1B3A6B;
        box-shadow: 0 0 0 4px rgba(27, 58, 107, 0.12);
    }

    .btn-primary {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(27, 58, 107, 0.35);
    }

    .btn-danger {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(219, 8, 8, 0.30);
    }

    .modal-scroll::-webkit-scrollbar {
        width: 5px;
    }
    .modal-scroll::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }
    .modal-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #1B3A6B, #0F2749);
        border-radius: 10px;
    }

    .table-row-hover {
        transition: all 0.2s ease;
    }
    .table-row-hover:hover {
        background: linear-gradient(90deg, #f8faff, #ffffff);
    }

    .badge-status {
        transition: all 0.2s ease;
    }
    .badge-status:hover {
        transform: scale(1.05);
    }

    /* Borde superior decorativo en tarjetas */
    .card-accent-blue {
        position: relative;
        overflow: hidden;
    }
    .card-accent-blue::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #1B3A6B, #0F2749);
    }

    .card-accent-red {
        position: relative;
        overflow: hidden;
    }
    .card-accent-red::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #db0808, #8d0707);
    }

    .card-accent-emerald {
        position: relative;
        overflow: hidden;
    }
    .card-accent-emerald::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #db0808, #8d0707);
    }

    .card-accent-gray {
        position: relative;
        overflow: hidden;
    }
    .card-accent-gray::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #1B3A6B, #0F2749);
    }
</style>

<div class="space-y-6 animate-slide-up">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #e8edff, #d0d9ff);">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 21V5.8A1.8 1.8 0 0 1 5.8 4h12.4A1.8 1.8 0 0 1 20 5.8V21"/>
                        <path d="M2.5 21h19M8 8h2M14 8h2M8 12h2M14 12h2M8 16h2M14 16h2"/>
                        <path d="M10 21v-3h4v3"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight" style="color: #0F2749;">Aulas</h1>
                    <p class="mt-0.5 text-sm text-slate-400">Administra las aulas disponibles para la programación de horarios.</p>
                </div>
            </div>
        </div>
        <button id="open-aula-modal" type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 btn-primary"
                style="background: linear-gradient(135deg, #1B3A6B, #0F2749);"
                onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 40px rgba(27,58,107,0.35)'"
                onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(27,58,107,0.25)'">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 8v8M8 12h8"/>
            </svg>
            Agregar aula
        </button>
    </div>

    {{-- ESTADÍSTICAS MEJORADAS --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

        <!-- TOTAL -->
        <div class="card-accent-blue card-hover rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
             style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Total de aulas</p>
                    <p id="total-aulas" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #0F2749;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: rgba(219,8,8,.08); color: #8d0707; border: 1px solid rgba(219,8,8,.12);">
                            3%
                        </span>
                        <span class="text-[0.55rem] text-slate-400 font-medium">vs mes anterior</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, #e8edff, #d0d9ff);">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 21V6.5A1.5 1.5 0 0 1 6.5 5h11A1.5 1.5 0 0 1 19 6.5V21"/>
                        <path d="M3 21h18M8 9h2M14 9h2M8 13h2M14 13h2M8 17h2M14 17h2"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1">
                <div class="flex gap-0.5">
                    <span class="w-2 h-1 rounded-full" style="background: #1B3A6B; opacity: 0.3;"></span>
                    <span class="w-2 h-1.5 rounded-full" style="background: #1B3A6B; opacity: 0.5;"></span>
                    <span class="w-2 h-2 rounded-full" style="background: #1B3A6B; opacity: 0.7;"></span>
                    <span class="w-2 h-3 rounded-full" style="background: #1B3A6B; opacity: 0.9;"></span>
                    <span class="w-2 h-2.5 rounded-full" style="background: #1B3A6B;"></span>
                </div>
                <span class="text-[0.45rem] text-slate-300 font-medium ml-1">crecimiento</span>
            </div>
        </div>

        <!-- ACTIVAS -->
        <div class="card-accent-emerald card-hover rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
             style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Aulas activas</p>
                    <p id="aulas-activas" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #db0808;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: rgba(219,8,8,.08); color: #8d0707; border: 1px solid rgba(219,8,8,.12);">
                            Disponibles
                        </span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, rgba(219,8,8,.10), rgba(141,7,7,.06));">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#db0808" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M12 3 19 6v5c0 4.7-2.8 8-7 10-4.2-2-7-5.3-7-10V6l7-3Z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background: #db0808;"></span>
                <span class="text-[0.55rem] text-slate-400 font-medium">En uso</span>
            </div>
        </div>

        <!-- INACTIVAS -->
        <div class="card-accent-gray card-hover rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
             style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Aulas inactivas</p>
                    <p id="aulas-inactivas" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #1B3A6B;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: rgba(27,58,107,.07); color: #1B3A6B; border: 1px solid rgba(27,58,107,.10);">
                            Sin actividad
                        </span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, rgba(27,58,107,.10), rgba(15,39,73,.05));">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M10 9v6M14 9v6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" style="background: #1B3A6B;"></span>
                <span class="text-[0.55rem] text-slate-400 font-medium">Archivadas</span>
            </div>
        </div>

    </div>

    {{-- FILTROS MEJORADOS --}}
    <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition-all duration-300"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.06); background: linear-gradient(180deg, #fafbff, #ffffff);">
        <div class="flex flex-col gap-3 md:flex-row">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2">
                    <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <input id="buscar-aula" type="text" placeholder="Buscar aula por nombre o código..."
                       class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-4 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                       style="background: #ffffff;"
                       onfocus="this.style.borderColor='#1B3A6B'; this.style.boxShadow='0 0 0 4px rgba(27,58,107,0.08)'"
                       onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
            </div>

            <select id="filtro-aula-estado"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                    onfocus="this.style.borderColor='#1B3A6B'"
                    onblur="this.style.borderColor='#e2e8f0'">
                <option value="">Todos los estados</option>
                <option value="1">Activas</option>
                <option value="0">Inactivas</option>
            </select>

            <select id="filtro-aula-tipo"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                    onfocus="this.style.borderColor='#1B3A6B'"
                    onblur="this.style.borderColor='#e2e8f0'">
                <option value="">Todos los tipos</option>
                <option value="aula_normal">Aula normal</option>
                <option value="taller">Taller</option>
                <option value="auditorio">Auditorio</option>
                <option value="virtual">Virtual</option>
            </select>

            <select id="filtro-aula-nivel"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                    onfocus="this.style.borderColor='#1B3A6B'"
                    onblur="this.style.borderColor='#e2e8f0'">
                <option value="">Todos los niveles</option>
                <option value="primaria">Primaria</option>
                <option value="secundaria">Secundaria</option>
                <option value="academia">Academia</option>
                <option value="todos">Todos</option>
            </select>
        </div>
    </div>

    {{-- TABLA MEJORADA --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-all duration-300"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);"
         onmouseenter="this.style.boxShadow='0 12px 50px rgba(27,58,107,0.10)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.06)'">

        <div class="flex items-center justify-between border-b border-slate-200/80 px-6 py-4"
             style="background: linear-gradient(90deg, #fafbff, #ffffff);">
            <div>
                <h2 class="font-bold" style="color: #0F2749;">Lista de aulas</h2>
                <p class="mt-0.5 text-xs text-slate-400">Mostrando <span id="resultados-aulas" class="font-semibold" style="color: #1B3A6B;">0</span> aulas</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" style="background: #db0808;"></span>
                <span class="text-[0.55rem] text-slate-400 font-medium">Sistema en línea</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr style="background: linear-gradient(90deg, #f8faff, #f1f5f9);">
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Aula</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Código</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Capacidad</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Tipo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Nivel</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/80">
                    <tr id="aulas-empty" class="hidden">
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full"
                                     style="background: linear-gradient(135deg, #f1f5f9, #e2e8f0);">
                                    <svg class="w-10 h-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-base font-bold" style="color: #0F2749;">No se encontraron aulas</h3>
                                <p class="mt-1 max-w-sm text-sm text-slate-400">Intenta cambiar tu búsqueda o filtro.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- PIE DE TABLA --}}
        <div class="flex items-center justify-between border-t border-slate-200/80 px-6 py-4"
             style="background: #fafbfc;">
            <p class="text-sm text-slate-500">
                Mostrando <span id="resultados-aulas-footer" class="font-bold" style="color: #0F2749;">0</span>
                de <span id="total-resultados-aulas" class="font-bold" style="color: #0F2749;">0</span> aulas
            </p>
            <div class="flex items-center gap-2">
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-50 hover:border-slate-300">Anterior</button>
                <button class="rounded-lg px-4 py-1.5 text-sm font-semibold text-white"
                        style="background: linear-gradient(135deg, #1B3A6B, #0F2749);">1</button>
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-50">2</button>
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-50">3</button>
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-50 hover:border-slate-300">Siguiente</button>
            </div>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL AGREGAR / EDITAR AULA (MEJORADO)    --}}
{{-- ========================================== --}}
<div id="aula-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div id="aula-modal-overlay" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl modal-scroll"
             style="box-shadow: 0 30px 80px rgba(27,58,107,0.2);">

            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200/80 px-6 py-5 rounded-t-2xl"
                 style="background: linear-gradient(90deg, #fafbff, #ffffff);">
                <div>
                    <h2 id="aula-modal-title" class="text-xl font-extrabold" style="color: #0F2749;">Agregar aula</h2>
                    <p class="mt-0.5 text-sm text-slate-400">Completa la información del aula.</p>
                </div>
                <button id="close-aula-modal" type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-200 hover:bg-slate-100 hover:text-slate-700">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="aula-form">
                <div class="space-y-5 px-6 py-6">

                    {{-- CÓDIGO --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input id="aula-codigo" type="text" placeholder="Ej. AUL-001" maxlength="20"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm uppercase outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                               onfocus="this.style.borderColor='#1B3A6B'"
                               onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    {{-- NOMBRE --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                            Nombre del aula <span class="text-red-500">*</span>
                        </label>
                        <input id="aula-nombre" type="text" placeholder="Ej. Aula 301" maxlength="50"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                               onfocus="this.style.borderColor='#1B3A6B'"
                               onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    {{-- CAPACIDAD Y TIPO --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Capacidad <span class="text-red-500">*</span>
                            </label>
                            <input id="aula-capacidad" type="number" min="1" placeholder="Ej. 30"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Tipo de aula <span class="text-red-500">*</span>
                            </label>
                            <select id="aula-tipo"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                    onfocus="this.style.borderColor='#1B3A6B'"
                                    onblur="this.style.borderColor='#e2e8f0'">
                                <option value="">Seleccionar tipo</option>
                                <option value="aula_normal">Aula normal</option>
                                <option value="taller">Taller</option>
                                <option value="auditorio">Auditorio</option>
                                <option value="virtual">Virtual</option>
                            </select>
                        </div>
                    </div>

                    {{-- NIVEL Y EQUIPAMIENTO --}}
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Nivel</label>
                            <select id="aula-nivel"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                    onfocus="this.style.borderColor='#1B3A6B'"
                                    onblur="this.style.borderColor='#e2e8f0'">
                                <option value="todos">Todos</option>
                                <option value="primaria">Primaria</option>
                                <option value="secundaria">Secundaria</option>
                                <option value="academia">Academia</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Equipamiento</label>
                            <input id="aula-equipamiento" type="text" placeholder="Ej. Proyector, Pizarra digital..."
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    {{-- ESTADO --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Estado</label>
                        <select id="aula-estado"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                onfocus="this.style.borderColor='#1B3A6B'"
                                onblur="this.style.borderColor='#e2e8f0'">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>

                    {{-- OBSERVACIONES --}}
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Observaciones</label>
                        <textarea id="aula-observaciones" rows="2" placeholder="Notas adicionales..."
                                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                  onfocus="this.style.borderColor='#1B3A6B'"
                                  onblur="this.style.borderColor='#e2e8f0'"></textarea>
                    </div>
                </div>

                <div class="sticky bottom-0 z-10 flex flex-col-reverse gap-3 border-t border-slate-200/80 px-6 py-4 rounded-b-2xl"
                     style="background: linear-gradient(0deg, #ffffff, #fafbfc);">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button id="cancel-aula-modal" type="button"
                                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300">
                            Cancelar
                        </button>
                        <button id="aula-submit-button" type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 btn-primary"
                                style="background: linear-gradient(135deg, #1B3A6B, #0F2749);">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 4h11l3 3v13H5z"/>
                                <path d="M8 4v6h8V4M8 20v-6h8v6"/>
                            </svg>
                            Guardar aula
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ========================================== --}}
{{-- MODAL ELIMINAR (MEJORADO)                 --}}
{{-- ========================================== --}}
<div id="delete-aula-modal" class="fixed inset-0 z-[110] hidden" aria-hidden="true">
    <div id="delete-aula-overlay" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl"
             style="box-shadow: 0 30px 80px rgba(27,58,107,0.2);">
            <div class="flex items-start justify-between px-6 py-5">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full"
                         style="background: linear-gradient(135deg, #fecaca, #fca5a5);">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            <path d="M10 11v6M14 11v6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-extrabold" style="color: #0F2749;">Eliminar aula</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            ¿Estás seguro de que deseas eliminar <span id="delete-aula-name" class="font-bold" style="color: #0F2749;"></span>?
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
                <button id="cancel-delete-aula" type="button"
                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-slate-200/80 px-6 py-4 sm:flex-row sm:justify-end">
                <button id="cancel-delete-aula" type="button"
                        class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300">
                    Cancelar
                </button>
                <button id="confirm-delete-aula" type="button"
                        class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 btn-danger"
                        style="background: linear-gradient(135deg, #db0808, #8d0707);">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                        <path d="M10 10v6M14 10v6"/>
                    </svg>
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection