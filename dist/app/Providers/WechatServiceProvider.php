<?php

namespace Wechat\Providers;

use EasyWeChat\Encryption\Encryptor;
use EasyWeChat\Support\Log;
use Illuminate\Support\ServiceProvider;
use Overtrue\LaravelWechat\CacheBridge;
use Wechat\Modules\Component\Component;
use Wechat\Modules\Component\ComponentToken;
use Wechat\Modules\Component\Guard;
use Wechat\Modules\OAuth\OAuth;

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
        // 让EasyWechat使用Laravel的日志
        Log::setLogger(app('log'));

        $config = config('wechat');
        $encryptor = new Encryptor(
            $config['app_id'],
            $config['token'],
            $config['aes_key']
        );

        $cache = new CacheBridge();
        $component_token = new ComponentToken(
            $config['app_id'],
            $config['secret'],
            $cache
        );

        /**
         * Component Guard
         */
        $this->app->bind(Guard::class, function($app) use ($config, $encryptor){
            $server = new Guard($config['token']);
            $server->debug($config['debug']);
            $server->setEncryptor($encryptor);

            return $server;
        });

        /**
         * Component
         */
        $this->app->bind(Component::class, function($app) use ($config, $component_token){
            $component = new Component($config['app_id']);
            $component->setAccessToken($component_token);

            return $component;
        });

        /**
         * OAuth
         */
        $this->app->bind(OAuth::class, function($app) use ($config, $component_token){
            $oauth = new OAuth($config['app_id']);
            $oauth->setAccessToken($component_token);

            return $oauth;
        });
    }
}
