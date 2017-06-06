<?php

namespace iBrand\WechatPlatform\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use iBrand\WechatPlatform\Services\OAuthService;

class OAuthController extends Controller
{
    const OAUTH_REDIRECT = 'oauth.redirect';

    /**
     * 第三方登录
     *
     * @param OAuthService $oauth
     * @return mixed
     */
    public function oauth(OAuthService $oauth)
    {
        $appid = request('appid');
        $scope = !empty(request('scope')) ? request('scope') : 'snsapi_userinfo';
        $callback = route('oauth_result', ['appid' => $appid]);

        // 记录回调地址
        session([self::OAUTH_REDIRECT => request('redirect')]);

        $url = $oauth->authRedirectUrl($appid, $callback, $scope);
        return Redirect::to($url);
    }

    /**
     * 第三方登录回调
     *
     * @return mixed
     */
    public function result($appid, OAuthService $oauth)
    {
        $token = $oauth->saveAuthorization($appid, request('code'));

        // 回调返回openid
        $url = session(self::OAUTH_REDIRECT) . '?openid=' . $token->openid;

        return Redirect::to($url);
    }

    /**
     * 获取用户信息
     *
     * @param OAuthService $oauth
     * @return string
     */
    public function userinfo(OAuthService $oauth) {
        $appid = request('appid');
        $openid = request('openid');

        $user = $oauth->getUserInfo($appid, $openid);

        return json_encode($user);
    }
}
