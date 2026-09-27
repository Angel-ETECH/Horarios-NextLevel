<?php
// app/Http/Controllers/Api/PublicController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Profesor;
use App\Models\Grado;
use App\Models\Aula;
use App\Models\Curso;
use App\Services\ExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    /**
     * Servicio de exportación.
     */
    protected ExportService $exportService;


    /**
     * Días de la semana.
     */
    protected array $dias = [
        'lunes',
        'martes',
        'miércoles',
        'jueves',
        'viernes',
        'sábado',
    ];


    /**
     * Constructor.
     */
    public function __construct(
        ExportService $exportService
    ) {
        $this->exportService = $exportService;
    }


    // =========================================================
    // INSTITUCIONES
    // =========================================================

    /**
     * GET /api/publico/instituciones
     */
    public function instituciones(): JsonResponse
    {
        $instituciones = [];


        // =====================================================
        // COLEGIO
        // =====================================================

        $tieneColegio =
            Grado::where('activo', true)
                ->whereIn(
                    'nivel',
                    [
                        'primaria',
                        'secundaria',
                    ]
                )
                ->exists();


        if ($tieneColegio) {

            $instituciones[] = [
                'value' =>
                    'colegio',

                'label' =>
                    'Colegio',

                'descripcion' =>
                    'Primaria y Secundaria',
            ];
        }


        // =====================================================
        // ACADEMIA
        // =====================================================

        $tieneAcademia =
            Grado::where('activo', true)
                ->where(
                    'nivel',
                    'academia'
                )
                ->exists();


        if ($tieneAcademia) {

            $instituciones[] = [
                'value' =>
                    'academia',

                'label' =>
                    'Academia',

                'descripcion' =>
                    'Cursos de Academia',
            ];
        }


        return response()->json([
            'success' =>
                true,

            'data' =>
                $instituciones,
        ]);
    }


    // =========================================================
    // PROFESORES
    // =========================================================

    /**
     * GET /api/publico/profesores
     *
     * Ejemplo:
     * ?institucion=colegio
     * ?search=DOC-002
     */
    public function listarProfesores(
        Request $request
    ): JsonResponse {

        $request->validate([
            'institucion' =>
                'required|in:colegio,academia',

            'search' =>
                'nullable|string|max:100',
        ]);


        $query =
            Profesor::where(
                'estado',
                'activo'
            )
                ->where(
                    function ($q) use ($request) {

                        $q->where(
                            'institucion',
                            $request->institucion
                        )
                            ->orWhere(
                                'institucion',
                                'ambos'
                            );
                    }
                );


        // =====================================================
        // BÚSQUEDA POR CÓDIGO O NOMBRE
        // =====================================================

        if (
            $request->filled(
                'search'
            )
        ) {

            $search =
                $request->search;


            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'codigo',
                        'LIKE',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'nombre',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'apellido_paterno',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'apellido_materno',
                            'LIKE',
                            "%{$search}%"
                        );
                }
            );
        }


        $profesores =
            $query
                ->orderBy(
                    'apellido_paterno'
                )
                ->orderBy(
                    'nombre'
                )
                ->limit(50)
                ->get()
                ->map(
                    function ($profesor) {

                        return [
                            'id' =>
                                $profesor->id,

                            'codigo' =>
                                $profesor->codigo,

                            'nombre_completo' =>
                                $profesor->nombre_completo,

                            'especialidad' =>
                                $profesor->especialidad,

                            'institucion' =>
                                $profesor->institucion,

                            'label' =>
                                "{$profesor->codigo} - {$profesor->nombre_completo}",
                        ];
                    }
                );


        return response()->json([
            'success' =>
                true,

            'data' =>
                $profesores,
        ]);
    }


    // =========================================================
    // GRADOS
    // =========================================================

    /**
     * GET /api/publico/grados
     */
    public function listarGrados(
        Request $request
    ): JsonResponse {

        $request->validate([
            'institucion' =>
                'required|in:colegio,academia',

            'search' =>
                'nullable|string|max:100',
        ]);


        $query =
            Grado::where(
                'activo',
                true
            );


        // =====================================================
        // INSTITUCIÓN
        // =====================================================

        if (
            $request->institucion ===
            'colegio'
        ) {

            $query->whereIn(
                'nivel',
                [
                    'primaria',
                    'secundaria',
                ]
            );

        } else {

            $query->where(
                'nivel',
                'academia'
            );
        }


        // =====================================================
        // BÚSQUEDA
        // =====================================================

        if (
            $request->filled(
                'search'
            )
        ) {

            $search =
                $request->search;


            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'codigo',
                        'LIKE',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'nombre_completo',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'grado',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'seccion',
                            'LIKE',
                            "%{$search}%"
                        );
                }
            );
        }


        $grados =
            $query
                ->orderBy(
                    'nivel'
                )
                ->orderBy(
                    'grado'
                )
                ->orderBy(
                    'seccion'
                )
                ->limit(50)
                ->get()
                ->map(
                    function ($grado) {

                        return [
                            'id' =>
                                $grado->id,

                            'codigo' =>
                                $grado->codigo,

                            'nombre_completo' =>
                                $grado->nombre_completo,

                            'nivel' =>
                                $grado->nivel,

                            'turno' =>
                                $grado->turno,

                            'label' =>
                                "{$grado->codigo} - {$grado->nombre_completo}",
                        ];
                    }
                );


        return response()->json([
            'success' =>
                true,

            'data' =>
                $grados,
        ]);
    }


    // =========================================================
    // AULAS
    // =========================================================

    /**
     * GET /api/publico/aulas
     */
    public function listarAulas(
        Request $request
    ): JsonResponse {

        $request->validate([
            'institucion' =>
                'required|in:colegio,academia',

            'search' =>
                'nullable|string|max:100',
        ]);


        $query =
            Aula::where(
                'activo',
                true
            );


        // =====================================================
        // INSTITUCIÓN
        // =====================================================

        if (
            $request->institucion ===
            'colegio'
        ) {

            $query->where(
                function ($q) {

                    $q->whereIn(
                        'nivel',
                        [
                            'primaria',
                            'secundaria',
                        ]
                    )
                        ->orWhere(
                            'nivel',
                            'todos'
                        );
                }
            );

        } else {

            $query->where(
                function ($q) {

                    $q->where(
                        'nivel',
                        'academia'
                    )
                        ->orWhere(
                            'nivel',
                            'todos'
                        );
                }
            );
        }


        // =====================================================
        // BÚSQUEDA
        // =====================================================

        if (
            $request->filled(
                'search'
            )
        ) {

            $search =
                $request->search;


            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'codigo',
                        'LIKE',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'nombre',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'edificio',
                            'LIKE',
                            "%{$search}%"
                        );
                }
            );
        }


        $aulas =
            $query
                ->orderBy(
                    'nombre'
                )
                ->limit(50)
                ->get()
                ->map(
                    function ($aula) {

                        return [
                            'id' =>
                                $aula->id,

                            'codigo' =>
                                $aula->codigo,

                            'nombre' =>
                                $aula->nombre,

                            'tipo' =>
                                $aula->tipo,

                            'capacidad' =>
                                $aula->capacidad,

                            'label' =>
                                "{$aula->codigo} - {$aula->nombre}",
                        ];
                    }
                );


        return response()->json([
            'success' =>
                true,

            'data' =>
                $aulas,
        ]);
    }


    // =========================================================
    // CURSOS
    // =========================================================

    /**
     * GET /api/publico/cursos
     */
    public function listarCursos(
        Request $request
    ): JsonResponse {

        $request->validate([
            'institucion' =>
                'required|in:colegio,academia',

            'search' =>
                'nullable|string|max:100',
        ]);


        $query =
            Curso::where(
                'activo',
                true
            );


        // =====================================================
        // INSTITUCIÓN
        // =====================================================

        if (
            $request->institucion ===
            'colegio'
        ) {

            $query->where(
                function ($q) {

                    $q->whereIn(
                        'nivel',
                        [
                            'primaria',
                            'secundaria',
                        ]
                    )
                        ->orWhere(
                            'nivel',
                            'todos'
                        );
                }
            );

        } else {

            $query->where(
                function ($q) {

                    $q->where(
                        'nivel',
                        'academia'
                    )
                        ->orWhere(
                            'nivel',
                            'todos'
                        );
                }
            );
        }


        // =====================================================
        // BÚSQUEDA
        // =====================================================

        if (
            $request->filled(
                'search'
            )
        ) {

            $search =
                $request->search;


            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'codigo',
                        'LIKE',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'nombre',
                            'LIKE',
                            "%{$search}%"
                        );
                }
            );
        }


        $cursos =
            $query
                ->orderBy(
                    'nombre'
                )
                ->limit(50)
                ->get()
                ->map(
                    function ($curso) {

                        return [
                            'id' =>
                                $curso->id,

                            'codigo' =>
                                $curso->codigo,

                            'nombre' =>
                                $curso->nombre,

                            'nivel' =>
                                $curso->nivel,

                            'tipo' =>
                                $curso->tipo,

                            'label' =>
                                "{$curso->codigo} - {$curso->nombre}",
                        ];
                    }
                );


        return response()->json([
            'success' =>
                true,

            'data' =>
                $cursos,
        ]);
    }


    // =========================================================
    // HORARIO POR PROFESOR
    // =========================================================

    /**
     * GET /api/publico/horario/profesor/{id}
     */
    public function horarioProfesor(
        int $id
    ): JsonResponse {

        $profesor =
            Profesor::where(
                'estado',
                'activo'
            )
                ->find(
                    $id
                );


        if (!$profesor) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Profesor no encontrado o inactivo',
            ], 404);
        }


        $horarios =
            Horario::with([
                'curso',
                'aula',
                'grado',
            ])
                ->where(
                    'profesor_id',
                    $id
                )
                ->where(
                    'estado',
                    'activo'
                )
                ->orderBy(
                    'dia_semana'
                )
                ->orderBy(
                    'hora_inicio'
                )
                ->get();


        return response()->json([
            'success' =>
                true,

            'data' => [

                'tipo' =>
                    'profesor',

                'info' => [

                    'id' =>
                        $profesor->id,

                    'codigo' =>
                        $profesor->codigo,

                    'nombre_completo' =>
                        $profesor->nombre_completo,

                    'especialidad' =>
                        $profesor->especialidad,

                    'institucion' =>
                        $profesor->institucion,
                ],

                'carga_horaria_total' =>
                    $this->calcularCargaHoraria(
                        $horarios
                    ),

                'total_clases' =>
                    $horarios->count(),

                'horarios' =>
                    $this->organizarPorDia(
                        $horarios
                    ),
            ],
        ]);
    }


    // =========================================================
    // PDF PÚBLICO DE PROFESOR
    // =========================================================

    /**
     * GET
     * /api/publico/horario/profesor/{id}/pdf
     *
     * Ejemplo:
     * ?institucion=colegio
     */
    public function descargarPdfProfesor(
        Request $request,
        int $id
    ) {

        $request->validate([
            'institucion' =>
                'required|in:colegio,academia',
        ]);


        $profesor =
            Profesor::where(
                'estado',
                'activo'
            )
                ->find(
                    $id
                );


        if (!$profesor) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Profesor no encontrado o inactivo',
            ], 404);
        }


        $filtros = [

            'profesor_id' =>
                $profesor->id,

            'institucion' =>
                $request->institucion,
        ];


        $horarios =
            $this
                ->exportService
                ->prepareData(
                    $filtros
                );


        if (
            $horarios->isEmpty()
        ) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'El profesor no tiene horarios disponibles para esta institución.',
            ], 404);
        }


        $institucion =
            $request->institucion ===
            'colegio'
                ? 'Colegio'
                : 'Academia';


        $titulo =
            'Horario de ' .
            $profesor->nombre_completo .
            ' - ' .
            $institucion;


        return $this
            ->exportService
            ->downloadPdf(
                $horarios,
                $titulo,
                $filtros
            );
    }


    // =========================================================
    // HTML PÚBLICO PARA IMAGEN DE PROFESOR
    // =========================================================

    /**
     * GET
     * /api/publico/horario/profesor/{id}/imagen
     *
     * Este endpoint NO genera todavía el PNG.
     * Devuelve el HTML preparado para que el frontend
     * lo capture usando html2canvas.
     */
    public function imagenProfesor(
        Request $request,
        int $id
    ): JsonResponse {

        $request->validate([
            'institucion' =>
                'required|in:colegio,academia',
        ]);


        $profesor =
            Profesor::where(
                'estado',
                'activo'
            )
                ->find(
                    $id
                );


        if (!$profesor) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Profesor no encontrado o inactivo',
            ], 404);
        }


        $filtros = [

            'profesor_id' =>
                $profesor->id,

            'institucion' =>
                $request->institucion,
        ];


        $horarios =
            $this
                ->exportService
                ->prepareData(
                    $filtros
                );


        if (
            $horarios->isEmpty()
        ) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'El profesor no tiene horarios disponibles para esta institución.',
            ], 404);
        }


        $institucion =
            $request->institucion ===
            'colegio'
                ? 'Colegio'
                : 'Academia';


        $titulo =
            'Horario de ' .
            $profesor->nombre_completo .
            ' - ' .
            $institucion;


        $html =
            $this
                ->exportService
                ->getHtmlForImage(
                    $horarios,
                    $titulo,
                    $filtros
                );


        $nombreArchivo =
            'Horario_' .
            $profesor->codigo .
            '_' .
            $request->institucion .
            '.png';


        return response()->json([
            'success' =>
                true,

            'message' =>
                'HTML para imagen generado correctamente',

            'data' => [

                'html' =>
                    $html,

                'nombre_archivo' =>
                    $nombreArchivo,

                'total_registros' =>
                    $horarios->count(),

                'profesor' => [

                    'id' =>
                        $profesor->id,

                    'codigo' =>
                        $profesor->codigo,

                    'nombre_completo' =>
                        $profesor->nombre_completo,
                ],

                'institucion' =>
                    $request->institucion,
            ],
        ]);
    }


    // =========================================================
    // HORARIO POR GRADO
    // =========================================================

    /**
     * GET /api/publico/horario/grado/{id}
     */
    public function horarioGrado(
        int $id
    ): JsonResponse {

        $grado =
            Grado::where(
                'activo',
                true
            )
                ->find(
                    $id
                );


        if (!$grado) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Grado no encontrado o inactivo',
            ], 404);
        }


        $horarios =
            Horario::with([
                'profesor',
                'curso',
                'aula',
            ])
                ->where(
                    'grado_id',
                    $id
                )
                ->where(
                    'estado',
                    'activo'
                )
                ->orderBy(
                    'dia_semana'
                )
                ->orderBy(
                    'hora_inicio'
                )
                ->get();


        return response()->json([
            'success' =>
                true,

            'data' => [

                'tipo' =>
                    'grado',

                'info' => [

                    'id' =>
                        $grado->id,

                    'codigo' =>
                        $grado->codigo,

                    'nombre_completo' =>
                        $grado->nombre_completo,

                    'nivel' =>
                        $grado->nivel,

                    'turno' =>
                        $grado->turno,

                    'numero_estudiantes' =>
                        $grado->numero_estudiantes,
                ],

                'total_clases' =>
                    $horarios->count(),

                'horarios' =>
                    $this->organizarPorDia(
                        $horarios
                    ),
            ],
        ]);
    }


    // =========================================================
    // HORARIO POR AULA
    // =========================================================

    /**
     * GET /api/publico/horario/aula/{id}
     */
    public function horarioAula(
        int $id
    ): JsonResponse {

        $aula =
            Aula::where(
                'activo',
                true
            )
                ->find(
                    $id
                );


        if (!$aula) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Aula no encontrada o inactiva',
            ], 404);
        }


        $horarios =
            Horario::with([
                'profesor',
                'curso',
                'grado',
            ])
                ->where(
                    'aula_id',
                    $id
                )
                ->where(
                    'estado',
                    'activo'
                )
                ->orderBy(
                    'dia_semana'
                )
                ->orderBy(
                    'hora_inicio'
                )
                ->get();


        return response()->json([
            'success' =>
                true,

            'data' => [

                'tipo' =>
                    'aula',

                'info' => [

                    'id' =>
                        $aula->id,

                    'codigo' =>
                        $aula->codigo,

                    'nombre' =>
                        $aula->nombre,

                    'tipo' =>
                        $aula->tipo,

                    'capacidad' =>
                        $aula->capacidad,

                    'edificio' =>
                        $aula->edificio,

                    'piso' =>
                        $aula->piso,
                ],

                'total_clases' =>
                    $horarios->count(),

                'horarios' =>
                    $this->organizarPorDia(
                        $horarios
                    ),
            ],
        ]);
    }


    // =========================================================
    // HORARIO POR CURSO
    // =========================================================

    /**
     * GET /api/publico/horario/curso/{id}
     */
    public function horarioCurso(
        int $id
    ): JsonResponse {

        $curso =
            Curso::where(
                'activo',
                true
            )
                ->find(
                    $id
                );


        if (!$curso) {

            return response()->json([
                'success' =>
                    false,

                'message' =>
                    'Curso no encontrado o inactivo',
            ], 404);
        }


        $horarios =
            Horario::with([
                'profesor',
                'aula',
                'grado',
            ])
                ->where(
                    'curso_id',
                    $id
                )
                ->where(
                    'estado',
                    'activo'
                )
                ->orderBy(
                    'dia_semana'
                )
                ->orderBy(
                    'hora_inicio'
                )
                ->get();


        return response()->json([
            'success' =>
                true,

            'data' => [

                'tipo' =>
                    'curso',

                'info' => [

                    'id' =>
                        $curso->id,

                    'codigo' =>
                        $curso->codigo,

                    'nombre' =>
                        $curso->nombre,

                    'nivel' =>
                        $curso->nivel,

                    'tipo' =>
                        $curso->tipo,
                ],

                'total_clases' =>
                    $horarios->count(),

                'horarios' =>
                    $this->organizarPorDia(
                        $horarios
                    ),
            ],
        ]);
    }


    // =========================================================
    // ORGANIZAR POR DÍA
    // =========================================================

    /**
     * Organizar colección de horarios
     * por día de la semana.
     */
    protected function organizarPorDia(
        $horarios
    ): array {

        $resultado =
            [];


        foreach (
            $this->dias as $dia
        ) {

            $resultado[
                $dia
            ] =
                $horarios
                    ->where(
                        'dia_semana',
                        $dia
                    )
                    ->values();
        }


        return $resultado;
    }


    // =========================================================
    // CALCULAR CARGA HORARIA
    // =========================================================

    /**
     * Calcular horas totales.
     */
    protected function calcularCargaHoraria(
        $horarios
    ): float {

        $minutos =
            $horarios->sum(
                function ($horario) {

                    return \Carbon\Carbon::parse(
                        $horario->hora_inicio
                    )
                        ->diffInMinutes(
                            \Carbon\Carbon::parse(
                                $horario->hora_fin
                            )
                        );
                }
            );


        return round(
            $minutos /
            60,
            2
        );
    }
}