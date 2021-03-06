<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Repositories;

use iBrand\WechatPlatform\Models\Oauth2Token;

/**
 * OAuthToken 仓库.
 */
class OAuthTokenRepository
{
    /**
     * 获取授权TOKEN.
     *
     * @param $appid
     * @param $openid
     */
    public function getToken($appid, $openid)
    {
        $oauth_token = Oauth2Token::where('appid', $appid)
            ->where('openid', $openid)
            ->first();

        return $oauth_token;
    }

    /**
     * 获取APP授权, 不存在则创建一个.
     *
     * @param $appid
     */
    public function ensureToken($appid, $openid)
    {
        return Oauth2Token::firstOrNew(['appid' => $appid, 'openid' => $openid]);
    }
}
