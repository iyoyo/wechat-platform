<?php

namespace Wechat\Http\Middleware;

use Closure;
use Exception;

/**
 * 验证调用api的参数是否完整
 * Class ParameterVerify
 * @package Wechat\Http\Middleware
 */
class ParameterVerify
{
    public function handle($request, Closure $next){
        if(request('appid') == NULL){
            throw new Exception('Required parameter missing', 2);
        }
        
        return $next($request);
    }
}
