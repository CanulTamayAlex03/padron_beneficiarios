<?php

namespace App\Http\Controllers;

use App\Exports\BeneficiariosExport;
use App\Imports\BeneficiariosImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function export()
    {
        return Excel::download(new BeneficiariosExport, 'beneficiarios.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new \App\Imports\BeneficiariosImport();

            Excel::import($import, $request->file('file'));

            $successMessage = '';
            $warningMessage = null;

            if ($import->importados > 0) {
                $successMessage = "Se importaron {$import->importados} beneficiarios correctamente.";
            }

            if (!empty($import->errores)) {
                $warningMessage = implode('<br>', $import->errores);
            }

            // Si no se importó nada
            if ($import->importados === 0 && empty($import->errores)) {
                $warningMessage = "No se importó ningún beneficiario.";
            }

            return back()
                ->with('success', $successMessage)
                ->with('warning', $warningMessage);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }


    public function showImportForm()
    {
        return view('administrador.importar_beneficiarios');
    }
}
