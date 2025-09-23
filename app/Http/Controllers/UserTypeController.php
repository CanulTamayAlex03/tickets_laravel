<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:ver tipos usuario'])->only(['index']);
        $this->middleware(['auth', 'permission:crear tipos usuario'])->only(['store']);
        $this->middleware(['auth', 'permission:editar tipos usuario'])->only(['update']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $userTypes = UserType::with('role.permissions')
            ->when($search, function ($query, $search) {
                return $query->where('description', 'like', "%{$search}%");
            })
            ->orderBy('id', 'ASC')
            ->paginate(20);

        $permissions = \Spatie\Permission\Models\Permission::all();
        return view('administrador.admin.usuarios_tipo', compact('userTypes', 'permissions'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255|unique:user_types,description',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userType = UserType::create([
            'description' => $request->description,
        ]);

        if ($userType->role && $request->has('permissions')) {
            $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $request->permissions)
                ->pluck('name')
                ->toArray();

            $userType->role->syncPermissions($permissionNames);
        }

        return redirect()->route('admin.usuarios_tipo')
            ->with('success', 'Tipo de usuario y rol creados exitosamente');
    }


    public function edit($id)
    {
        $userType = UserType::with(['role', 'role.permissions'])->findOrFail($id);
        $allPermissions = \Spatie\Permission\Models\Permission::all();

        return response()->json([
            'userType' => $userType,
            'permissions' => $allPermissions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255|unique:user_types,description,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $userType = UserType::findOrFail($id);
        $userType->update(['description' => $request->description]);

        if ($userType->role) {
            $userType->role->update([
                'name' => \Illuminate\Support\Str::slug($request->description)
            ]);

            if ($request->has('permissions')) {
                $permissionNames = \Spatie\Permission\Models\Permission::whereIn('id', $request->permissions)
                    ->pluck('name')
                    ->toArray();

                $userType->role->syncPermissions($permissionNames);
            } else {
                $userType->role->syncPermissions([]);
            }
        }

        return redirect()->route('admin.usuarios_tipo')
            ->with('success', 'Tipo de usuario actualizado correctamente');
    }
}
