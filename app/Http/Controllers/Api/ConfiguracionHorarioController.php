<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionHorario;
use App\Services\HorarioRecesoService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConfiguracionHorarioController extends Controller
{
    public function __construct(private HorarioRecesoService $recesoService) {}

    public function index(Request $request): JsonResponse
    {
        $query = ConfiguracionHorario::with('bloques');
        foreach (['institucion', 'nivel', 'turno', 'año_academico'] as $campo) {
            if ($request->filled($campo)) {
                $query->where($campo, $request->input($campo));
            }
        }
        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }
        return response()->json(['success' => true, 'data' => $query->orderBy('institucion')->orderBy('nivel')->orderBy('turno')
            ->get()->map(fn ($config) => $this->recesoService->normalizarConfiguracion($config)), 'recesos_fijos' => true]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $this->recesoService->normalizarConfiguracion(ConfiguracionHorario::findOrFail($id))]);
    }

    public function store(Request $request): JsonResponse
    {
        return $this->rechazarEdicion();
    }

    public function update(Request $request, int $id): JsonResponse
    {
        return $this->rechazarEdicion();
    }

    public function destroy(int $id): JsonResponse
    {
        return $this->rechazarEdicion();
    }

    private function rechazarEdicion(): never
    {
        throw ValidationException::withMessages(['configuracion' => 'Los recesos institucionales son fijos y no se pueden editar ni eliminar.']);
    }

    public function vigente(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia', 'nivel' => 'required|in:primaria,secundaria,academia',
            'turno' => 'required|in:mañana,tarde,completo',
        ]);
        $config = $this->recesoService->configuracionVigente($request->institucion, $request->nivel, $request->turno);
        return $config ? response()->json(['success' => true, 'data' => $config])
            : response()->json(['success' => false, 'message' => 'No hay jornada vigente para estos parametros'], 404);
    }

    public function recesos(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'sometimes|in:colegio,academia', 'nivel' => 'sometimes|in:primaria,secundaria,academia',
            'turno' => 'sometimes|in:mañana,tarde,completo', 'alcance' => 'sometimes|in:disponibilidad',
        ]);
        $recesos = $request->input('alcance') === 'disponibilidad'
            ? $this->recesoService->recesosParaDisponibilidad($request->input('institucion', 'colegio'), $request->input('nivel'), $request->input('turno'))
            : $this->recesoService->recesosVigentes($request->input('institucion'), $request->input('nivel'), $request->input('turno'));
        $data = $recesos->map(fn ($receso) => [
            'id' => null, 'nombre' => $receso->nombre, 'hora_inicio' => Carbon::parse($receso->hora_inicio)->format('H:i'),
            'hora_fin' => Carbon::parse($receso->hora_fin)->format('H:i'),
        ])->values();
        return response()->json(['success' => true, 'data' => $data, 'total' => $data->count(), 'recesos_fijos' => true]);
    }
}
