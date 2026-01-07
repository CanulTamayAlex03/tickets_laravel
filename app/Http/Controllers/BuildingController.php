<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BuildingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $buildings = Building::withTrashed()
            ->when($search, function ($query, $search) {
                return $query->where('description', 'like', '%' . $search . '%');
            })
            ->orderByRaw('deleted_at IS NULL DESC, id ASC')
            ->paginate(20);

        return view('administrador.admin.edificios', compact('buildings', 'search'));
    }

    public function create()
    {
        return view('administrador.admin.modals.building_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255|unique:buildings,description,NULL,id,deleted_at,NULL'
        ]);

        try {
            DB::beginTransaction();
            
            Building::create([
                'description' => $request->description
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Edificio creado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el edificio: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {        $building = Building::withTrashed()->findOrFail($id);
        return view('administrador.admin.modals.building_edit', compact('building'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255|unique:buildings,description,' . $id . ',id,deleted_at,NULL',
            'active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            
            $building = Building::withTrashed()->findOrFail($id);
            
            $building->update([
                'description' => $request->description
            ]);
            
            if ($request->has('active')) {
                if ($request->active == '1' && $building->trashed()) {
                    $building->restore();
                } elseif ($request->active == '0' && !$building->trashed()) {
                    $building->delete();
                }
            }
            
            DB::commit();
            
            $action = $request->has('active') ? 
                ($request->active == '1' ? 'activado' : 'inactivado') : 
                'actualizado';
            
            return response()->json([
                'success' => true,
                'message' => "Edificio {$action} exitosamente"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el edificio: ' . $e->getMessage()
            ], 500);
        }
    }

}