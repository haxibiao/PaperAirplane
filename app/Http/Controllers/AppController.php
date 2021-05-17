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
     * @description: 响应创建通知应用请求接口
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

    /**
     * @description: 响应根据用户获取应用列表请求接口
     * @param {Request} $request
     * @return {*}
     */
    public function ApiGetListByUser(Request $request)
    {
        // 获取当前登陆用户
        $me = Auth::user();

        if (!$me) {
            // 用户未登陆
            return response()->json(['code' => -1, 'msg' => '用户信息异常。', 'data' => null]);
        }

        if ($me->name == "admin") {
            // admin 用户拥有管理全部 app 权限
            $apps = App::all()->paginate(15);
        } else {
            $apps = User::find($me->id)->apps()->paginate(15);
        }

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

    /**
     * @description: 响应获取订阅用户列表接口
     * @param {Request} $request
     * @return {*}
     */
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
                // dd($th);
            }
        }

        return response()->json(['code' => -1, 'msg' => '', 'data' => $callBackUsers]);

    }

    /**
     * @description: 响应将一个用户添加到订阅列表接口
     * @param {Request} $request
     * @return {*}
     */
    public function ApiAddSubscribeUser(Request $request)
    {
        // 获取请求参数
        $appID    = $request->json('app_id');
        $userID   = $request->json('user_id');
        $fsUserID = $request->json('fs_user_id');

        $my = Auth::user();
        if (!$my || !$appID || (!$userID && !$fsUserID)) {
            // 用户未登陆或关键参数未传递
            return response()->json(['code' => -1, 'msg' => '用户信息异常或参数未输入。', 'data' => null]);
        }

        // 判断应用是否存在
        $app = App::find($appID);
        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '该通知应用 APP 不存在。', 'data' => null]);
        }

        // 判断用户是否存在
        $user = null;
        try {
            if ($userID) {
                $user = User::get($userID, null);
            } else if ($fsUserID) {
                $user = User::get(null, $fsUserID);
            }

            if ($user->name == "admin") {
                return response()->json(['code' => -1, 'msg' => 'admin 用户不允许添加订阅。', 'data' => null]);
            }

            if (!$user) {
                return response()->json(['code' => -1, 'msg' => '该用户不存在。', 'data' => null]);
            }
        } catch (\Throwable $th) {
            return response()->json(['code' => -1, 'msg' => $th->getMessage(), 'data' => null]);
        }

        // 获取应用已有订阅用户，将用户添加至订阅用户列表
        $users = json_decode($app->users);
        if ($user->fs_user_id) {
            // 判断用户是否已经订阅，订阅用户列表判重
            foreach ($users as $key => $item) {
                if ($item == $user->fs_user_id) {
                    return response()->json(['code' => -1, 'msg' => '该用户已订阅该通知。', 'data' => null]);
                }
            }
            $users[] = $user->fs_user_id;
        }

        $app->users = $users;
        $app->save();

        return response()->json(['code' => 1, 'msg' => '', 'data' => $app]);

    }

    /**
     * @description: 响应登陆用户订阅一个应用接口
     * @param {Request} $request
     * @return {*}
     */
    public function ApiAddSubscribeMy(Request $request)
    {
        // 获取请求参数
        $appID = $request->json('app_id');

        $my = Auth::user();
        if (!$my || !$appID) {
            // 用户未登陆或关键参数未传递
            return response()->json(['code' => -1, 'msg' => '用户信息异常或参数未输入。', 'data' => null]);
        }

        if ($my->name == "admin") {
            return response()->json(['code' => -1, 'msg' => 'admin 用户不允许添加订阅。', 'data' => null]);
        }

        // 判断应用是否存在
        $app = App::find($appID);
        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '该通知应用 APP 不存在。', 'data' => null]);
        }

        // 获取应用已有订阅用户，将用户添加至订阅用户列表
        $users = json_decode($app->users);
        if ($my->fs_user_id) {
            // 判断用户是否已经订阅，订阅用户列表判重
            foreach ($users as $key => $item) {
                if ($item == $my->fs_user_id) {
                    return response()->json(['code' => -1, 'msg' => '你已订阅该通知。', 'data' => null]);
                }
            }
            $users[] = $my->fs_user_id;
        }

        $app->users = $users;
        $app->save();

        return response()->json(['code' => 1, 'msg' => '', 'data' => $app]);

    }

    /**
     * @description: 响应将一个用户从应用订阅列表移除请求
     * @param {Request} $request
     * @return {*}
     */
    public function ApiDeleteSubscribeUser(Request $request)
    {
        // 获取请求参数
        $appID    = $request->json('app_id');
        $userID   = $request->json('user_id');
        $fsUserID = $request->json('fs_user_id');

        $my = Auth::user();
        if (!$my || !$appID || (!$userID && !$fsUserID)) {
            // 用户未登陆或关键参数未传递
            return response()->json(['code' => -1, 'msg' => '用户信息异常或参数未输入。', 'data' => null]);
        }

        // 判断应用是否存在
        $app = App::find($appID);
        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '该通知应用 APP 不存在。', 'data' => null]);
        }

        // 判断用户是否存在
        $user = null;
        try {
            if ($userID) {
                $user = User::get($userID, null);
            } else if ($fsUserID) {
                $user = User::get(null, $fsUserID);
            }
            if (!$user) {
                return response()->json(['code' => -1, 'msg' => '该用户不存在。', 'data' => null]);
            }
        } catch (\Throwable $th) {
            return response()->json(['code' => -1, 'msg' => $th->getMessage(), 'data' => null]);
        }

        // 获取应用已有订阅用户，将用户从订阅用户列表移除
        $users = json_decode($app->users);
        if ($user->fs_user_id) {
            $user_keys = array_keys($users, $user->fs_user_id);
            if (!$user_keys) {
                return response()->json(['code' => -1, 'msg' => '此用户未订阅该通知。', 'data' => null]);
            } else {
                array_splice($users, $user_keys[0], 1);
            }
        }

        $app->users = $users;
        $app->save();

        return response()->json(['code' => 1, 'msg' => '', 'data' => $app]);

    }

    /**
     * @description: 响应登陆用户取消通知订阅
     * @param {Request} $request
     * @return {*}
     */
    public function ApiDeleteSubscribeMy(Request $request)
    {
        // 获取请求参数
        $appID = $request->json('app_id');

        $my = Auth::user();
        if (!$my || !$appID) {
            // 用户未登陆或关键参数未传递
            return response()->json(['code' => -1, 'msg' => '用户信息异常或参数未输入。', 'data' => null]);
        }

        // 判断应用是否存在
        $app = App::find($appID);
        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '该通知应用 APP 不存在。', 'data' => null]);
        }

        // 获取应用已有订阅用户，将用户从订阅用户列表移除
        $users = json_decode($app->users);
        if ($my->fs_user_id) {
            $user_keys = array_keys($users, $my->fs_user_id);
            if (!$user_keys) {
                return response()->json(['code' => -1, 'msg' => '你未订阅该通知。', 'data' => null]);
            } else {
                array_splice($users, $user_keys[0], 1);
            }
        }

        $app->users = $users;
        $app->save();

        return response()->json(['code' => 1, 'msg' => '', 'data' => $app]);

    }

}
