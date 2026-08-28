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

        try {
            $validated = $request->validate([
                'estudio_socioeconomico_id' => 'required|exists:estudio_socioeconomico,id',
                'integrante' => 'required|integer|min:1',
                'ingreso_mensual' => 'required|numeric|min:0'
            ]);

            Log::debug('Datos validados:', $validated);

            $existe = IntegranteHogar::where('estudio_socioeconomico_id', $validated['estudio_socioeconomico_id'])
                ->where('integrante', $validated['integrante'])
                ->exists();

            if ($existe) {
                return response()->json([
                    'error' => 'Ya existe un integrante con el número ' . $validated['integrante']
                ], 422);
            }

            $integrante = IntegranteHogar::create($validated);
            Log::debug('Integrante creado exitosamente:', $integrante->toArray());

            $this->recalcularResultadosEstudio($validated['estudio_socioeconomico_id']);

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

        try {
            $validated = $request->validate([
                'integrante' => 'sometimes|integer|min:1',
                'ingreso_mensual' => 'required|numeric|min:0'
            ]);

            if (isset($validated['integrante'])) {
                $existe = IntegranteHogar::where('estudio_socioeconomico_id', $integrante->estudio_socioeconomico_id)
                    ->where('integrante', $validated['integrante'])
                    ->where('id', '!=', $integrante->id)
                    ->exists();

                if ($existe) {
                    return response()->json([
                        'error' => 'Ya existe un integrante con el número ' . $validated['integrante']
                    ], 422);
                }
            }

            $integrante->update($validated);
            Log::debug('Integrante actualizado exitosamente:', $integrante->toArray());

            $this->recalcularResultadosEstudio($integrante->estudio_socioeconomico_id);

            return response()->json([
                'success' => 'Integrante actualizado exitosamente',
                'integrante' => $integrante
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación:', $e->errors());
            return response()->json([
                'error' => 'Error de validación: ' . implode(', ', array_merge(...array_values($e->errors())))
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al actualizar integrante: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al actualizar el integrante del hogar: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(IntegranteHogar $integrante)
    {
        Log::debug('=== INICIANDO DESTROY INTEGRANTE ===');
        Log::debug('Integrante ID:', ['id' => $integrante->id]);

        try {
            $estudioId = $integrante->estudio_socioeconomico_id;
            $integrante->delete();

            Log::debug('Integrante eliminado exitosamente');

            $this->recalcularResultadosEstudio($estudioId);

            return response()->json([
                'success' => 'Integrante eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar integrante: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error al eliminar el integrante del hogar: ' . $e->getMessage()
            ], 500);
        }
    }

    private function recalcularResultadosEstudio($estudioId)
    {
        try {
            $estudio = EstudioSocioeconomico::with('integrantesHogar')->find($estudioId);

            if (!$estudio) {
                Log::warning('Estudio no encontrado para recalcular:', ['id' => $estudioId]);
                return;
            }

            $controller = new EstudioSocioeconomicoController();

            if (method_exists($controller, 'calcularResultadosEstudios')) {
                $datos = $estudio->toArray();
                $resultados = $controller->calcularResultadosEstudios($estudio, $datos);
                $estudio->update($resultados);
                Log::debug('Resultados recalculados para estudio:', ['id' => $estudioId]);
            }
        } catch (\Exception $e) {

            Log::error('Error al recalcular resultados del estudio: ' . $e->getMessage());
        }
    }

    public function __construct()
    {
        $this->middleware(['auth'])->except(['store']);
    }
}
