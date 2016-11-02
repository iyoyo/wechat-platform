<?php

namespace Wechat\Modules\Component;

use Carbon\Carbon;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Exceptions\HttpException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

/**
 * 全局通用 authorizer_access_token
 */
class AuthorizerToken extends AccessToken
{
    const API_AUTHORIZER_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token';

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'wechat.authorizer_access_token.';

    /**
     * 授权方的APPID
     *
     * @var string
     */
    protected $authorizer_appid;

    /**
     * 授权方的刷新令牌
     *
     * @var string
     */
    protected $authorizer_refresh_token;

    /**
     * 设置授权方的信息
     *
     * @param $authorizer_appid
     * @param $authorizer_refresh_token
     */
    public function setAuthorizer($authorizer_appid, $authorizer_refresh_token)
    {
        $this->authorizer_appid = $authorizer_appid;
        $this->authorizer_refresh_token = $authorizer_refresh_token;
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
            'component_appid'          => $this->appId,
            'authorizer_appid'         => $this->authorizer_appid,
            'authorizer_refresh_token' => $this->authorizer_refresh_token,
        ];

        $http = $this->getHttp();
        $token = $http->parseJSON($http->json(self::API_AUTHORIZER_TOKEN, $params));

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
