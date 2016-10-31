<?php

namespace Wechat\Http\Controllers;

use Wechat\Authorizer;
use Wechat\Oauth2Token;
use Breeze\Wecom\AccessToken;
use Breeze\Wecom\Auth;
use EasyWeChat\User\User;
use Illuminate\Http\Request;

use Wechat\Http\Requests;
use Wechat\Http\Controllers\Controller;

class UserController extends Controller
{
    public function info(Request $request) {
        $appid = $request->get('appid');
        $openid = $request->get('openid');

        $authorizer = Authorizer::where('appid', $appid)->first();
        $access_token = new AccessToken($appid, $authorizer->refresh_token);
        
        $userService = new User($access_token);
        $user = $userService->get($openid);
        return json_encode($user);
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
