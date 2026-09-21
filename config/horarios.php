<?php
// config/horarios.php

return [
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
    | Define los bloques horarios, recesos y turnos según la institución
    | y el nivel educativo.
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
            'bloques' => [
                ['numero' => 1, 'inicio' => '07:00', 'fin' => '07:45'],
                ['numero' => 2, 'inicio' => '07:45', 'fin' => '08:30'],
                ['receso' => true, 'inicio' => '08:30', 'fin' => '08:50', 'nombre' => 'Recreo 1'],
                ['numero' => 3, 'inicio' => '08:50', 'fin' => '09:35'],
                ['numero' => 4, 'inicio' => '09:35', 'fin' => '10:20'],
                ['receso' => true, 'inicio' => '10:20', 'fin' => '10:40', 'nombre' => 'Recreo 2'],
                ['numero' => 5, 'inicio' => '10:40', 'fin' => '11:25'],
                ['numero' => 6, 'inicio' => '11:25', 'fin' => '12:15'],
                ['numero' => 7, 'inicio' => '12:15', 'fin' => '12:55'],
            ],
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
            'bloques' => [
                ['numero' => 1, 'inicio' => '07:00', 'fin' => '07:45'],
                ['numero' => 2, 'inicio' => '07:45', 'fin' => '08:30'],
                ['numero' => 3, 'inicio' => '08:30', 'fin' => '09:15'],
                ['receso' => true, 'inicio' => '09:15', 'fin' => '09:35', 'nombre' => 'Receso 1'],
                ['numero' => 4, 'inicio' => '09:35', 'fin' => '10:25'],
                ['numero' => 5, 'inicio' => '10:25', 'fin' => '11:10'],
                ['receso' => true, 'inicio' => '11:10', 'fin' => '11:25', 'nombre' => 'Receso 2'],
                ['numero' => 6, 'inicio' => '11:25', 'fin' => '12:15'],
                ['numero' => 7, 'inicio' => '12:15', 'fin' => '13:10'],
            ],
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
            'bloques' => [
                ['numero' => 1, 'inicio' => '07:00', 'fin' => '07:55'],
                ['numero' => 2, 'inicio' => '07:55', 'fin' => '08:50'],
                ['receso' => true, 'inicio' => '08:50', 'fin' => '09:10', 'nombre' => 'Receso 1'],
                ['numero' => 3, 'inicio' => '09:10', 'fin' => '09:55'],
                ['numero' => 4, 'inicio' => '09:55', 'fin' => '10:40'],
                ['receso' => true, 'inicio' => '10:40', 'fin' => '10:50', 'nombre' => 'Receso 2'],
                ['numero' => 5, 'inicio' => '10:50', 'fin' => '11:35'],
                ['numero' => 6, 'inicio' => '11:35', 'fin' => '12:15'],
                ['numero' => 7, 'inicio' => '12:15', 'fin' => '13:15'],
            ],
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
            'bloques' => [
                ['numero' => 1, 'inicio' => '15:30', 'fin' => '17:00'],
                ['receso' => true, 'inicio' => '17:00', 'fin' => '17:10', 'nombre' => 'Receso 1'],
                ['numero' => 2, 'inicio' => '17:10', 'fin' => '18:30'],
                ['receso' => true, 'inicio' => '18:30', 'fin' => '18:40', 'nombre' => 'Receso 2'],
                ['numero' => 3, 'inicio' => '18:40', 'fin' => '20:00'],
            ],
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
            'bloques' => [
                // Mañana
                ['numero' => 1, 'inicio' => '07:00', 'fin' => '07:55'],
                ['numero' => 2, 'inicio' => '07:55', 'fin' => '08:50'],
                ['receso' => true, 'inicio' => '08:50', 'fin' => '09:10', 'nombre' => 'Receso 1'],
                ['numero' => 3, 'inicio' => '09:10', 'fin' => '09:55'],
                ['numero' => 4, 'inicio' => '09:55', 'fin' => '10:40'],
                ['receso' => true, 'inicio' => '10:40', 'fin' => '10:50', 'nombre' => 'Receso 2'],
                ['numero' => 5, 'inicio' => '10:50', 'fin' => '11:35'],
                ['numero' => 6, 'inicio' => '11:35', 'fin' => '12:15'],
                ['numero' => 7, 'inicio' => '12:15', 'fin' => '13:15'],
                // Descanso largo (almuerzo)
                ['receso' => true, 'inicio' => '13:15', 'fin' => '15:30', 'nombre' => 'Almuerzo'],
                // Tarde
                ['numero' => 8, 'inicio' => '15:30', 'fin' => '17:00'],
                ['receso' => true, 'inicio' => '17:00', 'fin' => '17:10', 'nombre' => 'Receso 3'],
                ['numero' => 9, 'inicio' => '17:10', 'fin' => '18:30'],
            ],
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
