<?php
// app/Http/Controllers/Api/ConfiguracionHorarioController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionHorario;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfiguracionHorarioController extends Controller
{
    /**
     * GET /api/configuraciones-horario
     */
    public function index(Request $request): JsonResponse
    {
        $query = ConfiguracionHorario::with('bloques');

        if ($request->has('institucion')) {
            $query->where('institucion', $request->institucion);
        }

        if ($request->has('nivel')) {
            $query->where('nivel', $request->nivel);
        }

        if ($request->has('turno')) {
            $query->where('turno', $request->turno);
        }

        if ($request->has('año_academico')) {
            $query->where('año_academico', $request->año_academico);
        }

        if ($request->has('activo')) {
            $query->where('activo', $request->boolean('activo'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('institucion')
                           ->orderBy('nivel')
                           ->orderBy('turno')
                           ->get(),
        ]);
    }

    /**
     * GET /api/configuraciones-horario/{id}
     */
    public function show(int $id): JsonResponse
    {
        $config = ConfiguracionHorario::with('bloques')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $config,
        ]);
    }

    /**
     * POST /api/configuraciones-horario
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia',
            'nivel' => 'required|in:primaria,secundaria,academia',
            'turno' => 'required|in:mañana,tarde,completo',
            'nombre' => 'required|string|max:100',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_bloque_minutos' => 'required|integer|min:30|max:120',
            'año_academico' => 'required|integer|min:2020|max:2100',
            'bloques' => 'required|array|min:1',
            'bloques.*.orden' => 'required|integer',
            'bloques.*.hora_inicio' => 'required|date_format:H:i',
            'bloques.*.hora_fin' => 'required|date_format:H:i',
            'bloques.*.tipo' => 'required|in:clase,receso',
        ]);

        $config = ConfiguracionHorario::create($request->except('bloques'));

        foreach ($request->bloques as $bloque) {
            $config->bloques()->create($bloque);
        }

        return response()->json([
            'success' => true,
            'message' => 'Configuración creada exitosamente',
            'data' => $config->load('bloques'),
        ], 201);
    }

    /**
     * PUT /api/configuraciones-horario/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $config = ConfiguracionHorario::findOrFail($id);

        $request->validate([
            'institucion' => 'sometimes|in:colegio,academia',
            'nivel' => 'sometimes|in:primaria,secundaria,academia',
            'turno' => 'sometimes|in:mañana,tarde,completo',
            'nombre' => 'sometimes|string|max:100',
            'hora_inicio' => 'sometimes|date_format:H:i',
            'hora_fin' => 'sometimes|date_format:H:i|after:hora_inicio',
            'duracion_bloque_minutos' => 'sometimes|integer|min:30|max:120',
            'año_academico' => 'sometimes|integer',
            'bloques' => 'sometimes|array',
        ]);

        $config->update($request->except('bloques'));

        if ($request->has('bloques')) {
            $config->bloques()->delete();
            foreach ($request->bloques as $bloque) {
                $config->bloques()->create($bloque);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Configuración actualizada exitosamente',
            'data' => $config->fresh()->load('bloques'),
        ]);
    }

    /**
     * DELETE /api/configuraciones-horario/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $config = ConfiguracionHorario::findOrFail($id);

        // Verificar que no tenga horarios generados
        // (opcional: podrías bloquear la eliminación si hay horarios asociados)

        $config->delete();

        return response()->json([
            'success' => true,
            'message' => 'Configuración eliminada exitosamente',
        ]);
    }

    /**
     * GET /api/configuraciones-horario/vigente
     * Obtener la configuración vigente para una institución/nivel/turno
     */
    public function vigente(Request $request): JsonResponse
    {
        $request->validate([
            'institucion' => 'required|in:colegio,academia',
            'nivel' => 'required|in:primaria,secundaria,academia',
            'turno' => 'required|in:mañana,tarde,completo',
        ]);

        $config = ConfiguracionHorario::with('bloques')
            ->where('institucion', $request->institucion)
            ->where('nivel', $request->nivel)
            ->where('turno', $request->turno)
            ->where('activo', true)
            ->orderBy('año_academico', 'desc')
            ->first();

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'No hay configuración vigente para estos parámetros',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $config,
        ]);
    }
}
