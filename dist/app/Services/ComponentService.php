<?php
namespace Wechat\Services;

use EasyWeChat\Foundation\Application;
use Wechat\Modules\Component\Guard;
use Wechat\Modules\Component\VerifyTicket;
use Wechat\Repositories\ComponentRepository;

/**
 * Component 服务
 * @package Wechat\Services
 */
class ComponentService
{
    /**
     * EasyWechat 微信接口入口对象
     * @var Application
     */
    protected $api;

    /**
     * 仓库
     * @var
     */
    protected $repository;

    /**
     * ComponentService constructor.
     * @param Application $api
     * @param ComponentRepository $repository
     */
    public function __construct(Application $api, ComponentRepository $repository)
    {
        $this->api = $api;
        $this->repository = $repository;
    }

    /**
     * 授权事件处理
     *
     * @return string
     */
    public function authEventProcess()
    {
        $server = $this->api->component_server;

        $server->setMessageHandler(function ($message) {
            switch ($message->InfoType) {
                /*
                 * 1、推送component_verify_ticket
                 * 出于安全考虑，在第三方平台创建审核通过后，微信服务器每隔10分钟会向第三方的消息接收地址推送一次
                 * component_verify_ticket，用于获取第三方平台接口调用凭据
                 * 注: component_verify_ticket 在底层调用接口会用到, 这里只考虑保存。
                 */
                case Guard::MSG_VERIFY_TICKET:
                    VerifyTicket::setTicket($message['ComponentVerifyTicket']);
                    break;
            }
        });

        return $server->serve();
    }

    /**
     * 步骤2：引入用户进入授权页
     * 第三方平台方可以在自己的网站:中放置“微信公众号授权”的入口，引导公众号运营者进入授权页。授权页网址为
     * https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=xxxx&pre_auth_code=xxxxx&redirect_uri=xxxx，
     * 该网址中第三方平台方需要提供第三方平台方appid、预授权码和回调URI
     *
     * @param $callback
     * @return mixed
     */
    public function authRedirectUrl($callback)
    {
        // 获取API接口
        $component = $this->api->component;
        return $component->getAuthUrl($callback);
    }

    /**
     * 步骤4：授权后回调URI，得到授权码（authorization_code）和过期时间
     * 授权流程完成后，授权页会自动跳转进入回调URI，并在URL参数中返回授权码和过期时间(redirect_url?auth_code=xxx&expires_in=600)
     * 在得到授权码后，第三方平台方可以使用授权码换取授权公众号的接口调用凭据（authorizer_access_token，也简称为令牌）
     *
     * @param $auth_code
     */
    public function saveAuthorization($auth_code)
    {
        // 获取API接口
        $component = $this->api->component;

        // 换取公众号的接口调用凭据
        $result = $component->queryAuth($auth_code);
        $info = $result['authorization_info'];

        // 创建一个授权对象
        $authorizer = $this->repository->ensureAuthorizer($info['authorizer_appid']);

        // 刷新令牌主要用于公众号第三方平台获取和刷新已授权用户的access_token，只会在授权时刻提供，请妥善保存。 一旦丢失，只能让用户重新
        // 授权，才能再次拿到新的刷新令牌
        $authorizer->refresh_token = $info['authorizer_refresh_token'];
        $authorizer->func_info = \GuzzleHttp\json_encode($info['func_info']);

        // 保存到数据库
        $authorizer->save();
    }
}