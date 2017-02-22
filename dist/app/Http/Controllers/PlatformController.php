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

        $callback = route('component_auth_result', ['client_id' => $clientId]);
        $url = $platform->authRedirectUrl($callback);

        //return Redirect::to($url);
        return view('platform/auth', ['redirect_url' => $url]);
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
        $authorizer = $platform->saveAuthorization($auth_code);

        if ($clientId = request('client_id') AND cache($clientId)) {
            $authorizer->client_id = $clientId;
            $authorizer->save();
            return redirect(cache($clientId));
        }

        return '授权成功！';
    }
}
