<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Ticket extends Model
{
    use HasFactory;

    protected $table = 'requests';

    protected $fillable = [
        'description',
        'department_id',
        'building_id',
        'employee_id',
        'service_status_id',
    ];
    
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function serviceStatus()
    {
        return $this->belongsTo(ServiceStatus::class, 'service_status_id');
    }
}
