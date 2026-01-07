<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $departments = Department::withTrashed()
            ->when($search, function ($query, $search) {
                return $query->where('description', 'like', '%' . $search . '%');
            })
            ->orderByRaw('deleted_at IS NULL DESC, id ASC')
            ->paginate(20);

        return view('administrador.admin.departamentos', compact('departments', 'search'));
    }

    public function create()
    {
        return view('administrador.admin.modals.department_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255|unique:departments,description,NULL,id,deleted_at,NULL'
        ]);

        try {
            DB::beginTransaction();
            
            Department::create([
                'description' => $request->description
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Departamento creado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el departamento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $department = Department::withTrashed()->findOrFail($id);
        return view('administrador.admin.modals.department_edit', compact('department'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255|unique:departments,description,' . $id . ',id,deleted_at,NULL',
            'active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            
            $department = Department::withTrashed()->findOrFail($id);
            
            $department->update([
                'description' => $request->description
            ]);
            
            if ($request->has('active')) {
                if ($request->active == '1' && $department->trashed()) {
                    $department->restore();
                } elseif ($request->active == '0' && !$department->trashed()) {
                    $department->delete();
                }
            }
            
            DB::commit();
            
            $action = $request->has('active') ? 
                ($request->active == '1' ? 'activado' : 'inactivado') : 
                'actualizado';
            
            return response()->json([
                'success' => true,
                'message' => "Departamento {$action} exitosamente"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el departamento: ' . $e->getMessage()
            ], 500);
        }
    }

}