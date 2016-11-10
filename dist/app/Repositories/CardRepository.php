<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 16/11/2
 * Time: 09:58
 */

namespace Wechat\Repositories;

use Wechat\Models\Card;

/**
 * Card 仓库
 * @package Wechat\Repositories
 */
class CardRepository
{
    /**
     * 保存Card
     * @param $appid
     * @param $card_id
     * @param $code
     * @param $openid
     * @return mixed
     */
    public function creatCard($appid, $card_id, $code, $openid){
        return Card::firstOrNew(['appid' => $appid, 'card_id' => $card_id, 'code' => $code, 'openid' => $openid]);
    }

    /**
     * 获取code
     * @param $appid
     * @param $card_id
     * @param $openid
     * @return mixed
     */
    public function getCode($appid, $card_id, $openid){
        return Card::where('appid', $appid)
            ->where('card_id', $card_id)
            ->where('openid', $openid)
            ->first();
    }

    /**
     * 删除会员卡
     * @param $appid
     * @param $card_id
     * @param $code
     * @param $openid
     */
    public function delCard($appid, $card_id, $code, $openid){
        Card::where('appid', $appid)
            ->where('card_id', $card_id)
            ->where('code', $code)
            ->where('openid', $openid)
            ->delete();
    }
}