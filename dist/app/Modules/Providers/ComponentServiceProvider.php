<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 2016/7/26
 * Time: 15:34
 */

namespace Wechat\Modules\Providers;

use EasyWeChat\Encryption\Encryptor;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Wechat\Modules\Component\Component;
use Wechat\Modules\Component\ComponentToken;
use Wechat\Modules\Component\Guard;

/**
 * 基于 EasyWechat 的服务提供者
 * @package Wechat\Modules\Providers
 */
class ComponentServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['encryptor'] = function ($pimple) {
            return new Encryptor(
                $pimple['config']['app_id'],
                $pimple['config']['token'],
                $pimple['config']['aes_key']
            );
        };
        $pimple['component_server'] = function ($pimple) {
            $server = new Guard($pimple['config']['token']);
            $server->debug($pimple['config']['debug']);
            $server->setEncryptor($pimple['encryptor']);
            return $server;
        };

        $pimple['component'] = function ($pimple) {
            $component = new Component(
                $pimple['config']['app_id'],
                $pimple['config']['component']['callback']
            );

            $component_token = new ComponentToken(
                $pimple['config']['app_id'],
                $pimple['config']['secret'],
                $pimple['cache']
            );
            $component->setAccessToken($component_token);

            return $component;
        };
    }
}