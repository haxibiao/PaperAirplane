<?php

namespace App\Models;

use Exception;
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
     * 获取该机器人关联的全部 APP
     */
    public function apps()
    {
        return $this->hasMany('App\Models\App');
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
            $bot->save();
        } catch (\Throwable $th) {
            // 机器人添加失败，获取飞书 Access Token 出现异常
            return null;
        }

        return $bot;
    }

    /**
     * @description: 获取一个指定的 Bot 配置，不传参数则获取默认 Bot 配置
     * @param {Int} $id
     * @param {String} $fsAppID
     * @param {String} $fsAppToken
     * @return Bot
     */
    public static function get(Int $id = null, String $fsAppID = null, String $fsAppToken = null)
    {
        $bot = null;
        if (!$id && !$fsAppID && !$fsAppToken) {
            $bot = Bot::where('id', 1)->first();
        }

        if ($id) {
            $bot = Bot::where('id', $id)->first();
        }

        if ($fsAppID) {
            $bot = Bot::where('fs_app_id', $fsAppID)->first();
        }

        if ($fsAppToken) {
            $bot = Bot::where('fs_access_token', $fsAppToken)->first();
        }

        if (!$bot) {
            return $bot;
        }

        if ($bot->fs_access_time <= time()) {
            try {
                $accessTokenObj       = Bot::getFeishuAppAccessToken($bot->fs_app_id, $bot->fs_app_secret);
                $bot->fs_access_token = $accessTokenObj["token"];
                $bot->fs_access_time  = time() + $accessTokenObj["time"];
                $bot->save();
            } catch (\Throwable $th) {
                // 刷新飞书 Access Token 出现异常
                $bot = null;
            }
        }

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

    /**
     * @description:
     * @param {Bot} $bot
     * @return {*}
     */
    public static function getFeishuAppInfo(Bot $bot)
    {
        if (!$bot) {
            return null;
        }

        $accessToken = $bot->fs_access_token;

        if ($accessToken) {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json; charset=utf-8',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://open.feishu.cn/open-apis/bot/v3/info");
            $resObj  = $response->json();
            $botData = isset($resObj["bot"]) ? $resObj["bot"] : null;

            if ($resObj && $botData) {
                return $botData;
            } else if (isset($resObj["msg"])) {
                throw new Exception($resObj["msg"]);
            }

            return null;
        }

        return null;
    }

}
