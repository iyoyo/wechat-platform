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
        $clientId = request('client_id');
        $redirectUrl = request('redirect_url');

        if ($clientId AND $redirectUrl) {
            \Cache::put($clientId, $redirectUrl);
        }

        $callback = route('component_auth_result',['client_id'=>$clientId]);
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

        if(request('client_id') AND cache(request('client_id'))){
            return redirect(cache('client_id'));
        }

        return '授权成功！';
    }
}
