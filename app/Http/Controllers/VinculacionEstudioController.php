<?php

namespace App\Http\Controllers;

use App\Models\BeneficiarioEstudioVinculado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VinculacionEstudioController extends Controller
{
    public function index(Request $request)
    {
        $query = BeneficiarioEstudioVinculado::with([
            'estudio.programa',
            'beneficiarioVinculado',
            'beneficiarioPrincipal'
        ]);

        // Filtros SÍ necesarios
        if ($request->filled('folio')) {
            $query->whereHas('estudio', function ($q) use ($request) {
                $q->where('folio', 'like', '%' . $request->folio . '%');
            });
        }

        if ($request->filled('beneficiario_vinculado')) {
            $query->whereHas('beneficiarioVinculado', function ($q) use ($request) {
                $q->where('nombres', 'like', '%' . $request->beneficiario_vinculado . '%')
                    ->orWhere('primer_apellido', 'like', '%' . $request->beneficiario_vinculado . '%')
                    ->orWhere('segundo_apellido', 'like', '%' . $request->beneficiario_vinculado . '%');
            });
        }

        if ($request->filled('beneficiario_principal')) {
            $query->whereHas('beneficiarioPrincipal', function ($q) use ($request) {
                $q->where('nombres', 'like', '%' . $request->beneficiario_principal . '%')
                    ->orWhere('primer_apellido', 'like', '%' . $request->beneficiario_principal . '%')
                    ->orWhere('segundo_apellido', 'like', '%' . $request->beneficiario_principal . '%');
            });
        }

        $vinculaciones = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('estudios.vinculaciones-estudios.index', compact('vinculaciones'));
    }

    public function destroy(BeneficiarioEstudioVinculado $vinculacion)
    {
        try {
            $vinculacion->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Vinculación eliminada correctamente.'
                ]);
            }

            return redirect()->route('vinculaciones-estudios.index')
                ->with('success', 'Vinculación eliminada correctamente.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar vinculación: ' . $e->getMessage());

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar la vinculación.'
                ], 500);
            }

            return redirect()->route('vinculaciones-estudios.index')
                ->with('error', 'Error al eliminar la vinculación.');
        }
    }
}
