<?php

namespace iBrand\WechatPlatform\Http\Controllers;

use EasyWeChat\Material\Material;
use EasyWeChat\Message\Article;
use iBrand\WechatPlatform\Services\PlatformService;
use Exception;
/**
 * 菜单
 * @package Wechat\Http\Controllers
 */
class MediasController extends Controller
{

    protected $material;
    protected $platform;


    public function __construct(
        Material $material
        ,PlatformService $platformService


    )
    {

        $this->material = $material;
        $this->platform = $platformService;

    }


    /**
     * 上传图片素材
     */
    public function RemoteImage(){

        $appid = request('appid');

        $file = request()->file('image');

        // 授权
        $this->platform->authorizeAPI($this->material, $appid);

        //修改文件名
        rename($file->getPathname(), "/tmp/".$file->getClientOriginalName());

        //调用接口
        $result = $this->material->uploadImage("/tmp/".$file->getClientOriginalName());

        return json_encode($result);
    }


    /**
     * 上传图文内容素材
     */
    public function RemoteArticleImage(){

        $appid = request('appid');

        $file = request()->file('image');

        // 授权
        $this->platform->authorizeAPI($this->material, $appid);

        //修改文件名
        rename($file->getPathname(), "/tmp/".$file->getClientOriginalName());

        //调用接口
        $result = $this->material->uploadArticleImage("/tmp/".$file->getClientOriginalName());

        return json_encode($result->url);
    }






    /**
     * 上传视频素材
     */
    public function RemoteVideo(){

        $appid = request('appid');

        $file = request()->file('video');

        $title = request('title');

        $description= request('description');

        // 授权
        $this->platform->authorizeAPI($this->material, $appid);

        //修改文件名
        rename($file->getPathname(), "/tmp/".$file->getClientOriginalName());

        //调用接口
        $result = $this->material->uploadVideo("/tmp/".$file->getClientOriginalName(),$title,$description);


        return json_encode($result);
    }


    /**
     * 删除素材
     */
    public function delete(){

        $appid = request('appid');

        $data = request()->json()->all();

        $mediaId=$data['mediaId'];

        // 授权
        $this->platform->authorizeAPI($this->material, $appid);

        //调用接口
        $result = $this->material->delete($mediaId);

        return json_encode($result);
    }


    /**
     * 上传永久图文消息
     * @return string
     */
    public function RemoteArticle(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        $article=[];

        if(count($data) == count($data,1)){
            $article=new Article($data);
        }else{
            foreach ($data as $item){
                $article[]=new Article($item);
            }
        }

        // 授权
        $this->platform->authorizeAPI($this->material, $appid);
        //调用接口
        $result = $this->material->uploadArticle($article);

        return json_encode($result);
    }


    /**
     * 获取素材通过mediaId
     * @return string
     */
        public function get(){
            // 参数
            $appid = request('appid');

            $data = request()->json()->all();

            // 授权
            $this->platform->authorizeAPI($this->material, $appid);

            //调用接口
            $result = $this->material->get($data['mediaId']);

            return json_encode($result);

        }


    /**
     * 获取永久素材列表
     * @return string
     */

    public function getLists(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->material, $appid);

        //调用接口
        $result = $this->material->lists($data['type'],$data['offset'],$data['count']);

        return json_encode($result);

    }


    /**
     * 获取素材计数
     * @return string
     */

    public function stats(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->material, $appid);

        //调用接口
        $result = $this->material->stats();

        return json_encode($result);

    }


    /**
     * 修改图文素材
     * @return string
     */

    public function updateArticle(){
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->material, $appid);

        //调用接口

        $article=new Article($data);

        if(isset($data['index'])){
            $result = $this->material->updateArticle($data['mediaId'],$article,$data['index']);
        }else{
            $result = $this->material->updateArticle($data['mediaId'],$article);
        }
        return json_encode($result);

    }
















}
