<?php
/*
 * @Author: Bin
 * @Date: 2021-04-04
 * @FilePath: /PaperAirplane/app/Http/Controllers/BotController.php
 */

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        if ($user) {
            $bot->user_id = $user->id;
        }

        try {
            $accessTokenObj       = BotController::getFeishuAppAccessToken($fsAppID, $fsAppSecret);
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

    /**
     * @description: 响应添加 Bot POST 请求函数
     * @param {Request} $request
     * @return {*}
     */
    public static function apiCreateBot(Request $request)
    {
        // 定义关键参数数组

        $fsAppID     = $request->json('app_id');
        $fsAppSecret = $request->json('app_secret');
        $remarks     = $request->json("remarks", "");

        if (!Auth::check() || !$fsAppID || !$fsAppSecret) {
            // 用户未登陆或缺少关键参数
            return response()->json(['code' => -1, 'msg' => '关键参数不完整或用户信息异常。', 'data' => null]);
        }

        // 获取当前登陆用户
        $user = Auth::user();

        if (Bot::where('fs_app_id', $fsAppID)->first()) {
            // 机器人已添加过
            return response()->json(['code' => -1, 'msg' => '机器人已存在，添加失败。', 'data' => null]);
        }

        // 添加机器人成功
        $bot = BotController::create($user, $fsAppID, $fsAppSecret, $remarks);

        if (!$bot) {
            return response()->json(['code' => -1, 'msg' => '机器人添加失败。', 'data' => null]);
        }

        return response()->json(['code' => 1, 'msg' => '', 'data' => $bot]);

        // dd($request);
        // dd(Auth::user());
    }
}
