<?php

namespace App\Http\Controllers;

use App\Models\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscribeController extends Controller
{
    //
    public function index(Request $request, $id)
    {

        $my = Auth::user();

        if (!$my) {
            // 用户未登陆
            return redirect('login/tofeishu/?appid=' . $id . '&');
        }

        $app = App::find($id);

        return view("subscribe", ['app' => $app ?? [], 'my' => $my]);
    }

}
