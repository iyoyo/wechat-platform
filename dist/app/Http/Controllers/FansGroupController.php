<?php

namespace Wechat\Http\Controllers;

use EasyWeChat\User\Group;
use Wechat\Services\PlatformService;

/**
 * 粉丝分组
 * @package Wechat\Http\Controllers
 */
class FansGroupController extends Controller
{
    protected $group;

    protected $platform;

    public function __construct(
        Group $group
        ,PlatformService $platformService
    )
    {
        $this->group=$group;
        $this->platform = $platformService;
    }


    /**
     * 获取所有分组
     * @return \EasyWeChat\Support\Collection
     */

    public function lists(){
        // 参数
        $appid = request('appid');

        // 授权
        $this->platform->authorizeAPI($this->group, $appid);

        // 调用接口
        $result = $this->group->lists();

        // 返回JSON
        return $result;
    }

    /**
     * 创建分组
     * @return \EasyWeChat\Support\Collection
     */


    public function create(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->group, $appid);

        // 调用接口
        $result = $this->group->create($data['name']);

        // 返回JSON
        return $result;
    }

    /**
     * 修改分组
     * @return \EasyWeChat\Support\Collection
     */


    public function update(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->group, $appid);

        // 调用接口
        $result = $this->group->update($data['groupid'],$data['name']);

        // 返回JSON
        return $result;
    }

    /**
     * 删除分组
     * @return \EasyWeChat\Support\Collection
     */


    public function delete(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->group, $appid);

        // 调用接口
        $result = $this->group->delete($data['groupid']);

        // 返回JSON
        return $result;
    }


    /**
     * 移动用户到指定分组
     * @return \EasyWeChat\Support\Collection
     */


    public function moveUsers(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->group, $appid);

        // 调用接口
        $result = $this->group->moveUsers($data['openids'],$data['groupid']);

        // 返回JSON
        return $result;
    }





}
