<?php
/*
 * @Author: Bin
 * @Date: 2021-04-04
 * @FilePath: /PaperAirplane/app/Http/Controllers/BotController.php
 */

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class BotController extends Controller
{

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
        $bot->user          = $user;

        $accessTokenObj       = BotController::getFeishuAppAccessToken($fsAppID, $fsAppSecret);
        $bot->fs_access_token = $accessTokenObj["token"];
        $bot->fs_access_time  = time() + $accessTokenObj["time"];

        // $bot->save();

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
