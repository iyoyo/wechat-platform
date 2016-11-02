<?php
/**
 * Created by PhpStorm.
 * User: andrew
 * Date: 16/11/2
 * Time: 09:58
 */

namespace Wechat\Repositories;

use Wechat\Models\Authorizer;

/**
 * Component 仓库
 * @package Wechat\Repositories
 */
class ComponentRepository
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
}