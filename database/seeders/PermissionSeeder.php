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
        Permission::create(['name' => 'ver usuarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'crear usuarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'editar usuarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'eliminar usuarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'cambiar estatus usuarios', 'guard_name' => 'web']);

        // ================== PERMISOS PARA BENEFICIARIOS ==================
        Permission::create(['name' => 'ver beneficiarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'crear beneficiarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'editar beneficiarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'eliminar beneficiarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'importar beneficiarios', 'guard_name' => 'web']);
        Permission::create(['name' => 'exportar beneficiarios', 'guard_name' => 'web']);

        // ================== PERMISOS PARA LÍNEAS CONEVAL ==================
        Permission::create(['name' => 'ver lineas coneval', 'guard_name' => 'web']);
        Permission::create(['name' => 'crear lineas coneval', 'guard_name' => 'web']);
        Permission::create(['name' => 'editar lineas coneval', 'guard_name' => 'web']);

        // ================== PERMISOS PARA ÁREAS ==================
        Permission::create(['name' => 'ver areas', 'guard_name' => 'web']);
        Permission::create(['name' => 'crear areas', 'guard_name' => 'web']);
        Permission::create(['name' => 'editar areas', 'guard_name' => 'web']);
        Permission::create(['name' => 'eliminar areas', 'guard_name' => 'web']);

        // ================== PERMISOS PARA VINCULACIONES DE ESTUDIOS ==================
        Permission::create(['name' => 'ver vinculaciones estudios', 'guard_name' => 'web']);
        Permission::create(['name' => 'crear vinculaciones estudios', 'guard_name' => 'web']);
        Permission::create(['name' => 'eliminar vinculaciones estudios', 'guard_name' => 'web']);

        // ================== PERMISOS ADMINISTRATIVOS ==================
        Permission::create(['name' => 'gestionar roles', 'guard_name' => 'web']);
        Permission::create(['name' => 'acceder panel administracion', 'guard_name' => 'web']);

        // ================== CREAR ROLES ==================
        $superadmin = Role::create(['name' => 'superadmin', 'guard_name' => 'web']);
        $admin = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $user = Role::create(['name' => 'user', 'guard_name' => 'web']);

        // ================== ASIGNAR PERMISOS A ROLES ==================
        
        // superadmin - TODOS los permisos
        $superadmin->givePermissionTo(Permission::all());

        // admin - Todos los permisos
        $admin->givePermissionTo([
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios', 'cambiar estatus usuarios',
            'ver beneficiarios', 'crear beneficiarios', 'editar beneficiarios', 'eliminar beneficiarios', 
            'importar beneficiarios', 'exportar beneficiarios',
            'ver areas', 'crear areas', 'editar areas', 'eliminar areas', 
            'ver vinculaciones estudios', 'crear vinculaciones estudios', 
            'eliminar vinculaciones estudios',
            'acceder panel administracion', 
        ]);

        // user - Permisos básicos
        $user->givePermissionTo([
            'ver beneficiarios'
        ]);

        // ================== ASIGNAR ROLES A USUARIOS EXISTENTES ==================
        
        // Asignar superadmin al primer usuario (tu usuario principal)
        $usuarioSuperAdmin = Usuario::first();
        if ($usuarioSuperAdmin) {
            $usuarioSuperAdmin->assignRole('superadmin');
            $this->command->info("✅ SuperAdmin asignado a: " . $usuarioSuperAdmin->email);
        }
    }
    
    private function createRoleIfNotExists($roleName)
    {
        $role = Role::where('name', $roleName)
            ->where('guard_name', 'web')
            ->first();

        if (!$role) {
            $role = Role::create(['name' => $roleName, 'guard_name' => 'web']);
            $this->command->info("Rol creado: " . $roleName);
        } else {
            $this->command->info("ℹRol ya existe: " . $roleName);
        }

        return $role;
    }
}