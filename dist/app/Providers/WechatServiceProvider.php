<?php

namespace Wechat\Providers;

use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Log;
use Illuminate\Support\ServiceProvider;
use Overtrue\LaravelWechat\CacheBridge;
use Wechat\Modules\Providers\ComponentServiceProvider;
use Wechat\Services\ComponentService;

class WechatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * 微信接口入口
         */
        $this->app->singleton(['EasyWeChat\\Foundation\\Application' => 'wechat'], function($app){
            // 使用Laravel的日志
            Log::setLogger(app('log'));

            // 创建实例
            $wechat = new Application(config('wechat'));

            // 使用Laravel的缓存
            if (config('wechat.use_laravel_cache')) {
                $wechat->cache = new CacheBridge();
            }

            // 注册Component API, 这是EasyWechat的扩展
            $wechat->register(new ComponentServiceProvider());

            return $wechat;
        });
    }
}
