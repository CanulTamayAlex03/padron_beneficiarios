<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Ocupacion;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parentesco;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use App\Models\EstudioSocioeconomico;

class BeneficiarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Beneficiario::query()->with(['estudiosSocioeconomicos', 'estado', 'ocupacion']);

        if ($request->has('curp') && !empty($request->curp)) {
            $query->where('curp', 'like', '%' . $request->curp . '%');
        }

        $query->orderBy('id', 'desc');

        if ($request->ajax()) {
            $beneficiarios = $query->get();
            return response()->json(['data' => $beneficiarios]);
        }

        $beneficiarios = $query->paginate(20);

        $ocupaciones = Ocupacion::where('activo', 1)->orderBy('ocupacion')->get();
        $estados = Estado::orderBy('nombre')->get();
        $municipios = Municipio::orderBy('descripcion')->get();

        return view('beneficiarios', compact('beneficiarios', 'ocupaciones', 'estados', 'municipios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres'          => 'required|string|max:100',
            'primer_apellido'  => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'curp'             => 'nullable|string|size:18|unique:beneficiarios,curp',
            'fecha_nac'        => 'required|date',
            'sexo'             => 'nullable|string|max:100',
            'discapacidad'     => 'boolean',
            'indigena'         => 'boolean',
            'maya_hablante'    => 'boolean',
            'afromexicano'     => 'boolean',
            'estado_civil'     => 'nullable|string|max:100',
            'ocupacion_id'     => 'nullable|integer|exists:ocupaciones,id',
            'estado_id'        => 'nullable|integer|exists:estados,id_estado',

            'calle'                  => 'nullable|string|max:255',
            'numero'                 => 'nullable|string|max:10',
            'letra'                  => 'nullable|string|max:5',
            'cruzamiento_1'          => 'nullable|string|max:255',
            'cruzamiento_2'          => 'nullable|string|max:255',
            'tipo_asentamiento'      => 'nullable|string|max:100',
            'estado_viv_id'          => 'nullable|integer|exists:estados,id_estado',
            'municipio_id'           => 'nullable|integer|exists:municipios,id',
            'localidad'              => 'nullable|string|max:255',
            'colonia_fracc'          => 'nullable|string|max:255',
            'cp'                     => 'nullable|string|max:5',
            'telefono'               => 'nullable|string|max:15',
            'referencias_domicilio'  => 'nullable|string',
        ]);

        try {
            $beneficiario = Beneficiario::create($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'ok' => true,
                    'message' => 'Beneficiario creado correctamente.',
                    'data' => $beneficiario->load(['estado', 'ocupacion'])
                ], 201);
            }

            return redirect()->route('beneficiarios.index')
                ->with('success', 'Beneficiario creado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear beneficiario: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'ok' => false,
                    'message' => 'Error al crear el beneficiario.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al crear el beneficiario.');
        }
    }

    public function show(Beneficiario $beneficiario)
    {
        $beneficiario->load(['estado', 'estadoViv', 'municipio', 'ocupacion']);

        if (request()->expectsJson()) {
            return response()->json($beneficiario);
        }

        return view('beneficiarios.show', compact('beneficiario'));
    }

    public function edit(Beneficiario $beneficiario): JsonResponse
    {
        $beneficiario->load(['estado', 'estadoViv', 'municipio', 'ocupacion']);

        return response()->json([
            'success' => true,
            'data'    => $beneficiario
        ]);
    }

    public function update(Request $request, Beneficiario $beneficiario)
    {
        $request->validate([
            'nombres'          => 'required|string|max:100',
            'primer_apellido'  => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'curp'             => 'nullable|string|size:18|unique:beneficiarios,curp,' . $beneficiario->id,
            'fecha_nac'        => 'nullable|date',
            'sexo'             => 'nullable|string|max:100',
            'discapacidad'     => 'boolean',
            'indigena'         => 'boolean',
            'maya_hablante'    => 'boolean',
            'afromexicano'     => 'boolean',
            'estado_civil'     => 'nullable|string|max:100',
            'ocupacion_id'     => 'nullable|integer|exists:ocupaciones,id',
            'estado_id'        => 'nullable|integer|exists:estados,id_estado',

            'calle'                  => 'nullable|string|max:255',
            'numero'                 => 'nullable|string|max:10',
            'letra'                  => 'nullable|string|max:5',
            'cruzamiento_1'          => 'nullable|string|max:255',
            'cruzamiento_2'          => 'nullable|string|max:255',
            'tipo_asentamiento'      => 'nullable|string|max:100',
            'estado_viv_id'          => 'nullable|integer|exists:estados,id_estado',
            'municipio_id'           => 'nullable|integer|exists:municipios,id',
            'localidad'              => 'nullable|string|max:255',
            'colonia_fracc'          => 'nullable|string|max:255',
            'cp'                     => 'nullable|string|max:5',
            'telefono'               => 'nullable|string|max:15',
            'referencias_domicilio'  => 'nullable|string',
        ]);
        $ocupacionAnterior = $beneficiario->ocupacion_id;

        $beneficiario->update($request->all());

        if ($ocupacionAnterior != $beneficiario->ocupacion_id) {
            $this->recalcularEstudiosPorCambioOcupacion($beneficiario);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Beneficiario actualizado correctamente.',
                'data'    => $beneficiario
            ]);
        }

        if ($request->has('estudio_actual')) {
            return redirect()->route('beneficiarios.estudios.editar', [$beneficiario->id, $request->estudio_actual])
                ->with('success', 'Beneficiario actualizado correctamente.');
        } else {
            return redirect()->route('beneficiarios.editar', $beneficiario->id)
                ->with('success', 'Beneficiario actualizado correctamente.');
        }
    }

    public function destroy(Request $request, Beneficiario $beneficiario)
    {
        $beneficiario->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Beneficiario eliminado correctamente.'
            ]);
        }

        return redirect()->route('beneficiarios.index')
            ->with('success', 'Beneficiario eliminado correctamente.');
    }

    public function checkCurp(Request $request)
    {
        try {
            $curp = $request->query('curp');

            if (!$curp || strlen($curp) !== 18) {
                return response()->json(['exists' => false]);
            }

            $beneficiario = Beneficiario::where('curp', $curp)->first();

            if ($beneficiario) {
                return response()->json([
                    'exists' => true,
                    'beneficiario' => [
                        'id' => $beneficiario->id,
                        'nombres' => $beneficiario->nombres,
                        'primer_apellido' => $beneficiario->primer_apellido,
                        'segundo_apellido' => $beneficiario->segundo_apellido,
                        'ruta_edicion' => route('beneficiarios.editar', $beneficiario->id)
                    ]
                ]);
            }

            return response()->json(['exists' => false]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno'], 500);
        }
    }

    public function getMunicipiosByEstado($estadoId)
    {
        $municipios = Municipio::where('estado_id', $estadoId)
            ->orderBy('descripcion')
            ->get();

        return response()->json($municipios);
    }

    public function editarBeneficiario(Beneficiario $beneficiario)
    {
        $ocupaciones = Ocupacion::where('activo', 1)->orderBy('ocupacion')->get();
        $estados = Estado::orderBy('nombre')->get();
        $municipios = Municipio::orderBy('descripcion')->get();
        $parentescos = Parentesco::all();

        return view('beneficiarios.editar', compact(
            'beneficiario',
            'ocupaciones',
            'estados',
            'municipios',
            'parentescos'
        ));
    }

    private function recalcularEstudiosPorCambioOcupacion(Beneficiario $beneficiario)
    {
        try {
            $estudios = $beneficiario->estudiosSocioeconomicos;

            foreach ($estudios as $estudio) {
                $resultados = [
                    'res_estudio_1' => $estudio->res_estudio_1,
                    'res_estudio_2' => $estudio->res_estudio_2,
                    'res_estudio_3' => $estudio->res_estudio_3
                ];

                $nuevoResTotal = $this->calcularResultadoTotalParaBeneficiario($estudio, $resultados);

                if ($estudio->res_total !== $nuevoResTotal) {
                    $estudio->update(['res_total' => $nuevoResTotal]);

                    Log::info("Estudio {$estudio->id} actualizado por cambio de ocupación", [
                        'beneficiario_id' => $beneficiario->id,
                        'ocupacion_anterior' => $estudio->res_total,
                        'ocupacion_nueva' => $nuevoResTotal
                    ]);
                }
            }

            Log::info("Recálculo completado para beneficiario {$beneficiario->id}", [
                'estudios_actualizados' => $estudios->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error al recalcular estudios por cambio de ocupación: ' . $e->getMessage());
        }
    }

    private function calcularResultadoTotalParaBeneficiario($estudio, $resultados)
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

    public function getEstudiosCompletos(Beneficiario $beneficiario)
    {
        try {
            $estudiosCompletos = $beneficiario->estudiosSocioeconomicos()
                ->whereNotNull('res_estudio_1')
                ->whereNotNull('res_estudio_2')
                ->whereNotNull('res_estudio_3')
                ->with(['beneficiario.ocupacion'])
                ->get();

            return response()->json([
                'success' => true,
                'estudios' => $estudiosCompletos,
                'total' => $estudiosCompletos->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar estudios: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEstudiosApi(Beneficiario $beneficiario)
    {
        try {
            $estudios = $beneficiario->estudiosSocioeconomicos->map(function ($estudio) use ($beneficiario) {
                return [
                    'id' => $estudio->id,
                    'folio' => $estudio->folio,
                    'created_at' => $estudio->created_at,
                    'res_estudio_1' => $estudio->res_estudio_1,
                    'res_estudio_2' => $estudio->res_estudio_2,
                    'res_estudio_3' => $estudio->res_estudio_3,
                    'ruta_edicion' => route('beneficiarios.estudios.editar', [$beneficiario->id, $estudio->id])
                ];
            });

            return response()->json([
                'beneficiario' => [
                    'id' => $beneficiario->id,
                    'nombre' => $beneficiario->nombres . ' ' . $beneficiario->primer_apellido . ' ' . $beneficiario->segundo_apellido
                ],
                'estudios' => $estudios
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar los estudios: ' . $e->getMessage()
            ], 500);
        }
    }
}
