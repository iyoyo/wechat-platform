<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Http\Controllers;

use EasyWeChat\Staff\Staff;
use EasyWeChat\Staff\Session;
use iBrand\WechatPlatform\Services\PlatformService;
use iBrand\WechatPlatform\Services\MessageService;
/**
 * 客服
 */
class StaffController extends Controller
{
    protected $staff;
    protected $platform;
    protected $message;
    protected $session;

    public function __construct(
        Staff $staff, PlatformService $platformService,MessageService $messageService,Session $session
    ) {
        $this->staff = $staff;
        $this->platform = $platformService;
        $this->message=$messageService;
        $this->session=$session;

    }

    /**
     * 获取所有客服账号列表.
     * @return \EasyWeChat\Support\Collection
     */
    public function getLists()
    {

        // 参数
        $appid = request('appid');

        // 授权
        $this->platform->authorizeAPI($this->staff, $appid);

        // 调用接口
        $result = $this->staff->lists();

        // 返回JSON
        return $result;
    }

    /**
     * 获取所有在线的客服账号列表.
     * @return \EasyWeChat\Support\Collection
     */
    public function getOnLines()
    {

        // 参数
        $appid = request('appid');

        // 授权
        $this->platform->authorizeAPI($this->staff, $appid);

        // 调用接口
        $result = $this->staff->onlines();

        // 返回JSON
        return $result;
    }


    /**
     * 添加客服帐号
     * @return \EasyWeChat\Support\Collection
     */
    public function store()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->staff, $appid);

        // 调用接口
        $result = $this->staff->create($data['kf_account'],$data['kf_nick']);

        // 返回JSON
        return $result;
    }


    /**
     * 主动发送消息给用户
     * @return \EasyWeChat\Support\Collection
     */
    public function sendMessage()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->staff, $appid);

        $message=$data['message'];


        // 调用接口
//        $result = $this->staff->message($obj)->to($data['openid'])->send();

        // 返回JSON
//        return $result;
    }


    /**
     * 绑定客服微信
     * @return \EasyWeChat\Support\Collection
     */
    public function invite()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->staff, $appid);

        // 调用接口
        $result = $this->staff->invite($data['kf_account'],$data['invite_wx']);

//         返回JSON
        return $result;
    }


    /**
     * 创建会话
     * @return \EasyWeChat\Support\Collection
     */
    public function SessionCreate()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->session, $appid);


        // 调用接口
        $result = $this->session->create($data['kf_account'],$data['openid']);

        return $result;

    }



    /**
     * 关闭会话
     * @return \EasyWeChat\Support\Collection
     */
    public function SessionClose()
    {

        // 参数
        $appid = request('appid');

        $data = request()->json()->all();

        // 授权
        $this->platform->authorizeAPI($this->session, $appid);


        // 调用接口
        $result = $this->session->close($data['kf_account'],$data['openid']);

        return $result;

    }








}
