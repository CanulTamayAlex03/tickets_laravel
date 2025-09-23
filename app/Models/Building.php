<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    use HasFactory;

    protected $table = 'buildings';

    protected $fillable = ['description'];

    // Relación muchos a muchos con users
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_has_buildings', 'building_id', 'user_id')
                    ->withTimestamps();
    }
}