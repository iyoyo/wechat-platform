<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-12-12
 * Time: 18:53
 */

namespace Wechat\Http\Controllers;


use Wechat\Repositories\AuthorizerRepository;
use Log;


class AuthorizerController extends Controller
{
    protected $repository;

    public function __construct(
        AuthorizerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $call_back_url = request('call_back_url');
        if ($clientId = request('client_id')) {
            $res=$this->repository->getAuthorizersByClient($clientId);
            if(count($res)>0&&!empty($call_back_url)){
                      $this->repository->updateCallBackUrl($clientId,$call_back_url);
            }
            return $this->repository->getAuthorizersByClient($clientId);
        }
        return '';
    }
}