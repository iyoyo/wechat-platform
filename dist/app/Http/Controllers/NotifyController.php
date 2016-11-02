<?php

namespace Wechat\Http\Controllers;

use EasyWeChat\Foundation\Application;
use Wechat\Modules\Component\Guard;
use Wechat\Modules\Component\VerifyTicket;
use Wechat\Services\ComponentService;

class NotifyController extends Controller
{
    /**
     * 授权事件接收URL
     *
     * @param ComponentService $component
     * @return string
     * @internal param Application $wechat
     */
    public function notifyPlatform(ComponentService $component)
    {
        return $component->authEventProcess();
    }

    /**
     * 公众号消息与事件接收URL
     * @param ComponentService $component
     */
    public function notifyAccount(ComponentService $component)
    {
        //
    }
}
