@extends('layouts.app')

@section('title', 'Dashboard | Next Level')

@section('page-title', 'Dashboard')

@section('content')

<!-- ============================================ -->
<!-- HEADER CON EFECTO MODERNO                    -->
<!-- ============================================ -->
<div class="rounded-2xl p-6 md:p-8 mb-8 relative overflow-hidden"
     style="background: linear-gradient(135deg, #0F2749 0%, #1B3A6B 40%, #8d0707 100%); box-shadow: 0 20px 60px rgba(27,58,107,0.3);">

    <!-- Efectos decorativos -->
    <div class="absolute top-[-30%] right-[-5%] w-80 h-80 rounded-full opacity-15"
         style="background: radial-gradient(circle, #db0808, transparent 70%); pointer-events: none;"></div>
    <div class="absolute bottom-[-40%] left-[-5%] w-72 h-72 rounded-full opacity-10"
         style="background: radial-gradient(circle, #FFFFFF, transparent 70%); pointer-events: none;"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full opacity-5"
         style="background: radial-gradient(circle, #db0808, transparent 70%); pointer-events: none;"></div>

    <!-- Cuadrícula decorativa -->
    <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
         style="background-image: 
            linear-gradient(rgba(255,255,255,0.3) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.3) 1px, transparent 1px);
            background-size: 40px 40px;">
    </div>

    <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                     style="background: rgba(219,8,8,0.3); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.15);">
                    <!-- Icono SVG: Graduation cap -->
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span class="text-white/60 text-xs font-semibold uppercase tracking-widest">Panel Académico</span>
            </div>
            <h1 class="text-2xl md:text-4xl font-extrabold text-white tracking-tight">
                Bienvenido a <span style="color: #ff6b6b; text-shadow: 0 0 40px rgba(219,8,8,0.3);">Next Level</span>
                <span class="inline-block ml-2"></span>
            </h1>
            <p class="text-white/70 mt-1 text-sm md:text-base max-w-lg">
                Administra profesores, cursos, aulas y horarios desde un solo lugar.
            </p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="px-4 py-2 rounded-xl text-white text-xs font-semibold uppercase tracking-wider border border-white/20"
                 style="background: rgba(255,255,255,0.08); backdrop-filter: blur(12px);">
                <!-- Icono SVG: Calendar -->
                <svg class="w-3.5 h-3.5 inline-block mr-1.5 -mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M3 10H21M8 2V6M16 2V6"/>
                </svg>
                Hoy · 24/08/2026
            </div>
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white border border-white/20"
                 style="background: rgba(255,255,255,0.06); backdrop-filter: blur(12px);">
                <!-- Icono SVG: Bell -->
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Mini estadísticas en el header -->
    <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4 mt-6 pt-6"
         style="border-top: 1px solid rgba(255,255,255,0.08);">
        <div>
            <p class="text-white/40 text-[0.6rem] uppercase tracking-wider font-semibold">Total Profesores</p>
            <p class="text-white text-xl font-bold">25</p>
        </div>
        <div>
            <p class="text-white/40 text-[0.6rem] uppercase tracking-wider font-semibold">Total Cursos</p>
            <p class="text-white text-xl font-bold">12</p>
        </div>
        <div>
            <p class="text-white/40 text-[0.6rem] uppercase tracking-wider font-semibold">Aulas Activas</p>
            <p class="text-white text-xl font-bold">8</p>
        </div>
        <div>
            <p class="text-white/40 text-[0.6rem] uppercase tracking-wider font-semibold">Horarios Generados</p>
            <p class="text-white text-xl font-bold">48</p>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- TARJETAS DE ESTADÍSTICAS CON DISEÑO PREMIUM  -->
<!-- ============================================ -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <!-- PROFESORES -->
    <div class="group bg-white rounded-2xl p-6 transition-all duration-500 relative overflow-hidden cursor-pointer"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.08);"
         onmouseenter="this.style.boxShadow='0 20px 60px rgba(27,58,107,0.15)'; this.style.transform='translateY(-6px)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.08)'; this.style.transform='translateY(0)'">

        <div class="absolute top-0 left-0 right-0 h-1.5"
             style="background: linear-gradient(90deg, #1B3A6B, #0F2749);"></div>

        <div class="absolute -bottom-8 -right-8 w-32 h-32 rounded-full opacity-[0.04]"
             style="background: #1B3A6B;"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Profesores</p>
                <p class="text-3xl font-extrabold mt-1 tracking-tight" style="color: #0F2749;">25</p>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="text-[0.6rem] font-bold px-2 py-0.5 rounded-full"
                          style="background: #d1fae5; color: #065f46;">↑ 8%</span>
                    <span class="text-[0.6rem] text-slate-400 font-medium">este mes</span>
                </div>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl transition-transform duration-300 group-hover:scale-110"
                 style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe);">
                <!-- Icono SVG: User -->
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/>
                    <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- CURSOS -->
    <div class="group bg-white rounded-2xl p-6 transition-all duration-500 relative overflow-hidden cursor-pointer"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.08);"
         onmouseenter="this.style.boxShadow='0 20px 60px rgba(27,58,107,0.15)'; this.style.transform='translateY(-6px)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.08)'; this.style.transform='translateY(0)'">

        <div class="absolute top-0 left-0 right-0 h-1.5"
             style="background: linear-gradient(90deg, #db0808, #8d0707);"></div>

        <div class="absolute -bottom-8 -right-8 w-32 h-32 rounded-full opacity-[0.04]"
             style="background: #db0808;"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Cursos</p>
                <p class="text-3xl font-extrabold mt-1 tracking-tight" style="color: #0F2749;">12</p>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="text-[0.6rem] font-bold px-2 py-0.5 rounded-full"
                          style="background: #d1fae5; color: #065f46;">↑ 4%</span>
                    <span class="text-[0.6rem] text-slate-400 font-medium">este mes</span>
                </div>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl transition-transform duration-300 group-hover:scale-110"
                 style="background: linear-gradient(135deg, #fecaca, #fca5a5);">
                <!-- Icono SVG: Book -->
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#8d0707" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6H20M4 12H20M4 18H14"/>
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- AULAS -->
    <div class="group bg-white rounded-2xl p-6 transition-all duration-500 relative overflow-hidden cursor-pointer"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.08);"
         onmouseenter="this.style.boxShadow='0 20px 60px rgba(27,58,107,0.15)'; this.style.transform='translateY(-6px)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.08)'; this.style.transform='translateY(0)'">

        <div class="absolute top-0 left-0 right-0 h-1.5"
             style="background: linear-gradient(90deg, #f59e0b, #d97706);"></div>

        <div class="absolute -bottom-8 -right-8 w-32 h-32 rounded-full opacity-[0.04]"
             style="background: #f59e0b;"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Aulas</p>
                <p class="text-3xl font-extrabold mt-1 tracking-tight" style="color: #0F2749;">8</p>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="text-[0.6rem] font-bold px-2 py-0.5 rounded-full"
                          style="background: #f3f4f6; color: #6b7280;">●</span>
                    <span class="text-[0.6rem] text-slate-400 font-medium">Disponibles</span>
                </div>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl transition-transform duration-300 group-hover:scale-110"
                 style="background: linear-gradient(135deg, #fde68a, #fcd34d);">
                <!-- Icono SVG: Building / Home -->
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="1"/>
                    <path d="M8 2V6M16 2V6M3 10H21"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- HORARIOS -->
    <div class="group bg-white rounded-2xl p-6 transition-all duration-500 relative overflow-hidden cursor-pointer"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.08);"
         onmouseenter="this.style.boxShadow='0 20px 60px rgba(27,58,107,0.15)'; this.style.transform='translateY(-6px)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.08)'; this.style.transform='translateY(0)'">

        <div class="absolute top-0 left-0 right-0 h-1.5"
             style="background: linear-gradient(90deg, #8b5cf6, #6d28d9);"></div>

        <div class="absolute -bottom-8 -right-8 w-32 h-32 rounded-full opacity-[0.04]"
             style="background: #8b5cf6;"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Horarios</p>
                <p class="text-3xl font-extrabold mt-1 tracking-tight" style="color: #0F2749;">48</p>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="text-[0.6rem] font-bold px-2 py-0.5 rounded-full"
                          style="background: #d1fae5; color: #065f46;">✓</span>
                    <span class="text-[0.6rem] text-slate-400 font-medium">Generados</span>
                </div>
            </div>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl transition-transform duration-300 group-hover:scale-110"
                 style="background: linear-gradient(135deg, #e9d5ff, #d8b4fe);">
                <!-- Icono SVG: Calendar -->
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="#6d28d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M3 10H21M8 2V6M16 2V6"/>
                </svg>
            </div>
        </div>
    </div>

</div>

<!-- ============================================ -->
<!-- CONTENIDO INFERIOR                          -->
<!-- ============================================ -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- HORARIOS DE HOY -->
    <div class="lg:col-span-2 bg-white rounded-2xl transition-all duration-300 overflow-hidden"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.08);"
         onmouseenter="this.style.boxShadow='0 12px 50px rgba(27,58,107,0.12)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.08)'">

        <div class="p-5 md:p-6 border-b flex items-center justify-between flex-wrap gap-3"
             style="border-color: #f1f5f9;">
            <div>
                <h2 class="text-lg font-bold flex items-center gap-2" style="color: #0F2749;">
                    <!-- Icono SVG: Calendar -->
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#0F2749" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <path d="M3 10H21M8 2V6M16 2V6"/>
                    </svg>
                    Horarios de hoy
                </h2>
                <p class="text-sm text-slate-400 mt-0.5">Clases programadas para hoy</p>
            </div>
            <a href="#" class="text-sm font-semibold transition-colors flex items-center gap-1"
               style="color: #db0808;">
                Ver todos
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        <div>

            <!-- Horario 1 -->
            <div class="px-5 md:px-6 py-4 flex items-center gap-4 md:gap-5 transition-all duration-200 border-b"
                 style="border-color: #f8fafc;"
                 onmouseenter="this.style.background='#f8faff'"
                 onmouseleave="this.style.background='transparent'">

                <div class="text-center min-w-[3.5rem]">
                    <p class="text-sm font-extrabold" style="color: #0F2749;">08:00</p>
                    <p class="text-[0.55rem] uppercase font-semibold text-slate-300 tracking-wider">AM</p>
                </div>

                <div class="w-1 h-12 rounded-full flex-shrink-0"
                     style="background: linear-gradient(180deg, #1B3A6B, #0F2749);"></div>

                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold" style="color: #0F2749;">Matemática</p>
                        <span class="text-[0.5rem] font-bold px-2 py-0.5 rounded-full"
                              style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #1B3A6B; letter-spacing: 0.5px;">EN CURSO</span>
                    </div>
                    <p class="text-sm text-slate-400 flex items-center gap-1.5">
                        <!-- Icono SVG: User -->
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/>
                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/>
                        </svg>
                        Juan Pérez
                        <span class="text-slate-300">·</span>
                        <!-- Icono SVG: Building -->
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="1"/>
                            <path d="M8 2V6M16 2V6M3 10H21"/>
                        </svg>
                        Aula 201
                    </p>
                </div>
            </div>

            <!-- Horario 2 -->
            <div class="px-5 md:px-6 py-4 flex items-center gap-4 md:gap-5 transition-all duration-200 border-b"
                 style="border-color: #f8fafc;"
                 onmouseenter="this.style.background='#f8faff'"
                 onmouseleave="this.style.background='transparent'">

                <div class="text-center min-w-[3.5rem]">
                    <p class="text-sm font-extrabold" style="color: #0F2749;">09:00</p>
                    <p class="text-[0.55rem] uppercase font-semibold text-slate-300 tracking-wider">AM</p>
                </div>

                <div class="w-1 h-12 rounded-full flex-shrink-0"
                     style="background: linear-gradient(180deg, #10b981, #059669);"></div>

                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold" style="color: #0F2749;">Inglés</p>
                        <span class="text-[0.5rem] font-bold px-2 py-0.5 rounded-full"
                              style="background: #f1f5f9; color: #64748b; letter-spacing: 0.5px;">PRÓXIMA</span>
                    </div>
                    <p class="text-sm text-slate-400 flex items-center gap-1.5">
                        <!-- Icono SVG: User -->
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/>
                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/>
                        </svg>
                        María López
                        <span class="text-slate-300">·</span>
                        <!-- Icono SVG: Building -->
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="1"/>
                            <path d="M8 2V6M16 2V6M3 10H21"/>
                        </svg>
                        Aula 203
                    </p>
                </div>
            </div>

            <!-- Horario 3 -->
            <div class="px-5 md:px-6 py-4 flex items-center gap-4 md:gap-5 transition-all duration-200"
                 style="border-color: #f8fafc;"
                 onmouseenter="this.style.background='#f8faff'"
                 onmouseleave="this.style.background='transparent'">

                <div class="text-center min-w-[3.5rem]">
                    <p class="text-sm font-extrabold" style="color: #0F2749;">10:00</p>
                    <p class="text-[0.55rem] uppercase font-semibold text-slate-300 tracking-wider">AM</p>
                </div>

                <div class="w-1 h-12 rounded-full flex-shrink-0"
                     style="background: linear-gradient(180deg, #f59e0b, #d97706);"></div>

                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold" style="color: #0F2749;">Física</p>
                        <span class="text-[0.5rem] font-bold px-2 py-0.5 rounded-full"
                              style="background: #f1f5f9; color: #64748b; letter-spacing: 0.5px;">PRÓXIMA</span>
                    </div>
                    <p class="text-sm text-slate-400 flex items-center gap-1.5">
                        <!-- Icono SVG: User -->
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/>
                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/>
                        </svg>
                        Carlos Díaz
                        <span class="text-slate-300">·</span>
                        <!-- Icono SVG: Building -->
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="1"/>
                            <path d="M8 2V6M16 2V6M3 10H21"/>
                        </svg>
                        Aula 202
                    </p>
                </div>
            </div>

        </div>

        <!-- Footer de horarios -->
        <div class="px-5 md:px-6 py-3 border-t flex items-center justify-between"
             style="border-color: #f1f5f9; background: #fafbfc;">
            <span class="text-[0.6rem] text-slate-400 font-medium uppercase tracking-wider">3 clases programadas</span>
            <span class="text-[0.6rem] font-semibold" style="color: #1B3A6B;">⬤ <span class="text-slate-400 font-normal">Hoy</span></span>
        </div>
    </div>

    <!-- ACCIONES RÁPIDAS -->
    <div class="bg-white rounded-2xl p-5 md:p-6 transition-all duration-300"
         style="box-shadow: 0 4px 20px rgba(27,58,107,0.08);"
         onmouseenter="this.style.boxShadow='0 12px 50px rgba(27,58,107,0.12)'"
         onmouseleave="this.style.boxShadow='0 4px 20px rgba(27,58,107,0.08)'">

        <div class="flex items-center justify-between mb-1">
            <h2 class="text-lg font-bold flex items-center gap-2" style="color: #0F2749;">
                <!-- Icono SVG: Zap -->
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="#0F2749" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                </svg>
                Acciones rápidas
            </h2>
            <span class="text-[0.5rem] font-bold px-2 py-0.5 rounded-full"
                  style="background: #f0f4ff; color: #1B3A6B; letter-spacing: 0.5px;">+</span>
        </div>
        <p class="text-sm text-slate-400 mb-5">Accede rápidamente a las funciones principales.</p>

        <!-- Agregar profesor -->
        <a href="#" class="group flex items-center gap-4 p-3.5 rounded-xl transition-all duration-300 mb-2.5 border"
           style="background: #f8fafc; border-color: transparent;"
           onmouseenter="this.style.background='#f0f4ff'; this.style.borderColor='#1B3A6B'; this.style.transform='translateX(6px)'; this.style.boxShadow='0 4px 15px rgba(27,58,107,0.1)'"
           onmouseleave="this.style.background='#f8fafc'; this.style.borderColor='transparent'; this.style.transform='translateX(0)'; this.style.boxShadow='none'">

            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110"
                 style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe);">
                <!-- Icono SVG: User Plus -->
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#1B3A6B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21"/>
                    <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z"/>
                    <path d="M17 14L19 16L23 12" stroke="#1B3A6B" stroke-width="2.5"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm" style="color: #0F2749;">Agregar profesor</p>
                <p class="text-xs text-slate-400">Registrar nuevo profesor</p>
            </div>
            <svg class="w-4 h-4 ml-auto text-slate-300 transition-all duration-300 group-hover:translate-x-1 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>

        <!-- Agregar curso -->
        <a href="#" class="group flex items-center gap-4 p-3.5 rounded-xl transition-all duration-300 mb-2.5 border"
           style="background: #f8fafc; border-color: transparent;"
           onmouseenter="this.style.background='#fff0f0'; this.style.borderColor='#db0808'; this.style.transform='translateX(6px)'; this.style.boxShadow='0 4px 15px rgba(219,8,8,0.1)'"
           onmouseleave="this.style.background='#f8fafc'; this.style.borderColor='transparent'; this.style.transform='translateX(0)'; this.style.boxShadow='none'">

            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110"
                 style="background: linear-gradient(135deg, #fecaca, #fca5a5);">
                <!-- Icono SVG: Book Plus -->
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#8d0707" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6H20M4 12H20M4 18H14"/>
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="M12 8V14M9 11H15" stroke="#8d0707" stroke-width="2.5"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm" style="color: #0F2749;">Agregar curso</p>
                <p class="text-xs text-slate-400">Registrar nuevo curso</p>
            </div>
            <svg class="w-4 h-4 ml-auto text-slate-300 transition-all duration-300 group-hover:translate-x-1 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>

        <!-- Ver horarios -->
        <a href="#" class="group flex items-center gap-4 p-3.5 rounded-xl transition-all duration-300 border"
           style="background: #f8fafc; border-color: transparent;"
           onmouseenter="this.style.background='#f5f0ff'; this.style.borderColor='#8b5cf6'; this.style.transform='translateX(6px)'; this.style.boxShadow='0 4px 15px rgba(139,92,246,0.1)'"
           onmouseleave="this.style.background='#f8fafc'; this.style.borderColor='transparent'; this.style.transform='translateX(0)'; this.style.boxShadow='none'">

            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-xl flex-shrink-0 transition-transform duration-300 group-hover:scale-110"
                 style="background: linear-gradient(135deg, #e9d5ff, #d8b4fe);">
                <!-- Icono SVG: Calendar -->
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="#6d28d9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <path d="M3 10H21M8 2V6M16 2V6"/>
                </svg>
            </div>
            <div>
                <p class="font-semibold text-sm" style="color: #0F2749;">Ver horarios</p>
                <p class="text-xs text-slate-400">Consultar horarios</p>
            </div>
            <svg class="w-4 h-4 ml-auto text-slate-300 transition-all duration-300 group-hover:translate-x-1 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>

        <!-- Indicador de atajos -->
        <div class="mt-5 pt-4 flex items-center justify-between"
             style="border-top: 1px solid #f1f5f9;">
            <span class="text-[0.55rem] text-slate-400 font-medium uppercase tracking-wider">Atajos disponibles</span>
            <div class="flex gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full" style="background: #1B3A6B;"></span>
                <span class="w-1.5 h-1.5 rounded-full" style="background: #db0808;"></span>
                <span class="w-1.5 h-1.5 rounded-full" style="background: #8b5cf6;"></span>
            </div>
        </div>
    </div>

</div>

@endsection