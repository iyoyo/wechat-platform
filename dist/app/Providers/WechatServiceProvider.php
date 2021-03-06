<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Providers;

use EasyWeChat\Card\Card;
use EasyWeChat\Support\Log;
use EasyWeChat\Core\AccessToken;
use EasyWeChat\Encryption\Encryptor;
use Illuminate\Support\ServiceProvider;
use Overtrue\LaravelWechat\CacheBridge;
use iBrand\WechatPlatform\Modules\Component\Guard;
use iBrand\WechatPlatform\Modules\Component\ComponentToken;

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

        /*
         * 默认使用ComponentToken, 这个设置只针对第三方平台的API有效, 对于其他API需要通过setAccessToken方法指定。
         */
        $this->app->bind(AccessToken::class, function ($app) use ($config, $cache) {
            $token = new ComponentToken(
                $config['app_id'],
                $config['secret'],
                $cache
            );

            return $token;
        });

        /*
         * Component Guard
         */
        $this->app->bind(Guard::class, function ($app) use ($config, $encryptor) {
            $server = new Guard($config['token']);
            $server->debug($config['debug']);
            $server->setEncryptor($encryptor);

            return $server;
        });

        /*
         * Guard
         */
        $this->app->bind(\EasyWeChat\Server\Guard::class, function ($app) use ($config, $encryptor) {
            $server = new \EasyWeChat\Server\Guard($config['token']);
            $server->debug($config['debug']);
            $server->setEncryptor($encryptor);

            return $server;
        });

        /*
         * Card
         */
        $this->app->bind(Card::class, function ($app) use ($cache) {
            $card = new Card(resolve(AccessToken::class));
            $card->setCache($cache);

            return $card;
        });
    }
}
