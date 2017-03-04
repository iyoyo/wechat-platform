<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 引导用户进行公众号授权
Route::get('/platform/auth', 'PlatformController@auth');
// 授权成功提示页面
Route::get('/platform/auth/result', 'PlatformController@authResult')->name('component_auth_result');

// 引导用户进行OAuth授权
Route::get('/oauth', 'OAuthController@oauth')->middleware(['middleware' => 'parameter']);
// OAuth授权结果返回
Route::get('/oauth/result/{appid}', 'OAuthController@result')->name('oauth_result');

// 兼容旧版本URL
//Route::any('/wecom/callback', 'WecomController@callback');
//Route::any('/wecom/{appid}/callback', 'WecomController@appCallback');
//Route::get('/wecom/{appid}/oauth', 'WecomController@oauth');
//Route::get('/wecom/{appid}/oauth/callback', 'WecomController@oauthCallback');