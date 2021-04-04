<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'fs_app_id',
        'fs_app_secret',
        'fs_access_token',
        'fs_access_time',
        'remarks',
        'created_at',
        'updated_at',
    ];

    /**
     * 获取该机器人的管理员
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
