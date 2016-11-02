<?php

namespace Wechat\Http\Controllers;

use Illuminate\Http\Request;

class OAuthController extends Controller
{
    /**
     * 第三方登录
     * @param $appid
     * @param Request $request
     * @return mixed
     */
    public function oauth($appid, Request $request)
    {
        $request->session()->set('redirect', $request->get('redirect'));

        $auth = new Auth($appid);
        $url = $auth->url(url('/wecom/'. $appid . '/oauth/callback'));
        return Redirect::to($url);
    }

    /**
     * 第三方登录回调
     * @param $appid
     * @param Request $request
     * @return mixed
     */
    public function result($appid, Request $request)
    {
        $auth = new Auth($appid);
        $result = $auth->getAccessPermission($request->get('code'));

        // 保存oauth_tokens
        $oauth_token = Oauth2Token::firstOrNew(['appid' => $appid, 'openid' => $result['openid']]);
        $oauth_token->access_token = $result['access_token'];
        $oauth_token->refresh_token = $result['refresh_token'];
        $oauth_token->scope = $result['scope'];
        $oauth_token->expires_in = $result['expires_in'];
        $oauth_token->save();

        $url = $request->session()->get('redirect') . '?openid=' . $result['openid'];
        return Redirect::to($url);
    }

    public function info2(Request $request) {
        $appid = $request->get('appid');
        $openid = $request->get('openid');

        $oauth_token = Oauth2Token::where('appid', $appid)
            ->where('openid', $openid)
            ->first();

        $auth = new Auth($appid);
        $user = $auth->getUser($oauth_token->access_token, $openid);
        return json_encode($user);
    }
}
