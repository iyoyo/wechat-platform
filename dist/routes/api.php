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


Route::group(['middleware' => 'auth_client'], function () {
// 获取OAuth用户信息
    Route::get('/oauth/user', 'OAuthController@userinfo');
    Route::get('/user/info', 'OAuthController@userinfo');

// 发送模板消息
    Route::post('/notice/send', 'NoticeController@send');

//创建会员卡
    Route::post('/card/create', 'CardController@create');

//创建货架
    Route::post('/card/landing-page/create', 'CardController@createLandingPage');

//会员卡激活
    Route::post('/card/membership/activate', 'CardController@membershipActivate');

//更新会员信息
    Route::post('/card/membership/update', 'CardController@membershipUpdate');

//删除卡券
    Route::post('/card/delete', 'CardController@delete');

//获取会员卡code
    Route::post('/card/getcode', 'CardController@getCode');

//上传会员卡背景图
    Route::post('/card/upload/image', 'CardController@uploadImage');
});