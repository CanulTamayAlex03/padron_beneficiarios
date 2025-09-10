<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Mostrar todos los usuarios
    public function index()
    {
        $usuarios = Usuario::with('roles')->get();
        $roles = Role::all();
        return view('administrador.usuarios', compact('usuarios', 'roles'));
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|min:8',
            'rol' => 'required|exists:roles,name',
            'estatus' => 'boolean',
        ]);

        $usuario = Usuario::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'estatus' => $request->estatus ?? true,
        ]);

        $usuario->assignRole($request->rol);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario creado exitosamente.');
    }

    // Actualizar usuario existente
    public function update(Request $request, Usuario $usuario)
    {
        $request->validate([
            'email' => 'required|email|unique:usuarios,email,' . $usuario->usuario_id . ',usuario_id',
            'rol' => 'required|exists:roles,name',
            'estatus' => 'required|boolean',
        ]);

        $data = [
            'email' => $request->email,
            'estatus' => $request->estatus,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $usuario->update($data);
        $usuario->syncRoles([$request->rol]);

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    // AJAX para obtener los datos de un usuario
    public function editAjax(Usuario $usuario)
    {
        return response()->json([
            'id' => $usuario->usuario_id,
            'email' => $usuario->email,
            'rol' => $usuario->roles->count() ? $usuario->getRoleNames()->first() : '',
            'estatus' => $usuario->estatus,
        ]);
    }

    // Eliminar usuario
    public function destroy(Usuario $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario eliminado correctamente.');
    }
}
