<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Beneficiario;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

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


}