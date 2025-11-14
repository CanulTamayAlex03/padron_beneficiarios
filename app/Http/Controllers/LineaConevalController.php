<?php

namespace App\Http\Controllers;

use App\Models\LineaConeval;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LineaConevalController extends Controller
{
    public function index()
    {
        $lineas = LineaConeval::orderBy('periodo', 'desc')
            ->get();

        $lineasAgrupadas = $lineas->groupBy('periodo')->map(function ($lineasDelPeriodo) {
            return $lineasDelPeriodo->sortBy(function ($linea) {
                $ordenZonas = [
                    'Rural' => 1,
                    'Urbana' => 2,
                    'Semiurbano' => 3
                ];
                return $ordenZonas[$linea->zona] ?? 999;
            });
        });
        return view('estudios.lineas-coneval.index', compact('lineas', 'lineasAgrupadas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periodo' => 'required|date',
            'descripcion' => 'nullable|string',
            'cantidades' => 'required|array',
            'cantidades.Rural' => 'required|numeric|min:0',
            'cantidades.Urbana' => 'required|numeric|min:0',
            'cantidades.Semiurbano' => 'required|numeric|min:0',
            'activo' => 'sometimes|boolean'
        ]);

        try {
            if ($validated['activo'] ?? true) {
                LineaConeval::where('activo', true)->update(['activo' => false]);
            }

            $zonas = ['Rural', 'Urbana', 'Semiurbano'];

            foreach ($zonas as $zona) {
                LineaConeval::create([
                    'zona' => $zona,
                    'cantidad' => $validated['cantidades'][$zona],
                    'periodo' => $validated['periodo'],
                    'descripcion' => $validated['descripcion'],
                    'activo' => $validated['activo'] ?? true
                ]);
            }

            return redirect()->route('lineas-coneval.index')
                ->with('success', 'Conjunto de 3 líneas CONEVAL creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el conjunto: ' . $e->getMessage())
                ->withInput();
        }
    }


    public function update(Request $request, $id)
    {
        $lineaConeval = LineaConeval::findOrFail($id);
        $validated = $request->validate([
            'zona' => 'required|string|in:Rural,Urbana,Semiurbano',
            'cantidad' => 'required|numeric|min:0',
            'periodo' => 'required|date',
            'descripcion' => 'nullable|string',
            'activo' => 'sometimes|boolean'
        ]);

        try {
            $validated['activo'] = $request->has('activo');

            if ($validated['activo']) {
                LineaConeval::where('zona', $validated['zona'])
                    ->where('id', '!=', $lineaConeval->id)
                    ->update(['activo' => false]);
            }

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
            if ($lineaConeval->activo) {
                $lineaConeval->update(['activo' => false]);

                return redirect()->route('lineas-coneval.index')
                    ->with('success', 'Línea CONEVAL desactivada exitosamente.');
            }

            return redirect()->route('lineas-coneval.index')
                ->with('info', 'La línea CONEVAL ya estaba inactiva.');
        } catch (\Exception $e) {
            return redirect()->route('lineas-coneval.index')
                ->with('error', 'Error al procesar la línea CONEVAL: ' . $e->getMessage());
        }
    }

    public function toggleActivo(LineaConeval $lineaConeval)
    {
        if (!$lineaConeval->activo) {
            LineaConeval::where('periodo', $lineaConeval->periodo)
                ->where('zona', $lineaConeval->zona)
                ->where('activo', true)
                ->update(['activo' => false]);
        }

        $lineaConeval->update(['activo' => !$lineaConeval->activo]);

        return redirect()->route('lineas-coneval.index')
            ->with('success', 'Estado actualizado.');
    }

    public function activarConjunto(Request $request)
    {
        $periodo = $request->input('periodo');

        try {
            LineaConeval::where('activo', true)->update(['activo' => false]);

            LineaConeval::where('periodo', $periodo)->update(['activo' => true]);

            return redirect()->route('lineas-coneval.index')
                ->with('success', "Conjunto del período {$periodo} activado exitosamente.");
        } catch (\Exception $e) {
            return redirect()->route('lineas-coneval.index')
                ->with('error', 'Error al activar el conjunto: ' . $e->getMessage());
        }
    }

    public function getByPeriodo(Request $request)
    {
        $periodo = $request->input('periodo');

        $lineas = LineaConeval::where('periodo', $periodo)
            ->where('activo', true)
            ->orderBy('zona')
            ->get();

        return response()->json($lineas);
    }
}
