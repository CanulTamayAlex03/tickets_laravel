<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Building extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'buildings';

    protected $fillable = ['description'];

    protected $dates = ['deleted_at'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'users_has_buildings', 'building_id', 'user_id')
                    ->withTimestamps();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'building_id')
                    ->withTrashed();
    }
}