<?php
namespace Wechat\Services;

use \EasyWeChat\Server\Guard;
use Wechat\Repositories\CardRepository;
use Wechat\Repositories\AuthorizerRepository;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Video;
use EasyWeChat\Message\Article;
use EasyWeChat\Message\Material;
use EasyWeChat\Message\News;

use Log;



/**
 * 公众平台推送
 * @package Wechat\Services
 */
class MessageService
{

    const GET    = 'GET';
    const POST   = 'POST';
    const PUT    = 'PUT';
    const PATCH  = 'PATCH';
    const DELETE = 'DELETE';

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

    protected $authorizerRepository;


    public function __construct(
        CardRepository $repository,
        Guard $server,
        AuthorizerRepository $authorizerRepository
)
    {
        $this->repository = $repository;
        $this->server = $server;
        $this->authorizerRepository=$authorizerRepository;
    }

    /**
     * 平台授权事件处理
     *
     * @return string
     */
    public function accountEventProcess($appid)
    {
        $url= $this->authorizerRepository->getCallBackUrl($appid);
        $this->server->setMessageHandler(function ($message) use ($appid,$url) {
//            return json_encode($message);
            if($message->MsgType == "event") {
                switch ($message->Event) {
                    case "subscribe":
                        $key=isset($message->EventKey)?$message->EventKey:'';
                        $ticket=isset($message->Ticket)?$message->Ticket:'';
                        $params=[
                            'app_id'=>$appid,
                            'openid'=>$message->FromUserName,
                            'event_type'=>'subscribe',
                            'key'=>$key,
                            'ticket'=>$ticket,
                        ];
                        return $this->callBackEvent($url,$params);
                        break;
                    case "unsubscribe":
                        $params=[
                            'app_id'=>$appid,
                            'openid'=>$message->FromUserName,
                            'event_type'=>'unsubscribe',
                        ];
                        return $this->callBackEvent($url,$params);
                        break;
                    case "user_get_card":
                        $params=[
                            'appid'=>$appid,
                            'open_id'=>$message->FromUserName,
                            'event_type'=>'user_get_card',
                            'card_id'=>$message->CardId,
                            'code'=>$message->UserCardCode,
                        ];
                   return $this->callBackEvent($url,$params);
                   break;
//                        $card = $this->repository->createCard($appid, $message->CardId, $message->UserCardCode, $message->FromUserName);
//                        $card->save();
//                        break;
                    case "user_del_card":
                        $params=[
                            'appid'=>$appid,
                            'open_id'=>$message->FromUserName,
                            'event_type'=>'user_del_card',
                            'card_id'=>$message->CardId,
                            'code'=>$message->UserCardCode,
                        ];
                        return $this->callBackEvent($url,$params);
//                        $this->repository->deleteCard($appid, $message->CardId, $message->UserCardCode, $message->FromUserName);
//                        break;

                    case "SCAN":
                        $params=[
                            'app_id'=>$appid,
                            'openid'=>$message->FromUserName,
                            'event_type'=>'SCAN',
                            'key'=>$message->EventKey,
                            'ticket'=>$message->Ticket,
                        ];
                        return $this->callBackEvent($url,$params);
                        break;

                    case "CLICK":
                        $params=[
                            'app_id'=>$appid,
                            'open_id'=>$message->FromUserName,
                            'event_type'=>'CLICK',
                            'key'=>$message->EventKey,
                        ];
                        return $this->callBackEvent($url,$params);
                        break;
                    //全网发布测试：事件
                    case "LOCATION":
                        return "LOCATIONfrom_callback";
//                    点击事件
                }
            }
            if($message->MsgType == "text") {
                //全网发布测试：文本消息
                if($message->Content == "TESTCOMPONENT_MSG_TYPE_TEXT"){
                    return "TESTCOMPONENT_MSG_TYPE_TEXT_callback";
                }
                $params=[
                    'app_id'=>$appid,
                    'open_id'=>$message->FromUserName,
                    'type'=>$message->MsgType,
                    'content'=>$message->Content,
                ];
                $data=$this->BackCurl($url.'/wechat_call_back/message',$method = self::GET,$params);
                return $this->BackMessage($data);
            }
            return '';

        });

        //全网发布测试：调用接口
        $message = $this->server->getMessage();
        if($message['MsgType'] == "text" && strstr($message['Content'], "QUERY_AUTH_CODE:")) {
                return $message['Content'];
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


    public function BackCurl($url, $method = self::GET, $params = [], $request_header = [])
    {
        $request_header = ['Content-Type' => 'application/x-www-form-urlencoded'];
        if ($method === self::GET || $method === self::DELETE) {
            $url .= (stripos($url, '?') ? '&' : '?') . http_build_query($params);
            $params = [];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_header);
        if ($method === self::POST) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }


        // 消息回复
        protected function BackMessage($data){
            $type=isset($data['type'])?$data['type']:'';
            $content=isset($data['content'])?$data['content']:'';
            $mediaId=isset($data['media_id'])?$data['media_id']:'';
            // 图文素材
            $title=isset($data['title'])?$data['title']:'';
            $description=isset($data['description'])?$data['description']:'';
            $image=isset($data['image'])?$data['image']:'';
            $url=isset($data['url'])?$data['url']:'';

            switch ($type) {
                case 'text':
                    return  new Text(['content' =>$content]);
                    break;
                case 'image':
                     return new Image(['media_id' => $mediaId]);
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return new Video(['media_id' => $mediaId]);
                    break;
                case 'article':
                    $news = new News([
                        'title'       =>$title,
                        'description' =>$description,
                        'url'         =>$url,
                        'image'       => $image
                    ]);
                    return $news;
                    break;
                // ... 其它消息
                default:
                    return '';
                    break;
            }

        }


    // K事件处理
    public function  callBackEvent($url,$data){
        $data=$this->BackCurl($url.'/wechat_call_back/event',$method = self::GET,$data);
        return $this->BackMessage($data);
    }








}