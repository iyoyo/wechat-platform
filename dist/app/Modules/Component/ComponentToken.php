<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Modules\Component;

use EasyWeChat\Core\AccessToken;
use EasyWeChat\Core\Exceptions\HttpException;

/**
 * 全局通用 component_access_token.
 *
 * 2、获取第三方平台component_access_token
 * 第三方平台通过自己的component_appid（即在微信开放平台管理中心的第三方平台详情页中的AppID和AppSecret）和component_appsecret，以及
 * component_verify_ticket（每10分钟推送一次的安全ticket）来获取自己的接口调用凭据（component_access_token）
 */
class ComponentToken extends AccessToken
{
    const API_COMPONENT_TOKEN = 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';

    /**
     * Cache key prefix.
     *
     * @var string
     */
    protected $prefix = 'wechat.component_access_token.';

    /**
     * Query name.
     *
     * @var string
     */
    protected $queryName = 'component_access_token';

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
            'component_appid' => $this->appId,
            'component_appsecret' => $this->secret,
            /*
             * component_verify_ticket 是服务端每隔10分钟主动推送过来, 保存在缓存中
             */
            'component_verify_ticket' => VerifyTicket::getTicket(),
        ];

        $http = $this->getHttp();
        $token = $http->parseJSON($http->json(self::API_COMPONENT_TOKEN, $params));

        if (empty($token['component_access_token'])) {
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
            $this->getCache()->save($cacheKey, $token['component_access_token'], $token['expires_in'] - 1500);

            return $token['component_access_token'];
        }

        return $cached;
    }
}
