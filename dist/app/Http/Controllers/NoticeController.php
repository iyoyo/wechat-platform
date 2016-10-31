<?php

namespace Wechat\Http\Controllers;

use Wechat\Authorizer;
use Breeze\Wecom\AccessToken;
use EasyWeChat\Notice\Notice;
use Illuminate\Http\Request;

use Wechat\Http\Requests;
use Wechat\Http\Controllers\Controller;

class NoticeController extends Controller
{
    public function send(Request $request) {
        $appid = $request->get('appid');
        $authorizer = Authorizer::where('appid', $appid)->first();
        $access_token = new AccessToken($appid, $authorizer->refresh_token);

        $data = $request->json()->all();
        $service = new Notice($access_token);
        $result = $service->send($data);
        
        return json_encode($result);
    }
}
