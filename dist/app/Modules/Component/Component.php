<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Modules\Component;

use Carbon\Carbon;
use EasyWeChat\Core\AbstractAPI;
use EasyWeChat\Core\AccessToken;
use Illuminate\Support\Facades\Cache;

/**
 * 公众号第三方平台服务
 *
 * 授权流程技术说明
 * 步骤1：第三方平台方获取预授权码（pre_auth_code）
 *       预授权码是第三方平台方实现授权托管的必备信息，可以通过本文下文中的XXXX API来获取预授权码。
 * 步骤2：引入用户进入授权页
 *       第三方平台方可以在自己的网站:中放置“微信公众号授权”的入口，引导公众号运营者进入授权页。
 *       授权页网址为https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=xxxx&pre_auth_code=xxxxx&redirect_uri=xxxx，
 *       该网址中第三方平台方需要提供第三方平台方appid、预授权码和回调URI
 * 步骤3：用户确认并同意登录授权给第三方平台方
 *       用户进入第三方平台授权页后，需要确认并同意将自己的公众号登录授权给第三方平台方，完成授权流程。
 * 步骤4：授权后回调URI，得到授权码（authorization_code）和过期时间
 *       授权流程完成后，授权页会自动跳转进入回调URI，并在URL参数中返回授权码和过期时间(redirect_url?auth_code=xxx&expires_in=600)
 * 步骤5：利用授权码调用用户公众号的相关API
 *       在得到授权码后，第三方平台方可以使用授权码换取授权公众号的接口调用凭据（authorizer_access_token，也简称为令牌），
 *       再通过该接口调用凭据，按照公众号开发者文档（mp.weixin.qq.com/wiki）的说明，去调用公众号相关API（能调用哪些API，
 *       取决于用户将哪些权限集授权给了第三方平台方，也取决于公众号自身拥有哪些接口权限），使用JS SDK等能力。
 *
 * 参考:
 * https://open.weixin.qq.com/cgi-bin/showdocument?action=dir_list&t=resource/res_list&verify=1&id=open1453779503&token=&lang=zh_CN
 */
class Component extends AbstractAPI
{
    const COMPONENT_LOGIN_PAGE = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=%s&pre_auth_code=%s&redirect_uri=%s';
    const API_CREATE_PREAUTHCODE = 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode';
    const API_QUERY_AUTH = 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth';
    const API_GET_AUTHORIZER_INFO = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info';
    const API_GET_AUTHORIZER_OPTION = 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_option';
    const API_SET_AUTHORIZER_OPTION = 'https://api.weixin.qq.com/cgi-bin/component/api_set_authorizer_option';

    const CACHE_PRE_AUTH_CODE = 'wechat.pre_auth_code';

    /**
     * 第三方平台appid.
     *
     * @var string
     */
    protected $component_appid;

    /**
     * ComponentService 构造函数.
     * @param ComponentToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->component_appid = $accessToken->getAppId();
        parent::__construct($accessToken);
    }

    /**
     * 引入用户进入授权页.
     *
     * @param $callback
     * @return string
     */
    public function getAuthUrl($callback)
    {
        $preAuthCode = $this->createPreAuthCode();

        // 拼接出微信公众号登录授权页面url
        return sprintf(self::COMPONENT_LOGIN_PAGE, $this->component_appid, $preAuthCode, urlencode($callback));
    }

    /**
     * 3、获取预授权码pre_auth_code.
     *
     * @return mixed
     */
    public function createPreAuthCode()
    {
        $cacheKey = self::CACHE_PRE_AUTH_CODE;

        return Cache::get($cacheKey, function () use ($cacheKey) {
            $params = [
                'component_appid' => $this->component_appid,
            ];
            $response = $this->parseJSON('json', [self::API_CREATE_PREAUTHCODE, $params]);

            $pre_auth_code = $response['pre_auth_code'];

            // 把pre_auth_code缓存起来
            $expiresAt = Carbon::now()->addSeconds($response['expires_in']);
            Cache::put($cacheKey, $pre_auth_code, $expiresAt);

            return $pre_auth_code;
        });
    }

    /**
     * 4、使用授权码换取公众号的接口调用凭据和授权信息.
     *
     * @param $authorization_code
     * @return mixed
     */
    public function queryAuth($authorization_code)
    {
        $params = [
            'component_appid'    => $this->component_appid,
            'authorization_code' => $authorization_code,
        ];

        return $this->parseJSON('json', [self::API_QUERY_AUTH, $params]);
    }

    /**
     * 6、获取授权方的公众号帐号基本信息.
     *
     * @param $authorizer_appid
     * @return mixed
     */
    public function getAuthorizerInfo($authorizer_appid)
    {
        $params = [
            'component_appid'  => $this->component_appid,
            'authorizer_appid' => $authorizer_appid,
        ];

        return $this->parseJSON('json', [self::API_GET_AUTHORIZER_INFO, $params]);
    }

    /**
     * 7、获取授权方的选项设置信息.
     *
     * @param $authorizer_appid
     * @param $option_name
     * @return mixed
     */
    public function getAuthorizerOption($authorizer_appid, $option_name)
    {
        $params = [
            'component_appid'  => $this->component_appid,
            'authorizer_appid' => $authorizer_appid,
            'option_name'      => $option_name,
        ];

        return $this->parseJSON('json', [self::API_GET_AUTHORIZER_OPTION, $params]);
    }

    /**
     * 8、设置授权方的选项信息.
     *
     * @param $authorizer_appid
     * @param $option_name
     * @param $option_value
     * @return mixed
     */
    public function setAuthorizerOption($authorizer_appid, $option_name, $option_value)
    {
        $params = [
            'component_appid'  => $this->component_appid,
            'authorizer_appid' => $authorizer_appid,
            'option_name'      => $option_name,
            'option_value'     => $option_value,
        ];

        return $this->parseJSON('json', [self::API_SET_AUTHORIZER_OPTION, $params]);
    }

    /**
     * 创建一个授权公众号的访问码
     *
     * @param $appid
     * @param $refresh_token
     */
    public function createAuthorizerToken($appid, $refresh_token)
    {
        $component_token = $this->getAccessToken();

        return new AuthorizerToken(
            $appid,
            $refresh_token,
            $component_token
        );
    }
}
