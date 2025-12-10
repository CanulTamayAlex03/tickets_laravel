<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class TicketsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Ticket::with([
            'building',
            'department', 
            'employee',
            'serviceStatus',
            'supportPersonal',
            'extraInfos',
            'indicatorType',
            'anotherService'
        ]);

        if (!empty($this->filters)) {
            if (isset($this->filters['start_date']) && $this->filters['start_date']) {
                $query->whereDate('created_at', '>=', $this->filters['start_date']);
            }
            
            if (isset($this->filters['end_date']) && $this->filters['end_date']) {
                $query->whereDate('created_at', '<=', $this->filters['end_date']);
            }
            
            if (isset($this->filters['status']) && $this->filters['status']) {
                switch ($this->filters['status']) {
                    case 'nuevo': $query->where('service_status_id', 1); break;
                    case 'atendiendo': $query->where('service_status_id', 2); break;
                    case 'cerrado': $query->where('service_status_id', 3); break;
                    case 'pendiente': $query->where('service_status_id', 4); break;
                    case 'completado': $query->where('service_status_id', 5); break;
                }
            }
            
            if (isset($this->filters['building_id']) && $this->filters['building_id']) {
                $query->where('building_id', $this->filters['building_id']);
            }
            
            if (isset($this->filters['department_id']) && $this->filters['department_id']) {
                $query->where('department_id', $this->filters['department_id']);
            }
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID Ticket',
            'Fecha Recepción',
            'Fecha Cierre',
            'Tiempo Atención (días L-V)',
            'Estatus',
            'Solicitud (Detalle)',
            'Edificio',
            'Departamento',
            'Quién Solicita',
            'Indicador',
            'Tipo Servicio',
            'Actividad Realizada',
            'Personal Asignado',
            'Último Seguimiento',
            'Estrellas'
        ];
    }

    public function map($ticket): array
    {
        $tiempoAtencion = '';
        if ($ticket->support_closing) {
            $diasHabiles = $this->calculateBusinessDays(
                $ticket->created_at,
                $ticket->support_closing
            );
            $tiempoAtencion = $diasHabiles . ' días';
        }

        $ultimoSeguimiento = '';
        if ($ticket->extraInfos->isNotEmpty()) {
            $ultimo = $ticket->extraInfos->sortByDesc('created_at')->first();
            $ultimoSeguimiento = $ultimo->description . ' (' . $ultimo->created_at->format('d/m/Y H:i') . ')';
        }

        return [
            $ticket->id,
            $ticket->created_at->format('d/m/Y H:i'),
            $ticket->support_closing ? $ticket->support_closing->format('d/m/Y H:i') : '',
            $tiempoAtencion,
            $ticket->serviceStatus->description ?? '',
            $ticket->description,
            $ticket->building->description ?? '',
            $ticket->department->description ?? '',
            $ticket->employee->full_name ?? '',
            $ticket->indicatorType->description ?? '',
            $ticket->anotherService->description ?? '',
            $ticket->activity_description ?? '',
            $ticket->supportPersonal ? 
                $ticket->supportPersonal->name . ' ' . $ticket->supportPersonal->lastnames : '',
            $ultimoSeguimiento,
            $ticket->stars ?? '0'
        ];
    }

    private function calculateBusinessDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $businessDays = 0;
        
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->dayOfWeek != Carbon::SATURDAY && $date->dayOfWeek != Carbon::SUNDAY) {
                $businessDays++;
            }
        }
        
        return $businessDays;
    }
}