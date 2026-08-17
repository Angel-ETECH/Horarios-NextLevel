<?php
// config/horarios.php

return [
    'horario' => [
        'inicio' => env('HORARIO_INICIO_CLASES', '08:00'),
        'fin' => env('HORARIO_FIN_CLASES', '18:00'),
        'duracion_bloque' => env('DURACION_BLOQUE_MINUTOS', 60),
        'dias_laborales' => explode(',', env('DIAS_LABORALES', '1,2,3,4,5,6')),
    ],
    'profesor' => [
        'carga_maxima' => env('CARGA_HORARIA_MAXIMA', 30),
        'carga_minima' => env('CARGA_HORARIA_MINIMA', 8),
        'max_horas_continuas' => env('MAX_HORAS_CONTINUAS', 4),
    ],
    'aula' => [
        'capacidad_default' => env('CAPACIDAD_AULA_DEFAULT', 30),
    ],
    'exportacion' => [
        'excel_driver' => env('EXPORT_EXCEL_DRIVER', 'maatwebsite'),
        'pdf_driver' => env('EXPORT_PDF_DRIVER', 'dompdf'),
        'image_quality' => env('EXPORT_IMAGE_QUALITY', 90),
    ],
];
