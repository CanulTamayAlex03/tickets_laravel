<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndicatorType extends Model
{
    use HasFactory;

    protected $table = 'indicator_types';

    protected $fillable = [
        'description'
    ];

    public function anotherServices()
    {
        return $this->hasMany(AnotherService::class, 'indicator_type_id');
    }
}