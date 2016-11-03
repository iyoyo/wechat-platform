<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 16/11/2
 * Time: 16:29
 */

namespace Wechat\Modules\OAuth;

use EasyWeChat\Core\Exceptions\HttpException;


class AccessToken extends \EasyWeChat\Core\AccessToken
{
    const API_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token';

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'wechat.access_token.';

    /**
     * 授权方的APPID
     *
     * @var string
     */
    protected $accesstoken_appid;

    /**
     * 授权方的刷新令牌
     *
     * @var string
     */
    protected $accesstoken_refresh_token;

    /**
     * component_access_token
     *
     * @var string
     */
    protected $component_token;

    /**
     * 设置授权方的信息
     *
     * @param $accesstoken_appid
     * @param $accesstoken_refresh_token
     */
    public function setAccessToken($accesstoken_appid, $accesstoken_refresh_token,$component_token)
    {
        $this->accesstoken_appid = $accesstoken_appid;
        $this->accesstoken_refresh_token = $accesstoken_refresh_token;
        $this->component_token = $component_token;
    }

    /**
     * Get the access token from WeChat server.
     *
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     *
     * @return string
     */
    public function getTokenFromServer()
    {
        $params = [
            'appid' => $this->accesstoken_appid,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->accesstoken_refresh_token,
            'component_appid' => $this->appId,
            'component_access_token' => $this->component_token,
        ];

        $http = $this->getHttp();

        $token = $http->parseJSON($http->get(self::API_ACCESS_TOKEN, $params));

        if (empty($token['access_token'])) {
            throw new HttpException('Request AccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }
}