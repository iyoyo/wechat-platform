<?php

namespace Wechat\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Wechat\Services\ComponentService;

class PlatformController extends Controller
{
    /**
     * 引导用户进入授权页
     * @param ComponentService $component
     * @return mixed
     */
    public function auth(ComponentService $component)
    {
        $callback = route('component_auth_result');
        $url = $component->authRedirectUrl($callback);

        return Redirect::to($url);
    }

    /**
     * 保存授权信息
     * @param ComponentService $component
     * @return string
     * @internal param Request $request
     */
    public function authResult(ComponentService $component)
    {
        $auth_code = request('auth_code');
        $component->saveAuthorization($auth_code);

        return '授权成功！';
    }
}
