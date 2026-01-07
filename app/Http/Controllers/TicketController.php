<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Building;
use App\Models\Department;
use App\Models\Employee;
use App\Models\SupportPersonal;
use App\Models\IndicatorType;
use App\Models\AnotherService;
use App\Models\ExtraInfo;
use App\Models\ServiceStatus;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with([
            'building' => function ($query) {
                $query->withTrashed();
            }, 
            'department' => function ($query) {
                $query->withTrashed();
            }, 
            'employee' => function ($query) {
                $query->withTrashed();
            }, 
            'serviceStatus', 
            'supportPersonal', 
            'extraInfos.user', 
            'indicatorType', 
            'anotherService', 
            'equipment'
        ]);
        $employees = Employee::orderBy('full_name')->get();
        $buildings = Building::orderBy('description')->get();
        $departments = Department::orderBy('description')->get();
        $supportPersonals = SupportPersonal::where('active', true)->orderBy('name')->get();
        $indicatorTypes = IndicatorType::orderBy('description')->get();
        $anotherServices = AnotherService::orderBy('description')->get();
        $serviceStatuses = ServiceStatus::orderBy('id')->get();
        $equipmentList = Equipment::orderBy('description')->get();

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
                    })
                    ->orWhereHas('supportPersonal', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('lastnames', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        if ($request->ajax() || $request->has('partial')) {
            $html = view('administrador.admin.admin_solicitudes', compact(
                'tickets',
                'employees',
                'buildings',
                'departments',
                'supportPersonals',
                'indicatorTypes',
                'anotherServices',
                'serviceStatuses',
                'equipmentList'
            ))->render();

            return response()->json([
                'success' => true,
                'table_html' => $this->extractTableHtml($html),
                'pagination_html' => $tickets->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4')->toHtml(),
                'metadata_html' => 'Mostrando ' . $tickets->firstItem() . ' a ' . $tickets->lastItem() . ' de ' . $tickets->total() . ' registros',
                'total_count' => $tickets->total(),
                'timestamp' => now()->toISOString()
            ]);
        }
        return view('administrador.admin.admin_solicitudes', compact(
            'tickets',
            'employees',
            'buildings',
            'departments',
            'supportPersonals',
            'indicatorTypes',
            'anotherServices',
            'serviceStatuses',
            'equipmentList'
        ));
    }

    public function edit($id)
    {
        $ticket = Ticket::with([
            'building',
            'department',
            'employee',
            'serviceStatus',
            'supportPersonal',
            'extraInfos.user',
            'indicatorType',
            'anotherService',
            'equipment',
            'extraInfos' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'ticket' => $ticket,
            'extra_infos' => $ticket->extraInfos
        ]);
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

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'support_personal_id' => 'nullable|exists:support_personals,id',
            'indicator_type_id' => 'nullable|exists:indicator_types,id',
            'another_service_id' => 'nullable|exists:another_services,id',
            'equipment_id' => 'nullable|exists:equipment,id',
            'equipment_number' => 'nullable|string|max:255',
            'activity_description' => 'nullable|string',
            'service_status_id' => 'required|exists:service_statuses,id',
            'mk_pendient' => 'nullable|boolean',
            'nuevo_seguimiento' => 'nullable|string'
        ]);

        try {
            $ticket = Ticket::findOrFail($id);

            $updateData = [
                'indicator_type_id' => $validated['indicator_type_id'] ?? null,
                'another_service_id' => $validated['another_service_id'] ?? null,
                'equipment_id' => $validated['equipment_id'] ?? null,
                'equipment_number' => $validated['equipment_number'] ?? null,
                'activity_description' => $validated['activity_description'] ?? null,
                'service_status_id' => $validated['service_status_id'],
                'mk_pendient' => $request->has('mk_pendient') ? 1 : 0,
            ];

            if (isset($validated['support_personal_id'])) {
                $updateData['support_personal_id'] = $validated['support_personal_id'];

                if (!$ticket->support_personal_id && $validated['support_personal_id']) {
                    $updateData['service_status_id'] = 2;
                }
            }

            $ticket->update($updateData);

            if (!empty($validated['nuevo_seguimiento'])) {
                ExtraInfo::create([
                    'description' => $validated['nuevo_seguimiento'],
                    'request_id' => $ticket->id,
                    'user_id' => Auth::id()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => '¡Solicitud actualizada exitosamente!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getServicesByIndicator($indicatorId)
    {
        $services = AnotherService::where('indicator_type_id', $indicatorId)
            ->orderBy('description')
            ->get();

        return response()->json($services);
    }

    public function agregarSeguimiento(Request $request, $id)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:1000'
        ]);

        try {
            $ticket = Ticket::findOrFail($id);

            $seguimiento = ExtraInfo::create([
                'description' => $validated['description'],
                'request_id' => $ticket->id,
                'user_id' => Auth::id()
            ]);

            $seguimiento->load('user');

            return response()->json([
                'success' => true,
                'message' => 'Seguimiento guardado exitosamente',
                'seguimiento' => $seguimiento
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el seguimiento: ' . $e->getMessage()
            ], 500);
        }
    }
    private function extractTableHtml($html)
    {
        try {
            if (preg_match('/<table[^>]*>.*?<\/table>/is', $html, $matches)) {
                return $matches[0];
            }

            if (preg_match('/<tbody[^>]*>.*?<\/tbody>/is', $html, $matches)) {
                return '<table class="table table-sm table-striped table-hover mb-2"><thead class="table-dark">' .
                    '<tr><th width="5%">ID</th><th>Descripción</th><th width="15%">Usuario/Área</th>' .
                    '<th>Fecha Recepción</th><th>Estatus</th><th width="20%" class="text-center">Acciones</th></tr>' .
                    '</thead>' . $matches[0] . '</table>';
            }

            return $html;
        } catch (\Exception $e) {
            return $html;
        }
    }

    public function getNewTicketsCount(Request $request)
    {
        if (!auth()->user()->can('ver tickets')) {
            return response()->json(['count' => 0]);
        }
        
        $count = Ticket::where('service_status_id', 1)->count();
        
        return response()->json([
            'success' => true,
            'count' => $count,
            'last_updated' => now()->toISOString()
        ]);
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::with(['employee', 'serviceStatus'])->findOrFail($id);
            Log::info('Ticket eliminado por usuario', [
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Unknown',
                'ticket_description' => $ticket->description,
                'employee' => $ticket->employee->full_name ?? 'N/A',
                'status' => $ticket->serviceStatus->description ?? 'N/A',
                'deleted_at' => now()
            ]);

            $ticket->delete();

            return response()->json([
                'success' => true,
                'message' => 'Ticket eliminado exitosamente.'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'El ticket no existe o ya fue eliminado.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error al eliminar ticket: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el ticket: ' . $e->getMessage()
            ], 500);
        }
    }
}
