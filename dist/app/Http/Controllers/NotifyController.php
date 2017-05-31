<?php

namespace Wechat\Http\Controllers;

use Wechat\Services\MessageService;
use Wechat\Services\PlatformService;
use EasyWeChat\Staff\Staff;
use EasyWeChat\Message\Text;

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
     * @param $appid
     * @param MessageService $message
     * @param PlatformService $platform
     * @param Staff $staff
     * @return bool|string
     * @internal param PlatformService $component
     */
    public function notifyAccount($appid, MessageService $message, PlatformService $platform, Staff $staff)
    {
        $result = $message->accountEventProcess($appid);

        //全网发布测试: 调用接口
        if (strpos($result, "QUERY_AUTH_CODE:") !== false){
            $message = new Text(['content' => str_replace("QUERY_AUTH_CODE:", "", $result).'_from_api']);
            $platform->authorizeAPI($staff, 'wx570bc396a51b8ff8');
            $result = $staff->message($message)->to("ozy4qt0Rsc9YJzR5nEeVAaTHg9DQ")->send();

        }

        return $result;
    }
}
