<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'departments';
    protected $fillable = ['description'];
    
    protected $dates = ['deleted_at'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'department_id');
    }
    
    public function ticketsWithTrashed()
    {
        return $this->hasMany(Ticket::class, 'department_id')->withTrashed();
    }
}