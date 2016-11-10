<?php
namespace Wechat\Services;

use EasyWeChat\Server\Guard;
use Wechat\Repositories\CardRepository;

/**
 * 公众平台推送
 * @package Wechat\Services
 */
class MessageService
{
    /**
     * 仓库
     * @var
     */
    protected $repository;

    /**
     * 公众平台事件接口
     * @var
     */
    protected $server;

    public function __construct(
        CardRepository $repository,
        Guard $server)
    {
        $this->repository = $repository;
        $this->server = $server;
    }

    /**
     * 平台授权事件处理
     *
     * @return string
     */
    public function cardEventProcess($appid)
    {
        $this->server->setMessageHandler(function ($message) use ($appid) {
            switch ($message->MsgType) {
                case "event":
                    //用户领取会员卡，记录会员卡code
                    if ($message->Event == "user_get_card"){
                        $card = $this->repository->creatCard($appid, $message->CardId, $message->UserCardCode, $message->FromUserName);
                        $card->save();
                    }
                    //用户删除会员卡，一并删除code记录
                    if ($message->Event == "user_del_card"){
                        $this->repository->delCard($appid, $message->CardId, $message->UserCardCode, $message->FromUserName);
                    }
                    break;
            }
        });
        return $this->server->serve();
    }

    /**
     * 获取会员卡code
     *
     * @return string
     */
    public function getCode($appid, $card_id, $openid){
        return $this->repository->getCode($appid, $card_id, $openid);
    }

}