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
            'coneval_active' => 'nullable|boolean',
            'servicio_salud_id' => 'nullable|exists:servicio_salud,id',
            'escolaridad_id' => 'nullable|exists:escolaridad,id',

            'tipo_piso' => 'nullable|string|max:50',
            'tipo_techo' => 'nullable|string|max:50',
            'agua_alimentos' => 'nullable|string|max:50',
            'medio_cocina' => 'nullable|string|max:50',
            'vivienda' => 'nullable|string|max:50',
            'servicio_sanitario' => 'nullable|string|max:50',
            'electricidad' => 'nullable|boolean',
            'cuartos_dormir' => 'nullable|integer|min:0',
            'razon_mayor' => 'nullable|boolean',

            'preocupa_sin_alimentos' => 'nullable|boolean',
            'alimentos_no_alcanzaron' => 'nullable|boolean',
            'dieta_poco_variada_adultos' => 'nullable|boolean',
            'adultos_comieron_menos' => 'nullable|boolean',
            'adultos_hambre_sin_comer' => 'nullable|boolean',
            'adultos_dejaron_comer_dia' => 'nullable|boolean',
            'menores_dieta_poco_variada' => 'nullable|boolean',
            'menores_comieron_menos' => 'nullable|boolean',
            'menores_hambre_sin_comer' => 'nullable|boolean',
            'menores_sin_comer_dia' => 'nullable|boolean',
            'menores_sin_alimentos_saludables' => 'nullable|boolean',
            'menores_dieta_alimentos_baratos' => 'nullable|boolean'
        ]);

        try {
            $resultados = $this->calcularResultadosEstudios(new EstudioSocioeconomico(), $validated);

            $datosCompletos = array_merge($validated, $resultados);

            $estudio = EstudioSocioeconomico::create($datosCompletos);

            return redirect()->route('beneficiarios.estudios.editar', [
                'beneficiario' => $estudio->beneficiario_id,
                'estudio' => $estudio->id
            ])->with('success', 'Estudio socioeconómico creado exitosamente. Complete la información restante.');
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
            'coneval_active' => 'nullable|boolean',
            'servicio_salud_id' => 'nullable|exists:servicio_salud,id',
            'escolaridad_id' => 'nullable|exists:escolaridad,id',

            'tipo_piso' => 'nullable|string|max:50',
            'tipo_techo' => 'nullable|string|max:50',
            'agua_alimentos' => 'nullable|string|max:50',
            'medio_cocina' => 'nullable|string|max:50',
            'vivienda' => 'nullable|string|max:50',
            'servicio_sanitario' => 'nullable|string|max:50',
            'electricidad' => 'nullable|boolean',
            'cuartos_dormir' => 'nullable|integer|min:0',
            'razon_mayor' => 'nullable|boolean',

            'preocupa_sin_alimentos' => 'nullable|boolean',
            'alimentos_no_alcanzaron' => 'nullable|boolean',
            'dieta_poco_variada_adultos' => 'nullable|boolean',
            'adultos_comieron_menos' => 'nullable|boolean',
            'adultos_hambre_sin_comer' => 'nullable|boolean',
            'adultos_dejaron_comer_dia' => 'nullable|boolean',
            'menores_dieta_poco_variada' => 'nullable|boolean',
            'menores_comieron_menos' => 'nullable|boolean',
            'menores_hambre_sin_comer' => 'nullable|boolean',
            'menores_sin_comer_dia' => 'nullable|boolean',
            'menores_sin_alimentos_saludables' => 'nullable|boolean',
            'menores_dieta_alimentos_baratos' => 'nullable|boolean'
        ]);

        try {
            $resultados = $this->calcularResultadosEstudios($estudio, $validated);

            $datosActualizados = array_merge($validated, $resultados);

            $estudio->update($datosActualizados);

            return redirect()->route('beneficiarios.estudios.editar', [$estudio->beneficiario_id, $estudio->id])
                ->with('success', 'Estudio socioeconómico actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar estudio socioeconómico: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Error al actualizar el estudio socioeconómico: ' . $e->getMessage())
                ->withInput();
        }

        $request->merge([
            'coneval_active' => $request->input('coneval_active', 0),
        ]);
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

    private function calcularResultadosEstudios($estudio, $datos)
    {
        $resultados = [];

        $resultados['res_estudio_1'] = $this->calcularEstudio1($estudio, $datos);

        $resultados['res_estudio_2'] = $this->calcularEstudio2($estudio, $datos);

        $resultados['res_estudio_3'] = $this->calcularEstudio3($estudio, $datos);

        $resultados['res_total'] = $this->calcularResultadoTotal($estudio, $resultados);

        return $resultados;
    }

    private function calcularEstudio1($estudio, $datos)
    {
        $puntosConeval = isset($datos['coneval_active']) && $datos['coneval_active'] ? 3 : 0;

        $puntosServicioSalud = 0;
        if (isset($datos['servicio_salud_id']) && $datos['servicio_salud_id']) {
            $servicio = ServicioSalud::find($datos['servicio_salud_id']);
            $puntosServicioSalud = $servicio ? $servicio->puntos : 0;
        }

        $puntosEscolaridad = 0;
        if (isset($datos['escolaridad_id']) && $datos['escolaridad_id']) {
            $escolaridad = Escolaridad::find($datos['escolaridad_id']);
            $puntosEscolaridad = $escolaridad ? $escolaridad->puntos : 0;
        }

        $puntosTotales = $puntosConeval + $puntosServicioSalud + $puntosEscolaridad;

        if ($puntosTotales >= 1 && $puntosTotales <= 3) {
            return 'Leve';
        } elseif ($puntosTotales >= 4 && $puntosTotales <= 6) {
            return 'Moderada';
        } elseif ($puntosTotales >= 7 && $puntosTotales <= 9) {
            return 'Severa';
        } else {
            return 'No aplica';
        }
    }

    private function calcularEstudio2($estudio, $datos)
    {
        $calcularPuntosVivienda = function ($valor) {
            $puntos = [
                'Tierra' => 3,
                'Cemento' => 2,
                'Mosaico, madera, otro' => 1,
                'Cantón, llantas, huano' => 3,
                'Asbesto, madera, lamina' => 2,
                'Cemento, piedra, block' => 1,
                'Pozo' => 3,
                'De la llave' => 2,
                'Purificada' => 1,
                'Carbón, leña' => 3,
                'Gas' => 2,
                'Parrilla eléctrica, otra' => 1,
                'Rentada' => 3,
                'Prestada' => 2,
                'Propia o familiar' => 1,
                'Ningún servicio' => 3,
                'Letrina' => 2,
                'Inodoro' => 1
            ];
            return $puntos[$valor] ?? 0;
        };

        $puntosElectricidad = (isset($datos['electricidad']) && $datos['electricidad'] == 0) ? 1 : 0;
        $puntosPiso = isset($datos['tipo_piso']) ? $calcularPuntosVivienda($datos['tipo_piso']) : 0;
        $puntosTecho = isset($datos['tipo_techo']) ? $calcularPuntosVivienda($datos['tipo_techo']) : 0;
        $puntosAgua = isset($datos['agua_alimentos']) ? $calcularPuntosVivienda($datos['agua_alimentos']) : 0;
        $puntosCocina = isset($datos['medio_cocina']) ? $calcularPuntosVivienda($datos['medio_cocina']) : 0;
        $puntosVivienda = isset($datos['vivienda']) ? $calcularPuntosVivienda($datos['vivienda']) : 0;
        $puntosSanitario = isset($datos['servicio_sanitario']) ? $calcularPuntosVivienda($datos['servicio_sanitario']) : 0;
        $puntosRazon = (isset($datos['razon_mayor']) && $datos['razon_mayor'] == 1) ? 2 : 0;

        $totalVivienda = $puntosElectricidad + $puntosPiso + $puntosTecho + $puntosAgua +
            $puntosCocina + $puntosVivienda + $puntosSanitario + $puntosRazon;

        if ($totalVivienda >= 16) {
            return 'Severa';
        } elseif ($totalVivienda >= 11) {
            return 'Moderada';
        } elseif ($totalVivienda >= 6) {
            return 'Leve';
        } else {
            return 'No aplica';
        }
    }

    private function calcularEstudio3($estudio, $datos)
    {
        $camposBloque1 = [
            'preocupa_sin_alimentos',
            'alimentos_no_alcanzaron',
            'dieta_poco_variada_adultos',
            'adultos_comieron_menos',
            'adultos_hambre_sin_comer',
            'adultos_dejaron_comer_dia'
        ];

        $camposBloque2 = [
            'menores_dieta_poco_variada',
            'menores_comieron_menos',
            'menores_hambre_sin_comer',
            'menores_sin_comer_dia',
            'menores_sin_alimentos_saludables',
            'menores_dieta_alimentos_baratos'
        ];

        $puntosBloque1 = 0;
        foreach ($camposBloque1 as $campo) {
            $puntosBloque1 += (isset($datos[$campo]) && $datos[$campo] == 1) ? 1 : 0;
        }

        $hayDatosBloque2 = false;
        foreach ($camposBloque2 as $campo) {
            if (isset($datos[$campo]) && !is_null($datos[$campo])) {
                $hayDatosBloque2 = true;
                break;
            }
        }

        $puntosTotales = $puntosBloque1;

        if ($hayDatosBloque2) {
            $puntosBloque2 = 0;
            foreach ($camposBloque2 as $campo) {
                $puntosBloque2 += (isset($datos[$campo]) && $datos[$campo] == 1) ? 1 : 0;
            }
            $puntosTotales += $puntosBloque2;

            if ($puntosTotales >= 8) {
                return 'Severa';
            } elseif ($puntosTotales >= 4) {
                return 'Moderada';
            } elseif ($puntosTotales >= 1) {
                return 'Leve';
            } else {
                return 'No aplica';
            }
        } else {
            if ($puntosTotales >= 5) {
                return 'Severa';
            } elseif ($puntosTotales >= 3) {
                return 'Moderada';
            } elseif ($puntosTotales >= 1) {
                return 'Leve';
            } else {
                return 'No aplica';
            }
        }
    }

    private function calcularResultadoTotal($estudio, $resultados)
    {
        $severidades = [
            'Severa' => 3,
            'Moderada' => 2,
            'Leve' => 1,
            'No aplica' => 0
        ];

        $puntosEstudios = 0;
        $estudiosValidos = 0;

        foreach (['res_estudio_1', 'res_estudio_2', 'res_estudio_3'] as $estudioKey) {
            if (isset($resultados[$estudioKey]) && $resultados[$estudioKey] !== 'No aplica') {
                $puntosEstudios += $severidades[$resultados[$estudioKey]];
                $estudiosValidos++;
            }
        }

        $puntosOcupacion = 0;

        if (!$estudio->relationLoaded('beneficiario')) {
            $estudio->load('beneficiario.ocupacion');
        }

        if (
            $estudio->beneficiario &&
            $estudio->beneficiario->ocupacion &&
            !is_null($estudio->beneficiario->ocupacion->puntos)
        ) {
            $puntosOcupacion = min((int) $estudio->beneficiario->ocupacion->puntos, 3);
        }

        $puntosTotales = $puntosEstudios + $puntosOcupacion;

        if ($estudiosValidos === 0 && $puntosOcupacion === 0) {
            return 'No aplica';
        }

        if ($puntosTotales >= 10 && $puntosTotales <= 12) {
            return 'Severa';
        } elseif ($puntosTotales >= 7 && $puntosTotales <= 9) {
            return 'Moderada';
        } elseif ($puntosTotales >= 4 && $puntosTotales <= 6) {
            return 'Leve';
        } else {
            return 'No aplica';
        }
    }

    public function vistaResultado(EstudioSocioeconomico $estudio, Request $request)
    {
        $index = $request->get('index', 1);

        return view('componentes.resultado-estudio', [
            'estudio' => $estudio->load(['beneficiario.ocupacion']),
            'index' => $index
        ]);
    }
}
