<?php

namespace Wechat\Http\Controllers;

use EasyWeChat\Js\Js;
use Wechat\Services\PlatformService;

/**
 * JSSDK
 * @package Wechat\Http\Controllers
 */

class JsController extends Controller
{
    public function ticket(Js $js, PlatformService $platform){
        // 参数
        $appid = request('appid');

        // 授权
        $platform->authorizeAPI($js, $appid);

        //调用接口
        $result = $js->ticket();

        //返回json
        return json_encode($result);
    }
}