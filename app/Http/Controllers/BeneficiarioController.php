<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Ocupacion;
use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parentesco;
use App\Models\Programa;
use App\Models\BeneficiarioEstudioVinculado;
use App\Models\EstudioSocioeconomico;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class BeneficiarioController extends Controller
{
    public function index(Request $request)
    {
        $programas = Programa::with('tiposPrograma')->get();

        $query = Beneficiario::query()->with(['estudiosSocioeconomicos.programa', 'estudiosVinculados.estudio.programa', 'estado', 'ocupacion']);

        if ($request->hasAny(['nombre_completo', 'curp', 'programa_id', 'tipo_programa_id', 'con_estudios'])) {

            $exactMatch = $request->boolean('exact_match');

            if ($request->filled('nombre_completo')) {
                $searchTerm = $request->nombre_completo;

                if ($exactMatch) {
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('nombres', $searchTerm)
                            ->orWhere('primer_apellido', $searchTerm)
                            ->orWhere('segundo_apellido', $searchTerm);
                    });
                } else {
                    $words = explode(' ', $searchTerm);

                    $query->where(function ($q) use ($words) {
                        foreach ($words as $word) {
                            if (strlen(trim($word)) > 0) {
                                $q->where(function ($innerQ) use ($word) {
                                    $innerQ->where('nombres', 'like', '%' . $word . '%')
                                        ->orWhere('primer_apellido', 'like', '%' . $word . '%')
                                        ->orWhere('segundo_apellido', 'like', '%' . $word . '%');
                                });
                            }
                        }
                    });
                }
            }

            if ($request->filled('curp')) {
                if ($exactMatch) {
                    $query->where('curp', $request->curp);
                } else {
                    $query->where('curp', 'like', '%' . $request->curp . '%');
                }
            }

            if ($request->filled('programa_id')) {
                $query->whereHas('estudiosSocioeconomicos', function ($q) use ($request) {
                    $q->where('programa_id', $request->programa_id);

                    if ($request->filled('tipo_programa_id')) {
                        $q->where('tipo_programa_id', $request->tipo_programa_id);
                    }
                });
            }

            if ($request->has('con_estudios')) {
                if ($request->con_estudios == '1') {
                    $query->has('estudiosSocioeconomicos');
                } elseif ($request->con_estudios == '0') {
                    $query->doesntHave('estudiosSocioeconomicos');
                }
            }
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

        return view('beneficiarios', compact('beneficiarios', 'ocupaciones', 'estados', 'municipios', 'programas'));
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

        if ($request->has('es_vinculado') && $request->has('estudio_actual')) {
            return redirect()->route('beneficiarios.estudios-vinculados.editar', [
                'beneficiarioVinculado' => $beneficiario->id,
                'estudio' => $request->estudio_actual
            ])->with('success', 'Beneficiario actualizado correctamente.');
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

    public function getLocalidadesByMunicipio($municipioId)
    {
        try {
            $municipiosConLocalidades = range(2294, 2399);

            if (!in_array($municipioId, $municipiosConLocalidades)) {
                return response()->json([]);
            }

            $localidades = \App\Models\Localidad::where('municipio_id', $municipioId)
                ->orderBy('id')
                ->get(['id', 'nom_loc', 'cvegeo']);

            return response()->json($localidades);
        } catch (\Exception $e) {
            Log::error('Error cargando localidades: ' . $e->getMessage());
            return response()->json([], 500);
        }
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
            Log::info("=== INICIANDO API ESTUDIOS PARA BENEFICIARIO {$beneficiario->id} ===");

            $estudiosPropios = DB::table('estudio_socioeconomico')
                ->where('beneficiario_id', $beneficiario->id)
                ->whereNull('deleted_at')
                ->select([
                    'id',
                    'folio',
                    'created_at',
                    'res_estudio_1',
                    'res_estudio_2',
                    'res_estudio_3'
                ])
                ->get()
                ->map(function ($estudio) use ($beneficiario) {
                    return [
                        'id' => $estudio->id,
                        'folio' => $estudio->folio ?? 'N/A',
                        'created_at' => $estudio->created_at,
                        'res_estudio_1' => $estudio->res_estudio_1,
                        'res_estudio_2' => $estudio->res_estudio_2,
                        'res_estudio_3' => $estudio->res_estudio_3,
                        'ruta_edicion' => route('beneficiarios.estudios.editar', [
                            'beneficiario' => $beneficiario->id,
                            'estudio' => $estudio->id
                        ]),
                        'tipo' => 'propio',
                        'beneficiario_principal_nombre' => $beneficiario->nombres . ' ' . $beneficiario->primer_apellido
                    ];
                });

            Log::info("Estudios propios encontrados: " . $estudiosPropios->count());

            $estudiosVinculados = DB::table('beneficiario_estudio_vinculados as bev')
                ->join('estudio_socioeconomico as es', 'bev.estudio_socioeconomico_id', '=', 'es.id')
                ->join('beneficiarios as bp', 'bev.beneficiario_principal_id', '=', 'bp.id')
                ->where('bev.beneficiario_vinculado_id', $beneficiario->id)
                ->whereNull('es.deleted_at')
                ->whereNull('bp.deleted_at')
                ->select([
                    'es.id as estudio_id',
                    'es.folio',
                    'es.created_at',
                    'es.res_estudio_1',
                    'es.res_estudio_2',
                    'es.res_estudio_3',
                    'bp.id as principal_id',
                    'bp.nombres as principal_nombres',
                    'bp.primer_apellido as principal_apellido'
                ])
                ->get()
                ->map(function ($vinculo) use ($beneficiario) {
                    return [
                        'id' => $vinculo->estudio_id,
                        'folio' => $vinculo->folio ?? 'N/A',
                        'created_at' => $vinculo->created_at,
                        'res_estudio_1' => $vinculo->res_estudio_1,
                        'res_estudio_2' => $vinculo->res_estudio_2,
                        'res_estudio_3' => $vinculo->res_estudio_3,
                        'ruta_edicion' => route('beneficiarios.estudios-vinculados.editar', [
                            'beneficiarioVinculado' => $beneficiario->id,
                            'estudio' => $vinculo->estudio_id
                        ]),
                        'tipo' => 'vinculado',
                        'beneficiario_principal_nombre' => $vinculo->principal_nombres . ' ' . $vinculo->principal_apellido,
                        'beneficiario_principal_id' => $vinculo->principal_id
                    ];
                });

            Log::info("Estudios vinculados encontrados: " . $estudiosVinculados->count());

            $todosEstudios = $estudiosPropios->merge($estudiosVinculados);

            Log::info("TOTAL estudios para beneficiario {$beneficiario->id}: " . $todosEstudios->count());

            return response()->json([
                'beneficiario' => [
                    'id' => $beneficiario->id,
                    'nombre' => $beneficiario->nombres . ' ' . $beneficiario->primer_apellido . ' ' . $beneficiario->segundo_apellido,
                    'estudios_propios' => $estudiosPropios->count(),
                    'estudios_vinculados' => $estudiosVinculados->count(),
                    'total_estudios' => $todosEstudios->count()
                ],
                'estudios' => $todosEstudios
            ]);
        } catch (\Exception $e) {
            Log::error('ERROR en getEstudiosApi: ' . $e->getMessage());
            Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            Log::error('Trace: ' . $e->getTraceAsString());

            return response()->json([
                'beneficiario' => [
                    'id' => $beneficiario->id,
                    'nombre' => $beneficiario->nombres . ' ' . $beneficiario->primer_apellido . ' ' . $beneficiario->segundo_apellido,
                    'estudios_propios' => 0,
                    'estudios_vinculados' => 0,
                    'total_estudios' => 0
                ],
                'estudios' => []
            ]);
        }
    }

    public function cargarResultados($id)
    {
        $beneficiario = \App\Models\Beneficiario::with('estudiosSocioeconomicos')->find($id);

        if (!$beneficiario) {
            return response()->json(['error' => 'Beneficiario no encontrado'], 404);
        }

        $html = '';
        $index = 1;

        foreach ($beneficiario->estudiosSocioeconomicos as $estudio) {
            $html .= view('componentes.resultado-estudio', compact('estudio', 'index'))->render();
            $index++;
        }

        if ($html === '') {
            $html = '<p class="text-center text-muted">Este beneficiario no tiene estudios registrados.</p>';
        }

        return response($html);
    }

    public function mostrarResultados($id)
    {
        $beneficiario = \App\Models\Beneficiario::with('estudiosSocioeconomicos')->find($id);

        if (!$beneficiario) {
            return response()->json(['error' => 'Beneficiario no encontrado'], 404);
        }

        $html = '';
        $index = 1;

        foreach ($beneficiario->estudiosSocioeconomicos as $estudio) {
            $html .= view('componentes.resultado-estudio', compact('estudio', 'index'))->render();
            $index++;
        }

        if ($html === '') {
            $html = '<p class="text-center text-muted">Este beneficiario no tiene estudios registrados.</p>';
        }

        return response($html);
    }

    public function vincularAEstudio(Request $request, Beneficiario $beneficiario)
    {
        try {
            Log::info('Iniciando vinculación de estudio', [
                'beneficiario_id' => $beneficiario->id,
                'estudio_id' => $request->estudio_id,
                'datos_request' => $request->all()
            ]);

            $request->validate([
                'estudio_id' => 'required|exists:estudio_socioeconomico,id'
            ]);

            $estudioId = $request->estudio_id;

            $estudio = EstudioSocioeconomico::find($estudioId);
            if (!$estudio) {
                return response()->json([
                    'success' => false,
                    'message' => 'El estudio no existe.'
                ], 404);
            }

            if ($estudio->beneficiario_id === $beneficiario->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede vincular un beneficiario a su propio estudio.'
                ], 422);
            }

            $vinculacionExistente = BeneficiarioEstudioVinculado::where([
                'estudio_socioeconomico_id' => $estudioId,
                'beneficiario_vinculado_id' => $beneficiario->id
            ])->exists();

            if ($vinculacionExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El beneficiario ya está vinculado a este estudio.'
                ], 422);
            }

            $vinculacion = BeneficiarioEstudioVinculado::create([
                'estudio_socioeconomico_id' => $estudioId,
                'beneficiario_vinculado_id' => $beneficiario->id,
                'beneficiario_principal_id' => $estudio->beneficiario_id
            ]);

            Log::info('Vinculación creada exitosamente', [
                'vinculacion_id' => $vinculacion->id,
                'estudio_id' => $estudioId,
                'beneficiario_vinculado_id' => $beneficiario->id,
                'beneficiario_principal_id' => $estudio->beneficiario_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Beneficiario vinculado correctamente al estudio.',
                'data' => [
                    'vinculacion' => $vinculacion,
                    'estudio' => $estudio,
                    'ruta_edicion' => route('beneficiarios.estudios-vinculados.editar', [
                        'beneficiarioVinculado' => $beneficiario->id,
                        'estudio' => $estudioId
                    ])
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación al vincular estudio', [
                'errors' => $e->errors(),
                'beneficiario_id' => $beneficiario->id,
                'estudio_id' => $request->estudio_id
            ]);

            $errorMessages = collect($e->errors())->flatten()->implode(', ');

            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . $errorMessages,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al vincular estudio', [
                'error' => $e->getMessage(),
                'beneficiario_id' => $beneficiario->id,
                'estudio_id' => $request->estudio_id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al vincular el estudio: ' . $e->getMessage()
            ], 500);
        }
    }
}
