<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PermissionManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin']);
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        $users = User::with('roles')->get();

        return view('administrador.admin.permisos', compact('roles', 'permissions', 'users'));
    }

    public function updateRolePermissions(Request $request, $roleId)
    {
        try {
            $role = Role::findOrFail($roleId);

            Log::info('Actualizando permisos para rol ID: ' . $roleId);
            Log::info('Datos recibidos:', $request->all());

            $permissions = $request->input('permissions', []);

            $permissionIds = array_map('intval', $permissions);

            $permissionNames = Permission::whereIn('id', $permissionIds)
                ->pluck('name')
                ->toArray();

            $role->syncPermissions($permissionNames);

            Log::info('Permisos sincronizados: ' . implode(', ', $permissionNames));

            return response()->json([
                'success' => true,
                'message' => 'Permisos actualizados correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error crítico: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUserRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id'
        ]);

        $user->syncRoles($request->roles ?? []);

        return redirect()->back()->with('success', 'Roles actualizados para el usuario ' . $user->email);
    }

    public function createPermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Permiso creado correctamente');
    }

    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return redirect()->route('admin.permisos.manager')->with('success', 'Rol creado correctamente');
    }

    public function deletePermission(Permission $permission)
    {
        $permission->delete();

        return redirect()->back()->with('success', 'Permiso eliminado correctamente');
    }

    public function deleteRole(Role $role)
    {
        $role->delete();

        return redirect()->back()->with('success', 'Rol eliminado correctamente');
    }

    public function editAjax(Role $role)
    {
        try {
            $permisos = Permission::all();

            return response()->json([
                'rol' => $role->load('permissions'),
                'permisos' => $permisos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al cargar los datos: ' . $e->getMessage()
            ], 500);
        }
    }
}
