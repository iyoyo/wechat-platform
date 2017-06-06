<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 16/11/2
 * Time: 14:50
 */

namespace iBrand\WechatPlatform\Services;

use iBrand\WechatPlatform\Modules\OAuth\AccessToken;
use iBrand\WechatPlatform\Modules\OAuth\OAuth;
use iBrand\WechatPlatform\Modules\Component\ComponentToken;
use iBrand\WechatPlatform\Repositories\OAuthTokenRepository;


/**
 * 基于Component授权的OAuth服务
 *
 * Class OAuthService
 * @package Wechat\Services
 */
class OAuthService
{
    /**
     * 仓库
     * @var
     */
    protected $repository;

    /**
     * 网页授权接口
     * @var
     */
    protected $oauth;

    /**
     * 第三方平台接口
     * @var Component
     */
    protected $component;

    /**
     * ComponentService constructor.
     * @param OAuthTokenRepository $repository
     * @param OAuth $oauth
     */
    public function __construct(
        OAuthTokenRepository $repository,
        OAuth $oauth)
    {
        $this->repository = $repository;
        $this->oauth = $oauth;
    }

    /**
     * 获取引导用户授权的URL
     *
     * @param $appid
     * @param $callback
     * @param string $scope
     * @return mixed
     */
    public function authRedirectUrl($appid, $callback, $scope = 'snsapi_userinfo')
    {
        return $this->oauth->getOAuthUrl($appid, $callback, $scope);
    }

    /**
     * 保存用户授权信息
     *
     * @param $appid
     * @param $code
     * @return
     */
    public function saveAuthorization($appid, $code)
    {
        // 获取Token
        $result = $this->oauth->getOAuthToken($appid, $code);

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
        //获取refresh_token
        $token = $this->repository->getToken($appid, $openid);

        return $this->oauth->getUserInfo($appid, $token->refresh_token, $openid);
    }
}