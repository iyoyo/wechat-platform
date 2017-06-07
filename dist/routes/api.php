<?php

/*
 * add .styleci.yml
 */

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

Route::group(['middleware' => ['auth_client']], function () {
    Route::get('authorizers', 'AuthorizerController@index');
});

Route::group(['middleware' => ['auth_client', 'parameter']], function () {

//    Route::get('authorizers', 'AuthorizerController@index');

    // 获取OAuth用户信息
    Route::get('/oauth/user', 'OAuthController@userinfo');
    //Route::get('/user/info', 'OAuthController@userinfo');

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
    Route::post('/card/upload/image', 'MediaController@uploadArticleImage');

    //上传图片素材
    Route::post('/media/upload/image', 'MediaController@uploadImage');

    //上传永久图文消息
    Route::post('/media/upload/article', 'MediaController@uploadArticle');

    //预览消息
    Route::post('/broadcast/preview', 'BroadcastController@preview');

    //预览图文消息
    Route::post('/broadcast/send', 'BroadcastController@send');

    //获取jsapi_ticket
    Route::get('/js/ticket', 'JsController@ticket');

    Route::get('/js/config', 'JsController@config');

    Route::group(['prefix' => 'menu'], function ($router) {
        Route::post('/store', 'MenuController@store')->name('admin.wechat.menu.store');
    });

    Route::group(['prefix' => 'medias'], function ($router) {
        //上传图片
        Route::post('/remote/image', 'MediasController@RemoteImage');
        //上传图文素材内容图片
        Route::post('/remote/article/image', 'MediasController@RemoteArticleImage');

        //删除素材
        Route::post('/delete', 'MediasController@delete');
        //上传视频
        Route::post('/remote/video', 'MediasController@RemoteVideo');
        //上传图文
        Route::post('/remote/article', 'MediasController@RemoteArticle');
        // 修改图文
        Route::post('/update/article', 'MediasController@updateArticle');

        // 获取素材
        Route::post('/get', 'MediasController@get');

        // 获取素材列表
        Route::post('/lists', 'MediasController@getLists');
        // 获取素材数量统计
        Route::get('/stats', 'MediasController@stats');
    });

    // 粉丝管理
    Route::group(['prefix' => 'fans'], function ($router) {
        Route::get('/lists', 'FansController@lists');
        Route::post('/get', 'FansController@get');

        Route::get('/group/lists', 'FansGroupController@lists');
        Route::post('/group/create', 'FansGroupController@create');
        Route::post('/group/update', 'FansGroupController@update');
        Route::post('/group/delete', 'FansGroupController@delete');

        Route::post('/group/moveUsers', 'FansGroupController@moveUsers');
    });

    // 模板消息
    Route::group(['prefix' => 'notice'], function ($router) {
        Route::get('/get', 'NoticeController@getAll');
        Route::post('/sendall', 'NoticeController@sendAll');
    });

    //二维码
    Route::group(['prefix' => 'qrcode'], function ($router) {
        // 创建临时
        Route::post('/temporary', 'QRCodeController@storeTemporary');
        // 创建永久
        Route::post('/forever', 'QRCodeController@storeForever');
        // 获取二维码网址
        Route::post('/url', 'QRCodeController@getUrl');
    });

    //会员卡
    Route::group(['prefix' => 'card'], function ($router) {
        Route::get('/colors', 'CardController@getColors');
        //获取会员卡券信息
        Route::post('/getinfo', 'CardController@getCard');
        //白名单
        Route::post('/setTestWhitelist', 'CardController@setTestWhitelist');
        //二维码
        Route::post('/QRCode', 'CardController@QRCode');
        // 更新会员卡券信息
        Route::post('/update/member_card', 'CardController@updateCard');
        // 获取会员信息
        Route::post('/membership/get', 'CardController@membershipGet');
        //更改会员卡库存
        Route::post('/update/quantityt', 'CardController@updateQuantity');
        //设置会员卡券失效
        Route::post('/disable', 'CardController@disable');
    });
});
