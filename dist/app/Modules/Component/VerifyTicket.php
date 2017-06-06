<?php

namespace iBrand\WechatPlatform\Modules\Component;

use Illuminate\Support\Facades\Cache;

/**
 * 推送component_verify_ticket协议
 *
 * @package Breeze\Wecom
 */
class VerifyTicket
{
    protected static $cacheKey = 'wechat.component_verify_ticket';

    public static function setTicket($componentVerifyTicket)
    {
        Cache::forever(self::$cacheKey, $componentVerifyTicket);
    }

    public static function getTicket()
    {
        return Cache::get(self::$cacheKey);
    }
}