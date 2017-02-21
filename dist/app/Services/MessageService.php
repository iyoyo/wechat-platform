<?php
namespace Wechat\Services;

use \EasyWeChat\Server\Guard;
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
    public function accountEventProcess($appid)
    {
        $this->server->setMessageHandler(function ($message) use ($appid) {
            if($message->MsgType == "event") {
                switch ($message->Event) {
                    case "user_get_card":
                        $card = $this->repository->createCard($appid, $message->CardId, $message->UserCardCode, $message->FromUserName);
                        $card->save();
                        break;
                    case "user_del_card":
                        $this->repository->deleteCard($appid, $message->CardId, $message->UserCardCode, $message->FromUserName);
                        break;
                    //全网发布测试：事件
                    case "LOCATION":
                        return "LOCATIONfrom_callback";
                }
            }
            if($message->MsgType == "text") {
                //全网发布测试：文本消息
                if($message->Content == "TESTCOMPONENT_MSG_TYPE_TEXT"){
                    return "TESTCOMPONENT_MSG_TYPE_TEXT_callback";
                }
            }
        });

        //全网发布测试：调用接口
        $message = $this->server->getMessage();
        if(strpos($message->Content, "QUERY_AUTH_CODE:") ==! false){
            return $message->Content;
        }
        return $this->server->serve();
    }

    /**
     * 获取会员卡code
     *
     * @param $appid
     * @param $card_id
     * @param $openid
     * @return string
     */
    public function getCode($appid, $card_id, $openid){
        return $this->repository->getCode($appid, $card_id, $openid);
    }

}