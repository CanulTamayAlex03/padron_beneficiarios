<?php

namespace App\Http\Controllers;

use App\Models\BeneficiarioFamiliar;
use App\Models\Parentesco;
use Illuminate\Http\Request;

class BeneficiarioFamiliarController extends Controller
{
    public function index()
    {
        $familiares = BeneficiarioFamiliar::with('beneficiario', '')->get();
        return response()->json($familiares);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombres' => 'required|string|max:100',
            'primer_apellido' => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'curp' => 'nullable|string|max:18|unique:beneficiario_familiar,curp',
            'telefono' => 'nullable|string|max:15',
            'parentesco_id' => 'required|exists:parentesco,id',
            'beneficiario_id' => 'required|exists:beneficiarios,id',
        ]);

        $familiar = BeneficiarioFamiliar::create($request->all());

        return redirect()->back()->with('success', 'Familiar agregado correctamente');
    }

    public function show($id)
    {
        $familiar = BeneficiarioFamiliar::with(['beneficiario', 'parentesco'])->findOrFail($id);
        return response()->json($familiar);
    }

    public function update(Request $request, $id)
    {
        $familiar = BeneficiarioFamiliar::findOrFail($id);

        $request->validate([
            'nombres' => 'sometimes|string|max:100',
            'primer_apellido' => 'sometimes|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'curp' => 'nullable|string|max:18|unique:beneficiario_familiar,curp,' . $familiar->id,
            'telefono' => 'nullable|string|max:15',
            'parentesco_id' => 'sometimes|exists:parentesco,id', 
            'beneficiario_id' => 'sometimes|exists:beneficiarios,id',
        ]);

        $familiar->update($request->all());

        return redirect()->back()->with('success', 'Familiar actualizado correctamente');
    }

    public function destroy($id)
    {
        $familiar = BeneficiarioFamiliar::findOrFail($id);
        $familiar->delete();

        return redirect()->back()->with('success', 'Familiar eliminado correctamente');
    }
}
