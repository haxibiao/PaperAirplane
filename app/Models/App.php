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

    /**
     * 获取应用管理用户
     */
    public function user()
    {
        return $this->belongsTo('App\Models\Users');
    }

    /**
     * 获取该应用关联的机器人
     */
    public function bot()
    {
        return $this->belongsTo('App\Models\Bots');
    }

}
