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

        parent::__construct($appId, NULL, $cache);
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
        $cacheKey = $this->prefix.$this->appId.$openid;
        $cached = $this->getCache()->fetch($cacheKey);
        echo 'cachekey:'.$cacheKey.'</br>';
        echo 'cached:'.$cached.'</br>';
        if ($forceRefresh || empty($cached)) {
            $token = $this->getTokenFromServer();
            echo 'token:'.$token.'</br>';
            // XXX: T_T... 7200 - 1500
            $this->getCache()->save($cacheKey, $token['access_token'], $token['expires_in'] - 1500);

            return $token['access_token'];
        }
        return $cached;
    }

}