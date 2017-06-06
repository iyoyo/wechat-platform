<?php

namespace iBrand\WechatPlatform\Http\Controllers;

use EasyWeChat\Menu\Menu;
use iBrand\WechatPlatform\Services\PlatformService;
use EasyWeChat\Message\Text;

/**
 * 消息回复
 * @package Wechat\Http\Controllers
 */
class MessageController extends Controller
{

    protected $text;
    protected $platform;

    public function __construct(
        Text $text
        ,PlatformService $platformService
    )
    {
        $this->text = $text;
        $this->platform = $platformService;
    }


    public function MessageText(){
//        // 参数
//        $appid = request('appid');
//
//        // 授权
//        $this->platform->authorizeAPI($this->text, $appid);
//
//        // 调用接口
//        $result = $this->text->content(['']);
//
//        // 返回JSON
//        return $result;
    }










}
