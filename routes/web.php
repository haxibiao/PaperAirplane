<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SubscribeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});

Route::post('login', ['as' => 'login', 'uses' => 'LoginController@index']);
Route::get('/logout', [LoginController::class, 'logout']);

Route::prefix('login')->group(function () {
    Route::get('/', [LoginController::class, 'index']);
    Route::get('/feishu', [LoginController::class, 'feishu']); // 飞书登陆授权回调
    Route::get('/tofeishu', [LoginController::class, 'toFeishu']); // 跳转飞书授权页面
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
});

Route::prefix('subscribe')->group(function () {
    Route::get('/{id}', [SubscribeController::class, 'index']);
});
