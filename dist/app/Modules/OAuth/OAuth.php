<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 16/11/2
 * Time: 13:55
 */

namespace Wechat\Modules\OAuth;

use EasyWeChat\Core\AbstractAPI;

/**
 * 代公众号发起网页授权
 * 1. 获取code
 * 2. 通过code换取accesstoken
 *
 * @package Wechat\Models\OAuth
 */
class OAuth extends AbstractAPI
{
    const API_OAUTH_URL = 'https://open.weixin.qq.com/connect/oauth2/authorize'; //请求CODE
    const API_TOKEN_GET = 'https://api.weixin.qq.com/sns/oauth2/component/access_token'; //通过code换取access_token
    const API_TOKEN_REFRESH = 'https://api.weixin.qq.com/sns/oauth2/component/refresh_token'; //刷新access_token
    const API_USERINFO = 'https://api.weixin.qq.com/sns/userinfo'; // 获取用户信息

    /**
     * 第三方平台appid
     *
     * @var string
     */
    protected $component_appid;

    /**
     * 构造函数.
     * @param $app_id
     */
    public function __construct($component_appid)
    {
        $this->component_appid = $component_appid;
    }

    /**
     * 第一步：请求CODE
     * 在确保微信公众账号拥有授权作用域（scope参数）的权限的前提下（一般而言，已微信认证的服务号拥有snsapi_base和snsapi_userinfo），
     * 使用微信客户端打开以下链接（严格按照以下格式，包括顺序和大小写，并请将参数替换为实际内容）
     *
     * @param $authorizer_appid
     * @param $callback
     * @param string $scope snsapi_base, snsapi_userinfo
     * @param string $state
     * @return string
     */
    public function getOAuthUrl($authorizer_appid, $callback, $scope = 'snsapi_userinfo', $state = 'STATE')
    {
        $params = array(
            'appid'           => $authorizer_appid,
            'redirect_uri'    => $callback,
            'response_type'   => 'code',
            'scope'           => $scope,
            'state'           => $state,
            'component_appid' => $this->component_appid,
        );

        return self::API_OAUTH_URL . '?' . http_build_query($params) . '#wechat_redirect';
    }

    /**
     * 第二步：通过code换取access_token
     * 获取第一步的code后，请求以下链接获取access_token：
     *
     * @param $authorizer_appid
     * @param $code
     * @return mixed
     */
    public function getOAuthToken($authorizer_appid, $code)
    {
        $params = array(
            'appid'           => $authorizer_appid,
            'code'            => $code,
            'grant_type'      => 'authorization_code',
            'component_appid' => $this->component_appid,
        );

        return $this->parseJSON('get', [self::API_TOKEN_GET, $params]);
    }

    /**
     * 第四步：通过网页授权access_token获取用户基本信息（需授权作用域为snsapi_userinfo）
     *
     * @param $refresh_token
     * @param $openid
     * @param string $lang
     * @return \Psr\Http\Message\ResponseInterface
     * @internal param $access_token
     */
    public function getUserInfo($appid,$refresh_token, $openid, $lang = 'zh_CN')
    {
        // 通过refresh_token 获取 access_token
        $config = config('wechat');
        $access_token = new AccessToken($config['app_id'],$config['secret']);
        $access_token->setAccessToken($appid, $refresh_token,$this->accessToken);

        // 获取用户信息
        $params = [
            'access_token'      => $access_token->getToken(),
            'openid'            => $openid,
            'lang'              => $lang,
        ];
        $http = $this->getHttp();
        return $http->parseJSON($http->get(self::API_USERINFO, $params));;

    }
}