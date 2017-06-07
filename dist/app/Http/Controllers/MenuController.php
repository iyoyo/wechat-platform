<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Http\Controllers;

use EasyWeChat\Menu\Menu;
use iBrand\WechatPlatform\Services\PlatformService;

/**
 * 菜单.
 */
class MenuController extends Controller
{
    protected $menu;
    protected $platform;

    public function __construct(
        Menu $menu, PlatformService $platformService
    ) {
        $this->menu = $menu;
        $this->platform = $platformService;
    }

    /**
     * 获取查询菜单.
     * @return \EasyWeChat\Support\Collection
     */
    public function getAll()
    {

        // 参数
        $appid = request('appid');

        // 授权
        $this->platform->authorizeAPI($this->menu, $appid);

        // 调用接口
        $result = $this->menu->all();

        // 返回JSON
        return $result;
    }

    /**
     * 获取自定义菜单.
     * @return \EasyWeChat\Support\Collection
     */
    public function getCurrent()
    {

        // 参数
        $appid = request('appid');

        // 授权
        $this->platform->authorizeAPI($this->menu, $appid);

        // 调用接口
        $result = $this->menu->current();

        // 返回JSON
        return $result;
    }

    /**
     * 添加菜单.
     * @return \EasyWeChat\Support\Collection
     */
    public function store()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->menu, $appid);

        // 调用接口
        if (isset($data['matchRule']) && ! empty($data['matchRule'])) {
            $result = $this->menu->add($data['buttons'], $data['matchRule']);
        } else {
            $result = $this->menu->add($data['buttons']);
        }

        // 返回JSON
        return $result;
    }

    /**
     * 删除菜单.
     * @return \EasyWeChat\Support\Collection
     */
    public function delMenu()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->menu, $appid);

        // 调用接口
        if (isset($data['menuid']) && empty($data['menuid'])) {
            $result = $this->menu->destroy();
        } else {
            $result = $this->menu->destroy($data['menuid']);
        }
        // 返回JSON
        return $result;
    }
}
