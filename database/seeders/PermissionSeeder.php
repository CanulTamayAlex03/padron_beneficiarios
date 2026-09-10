<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Usuario;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // ================== PERMISOS PARA USUARIOS ==================
        $this->createPermissionIfNotExists('ver usuarios', 'web');
        $this->createPermissionIfNotExists('crear usuarios', 'web');
        $this->createPermissionIfNotExists('editar usuarios', 'web');
        $this->createPermissionIfNotExists('eliminar usuarios', 'web');
        $this->createPermissionIfNotExists('cambiar estatus usuarios', 'web');

        // ================== PERMISOS PARA BENEFICIARIOS ==================
        $this->createPermissionIfNotExists('ver beneficiarios', 'web');
        $this->createPermissionIfNotExists('crear beneficiarios', 'web');
        $this->createPermissionIfNotExists('editar beneficiarios', 'web');
        $this->createPermissionIfNotExists('eliminar beneficiarios', 'web');
        $this->createPermissionIfNotExists('importar beneficiarios', 'web');
        $this->createPermissionIfNotExists('exportar beneficiarios', 'web');

        // ================== PERMISOS PARA LÍNEAS CONEVAL ==================
        $this->createPermissionIfNotExists('ver lineas coneval', 'web');
        $this->createPermissionIfNotExists('crear lineas coneval', 'web');
        $this->createPermissionIfNotExists('editar lineas coneval', 'web');

        // ================== PERMISOS PARA ÁREAS ==================
        $this->createPermissionIfNotExists('ver areas', 'web');
        $this->createPermissionIfNotExists('crear areas', 'web');
        $this->createPermissionIfNotExists('editar areas', 'web');
        $this->createPermissionIfNotExists('eliminar areas', 'web');

        // ================== PERMISOS PARA VINCULACIONES DE ESTUDIOS ==================
        $this->createPermissionIfNotExists('ver vinculaciones estudios', 'web');
        $this->createPermissionIfNotExists('crear vinculaciones estudios', 'web');
        $this->createPermissionIfNotExists('eliminar vinculaciones estudios', 'web');

        // ================== PERMISOS ADMINISTRATIVOS ==================
        $this->createPermissionIfNotExists('gestionar roles', 'web');
        $this->createPermissionIfNotExists('acceder panel administracion', 'web');
        $this->createPermissionIfNotExists('boton volver', 'web');
        $this->createPermissionIfNotExists('nuevo estudio en editar', 'web');

        // ================== CREAR ROLES ==================
        $superadmin = $this->createRoleIfNotExists('superadmin', 'web');
        $admin = $this->createRoleIfNotExists('admin', 'web');
        $user = $this->createRoleIfNotExists('user', 'web');

        
        $superadmin->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios', 'cambiar estatus usuarios',
            'ver beneficiarios', 'crear beneficiarios', 'editar beneficiarios', 'eliminar beneficiarios', 
            'importar beneficiarios', 'exportar beneficiarios',
            'ver areas', 'crear areas', 'editar areas', 'eliminar areas', 
            'ver vinculaciones estudios', 'crear vinculaciones estudios', 
            'eliminar vinculaciones estudios',
            'acceder panel administracion',
            'boton volver',
        ]);

        $user->syncPermissions([
            'ver beneficiarios',
        ]);

        $usuarioSuperAdmin = Usuario::first();
        if ($usuarioSuperAdmin && !$usuarioSuperAdmin->hasRole('superadmin')) {
            $usuarioSuperAdmin->assignRole('superadmin');
            $this->command->info("SuperAdmin asignado a: " . $usuarioSuperAdmin->email);
        }
    }
    
    private function createPermissionIfNotExists($name, $guardName = 'web')
    {
        $permission = Permission::where('name', $name)
            ->where('guard_name', $guardName)
            ->first();
        if (!$permission) {
            $permission = Permission::create([
                'name' => $name, 
                'guard_name' => $guardName
            ]);
            $this->command->info("Permiso creado: " . $name);
        } else {
            $this->command->info("ℹPermiso ya existe: " . $name);
        }
        return $permission;
    }
    
    private function createRoleIfNotExists($roleName, $guardName = 'web')
    {
        $role = Role::where('name', $roleName)
            ->where('guard_name', $guardName)
            ->first();
        if (!$role) {
            $role = Role::create([
                'name' => $roleName, 
                'guard_name' => $guardName
            ]);
            $this->command->info("Rol creado: " . $roleName);
        } else {
            $this->command->info("Rol ya existe: " . $roleName);
        }
        return $role;
    }
}