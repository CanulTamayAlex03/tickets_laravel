<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'employees';
    protected $fillable = ['name', 'lastname', 'lastname2', 'no_nomina', 'full_name'];
    
    protected $dates = ['deleted_at'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'employee_id');
    }
    
    public function ticketsWithTrashed()
    {
        return $this->hasMany(Ticket::class, 'employee_id')->withTrashed();
    }

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->lastname . ' ' . ($this->lastname2 ?? ''));
    }
}