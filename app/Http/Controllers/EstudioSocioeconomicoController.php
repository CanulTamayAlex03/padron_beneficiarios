<?php

namespace App\Http\Controllers;

use App\Models\EstudioSocioeconomico;
use App\Models\Beneficiario;
use App\Models\Region;
use App\Models\Solicitud;
use App\Models\Programa;
use App\Models\TipoPrograma;
use Illuminate\Http\Request;
use App\Models\Ocupacion;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\LineaConeval;
use App\Models\ServicioSalud;
use App\Models\Escolaridad;
use Illuminate\Support\Facades\Log;

class EstudioSocioeconomicoController extends Controller
{
    public function index()
    {
        $estudios = EstudioSocioeconomico::with([
            'beneficiario',
            'region',
            'solicitud',
            'programa',
            'tipoPrograma',
            'lineaConeval'
        ])->paginate(10);

        return view('estudios-socioeconomicos.index', compact('estudios'));
    }

    public function create($beneficiarioId, EstudioSocioeconomico $estudio = null)
    {
        $beneficiario = Beneficiario::findOrFail($beneficiarioId);

        $regiones = Region::activas()->get();
        $solicitudes = Solicitud::all();
        $programas = Programa::with('tiposPrograma')->get();
        $tiposPrograma = TipoPrograma::all();

        $lineasConeval = LineaConeval::where('periodo', '2025-03-01')->get();

        $serviciosSalud = ServicioSalud::all();
        $escolaridades = Escolaridad::all();

        return view('estudios.create', compact(
            'beneficiario',
            'estudio',
            'regiones',
            'solicitudes',
            'programas',
            'tiposPrograma',
            'lineasConeval',
            'serviciosSalud',
            'escolaridades'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'folio' => 'required|unique:estudio_socioeconomico,folio',
            'fecha_solicitud' => 'required|date',
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'region_id' => 'required|exists:region,id',
            'solicitud_id' => 'required|exists:solicitud,id',
            'programa_id' => 'required|exists:programa,id',
            'tipo_programa_id' => 'required|exists:tipo_programa,id',
            'linea_coneval_id' => 'nullable|exists:lineas_coneval,id',

            'tipo_piso' => 'nullable|string|max:50',
            'tipo_techo' => 'nullable|string|max:50',
            'agua_alimentos' => 'nullable|string|max:50',
            'medio_cocina' => 'nullable|string|max:50',
            'vivienda' => 'nullable|string|max:50',
            'servicio_sanitario' => 'nullable|string|max:50',
            'electricidad' => 'nullable|boolean',
            'cuartos_dormir' => 'nullable|integer|min:0',
            'razon_mayor' => 'nullable|boolean',
        ]);

        try {
            EstudioSocioeconomico::create($validated);

            return redirect()->route('beneficiarios')
                ->with('success', 'Estudio socioeconómico creado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear estudio socioeconómico: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al crear el estudio socioeconómico: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(EstudioSocioeconomico $estudio)
    {
        $estudio->load([
            'beneficiario',
            'region',
            'solicitud',
            'programa',
            'tipoPrograma',
            'lineaConeval'
        ]);

        return view('estudios-socioeconomicos.show', compact('estudio'));
    }

    public function editarCompleto(Beneficiario $beneficiario, EstudioSocioeconomico $estudio)
    {
        if ($estudio->beneficiario_id != $beneficiario->id) {
            abort(404, 'El estudio no pertenece a este beneficiario');
        }

        $estudio->load(['integrantesHogar', 'lineaConeval']);
        $totalPersonas = $estudio->integrantesHogar->count();

        $estudios = $beneficiario->estudiosSocioeconomicos()->orderBy('created_at', 'desc')->get();

        $regiones = Region::activas()->get();
        $solicitudes = Solicitud::all();
        $programas = Programa::with('tiposPrograma')->get();
        $tiposPrograma = TipoPrograma::all();

        $ocupaciones = Ocupacion::where('activo', 1)->orderBy('ocupacion')->get();
        $estados = Estado::orderBy('nombre')->get();
        $municipios = Municipio::orderBy('descripcion')->get();

        $lineasConeval = LineaConeval::where('periodo', '2025-03-01')->get();

        $serviciosSalud = ServicioSalud::all();
        $escolaridades = Escolaridad::all();

        return view('beneficiarios.editar-completo', compact(
            'beneficiario',
            'estudio',
            'estudios',
            'regiones',
            'solicitudes',
            'programas',
            'tiposPrograma',
            'ocupaciones',
            'estados',
            'municipios',
            'lineasConeval',
            'serviciosSalud',
            'escolaridades',
            'totalPersonas',
        ));
    }

    public function update(Request $request, EstudioSocioeconomico $estudio)
    {
        $validated = $request->validate([
            'folio' => 'required|unique:estudio_socioeconomico,folio,' . $estudio->id,
            'fecha_solicitud' => 'required|date',
            'beneficiario_id' => 'required|exists:beneficiarios,id',
            'region_id' => 'required|exists:region,id',
            'solicitud_id' => 'required|exists:solicitud,id',
            'programa_id' => 'required|exists:programa,id',
            'tipo_programa_id' => 'required|exists:tipo_programa,id',
            'linea_coneval_id' => 'nullable|exists:lineas_coneval,id',

            'tipo_piso' => 'nullable|string|max:50',
            'tipo_techo' => 'nullable|string|max:50',
            'agua_alimentos' => 'nullable|string|max:50',
            'medio_cocina' => 'nullable|string|max:50',
            'vivienda' => 'nullable|string|max:50',
            'servicio_sanitario' => 'nullable|string|max:50',
            'electricidad' => 'nullable|boolean',
            'cuartos_dormir' => 'nullable|integer|min:0',
            'razon_mayor' => 'nullable|boolean',
        ]);

        try {
            $estudio->update($validated);

            return redirect()->route('beneficiarios.estudios.editar', [$estudio->beneficiario_id, $estudio->id])
                ->with('success', 'Estudio socioeconómico actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar estudio socioeconómico: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al actualizar el estudio socioeconómico: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(EstudioSocioeconomico $estudio)
    {
        try {
            $estudio->delete();

            return redirect()->route('estudios.index')
                ->with('success', 'Estudio socioeconómico eliminado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar estudio socioeconómico: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al eliminar el estudio socioeconómico: ' . $e->getMessage());
        }
    }

    public function getTiposPrograma($programaId)
    {
        try {
            $programa = Programa::with('tiposPrograma')->find($programaId);

            if (!$programa) {
                return response()->json(['error' => 'Programa no encontrado'], 404);
            }

            return response()->json($programa->tiposPrograma);
        } catch (\Exception $e) {
            Log::error('Error al obtener tipos de programa: ' . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }


    public function updateConeval(Request $request, EstudioSocioeconomico $estudio)
    {
        $validated = $request->validate([
            'linea_coneval_id' => 'required|exists:lineas_coneval,id',
            'coneval_active' => 'required|boolean'
        ]);

        try {
            $estudio->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Datos CONEVAL actualizados correctamente',
                'data' => [
                    'linea_coneval_id' => $estudio->linea_coneval_id,
                    'coneval_active' => $estudio->coneval_active,
                    'linea_coneval' => $estudio->lineaConeval
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar datos CONEVAL: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar los datos CONEVAL: ' . $e->getMessage()
            ], 500);
        }
    }
}
