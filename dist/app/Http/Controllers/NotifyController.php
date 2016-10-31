<?php

namespace Wechat\Http\Controllers;

use Illuminate\Http\Request;
use Wechat\Services\ComponentService;

class NotifyController extends Controller
{
    /**
     * 授权事件接收URL
     * @param ComponentService $wechat
     * @return string
     */
    public function notifyPlatform(ComponentService $wechat)
    {
        return $wechat->options;
        // return $wechat->serve();
    }

    /**
     * 公众号消息与事件接收URL
     * @param $appid
     * @param Request $request
     */
    public function notifyAccount($appid, Request $request)
    {
    }
}
