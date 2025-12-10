<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $table = 'users';

    protected $guard_name = 'web';

    protected $fillable = [
        'email',
        'encrypted_password',
        'estatus'
    ];

    protected $casts = [
        'estatus' => 'boolean'
    ];

    protected $hidden = [
        'encrypted_password',
        'remember_token',
    ];

    public function buildings()
    {
        return $this->belongsToMany(Building::class, 'users_has_buildings', 'user_id', 'building_id')
                    ->withTimestamps();
    }

    public function getAuthPassword()
    {
        return $this->encrypted_password;
    }

    public function getRolPrincipalAttribute()
    {
        return $this->roles->first()->name ?? 'Sin rol';
    }

    public function tieneRol($rol)
    {
        return $this->hasRole($rol);
    }
}