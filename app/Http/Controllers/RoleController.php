<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->get();
        $permisos = Permission::orderBy('name')->get();

        return view('administrador.roles_permisos', compact('roles', 'permisos'));
    }

    // Endpoint AJAX para cargar datos del modal
    public function editAjax($id)
    {
        try {
            $rol = Role::with('permissions:id,name')->findOrFail($id);
            $permisos = Permission::select('id', 'name')->orderBy('name')->get();

            return response()->json([
                'rol' => [
                    'id' => (int)$rol->id,
                    'name' => $rol->name,
                    'permissions' => $rol->permissions->map(function ($p) {
                        return ['id' => (int)$p->id, 'name' => $p->name];
                    })
                ],
                'permisos' => $permisos->map(function ($p) {
                    return ['id' => (int)$p->id, 'name' => $p->name];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar los datos del rol'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->has('permissions')) {
            // Convertir IDs de permisos a nombres de permisos
            $permissionIds = $request->permissions;
            $permissionNames = Permission::whereIn('id', $permissionIds)
                ->pluck('name')
                ->toArray();

            $role->syncPermissions($permissionNames);
        }

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol creado correctamente');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array'
        ]);

        $role->update(['name' => $request->name]);

        if ($request->has('permissions')) {
            // Convertir IDs de permisos a nombres de permisos
            $permissionIds = $request->permissions;
            $permissionNames = Permission::whereIn('id', $permissionIds)
                ->pluck('name')
                ->toArray();

            $role->syncPermissions($permissionNames);
        } else {
            $role->syncPermissions([]);
        }

        // Si es una petición AJAX → responder JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado correctamente',
                'role' => $role->load('permissions:id,name')
            ]);
        }

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol actualizado correctamente');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Rol eliminado correctamente');
    }
}
