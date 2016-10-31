<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 2016/7/26
 * Time: 21:33
 */

namespace Breeze\Wecom\Providers;


use Breeze\Wecom\ComponentService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
        $pimple['component'] = function ($pimple) {
            return new ComponentService([
                'appid' => $pimple['config']['app_id'],
                'callback' => url($pimple['config']->get('component.callback')),
            ]);
        };
    }
}