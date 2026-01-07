<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // ================== PERMISOS PARA TICKETS ==================
        $this->createPermissionIfNotExists('ver tickets');
        $this->createPermissionIfNotExists('crear tickets');
        $this->createPermissionIfNotExists('editar tickets');
        $this->createPermissionIfNotExists('eliminar tickets');
        $this->createPermissionIfNotExists('gestionar tickets');
        $this->createPermissionIfNotExists('asignar tickets');

        // ================== PERMISOS PARA USUARIOS ==================
        $this->createPermissionIfNotExists('ver usuarios');
        $this->createPermissionIfNotExists('crear usuarios');
        $this->createPermissionIfNotExists('editar usuarios');
        $this->createPermissionIfNotExists('eliminar usuarios');

        // ================== PERMISOS PARA PERSONAL DE SOPORTE ==================
        $this->createPermissionIfNotExists('ver personal soporte');
        $this->createPermissionIfNotExists('crear personal soporte');
        $this->createPermissionIfNotExists('editar personal soporte');
        $this->createPermissionIfNotExists('eliminar personal soporte');

        // ================== PERMISOS PARA REPORTES ==================
        $this->createPermissionIfNotExists('ver reportes');
        $this->createPermissionIfNotExists('generar reportes');
        $this->createPermissionIfNotExists('exportar reportes');

        // ================== PERMISOS PARA EDIFICIOS ==================
        $this->createPermissionIfNotExists('ver edificios');
        $this->createPermissionIfNotExists('crear edificios');
        $this->createPermissionIfNotExists('editar edificios');
        $this->createPermissionIfNotExists('eliminar edificios');

        // ================== PERMISOS PARA DEPARTAMENTOS ==================
        $this->createPermissionIfNotExists('ver departamentos');
        $this->createPermissionIfNotExists('crear departamentos');
        $this->createPermissionIfNotExists('editar departamentos');
        $this->createPermissionIfNotExists('eliminar departamentos');

        // ================== PERMISOS PARA EMPLEADOS ==================
        $this->createPermissionIfNotExists('ver empleados');
        $this->createPermissionIfNotExists('crear empleados');
        $this->createPermissionIfNotExists('editar empleados');
        $this->createPermissionIfNotExists('eliminar empleados');

        // ================== PERMISOS ADMINISTRATIVOS ==================
        $this->createPermissionIfNotExists('gestionar permisos');
        $this->createPermissionIfNotExists('gestionar roles');
        $this->createPermissionIfNotExists('acceso administrador');

        // ================== PERMISOS PARA NOTIFICACIONES ==================
        $this->createPermissionIfNotExists('notificaciones tickets nuevos');
        $this->createPermissionIfNotExists('notificaciones tickets asignados');

        // ================== CREAR ROLES EN MINÚSCULA ==================
        $superadmin = $this->createRoleIfNotExists('superadmin');
        $informatica = $this->createRoleIfNotExists('informatica');
        $admin = $this->createRoleIfNotExists('admin');
        $ati = $this->createRoleIfNotExists('ati');
        $user = $this->createRoleIfNotExists('user');

        // ================== ASIGNAR PERMISOS A ROLES ==================
        
        // superadmin - TODOS los permisos
        $superadmin->syncPermissions(Permission::all());

        // informatica - Permisos técnicos amplios
        $informatica->syncPermissions([
            'ver tickets', 'crear tickets', 'editar tickets', 'gestionar tickets',
            'ver usuarios', 'crear usuarios', 'editar usuarios',
            'ver reportes', 'generar reportes', 'exportar reportes',
            'acceso administrador', 'notificaciones tickets asignados'
        ]);

        // admin - Permisos administrativos
        $admin->syncPermissions([
            'ver tickets', 'crear tickets', 'editar tickets', 'gestionar tickets',
            'ver usuarios', 'crear usuarios', 'editar usuarios',
            'ver reportes', 'generar reportes',
            'acceso administrador', 'notificaciones tickets nuevos', 'editar personal soporte',
            'ver edificios', 'crear edificios', 'editar edificios', 'eliminar edificios',
            'ver departamentos', 'crear departamentos', 'editar departamentos', 'eliminar departamentos',
            'ver empleados', 'crear empleados', 'editar empleados', 'eliminar empleados'
        ]);

        // ati - Permisos de soporte técnico
        $ati->syncPermissions([
            'ver tickets', 'editar tickets', 'gestionar tickets',
            'ver reportes'
        ]);

        // user - Permisos básicos
        $user->syncPermissions([
            'ver tickets', 'crear tickets'
        ]);

        // ================== ASIGNAR ROLES A USUARIOS EXISTENTES ==================
        
        $superadminUser = User::where('email', 'superadmin@yucatan.gob.mx')->first();
        if ($superadminUser) {
            $superadminUser->syncRoles(['superadmin']);
            echo "Rol superadmin asignado a: superadmin@yucatan.gob.mx\n";
        } else {
            echo "No se encontró el usuario: superadmin@yucatan.gob.mx\n";
            echo "   Crea este usuario o cambia el email en el seeder\n";
        }

        $usersWithoutRole = User::where('email', '!=', 'superadmin@yucatan.gob.mx')
                               ->doesntHave('roles')
                               ->get();
        
        foreach ($usersWithoutRole as $user) {
            $user->syncRoles(['user']);
            echo "Rol user asignado a: {$user->email}\n";
        }

        echo "Estructura de permisos creada correctamente\n";
        echo "Tu usuario superadmin@yucatan.gob.mx tiene todos los permisos\n";
        echo "Los demás usuarios tienen rol 'user' por defecto\n";
    }

    private function createPermissionIfNotExists($name)
    {
        $permission = Permission::where('name', $name)->first();
        if (!$permission) {
            return Permission::create(['name' => $name]);
        }
        return $permission;
    }

    private function createRoleIfNotExists($name)
    {
        $role = Role::where('name', $name)->first();
        if (!$role) {
            return Role::create(['name' => $name]);
        }
        return $role;
    }
}