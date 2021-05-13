<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Bot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppController extends Controller
{

    /**
     * @description: 相应创建通知应用请求接口
     * @param {Request} $request
     * @return {*}
     */
    public function ApiCreateApp(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            // 用户未登陆
            return response()->json(['code' => -1, 'msg' => '用户信息异常。', 'data' => null]);
        }

        // 获取请求参数
        $botID   = $request->json('bot_id');
        $status  = $request->json('status', 1);
        $remarks = $request->json("remarks", "");

        if (!$botID) {
            return response()->json(['code' => -1, 'msg' => '未传入需要绑定的 Bot ID', 'data' => null]);
        }
        $bot = Bot::find($botID);
        if (!$bot) {
            return response()->json(['code' => -1, 'msg' => 'Bot ID 异常，不存在该机器人', 'data' => null]);
        }

        $app = App::create($user, $bot, [], $remarks, $status);
        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '通知 App 创建失败！', 'data' => null]);
        }

        return response()->json(['code' => 1, 'msg' => '', 'data' => $app]);

    }

    public function ApiGetListByUser(Request $request)
    {
        // 获取当前登陆用户
        $user = Auth::user();

        if (!$user) {
            // 用户未登陆
            return response()->json(['code' => -1, 'msg' => '用户信息异常。', 'data' => null]);
        }

        $apps = User::find($user->id)->apps()->paginate(15);

        $data = [];
        foreach ($apps as $key => $app) {
            $data[$key] = [
                "id"         => $app->id,
                "sign"       => $app->sign,
                "status"     => $app->status,
                "remarks"    => $app->remarks,
                "users"      => json_decode($app->users),
                "created_at" => $app->created_at,
                "updated_at" => $app->updated_at,
                "user"       => [
                    'id'   => $app->user->id,
                    'name' => $app->user->fs_user_name,
                ],
                "bot"        => [
                    'id'      => $app->bot->id,
                    'remarks' => $app->bot->remarks,
                ],

            ];
        }

        $list = [
            "count"          => $apps->count(), // 获取当前页数据的数量。
            "current_page"   => $apps->currentPage(), // 获取当前页页码。
            "per_page"       => $apps->perPage(), // 每页的数据条数。
            "has_more_pages" => $apps->hasMorePages(), // 是否有更多页。
            // "next_page_url"     => $apps->nextPageUrl(), // 获取下一页的 URL。
            // "previous_page_url" => $apps->previousPageUrl(), // 获取前一页的 URL。

            "data"           => $data,
        ];

        return response()->json(['code' => 1, 'msg' => '', 'data' => $list]);
    }

    public function ApiGetSubscribeUserList(Request $request)
    {

        // 获取请求参数
        $appID = $request->input('id');

        $user = Auth::user();
        if (!$user) {
            // 用户未登陆或关键参数未传递
            return response()->json(['code' => -1, 'msg' => '用户信息异常或 APP ID 未输入。', 'data' => null]);
        }

        $app = App::find($appID);
        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '该通知应用 APP 不存在。', 'data' => null]);
        }

        $users         = json_decode($app->users);
        $callBackUsers = [];
        foreach ($users as $key => $item) {
            try {
                $user_item = User::get(0, $item);
                array_push($callBackUsers, $user_item);
            } catch (\Throwable $th) {
                // 用户异常，跳过该用户
                dd($th);
            }
        }

        return response()->json(['code' => -1, 'msg' => '', 'data' => $callBackUsers]);

    }
}
