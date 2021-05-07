<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{

    /**
     * @description: 获取我的用户信息
     * @param {Request} $request
     * @return {*}
     */
    public function ApiGetMe(Request $request)
    {
        $value = $request->cookie('api_token');

        dd($value);
        $user = Auth::user();

        if (!$user) {
            return response()->json(['code' => -1, 'msg' => '用户信息异常。', 'data' => null]);
        }

        return response()->json(['code' => 1, 'msg' => '', 'data' => $user]);
    }

    /**
     * @description: 获取全部用户列表
     * @param {Request} $request
     * @return {*}
     */
    public function ApiGetList(Request $request)
    {
        $users = User::paginate(15);

        $data = [];
        foreach ($users as $key => $user) {
            $data[$key] = [
                "id"                 => $user->id,
                "name"               => $user->name,
                "email_verified_at"  => $user->email_verified_at,
                "fs_user_id"         => $user->fs_user_id,
                "fs_user_name"       => $user->fs_user_name,
                "fs_user_avatar_url" => $user->fs_user_avatar_url,
                "created_at"         => $user->created_at,
                "updated_at"         => $user->updated_at,
            ];
        }

        $list = [
            "count"          => $users->count(), // 获取当前页数据的数量。
            "current_page"   => $users->currentPage(), // 获取当前页页码。
            "per_page"       => $users->perPage(), // 每页的数据条数。
            "has_more_pages" => $users->hasMorePages(), // 是否有更多页。
            // "next_page_url"     => $users->nextPageUrl(), // 获取下一页的 URL。
            // "previous_page_url" => $users->previousPageUrl(), // 获取前一页的 URL。

            "data"           => $data,
        ];

        return response()->json(['code' => 1, 'msg' => '', 'data' => $list]);
    }

}
