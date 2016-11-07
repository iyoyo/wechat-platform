<?php

namespace Wechat\Http\Controllers;

use EasyWeChat\Card\Card;
use Illuminate\Http\Request;
use Wechat\Services\PlatformService;

/**
 * 会员卡
 * @package Wechat\Http\Controllers
 */

class CardController extends Controller
{
    /**
     * 激活会员卡
     * @param Card $card
     * @param PlatformService $platform
     */
    public function activate(Card $card, PlatformService $platform){
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);
        //调用接口
        $result = $card->activate($data);

        //返回json
        return json_encode($result);
    }
}