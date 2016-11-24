<?php

namespace Wechat\Modules\Card;

use Wechat\Modules\OAuth\AccessToken;


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