<?php

namespace App\Http\Controllers;

use App\Models\Beneficiario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BeneficiarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Beneficiario::query();

        if ($request->has('curp') && !empty($request->curp)) {
            $query->where('curp', 'like', '%' . $request->curp . '%');
        }

        $query->orderBy('id', 'desc');

        if ($request->ajax()) {
            $beneficiarios = $query->get();
            return response()->json(['data' => $beneficiarios]);
        }

        $beneficiarios = $query->paginate(20);

        return view('beneficiarios', compact('beneficiarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres'          => 'required|string|max:100',
            'primer_apellido'  => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'curp'             => 'nullable|string|size:18|unique:beneficiarios,curp',
            'fecha_nac'        => 'required|date',
            'estado_nac'       => 'nullable|string|max:100',
            'sexo'             => 'nullable|string|max:100',
            'discapacidad'     => 'boolean',
            'indigena'         => 'boolean',
            'maya_hablante'    => 'boolean',
            'afromexicano'     => 'boolean',
            'estado_civil'     => 'nullable|string|max:100',
            'ocupacion'        => 'nullable|string|max:100',
        ]);

        $beneficiario = Beneficiario::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Beneficiario creado correctamente.',
                'data'    => $beneficiario
            ]);
        }

        return redirect()->route('beneficiarios')
            ->with('success', 'Beneficiario creado correctamente.');
    }

    public function show(Beneficiario $beneficiario)
    {
        if (request()->expectsJson()) {
            return response()->json($beneficiario);
        }

        return view('beneficiarios.show', compact('beneficiario'));
    }


    public function edit(Beneficiario $beneficiario): JsonResponse
    {
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
            'estado_nac'       => 'nullable|string|max:100',
            'sexo'             => 'nullable|string|max:100',
            'discapacidad'     => 'boolean',
            'indigena'         => 'boolean',
            'maya_hablante'    => 'boolean',
            'afromexicano'     => 'boolean',
            'estado_civil'     => 'nullable|string|max:100',
            'ocupacion'        => 'nullable|string|max:100',
        ]);

        $beneficiario->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Beneficiario actualizado correctamente.',
                'data'    => $beneficiario
            ]);
        }

        return redirect()->route('beneficiarios')
            ->with('success', 'Beneficiario actualizado correctamente.');
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

        return redirect()->route('beneficiarios')
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
}
