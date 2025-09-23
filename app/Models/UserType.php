<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class UserType extends Model
{
    use HasFactory;

    protected $table = 'user_types';

    protected $fillable = ['description', 'role_id'];

    public function users()
    {
        return $this->hasMany(User::class, 'user_type_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    protected static function booted()
    {
        static::created(function ($userType) {
            $role = Role::create([
                'name' => \Illuminate\Support\Str::slug($userType->description),
                'guard_name' => 'web'
            ]);
            
            $userType->update(['role_id' => $role->id]);
        });

        static::deleting(function ($userType) {
            if ($userType->role) {
                $userType->role->delete();
            }
        });
    }
}