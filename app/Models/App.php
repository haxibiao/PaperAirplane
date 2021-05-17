<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
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

    // 获取 APP 的 Bot (此处获取的 Bot 才能保证 accessToken 有效)
    public function getBot()
    {
        return Bot::get($this->bot->id);
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

    /**
     * @description: 添加用户到订阅用户列表
     * @param {App} $app
     * @param {User} $user
     * @return {*}
     */
    public static function usersAdd(App $app, User $user)
    {
        if (!$app || !$user) {
            throw new Exception("通知 APP 或用户为空。");
        }

        // 获取应用已有订阅用户，将用户添加至订阅用户列表
        $users = json_decode($app->users);
        if ($user->fs_user_id) {
            // 判断用户是否已经订阅，订阅用户列表判重
            foreach ($users as $key => $item) {
                if ($item == $user->fs_user_id) {
                    throw new Exception("该用户已订阅此通知。");
                }
            }
            $users[] = $user->fs_user_id;
        }

        try {
            $app->users = $users;
            $app->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        return $app;
    }

    /**
     * @description: 将用户移出订阅用户列表
     * @param {App} $app
     * @param {User} $user
     * @return {*}
     */
    public static function usersDelete(App $app, User $user)
    {
        if (!$app || !$user) {
            throw new Exception("通知 APP 或用户为空。");
        }

        // 获取应用已有订阅用户，将用户从订阅用户列表移除
        $users = json_decode($app->users);
        if ($user->fs_user_id) {
            $user_keys = array_keys($users, $user->fs_user_id);
            if (!$user_keys) {
                throw new Exception("该用户未订阅此通知。");
            } else {
                array_splice($users, $user_keys[0], 1);
            }
        }

        try {
            $app->users = $users;
            $app->save();
        } catch (\Throwable $th) {
            throw $th;
        }

        return $app;
    }

    public static function pushMessageToUsers(App $app, String $type, String $content)
    {

        if (!$app || !$type || !$content) {
            throw new Exception("参数不完整！");
        }

        $bot = $app->getBot();
        if (!$bot) {
            throw new Exception("该应用绑定的 Bot 异常。");
        }

        try {
            $users      = json_decode($app->users);
            $contentObj = json_decode($content);
            if (count($users) < 1) {
                // 判断该应用不存在任何订阅用户
                return null;
            }
        } catch (\Throwable $th) {
            throw $th;
        }

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json; charset=utf-8',
            'Authorization' => 'Bearer ' . $bot->fs_access_token,
        ])->post("https://open.feishu.cn/open-apis/message/v4/batch_send/", [
            "user_ids" => $users,
            "msg_type" => $type,
            "content"  => $contentObj,
        ]);
        $resObj       = $response->json();
        $callBackData = isset($resObj["data"]) ? $resObj["data"] : null;

        return $resObj;
    }

}
