<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Http\Controllers;

use EasyWeChat\QRCode\QRCode;
use iBrand\WechatPlatform\Services\PlatformService;

/**
 * 二维码
 */
class QRCodeController extends Controller
{
    protected $QRCode;

    protected $platform;

    public function __construct(
        QRCode $QRCode, PlatformService $platformService
    ) {
        $this->QRCode = $QRCode;
        $this->platform = $platformService;
    }

    /**
     * 创建临时二维码
     * @return \EasyWeChat\Support\Collection
     */
    public function storeTemporary()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        $expireSeconds = ! isset($data['expire_seconds']) || empty($data['expire_seconds']) ? null : $data['expire_seconds'];

        // 授权
        $this->platform->authorizeAPI($this->QRCode, $appid);

        // 调用接口

        $result = $this->QRCode->temporary($data['scene_id'], $expireSeconds);

        if (isset($result->ticket) && ! empty($result->ticket)) {
            $res = $this->QRCode->url($result->ticket);
            $result->qr_code_url = $res;

            return $result;
        }

        // 返回JSON
        return $result;
    }

    /**
     * 创建永久二维码
     * @return \EasyWeChat\Support\Collection
     */
    public function storeForever()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->QRCode, $appid);

        // 调用接口

        $result = $this->QRCode->forever($data['scene_id']);

        if (isset($result->ticket) && ! empty($result->ticket)) {
            $res = $this->QRCode->url($result->ticket);
            $result->qr_code_url = $res;

            return $result;
        }

        // 返回JSON
        return $result;
    }

    /**
     * 获取二维码网址
     * @return \EasyWeChat\Support\Collection
     */
    public function getUrl()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->QRCode, $appid);

        // 调用接口

        $result = $this->QRCode->url($data['ticket']);

        // 返回JSON
        return $result;
    }
}
