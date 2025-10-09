<?php

namespace App\Http\Controllers;

use App\Models\IntegranteHogar;
use App\Models\EstudioSocioeconomico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IntegranteHogarController extends Controller
{
    public function store(Request $request)
    {
        Log::debug('=== INICIANDO STORE INTEGRANTE ===');
        Log::debug('Datos recibidos:', $request->all());
        Log::debug('Headers:', $request->headers->all());

        try {
            $validated = $request->validate([
                'estudio_socioeconomico_id' => 'required|exists:estudio_socioeconomico,id',
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'edad' => 'required|integer|min:0',
                'parentesco' => 'required|string|max:100',
                'ingreso_mensual' => 'required|numeric|min:0'
            ]);

            Log::debug('Datos validados:', $validated);

            $integrante = IntegranteHogar::create($validated);
            Log::debug('Integrante creado exitosamente:', $integrante->toArray());

            return response()->json([
                'success' => 'Integrante agregado exitosamente',
                'integrante' => $integrante
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', $e->errors());
            return response()->json([
                'error' => 'Error de validación: ' . implode(', ', array_merge(...array_values($e->errors())))
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error general al crear integrante: ' . $e->getMessage());
            Log::error('Trace:', ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Error al crear el integrante del hogar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, IntegranteHogar $integrante)
    {
        $validated = $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'edad' => 'required|integer|min:0',
            'parentesco' => 'required|string|max:100',
            'ingreso_mensual' => 'required|numeric|min:0'
        ]);

        try {
            $integrante->update($validated);

            return response()->json([
                'success' => 'Integrante actualizado exitosamente',
                'integrante' => $integrante
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al actualizar el integrante del hogar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(IntegranteHogar $integrante)
    {
        try {
            $integrante->delete();

            return response()->json([
                'success' => 'Integrante eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al eliminar el integrante del hogar: ' . $e->getMessage()
            ], 500);
        }
    }
    public function __construct()
    {
        $this->middleware(['auth'])->except(['store']);
    }
}
