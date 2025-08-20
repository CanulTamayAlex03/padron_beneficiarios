<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('rol')->get();
        $roles = Rol::where('estatus', true)->get();
        return view('administrador.usuarios', compact('usuarios', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|min:8',
            'rol_id' => 'required|exists:roles,rol_id',
            'estatus' => 'boolean'
        ]);

        Usuario::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'estatus' => $request->estatus ?? true
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function update(Request $request, $usuario)
    {
        try {
            // Obtener el usuario
            $usuario = Usuario::findOrFail($usuario);

            // Validación
            $request->validate([
                'email' => 'required|email|unique:usuarios,email,' . $usuario->usuario_id . ',usuario_id',
                'password' => 'nullable|min:8',
                'rol_id' => 'required|exists:roles,rol_id',
                'estatus' => 'required|boolean'
            ]);

            // Preparar datos para actualizar
            $data = [
                'email' => $request->email,
                'rol_id' => $request->rol_id,
                'estatus' => $request->estatus
            ];

            // Actualizar contraseña solo si se proporcionó
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Registrar datos antes de actualizar
            Log::info('Actualizando usuario ID: ' . $usuario->usuario_id, ['datos_anteriores' => $usuario->toArray(), 'nuevos_datos' => $data]);

            // Actualizar el usuario
            $updated = $usuario->update($data);

            if ($updated) {
                Log::info('Usuario actualizado exitosamente', $usuario->fresh()->toArray());
                return redirect()->route('usuarios.index')
                    ->with('success', 'Usuario actualizado exitosamente.');
            }

            Log::error('Error al actualizar usuario', ['usuario_id' => $usuario->usuario_id]);
            return back()->with('error', 'Error al actualizar el usuario');
        } catch (\Exception $e) {
            Log::error('Error en actualización de usuario: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error al actualizar el usuario');
        }
    }
}
