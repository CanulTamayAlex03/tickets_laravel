<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Building;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:ver usuarios'])->only(['index', 'edit']);
        $this->middleware(['auth', 'permission:crear usuarios'])->only(['store']);
        $this->middleware(['auth', 'permission:editar usuarios'])->only(['update']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with(['roles', 'buildings'])
            ->when($search, function ($query, $search) {
                return $query->where('email', 'like', "%{$search}%");
            })
            ->orderBy('estatus', 'DESC')
            ->orderBy('id', 'ASC')
            ->paginate(20);

        $buildings = Building::all();
        $roles = Role::all();

        return view('administrador.admin.usuarios', compact('users', 'buildings', 'roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'buildings' => 'sometimes|array',
            'buildings.*' => 'exists:buildings,id',
            'estatus' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'email' => $request->email,
            'encrypted_password' => Hash::make($request->password),
            'estatus' => $request->estatus ?? 1
        ]);

        $role = Role::find($request->role_id);
        $user->assignRole($role);

        if ($request->has('buildings')) {
            $user->buildings()->sync($request->buildings);
        }

        return redirect()->route('admin.usuarios')->with('success', 'Usuario creado correctamente');
    }

    public function edit($id)
    {
        $user = User::with(['buildings', 'roles'])->findOrFail($id);
        $buildings = Building::all();
        $roles = Role::all();
        $userBuildings = $user->buildings->pluck('id')->toArray();
        $userRole = $user->roles->first()->id ?? null;

        return view('administrador.admin.usuarios-edit', compact('user', 'buildings', 'roles', 'userBuildings', 'userRole'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'role_id' => 'required|exists:roles,id',
            'buildings' => 'sometimes|array',
            'buildings.*' => 'exists:buildings,id',
            'estatus' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::findOrFail($id);

        $data = [
            'email' => $request->email,
            'estatus' => $request->estatus ?? $user->estatus
        ];

        if ($request->filled('password')) {
            $data['encrypted_password'] = Hash::make($request->password);
        }

        $user->update($data);

        $role = Role::find($request->role_id);
        $user->syncRoles([$role->name]);

        $user->buildings()->sync($request->buildings ?? []);

        return redirect()->route('admin.usuarios')
            ->with('success', 'Usuario actualizado correctamente');
    }
}