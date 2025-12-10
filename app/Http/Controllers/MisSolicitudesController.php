<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Employee;
use Illuminate\Http\Request;

class MisSolicitudesController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['building', 'department', 'employee', 'serviceStatus'])
                      ->where('service_status_id', 2);

        if ($request->filled('employee_search')) {
            $search = $request->employee_search;
            
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('full_name', $search);
            });
        } else {
            $tickets = $query->whereRaw('1 = 0')->paginate(20);
            $employees = Employee::orderBy('full_name')->get();
            return view('solicitudes.mis-solicitudes', compact('tickets', 'employees'));
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::orderBy('full_name')->get();

        return view('solicitudes.mis-solicitudes', compact('tickets', 'employees'));
    }
}