@extends('layouts.app')

@section('title', 'Cursos - Next Level School')
@section('page-title', 'Cursos')

@section('content')

<style>
    /* ============================================ */
    /* ANIMACIONES Y ESTILOS PERSONALIZADOS        */
    /* ============================================ */
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
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
        box-shadow: 0 20px 60px rgba(27, 58, 107, 0.12);
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
        box-shadow: 0 12px 40px rgba(220, 38, 38, 0.35);
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
</style>

<div class="space-y-6 animate-slide-up">

    <!-- ============================================ -->
    <!-- ENCABEZADO MEJORADO                         -->
    <!-- ============================================ -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background: linear-gradient(135deg, #e8edff, #d0d9ff);">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6H20M4 12H20M4 18H14"/>
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight" style="color: #0F2749;">Cursos</h1>
                    <p class="mt-0.5 text-sm text-slate-400">Gestiona los cursos disponibles en Next Level School.</p>
                </div>
            </div>
        </div>
        <button id="open-curso-modal" type="button"
                class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 btn-primary"
                style="background: linear-gradient(135deg, #1B3A6B, #0F2749);"
                onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 12px 40px rgba(27,58,107,0.35)'"
                onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 8px 30px rgba(27,58,107,0.25)'">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 4v16m8-8H4"/>
            </svg>
            Agregar curso
        </button>
    </div>

    <!-- ============================================ -->
    <!-- ESTADÍSTICAS MEJORADAS                      -->
    <!-- ============================================ -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

        <!-- TOTAL -->
        <div class="card-hover rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
             style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Total de cursos</p>
                    <p id="total-cursos" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #0F2749;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46;">
                            <span>↑</span> 5%
                        </span>
                        <span class="text-[0.55rem] text-slate-400 font-medium">vs mes anterior</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, #e8edff, #d0d9ff);">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6H20M4 12H20M4 18H14"/>
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
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
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Cursos activos</p>
                    <p id="cursos-activos" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #059669;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46;">
                            <span>●</span> Disponibles
                        </span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, #d1fae5, #a7f3d0);">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17L4 12"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background: #10b981;"></span>
                <span class="text-[0.55rem] text-slate-400 font-medium">En uso</span>
            </div>
        </div>

        <!-- INACTIVOS -->
        <div class="card-hover rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm"
             style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-slate-400 uppercase tracking-wider">Cursos inactivos</p>
                    <p id="cursos-inactivos" class="mt-2 text-3xl font-extrabold tracking-tight" style="color: #64748b;">0</p>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="text-[0.6rem] font-bold px-2.5 py-0.5 rounded-full flex items-center gap-1"
                              style="background: #f3f4f6; color: #6b7280;">
                            <span>○</span> Sin actividad
                        </span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center"
                     style="background: linear-gradient(145deg, #f1f5f9, #e2e8f0);">
                    <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" style="background: #94a3b8;"></span>
                <span class="text-[0.55rem] text-slate-400 font-medium">Archivados</span>
            </div>
        </div>

    </div>

    <!-- ============================================ -->
    <!-- TABLA PRINCIPAL MEJORADA                    -->
    <!-- ============================================ -->
    <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm transition-all duration-300"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.06);"
         onmouseenter="this.style.boxShadow='0 12px 50px rgba(27,58,107,0.10)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.06)'">

        <!-- FILTROS -->
        <div class="border-b border-slate-200/80 p-5"
             style="background: linear-gradient(180deg, #fafbff, #ffffff);">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="relative w-full md:max-w-md">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="w-5 h-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                    </div>
                    <input id="buscar-curso" type="text" placeholder="Buscar curso por nombre o código..."
                           class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-4 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                           style="background: #ffffff;"
                           onfocus="this.style.borderColor='#1B3A6B'; this.style.boxShadow='0 0 0 4px rgba(27,58,107,0.08)'"
                           onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'">
                </div>
                <select id="filtro-curso-estado"
                        class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring md:w-48"
                        onfocus="this.style.borderColor='#1B3A6B'"
                        onblur="this.style.borderColor='#e2e8f0'">
                    <option value="">Todos los estados</option>
                    <option value="1">🟢 Activos</option>
                    <option value="0">🔴 Inactivos</option>
                </select>
            </div>
        </div>

        <!-- TABLA -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr style="background: linear-gradient(90deg, #f8faff, #f1f5f9);">
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Curso</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Código</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Nivel</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Tipo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Horas semanales</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Estado</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider" style="color: #1B3A6B;">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/80">
                    <tr id="cursos-empty" class="hidden">
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <div class="flex h-20 w-20 items-center justify-center rounded-full"
                                     style="background: linear-gradient(135deg, #f1f5f9, #e2e8f0);">
                                    <svg class="w-10 h-10 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <path d="m21 21-4.35-4.35"/>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-base font-bold" style="color: #0F2749;">No se encontraron cursos</h3>
                                <p class="mt-1 max-w-sm text-sm text-slate-400">Intenta cambiar el término de búsqueda o el filtro seleccionado.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- PIE -->
        <div class="flex flex-col gap-3 border-t border-slate-200/80 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
             style="background: #fafbfc;">
            <p class="text-sm text-slate-500">
                Mostrando <span id="resultados-cursos" class="font-bold" style="color: #0F2749;">0</span>
                de <span id="total-resultados-cursos" class="font-bold" style="color: #0F2749;">0</span> cursos
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
<!-- MODAL AGREGAR / EDITAR CURSO (MEJORADO)   -->
<!-- ========================================== -->
<div id="curso-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div id="curso-modal-overlay" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300"></div>
    <div class="relative flex min-h-full items-center justify-center p-4">
        <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl modal-scroll"
             style="box-shadow: 0 30px 80px rgba(27,58,107,0.2);">

            <!-- ENCABEZADO -->
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200/80 px-6 py-5 rounded-t-2xl"
                 style="background: linear-gradient(90deg, #fafbff, #ffffff);">
                <div>
                    <h2 id="curso-modal-title" class="text-xl font-extrabold" style="color: #0F2749;">Agregar curso</h2>
                    <p class="mt-0.5 text-sm text-slate-400">Registra un nuevo curso en el sistema.</p>
                </div>
                <button id="close-curso-modal" type="button"
                        class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-all duration-200 hover:bg-slate-100 hover:text-slate-700">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="curso-form">
                <div class="space-y-5 px-6 py-6">

                    <!-- CÓDIGO -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input id="curso-codigo" type="text" placeholder="Ej. MAT-001" maxlength="20"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm uppercase outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                               onfocus="this.style.borderColor='#1B3A6B'"
                               onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    <!-- NOMBRE -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                            Nombre del curso <span class="text-red-500">*</span>
                        </label>
                        <input id="curso-nombre" type="text" placeholder="Ej. Matemática" maxlength="100"
                               class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                               onfocus="this.style.borderColor='#1B3A6B'"
                               onblur="this.style.borderColor='#e2e8f0'">
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Descripción</label>
                        <textarea id="curso-descripcion" rows="2" placeholder="Breve descripción del curso..."
                                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                  onfocus="this.style.borderColor='#1B3A6B'"
                                  onblur="this.style.borderColor='#e2e8f0'"></textarea>
                    </div>

                    <!-- NIVEL Y TIPO -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Nivel <span class="text-red-500">*</span>
                            </label>
                            <select id="curso-nivel"
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
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Tipo <span class="text-red-500">*</span>
                            </label>
                            <select id="curso-tipo"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                    onfocus="this.style.borderColor='#1B3A6B'"
                                    onblur="this.style.borderColor='#e2e8f0'">
                                <option value="obligatorio">Obligatorio</option>
                                <option value="electivo">Electivo</option>
                                <option value="taller">Taller</option>
                            </select>
                        </div>
                    </div>

                    <!-- HORAS Y DURACIÓN -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">
                                Horas semanales <span class="text-red-500">*</span>
                            </label>
                            <input id="curso-horas" type="number" min="1" max="20" placeholder="Ej. 5"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Duración por clase (minutos)</label>
                            <input id="curso-duracion" type="number" min="30" max="180" value="60"
                                   class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                        </div>
                    </div>

                    <!-- COLOR -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Color (para el horario)</label>
                        <div class="flex items-center gap-3">
                            <input id="curso-color" type="color" value="#1B3A6B"
                                   class="h-12 w-12 cursor-pointer rounded-xl border border-slate-200 p-1 outline-none transition-all duration-200"
                                   onfocus="this.style.borderColor='#1B3A6B'"
                                   onblur="this.style.borderColor='#e2e8f0'">
                            <span id="curso-color-preview" class="text-sm font-medium" style="color: #0F2749;">#1B3A6B</span>
                        </div>
                    </div>

                    <!-- ESTADO -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Estado</label>
                        <select id="curso-estado"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition-all duration-200 input-focus-ring"
                                onfocus="this.style.borderColor='#1B3A6B'"
                                onblur="this.style.borderColor='#e2e8f0'">
                            <option value="1">🟢 Activo</option>
                            <option value="0">🔴 Inactivo</option>
                        </select>
                    </div>

                    <!-- OBSERVACIONES -->
                    <div>
                        <label class="mb-2 block text-sm font-semibold" style="color: #0F2749;">Observaciones</label>
                        <textarea id="curso-observaciones" rows="2" placeholder="Notas adicionales..."
                                  class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition-all duration-200 placeholder:text-slate-400 input-focus-ring"
                                  onfocus="this.style.borderColor='#1B3A6B'"
                                  onblur="this.style.borderColor='#e2e8f0'"></textarea>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="sticky bottom-0 z-10 flex flex-col-reverse gap-3 border-t border-slate-200/80 px-6 py-4 rounded-b-2xl"
                     style="background: linear-gradient(0deg, #ffffff, #fafbfc);">
                    <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button id="cancel-curso-modal" type="button"
                                class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300">
                            Cancelar
                        </button>
                        <button id="curso-submit-button" type="submit"
                                class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 btn-primary"
                                style="background: linear-gradient(135deg, #1B3A6B, #0F2749);">
                            Guardar curso
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL ELIMINAR CURSO (MEJORADO)           -->
<!-- ========================================== -->
<div id="eliminar-curso-modal" class="fixed inset-0 z-[100] hidden" aria-hidden="true">
    <div id="eliminar-curso-modal-overlay" class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity duration-300"></div>
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
                        <h2 class="text-lg font-extrabold" style="color: #0F2749;">Eliminar curso</h2>
                        <p class="mt-1 text-sm text-slate-500">
                            ¿Estás seguro de eliminar el curso <span id="eliminar-curso-nombre" class="font-bold" style="color: #0F2749;"></span>?
                            Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
                <button id="close-eliminar-curso-modal" type="button"
                        class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-slate-200/80 px-6 py-4 sm:flex-row sm:justify-end">
                <button id="cancel-eliminar-curso-modal" type="button"
                        class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-medium text-slate-600 transition-all duration-200 hover:bg-slate-50 hover:border-slate-300">
                    Cancelar
                </button>
                <button id="confirm-eliminar-curso" type="button"
                        class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white shadow-lg transition-all duration-300 btn-danger"
                        style="background: linear-gradient(135deg, #dc2626, #991b1b);">
                    Eliminar curso
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Script para preview de color --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('curso-color');
    const colorPreview = document.getElementById('curso-color-preview');
    if (colorInput && colorPreview) {
        colorInput.addEventListener('input', function() {
            colorPreview.textContent = this.value;
        });
    }
});
</script>

@endsection