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
        Permission::create(['name' => 'ver tickets']);
        Permission::create(['name' => 'crear tickets']);
        Permission::create(['name' => 'editar tickets']);
        Permission::create(['name' => 'eliminar tickets']);
        Permission::create(['name' => 'gestionar tickets']);

        // ================== PERMISOS PARA USUARIOS ==================
        Permission::create(['name' => 'ver usuarios']);
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'eliminar usuarios']);

        // ================== PERMISOS PARA TIPOS DE USUARIO ==================
        Permission::create(['name' => 'ver tipos usuario']);
        Permission::create(['name' => 'crear tipos usuario']);
        Permission::create(['name' => 'editar tipos usuario']);
        Permission::create(['name' => 'eliminar tipos usuario']);

        // ================== PERMISOS PARA PERSONAL DE SOPORTE ==================
        Permission::create(['name' => 'ver personal soporte']);
        Permission::create(['name' => 'crear personal soporte']);
        Permission::create(['name' => 'editar personal soporte']);
        Permission::create(['name' => 'eliminar personal soporte']);

        // ================== PERMISOS PARA REPORTES ==================
        Permission::create(['name' => 'ver reportes']);
        Permission::create(['name' => 'generar reportes']);
        Permission::create(['name' => 'exportar reportes']);

        // ================== PERMISOS ADMINISTRATIVOS ==================
        Permission::create(['name' => 'gestionar permisos']);
        Permission::create(['name' => 'gestionar roles']);
        Permission::create(['name' => 'acceso administrador']);

        // ================== CREAR ROLES EN MINÚSCULA ==================
        $superadmin = Role::create(['name' => 'superadmin']);
        $informatica = Role::create(['name' => 'informatica']);
        $admin = Role::create(['name' => 'admin']);
        $ati = Role::create(['name' => 'ati']);
        $user = Role::create(['name' => 'user']);

        // ================== ASIGNAR PERMISOS A ROLES ==================
        
        // superadmin - TODOS los permisos
        $superadmin->givePermissionTo(Permission::all());

        // informatica - Permisos técnicos amplios
        $informatica->givePermissionTo([
            'ver tickets', 'crear tickets', 'editar tickets', 'gestionar tickets',
            'ver usuarios', 'crear usuarios', 'editar usuarios',
            'ver reportes', 'generar reportes', 'exportar reportes',
            'acceso administrador'
        ]);

        // admin - Permisos administrativos
        $admin->givePermissionTo([
            'ver tickets', 'crear tickets', 'editar tickets', 'gestionar tickets',
            'ver usuarios', 'crear usuarios', 'editar usuarios',
            'ver reportes', 'generar reportes',
            'acceso administrador'
        ]);

        // ati - Permisos de soporte técnico
        $ati->givePermissionTo([
            'ver tickets', 'editar tickets', 'gestionar tickets',
            'ver reportes'
        ]);

        // user - Permisos básicos
        $user->givePermissionTo([
            'ver tickets', 'crear tickets'
        ]);

        // ================== ASIGNAR ROLES SEGÚN USER_TYPE_ID ==================
        
        // User_type 2 = superadmin
        $userSuperAdmin = User::where('user_type_id', 2)->first();
        if ($userSuperAdmin) {
            $userSuperAdmin->assignRole('superadmin');
        }

        // User_type 3 = informatica
        $usersInformatica = User::where('user_type_id', 3)->get();
        foreach ($usersInformatica as $user) {
            $user->assignRole('informatica');
        }

        // User_type 4 = admin
        $usersAdmin = User::where('user_type_id', 4)->get();
        foreach ($usersAdmin as $user) {
            $user->assignRole('admin');
        }

        // User_type 5 = ati
        $usersATI = User::where('user_type_id', 5)->get();
        foreach ($usersATI as $user) {
            $user->assignRole('ati');
        }

        // User_type 1 = user
        $usersBasic = User::where('user_type_id', 1)->get();
        foreach ($usersBasic as $user) {
            $user->assignRole('user');
        }
    }
}