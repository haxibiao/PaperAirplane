<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\BotController;
use App\Http\Controllers\UseAppController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->post('/bot/create', [BotController::class, 'ApiCreateBot']);
Route::middleware('auth:api')->post('/bot/modify', [BotController::class, 'ApiModifyBot']);
Route::middleware('auth:api')->get('/bot/list', [BotController::class, 'ApiGetListByUser']);
Route::middleware('auth:api')->get('/bot/info', [BotController::class, 'ApiGetBotFeishuInfo']);

Route::middleware('auth:api')->get('/user/me', [UserController::class, 'ApiGetMe']);
Route::middleware('auth:api')->get('/user/list', [UserController::class, 'ApiGetList']);
Route::middleware('auth:api')->get('/user/search', [UserController::class, 'ApiNameSearchUser']);

Route::middleware('auth:api')->post('/app/create', [AppController::class, 'ApiCreateApp']);
Route::middleware('auth:api')->get('/app/list', [AppController::class, 'ApiGetListByUser']);
Route::middleware('auth:api')->get('/app/users', [AppController::class, 'ApiGetSubscribeUserList']);
Route::middleware('auth:api')->post('/app/user/add', [AppController::class, 'ApiAddSubscribeUser']);
Route::middleware('auth:api')->post('/app/my/add', [AppController::class, 'ApiAddSubscribeMy']);
Route::middleware('auth:api')->post('/app/user/delete', [AppController::class, 'ApiDeleteSubscribeUser']);
Route::middleware('auth:api')->post('/app/my/delete', [AppController::class, 'ApiDeleteSubscribeMy']);

Route::prefix('use')->group(function () {

    Route::middleware('auth:api')->get('/app/{id}', [UseAppController::class, 'ApiAppData']);
    Route::middleware('auth:api')->post('/app/{id}/unsubscribe', [UseAppController::class, 'ApiUnsubscribeByMe']);
    Route::middleware('auth:api')->post('/app/{id}/subscribe', [UseAppController::class, 'ApiSubscribeByMe']);

    Route::post('/message/text/{id}', [UseAppController::class, 'ApiMessagePushText']);
});
