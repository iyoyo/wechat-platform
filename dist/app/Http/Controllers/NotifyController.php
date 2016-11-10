<?php

namespace Wechat\Http\Controllers;

use Wechat\Services\MessageService;
use Wechat\Services\PlatformService;
use Illuminate\Http\Request;

class NotifyController extends Controller
{
    /**
     * 授权事件接收URL
     *
     * @param PlatformService $component
     * @return string
     */
    public function notifyPlatform(PlatformService $component)
    {
        return $component->authEventProcess();
    }

    /**
     * 公众号消息与事件接收URL
     * @param PlatformService $component
     */
    public function notifyAccount($appid, MessageService $message)
    {
        return $message->cardEventProcess($appid);
    }
}
