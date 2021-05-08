<?php

namespace App\Models;

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
     * @return {*}
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
}
