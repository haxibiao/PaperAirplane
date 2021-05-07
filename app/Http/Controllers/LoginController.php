<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\Bot;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function index(Request $request)
    {
        # code...
        // dd($request);
        return view("login");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * @description: 飞书授权回调页面
     * @param {Request} $request
     * @return {*}
     */
    public function feishu(Request $request)
    {
        $u_code  = $request->get("code");
        $u_appid = $request->get("state");

        if (!$u_code) {
            // 授权失败
            abort(403, "用户授权获取失败，请稍后重试！");
        }

        if ($u_appid) {
            $app       = App::where('id', $u_appid)->get();
            $appid     = $app->bot->fs_app_id;
            $appsecret = $app->bot->fs_app_secret;
        } else {
            $appid     = env("FS_BASE_APP_ID");
            $appsecret = env("FS_BASE_APP_SECRET");
        }

        $bot = Bot::where('fs_app_id', $appid)->first();
        if (!$bot) {
            $bot = Bot::create(User::find(1), $appid, $appsecret);
        }

        $fs_user = User::getFeishuUserInfo($bot->fs_access_token, $u_code);

        if (!$fs_user && !isset($fs_user['user_id'])) {
            abort(403, "用户信息获取失败，请稍后重试！");
        }

        $fs_user_id         = $fs_user['user_id'];
        $fs_user_name       = $fs_user['name'];
        $fs_user_avatar_url = $fs_user['avatar_url'];
        $user               = User::where("fs_user_id", $fs_user_id)->first();

        if (!$user) {
            $user = User::create($fs_user_id, $fs_user_name, $fs_user_avatar_url);
        }

        Auth::login($user, true);
        return redirect()->intended('admin');
    }

    /**
     * @description: 跳转飞书授权登陆页面
     * @param {Request} $request
     * @return {*}
     */
    public function toFeishu(Request $request)
    {
        $webUrl      = env("APP_URL") . "/login/feishu"; // 登陆结果接受路由
        $fsAppID     = env("FS_BASE_APP_ID"); // 飞书默认 APPID
        $fsAppSecret = env("FS_BASE_APP_SECRET"); // 飞书默认 SECRET

        $appID  = $request->get("appid"); // 获取到 appid 参数的话表示，是其他通知应用请求的登陆
        $action = $request->get("action"); // 动作
        // TODO: 这里待实现通过 action 区分管理用户登陆还是普通订阅用户登陆，以便重定向不同 URL

        if ($fsAppID == null || $fsAppSecret == null) {
            abort(500, '项目飞书默认配置异常，请通知开发者！');
        }

        $autoLoginUrl = "https://open.feishu.cn/open-apis/authen/v1/index?redirect_uri=" . urldecode($webUrl) . "&app_id=" . $fsAppID . "&state=" . $appID;
        return redirect()->away($autoLoginUrl);
    }

}
