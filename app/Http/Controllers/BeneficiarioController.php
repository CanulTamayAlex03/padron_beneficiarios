<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use App\Models\Ocupacion;
use App\Models\Estado;
use App\Models\Municipio;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


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

        $beneficiario->update($request->all());

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
        $request->validate([
            'curp' => 'required|string|size:18'
        ]);

        $exists = Beneficiario::whereRaw('LOWER(curp) = ?', [strtolower($request->curp)])->exists();

        return response()->json(['exists' => $exists]);
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

        return view('beneficiarios.editar', compact(
            'beneficiario',
            'ocupaciones',
            'estados',
            'municipios'
        ));
    }
}
