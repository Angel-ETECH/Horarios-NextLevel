<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecuperacionHorario;
use App\Services\HorarioCalendarioService;
use App\Services\RecuperacionHorarioService;
use Illuminate\Http\Request;

class RecuperacionHorarioController extends Controller
{
    public function __construct(private RecuperacionHorarioService $service, private HorarioCalendarioService $calendario) {}

    public function index(Request $request)
    {
        if (!$this->calendario->instalado()) return response()->json(['success' => true, 'data' => [], 'instalado' => false]);
        $query = RecuperacionHorario::with(['horario.profesor', 'horario.curso', 'horario.grado', 'aula']);
        $request->validate(['vigentes' => 'sometimes|boolean']);
        if ($request->boolean('vigentes')) {
            $query->where('estado', 'programada')->whereDate('fecha_recuperacion', '>=', today());
        } else {
            $semana = $this->calendario->inicioSemana($request->input('semana_inicio', now()->toDateString()));
            $query->where(fn ($q) => $q->whereBetween('fecha_original', [$semana->toDateString(), $semana->addDays(6)->toDateString()])
                ->orWhereBetween('fecha_recuperacion', [$semana->toDateString(), $semana->addDays(6)->toDateString()]));
        }
        return response()->json(['success' => true, 'data' => $query->orderBy('fecha_recuperacion')->get(), 'instalado' => true]);
    }

    private function validar(Request $request, bool $buscar = false): array
    {
        return $request->validate([
            'horario_id' => 'required|integer|exists:horarios,id', 'fecha_original' => 'required|date_format:Y-m-d',
            'fecha_recuperacion' => 'required|date_format:Y-m-d|after_or_equal:today|after_or_equal:fecha_original',
            'aula_id' => 'required|integer|exists:aulas,id',
            'hora_inicio' => ($buscar ? 'sometimes' : 'required').'|date_format:H:i',
            'buscar_desde' => ($buscar ? 'required' : 'sometimes').'|date_format:H:i',
            'buscar_hasta' => ($buscar ? 'required' : 'sometimes').'|date_format:H:i|after:buscar_desde',
            'disponibilidad_excepcional' => 'required|boolean', 'jornada_autorizada' => 'required|boolean',
            'disponible_desde' => 'nullable|required_if:disponibilidad_excepcional,true|date_format:H:i',
            'disponible_hasta' => 'nullable|required_if:disponibilidad_excepcional,true|date_format:H:i|after:disponible_desde',
            'motivo' => ($buscar ? 'sometimes' : 'required').'|string|min:5|max:255',
        ]);
    }

    public function opciones(Request $request)
    {
        return response()->json(['success' => true, 'data' => $this->service->opciones($this->validar($request, true))]);
    }

    public function store(Request $request)
    {
        return response()->json(['success' => true, 'data' => $this->service->crear($this->validar($request))], 201);
    }

    public function cancelar(Request $request, int $id)
    {
        $datos = $request->validate(['motivo' => 'required|string|min:5|max:255']);
        return response()->json(['success' => true, 'data' => $this->service->cancelar($id, $datos['motivo'])]);
    }
}
