<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $employees = Employee::withTrashed()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', '%' . $search . '%')
                      ->orWhere('name', 'like', '%' . $search . '%')
                      ->orWhere('lastname', 'like', '%' . $search . '%')
                      ->orWhere('no_nomina', 'like', '%' . $search . '%');
                });
            })
            ->orderByRaw('deleted_at IS NULL DESC, id ASC')
            ->paginate(20);

        return view('administrador.admin.empleados', compact('employees', 'search'));
    }

    public function create()
    {
        return view('administrador.admin.modals.employee_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'lastname2' => 'nullable|string|max:100',
            'no_nomina' => 'required|string|max:50|unique:employees,no_nomina,NULL,id,deleted_at,NULL',
        ]);

        try {
            DB::beginTransaction();
            
            $full_name = trim($request->name . ' ' . $request->lastname . ' ' . ($request->lastname2 ?? ''));
            
            Employee::create([
                'name' => $request->name,
                'lastname' => $request->lastname,
                'lastname2' => $request->lastname2,
                'no_nomina' => $request->no_nomina,
                'full_name' => $full_name
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Empleado creado exitosamente'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $employee = Employee::withTrashed()->findOrFail($id);
        return view('administrador.admin.modals.employee_edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'lastname' => 'required|string|max:100',
            'lastname2' => 'nullable|string|max:100',
            'no_nomina' => 'required|string|max:50|unique:employees,no_nomina,' . $id . ',id,deleted_at,NULL',
            'active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();
            
            $employee = Employee::withTrashed()->findOrFail($id);
            
            $full_name = trim($request->name . ' ' . $request->lastname . ' ' . ($request->lastname2 ?? ''));
            
            $employee->update([
                'name' => $request->name,
                'lastname' => $request->lastname,
                'lastname2' => $request->lastname2,
                'no_nomina' => $request->no_nomina,
                'full_name' => $full_name
            ]);
            
            if ($request->has('active')) {
                if ($request->active == '1' && $employee->trashed()) {
                    $employee->restore();
                } elseif ($request->active == '0' && !$employee->trashed()) {
                    $employee->delete();
                }
            }
            
            DB::commit();
            
            $action = $request->has('active') ? 
                ($request->active == '1' ? 'activado' : 'inactivado') : 
                'actualizado';
            
            return response()->json([
                'success' => true,
                'message' => "Empleado {$action} exitosamente"
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el empleado: ' . $e->getMessage()
            ], 500);
        }
    }
}