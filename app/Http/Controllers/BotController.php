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

        // 获取当前登陆用户
        $user = Auth::user();

        if (!$user || !$fsAppID || !$fsAppSecret) {
            // 用户未登陆或缺少关键参数
            return response()->json(['code' => -1, 'msg' => '关键参数不完整或用户信息异常。', 'data' => null]);
        }

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
     * @description: 响应修改 Bot POST 请求函数
     * @param {Request} $request
     * @return {*}
     */
    public function ApiModifyBot(Request $request)
    {
        // 定义关键参数数组
        $id          = $request->json('id');
        $fsAppID     = $request->json('app_id', null);
        $fsAppSecret = $request->json('app_secret', null);
        $remarks     = $request->json("remarks", null);

        // 获取当前登陆用户
        $user = Auth::user();

        if (!$user || !$id) {
            // 用户未登陆或缺少关键参数
            return response()->json(['code' => -1, 'msg' => '关键参数不完整或用户信息异常。', 'data' => null]);
        }

        if (!$fsAppID || !$fsAppSecret) {
            // 缺少关键参数
            return response()->json(['code' => -1, 'msg' => '飞书 App ID 和 飞书 App Secret 不得为空。', 'data' => null]);
        }

        // 修改机器人信息
        try {
            $bot = Bot::modify($id, $fsAppID, $fsAppSecret, $remarks);
            if (!$bot) {
                return response()->json(['code' => -1, 'msg' => '机器人信息修改失败。', 'data' => null]);
            }
            return response()->json(['code' => 1, 'msg' => '', 'data' => $bot]);

        } catch (\Throwable $th) {
            return response()->json(['code' => -1, 'msg' => '机器人信息修改失败。' . $th->getMessage(), 'data' => null]);
        }

        // dd($request);
        // dd(Auth::user());
    }

    /**
     * @description: 响应获取当前用户全部 Bot GET 请求函数
     * @param {Request} $request
     * @return {*}
     */
    public function ApiGetListByUser(Request $request)
    {
        // 获取当前登陆用户
        $user = Auth::user();

        if (!$user) {
            // 用户未登陆
            return response()->json(['code' => -1, 'msg' => '用户信息异常。', 'data' => null]);
        }

        // $bots = Bot::where('user_id', $user->id)
        //     ->simplePaginate(15);

        $bots = User::find($user->id)->bots()->paginate(15);

        $data = [];
        foreach ($bots as $key => $bot) {
            $data[$key] = [
                "id"             => $bot->id,
                "user"           => [
                    'id'   => $bot->user->id,
                    'name' => $bot->user->fs_user_name,
                ],
                "fs_app_id"      => $bot->fs_app_id,
                "fs_app_secret"  => $bot->fs_app_secret,
                "fs_access_time" => $bot->fs_access_time,
                "remarks"        => $bot->remarks,
                "created_at"     => $bot->created_at,
                "updated_at"     => $bot->updated_at,
            ];
        }

        $list = [
            "count"          => $bots->count(), // 获取当前页数据的数量。
            "current_page"   => $bots->currentPage(), // 获取当前页页码。
            "per_page"       => $bots->perPage(), // 每页的数据条数。
            "has_more_pages" => $bots->hasMorePages(), // 是否有更多页。
            // "next_page_url"     => $bots->nextPageUrl(), // 获取下一页的 URL。
            // "previous_page_url" => $bots->previousPageUrl(), // 获取前一页的 URL。

            "data"           => $data,
        ];

        return response()->json(['code' => 1, 'msg' => '', 'data' => $list]);
    }

    /**
     * @description: 响应获取指定 Feishu Bot info GET 请求函数
     * @param {Request} $request
     * @return {*}
     */
    public function ApiGetBotFeishuInfo(Request $request)
    {
        // 获取当前登陆用户
        $user  = Auth::user();
        $botID = $request->input('id');

        if (!$user || !$botID) {
            return response()->json(['code' => -1, 'msg' => '关键参数不完整或用户信息异常。', 'data' => null]);
        }

        $bot = Bot::get($botID);
        if (!$bot) {
            return response()->json(['code' => -1, 'msg' => 'Bot 信息异常。', 'data' => null]);
        }

        try {
            $info = Bot::getFeishuAppInfo($bot);
        } catch (\Throwable $th) {
            return response()->json(['code' => -1, 'msg' => $th->getMessage(), 'data' => null]);
        }
        if (!$info) {
            return response()->json(['code' => -1, 'msg' => 'Bot 信息获取失败。', 'data' => null]);
        }

        return response()->json(['code' => 1, 'msg' => '', 'data' => $info]);

    }
}
