<?php
/**
 * Created by PhpStorm.
 * User: breeze
 * Date: 2016/7/26
 * Time: 14:53
 */

namespace Breeze\Wecom;
use Breeze\Wecom\Providers\ComponentServerServiceProvider;
use Breeze\Wecom\Providers\ComponentServiceProvider;

/**
 * Class Application
 * @package Breeze\Wecom
 */
class Application extends \EasyWeChat\Foundation\Application
{
    protected $providers = [
        ComponentServerServiceProvider::class,
        ComponentServiceProvider::class
    ];
}