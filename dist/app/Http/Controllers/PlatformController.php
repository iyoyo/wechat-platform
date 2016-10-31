<?php

namespace Wechat\Http\Controllers;

use Wechat\Authorizer;
use Wechat\Oauth2Token;
use Breeze\Wecom\Application;
use Breeze\Wecom\Auth;
use Breeze\Wecom\ComponentService;
use Illuminate\Http\Request;

use Wechat\Http\Requests;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery\CountValidator\Exception;

class PlatformController extends Controller
{
    protected $options = [];

    public function __construct()
    {
        $this->options = [
            'debug'  => true,

            'app_id'    => Config::get('wecom.componentAppId'),
            'secret'    => Config::get('wecom.componentAppSecret'),
            'token'     => Config::get('wecom.encodingToken'),
            'aes_key'   => Config::get('wecom.encodingAESKey'),

            'log' => [
                'level' => 'debug',
                'file'  => '/tmp/easywechat.log',
            ],

            'component' => [
                'callback' => '/wecom/auth/success',
            ],
        ];
    }

    /**
     * 引入用户进入授权页
     * @return mixed
     */
    public function auth() 
    {
        $app = new Application($this->options);
        $component = $app->component;
        return $component->redirect();
    }

    /**
     * 授权后回调URI，得到授权码（authorization_code）和过期时间
     * @param Request $request
     * @return string
     */
    public function authSuccess(Request $request)
    {
        $app = new Application($this->options);
        $component = $app->component;

        $result = $component->queryAuth($request->get('auth_code'));
        $info = $result['authorization_info'];

        // 保存
        $authorizer = Authorizer::firstOrNew(['appid' => $info['authorizer_appid']]);
        $authorizer->access_token = $info['authorizer_access_token'];
        $authorizer->refresh_token = $info['authorizer_refresh_token'];
        $authorizer->func_info = \GuzzleHttp\json_encode($info['func_info']);
        $authorizer->save();

        return '授权成功！';
    }

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
    public function oauthCallback($appid, Request $request)
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
}
