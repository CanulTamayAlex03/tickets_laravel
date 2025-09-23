<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionManagerController extends Controller
{
    public function __construct()
    {
        // Solo superadmin puede acceder a estas rutas
        $this->middleware(['auth', 'role:superadmin']);
    }

    /* Mostrar el gestor de permisos */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $users = User::with('roles')->get();

        return view('admin.permisos.manager', compact('roles', 'permissions', 'users'));
    }

    /* Actualizar permisos de un rol */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->back()->with('success', 'Permisos actualizados para el rol ' . $role->name);
    }

    /* Actualizar roles de un usuario */
    public function updateUserRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->syncRoles($request->roles ?? []);

        return redirect()->back()->with('success', 'Roles actualizados para el usuario ' . $user->email);
    }

    /* Crear nuevo permiso */
    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Permiso creado correctamente');
    }

    /* Crear nuevo rol */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name'
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Rol creado correctamente');
    }

    /* Eliminar un permiso */
    public function deletePermission(Permission $permission)
    {
        $permission->delete();

        return redirect()->back()->with('success', 'Permiso eliminado correctamente');
    }

    /* Eliminar un rol */
    public function deleteRole(Role $role)
    {
        $role->delete();

        return redirect()->back()->with('success', 'Rol eliminado correctamente');
    }
}
