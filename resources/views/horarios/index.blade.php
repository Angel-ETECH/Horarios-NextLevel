@extends('layouts.app')

@section('title', 'Horarios - Next Level School')
@section('page-title', 'Horarios')

@section('content')

<style>
    :root{
        --rojo-principal:#db0808;
        --rojo-oscuro:#8d0707;
        --azul-noche:#1B3A6B;
        --azul-oscuro:#0F2749;
        --blanco:#FFFFFF;
        --borde:#DCE4EE;
    }

    /* =========================================================
       ANIMACIÓN GENERAL
    ========================================================= */

    @keyframes horarios-slide-up {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes horario-menu-entrada {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .horarios-page{
        animation: horarios-slide-up .45s ease-out both;
    }


    /* =========================================================
       TARJETAS
    ========================================================= */

    .horarios-card{
        border:1px solid rgba(27,58,107,.10);

        box-shadow:
            0 8px 28px rgba(15,39,73,.06);
    }

    .horarios-card-hover{
        transition:
            transform .25s ease,
            box-shadow .25s ease,
            border-color .25s ease;
    }

    .horarios-card-hover:hover{
        transform:translateY(-3px);

        box-shadow:
            0 16px 42px rgba(15,39,73,.10);

        border-color:
            rgba(27,58,107,.18);
    }


    /* =========================================================
       INPUTS
    ========================================================= */

    .horarios-input{
        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            background .2s ease;
    }

    .horarios-input:focus{
        border-color:
            var(--azul-noche) !important;

        box-shadow:
            0 0 0 4px rgba(27,58,107,.09) !important;
    }


    /* =========================================================
       SELECT NATIVO OCULTO
    ========================================================= */

    .horario-native-select{
        position:absolute !important;

        width:1px !important;
        height:1px !important;

        opacity:0 !important;

        pointer-events:none !important;

        overflow:hidden !important;
    }


    /* =========================================================
       CUSTOM SELECT
    ========================================================= */

    .horario-custom-select{
        position:relative;

        width:100%;
        min-width:0;
    }

    .horario-custom-trigger{
        position:relative;

        display:flex;

        width:100%;
        min-height:54px;

        align-items:center;

        gap:10px;

        border:1px solid #DCE4EE;

        border-radius:14px;

        background:#FFFFFF;

        padding:
            8px
            43px
            8px
            9px;

        text-align:left;

        cursor:pointer;

        box-shadow:
            0 3px 10px rgba(15,39,73,.035);

        transition:
            border-color .2s ease,
            box-shadow .2s ease,
            transform .2s ease,
            background .2s ease;
    }

    .horario-custom-trigger:hover:not(:disabled){
        border-color:#A9BAD0;

        box-shadow:
            0 7px 18px rgba(15,39,73,.07);
    }

    .horario-custom-trigger:focus{
        outline:none;

        border-color:#1B3A6B;

        box-shadow:
            0 0 0 4px rgba(27,58,107,.08),
            0 7px 18px rgba(15,39,73,.07);
    }

    .horario-custom-select.open
    .horario-custom-trigger{
        border-color:#1B3A6B;

        box-shadow:
            0 0 0 4px rgba(27,58,107,.08),
            0 7px 18px rgba(15,39,73,.07);
    }

    .horario-custom-trigger:disabled{
        cursor:not-allowed;

        background:#F1F5F9;

        color:#94A3B8;

        box-shadow:none;
    }

    .horario-custom-icon{
        display:flex;

        width:36px;
        height:36px;

        flex:0 0 36px;

        align-items:center;
        justify-content:center;

        border-radius:10px;

        background:
            linear-gradient(
                135deg,
                #EEF4FB,
                #E4ECF7
            );

        color:#1B3A6B;
    }

    .horario-custom-trigger:disabled
    .horario-custom-icon{
        background:#E2E8F0;

        color:#94A3B8;
    }

    .horario-custom-content{
        min-width:0;

        flex:1;
    }

    .horario-custom-small{
        display:block;

        margin-bottom:2px;

        color:#94A3B8;

        font-size:9px;
        font-weight:800;
        line-height:1.15;

        text-transform:uppercase;
        letter-spacing:.075em;
    }

    .horario-custom-text{
        display:block;

        max-width:100%;

        color:#334155;

        font-size:13px;
        font-weight:700;
        line-height:1.3;

        white-space:nowrap;

        overflow:hidden;

        text-overflow:ellipsis;
    }

    .horario-custom-trigger:disabled
    .horario-custom-text{
        color:#94A3B8;
    }

    .horario-custom-arrow{
        position:absolute;

        top:50%;
        right:14px;

        width:17px;
        height:17px;

        transform:
            translateY(-50%);

        color:#64748B;

        transition:
            transform .2s ease;
    }

    .horario-custom-select.open
    .horario-custom-arrow{
        transform:
            translateY(-50%)
            rotate(180deg);
    }


    /* =========================================================
       MENÚ CUSTOM
    ========================================================= */

    .horario-custom-menu{
        position:absolute;

        z-index:350;

        top:calc(100% + 7px);
        left:0;

        display:none;

        width:100%;
        min-width:240px;

        overflow:hidden;

        border:
            1px solid #DCE4EE;

        border-radius:15px;

        background:#FFFFFF;

        box-shadow:
            0 22px 55px rgba(15,39,73,.17);
    }

    .horario-custom-select.open
    .horario-custom-menu{
        display:block;

        animation:
            horario-menu-entrada .17s ease;
    }

    .horario-custom-search-wrap{
        position:relative;

        padding:9px;

        border-bottom:
            1px solid #EEF2F7;

        background:#F8FAFC;
    }

    .horario-custom-search-icon{
        position:absolute;

        top:50%;
        left:22px;

        width:15px;
        height:15px;

        transform:
            translateY(-50%);

        color:#94A3B8;

        pointer-events:none;
    }

    .horario-custom-search{
        width:100%;
        height:42px;

        border:
            1px solid #DCE4EE;

        border-radius:10px;

        background:#FFFFFF;

        padding:
            0
            12px
            0
            37px;

        color:#334155;

        font-size:12px;

        outline:none;

        transition:
            border-color .2s ease,
            box-shadow .2s ease;
    }

    .horario-custom-search:focus{
        border-color:#1B3A6B;

        box-shadow:
            0 0 0 3px rgba(27,58,107,.07);
    }

    .horario-custom-options{
        max-height:240px;

        overflow-y:auto;

        padding:7px;
    }

    .horario-custom-options::-webkit-scrollbar{
        width:5px;
    }

    .horario-custom-options::-webkit-scrollbar-thumb{
        border-radius:999px;

        background:#CBD5E1;
    }

    .horario-custom-option{
        display:flex;

        width:100%;
        min-width:0;

        align-items:center;

        gap:9px;

        border:0;

        border-radius:10px;

        background:transparent;

        padding:
            9px
            10px;

        color:#334155;

        text-align:left;

        cursor:pointer;

        transition:
            background .15s ease,
            color .15s ease;
    }

    .horario-custom-option:hover{
        background:#F1F5F9;
    }

    .horario-custom-option.selected{
        background:#EDF4FC;

        color:#0F2749;
    }

    .horario-option-icon{
        display:flex;

        width:32px;
        height:32px;

        flex:0 0 32px;

        align-items:center;
        justify-content:center;

        border-radius:9px;

        background:#F1F5F9;

        color:#1B3A6B;
    }

    .horario-custom-option.selected
    .horario-option-icon{
        background:#DDEAF8;
    }

    .horario-option-text{
        flex:1;

        min-width:0;

        font-size:12px;
        font-weight:700;
        line-height:1.35;

        white-space:normal;

        overflow-wrap:anywhere;
    }

    .horario-option-check{
        width:17px;
        height:17px;

        flex:0 0 17px;

        color:#1B3A6B;

        opacity:0;
    }

    .horario-custom-option.selected
    .horario-option-check{
        opacity:1;
    }

    .horario-custom-empty{
        padding:
            25px
            15px;

        color:#94A3B8;

        font-size:12px;

        text-align:center;
    }


    /* =========================================================
       ICONOS
    ========================================================= */

    .horarios-icon{
        box-shadow:
            inset 0 0 0 1px rgba(27,58,107,.08);
    }

    .icon-box-blue{
        display:flex;
        align-items:center;
        justify-content:center;

        width:48px;
        height:48px;

        flex-shrink:0;

        border-radius:16px;

        background:
            linear-gradient(
                145deg,
                rgba(27,58,107,.12),
                rgba(27,58,107,.05)
            );

        color:#1B3A6B;

        box-shadow:
            inset 0 0 0 1px rgba(27,58,107,.08);
    }

    .icon-box-red{
        display:flex;
        align-items:center;
        justify-content:center;

        width:48px;
        height:48px;

        flex-shrink:0;

        border-radius:16px;

        background:
            linear-gradient(
                145deg,
                rgba(219,8,8,.10),
                rgba(219,8,8,.035)
            );

        color:#DB0808;

        box-shadow:
            inset 0 0 0 1px rgba(219,8,8,.08);
    }


    /* =========================================================
       BOTONES VISTA
    ========================================================= */

    .vista-btn{
        position:relative;

        overflow:hidden;

        transition:
            transform .22s ease,
            box-shadow .22s ease,
            border-color .22s ease,
            background .22s ease;
    }

    .vista-btn:hover{
        transform:translateY(-2px);

        box-shadow:
            0 10px 26px rgba(15,39,73,.08);
    }

    .vista-btn::after{
        content:'';

        position:absolute;

        left:0;
        right:0;
        bottom:0;

        height:3px;

        background:
            linear-gradient(
                90deg,
                var(--rojo-principal),
                var(--rojo-oscuro)
            );

        transform:scaleX(0);

        transform-origin:left;

        transition:
            transform .25s ease;
    }

    .vista-btn:hover::after{
        transform:scaleX(1);
    }


    #horarios-render-container{
        min-height:160px;
    }


    /* =========================================================
       DRAG
    ========================================================= */

    .horario-clase{
        cursor:grab;

        transition:
            transform .18s ease,
            box-shadow .18s ease,
            opacity .18s ease;
    }

    .horario-clase:hover{
        transform:translateY(-2px);

        box-shadow:
            0 8px 20px rgba(15,39,73,.12);
    }

    .horario-clase:active{
        cursor:grabbing;
    }

    .horario-clase.dragging{
        opacity:.35;

        transform:scale(.97);
    }

    .horario-dropzone{
        transition:
            background .18s ease,
            outline .18s ease;
    }

    .horario-dropzone.drag-over{
        background:
            rgba(27,58,107,.08) !important;

        outline:
            2px dashed #1B3A6B;

        outline-offset:-4px;
    }


    /* =========================================================
       ELIMINAR
    ========================================================= */

    .btn-eliminar-horario{
        position:absolute;

        top:7px;
        right:7px;

        z-index:8;

        display:flex;

        align-items:center;
        justify-content:center;

        width:30px;
        height:30px;

        border:
            1px solid rgba(219,8,8,.18);

        border-radius:9px;

        background:
            rgba(255,255,255,.96);

        color:#DB0808;

        cursor:pointer;

        box-shadow:
            0 3px 10px rgba(15,39,73,.07);

        transition:
            transform .18s ease,
            background .18s ease,
            color .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .btn-eliminar-horario:hover{
        transform:
            translateY(-1px)
            scale(1.04);

        background:#DB0808;

        color:#FFFFFF;

        border-color:#DB0808;

        box-shadow:
            0 8px 18px rgba(219,8,8,.22);
    }


    /* =========================================================
       HORARIO
    ========================================================= */

    .horario-scroll{
        scrollbar-width:thin;

        scrollbar-color:
            #cbd5e1 transparent;
    }

    .horario-scroll::-webkit-scrollbar{
        height:8px;
    }

    .horario-scroll::-webkit-scrollbar-track{
        background:transparent;
    }

    .horario-scroll::-webkit-scrollbar-thumb{
        background:#cbd5e1;

        border-radius:999px;
    }

    .horario-card{
        border:
            1px solid rgba(15,39,73,.10);

        box-shadow:
            0 10px 32px rgba(15,39,73,.055);
    }

    .horario-tabla{
        table-layout:fixed;
    }

    .horario-tabla thead th{
        position:sticky;

        top:0;

        z-index:20;
    }


    /* =========================================================
       EXPORTACIONES
    ========================================================= */

    .horario-export-btn{
        display:inline-flex;

        align-items:center;
        justify-content:center;

        gap:.45rem;

        padding:
            .5rem
            .78rem;

        border:
            1px solid #e2e8f0;

        border-radius:.75rem;

        background:#FFFFFF;

        color:#475569;

        font-size:11px;
        font-weight:700;

        transition:
            transform .18s ease,
            color .18s ease,
            background .18s ease,
            border-color .18s ease,
            box-shadow .18s ease;
    }

    .horario-export-btn svg{
        width:15px;
        height:15px;
    }

    .horario-export-btn:hover{
        transform:translateY(-1px);

        border-color:
            rgba(27,58,107,.24);

        color:#1B3A6B;

        background:#F8FAFC;

        box-shadow:
            0 5px 15px rgba(15,39,73,.08);
    }

    .horario-export-btn-pdf:hover{
        border-color:
            rgba(219,8,8,.25);

        background:#FFF5F5;

        color:#DB0808;
    }


    /* =========================================================
       MODAL
    ========================================================= */

    .horario-modal-card{
        box-shadow:
            0 30px 80px rgba(15,39,73,.24);
    }

    .modal-delete-icon{
        display:flex;

        align-items:center;
        justify-content:center;

        width:50px;
        height:50px;

        flex-shrink:0;

        border-radius:16px;

        color:#DB0808;

        background:
            linear-gradient(
                145deg,
                rgba(219,8,8,.11),
                rgba(219,8,8,.035)
            );

        border:
            1px solid rgba(219,8,8,.10);
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width:640px){

        #horarios-render-container{
            padding:1rem;
        }

        .horario-clase{
            min-width:130px;
        }

        .horario-custom-trigger{
            min-height:51px;

            padding:
                7px
                40px
                7px
                8px;
        }

        .horario-custom-icon{
            width:34px;
            height:34px;

            flex-basis:34px;
        }

        .horario-custom-text{
            font-size:12px;
        }

        .horario-custom-menu{
            min-width:100%;
        }

        .horario-custom-options{
            max-height:210px;
        }
    }
</style>


<div class="space-y-6 horarios-page">


    {{-- =========================================================
         ENCABEZADO
    ========================================================== --}}

    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <div class="flex items-center gap-3">

            <div
                class="horarios-icon flex h-12 w-12 items-center justify-center rounded-2xl"
                style="
                    background:
                        linear-gradient(
                            145deg,
                            rgba(27,58,107,.12),
                            rgba(219,8,8,.06)
                        );
                "
            >

                <svg
                    class="h-6 w-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="#1B3A6B"
                    stroke-width="1.8"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect x="3" y="5" width="18" height="16" rx="2.5"/>
                    <path d="M8 3v4"/>
                    <path d="M16 3v4"/>
                    <path d="M3 10h18"/>
                    <path d="M7.5 14h3"/>
                    <path d="M13.5 14h3"/>
                    <path d="M7.5 17.5h3"/>
                    <path d="M13.5 17.5h3"/>
                </svg>

            </div>


            <div>

                <h1
                    class="text-2xl font-extrabold tracking-tight"
                    style="color:#0F2749;"
                >
                    Horarios académicos
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Consulta y organiza los horarios por profesor, aula o grado.
                </p>

            </div>

        </div>


        <div class="flex flex-wrap gap-2">

            <button
                type="button"
                id="btn-excel"
                class="
                    inline-flex
                    items-center
                    gap-2
                    rounded-xl
                    border
                    bg-white
                    px-4
                    py-2.5
                    text-sm
                    font-semibold
                    transition
                    hover:-translate-y-0.5
                "
                style="
                    border-color:rgba(27,58,107,.18);
                    color:#1B3A6B;
                "
            >

                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M6 3h9l4 4v14H6z"/>
                    <path d="M15 3v5h5"/>
                    <path d="M9 12l4 5"/>
                    <path d="M13 12l-4 5"/>
                </svg>

                Exportar Excel

            </button>


            <button
                type="button"
                id="btn-pdf"
                class="
                    inline-flex
                    items-center
                    gap-2
                    rounded-xl
                    px-4
                    py-2.5
                    text-sm
                    font-semibold
                    text-white
                    shadow-lg
                    transition
                    hover:-translate-y-0.5
                "
                style="
                    background:
                        linear-gradient(
                            135deg,
                            #db0808,
                            #8d0707
                        );
                "
            >

                <svg
                    class="h-5 w-5"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                >
                    <path d="M6 3h9l4 4v14H6z"/>
                    <path d="M15 3v5h5"/>
                    <path d="M9 16v-4h1.4a1.5 1.5 0 0 1 0 3H9"/>
                    <path d="M14 12v4"/>
                    <path d="M14 12h1.2c1.2 0 1.8.8 1.8 2s-.6 2-1.8 2H14"/>
                </svg>

                Exportar PDF

            </button>

        </div>

    </div>


    {{-- =========================================================
         INSTITUCIÓN
    ========================================================== --}}

    <div class="horarios-card rounded-2xl bg-white p-2">

        <div class="flex flex-wrap gap-2">

            <button
                type="button"
                class="
                    institucion-tab
                    inline-flex
                    items-center
                    gap-2
                    rounded-xl
                    px-5
                    py-3
                    text-sm
                    font-semibold
                    text-white
                    shadow-sm
                "
                style="
                    background:
                        linear-gradient(
                            135deg,
                            #1B3A6B,
                            #0F2749
                        );
                "
                data-institucion="colegio"
            >

                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 21h18"/>
                    <path d="M5 21V8l7-4 7 4v13"/>
                    <path d="M9 21v-4h6v4"/>
                    <path d="M8 11h2"/>
                    <path d="M14 11h2"/>
                </svg>

                Colegio

            </button>


            <button
                type="button"
                class="
                    institucion-tab
                    inline-flex
                    items-center
                    gap-2
                    rounded-xl
                    px-5
                    py-3
                    text-sm
                    font-semibold
                    text-slate-600
                    transition
                    hover:bg-slate-50
                "
                data-institucion="academia"
            >

                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="m3 10 9-5 9 5-9 5z"/>
                    <path d="M7 12.5V17c3 2 7 2 10 0v-4.5"/>
                    <path d="M21 10v6"/>
                </svg>

                Academia

            </button>

        </div>

    </div>


    {{-- =========================================================
         EDICIÓN
    ========================================================== --}}

    <div
        class="rounded-2xl border p-4"
        style="
            border-color:rgba(27,58,107,.15);
            background:
                linear-gradient(
                    135deg,
                    rgba(27,58,107,.055),
                    rgba(219,8,8,.02)
                );
        "
    >

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-start gap-3">

                <div
                    class="
                        flex
                        h-10
                        w-10
                        shrink-0
                        items-center
                        justify-center
                        rounded-xl
                        bg-white
                    "
                    style="color:#1B3A6B;"
                >

                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3v12"/>
                        <path d="m8 7 4-4 4 4"/>
                        <path d="M5 14v3a3 3 0 0 0 3 3h8a3 3 0 0 0 3-3v-3"/>
                        <path d="M8 14h8"/>
                    </svg>

                </div>


                <div>

                    <p
                        class="text-sm font-bold"
                        style="color:#0F2749;"
                    >
                        Edición manual del horario
                    </p>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Puedes mover una clase hacia otro día u hora
                        o eliminarla. El sistema validará los conflictos.
                    </p>

                </div>

            </div>


            <span
                id="modo-edicion-horario"
                class="
                    inline-flex
                    shrink-0
                    items-center
                    gap-2
                    rounded-full
                    bg-emerald-50
                    px-3
                    py-1.5
                    text-xs
                    font-semibold
                    text-emerald-700
                "
            >
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>

                Edición habilitada
            </span>

        </div>

    </div>


    {{-- =========================================================
         ESTADÍSTICAS
    ========================================================== --}}

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

        <div class="horarios-card horarios-card-hover rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-sm text-slate-500">
                        Profesores con clases
                    </p>

                    <p
                        id="total-profesores"
                        class="mt-1 text-2xl font-bold text-slate-900"
                    >
                        0
                    </p>
                </div>

                <div class="icon-box-blue">

                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="12" cy="7.5" r="3"/>
                        <path d="M5.5 21v-2.5A6.5 6.5 0 0 1 12 12a6.5 6.5 0 0 1 6.5 6.5V21"/>
                    </svg>

                </div>

            </div>

        </div>


        <div class="horarios-card horarios-card-hover rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-sm text-slate-500">
                        Aulas en uso
                    </p>

                    <p
                        id="total-aulas"
                        class="mt-1 text-2xl font-bold text-slate-900"
                    >
                        0
                    </p>
                </div>

                <div class="icon-box-red">

                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <rect x="4" y="4" width="16" height="16" rx="2"/>
                        <path d="M8 8h2"/>
                        <path d="M14 8h2"/>
                        <path d="M9 20v-4h6v4"/>
                    </svg>

                </div>

            </div>

        </div>


        <div class="horarios-card horarios-card-hover rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between gap-4">

                <div>
                    <p class="text-sm text-slate-500">
                        Clases programadas
                    </p>

                    <p
                        id="total-clases"
                        class="mt-1 text-2xl font-bold text-slate-900"
                    >
                        0
                    </p>
                </div>

                <div class="icon-box-blue">

                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H11v16H6.5A2.5 2.5 0 0 0 4 21.5z"/>
                        <path d="M20 5.5A2.5 2.5 0 0 0 17.5 3H13v16h4.5A2.5 2.5 0 0 1 20 21.5z"/>
                    </svg>

                </div>

            </div>

        </div>


        <div class="horarios-card horarios-card-hover rounded-2xl bg-white p-5">

            <div class="flex items-center justify-between gap-4">

                <div>

                    <p class="text-sm text-slate-500">
                        Estado
                    </p>

                    <p
                        class="mt-2 flex items-center gap-2 text-sm font-semibold"
                        style="color:#1B3A6B;"
                    >
                        <span
                            id="estado-punto"
                            class="h-2.5 w-2.5 rounded-full"
                            style="background:#10b981;"
                        ></span>

                        <span id="estado-horario">
                            Sin conflictos
                        </span>
                    </p>

                </div>

                <div class="icon-box-red">

                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3 19 6v5c0 4.7-2.8 8-7 10-4.2-2-7-5.3-7-10V6z"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         TIPO VISTA
    ========================================================== --}}

    <div class="horarios-card rounded-2xl bg-white p-5">

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h2
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    ¿Qué horario deseas consultar?
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Consulta el horario por profesor, aula o grado.
                </p>

            </div>


            <label class="inline-flex items-center gap-2 text-sm text-slate-600">

                <input
                    type="checkbox"
                    id="chk-semana-completa"
                    class="h-4 w-4 rounded border-slate-300"
                    style="accent-color:#1B3A6B;"
                >

                Mostrar semana completa por horas

            </label>

        </div>


        <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">

            <button
                type="button"
                class="vista-btn rounded-2xl border-2 p-4 text-left"
                style="
                    border-color:#1B3A6B;
                    background:rgba(27,58,107,.045);
                "
                data-vista="profesor"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                        style="
                            background:rgba(27,58,107,.09);
                            color:#1B3A6B;
                        "
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="12" cy="7.5" r="3"/>
                            <path d="M6 20v-1.5A6 6 0 0 1 12 12.5a6 6 0 0 1 6 6V20"/>
                        </svg>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">
                            Por profesor
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Consulta qué clases tiene cada profesor.
                        </p>
                    </div>

                </div>

            </button>


            <button
                type="button"
                class="vista-btn rounded-2xl border-2 border-slate-200 bg-white p-4 text-left"
                data-vista="aula"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                        style="
                            background:rgba(219,8,8,.075);
                            color:#DB0808;
                        "
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 21V5h16v16"/>
                            <path d="M2 21h20"/>
                            <path d="M8 9h2"/>
                            <path d="M14 9h2"/>
                            <path d="M9 21v-4h6v4"/>
                        </svg>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">
                            Por aula
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Consulta las clases programadas en cada aula.
                        </p>
                    </div>

                </div>

            </button>


            <button
                type="button"
                class="vista-btn rounded-2xl border-2 border-slate-200 bg-white p-4 text-left"
                data-vista="aula-completa"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                        style="
                            background:rgba(27,58,107,.08);
                            color:#1B3A6B;
                        "
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="5" width="18" height="16" rx="2.5"/>
                            <path d="M8 3v4"/>
                            <path d="M16 3v4"/>
                            <path d="M3 10h18"/>
                        </svg>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">
                            Aula completa
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Semana completa con espacios libres.
                        </p>
                    </div>

                </div>

            </button>


            <button
                type="button"
                class="vista-btn rounded-2xl border-2 border-slate-200 bg-white p-4 text-left"
                data-vista="grado"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl"
                        style="
                            background:rgba(219,8,8,.075);
                            color:#DB0808;
                        "
                    >
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="m3 9 9-5 9 5-9 5z"/>
                            <path d="M7 12v4.5c3 2 7 2 10 0V12"/>
                        </svg>
                    </div>

                    <div>
                        <p class="font-semibold text-slate-900">
                            Por grado
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Consulta las clases de un grado o sección.
                        </p>
                    </div>

                </div>

            </button>

        </div>

    </div>


    {{-- =========================================================
         FILTROS
    ========================================================== --}}

    <div class="horarios-card overflow-visible rounded-2xl bg-white p-5">

        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="flex items-center gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                    style="
                        background:rgba(27,58,107,.07);
                        color:#1B3A6B;
                    "
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 5h16"/>
                        <path d="M7 12h10"/>
                        <path d="M10 19h4"/>
                    </svg>
                </div>


                <div>

                    <h2
                        class="font-bold"
                        style="color:#0F2749;"
                    >
                        Filtros
                    </h2>

                    <p class="mt-1 text-xs text-slate-500">
                        Filtra la información que deseas visualizar y exportar.
                    </p>

                </div>

            </div>


            <button
                type="button"
                id="btn-reset"
                class="inline-flex items-center gap-2 text-sm font-semibold transition hover:opacity-75"
                style="color:#db0808;"
            >

                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 12a9 9 0 1 0 3-6.7"/>
                    <path d="M3 4v6h6"/>
                </svg>

                Restablecer filtros

            </button>

        </div>


        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">


            {{-- PROFESOR --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Profesor
                </label>

                <select
                    id="horario-profesor"
                    class="horario-native-select"
                >
                    <option value="todos">
                        Todos los profesores
                    </option>
                </select>

                <div
                    class="horario-custom-select"
                    data-horario-select="horario-profesor"
                    data-label="Profesor"
                    data-placeholder="Todos los profesores"
                    data-icon="profesor"
                ></div>

            </div>


            {{-- AULA --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Aula
                </label>

                <select
                    id="horario-aula"
                    class="horario-native-select"
                >
                    <option value="todos">
                        Todas las aulas
                    </option>
                </select>

                <div
                    class="horario-custom-select"
                    data-horario-select="horario-aula"
                    data-label="Aula"
                    data-placeholder="Todas las aulas"
                    data-icon="aula"
                ></div>

            </div>


            {{-- GRADO --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Grado / Sección
                </label>

                <select
                    id="horario-grado"
                    class="horario-native-select"
                >
                    <option value="todos">
                        Todos los grados
                    </option>
                </select>

                <div
                    class="horario-custom-select"
                    data-horario-select="horario-grado"
                    data-label="Grado / Sección"
                    data-placeholder="Todos los grados"
                    data-icon="grado"
                ></div>

            </div>


            {{-- CURSO --}}

            <div>

                <label
                    class="mb-2 block text-sm font-semibold text-slate-700"
                >
                    Curso
                </label>

                <select
                    id="horario-curso"
                    class="horario-native-select"
                >
                    <option value="todos">
                        Todos los cursos
                    </option>
                </select>

                <div
                    class="horario-custom-select"
                    data-horario-select="horario-curso"
                    data-label="Curso"
                    data-placeholder="Todos los cursos"
                    data-icon="curso"
                ></div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         HORARIOS
    ========================================================== --}}

    <div class="horarios-card overflow-hidden rounded-2xl bg-white">

        <div
            class="flex items-center gap-3 border-b border-slate-200 px-5 py-4"
            style="
                background:
                    linear-gradient(
                        90deg,
                        rgba(27,58,107,.035),
                        #FFFFFF
                    );
            "
        >

            <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                style="
                    background:rgba(27,58,107,.08);
                    color:#1B3A6B;
                "
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 19V5"/>
                    <path d="M4 19h16"/>
                    <path d="M8 16v-4"/>
                    <path d="M12 16V8"/>
                    <path d="M16 16v-6"/>
                </svg>
            </div>


            <div>

                <h2
                    id="titulo-horario"
                    class="font-bold"
                    style="color:#0F2749;"
                >
                    Horario por profesor
                </h2>

                <p
                    id="subtitulo-horario"
                    class="mt-1 text-xs text-slate-500"
                >
                    Consulta las clases asignadas a cada profesor.
                </p>

            </div>

        </div>


        <div
            id="horarios-render-container"
            class="space-y-6 p-5"
        ></div>

    </div>

</div>


{{-- =========================================================
     MENSAJES
========================================================== --}}

<div
    id="horario-mensaje"
    class="
        fixed
        bottom-5
        right-5
        z-[100]
        hidden
        max-w-md
        rounded-2xl
        border
        bg-white
        p-4
        shadow-2xl
    "
></div>


{{-- =========================================================
     MODAL ELIMINAR
========================================================== --}}

<div
    id="eliminar-horario-modal"
    class="fixed inset-0 z-[200] hidden"
>

    <div
        id="eliminar-horario-overlay"
        class="absolute inset-0 bg-slate-950/55 backdrop-blur-sm"
    ></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div class="horario-modal-card relative w-full max-w-md overflow-hidden rounded-2xl bg-white">

            <div class="flex items-start justify-between gap-4 px-6 py-5">

                <div class="flex items-start gap-4">

                    <div class="modal-delete-icon">

                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M3 6h18"/>
                            <path d="M8 6V4h8v2"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M10 11v5"/>
                            <path d="M14 11v5"/>
                        </svg>

                    </div>

                    <div>

                        <h2
                            class="text-lg font-extrabold"
                            style="color:#0F2749;"
                        >
                            Eliminar horario
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-slate-500">
                            Esta clase será eliminada y el espacio volverá a quedar libre.
                        </p>

                    </div>

                </div>


                <button
                    id="close-eliminar-horario-modal"
                    type="button"
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6 6 18"/>
                        <path d="M6 6l12 12"/>
                    </svg>
                </button>

            </div>


            <div class="border-t border-slate-100 px-6 py-5">

                <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">

                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3">

                        <span class="text-xs font-semibold text-slate-400">
                            Profesor
                        </span>

                        <span
                            id="eliminar-horario-profesor"
                            class="text-right text-sm font-bold text-slate-700"
                        >
                            -
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3">

                        <span class="text-xs font-semibold text-slate-400">
                            Curso
                        </span>

                        <span
                            id="eliminar-horario-curso"
                            class="text-right text-sm font-bold"
                            style="color:#0F2749;"
                        >
                            -
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3">

                        <span class="text-xs font-semibold text-slate-400">
                            Día
                        </span>

                        <span
                            id="eliminar-horario-dia"
                            class="text-right text-sm font-semibold text-slate-700"
                        >
                            -
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4 border-b border-slate-200 px-4 py-3">

                        <span class="text-xs font-semibold text-slate-400">
                            Horario
                        </span>

                        <span
                            id="eliminar-horario-hora"
                            class="text-right text-sm font-semibold text-slate-700"
                        >
                            -
                        </span>

                    </div>


                    <div class="flex items-center justify-between gap-4 px-4 py-3">

                        <span class="text-xs font-semibold text-slate-400">
                            Aula
                        </span>

                        <span
                            id="eliminar-horario-aula"
                            class="text-right text-sm font-semibold text-slate-700"
                        >
                            -
                        </span>

                    </div>

                </div>


                <div class="mt-4 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-xs leading-5 text-red-700">

                    Solo se eliminará esta clase.
                    El profesor, curso, aula, grado y disponibilidad continuarán registrados.

                </div>

            </div>


            <div
                class="
                    flex
                    flex-col-reverse
                    gap-3
                    border-t
                    border-slate-200
                    bg-slate-50
                    px-6
                    py-4
                    sm:flex-row
                    sm:justify-end
                "
            >

                <button
                    id="cancel-eliminar-horario-modal"
                    type="button"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100"
                >
                    Cancelar
                </button>


                <button
                    id="confirm-eliminar-horario"
                    type="button"
                    class="rounded-xl px-5 py-2.5 text-sm font-semibold text-white"
                    style="
                        background:
                            linear-gradient(
                                135deg,
                                #DB0808,
                                #8D0707
                            );
                    "
                >
                    Eliminar horario
                </button>

            </div>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

@endsection