<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Http\Controllers;

use Exception;
use EasyWeChat\Card\Card;
use iBrand\WechatPlatform\Services\MessageService;
use iBrand\WechatPlatform\Services\PlatformService;

/**
 * 优惠券
 */
class CouponController extends Controller
{
    /**
     * 创建卡券
     * @param Card $card
     * @param PlatformService $platform
     */
    public function create(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        //调用接口
        $result = $card->create($data['type'], $data['base_info'], $especial = $data['especial']);

        //返回json
        return json_encode($result);
    }

    /**
     * 创建货架.
     * @param Card $card
     * @param PlatformService $platform
     */
    public function createLandingPage(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        //调用接口
        $result = $card->createLandingPage($data['banner'], $data['page_title'], $data['can_share'], $data['scene'], $data['card_list']);

        //返回json
        return json_encode($result);
    }

    public function getColors(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');
        // 授权
        $platform->authorizeAPI($card, $appid);

        $data = request()->json()->all();

        //调用接口
        $result = $card->getColors();

        return json_encode($result);
    }

    //  设置测试白名单
    public function setTestWhitelist(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        // 授权
        $platform->authorizeAPI($card, $appid);

        $data = request()->json()->all();

        //调用接口
        $result = $card->setTestWhitelist($data['openids']);

        return json_encode($result);
    }

    // 创建二维码
    public function QRCode(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        // 授权
        $platform->authorizeAPI($card, $appid);

        $data = request()->json()->all();

        //调用接口
        $result = $card->QRCode($data['cards']);

        return json_encode($result);
    }


    //  查看卡券详情
    public function getInfo(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        // 授权
        $platform->authorizeAPI($card, $appid);

        $data = request()->json()->all();

        //调用接口
        $result = $card->getCard($data['card_id']);

        return json_encode($result);
    }

    //更改卡券信息
    public function update(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');
        // 授权
        $platform->authorizeAPI($card, $appid);

        $data = request()->json()->all();

        //调用接口
        $result = $card->update($data['card_id'],strtolower($data['type']), $data['base_info'], $data['especial']);

        return json_encode($result);
    }


    //更改卡券库存接口
    public function updateQuantity(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        // 调用接口
        if ($data['amount'] >= 0) {
            // 增加库存
            $result = $card->increaseStock($data['card_id'], $data['amount']);
        }

        if ($data['amount'] < 0) {
            // // 减少库存
            $result = $card->reduceStock($data['card_id'], $data['amount']);
        }

        // 返回json
        return json_encode($result);
    }


    //卡券失效
    public function disable(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        // 调用接口
        $result = $card->disable($data['code'], $data['card_id']);

        // 返回json
        return json_encode($result);
    }

    /**
     * 删除卡券
     * @param Card $card
     * @param PlatformService $platform
     */
    public function delete(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        // 调用接口
        $result = $card->delete($data['card_id']);

        // 返回json
        return json_encode($result);
    }



    /**
 * 查询code
 * @param Card $card
 * @param PlatformService $platform
 */
    public function getCode(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        // 调用接口
        $result = $card->getCode($data['code'], false, $data['card_id']);

        // 返回json
        return json_encode($result);
    }


    /**
     * 核销Code
     * @param Card $card
     * @param PlatformService $platform
     */
    public function consumeCode(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        // 调用接口
        $result = $card->consume($data['code'], false, $data['card_id']);

        // 返回json
        return json_encode($result);
    }


}
