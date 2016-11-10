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
Route::get('/oauth', 'OAuthController@oauth');
// OAuth授权结果返回
Route::get('/oauth/result/{appid}', 'OAuthController@result')->name('oauth_result');
// 获取OAuth用户信息
Route::get('/oauth/user', 'OAuthController@userinfo');
Route::get('/user/info', 'OAuthController@userinfo');

// 发送模板消息
Route::post('/notice/send', 'NoticeController@send');

//创建会员卡
Route::post('/card/create', 'CardController@create');

//创建货架
Route::post('/card/landingpage/create', 'CardController@createLandingPage');

//会员卡激活
Route::post('/card/activate', 'CardController@activate');

//更新会员信息
Route::post('/card/updateuser','CardController@updateUser');

//删除卡券
Route::post('/card/delete','CardController@delete');

//获取会员卡code
Route::post('/card/getcode','CardController@getCode');