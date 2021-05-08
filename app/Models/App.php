<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 获取该应用关联的机器人
     */
    public function bot()
    {
        return $this->belongsTo('App\Models\Bot');
    }

    /**
     * @description: 创建一个 APP
     * @param {User} $user
     * @param {Bot} $bot
     * @param {array} $users
     * @param {String} $remarks
     * @return {*}
     */
    public static function create(User $user, Bot $bot, array $users = [], String $remarks = "", int $status = 1)
    {
        if (!$user || !$bot) {
            return null;
        }

        $app          = new App();
        $app->user_id = $user->id;
        $app->bot_id  = $bot->id;
        $app->sign    = Str::uuid();
        $app->users   = json_encode($users);
        $app->remarks = $remarks;
        $app->status  = $status;

        $app->save();

        return $app;

    }

}
