<?php

namespace Wechat\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Wechat\Services\PlatformService;

class PlatformController extends Controller
{
    /**
     * 引导用户进入授权页
     * @param PlatformService $platform
     * @return mixed
     */
    public function auth(PlatformService $platform)
    {
        $callback = route('component_auth_result');
        $url = $platform->authRedirectUrl($callback);

        return Redirect::to($url);
    }

    /**
     * 保存授权信息
     * @param PlatformService $platform
     * @return string
     * @internal param Request $request
     */
    public function authResult(PlatformService $platform)
    {
        $auth_code = request('auth_code');
        $platform->saveAuthorization($auth_code);

        return '授权成功！';
    }
}
