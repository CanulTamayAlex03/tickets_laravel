<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraInfo extends Model
{
    use HasFactory;

    protected $table = 'extra_infos';

    protected $fillable = [
        'description',
        'request_id',
        'user_id'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}