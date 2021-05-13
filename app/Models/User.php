<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'fs_user_name',
        'fs_user_avatar_url',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 获取该用户管理的全部 Bot
     */
    public function bots()
    {
        return $this->hasMany('App\Models\Bot');
    }

    /**
     * 获取该用户管理的全部 App
     */
    public function apps()
    {
        return $this->hasMany('App\Models\App');
    }

    /**
     * @description: 创建一个用户
     * @param {String} $fs_user_id
     * @param {String} $fs_user_name
     * @param {String} $fs_user_avatar_url
     * @return User
     */
    public static function create(String $fs_user_id, String $fs_user_name, String $fs_user_avatar_url)
    {
        $user                     = new User();
        $user->name               = Str::random(6);
        $user->password           = Str::random(30);
        $user->fs_user_id         = $fs_user_id;
        $user->fs_user_name       = $fs_user_name;
        $user->fs_user_avatar_url = $fs_user_avatar_url;
        $user->api_token          = Str::random(64);

        $user->save();

        return $user;
    }

    /**
     * @description: 获取一个用户
     * @param {Int} $id
     * @param {String} $fsUserId
     * @return User
     */
    public static function get(Int $id, String $fsUserId)
    {
        $user = null;
        if ($id) {
            $user = User::find($id);
        } else if ($fsUserId) {
            $user = User::where('fs_user_id', $fsUserId)->first();

            if (!$user) {
                // 用户不存在，尝试创建一个用户
                try {
                    $bot = Bot::get();
                    if (!$bot) {
                        // 系统异常，未配置默认 Bot
                        throw new Exception('系统异常，未配置默认 Bot');
                    }

                    $userInfo       = User::getFeishuIdByUserInfo($bot->fs_access_token, $fsUserId);
                    $fs_user        = isset($userInfo["user"]) ? $userInfo["user"] : null;
                    $fs_user_id     = isset($fs_user["user_id"]) ? $fs_user["user_id"] : $fsUserId;
                    $fs_user_name   = isset($fs_user["name"]) ? $fs_user["name"] : null;
                    $fs_user_avatar = isset($fs_user["avatar"]["avatar_72"]) ? $fs_user["avatar"]["avatar_72"] : null;

                    if (!$userInfo || !$fs_user || !$fs_user_id || !$fs_user_name || !$fs_user_avatar) {
                        // 飞书用户信息获取失败！
                        throw new Exception('飞书用户信息获取失败');
                    }

                    // 根据飞书想用户信息创建一个新的用户
                    $user = User::create($fs_user_id, $fs_user_name, $fs_user_avatar);
                    return $user;

                } catch (\Throwable $th) {
                    // 用户创建失败！
                    throw $th;
                }
            }

        } else {
            throw new Exception('id 和 fsUserId 都为空。');
        }

        if (!$user) {
            throw new Exception('该用户不存在。');
        }

        return $user;

    }

    /**
     * @description: 获取飞书登陆用户信息
     * @param String $accessToken
     * @param String $code
     * @return Array
     * 返回数据参考文档：https://open.feishu.cn/document/ukTMukTMukTM/uIDO4UjLygDO14iM4gTN
     */
    public static function getFeishuUserInfo(String $accessToken, String $code)
    {
        if ($accessToken && $code) {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json; charset=utf-8',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->post("https://open.feishu.cn/open-apis/authen/v1/access_token", [
                "app_access_token" => $accessToken,
                "grant_type"       => "authorization_code",
                "code"             => $code,
            ]);
            $resObj   = $response->json();
            $userData = isset($resObj["data"]) ? $resObj["data"] : null;

            if ($resObj && $userData) {
                return $userData;
            }

            return null;
        }

        return null;
    }

    /**
     * @description: 通过飞书 user_id 获取飞书用户信息
     * @param {String} $accessToken
     * @param {String} $fsUserId
     * @return Array
     * 返回数据参考文档：https://open.feishu.cn/document/uAjLw4CM/ukTMukTMukTM/reference/contact-v3/user/get
     */
    public static function getFeishuIdByUserInfo(String $accessToken, String $fsUserId)
    {
        if ($accessToken && $fsUserId) {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json; charset=utf-8',
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get("https://open.feishu.cn/open-apis/contact/v3/users/" . $fsUserId . "?user_id_type=user_id");
            $resObj   = $response->json();
            $backData = isset($resObj["data"]) ? $resObj["data"] : null;

            if ($resObj && $backData) {
                return $backData;
            }

            throw new Exception($resObj);
        }

        throw new Exception('accessToken 或 fsUserId 为空！');
    }

}
