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
        'closed_by_user',
        'retroalimentation',
        'stars',
        'support_closing',
        'support_personal_id',
        'indicator_type_id',
        'another_service_id',
        'equipment_id',
        'activity_description',
        'equipment_number',
        'mk_pendient'

    ];
    
    protected $casts = [
        'closed_by_user' => 'boolean',
        'stars' => 'integer',
        'mk_pendient' => 'boolean',
        'support_closing' => 'datetime',
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

    public function supportPersonal()
    {
        return $this->belongsTo(SupportPersonal::class, 'support_personal_id');
    }

    public function indicatorType()
    {
        return $this->belongsTo(IndicatorType::class, 'indicator_type_id');
    }

    public function anotherService()
    {
        return $this->belongsTo(AnotherService::class, 'another_service_id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function extraInfos()
    {
        return $this->hasMany(ExtraInfo::class, 'request_id');
    }
}