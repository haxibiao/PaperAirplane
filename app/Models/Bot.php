<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * 获取该机器人的管理员
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @description: 创建一个 Bot 配置
     * @param {User} $user
     * @param {String} $fsAppID
     * @param {String} $fsAppSecret
     * @param {String} $remarks
     * @return {*}
     */
    public static function create(User $user, String $fsAppID, String $fsAppSecret, String $remarks = "")
    {
        $bot                = new Bot();
        $bot->fs_app_id     = $fsAppID;
        $bot->fs_app_secret = $fsAppSecret;
        $bot->remarks       = $remarks;

        if ($user) {
            $bot->user_id = $user->id;
        }

        try {
            $accessTokenObj       = Bot::getFeishuAppAccessToken($fsAppID, $fsAppSecret);
            $bot->fs_access_token = $accessTokenObj["token"];
            $bot->fs_access_time  = time() + $accessTokenObj["time"];
        } catch (\Throwable $th) {
            // 机器人添加失败，获取飞书 Access Token 出现异常
            return null;
        }

        $bot->save();
        return $bot;
    }

    /**
     * @description: 获取飞书 Access Token（企业自建应用）
     * @param {String} $fsAppID
     * @param {String} $fsAppSecret
     * @return {array}
     * 对接文档：https://open.feishu.cn/document/ukTMukTMukTM/uIjNz4iM2MjLyYzM
     */
    public static function getFeishuAppAccessToken(String $fsAppID, String $fsAppSecret): array
    {
        $token = "";
        $time  = 0;

        if ($fsAppID && $fsAppSecret) {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json; charset=utf-8',
            ])->post("https://open.feishu.cn/open-apis/auth/v3/tenant_access_token/internal/", [
                "app_id"     => $fsAppID,
                "app_secret" => $fsAppSecret,
            ]);
            $resObj = $response->json();
            $token  = $resObj['tenant_access_token'];
            $time   = $resObj['expire'];
        }

        return ["token" => $token, "time" => $time];
    }

}
