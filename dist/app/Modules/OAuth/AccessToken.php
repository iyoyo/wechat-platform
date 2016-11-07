<?php

namespace Wechat\Modules\OAuth;

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Core\Exceptions\HttpException;
use Wechat\Modules\Component\ComponentToken;


class AccessToken extends \EasyWeChat\Core\AccessToken
{
    const API_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token';

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'wechat.oauth_access_token.';

    /**
     * 授权方的刷新令牌
     *
     * @var string
     */
    protected $refresh_token;

    /**
     * component_access_token
     *
     * @var string
     */
    protected $component_access_token;

    /**
     * Constructor.
     *
     * @param string                       $appId
     * @param string                       $secret
     * @param \Doctrine\Common\Cache\Cache $cache
     */
    public function __construct($appId, $refresh_token, Cache $cache = null, ComponentToken $component_access_token)
    {
        $this->appId = $appId;
        $this->refresh_token = $refresh_token;
        $this->cache = $cache;
        $this->component_access_token = $component_access_token;
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
            'appid' => $this->appId,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refresh_token,
            'component_appid' => $this->component_access_token->getAppId(),
            'component_access_token' => $this->component_access_token->getToken(),
        ];

        $http = $this->getHttp();

        $token = $http->parseJSON($http->get(self::API_ACCESS_TOKEN, $params));

        if (empty($token['access_token'])) {
            throw new HttpException('Request AccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }
}