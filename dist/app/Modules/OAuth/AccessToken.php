<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Modules\OAuth;

use Doctrine\Common\Cache\Cache;
use EasyWeChat\Core\Exceptions\HttpException;
use iBrand\WechatPlatform\Modules\Component\ComponentToken;

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
     * 授权方的刷新令牌.
     *
     * @var string
     */
    protected $refresh_token;

    /**
     * component_access_token.
     *
     * @var string
     */
    protected $component_token;

    /**
     * Constructor.
     *
     * @param string $appId
     * @param string $refresh_token
     * @param ComponentToken $component_token
     */
    public function __construct($appId, $refresh_token, ComponentToken $component_token)
    {
        $this->refresh_token = $refresh_token;
        $this->component_token = $component_token;
        $cache = $component_token->getCache();

        parent::__construct($appId, null, $cache);
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

            'component_appid' => $this->component_token->getAppId(),
            'component_access_token' => $this->component_token->getToken(),
        ];

        $http = $this->getHttp();
        $token = $http->parseJSON($http->get(self::API_ACCESS_TOKEN, $params));

        if (empty($token['access_token'])) {
            throw new HttpException('Request AccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }

    /**
     * Get token from WeChat API.
     *
     * @param $openid
     * @param bool $forceRefresh
     * @return string
     * @throws HttpException
     */
    public function getToken($openid = '', $forceRefresh = false)
    {
        $cacheKey = $this->getCacheKey($openid);
        $cached = $this->getCache()->fetch($cacheKey);

        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();
            // XXX: T_T... 7200 - 1500
            $this->getCache()->save($cacheKey, $token['access_token'], $token['expires_in'] - 1500);

            return $token['access_token'];
        }

        return $cached;
    }

    /**
     * Get access token cache key.
     *
     * @param $openid
     * @return string $this->cacheKey
     */
    public function getCacheKey($openid = '')
    {
        return $this->prefix.$this->appId.$openid;
    }

    /**
     * 设置自定义 token.
     *
     * @param string $openid
     * @param string $token
     * @param int $expires
     * @return $this
     */
    public function setToken($token, $expires = 7200, $openid = '')
    {
        $this->getCache()->save($this->getCacheKey($openid), $token, $expires - 1500);

        return $this;
    }
}
