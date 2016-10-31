<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 2016/7/26
 * Time: 15:34
 */

namespace Breeze\Wecom\Providers;


use Breeze\Wecom\ComponentGuard;
use EasyWeChat\Encryption\Encryptor;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ComponentServerServiceProvider implements ServiceProviderInterface
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
            $server = new ComponentGuard($pimple['config']['token']);
            $server->debug($pimple['config']['debug']);
            $server->setEncryptor($pimple['encryptor']);
            return $server;
        };
    }
}