<?php

namespace Wechat\Http\Controllers;

use EasyWeChat\Broadcast\Broadcast;
use Wechat\Services\PlatformService;


/**
 * 群发消息
 * @package Wechat\Http\Controllers
 */
class BroadcastController extends Controller
{
    /**
     * 预览消息
     * @param Broadcast $broadcast
     * @param PlatformService $platform
     * @return string
     */
    public function preview(Broadcast $broadcast, PlatformService $platform){
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($broadcast, $appid);

        //调用接口
        $result = $broadcast->preview($data['type'], $data['media_id'], $data['open_id']);

        // 返回JSON
        return json_encode($result);
    }

    /**
     * 群发消息
     * @param Broadcast $broadcast
     * @param PlatformService $platform
     * @return string
     */
    public function send(Broadcast $broadcast, PlatformService $platform){
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($broadcast, $appid);

        //调用接口
        $result = $broadcast->send($data['type'], $data['media_id'], $data['open_id']);

        // 返回JSON
        return json_encode($result);
    }
}