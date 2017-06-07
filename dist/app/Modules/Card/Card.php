<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Modules\Card;

use iBrand\WechatPlatform\Modules\OAuth\AccessToken;

class Card extends \EasyWeChat\Card\Card
{
    /**
     * Constructor.
     *
     * @param \Wechat\Modules\OAuth\AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->setAccessToken($accessToken);
    }
}
