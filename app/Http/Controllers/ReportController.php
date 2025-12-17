<?php

namespace App\Http\Controllers;

use App\Exports\TicketsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Building;
use App\Models\Department;
use App\Models\SupportPersonal;

class ReportController extends Controller
{
    public function index()
    {
        $buildings = Building::orderBy('description')->get();
        $departments = Department::orderBy('description')->get();
        $supportPersonnel = SupportPersonal::where('active', true) ->orderBy('name')->orderBy('lastnames')->get();

        
        return view('administrador.admin.reportes', compact(
            'buildings',
            'departments',
            'supportPersonnel'
        ));
    }

    public function export(Request $request)
    {
        $filters = $request->only([
            'status',
            'employee_id',
            'building_id',
            'department_id',
            'support_personal_id',
            'start_date',
            'end_date',
            'search'
        ]);

        $filename = 'reporte_tickets_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new TicketsExport($filters), $filename);
    }
}