<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 16/11/2
 * Time: 14:50
 */

namespace Wechat\Services;

use EasyWeChat\Foundation\Application;
use Wechat\Repositories\OAuthTokenRepository;

/**
 * 基于Component授权的OAuth服务
 *
 * Class OAuthService
 * @package Wechat\Services
 */
class OAuthService
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
     * @param OAuthTokenRepository $repository
     */
    public function __construct(Application $api, OAuthTokenRepository $repository)
    {
        $this->api = $api;
        $this->repository = $repository;
    }

    /**
     * 获取引导用户授权的URL
     *
     * @param $callback
     * @return mixed
     */
    public function authRedirectUrl($authorizer_appid, $callback, $scope = 'snsapi_userinfo')
    {
        // 获取API接口
        $oauth = $this->api->component_oauth;
        return $oauth->getOAuthUrl($authorizer_appid, $callback, $scope);
    }

    /**
     * 保存用户授权信息
     *
     * @param $code
     */
    public function saveAuthorization($appid, $code)
    {
        // 获取API接口
        $oauth = $this->api->component_oauth;

        // 获取Token
        $result = $oauth->getOAuthToken($appid, $code);

        // 保存Token
        $token = $this->repository->ensureToken($appid, $result['openid']);
        $token->access_token = $result['access_token'];
        $token->refresh_token = $result['refresh_token'];
        $token->scope = $result['scope'];
        $token->expires_in = $result['expires_in'];
        $token->save();

        return $token;
    }

    /**
     * 获取用户信息
     *
     * @param $appid
     * @param $openid
     * @return Application
     */
    public function getUserInfo($appid, $openid)
    {
        $token = $this->repository->getToken($appid, $openid);

        //

        return $this->api;
    }
}