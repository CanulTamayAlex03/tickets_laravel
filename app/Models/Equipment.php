<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    protected $fillable = [
        'description'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'equipment_id');
    }
}