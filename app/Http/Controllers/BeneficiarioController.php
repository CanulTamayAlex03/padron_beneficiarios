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

        $query->orderBy('created_at', 'desc');

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
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'curp' => 'required|string|size:18|unique:beneficiarios,curp',
        ]);

        $beneficiario = Beneficiario::create($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Beneficiario creado correctamente.',
                'data' => $beneficiario
            ]);
        }

        return redirect()->route('beneficiarios')
            ->with('success', 'Beneficiario creado correctamente.');
    }

    public function show(Beneficiario $beneficiario): JsonResponse
    {
        return response()->json(['data' => $beneficiario]);
    }

    public function edit(Beneficiario $beneficiario): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $beneficiario
        ]);
    }

    public function update(Request $request, Beneficiario $beneficiario)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'curp' => 'required|string|size:18|unique:beneficiarios,curp,' . $beneficiario->id,
        ]);

        $beneficiario->update($request->all());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Beneficiario actualizado correctamente.',
                'data' => $beneficiario
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
}
