<?php

namespace Breeze\Wecom;

use Illuminate\Support\Facades\Config;
use Overtrue\Wechat\Input;
use Overtrue\Wechat\Utils\Bag;

/**
 * OAuth 网页授权获取用户信息
 */
class Auth
{
    const API_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize';
    const API_TOKEN_GET = 'https://api.weixin.qq.com/sns/oauth2/component/access_token'; //请求CODE
    const API_TOKEN_REFRESH = 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token'; //通过code换取access_token
    const API_USER = 'https://api.weixin.qq.com/sns/userinfo'; //刷新access_token
    protected $component_id;

    public function __construct($appId)
    {
        $this->appId = $appId;
        //$this->input = new Input();
        $this->http = new ComponentHttp(new ComponentAccessToken());
        $this->component_appid = Config::get('wecom.componentAppId');
    }

    /**
     * 生成oAuth URL
     *
     * @param string $to
     * @param string $scope
     * @param string $state
     * @return string
     */
    public function url($to = null, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        $to !== null || $to = Url::current();

        $params = array(
            'appid'           => $this->appId,
            'redirect_uri'    => $to,
            'response_type'   => 'code',
            'scope'           => $scope,
            'state'           => $state,
            'component_appid' => $this->component_appid,
        );

        return self::API_URL . '?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 获取用户信息
     * @param $access_token
     * @param $openid
     */
    public function getUser($access_token, $openid, $lang = 'zh_CN')
    {
        $params = [
            'access_token'      => $access_token,
            'openid'            => $openid,
            'lang'              => $lang,
        ];

        return $this->http->get(self::API_USER, $params);
    }

    public function getAccessPermission($code)
    {
        $params = array(
            'appid'           => $this->appId,
            'code'            => $code,
            'grant_type'      => 'authorization_code',
            'component_appid' => $this->component_appid,
        );

        return $this->lastPermission = $this->http->get(self::API_TOKEN_GET, $params);
    }


}
