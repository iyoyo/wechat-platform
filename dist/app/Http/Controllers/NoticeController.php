<?php

namespace Wechat\Http\Controllers;

use EasyWeChat\Notice\Notice;
use Illuminate\Http\Request;
use Wechat\Services\PlatformService;

/**
 * 模板消息
 * @package Wechat\Http\Controllers
 */
class NoticeController extends Controller
{
    /**
     * 发送模板消息
     *
     * @param Notice $notice
     * @param PlatformService $platform
     * @return string
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function send(Notice $notice, PlatformService $platform) {
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($notice, $appid);

        // 调用接口
        $result = $notice->send($data);

        // 返回JSON
        return json_encode($result);
    }
}
