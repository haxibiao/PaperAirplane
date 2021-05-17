<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Bot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UseAppController extends Controller
{

    /**
     * @description: 响应获取订阅应用信息接口
     * @param {Request} $request
     * @param {*} $id
     * @return {*}
     */
    public function ApiAppData(Request $request, $id)
    {
        // 获取请求参数
        $appID = $id;

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

        // 获取机器人信息
        $botInfo = null;
        try {
            $bot = Bot::get($app->bot->id, null);
            if ($bot) {
                $botInfo = Bot::getFeishuAppInfo($bot);
            }
        } catch (\Throwable $th) {
            return response()->json(['code' => -1, 'msg' => $th->getMessage(), 'data' => null]);
        }
        if (!$botInfo) {
            return response()->json(['code' => -1, 'msg' => 'Bot 信息获取失败。', 'data' => null]);
        }

        // 判断该用户是否订阅该应用
        $isSubscribe = false;
        $users       = json_decode($app->users);
        foreach ($users as $key => $item) {
            if ($my->fs_user_id == $item) {
                $isSubscribe = true;
            }
        }

        return response()->json(['code' => 1, 'msg' => '', 'data' => [
            "id"           => $appID,
            "status"       => $app->status,
            "remarks"      => $app->remarks,
            "is_subscribe" => $isSubscribe,
            "bot"          => [
                "id"      => $app->bot->id,
                "remarks" => $app->bot->remarks,
                "status"  => $botInfo['activate_status'],
                "name"    => $botInfo['app_name'],
                "icon"    => $botInfo['avatar_url'],
            ],
        ]]);

    }

    /**
     * @description: 响应用户取消订阅应用接口
     * @param {Request} $request
     * @param {*} $id
     * @return {*}
     */
    public function ApiUnsubscribeByMe(Request $request, $id)
    {
        // 获取请求参数
        $appID = $id;

        $me = Auth::user();
        if (!$me || !$appID) {
            // 用户未登陆或关键参数未传递
            return response()->json(['code' => -1, 'msg' => '用户信息异常或参数未输入。', 'data' => null]);
        }

        // 获取通知应用信息
        $app = App::find($appID);
        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '此通知应用不存在。', 'data' => null]);
        }

        try {
            $app = App::usersDelete($app, $me);
        } catch (\Throwable $th) {
            return response()->json(['code' => -1, 'msg' => $th->getMessage(), 'data' => null]);
        }

        return response()->json(['code' => 1, 'msg' => '', 'data' => null]);

    }

    /**
     * @description: 响应用户请求订阅应用接口
     * @param {Request} $request
     * @param {*} $id
     * @return {*}
     */
    public function ApiSubscribeByMe(Request $request, $id)
    {
        // 获取请求参数
        $appID = $id;

        $me = Auth::user();
        if (!$me || !$appID) {
            // 用户未登陆或关键参数未传递
            return response()->json(['code' => -1, 'msg' => '用户信息异常或参数未输入。', 'data' => null]);
        }

        // 获取通知应用信息
        $app = App::find($appID);
        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '此通知应用不存在。', 'data' => null]);
        }

        try {
            $app = App::usersAdd($app, $me);
        } catch (\Throwable $th) {
            return response()->json(['code' => -1, 'msg' => $th->getMessage(), 'data' => null]);
        }

        return response()->json(['code' => 1, 'msg' => '', 'data' => null]);

    }

    /**
     * @description: 响应使用应用请求发送文本消息接口
     * @param {Request} $request
     * @param {*} $id
     * @return {*}
     */
    public function ApiMessagePushText(Request $request, $id)
    {
        // 获取请求参数
        $appID   = $id;
        $sign    = $request->json('sign');
        $massage = $request->json('message');

        if (!$appID || !$massage || !$sign) {
            // 用户未登陆或关键参数未传递
            return response()->json(['code' => -1, 'msg' => '用户信息异常或参数未输入。', 'data' => null]);
        }

        $app = App::where([
            ['id', '=', $appID],
            ['sign', '=', $sign],
        ])->first();

        if (!$app) {
            return response()->json(['code' => -1, 'msg' => '通知应用不存在或 sign 异常。', 'data' => null]);
        }

        try {
            $callBack = App::pushMessageToUsers($app, "text", '{ "text": "' . $massage . '" }');
        } catch (\Throwable $th) {
            return response()->json(['code' => -1, 'msg' => $th->getMessage(), 'data' => null]);
        }

        return response()->json(['code' => 1, 'msg' => $callBack['msg'], 'data' => $callBack['data']]);

    }

}
