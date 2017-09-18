<?php

/*
 * add .styleci.yml
 */

namespace iBrand\WechatPlatform\Http\Controllers;

use iBrand\WechatPlatform\Repositories\AuthorizerRepository;

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
            $res = $this->repository->getAuthorizersByClient($clientId);
            if (count($res) > 0 && ! empty($call_back_url)) {
                $this->repository->updateCallBackUrl($clientId, $call_back_url);
            }

            return $this->repository->getAuthorizersByClient($clientId);
        }

        return '';
    }

    public function update()
    {
        if (request('client_id')&&request('app_id')) {
          return $this->repository->updateDel(request('client_id'),request('app_id'));
        }
        return false;
    }

}
