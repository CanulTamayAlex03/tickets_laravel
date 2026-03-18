<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Extension extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'extensiones';

    protected $fillable = [
        'nombre_extension',
        'extension'
    ];

    protected $dates = ['deleted_at'];

    public $timestamps = false;
}
