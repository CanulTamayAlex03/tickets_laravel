<?php

namespace App\Http\Controllers;

use App\Models\SupportPersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportPersonalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:ver personal soporte'])->only(['index']);
        $this->middleware(['auth', 'permission:crear personal soporte'])->only(['store']);
        $this->middleware(['auth', 'permission:editar personal soporte'])->only(['edit', 'update']);
    }

    public function index(Request $request)
    {
        $search = $request->input('search');

        $supportPersonals = SupportPersonal::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('lastnames', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->orderBy('active', 'DESC')
            ->orderBy('id', 'ASC')
            ->paginate(20);

        return view('administrador.admin.personal_soporte', compact('supportPersonals'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastnames' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:support_personals,email',
            'active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        SupportPersonal::create([
            'name' => $request->name,
            'lastnames' => $request->lastnames,
            'email' => $request->email,
            'active' => $request->active ?? 1
        ]);

        return redirect()->route('admin.soporte')->with('success', 'Personal de soporte creado exitosamente');
    }

    public function edit($id)
    {
        $personal = SupportPersonal::findOrFail($id);
        return response()->json($personal);
    }

    public function update(Request $request, $id)
    {
        $personal = SupportPersonal::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastnames' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:support_personals,email,' . $id,
            'active' => 'sometimes|boolean'
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $personal->update([
            'name' => $request->name,
            'lastnames' => $request->lastnames,
            'email' => $request->email,
            'active' => $request->active ?? $personal->active
        ]);

        return redirect()->route('admin.soporte')
            ->with('success', 'Personal de soporte actualizado correctamente');
    }
}
