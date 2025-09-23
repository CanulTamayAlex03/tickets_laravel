<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Building;
use App\Models\UserType;
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

    private function getRoleByUserType($userTypeId)
    {
        $defaultRoles = [
            1 => 'user',
            2 => 'superadmin',
            3 => 'informatica',
            4 => 'admin',
            5 => 'ati'
        ];

        if (isset($defaultRoles[$userTypeId])) {
            return $defaultRoles[$userTypeId];
        }

        $userType = UserType::with('role')->find($userTypeId);

        if ($userType && $userType->role) {
            return $userType->role->name;
        }

        return 'user';
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::with(['userType', 'buildings'])
            ->when($search, function ($query, $search) {
                return $query->where('email', 'like', "%{$search}%");
            })
            ->orderBy('estatus', 'DESC')
            ->orderBy('id', 'ASC')
            ->paginate(20);

        $buildings = Building::all();
        $userTypes = UserType::all();

        return view('administrador.admin.usuarios', compact('users', 'buildings', 'userTypes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'user_type_id' => 'required|exists:user_types,id',
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
            'user_type_id' => $request->user_type_id,
            'estatus' => $request->estatus ?? 1
        ]);

        // 🔹 asignar rol según el user_type
        $role = $this->getRoleByUserType((int) $request->user_type_id);
        $user->syncRoles([$role]);

        if ($request->has('buildings')) {
            $user->buildings()->sync($request->buildings);
        }

        return redirect()->route('admin.usuarios')->with('success', 'Usuario creado correctamente');
    }

    public function edit($id)
    {
        $user = User::with('buildings')->findOrFail($id);
        $buildings = Building::all();
        $userTypes = UserType::all();
        $userBuildings = $user->buildings->pluck('id')->toArray();

        return view('administrador.admin.usuarios-edit', compact('user', 'buildings', 'userTypes', 'userBuildings'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
            'user_type_id' => 'required|exists:user_types,id',
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
            'user_type_id' => $request->user_type_id,
            'estatus' => $request->estatus ?? $user->estatus
        ];

        if ($request->filled('password')) {
            $data['encrypted_password'] = Hash::make($request->password);
        }

        $user->update($data);

        // 🔹 sincronizar rol en model_has_roles
        $role = $this->getRoleByUserType((int) $request->user_type_id);
        $user->syncRoles([$role]);

        // 🔹 edificios
        $user->buildings()->sync($request->buildings ?? []);

        return redirect()->route('admin.usuarios')
            ->with('success', 'Usuario actualizado correctamente');
    }
}
