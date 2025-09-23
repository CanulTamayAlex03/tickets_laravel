<?php

namespace App\Http\Controllers;

use App\Models\SupportPersonal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportPersonalController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('administrador.login');
        }

        if (auth()->user()->user_type_id != 2) {
            abort(403, 'No tienes permiso para acceder a esta sección');
        }

        $search = $request->input('search');

        $supportPersonals = SupportPersonal::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('lastnames', 'like', "%{$search}%");
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastnames' => 'required|string|max:255',
            'active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $personal = SupportPersonal::findOrFail($id);
        $personal->update([
            'name' => $request->name,
            'lastnames' => $request->lastnames,
            'active' => $request->active ?? $personal->active
        ]);

        return redirect()->route('admin.soporte')
            ->with('success', 'Personal de soporte actualizado correctamente');
    }
}
