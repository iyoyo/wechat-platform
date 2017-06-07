<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Modules\Component;

use EasyWeChat\Core\AccessToken;
use Illuminate\Support\Facades\Cache;
use EasyWeChat\Core\Exceptions\HttpException;

/**
 * 全局通用 authorizer_access_token.
 */
class AuthorizerToken extends AccessToken
{
    const API_AUTHORIZER_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token=%s';

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'wechat.authorizer_access_token.';

    /**
     * 授权方的刷新令牌.
     *
     * @var string
     */
    protected $refresh_token;

    /**
     * 第三方平台access_token.
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
     * @param \Doctrine\Common\Cache\Cache|Cache $cache
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
     * @return string
     * @throws HttpException
     * @throws \EasyWeChat\Core\Exceptions\HttpException
     */
    public function getTokenFromServer()
    {
        $params = [
            'component_appid'          => $this->component_token->getAppId(),
            'authorizer_appid'         => $this->appId,
            'authorizer_refresh_token' => $this->refresh_token,
        ];

        $http = $this->getHttp();
        $url = sprintf(self::API_AUTHORIZER_TOKEN, $this->component_token->getToken());
        $token = $http->parseJSON($http->json($url, $params));

        if (empty($token['authorizer_access_token'])) {
            throw new HttpException('Request AccessToken fail. response: '.json_encode($token, JSON_UNESCAPED_UNICODE));
        }

        return $token;
    }

    /**
     * Get token from WeChat API.
     *
     * @param bool $forceRefresh
     *
     * @return string
     */
    public function getToken($forceRefresh = false)
    {
        $cacheKey = $this->getCacheKey();
        $cached = $this->getCache()->fetch($cacheKey);

        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();

            // XXX: T_T... 7200 - 1500
            $this->getCache()->save($cacheKey, $token['authorizer_access_token'], $token['expires_in'] - 1500);

            return $token['authorizer_access_token'];
        }

        return $cached;
    }
}
