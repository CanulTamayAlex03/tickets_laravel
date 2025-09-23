<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Building;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['building', 'department', 'employee', 'serviceStatus']);

        // Datos para selects
        $employees = Employee::orderBy('full_name')->get();
        $buildings = Building::orderBy('description')->get();
        $departments = Department::orderBy('description')->get();

        // --- FILTRO POR STATUS (BOTONES DE ARRIBA) ---
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'nuevo':
                    $query->where('service_status_id', 1);
                    break;
                case 'atendiendo':
                    $query->where('service_status_id', 2);
                    break;
                case 'cerrado':
                    $query->where('service_status_id', 3);
                    break;
                case 'pendiente':
                    $query->where('service_status_id', 4);
                    break;
                case 'completado':
                    $query->where('service_status_id', 5);
                    break;
            }
        }

        if (!$request->hasAny(['status', 'employee_id', 'building_id', 'department_id', 'status_filter', 'search'])) {
            $query->where('service_status_id', 1);
        }


        // --- FILTROS AVANZADOS DEL MODAL ---
        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->has('employee_search') && $request->employee_search != '') {
            $search = $request->employee_search;

            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('no_nomina', 'like', "%{$search}%");
            });
        }


        if ($request->filled('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status_filter')) {
            switch ($request->status_filter) {
                case 'nuevo':
                    $query->where('service_status_id', 1);
                    break;
                case 'atendiendo':
                    $query->where('service_status_id', 2);
                    break;
                case 'cerrado':
                    $query->where('service_status_id', 3);
                    break;
                case 'pendiente':
                    $query->where('service_status_id', 4);
                    break;
                case 'completado':
                    $query->where('service_status_id', 5);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('lastname2', 'like', "%{$search}%")
                            ->orWhere('no_nomina', 'like', "%{$search}%");
                    })
                    ->orWhereHas('department', function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%");
                    })
                    ->orWhereHas('building', function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%");
                    })
                    ->orWhereHas('serviceStatus', function ($q) use ($search) {
                        $q->where('description', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('administrador.admin.admin_solicitudes', compact(
            'tickets',
            'employees',
            'buildings',
            'departments'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'building_id'   => 'required|exists:buildings,id',
            'department_id' => 'required|exists:departments,id',
            'employee_id'   => 'required|exists:employees,id',
            'description'   => 'required|string',
        ], [
            'building_id.required'   => 'Debe seleccionar un edificio',
            'department_id.required' => 'Debe seleccionar un departamento',
            'employee_id.required'   => 'Debe seleccionar un empleado',
            'description.required'   => 'La descripción es obligatoria',
        ]);

        try {

            $validated['service_status_id'] = 1;

            Ticket::create($validated);

            return redirect()->route('home')
                ->with('success', '¡Ticket creado exitosamente!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Error al crear el ticket: ' . $e->getMessage());
        }
    }
}
