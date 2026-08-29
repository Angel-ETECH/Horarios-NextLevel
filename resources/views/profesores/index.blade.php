@extends('layouts.app')

@section('title', 'Profesores - Next Level School')
@section('page-title', 'Profesores')

@section('content')

<style>
    /* ============================================ */
    /* ANIMACIONES Y MEJORAS VISUALES              */
    /* ============================================ */
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(219, 8, 8, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(219, 8, 8, 0); }
    }

    .animate-slide-up {
        animation: slide-up 0.5s ease-out forwards;
    }

    .animate-pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite;
    }

    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 60px rgba(27, 58, 107, 0.12);
    }

    .table-row-hover {
        transition: all 0.2s ease;
    }

    .table-row-hover:hover {
        background: linear-gradient(90deg, #f8faff, #ffffff);
        transform: scale(1.002);
    }

    /* Scrollbar personalizada para modales */
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

    .input-focus-ring {
        transition: all 0.2s ease;
    }
    .input-focus-ring:focus {
        border-color: #1B3A6B;
        box-shadow: 0 0 0 4px rgba(27, 58, 107, 0.12);
    }

    .badge-status {
        transition: all 0.2s ease;
    }
    .badge-status:hover {
        transform: scale(1.05);
    }
</style>

<div class="space-y-6 animate-slide-up">

    <!-- ============================================ -->
    <!-- ESTADÍSTICAS MEJORADAS                       -->
    <!-- ============================================ -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

        <!-- TOTAL -->
        <div class="card-hover rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
             style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Total Profesores</p>
                    <p id="total-profesores" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #0F2749;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46;">
                            <span>↑</span> 12%
                        </span>
                        <span class="text-[0.55rem] text-slate-400 font-medium">vs mes anterior</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, #e8edff, #d0d9ff);">
                    <!-- SVG User -->
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/>
                        <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/>
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

        <!-- ACTIVOS -->
        <div class="card-hover rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
             style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Profesores Activos</p>
                    <p id="profesores-activos" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #059669;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46;">
                            <span>●</span> Disponibles
                        </span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, #d1fae5, #a7f3d0);">
                    <!-- SVG Check -->
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17L4 12"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background: #10b981;"></span>
                <span class="text-[0.55rem] text-slate-400 font-medium">Todos activos</span>
            </div>
        </div>

        <!-- INACTIVOS -->
        <div class="card-hover rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
             style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Profesores Inactivos</p>
                    <p id="profesores-inactivos" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #64748b;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: #f3f4f6; color: #6b7280;">
                            <span>○</span> Sin actividad
                        </span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, #f1f5f9, #e2e8f0);">
                    <!-- SVG Circle -->
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" style="background: #94a3b8;"></span>
                <span class="text-[0.55rem] text-slate-400 font-medium">Necesitan atención</span>
            </div>
        </div>

    </div>

    <!-- ============================================ -->
    <!-- ENCABEZADO MEJORADO                          -->
    <!-- ============================================ -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #e8edff, #d0d9ff);">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight" style="color: #0F2749;">Profesores</h1>
                    <p class="mt-0.5 text-sm text-slate-400">Gestiona los profesores de Next Level School.</p>
                </div>
            </div>
        </div>
        <button id="open-profesor-modal" type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300"
                style="background: linear-gradient(135deg, #1B3A6B, #0F2749);"
                onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 40px rgba(27,58,107,0.35)'"
                onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(27,58,107,0.25)'">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 4v16m8-8H4"/>
            </svg>
            Agregar profesor
        </button>
    </div>

    <!-- ============================================ -->
    <!-- TARJETA PRINCIPAL MEJORADA                   -->
    <!-- ============================================ -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-all duration-300"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);"
         onmouseenter="this.style.boxShadow='0 12px 50px rgba(27,58,107,0.10)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.06)'">

        <!-- BARRA DE HERRAMIENTAS -->
        <div class="flex flex-col gap-4 border-b border-slate-200/80 p-5 lg:flex-row lg:items-center lg:justify-between"
             style="background: linear-gradient(180deg, #fafbff, #ffffff);">

            <!-- BUSCADOR -->
            <div class="relative w-full lg:max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
                <input id="buscar-profesor" type="text" placeholder="Buscar profesor por nombre, DNI o especialidad..."
                       class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-4 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                       style="background: #ffffff;"
                       onfocus="this.style.borderColor='#1B3A6B'; this.style.boxShadow='0 0 0 4px rgba(27,58,107,0.08)'"
                       onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
            </div>

            <!-- FILTRO Y ACCIONES -->
            <div class="flex flex-wrap items-center gap-3">
                <select id="filtro-estado"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                        onfocus="this.style.borderColor='#1B3A6B'"
                        onblur="this.style.borderColor='#e2e8f0'">
                    <option value="">Todos los estados</option>
                    <option value="activo">🟢 Activos</option>
                    <option value="inactivo">🔴 Inactivos</option>
                    <option value="licencia">🟡 Licencia</option>
                </select>
                <button type="button"
                        class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300 flex items-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18M7 12h10M10 18h4"/>
                    </svg>
                    Filtrar
                </button>
            </div>
        </div>

        <!-- TABLA -->
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr style="background: linear-gradient(90deg, #f8faff, #f1f5f9);">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Profesor</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">DNI</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Especialidad</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Teléfono</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Carga Horaria</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/80">
                    <tr id="profesores-empty" class="hidden">
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full"
                                     style="background: linear-gradient(135deg, #f1f5f9, #e2e8f0);">
                                    <svg class="w-10 h-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-base font-bold" style="color: #0F2749;">No se encontraron profesores</h3>
                                <p class="mt-1 max-w-sm text-sm text-slate-400">Intenta cambiar el término de búsqueda o el filtro seleccionado.</p>
                            </div>
                        </td>
                    </tr>
                    <!-- Filas generadas dinámicamente -->
                </tbody>
            </table>
        </div>

        <!-- PAGINACIÓN -->
        <div class="flex flex-col gap-3 border-t border-slate-200/80 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
             style="background: #fafbfc;">
            <p class="text-sm text-slate-500">
                Mostrando <span id="resultados-profesores" class="font-bold" style="color: #0F2749;">0</span>
                de <span id="total-resultados-profesores" class="font-bold" style="color: #0F2749;">0</span> profesores
            </p>
            <div class="flex items-center gap-2">
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-50 hover:border-slate-300 disabled:opacity-50 disabled:cursor-not-allowed">Anterior</button>
                <button class="rounded-lg px-4 py-1.5 text-sm font-semibold text-white"
                        style="background: linear-gradient(135deg, #1B3A6B, #0F2749);">1</button>
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-50">2</button>
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-50">3</button>
                <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-sm text-slate-500 transition hover:bg-slate-50 hover:border-slate-300">Siguiente</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL AGREGAR PROFESOR (MEJORADO)          -->
<!-- ========================================== -->
<div id="profesor-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div id="profesor-modal-overlay" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl modal-scroll"
             style="box-shadow: 0 30px 80px rgba(27,58,107,0.2);">

            <!-- ENCABEZADO -->
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200/80 px-6 py-5 rounded-t-2xl"
                 style="background: linear-gradient(90deg, #fafbff, #ffffff);">
                <div>
                    <h2 class="text-xl font-extrabold" style="color: #0F2749;">Agregar profesor</h2>
                    <p class="mt-0.5 text-sm text-slate-400">Registra un nuevo profesor en el sistema.</p>
                </div>
                <button id="close-profesor-modal" type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-200 hover:bg-slate-100 hover:text-slate-700">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- FORMULARIO -->
            <form id="profesor-form">
                <div class="space-y-5 px-6 py-6">
                    <!-- CÓDIGO -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input id="codigo" name="codigo" type="text" placeholder="Ej. PROF-001"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                               onfocus="this.style.borderColor='#1B3A6B'"
                               onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    <!-- NOMBRES Y APELLIDOS -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Nombres <span class="text-red-500">*</span>
                            </label>
                            <input id="nombre" name="nombre" type="text" placeholder="Ej. Juan Carlos"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Apellido Paterno <span class="text-red-500">*</span>
                            </label>
                            <input id="apellido_paterno" name="apellido_paterno" type="text" placeholder="Ej. Pérez"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Apellido Materno
                            </label>
                            <input id="apellido_materno" name="apellido_materno" type="text" placeholder="Ej. García"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                DNI <span class="text-red-500">*</span>
                            </label>
                            <input id="dni" name="dni" type="text" placeholder="Ej. 12345678" maxlength="8"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <!-- CORREO Y TELÉFONO -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Correo electrónico <span class="text-red-500">*</span>
                            </label>
                            <input id="email" name="email" type="email" placeholder="correo@ejemplo.com"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Teléfono
                            </label>
                            <input id="telefono" name="telefono" type="tel" placeholder="987 654 321"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <!-- SEXO Y FECHA -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Sexo</label>
                            <select id="sexo" name="sexo"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                    onfocus="this.style.borderColor='#1B3A6B'"
                                    onblur="this.style.borderColor='#e2e8f0'">
                                <option value="">Seleccionar</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Fecha de nacimiento</label>
                            <input id="fecha_nacimiento" name="fecha_nacimiento" type="date"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <!-- ESPECIALIDAD Y CARGA -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Especialidad</label>
                            <select id="especialidad" name="especialidad"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                    onfocus="this.style.borderColor='#1B3A6B'"
                                    onblur="this.style.borderColor='#e2e8f0'">
                                <option value="">Seleccionar especialidad</option>
                                <option value="matematica">Matemática</option>
                                <option value="comunicacion">Comunicación</option>
                                <option value="ingles">Inglés</option>
                                <option value="fisica">Física</option>
                                <option value="quimica">Química</option>
                                <option value="historia">Historia</option>
                                <option value="arte">Arte</option>
                                <option value="musica">Música</option>
                                <option value="educacion_fisica">Educación Física</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Carga horaria máxima (horas/semana)
                            </label>
                            <input id="carga_horaria_maxima" name="carga_horaria_maxima" type="number" min="1" max="40" value="30"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <!-- INSTITUCIÓN(ES) -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                            Institución(es) donde dicta clases
                        </label>
                        <p class="mb-3 text-xs text-slate-400">Un profesor puede trabajar en una o en ambas instituciones.</p>
                        <div class="flex flex-wrap gap-4" id="profesor-instituciones">
                            <label class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200 px-5 py-3 text-sm font-medium text-slate-700 transition-all duration-200 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50"
                                   style="background: #fafbfc;">
                                <input type="checkbox" name="instituciones[]" value="colegio"
                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 w-4 h-4">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="16" rx="1"/>
                                    <path d="M8 2V6M16 2V6M3 10H21"/>
                                </svg>
                                Colegio
                            </label>
                            <label class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200 px-5 py-3 text-sm font-medium text-slate-700 transition-all duration-200 cursor-pointer has-[:checked]:border-indigo-500 has-[:checked]:bg-indigo-50/50"
                                   style="background: #fafbfc;">
                                <input type="checkbox" name="instituciones[]" value="academia"
                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500/20 w-4 h-4">
                                <svg class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                    <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                </svg>
                                Academia
                            </label>
                        </div>
                    </div>

                    <!-- ESTADO -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Estado</label>
                        <select id="estado" name="estado"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                onfocus="this.style.borderColor='#1B3A6B'"
                                onblur="this.style.borderColor='#e2e8f0'">
                            <option value="activo">🟢 Activo</option>
                            <option value="inactivo">🔴 Inactivo</option>
                            <option value="licencia">🟡 Licencia</option>
                        </select>
                    </div>

                    <!-- OBSERVACIONES -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="2" placeholder="Notas adicionales..."
                                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                  onfocus="this.style.borderColor='#1B3A6B'"
                                  onblur="this.style.borderColor='#e2e8f0'"></textarea>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="sticky bottom-0 z-10 flex flex-col-reverse gap-3 border-t border-slate-200/80 px-6 py-4 rounded-b-2xl"
                     style="background: linear-gradient(0deg, #ffffff, #fafbfc);">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button id="cancel-profesor-modal" type="button"
                                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300"
                                style="background: linear-gradient(135deg, #1B3A6B, #0F2749);"
                                onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 40px rgba(27,58,107,0.35)'"
                                onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(27,58,107,0.25)'">
                            Guardar profesor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL EDITAR PROFESOR (MEJORADO)           -->
<!-- ========================================== -->
<div id="editar-profesor-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div id="editar-modal-overlay" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl modal-scroll"
             style="box-shadow: 0 30px 80px rgba(27,58,107,0.2);">

            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200/80 px-6 py-5 rounded-t-2xl"
                 style="background: linear-gradient(90deg, #fafbff, #ffffff);">
                <div>
                    <h2 class="text-xl font-extrabold" style="color: #0F2749;">Editar profesor</h2>
                    <p class="mt-0.5 text-sm text-slate-400">Modifica la información del profesor.</p>
                </div>
                <button id="close-editar-modal" type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-200 hover:bg-slate-100 hover:text-slate-700">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="editar-profesor-form">
                <div class="space-y-5 px-6 py-6">
                    <input type="hidden" id="editar-id">

                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Código</label>
                        <input id="editar-codigo" type="text"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/70 px-4 py-2.5 text-sm text-slate-500 outline-none cursor-not-allowed" readonly>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Nombres</label>
                            <input id="editar-nombre" type="text"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Apellido Paterno</label>
                            <input id="editar-apellido_paterno" type="text"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Apellido Materno</label>
                            <input id="editar-apellido_materno" type="text"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">DNI</label>
                            <input id="editar-dni" type="text"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Correo electrónico</label>
                            <input id="editar-email" type="email"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Teléfono</label>
                            <input id="editar-telefono" type="tel"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Sexo</label>
                            <select id="editar-sexo"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                    onfocus="this.style.borderColor='#1B3A6B'"
                                    onblur="this.style.borderColor='#e2e8f0'">
                                <option value="">Seleccionar</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Fecha de nacimiento</label>
                            <input id="editar-fecha_nacimiento" type="date"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Especialidad</label>
                            <select id="editar-especialidad"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                    onfocus="this.style.borderColor='#1B3A6B'"
                                    onblur="this.style.borderColor='#e2e8f0'">
                                <option value="">Seleccionar especialidad</option>
                                <option value="matematica">Matemática</option>
                                <option value="comunicacion">Comunicación</option>
                                <option value="ingles">Inglés</option>
                                <option value="fisica">Física</option>
                                <option value="quimica">Química</option>
                                <option value="historia">Historia</option>
                                <option value="arte">Arte</option>
                                <option value="musica">Música</option>
                                <option value="educacion_fisica">Educación Física</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Carga horaria máxima</label>
                            <input id="editar-carga_horaria_maxima" type="number" min="1" max="40"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Estado</label>
                        <select id="editar-estado"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                onfocus="this.style.borderColor='#1B3A6B'"
                                onblur="this.style.borderColor='#e2e8f0'">
                            <option value="activo">🟢 Activo</option>
                            <option value="inactivo">🔴 Inactivo</option>
                            <option value="licencia">🟡 Licencia</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Observaciones</label>
                        <textarea id="editar-observaciones" rows="2"
                                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 input-focus-ring"
                                  onfocus="this.style.borderColor='#1B3A6B'"
                                  onblur="this.style.borderColor='#e2e8f0'"></textarea>
                    </div>
                </div>

                <div class="sticky bottom-0 z-10 flex flex-col-reverse gap-3 border-t border-slate-200/80 px-6 py-4 rounded-b-2xl"
                     style="background: linear-gradient(0deg, #ffffff, #fafbfc);">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button id="cancel-editar-modal" type="button"
                                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300"
                                style="background: linear-gradient(135deg, #1B3A6B, #0F2749);"
                                onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 40px rgba(27,58,107,0.35)'"
                                onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(27,58,107,0.25)'">
                            Guardar cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL ELIMINAR PROFESOR (MEJORADO)         -->
<!-- ========================================== -->
<div id="eliminar-profesor-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div id="eliminar-profesor-modal-overlay" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300"></div>
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
                        <h2 class="text-lg font-extrabold" style="color: #0F2749;">Eliminar profesor</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            ¿Estás seguro de eliminar a <span id="eliminar-profesor-nombre" class="font-bold" style="color: #0F2749;"></span>?
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
                <button id="close-eliminar-profesor-modal" type="button"
                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-slate-200/80 px-6 py-4 sm:flex-row sm:justify-end">
                <button id="cancel-eliminar-profesor-modal" type="button"
                        class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300">
                    Cancelar
                </button>
                <button id="confirm-eliminar-profesor" type="button"
                        class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300"
                        style="background: linear-gradient(135deg, #dc2626, #991b1b);"
                        onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 40px rgba(220,38,38,0.35)'"
                        onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(220,38,38,0.25)'">
                    Eliminar profesor
                </button>
            </div>
        </div>
    </div>
</div>

@endsection