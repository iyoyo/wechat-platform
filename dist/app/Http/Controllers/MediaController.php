<?php

namespace iBrand\WechatPlatform\Http\Controllers;

use iBrand\WechatPlatform\Services\PlatformService;
use EasyWeChat\Material\Material;
use EasyWeChat\Message\Article;
use Exception;

/**
 * 素材管理
 * @package Wechat\Http\Controllers
 */
class MediaController extends Controller
{
    /**
     * 上传会员卡背景图
     * @param Material $material
     * @param PlatformService $platform
     * @return string
     * @throws Exception
     */
    public function uploadArticleImage(Material $material, PlatformService $platform){
        // 参数
        $appid = request('appid');
        $file = request()->file('image');

        if (empty($file)){
            throw new Exception( 'cannot not find file.', 2);
        }

        // 授权
        $platform->authorizeAPI($material, $appid);

        //修改文件名
        rename($file->getPathname(), "/tmp/".$file->getClientOriginalName());

        //调用接口
        $result = $material->uploadArticleImage("/tmp/".$file->getClientOriginalName());

        return json_encode($result);
    }

    /**
     * 上传图片素材
     * @param Material $material
     * @param PlatformService $platform
     * @return string
     * @throws Exception
     */
    public function uploadImage(Material $material, PlatformService $platform){
        // 参数
        $appid = request('appid');
        $file = request()->file('image');

        if (empty($file)){
            throw new Exception( 'cannot not find file.', 2);
        }

        // 授权
        $platform->authorizeAPI($material, $appid);

        //修改文件名
        rename($file->getPathname(), "/tmp/".$file->getClientOriginalName());

        //调用接口
        $result = $material->uploadImage("/tmp/".$file->getClientOriginalName());

        return json_encode($result);
    }

    /**
     * 上传永久图文消息
     * @param Material $material
     * @param PlatformService $platform
     * @return string
     */
    public function uploadArticle(Material $material, PlatformService $platform){
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        $article = new Article($data);
        // 授权
        $platform->authorizeAPI($material, $appid);
        //调用接口
        $result = $material->uploadArticle($article);

        return json_encode($result);
    }
}