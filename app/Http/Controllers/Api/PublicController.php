<?php
// app/Http/Controllers/Api/PublicController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\Profesor;
use App\Models\Grado;
use App\Models\Aula;
use App\Models\Curso;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    /**
     * Días de la semana para organizar horarios
     */
    protected array $dias = ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];

    /**
     * ========================================
     * DATOS PARA LOS DROPDOWNS
     * ========================================
     */

    /**
     * GET /api/publico/instituciones
     * Listar instituciones disponibles
     */
    public function instituciones(): JsonResponse
    {
        // Detectar qué instituciones tienen datos activos
        $instituciones = [];

        // Colegio (si hay grados de primaria/secundaria)
        $tieneColegio = Grado::where('activo', true)
            ->whereIn('nivel', ['primaria', 'secundaria'])
            ->exists();

        if ($tieneColegio) {
            $instituciones[] = [
                'value' => 'colegio',
                'label' => 'Colegio',
                'descripcion' => 'Primaria y Secundaria',
            ];
        }

        // Academia (si hay grados de academia)
        $tieneAcademia = Grado::where('activo', true)
            ->where('nivel', 'academia')
            ->exists();

        if ($tieneAcademia) {
            $instituciones[] = [
                'value' => 'academia',
                'label' => 'Academia',
                'descripcion' => 'Cursos de Academia',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $instituciones,
        ]);
    }

    /**
     * GET /api/publico/profesores?institucion=colegio&search=xxx
     * Listar profesores filtrados por institución
     * Búsqueda por nombre o código único
     */
    public function listarProfesores(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia',
            'search' => 'nullable|string|max:100',
        ]);

        $query = Profesor::where('estado', 'activo')
            ->where(function ($q) use ($request) {
                // Filtrar por institución (colegio/academia/ambos)
                $q->where('institucion', $request->institucion)
                  ->orWhere('institucion', 'ambos');
            });

        // Búsqueda por nombre o código
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('apellido_paterno', 'LIKE', "%{$search}%")
                  ->orWhere('apellido_materno', 'LIKE', "%{$search}%");
            });
        }

        $profesores = $query->orderBy('apellido_paterno')
            ->orderBy('nombre')
            ->limit(50)
            ->get()
            ->map(function ($profesor) {
                return [
                    'id' => $profesor->id,
                    'codigo' => $profesor->codigo,
                    'nombre_completo' => $profesor->nombre_completo,
                    'especialidad' => $profesor->especialidad,
                    'institucion' => $profesor->institucion,
                    'label' => "{$profesor->codigo} - {$profesor->nombre_completo}",
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $profesores,
        ]);
    }

    /**
     * GET /api/publico/grados?institucion=colegio&search=xxx
     * Listar grados filtrados por institución
     */
    public function listarGrados(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia',
            'search' => 'nullable|string|max:100',
        ]);

        $query = Grado::where('activo', true);

        // Filtrar por institución
        if ($request->institucion === 'colegio') {
            $query->whereIn('nivel', ['primaria', 'secundaria']);
        } else {
            $query->where('nivel', 'academia');
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('nombre_completo', 'LIKE', "%{$search}%")
                  ->orWhere('grado', 'LIKE', "%{$search}%")
                  ->orWhere('seccion', 'LIKE', "%{$search}%");
            });
        }

        $grados = $query->orderBy('nivel')
            ->orderBy('grado')
            ->orderBy('seccion')
            ->limit(50)
            ->get()
            ->map(function ($grado) {
                return [
                    'id' => $grado->id,
                    'codigo' => $grado->codigo,
                    'nombre_completo' => $grado->nombre_completo,
                    'nivel' => $grado->nivel,
                    'turno' => $grado->turno,
                    'label' => "{$grado->codigo} - {$grado->nombre_completo}",
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $grados,
        ]);
    }

    /**
     * GET /api/publico/aulas?institucion=colegio&search=xxx
     * Listar aulas filtradas por institución
     */
    public function listarAulas(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia',
            'search' => 'nullable|string|max:100',
        ]);

        $query = Aula::where('activo', true);

        // Filtrar por institución a través del nivel
        if ($request->institucion === 'colegio') {
            $query->where(function ($q) {
                $q->whereIn('nivel', ['primaria', 'secundaria'])
                  ->orWhere('nivel', 'todos');
            });
        } else {
            $query->where(function ($q) {
                $q->where('nivel', 'academia')
                  ->orWhere('nivel', 'todos');
            });
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('edificio', 'LIKE', "%{$search}%");
            });
        }

        $aulas = $query->orderBy('nombre')
            ->limit(50)
            ->get()
            ->map(function ($aula) {
                return [
                    'id' => $aula->id,
                    'codigo' => $aula->codigo,
                    'nombre' => $aula->nombre,
                    'tipo' => $aula->tipo,
                    'capacidad' => $aula->capacidad,
                    'label' => "{$aula->codigo} - {$aula->nombre}",
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $aulas,
        ]);
    }

    /**
     * GET /api/publico/cursos?institucion=colegio&search=xxx
     * Listar cursos filtrados por institución
     */
    public function listarCursos(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia',
            'search' => 'nullable|string|max:100',
        ]);

        $query = Curso::where('activo', true);

        // Filtrar por nivel según institución
        if ($request->institucion === 'colegio') {
            $query->where(function ($q) {
                $q->whereIn('nivel', ['primaria', 'secundaria'])
                  ->orWhere('nivel', 'todos');
            });
        } else {
            $query->where(function ($q) {
                $q->where('nivel', 'academia')
                  ->orWhere('nivel', 'todos');
            });
        }

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'LIKE', "%{$search}%")
                  ->orWhere('nombre', 'LIKE', "%{$search}%");
            });
        }

        $cursos = $query->orderBy('nombre')
            ->limit(50)
            ->get()
            ->map(function ($curso) {
                return [
                    'id' => $curso->id,
                    'codigo' => $curso->codigo,
                    'nombre' => $curso->nombre,
                    'nivel' => $curso->nivel,
                    'tipo' => $curso->tipo,
                    'label' => "{$curso->codigo} - {$curso->nombre}",
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $cursos,
        ]);
    }

    /**
     * ========================================
     * CONSULTAS DE HORARIOS
     * ========================================
     */

    /**
     * GET /api/publico/horario/profesor/{id}
     * Ver horario público de un profesor
     */
    public function horarioProfesor(int $id): JsonResponse
    {
        $profesor = Profesor::where('estado', 'activo')->find($id);

        if (!$profesor) {
            return response()->json([
                'success' => false,
                'message' => 'Profesor no encontrado o inactivo',
            ], 404);
        }

        $horarios = Horario::with(['curso', 'aula', 'grado'])
            ->where('profesor_id', $id)
            ->where('estado', 'activo')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tipo' => 'profesor',
                'info' => [
                    'id' => $profesor->id,
                    'codigo' => $profesor->codigo,
                    'nombre_completo' => $profesor->nombre_completo,
                    'especialidad' => $profesor->especialidad,
                    'institucion' => $profesor->institucion,
                ],
                'carga_horaria_total' => $this->calcularCargaHoraria($horarios),
                'total_clases' => $horarios->count(),
                'horarios' => $this->organizarPorDia($horarios),
            ],
        ]);
    }

    /**
     * GET /api/publico/horario/grado/{id}
     * Ver horario público de un grado
     */
    public function horarioGrado(int $id): JsonResponse
    {
        $grado = Grado::where('activo', true)->find($id);

        if (!$grado) {
            return response()->json([
                'success' => false,
                'message' => 'Grado no encontrado o inactivo',
            ], 404);
        }

        $horarios = Horario::with(['profesor', 'curso', 'aula'])
            ->where('grado_id', $id)
            ->where('estado', 'activo')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tipo' => 'grado',
                'info' => [
                    'id' => $grado->id,
                    'codigo' => $grado->codigo,
                    'nombre_completo' => $grado->nombre_completo,
                    'nivel' => $grado->nivel,
                    'turno' => $grado->turno,
                    'numero_estudiantes' => $grado->numero_estudiantes,
                ],
                'total_clases' => $horarios->count(),
                'horarios' => $this->organizarPorDia($horarios),
            ],
        ]);
    }

    /**
     * GET /api/publico/horario/aula/{id}
     * Ver horario público de un aula
     */
    public function horarioAula(int $id): JsonResponse
    {
        $aula = Aula::where('activo', true)->find($id);

        if (!$aula) {
            return response()->json([
                'success' => false,
                'message' => 'Aula no encontrada o inactiva',
            ], 404);
        }

        $horarios = Horario::with(['profesor', 'curso', 'grado'])
            ->where('aula_id', $id)
            ->where('estado', 'activo')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tipo' => 'aula',
                'info' => [
                    'id' => $aula->id,
                    'codigo' => $aula->codigo,
                    'nombre' => $aula->nombre,
                    'tipo' => $aula->tipo,
                    'capacidad' => $aula->capacidad,
                    'edificio' => $aula->edificio,
                    'piso' => $aula->piso,
                ],
                'total_clases' => $horarios->count(),
                'horarios' => $this->organizarPorDia($horarios),
            ],
        ]);
    }

    /**
     * GET /api/publico/horario/curso/{id}
     * Ver horario público de un curso
     */
    public function horarioCurso(int $id): JsonResponse
    {
        $curso = Curso::where('activo', true)->find($id);

        if (!$curso) {
            return response()->json([
                'success' => false,
                'message' => 'Curso no encontrado o inactivo',
            ], 404);
        }

        $horarios = Horario::with(['profesor', 'aula', 'grado'])
            ->where('curso_id', $id)
            ->where('estado', 'activo')
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tipo' => 'curso',
                'info' => [
                    'id' => $curso->id,
                    'codigo' => $curso->codigo,
                    'nombre' => $curso->nombre,
                    'nivel' => $curso->nivel,
                    'tipo' => $curso->tipo,
                ],
                'total_clases' => $horarios->count(),
                'horarios' => $this->organizarPorDia($horarios),
            ],
        ]);
    }

    /**
     * ========================================
     * MÉTODOS AUXILIARES
     * ========================================
     */

    /**
     * Organizar horarios por día de la semana
     */
    protected function organizarPorDia($horarios): array
    {
        $resultado = [];

        foreach ($this->dias as $dia) {
            $resultado[$dia] = $horarios->where('dia_semana', $dia)->values();
        }

        return $resultado;
    }

    /**
     * Calcular carga horaria total
     */
    protected function calcularCargaHoraria($horarios): float
    {
        $minutos = $horarios->sum(function ($h) {
            return \Carbon\Carbon::parse($h->hora_inicio)
                ->diffInMinutes(\Carbon\Carbon::parse($h->hora_fin));
        });

        return round($minutos / 60, 2);
    }
}
