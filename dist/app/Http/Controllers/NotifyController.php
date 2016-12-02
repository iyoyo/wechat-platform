<?php

namespace Wechat\Http\Controllers;

use Wechat\Services\MessageService;
use Wechat\Services\PlatformService;

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
        return $message->accountEventProcess($appid);
    }
}
