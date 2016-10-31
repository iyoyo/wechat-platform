<?php

use Illuminate\Http\Request;

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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// 平台授权事件接收URL
Route::any('/notify', 'NotifyController@notifyPlatform');
// 公众号消息与事件接收URL
Route::any('/notify/{appid}', 'NotifyController@notifyAccount');

// 引导用户进行OAuth授权
//Route::get('/oauth', 'WecomController@oauth');
// Oauth授权结果返回
//Route::get('/oauth/result', 'WecomController@oauthCallback');
// 获取用户信息
//Route::get('/user/info', 'UserController@info2');

// 发送模板消息
//Route::post('/notice/send', 'NoticeController@send');