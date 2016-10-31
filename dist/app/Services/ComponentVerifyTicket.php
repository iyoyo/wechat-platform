<?php

namespace Breeze\Wecom;

use Illuminate\Support\Facades\Cache;

/**
 * 推送component_verify_ticket协议
 *
 * @package Breeze\Wecom
 */
class ComponentVerifyTicket
{
    protected static $cacheKey = 'breeze.wechat.component_verify_ticket';

    public static function setTicket($componentVerifyTicket)
    {
        Cache::forever(self::$cacheKey, $componentVerifyTicket);
    }

    public static function getTicket()
    {
        return Cache::get(self::$cacheKey);
    }
}