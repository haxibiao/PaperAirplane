<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'status',
        'users',
        'bot_id',
        'sign',
        'remarks',
        'created_at',
        'updated_at',
    ];

}
