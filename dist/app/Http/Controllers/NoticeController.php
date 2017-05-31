<?php

namespace Wechat\Http\Controllers;

use EasyWeChat\Notice\Notice;
use Wechat\Services\PlatformService;

/**
 * 模板消息
 * @package Wechat\Http\Controllers
 */
class NoticeController extends Controller
{
    /**
     * 发送模板消息
     *
     * @param Notice $notice
     * @param PlatformService $platform
     * @return string
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function send(Notice $notice, PlatformService $platform) {
        // 参数
        $appid = request('appid');
        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($notice, $appid);

        // 调用接口
        $result = $notice->send($data);

        // 返回JSON
        return json_encode($result);
    }

    /**
     * 获取所有模板列表
     *
     * @param Notice $notice
     * @param PlatformService $platform
     * @return string
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function getAll(Notice $notice, PlatformService $platform) {
        // 参数
        $appid = request('appid');

        // 授权
        $platform->authorizeAPI($notice, $appid);

        // 调用接口
        $result = $notice->getPrivateTemplates();

        // 返回JSON
        return json_encode($result);
    }


    /**
     *
     *
     * @param Notice $notice
     * @param PlatformService $platform
     * @return string
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     */
    public function sendAll(Notice $notice, PlatformService $platform) {
        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $platform->authorizeAPI($notice, $appid);

        $error=[];

        $i=0;
        if(isset($data['touser'])&&is_array($data['touser'])){
            if(count($data['touser'])>100){
                return false;
            }
            $tousers=$data['touser'];
            foreach ($tousers as $item){
                $data['touser']=$item;
                // 调用接口
                try{
                   $notice->send($data);
                    $i++;
                }catch (\Exception $e){
                    $error[]=$data['touser'];
                }

            }
        }
        return json_encode(['success_num'=>$i,'error'=>$error]);
    }


}
