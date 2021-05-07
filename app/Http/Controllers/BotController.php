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
     * @description: 响应添加 Bot POST 请求函数
     * @param {Request} $request
     * @return {*}
     */
    public function ApiCreateBot(Request $request)
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
        $bot = Bot::create($user, $fsAppID, $fsAppSecret, $remarks);

        if (!$bot) {
            return response()->json(['code' => -1, 'msg' => '机器人添加失败。', 'data' => null]);
        }

        return response()->json(['code' => 1, 'msg' => '', 'data' => $bot]);

        // dd($request);
        // dd(Auth::user());
    }

    /**
     * @description: 相应获取当前用户全部 Bot GET 请求函数
     * @param {Request} $request
     * @return {*}
     */
    public function ApiGetListByUser(Request $request)
    {
        # code...
    }
}
