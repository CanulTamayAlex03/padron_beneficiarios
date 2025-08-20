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

        if ($request->ajax()) {
            $beneficiarios = $query->get();
            return response()->json(['data' => $beneficiarios]);
        }

        $beneficiarios = $query->paginate(10);

        return view('beneficiarios', compact('beneficiarios'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'curp' => 'required|string|size:18|unique:beneficiarios,curp',
        ]);

        $beneficiario = Beneficiario::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Beneficiario creado correctamente.',
            'data' => $beneficiario
        ]);
    }

    public function show(Beneficiario $beneficiario): JsonResponse
    {
        return response()->json(['data' => $beneficiario]);
    }

    public function edit(Beneficiario $beneficiario): JsonResponse
    {
        return response()->json(['data' => $beneficiario]);
    }

    public function update(Request $request, Beneficiario $beneficiario): JsonResponse
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'curp' => 'required|string|size:18|unique:beneficiarios,curp,' . $beneficiario->id,
        ]);

        $beneficiario->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Beneficiario actualizado correctamente.',
            'data' => $beneficiario
        ]);
    }

}
