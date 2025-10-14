<?php

namespace App\Http\Controllers;

use App\Models\LineaConeval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LineaConevalController extends Controller
{
   
    public function index()
    {
        $lineas = LineaConeval::orderBy('periodo', 'desc')
                            ->orderBy('zona')
                            ->get();

        return view('lineas-coneval.index', compact('lineas'));
    }


    public function create()
    {
        return view('lineas-coneval.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zona' => 'required|string|max:100',
            'cantidad' => 'required|numeric|min:0',
            'periodo' => 'required|date',
            'descripcion' => 'nullable|string|max:255'
        ]);

        try {
            LineaConeval::create($validated);
            
            return redirect()->route('lineas-coneval.index')
                            ->with('success', 'Línea CONEVAL creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Error al crear la línea CONEVAL: ' . $e->getMessage())
                            ->withInput();
        }
    }

    public function show(LineaConeval $lineaConeval)
    {
        return view('lineas-coneval.show', compact('lineaConeval'));
    }


    public function edit(LineaConeval $lineaConeval)
    {
        return view('lineas-coneval.edit', compact('lineaConeval'));
    }


    public function update(Request $request, LineaConeval $lineaConeval)
    {
        $validated = $request->validate([
            'zona' => 'required|string|max:100',
            'cantidad' => 'required|numeric|min:0',
            'periodo' => 'required|date',
            'descripcion' => 'nullable|string|max:255'
        ]);

        try {
            $lineaConeval->update($validated);
            
            return redirect()->route('lineas-coneval.index')
                            ->with('success', 'Línea CONEVAL actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->with('error', 'Error al actualizar la línea CONEVAL: ' . $e->getMessage())
                            ->withInput();
        }
    }


    public function destroy(LineaConeval $lineaConeval)
    {
        try {
            if ($lineaConeval->estudiosSocioeconomicos()->exists()) {
                return redirect()->route('lineas-coneval.index')
                                ->with('warning', 'No se puede eliminar esta línea porque está siendo utilizada en estudios socioeconómicos.');
            }

            $lineaConeval->delete();
            
            return redirect()->route('lineas-coneval.index')
                            ->with('success', 'Línea CONEVAL eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('lineas-coneval.index')
                            ->with('error', 'Error al eliminar la línea CONEVAL: ' . $e->getMessage());
        }
    }

    public function getByPeriodo(Request $request)
    {
        $periodo = $request->input('periodo');
        
        $lineas = LineaConeval::where('periodo', $periodo)
                            ->orderBy('zona')
                            ->get();

        return response()->json($lineas);
    }
}