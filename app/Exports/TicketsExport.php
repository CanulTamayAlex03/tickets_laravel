<?php

namespace App\Exports;

use App\Models\Ticket;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class TicketsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
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

            if (isset($this->filters['support_personal_id']) && $this->filters['support_personal_id']) {
            $query->where('support_personal_id', $this->filters['support_personal_id']);
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
            'Tiempo Atención',
            'Estatus',
            'Solicitud',
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
            $ultimoSeguimiento = $ultimo->description;
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

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                
                $sheet->insertNewRowBefore(1, 2);
                
                $sheet->mergeCells('A1:O1');
                $sheet->setCellValue('A1', 'REPORTE DE TICKETS - SISTEMA DE SOPORTE');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['rgb' => '6A5ACD'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);
                
                $sheet->mergeCells('A2:O2');
                $sheet->setCellValue('A2', 'Generado el: ' . Carbon::now()->format('d/m/Y
                '));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['rgb' => '666666'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);
                

                $sheet->getStyle('A3:O3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '6A5ACD'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => 'FFFFFF'],
                        ],
                    ],
                ]);
                
                $sheet->getRowDimension(3)->setRowHeight(25);
                
                $sheet->freezePane('A4');
                
                $sheet->setAutoFilter('A3:O3');
                
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                
                if ($highestRow > 3) {
                    $sheet->getStyle('A4:' . $highestColumn . $highestRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'DDDDDD'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_TOP,
                            'wrapText' => true,
                        ],
                    ]);
                    
                    for ($row = 4; $row <= $highestRow; $row++) {
                        $fillColor = ($row % 2 == 0) ? 'FFFFFF' : 'F8F9FA';
                        
                        $sheet->getStyle('A' . $row . ':' . $highestColumn . $row)
                            ->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB($fillColor);
                        
                        $sheet->getRowDimension($row)->setRowHeight(-1);
                    }
                    
                    $sheet->getStyle('A4:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D4:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E4:E' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('O4:O' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    $sheet->getStyle('B4:C' . $highestRow)->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm');
                    
                    $sheet->getStyle('A4:A' . $highestRow)->getNumberFormat()->setFormatCode('0');
                    $sheet->getStyle('O4:O' . $highestRow)->getNumberFormat()->setFormatCode('0');
                }
                
                $sheet->getColumnDimension('F')->setWidth(40);
                $sheet->getColumnDimension('N')->setWidth(50);
                $sheet->getColumnDimension('L')->setWidth(35);
                
                foreach(range('A', 'O') as $column) {
                    if (!in_array($column, ['F', 'N', 'L'])) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
                
                $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '6A5ACD'],
                        ],
                    ],
                ]);
            },
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