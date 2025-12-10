<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnotherService extends Model
{
    use HasFactory;

    protected $table = 'another_services';

    protected $fillable = [
        'description',
        'indicator_type_id'
    ];

    public function indicatorType()
    {
        return $this->belongsTo(IndicatorType::class, 'indicator_type_id');
    }
}