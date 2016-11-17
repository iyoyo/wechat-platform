<?php

namespace Wechat\Http\Controllers;

use EasyWeChat\Card\Card;
use Illuminate\Http\Request;
use Wechat\Services\PlatformService;
use Wechat\Services\MessageService;
use EasyWeChat\Material\Material;

/**
 * 会员卡
 * @package Wechat\Http\Controllers
 */

class CardController extends Controller
{
    /**
     * 创建会员卡
     * @param Card $card
     * @param PlatformService $platform
     */
    public function create(Card $card, PlatformService $platform){
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($card, $appid);

        //调用接口
        $result = $card->create('member_card',$data["card"]["member_card"]["base_info"],$especial = $data["card"]["member_card"]["especial"]);

        //返回json
        return json_encode($result);
    }

    /**
     * 创建会员卡
     * @param Card $card
     * @param PlatformService $platform
     */
    public function createLandingPage(Card $card, PlatformService $platform){
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
    public function membershipActivate(Card $card, PlatformService $platform){
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
     * 更新会员信息
     * @param Card $card
     * @param PlatformService $platform
     */
    public function membershipUpdate(Card $card, PlatformService $platform){
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
    public function delete(Card $card, PlatformService $platform){
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
     * 获取会员卡code
     * @param MessageService $message
     * @return string
     */
    public function getCode(MessageService $message){
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        //调用接口
        $result = $message->getCode($appid, $data['card_id'], $data['openid']);

        return json_encode($result);
    }

    public function uploadImage(Material $material, PlatformService $platform)
    {
        // 参数
        $appid = request('appid');
        $file = request()->file('image');

        // 授权
        $platform->authorizeAPI($material, $appid);

        //保存图片
        $path = base_path()."/image/";

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        $file->move($path, $file->getClientOriginalName());

        //调用接口
        $result = $material->uploadArticleImage($path.$file->getClientOriginalName());

        return json_encode($result);
    }
}