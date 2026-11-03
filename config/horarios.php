<?php
// config/horarios.php

return [
    // Recesos amarillos del Excel, confirmados el 21/10/2026.
    // Las franjas de tarde sin horas no definen recesos.
    'recesos_fijos' => [
        'colegio' => [
            'primaria' => [
                ['nombre' => 'Receso Primaria 1', 'hora_inicio' => '08:30', 'hora_fin' => '08:50'],
                ['nombre' => 'Receso Primaria 2', 'hora_inicio' => '10:20', 'hora_fin' => '10:40'],
            ],
            'secundaria' => [
                ['nombre' => 'Receso Secundaria 1', 'hora_inicio' => '09:15', 'hora_fin' => '09:35'],
                ['nombre' => 'Receso Secundaria 2', 'hora_inicio' => '11:10', 'hora_fin' => '11:25'],
            ],
        ],
        'academia' => [
            'academia' => [
                ['nombre' => 'Receso Academia 1', 'hora_inicio' => '08:50', 'hora_fin' => '09:10'],
                ['nombre' => 'Receso Academia 2', 'hora_inicio' => '10:40', 'hora_fin' => '10:50'],
            ],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Configuración General de Horarios
    |--------------------------------------------------------------------------
    */
    'general' => [
        'duracion_default_minutos' => env('DURACION_BLOQUE_MINUTOS', 45),
        'dias_laborales' => explode(',', env('DIAS_LABORALES', '1,2,3,4,5,6')),
        'max_horas_continuas' => env('MAX_HORAS_CONTINUAS', 4),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración por Institución y Nivel
    |--------------------------------------------------------------------------
    | Define jornadas y duraciones de referencia por nivel.
    | Los bloques se construyen desde recesos_fijos, sin duplicar esas reglas.
    */
    'instituciones' => [

        // ==========================================
        // COLEGIO - PRIMARIA (Turno Mañana)
        // ==========================================
        'colegio_primaria' => [
            'institucion' => 'colegio',
            'nivel' => 'primaria',
            'turno' => 'mañana',
            'hora_inicio' => '07:00',
            'hora_fin' => '12:55',
            'duracion_bloque' => 45, // minutos
        ],

        // ==========================================
        // COLEGIO - SECUNDARIA (Turno Mañana)
        // ==========================================
        'colegio_secundaria' => [
            'institucion' => 'colegio',
            'nivel' => 'secundaria',
            'turno' => 'mañana',
            'hora_inicio' => '07:00',
            'hora_fin' => '13:10',
            'duracion_bloque' => 45,
        ],

        // ==========================================
        // ACADEMIA - Turno Mañana (A2)
        // ==========================================
        'academia_mañana' => [
            'institucion' => 'academia',
            'nivel' => 'academia',
            'turno' => 'mañana',
            'hora_inicio' => '07:00',
            'hora_fin' => '13:15',
            'duracion_bloque' => 45,
        ],

        // ==========================================
        // ACADEMIA - Turno Tarde
        // ==========================================
        'academia_tarde' => [
            'institucion' => 'academia',
            'nivel' => 'academia',
            'turno' => 'tarde',
            'hora_inicio' => '15:30',
            'hora_fin' => '20:00',
            'duracion_bloque' => 90,
        ],

        // ==========================================
        // ACADEMIA - Día Completo (A1: mañana + tarde)
        // ==========================================
        'academia_completo' => [
            'institucion' => 'academia',
            'nivel' => 'academia',
            'turno' => 'completo',
            'hora_inicio' => '07:00',
            'hora_fin' => '18:30',
            'duracion_bloque' => 45,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Profesor
    |--------------------------------------------------------------------------
    */
    'profesor' => [
        'carga_maxima' => env('CARGA_HORARIA_MAXIMA', 30),
        'carga_minima' => env('CARGA_HORARIA_MINIMA', 8),
        'max_horas_continuas' => env('MAX_HORAS_CONTINUAS', 4),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Aula
    |--------------------------------------------------------------------------
    */
    'aula' => [
        'capacidad_default' => env('CAPACIDAD_AULA_DEFAULT', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Exportación
    |--------------------------------------------------------------------------
    */
    'exportacion' => [
        'excel_driver' => env('EXPORT_EXCEL_DRIVER', 'maatwebsite'),
        'pdf_driver' => env('EXPORT_PDF_DRIVER', 'dompdf'),
        'image_quality' => env('EXPORT_IMAGE_QUALITY', 90),
    ],
];
