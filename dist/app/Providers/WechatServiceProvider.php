<?php

namespace Wechat\Providers;

use Illuminate\Support\ServiceProvider;
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
        $this->app->singleton(ComponentService::class, function ($app) {
            return new ComponentService(config('wechat'));
        });
    }
}
