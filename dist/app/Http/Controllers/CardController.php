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
 * 会员卡
 */
class CardController extends Controller
{
    /**
     * 创建会员卡
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
        $result = $card->create('member_card', $data['card']['member_card']['base_info'], $especial = $data['card']['member_card']['especial']);

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

    /**
     * 激活会员卡
     * @param Card $card
     * @param PlatformService $platform
     */
    public function membershipActivate(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        // 调用接口
        $result = $card->activate($data);

        // 返回json
        return json_encode($result);
    }

    /**
     * 更新会员信息.
     * @param Card $card
     * @param PlatformService $platform
     */
    public function membershipUpdate(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        // 调用接口
        $result = $card->updateMemberCardUser($data);

        // 返回json
        return json_encode($result);
    }

    /**
     * 删除会员卡
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
     * 获取会员卡code.
     * @param MessageService $message
     * @return string
     */
    public function getCode(MessageService $message)
    {
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        //调用接口
        $result = $message->getCode($appid, $data['card_id'], $data['openid']);

        if (empty($result)) {
            throw new Exception('cannot get code.', 1);
        }

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

//  查看会员卡详情
    public function getCard(Card $card, PlatformService $platform)
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

//    更改会员卡券信息
    public function updateCard(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        // 授权
        $platform->authorizeAPI($card, $appid);

        $data = request()->json()->all();

        //调用接口
        $result = $card->update($data['card_id'], 'member_card', $data['base_info'], $data['especial']);

        return json_encode($result);
    }

   //拉取会员信息接口
    public function membershipGet(Card $card, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        // 调用接口
        $result = $card->getMemberCardUser($data['card_id'], $data['code']);

        // 返回json
        return json_encode($result);
    }

    //更改库存接口
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

  //会员卡失效
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
}
