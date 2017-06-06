<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 16/11/2
 * Time: 09:58
 */

namespace iBrand\WechatPlatform\Repositories;

use iBrand\WechatPlatform\Models\Authorizer;

/**
 * Authforizer 仓库
 * @package Wechat\Repositories
 */
class AuthorizerRepository
{
    /**
     * 获取APP授权
     *
     * @param $appid
     * @return Authorizer
     */
    public function getAuthorizer($appid)
    {
        $authorizer = Authorizer::where('appid', $appid)->first();
        return $authorizer;
    }

    /**
     * 获取APP授权, 不存在则创建一个
     *
     * @param $appid
     */
    public function ensureAuthorizer($appid)
    {
        $authorizer = Authorizer::firstOrNew(['appid' => $appid]);
        return $authorizer;
    }

    public function getAuthorizersByClient($clientId)
    {
        return Authorizer::where('client_id', $clientId)->get();
    }

    public function updateCallBackUrl($clientId,$url)
    {
        return Authorizer::where('client_id',$clientId)->update(['call_back_url'=>$url]);
    }

    public function getCallBackUrl($appId){
         $res=Authorizer::where('appid',$appId)->first(['call_back_url'])->toArray();
         return $res['call_back_url'];

    }




}