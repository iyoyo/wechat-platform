<?php

namespace iBrand\WechatPlatform\Http\Controllers;

use EasyWeChat\User\User;
use iBrand\WechatPlatform\Services\PlatformService;

/**
 * 粉丝
 * @package Wechat\Http\Controllers
 */
class FansController extends Controller
{
    protected $user;

    protected $platform;

    public function __construct(
        User $user
        ,PlatformService $platformService
    )
    {
        $this->user=$user;
        $this->platform = $platformService;
    }


    /**
     * 获取粉丝列表
     * @return \EasyWeChat\Support\Collection
     */

    public function lists(){
        // 参数
        $appid = request('appid');

        $nextOpenId = request('nextOpenId');

        // 授权
        $this->platform->authorizeAPI($this->user, $appid);

        // 调用接口
        $result = $this->user->lists($nextOpenId);

        // 返回JSON
        return $result;
    }

    /**
     * 获取粉丝信息
     * @return \EasyWeChat\Support\Collection
     */


    public function get(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->user, $appid);

        // 调用接口
        $result = $this->user->batchGet($data['openid']);

        // 返回JSON
        return $result;
    }







}
