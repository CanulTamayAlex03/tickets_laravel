<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceStatus extends Model
{
    use HasFactory;

    protected $table = 'service_statuses';

    protected $fillable = [
        'description'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'service_status_id');
    }
}