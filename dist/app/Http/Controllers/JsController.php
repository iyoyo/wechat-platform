<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Http\Controllers;

use EasyWeChat\Js\Js;
use iBrand\WechatPlatform\Services\PlatformService;

/**
 * JSSDK.
 */
class JsController extends Controller
{
    public function ticket(Js $js, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        // 授权
        $platform->authorizeAPI($js, $appid);

        //调用接口
        $result = $js->ticket();

        //返回json
        return json_encode($result);
    }

    public function config(Js $js, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        // 授权
        $platform->authorizeAPI($js, $appid);

        //调用接口

        $js->setUrl(request('url'));

        if ($method = request('method') and is_array($method)) {
            return json_encode($js->config($method));
        }

        return json_encode($js->config([]));
    }
}
