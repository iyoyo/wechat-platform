<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Http\Controllers;

use EasyWeChat\Broadcast\Broadcast;
use iBrand\WechatPlatform\Services\PlatformService;

/**
 * 群发消息.
 */
class BroadcastController extends Controller
{
    /**
     * 预览消息.
     * @param Broadcast $broadcast
     * @param PlatformService $platform
     * @return string
     */
    public function preview(Broadcast $broadcast, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($broadcast, $appid);

        //调用接口
        switch ($data['type']) {
            case 'news': case 'image': case 'video': case 'voice':
                $result = $broadcast->preview($data['type'], $data['media_id'], $data['open_id']);
                break;
            case 'text':
                $result = $broadcast->preview($data['type'], $data['text'], $data['open_id']);
                break;
            case 'card_id':
                $result = $broadcast->preview($data['type'], $data['card_id'], $data['open_id']);
                break;
        }

        // 返回JSON
        return json_encode($result);
    }

    /**
     * 群发消息.
     * @param Broadcast $broadcast
     * @param PlatformService $platform
     * @return string
     */
    public function send(Broadcast $broadcast, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($broadcast, $appid);

        //调用接口
        switch ($data['type']) {
            case 'news': case 'image': case 'video': case 'voice':
                $result = $broadcast->send($data['type'], $data['media_id'], $data['open_id']);
                break;
            case 'text':
                $result = $broadcast->send($data['type'], $data['text'], $data['open_id']);
                break;
            case 'card_id':
                $result = $broadcast->send($data['type'], $data['card_id'], $data['open_id']);
                break;
        }

        // 返回JSON
        return json_encode($result);
    }
}
