<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportPersonal extends Model
{
    use HasFactory;

    protected $table = 'support_personals';

    protected $fillable = [
        'name',
        'lastnames',
        'email',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return User::where('email', $this->email)->first();
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'support_personal_id');
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' ' . $this->lastnames;
    }

    public function hasUserAccount()
    {
        return !is_null($this->user());
    }
}